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
      header('HTTP/1.1 400 Bad Request');
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
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'createApp':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['name'])) {
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->createApp($_REQUEST['name'], $token, $begin, $end);
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No name supplied';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }    break;
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
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'updateApp':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['app_id']) && !empty($_REQUEST['name']) && !empty($_REQUEST['token'])) {
        $begin = !empty($_REQUEST['begin']) ? $_REQUEST['begin'] : null;
        $end = !empty($_REQUEST['end']) ? $_REQUEST['end'] : null;
        $output['success'] = $garage->updateApp($_REQUEST['app_id'], $_REQUEST['name'], $_REQUEST['token'], $begin, $end);
        $log['app_id'] = $_REQUEST['app_id'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'modifyObject':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['action']) && !empty($_REQUEST['type']) && !empty($_REQUEST['value'])) {
        $output['success'] = $garage->modifyObject($_REQUEST['action'], $_REQUEST['type'], $_REQUEST['value']);
        $log['action'] = $_REQUEST['action'];
        $log['type'] = $_REQUEST['type'];
        $log['value'] = $_REQUEST['value'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'getObjectDetails':
    if ($garage->isValidSession() && $garage->isAdmin()) {
      if (!empty($_REQUEST['type']) && !empty($_REQUEST['value'])) {
        if ($output['data'] = $garage->getObjectDetails($_REQUEST['type'], $_REQUEST['value'])) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['type'] = $_REQUEST['type'];
          $log['value'] = $_REQUEST['value'];
        }
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'Missing arguments';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'doActivate':
    if ($garage->isValidSession() || (array_key_exists('token', $_REQUEST) && $garage->isValidObject('token', $_REQUEST['token']))) {
      if (!empty($_REQUEST['device'])) {
        $output['success'] = $garage->doActivate($_REQUEST['device']);
        $log['device'] = $_REQUEST['device'];
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No device supplied';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'getPosition':
    if ($garage->isValidSession() || (array_key_exists('token', $_REQUEST) && $garage->isValidObject('token', $_REQUEST['token']))) {
      if (!empty($_REQUEST['device'])) {
        if (is_numeric($output['data'] = $garage->getPosition($_REQUEST['device']))) {
          $output['success'] = true;
          $putEvent = false;
        } else {
          $output['success'] = false;
          $log['device'] = $_REQUEST['device'];
        }
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No device supplied';
      }
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
  case 'suppressOpenNotifications':
    if (array_key_exists('nonce', $_REQUEST) && $garage->isValidNonce('suppressOpenNotifications', $_REQUEST['nonce'])) {
      $output['success'] = $garage->suppressOpenNotifications();
      $garage->expireNonce('suppressOpenNotifications', $_REQUEST['nonce']);
    } else {
      header('HTTP/1.1 401 Unauthorized');
      $output['success'] = false;
      $output['message'] = 'Unauthorized';
    }
    break;
}

if ($putEvent) {
  $user_id = array_key_exists('authenticated', $_SESSION) ? $_SESSION['user_id'] : null;
  $garage->putEvent($user_id, $_REQUEST['func'], array_merge(array_intersect_key($output, $logFields), $log));
}

header('Content-Type: application/json');
echo json_encode($output);
?>
