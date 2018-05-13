<?php
class Garage {
  private $dbFile = '/config/garage.db';
  private $dbConn = null;
  private $openerPin = null;
  private $sensorPin = null;
  private $buttonPin = null;

  public function __construct() {
    session_start();

    if (file_exists($this->dbFile) && is_readable($this->dbFile)) {
      $this->connectDb();
    } elseif (is_writable(dirname($this->dbFile))) {
      $this->connectDb();
      $this->initDb();
    }

    $this->openerPin = strtok(getenv('OPENER_PIN'), ':');
    $this->sensorPin = strtok(getenv('SENSOR_PIN'), ':');
    $this->buttonPin = strtok(getenv('BUTTON_PIN'), ':');
  }

  private function connectDb() {
    $this->dbConn = new SQLite3($this->dbFile);
    $this->dbConn->busyTimeout(500);
    $this->dbConn->exec('PRAGMA journal_mode = WAL');
  }

  private function initDb() {
    $query = <<<EOQ
CREATE TABLE `users` (
  `user_id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `pincode` INTEGER NOT NULL UNIQUE,
  `first_name` TEXT NOT NULL,
  `last_name` TEXT,
  `email` TEXT,
  `role` TEXT NOT NULL,
  `begin` TEXT,
  `end` TEXT
)
EOQ;
    return $this->dbConn->exec($query);
  }

  public function isConfigured() {
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
EOQ;
    if ($this->dbConn->querySingle($query)) {
      return true;
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
    $user_id = $this->dbConn->escapeString($_SESSION['user_id']);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `user_id` = {$user_id}
AND `role` LIKE 'admin'
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
WHERE `{$type}` = {$value}
EOQ;
    if ($this->dbConn->querySingle($query) && $this->isValidTime($type, $value)) {
      return true;
    }
    return false;
  }

  public function isValidTime($type, $value) {
    $type = $this->dbConn->escapeString($type);
    $value = $this->dbConn->escapeString($value);
    $query = <<<EOQ
SELECT COUNT(*)
FROM `users`
WHERE `{$type}` = {$value}
AND (NOT `begin` OR `begin` < DATETIME('now'))
AND (NOT `end` OR `end` > DATETIME('now'))
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
WHERE `pincode` = {$pincode}
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
WHERE `pincode` = {$pincode}
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
VALUES ('{$pincode}', '{$first_name}', '{$last_name}', '{$email}', '{$role}', '{$begin}', '{$end}')
EOQ;
      return $this->dbConn->exec($query);
    }
    return false;
  }

  public function removeUser($user_id) {
    $user_id = $this->dbConn->escapeString($user_id);
$query = <<<EOQ
DELETE
FROM `users`
WHERE `user_id` = {$user_id}
EOQ;
    return $this->dbConn->exec($query);
  }

  public function getUsers() {
    $query = <<<EOQ
SELECT `user_id`, substr('000000'||`pincode`,-6) AS `pincode`, `first_name`, `last_name`, `email`, `role`, `begin`, `end`
FROM `users`
EOQ;
    if ($users = $this->dbConn->query($query)) {
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
SELECT `user_id`, substr('000000'||`pincode`,-6) AS `pincode`, `first_name`, `last_name`, `email`, `role`, `begin`, `end`
FROM `users`
WHERE `user_id` = {$user_id}
EOQ;
    if ($user = $this->dbConn->querySingle($query, true)) {
      return $user;
    }
    return false;
  }

  public function doTrigger() {
    if (file_put_contents("/gpio/{$this->openerPin}/value", 1)) {
      usleep(500000);
      if (file_put_contents("/gpio/{$this->openerPin}/value", 0)) {
        return true;
      } else {
        return false;
      }
    }
    return false;
  }
}
?>
