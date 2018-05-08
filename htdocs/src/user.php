<?php
require_once('../inc/auth.class.php');

$auth = new Auth();

$output = array('success' => null);

switch ($_REQUEST['action']) {
  case 'validate':
    if (!empty($_REQUEST['pincode']) && $auth->isValidPinCode($_REQUEST['pincode'])) {
      $output['success'] = $auth->authenticateSession($_REQUEST['pincode']);
    } else {
      $output['success'] = false;
    }
    break;
  case 'create':
    if (!$auth->isConfigured() || $auth->isAdmin()) {
      if (!empty($_REQUEST['pincode']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['last_name'])) {
        $role = !empty($_REQUEST['role']) ? $_REQUEST['role'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $auth->createUser($_REQUEST['pincode'], $_REQUEST['first_name'], $_REQUEST['last_name'], $role, $begin, $end);
      } else {
        $output['success'] = false;
      }
    } else {
      $output['success'] = false;
    }
    break;
  case 'remove';
    if ($auth->isAdmin()) {
      if (!empty($_REQUEST['pincode'])) {
        $output['success'] = $auth->removeUser($_REQUEST['pincode']);
      } else {
        $output['success'] = false;
      }
    } else {
      $output['success'] = false;
    }
    break;
}

echo json_encode($output);
?>
