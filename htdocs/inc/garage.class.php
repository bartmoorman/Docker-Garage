<?php
class Garage {
  private $dbFile = '/config/garage.db';
  private $dbConn = null;
  private $devices = array('opener' => null, 'sensor' => null, 'button' => null, 'light' => null);
  private $gpioValue = '/sys/class/gpio/gpio%d/value';
  public $pageLimit = 20;

  public function __construct($requireConfigured = true, $requireValidSession = true, $requireAdmin = true, $requireIndex = false) {
    session_start();

    if (file_exists($this->dbFile) && is_writable($this->dbFile)) {
      $this->connectDb();
    } elseif (is_writable(dirname($this->dbFile))) {
      $this->connectDb();
      $this->initDb();
    }

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

    foreach (array_keys($this->devices) as $device) {
      $env = getenv(strtoupper($device) . '_PIN');
      if ($pos = strpos($env, ':')) {
        $this->devices[$device] = substr($env, 0, $pos);
      }
    }
  }

  private function connectDb() {
    $this->dbConn = new SQLite3($this->dbFile);
    $this->dbConn->busyTimeout(500);
    $this->dbConn->exec('PRAGMA journal_mode = WAL');
  }

  private function initDb() {
    $query = <<<EOQ
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pincode` INTEGER NOT NULL UNIQUE,
  `first_name` TEXT NOT NULL,
  `last_name` TEXT,
  `email` TEXT,
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
EOQ;
    return $this->dbConn->exec($query);
  }

  public function isConfigured($device = null) {
    if ($device) {
      if (array_key_exists($device, $this->devices) && !empty($this->devices[$device])) {
        return true;
      }
    } else {
      $query = <<<EOQ
SELECT COUNT(*)
FROM `users`;
EOQ;
      if ($this->dbConn->querySingle($query)) {
        return true;
      }
    }
    return false;
  }

  public function isValidSession() {
    if (array_key_exists('authenticated', $_SESSION) && $this->isValidUser('user_id', $_SESSION['user_id'])) {
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
AND `role` LIKE 'admin';
EOQ;
    if ($this->dbConn->querySingle($query)) {
      return true;
    }
    return false;
  }

  public function isValidUser($type, $value) {
    $type = $this->dbConn->escapeString($type);
    $value = $this->dbConn->escapeString($value);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
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

  public function authenticateSession($pincode) {
    if ($this->isValidUser('pincode', $pincode)) {
      $pincode = $this->dbConn->escapeString($pincode);
      $query = <<<EOQ
SELECT `user_id`
FROM `users`
WHERE `pincode` = '{$pincode}';
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
    if (session_destroy()) {
      return true;
    }
    return false;
  }

  public function createUser($pincode, $first_name, $last_name = null, $email = null, $role, $begin = null, $end = null) {
    $pincode = $this->dbConn->escapeString($pincode);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `pincode` = '{$pincode}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $first_name = $this->dbConn->escapeString($first_name);
      $last_name = $this->dbConn->escapeString($last_name);
      $email = $this->dbConn->escapeString($email);
      $role = $this->dbConn->escapeString($role);
      $begin = $this->dbConn->escapeString($begin);
      $end = $this->dbConn->escapeString($end);
      $query = <<<EOQ
INSERT
INTO `users` (`pincode`, `first_name`, `last_name`, `email`, `role`, `begin`, `end`)
VALUES ('{$pincode}', '{$first_name}', '{$last_name}', '{$email}', '{$role}', STRFTIME('%s', '{$begin}'), STRFTIME('%s', '{$end}'));
EOQ;
      return $this->dbConn->exec($query);
    }
    return false;
  }

  public function updateUser($user_id, $pincode, $first_name, $last_name = null, $email = null, $role, $begin = null, $end = null) {
    $user_id = $this->dbConn->escapeString($user_id);
    $pincode = $this->dbConn->escapeString($pincode);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `user_id` != '{$user_id}'
AND `pincode` = '{$pincode}';
EOQ;
    if (!$this->dbConn->querySingle($query)) {
      $first_name = $this->dbConn->escapeString($first_name);
      $last_name = $this->dbConn->escapeString($last_name);
      $email = $this->dbConn->escapeString($email);
      $role = $this->dbConn->escapeString($role);
      $begin = $this->dbConn->escapeString($begin);
      $end = $this->dbConn->escapeString($end);
      $query = <<<EOQ
UPDATE `users`
SET (`pincode`, `first_name`, `last_name`, `email`, `role`, `begin`, `end`) = ('{$pincode}', '{$first_name}', '{$last_name}', '{$email}', '{$role}', STRFTIME('%s', '{$begin}'), STRFTIME('%s', '{$end}'))
WHERE `user_id` = '{$user_id}';
EOQ;
      return $this->dbConn->exec($query);
    }
    return false;
  }

  public function modifyUser($action, $user_id) {
    $user_id = $this->dbConn->escapeString($user_id);
    switch ($action) {
      case 'enable':
        $query = <<<EOQ
UPDATE `users`
SET `disabled` = '0'
WHERE `user_id` = '{$user_id}';
EOQ;
        break;
      case 'disable':
        $query = <<<EOQ
UPDATE `users`
SET `disabled` = '1'
WHERE `user_id` = '{$user_id}';
EOQ;
        break;
      case 'delete':
        $query = <<<EOQ
DELETE
FROM `users`
WHERE `user_id` = '{$user_id}';
DELETE
FROM `events`
WHERE `user_id` = '{$user_id}';
EOQ;
        break;
    }
    return $this->dbConn->exec($query);
  }

  public function getUsers($page = 1) {
    $start = ($page - 1) * $this->pageLimit;
    $query = <<<EOQ
SELECT `user_id`, SUBSTR('000000'||`pincode`,-6) AS `pincode`, `first_name`, `last_name`, `email`, `role`, `begin`, `end`, `disabled`
FROM `users`
ORDER BY `last_name`, `first_name`
LIMIT {$start}, {$this->pageLimit};
EOQ;
    if ($users = $this->dbConn->query($query)) {
      $output = array();
      while ($user = $users->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $user;
      }
      return $output;
    }
    return false;
  }

  public function getUserDetails($user_id) {
    $user_id = $this->dbConn->escapeString($user_id);
    $query = <<<EOQ
SELECT `user_id`, SUBSTR('000000'||`pincode`,-6) AS `pincode`, `first_name`, `last_name`, `email`, `role`, STRFTIME('%Y-%m-%dT%H:%M', DATETIME(`begin`, 'unixepoch')) AS `begin`, STRFTIME('%Y-%m-%dT%H:%M', DATETIME(`end`, 'unixepoch')) AS `end`, `disabled`
FROM `users`
WHERE `user_id` = '{$user_id}';
EOQ;
    if ($user = $this->dbConn->querySingle($query, true)) {
      return $user;
    }
    return false;
  }

  public function logEvent($action, $message = array()) {
    $user_id = array_key_exists('authenticated', $_SESSION) ? $_SESSION['user_id'] : null;
    $action = $this->dbConn->escapeString($action);
    $message = $this->dbConn->escapeString(json_encode($message));
    $remote_addr = ip2long(array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
    $query = <<<EOQ
INSERT
INTO `events` (`user_id`, `action`, `message`, `remote_addr`)
VALUES ('{$user_id}', '{$action}', '{$message}', '{$remote_addr}');
EOQ;
    return $this->dbConn->exec($query);
  }

  public function getCount($type) {
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

  public function getEvents($page = 1) {
    $start = ($page - 1) * $this->pageLimit;
    $query = <<<EOQ
SELECT `event_id`, STRFTIME('%s', DATETIME(`date`, 'unixepoch', 'localtime')) AS `date`, `user_id`, `first_name`, `last_name`, `action`, `message`, `remote_addr`, `disabled`
FROM `events`
LEFT JOIN `users` USING (`user_id`)
ORDER BY `date` DESC
LIMIT {$start}, {$this->pageLimit};
EOQ;
    if ($events = $this->dbConn->query($query)) {
      $output = array();
      while ($event = $events->fetchArray(SQLITE3_ASSOC)) {
        $output[] = $event;
      }
      return $output;
    }
    return false;
  }

  public function doActivate($device) {
    if (file_put_contents(sprintf($this->gpioValue, $this->devices[$device]), 0)) {
      usleep(500000);
      if (file_put_contents(sprintf($this->gpioValue, $this->devices[$device]), 1)) {
        return true;
      }
    }
    return false;
  }
}
?>
