<?php
class Auth {
  private $authFile = '/config/credentials.json';
  private $credentials = array();

  public function __construct() {
    session_start();

    if (file_exists($this->authFile) && is_readable($this->authFile)) {
      $this->credentials = json_decode(file_get_contents($this->authFile), true);
    }
  }

  public function isConfigured() {
    if (!empty($this->credentials)) {
      return true;
    }
    return false;
  }

  public function isConfigurable() {
    if ((file_exists($this->authFile) && is_writable($this->authFile)) || is_writable(dirname($this->authFile))) {
      return true;
    }
    return false;
  }

  public function authenticateSession($pincode) {
    $_SESSION['authenticated'] = true;
    $_SESSION['pincode'] = $pincode;
    return true;
  }

  public function deauthenticateSession() {
    if (session_destroy()) {
      return true;
    }
    return false;
  }

  public function isValidPinCode($pincode) {
    if (array_key_exists($pincode, $this->credentials) && $this->isValidTime($pincode)) {
      return true;
    }
    return false;
  }

  public function isValidSession() {
    if (array_key_exists('authenticated', $_SESSION) && array_key_exists($_SESSION['pincode'], $this->credentials) && $this->isValidTime($_SESSION['pincode'])) {
      return true;
    }
    return false;
  }

  private function isValidTime($pincode) {
    if ((empty($this->credentials[$pincode]['begin']) || time() > $this->credentials[$pincode]['begin']) && (empty($this->credentials[$pincode]['end']) || time() < $this->credentials[$pincode]['end'])) {
      return true;
    }
    return false;
  }

  public function isAdmin() {
    if (array_key_exists($_SESSION['pincode'], $this->credentials) && $this->credentials[$_SESSION['pincode']]['role'] == 'admin') {
      return true;
    }
    return false;
  }

  public function createUser($pincode, $first_name, $last_name, $role = 'user', $begin = null, $end = null) {
    if (!array_key_exists($pincode, $this->credentials)) {
      $this->credentials[$pincode] = array(
        'first_name' => $first_name,
        'last_name' => $last_name,
        'role' => $role,
        'begin' => $begin,
        'end' => $end
      );
      file_put_contents($this->authFile, json_encode($this->credentials));
      return true;
    }
    return false;
  }

  public function removeUser($pincode) {
    if (array_key_exists($pincode, $this->credentials)) {
      unset($this->credentials[$pincode]);
      file_put_contents($this->authFile, json_encode($this->credentials));
      return true;
    }
    return false;
  }

  public function getUsers() {
    return $this->credentials;
  }
}
?>
