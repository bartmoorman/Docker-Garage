<?php
date_default_timezone_set(getenv('TZ'));

class Garage {
  private $dbFile = '/config/garage.db';
  private $dbConn;
  public $memcacheConn;
  private $queueKey = 8440;
  public $queueSize = 512;
  public $queueConn;
  private $pushoverAppToken;
  private $devices = ['opener' => null, 'sensor' => null];
  private $gpioValue = '/sys/class/gpio/gpio%u/value';
  public $astro = [];
  public $pageLimit = 20;

  public function __construct($requireConfigured = true, $requireValidSession = true, $requireAdmin = true, $requireIndex = false) {
    session_start([
      'save_path' => '/config/sessions',
      'name' => '_sess_garage',
      'gc_maxlifetime' => 60 * 60 * 24,
      'cookie_lifetime' => 60 * 60 * 24,
      'cookie_secure' => true,
      'cookie_httponly' => true,
      'use_strict_mode' => true
    ]);

    if (is_writable($this->dbFile)) {
      $this->connectDb();
    } elseif (is_writable(dirname($this->dbFile))) {
      $this->connectDb();
      $this->initDb();
    }

    $this->connectMemcache();

    $this->connectQueue();

    if ($this->isConfigured()) {
      if ($this->isValidSession()) {
        if (($requireAdmin && !$this->isAdmin()) || $requireIndex) {
          header('Location: index.php');
          exit;
        }
      } elseif ($requireValidSession) {
        header('Location: login.php');
        exit;
      }
    } elseif ($requireConfigured) {
      header('Location: setup.php');
      exit;
    }

    $this->pushoverAppToken = getenv('PUSHOVER_APP_TOKEN');

    foreach (array_keys($this->devices) as $device) {
      $env = getenv(strtoupper($device) . '_PIN');
      if ($pos = strpos($env, ':')) {
        $this->devices[$device] = substr($env, 0, $pos);
      }
    }

    $this->astro['latitude'] = getenv('LATITUDE') ?: ini_get('date.default_latitude');
    $this->astro['longitude'] = getenv('LONGITUDE') ?: ini_get('date.default_longitude');
    $this->astro['zenith']['sunrise'] = getenv('SUNRISE_ZENITH') ?: ini_get('date.sunrise_zenith');
    $this->astro['zenith']['sunset'] = getenv('SUNSET_ZENITH') ?: ini_get('date.sunset_zenith');
  }

  private function connectDb() {
    if ($this->dbConn = new SQLite3($this->dbFile)) {
      $this->dbConn->busyTimeout(500);
      $this->dbConn->exec('PRAGMA journal_mode = WAL');
      return true;
    }
    return false;
  }

