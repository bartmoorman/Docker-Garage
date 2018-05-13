<?php
require_once('../inc/garage.class.php');

$garage = new Garage();

$output = array('success' => null, 'message' => null);

switch ($_REQUEST['func']) {
  case 'validatePinCode':
    if (!empty($_REQUEST['pincode'])) {
      $output['success'] = $garage->authenticateSession($_REQUEST['pincode']);
    } else {
      $output['success'] = false;
      $output['message'] = 'No pincode supplied';
    }
    break;
  case 'createUser':
    if (!$garage->isConfigured() || $garage->isAdmin()) {
      if (!empty($_REQUEST['pincode']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['last_name']) && !empty($_REQUEST['email']) && !empty($_REQUEST['role'])) {
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->createUser($_REQUEST['pincode'], $_REQUEST['first_name'], $_REQUEST['last_name'], $_REQUEST['email'], $_REQUEST['role'], $begin, $end);
      } else {
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'removeUser';
    if ($garage->isAdmin()) {
      if (!empty($_REQUEST['user_id'])) {
        $output['success'] = $garage->removeUser($_REQUEST['user_id']);
      } else {
        $output['success'] = false;
        $output['message'] = 'No user id supplied';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'userDetails':
    if ($garage->isAdmin()) {
      if (!empty($_REQUEST['user_id'])) {
        $output['data'] = $garage->getUserDetails($_REQUEST['user_id']);
        $output['success'] = true;
      } else {
        $output['success'] = false;
        $output['message'] = 'No user id supplied';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'triggerOpener';
    if ($garage->isValidSession()) {
      $output['success'] = $garage->doTrigger();
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
}

echo json_encode($output);
?>
