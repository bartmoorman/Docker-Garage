<?php
require_once('../inc/garage.class.php');
$garage = new Garage(false, false, false, false);

$output = $logFields = ['success' => null, 'message' => null];
$log = [];
$putEvent = true;

switch ($_REQUEST['func']) {
  case 'authenticateSession':
    if (!empty($_REQUEST['pin'])) {
      $output['success'] = $garage->authenticateSession($_REQUEST['pin']);
      usleep(rand(750000, 1000000));
    } else {
      $output['success'] = false;
      $output['message'] = 'No pin supplied';
    }
    break;
  case 'createUser':
    if (!$garage->isConfigured() || ($garage->isValidSession() && $garage->isAdmin())) {
      if (!empty($_REQUEST['pin']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['role'])) {
        $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
        $pushover_user = !empty($_REQUEST['pushover_user']) ? $_REQUEST['pushover_user'] : null;
        $pushover_token = !empty($_REQUEST['pushover_token']) ? $_REQUEST['pushover_token'] : null;
        $pushover_priority = isset($_REQUEST['pushover_priority']) ? $_REQUEST['pushover_priority'] : null;
        $pushover_retry = isset($_REQUEST['pushover_retry']) ? $_REQUEST['pushover_retry'] : null;
        $pushover_expire = isset($_REQUEST['pushover_expire']) ? $_REQUEST['pushover_expire'] : null;
        $pushover_sound = !empty($_REQUEST['pushover_sound']) ? $_REQUEST['pushover_sound'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->createUser($_REQUEST['pin'], $_REQUEST['first_name'], $last_name, $pushover_user, $pushover_token, $pushover_priority, $pushover_retry, $pushover_expire, $pushover_sound, $_REQUEST['role'], $begin, $end);
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
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['user_id']) && !empty($_REQUEST['pin']) && !empty($_REQUEST['first_name']) && !empty($_REQUEST['role'])) {
        $last_name = !empty($_REQUEST['last_name']) ? $_REQUEST['last_name'] : null;
        $pushover_user = !empty($_REQUEST['pushover_user']) ? $_REQUEST['pushover_user'] : null;
        $pushover_token = !empty($_REQUEST['pushover_token']) ? $_REQUEST['pushover_token'] : null;
        $pushover_priority = isset($_REQUEST['pushover_priority']) ? $_REQUEST['pushover_priority'] : null;
        $pushover_retry = isset($_REQUEST['pushover_retry']) ? $_REQUEST['pushover_retry'] : null;
        $pushover_expire = isset($_REQUEST['pushover_expire']) ? $_REQUEST['pushover_expire'] : null;
        $pushover_sound = !empty($_REQUEST['pushover_sound']) ? $_REQUEST['pushover_sound'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->updateUser($_REQUEST['user_id'], $_REQUEST['pin'], $_REQUEST['first_name'], $last_name, $pushover_user, $pushover_token, $pushover_priority, $pushover_retry, $pushover_expire, $pushover_sound, $_REQUEST['role'], $begin, $end);
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
  case 'getUserDetails':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['user_id'])) {
        if ($output['data'] = $garage->getUserDetails($_REQUEST['user_id'])) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['user_id'] = $_REQUEST['user_id'];
        }
      } else {
        $output['success'] = false;
        $output['message'] = 'No user id supplied';
      }
    } else {
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'doActivate':
    if ($garage->isValidSession()) {
      if (!empty($_REQUEST['device'])) {
        if ($garage->isConfigured($_REQUEST['device'])) {
          $output['success'] = $garage->doActivate($_REQUEST['device']);
        } else {
          $output['success'] = false;
          $output['message'] = 'Device not configured';
        }
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

if ($putEvent) {
  $garage->putEvent($_REQUEST['func'], array_merge(array_intersect_key($output, $logFields), $log));
}

header('Content-Type: application/json');
echo json_encode($output);
?>
