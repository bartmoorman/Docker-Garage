<?php
require_once('../inc/garage.class.php');

$garage = new Garage(false, false, false, false);

$output = $logFields = array('success' => null, 'message' => null);
$log = array();

switch ($_REQUEST['func']) {
  case 'validatePinCode':
    if (!empty($_REQUEST['pincode'])) {
      $output['success'] = $garage->authenticateSession($_REQUEST['pincode']);
      $log['pincode'] = $_REQUEST['pincode'];
    } else {
      $output['success'] = false;
      $output['message'] = 'No pincode supplied';
    }
    break;
  case 'createUser':
    if (!$garage->isConfigured() || ($garage->isValidSession() && $garage->isAdmin())) {
      if (!empty($_REQUEST['pincode']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['role'])) {
        $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
        $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->createUser($_REQUEST['pincode'], $_REQUEST['first_name'], $last_name, $email, $_REQUEST['role'], $begin, $end);
      } else {
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'updateUser':
    if (($garage->isValidSession() && $garage->isAdmin())) {
      if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['pincode']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['role'])) {
        $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
        $email = !empty($_REQUEST['email']) ? $_REQUEST['email'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->updateUser($_REQUEST['user_id'], $_REQUEST['pincode'], $_REQUEST['first_name'], $last_name, $email, $_REQUEST['role'], $begin, $end);
        $log['user_id'] = $_REQUEST['user_id'];
      } else {
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'modifyUser':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['action']) && !empty($_REQUEST['user_id'])) {
        $output['success'] = $garage->modifyUser($_REQUEST['action'], $_REQUEST['user_id']);
        $log['action'] = $_REQUEST['action'];
        $log['user_id'] = $_REQUEST['user_id'];
      } else {
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'userDetails':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['user_id'])) {
        $output['success'] = true;
        $output['data'] = $garage->getUserDetails($_REQUEST['user_id']);
        $log['user_id'] = $_REQUEST['user_id'];
      } else {
        $output['success'] = false;
        $output['message'] = 'No user id supplied';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'activateDevice':
    if ($garage->isValidSession()) {
      if (!empty($_REQUEST['device'])) {
        $output['success'] = $garage->doActivate($_REQUEST['device']);
        $log['device'] = $_REQUEST['device'];
      } else {
        $output['success'] = false;
        $output['message'] = 'No device supplied';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
}

$garage->logEvent($_REQUEST['func'], array_merge(array_intersect_key($output, $logFields), $log));

echo json_encode($output);
?>