  private function initDb() {
    $query = <<<EOQ
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pin` INTEGER NOT NULL UNIQUE,
  `first_name` TEXT NOT NULL,
  `last_name` TEXT,
  `pushover_user` TEXT,
  `pushover_token` TEXT,
  `pushover_priority` INTEGER DEFAULT 0,
  `pushover_retry` INTEGER DEFAULT 60,
  `pushover_expire` INTEGER DEFAULT 3600,
  `pushover_sound` TEXT,
  `role` TEXT NOT NULL,
  `begin` INTEGER,
  `end` INTEGER,
  `disabled` INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `date` INTEGER DEFAULT (STRFTIME('%s', 'now')),
  `user_id` INTEGER,
  `action` TEXT,
  `message` BLOB,
  `remote_addr` INTEGER
);
CREATE TABLE IF NOT EXISTS `apps` (
  `app_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` TEXT NOT NULL,
  `key` TEXT NOT NULL UNIQUE,
  `begin` INTEGER,
  `end` INTEGER,
  `disabled` INTEGER NOT NULL DEFAULT 0
);
CREATE TABLE IF NOT EXISTS `calls` (
  `call_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `date` INTEGER DEFAULT (STRFTIME('%s', 'now')),
  `app_id` INTEGER NOT NULL,
  `action` TEXT,
  `message` BLOB,
  `remote_addr` INTEGER
);
EOQ;
    if ($this->dbConn->exec($query)) {
      return true;
    }
    return false;
  }

  private function connectMemcache() {
    if ($this->memcacheConn = new Memcached()) {
      $this->memcacheConn->addServer('localhost', null);
      return true;
    }
    return false;
  }

  private function connectQueue() {
    if ($this->queueConn = msg_get_queue($this->queueKey)) {
      return true;
    }
    return false;
  }

  public function isConfigured($device = null) {
    if ($device) {
      if (array_key_exists($device, $this->devices) && !empty($this->devices[$device])) {
        return true;
      }
    } else {
      if ($this->getObjectCount('users')) {
        return true;
      }
    }
    return false;
  }

  public function isValidSession() {
    if (array_key_exists('authenticated', $_SESSION) && $this->isValidObject('user_id', $_SESSION['user_id'])) {
      return true;
    }
    return false;
  }

  public function isAdmin() {
    $user_id = $_SESSION['user_id'];
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `user_id` = '{$user_id}'
AND `role` = 'admin';
EOQ;
    if ($this->dbConn->querySingle($query)) {
      return true;
    }
    return false;
  }

  public function isValidObject($type, $value) {
    $type = $this->dbConn->escapeString($type);
    $value = $this->dbConn->escapeString($value);
    switch ($type) {
      case 'pin':
      case 'user_id':
        $table = 'users';
        break;
      case 'key':
      case 'app_id':
        $table = 'apps';
        break;
    }
    $query = <<<EOQ
SELECT COUNT(*)
FROM `{$table}`
WHERE `{$type}` = '{$value}'
AND (`begin` IS NULL OR `begin` < STRFTIME('%s', 'now', 'localtime'))
AND (`end` IS NULL OR `end` > STRFTIME('%s', 'now', 'localtime'))
AND NOT `disabled`;
EOQ;
    if ($this->dbConn->querySingle($query)) {
      return true;
    }
    return false;
  }

  public function authenticateSession($pin) {
    if ($this->isValidObject('pin', $pin)) {
      $pin = $this->dbConn->escapeString($pin);
      $query = <<<EOQ
SELECT `user_id`
FROM `users`
WHERE `pin` = '{$pin}';
EOQ;
      if ($user_id = $this->dbConn->querySingle($query)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user_id'] = $user_id;
        return true;
      }
    }
    return false;
  }

  public function deauthenticateSession() {
    if (session_unset() && session_destroy()) {
      return true;
    }
    return false;
  }

  public function createUser($pin, $first_name, $last_name = null, $pushover_user = null, $pushover_token = null, $pushover_priority = null, $pushover_retry = null, $pushover_expire = null, $pushover_sound = null, $role, $begin = null, $end = null) {
    $pin = $this->dbConn->escapeString($pin);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `pin` = '{$pin}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $first_name = $this->dbConn->escapeString($first_name);
      $last_name = $this->dbConn->escapeString($last_name);
      $pushover_user = $this->dbConn->escapeString($pushover_user);
      $pushover_token = $this->dbConn->escapeString($pushover_token);
      $pushover_priority = $this->dbConn->escapeString($pushover_priority);
      $pushover_retry = $this->dbConn->escapeString($pushover_retry);
      $pushover_expire = $this->dbConn->escapeString($pushover_expire);
      $pushover_sound = $this->dbConn->escapeString($pushover_sound);
      $role = $this->dbConn->escapeString($role);
      $begin = $this->dbConn->escapeString($begin);
      $end = $this->dbConn->escapeString($end);
      $query = <<<EOQ
INSERT
INTO `users` (`pin`, `first_name`, `last_name`, `pushover_user`, `pushover_token`, `pushover_priority`, `pushover_retry`, `pushover_expire`, `pushover_sound`, `role`, `begin`, `end`)
VALUES ('{$pin}', '{$first_name}', '{$last_name}', '{$pushover_user}', '{$pushover_token}', '{$pushover_priority}', '{$pushover_retry}', '{$pushover_expire}', '{$pushover_sound}', '{$role}', STRFTIME('%s', '{$begin}'), STRFTIME('%s', '{$end}'));
EOQ;
      if ($this->dbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function createApp($name, $key = null, $begin = null, $end = null) {
    $key = !$key ? bin2hex(random_bytes(8)) : $this->dbConn->escapeString($key);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `apps`
WHERE `key` = '{$key}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $name = $this->dbConn->escapeString($name);
      $begin = $this->dbConn->escapeString($begin);
      $end = $this->dbConn->escapeString($end);
      $query = <<<EOQ
INSERT
INTO `apps` (`name`, `key`, `begin`, `end`)
VALUES ('{$name}', '{$key}', STRFTIME('%s','{$begin}'), STRFTIME('%s','{$end}'));
EOQ;
      if ($this->dbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function updateUser($user_id, $pin, $first_name, $last_name = null, $pushover_user = null, $pushover_token = null, $pushover_priority = null, $pushover_retry = null, $pushover_expire = null, $pushover_sound = null, $role, $begin = null, $end = null) {
    $user_id = $this->dbConn->escapeString($user_id);
    $pin = $this->dbConn->escapeString($pin);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `user_id` != '{$user_id}'
AND `pin` = '{$pin}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $first_name = $this->dbConn->escapeString($first_name);
      $last_name = $this->dbConn->escapeString($last_name);
      $pushover_user = $this->dbConn->escapeString($pushover_user);
      $pushover_token = $this->dbConn->escapeString($pushover_token);
      $pushover_priority = $this->dbConn->escapeString($pushover_priority);
      $pushover_retry = $this->dbConn->escapeString($pushover_retry);
      $pushover_expire = $this->dbConn->escapeString($pushover_expire);
      $pushover_sound = $this->dbConn->escapeString($pushover_sound);
      $role = $this->dbConn->escapeString($role);
      $begin = $this->dbConn->escapeString($begin);
      $end = $this->dbConn->escapeString($end);
      $query = <<<EOQ
UPDATE `users`
SET
  `pin` = '{$pin}',
  `first_name` = '{$first_name}',
  `last_name` = '{$last_name}',
  `pushover_user` = '{$pushover_user}',
  `pushover_token` = '{$pushover_token}',
  `pushover_priority` = '{$pushover_priority}',
  `pushover_retry` = '{$pushover_retry}',
  `pushover_expire` = '{$pushover_expire}',
  `pushover_sound` = '{$pushover_sound}',
  `role` = '{$role}',
  `begin` = STRFTIME('%s', '{$begin}'),
  `end` = STRFTIME('%s', '{$end}')
WHERE `user_id` = '{$user_id}';
EOQ;
      if ($this->dbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function updateApp($app_id, $name, $key, $begin, $end) {
    $app_id = $this->dbConn->escapeString($app_id);
    $key = $this->dbConn->escapeString($key);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `apps`
WHERE `app_id` != '{$app_id}'
AND `key` = '{$key}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $name = $this->dbConn->escapeString($name);
      $begin = $this->dbConn->escapeString($begin);
      $end = $this->dbConn->escapeString($end);
      $query = <<<EOQ
UPDATE `apps`
SET
  `name` = '{$name}',
  `key` = '{$key}',
  `begin` = STRFTIME('%s', '{$begin}'),
  `end` = STRFTIME('%s', '{$end}')
WHERE `app_id` = '{$app_id}';
EOQ;
      if ($this->dbConn->exec($query)) {
        return true;
      }
    }
    return false;
  }

  public function modifyObject($action, $type, $value, $extra_type = null, $extra_value = null) {
    $type = $this->dbConn->escapeString($type);
    $value = $this->dbConn->escapeString($value);
    $extra_type = $this->dbConn->escapeString($extra_type);
    $extra_value = $this->dbConn->escapeString($extra_value);
    switch ($type) {
      case 'pin':
      case 'user_id':
        $table = 'users';
        $extra_table = 'events';
        break;
      case 'key':
      case 'app_id':
        $table = 'apps';
        $extra_table = 'calls';
        break;
    }
    switch ($action) {
      case 'enable':
        $query = <<<EOQ
UPDATE `{$table}`
SET `disabled` = '0'
WHERE `{$type}` = '{$value}';
EOQ;
        break;
      case 'disable':
        $query = <<<EOQ
UPDATE `{$table}`
SET `disabled` = '1'
WHERE `{$type}` = '{$value}';
EOQ;
        break;
      case 'delete':
        $query = <<<EOQ
DELETE
FROM `{$table}`
WHERE `{$type}` = '{$value}';
DELETE
FROM `{$extra_table}`
WHERE `{$type}` = '{$value}';
EOQ;
        break;
    }
    if ($this->dbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function getObjects($type) {
    switch ($type) {
      case 'users':
        $query = <<<EOQ
SELECT `user_id`, `first_name`, `last_name`, `pushover_user`, `pushover_token`, `pushover_priority`, `pushover_retry`, `pushover_expire`, `pushover_sound`, `role`, `begin`, `end`, `disabled`
FROM `users`
ORDER BY `last_name`, `first_name`
EOQ;
        break;
      case 'apps':
        $query = <<<EOQ
SELECT `app_id`, `name`, `key`, `begin`, `end`, `disabled`
FROM `apps`
ORDER BY `name`;
EOQ;
        break;
    }
    if ($objects = $this->dbConn->query($query)) {
      $output = [];
      while ($object = $objects->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $object;
      }
      return $output;
    }
    return false;
  }

  public function getObjectDetails($type, $value) {
    $value = $this->dbConn->escapeString($value);
    switch ($type) {
      case 'user':
        $query = <<<EOQ
SELECT `user_id`, SUBSTR('000000'||`pin`,-6) AS `pin`, `first_name`, `last_name`, `pushover_user`, `pushover_token`, `pushover_priority`, `pushover_retry`, `pushover_expire`, `pushover_sound`, `role`, STRFTIME('%Y-%m-%dT%H:%M', `begin`, 'unixepoch') AS `begin`, STRFTIME('%Y-%m-%dT%H:%M', `end`, 'unixepoch') AS `end`, `disabled`
FROM `users`
WHERE `user_id` = '{$value}';
EOQ;
        break;
      case 'app':
        $query = <<<EOQ
SELECT `app_id`, `name`, `key`, STRFTIME('%Y-%m-%dT%H:%M', `begin`, 'unixepoch') AS `begin`, STRFTIME('%Y-%m-%dT%H:%M', `end`, 'unixepoch') AS `end`, `disabled`
FROM `apps`
WHERE `app_id` = '{$value}';
EOQ;
        break;
    }
    if ($object = $this->dbConn->querySingle($query, true)) {
      return $object;
    }
    return false;
  }

  public function getObjectCount($type) {
    $type = $this->dbConn->escapeString($type);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `{$type}`;
EOQ;
    if ($count = $this->dbConn->querySingle($query)) {
      return $count;
    }
    return false;
  }

  public function putEvent($action, $message = []) {
    $user_id = array_key_exists('authenticated', $_SESSION) ? $_SESSION['user_id'] : null;
    $action = $this->dbConn->escapeString($action);
    $message = $this->dbConn->escapeString(json_encode($message));
    $remote_addr = ip2long(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
    $query = <<<EOQ
INSERT
INTO `events` (`user_id`, `action`, `message`, `remote_addr`)
VALUES ('{$user_id}', '{$action}', '{$message}', '{$remote_addr}');
EOQ;
    if ($this->dbConn->exec($query)) {
      return true;
    }
    return false;
  }

  public function getEvents($page = 1) {
    $start = ($page - 1) * $this->pageLimit;
    $query = <<<EOQ
SELECT `event_id`, STRFTIME('%s', `date`, 'unixepoch') AS `date`, `user_id`, `first_name`, `last_name`, `action`, `message`, `remote_addr`, `disabled`
FROM `events`
LEFT JOIN `users` USING (`user_id`)
ORDER BY `date` DESC
LIMIT {$start}, {$this->pageLimit};
EOQ;
    if ($events = $this->dbConn->query($query)) {
      $output = [];
      while ($event = $events->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $event;
      }
      return $output;
    }
    return false;
  }

  public function doActivate($device) {
    if ($this->isConfigured($device)) {
      if ($this->isConfigured('sensor')) {
        switch ($this->getPosition('sensor')) {
          case '0':
            $status = 'CLOSED';
            break;
          case '1':
            $status = 'OPENED';
            break;
          default:
            $status = 'ACTIVATED';
        }
      } else {
        $status = 'ACTIVATED';
      }
      if (file_put_contents(sprintf($this->gpioValue, $this->devices[$device]), 0)) {
        usleep(750000);
        if (file_put_contents(sprintf($this->gpioValue, $this->devices[$device]), 1)) {
          if ($user = $this->getObjectDetails('user', $_SESSION['user_id'])) {
            $user_name = !empty($user['last_name']) ? sprintf('%2$s, %1$s', $user['first_name'], $user['last_name']) : $user['first_name'];
            $message = sprintf('Garage was %s by %s (user_id: %u)', $status, $user_name, $user['user_id']);
            msg_send($this->queueConn, 1, $message, true, false);
          }
          return true;
        }
      }
    }
    return false;
  }

  public function getPosition($device) {
    if ($this->isConfigured($device)) {
      if (is_numeric($position = trim(file_get_contents(sprintf($this->gpioValue, $this->devices[$device]))))) {
        return $position;
      }
    }
    return false;
  }

  public function getSounds() {
    if ($result = $this->memcacheConn->get('pushoverSounds')) {
      return json_decode($result)->sounds;
    } else {
      $ch = curl_init("https://api.pushover.net/1/sounds.json?token={$this->pushoverAppToken}");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      if (($result = curl_exec($ch)) !== false && curl_getinfo($ch, CURLINFO_RESPONSE_CODE) == 200) {
        $this->memcacheConn->set('pushoverSounds', $result, 60 * 60 * 24);
        return json_decode($result)->sounds;
      }
    }
    return false;
  }

  public function sendNotifications($messages = []) {
    $query = <<<EOQ
SELECT `user_id`, `first_name`, `last_name`, `pushover_user`, `pushover_token`, `pushover_priority`, `pushover_retry`, `pushover_expire`, `pushover_sound`
FROM `users`
WHERE LENGTH(`pushover_user`) AND LENGTH(`pushover_token`)
AND NOT `disabled`;
EOQ;
    if ($messages && $users = $this->dbConn->query($query)) {
      $ch = curl_init('https://api.pushover.net/1/messages.json');
      while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
        $user_name = !empty($user['last_name']) ? sprintf('%2$s, %1$s', $user['first_name'], $user['last_name']) : $user['first_name'];
        foreach ($messages as $message) {
          curl_setopt($ch, CURLOPT_POSTFIELDS, ['user' => $user['pushover_user'], 'token' => $user['pushover_token'], 'message' => $message, 'priority' => $user['pushover_priority'], 'retry' => $user['pushover_retry'], 'expire' => $user['pushover_expire'], 'sound' => $user['pushover_sound']]);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          if (curl_exec($ch) !== false && curl_getinfo($ch, CURLINFO_RESPONSE_CODE) == 200) {
            $status = 'successful';
          } else {
            $status = 'failed';
          }
          echo date('Y-m-d H:i:s') . " - notification to {$user_name} (user_id: {$user['user_id']}) {$status}: {$message}" . PHP_EOL;
        }
      }
      curl_close($ch);
      return true;
    }
    return false;
  }
}
?>
