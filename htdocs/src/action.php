<?php
require_once('../inc/garage.class.php');
$garage = new Garage(false, false, false, false);

$output = $logFields = ['success' => null, 'message' => null];
$log = [];
$putEvent = true;

switch ($_REQUEST['func']) {
  case 'authenticateSession':
    if (!empty($_POST['pin'])) {
      $output['success'] = $garage->authenticateSession($_POST['pin']);
      usleep(rand(750000, 1000000));
    } else {
      header('HTTP/1.1 400 Bad Request');
      $output['success'] = false;
      $output['message'] = 'No pin supplied';
    }
    break;
  case 'createUser':
    if (!$garage->isConfigured() || ($garage->isValidSession() && $garage->isAdmin())) {
      if (!empty($_POST['pin']) && !empty($_POST['first_name']) && !empty($_POST['role'])) {
        $last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : null;
        $pushover_user = !empty($_POST['pushover_user']) ? $_POST['pushover_user'] : null;
        $pushover_token = !empty($_POST['pushover_token']) ? $_POST['pushover_token'] : null;
        $pushover_priority = isset($_POST['pushover_priority']) ? $_POST['pushover_priority'] : null;
        $pushover_retry = isset($_POST['pushover_retry']) ? $_POST['pushover_retry'] : null;
        $pushover_expire = isset($_POST['pushover_expire']) ? $_POST['pushover_expire'] : null;
        $pushover_sound = !empty($_POST['pushover_sound']) ? $_POST['pushover_sound'] : null;
        $begin = !empty($_POST['begin']) ? $_POST['begin'] : null;
        $end = !empty($_POST['end']) ? $_POST['end'] : null;
        $output['success'] = $garage->createUser($_POST['pin'], $_POST['first_name'], $last_name, $pushover_user, $pushover_token, $pushover_priority, $pushover_retry, $pushover_expire, $pushover_sound, $_POST['role'], $begin, $end);
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
      if (!empty($_POST['name'])) {
        $token = isset($_POST['token']) ? $_POST['token'] : null;
        $begin = !empty($_POST['begin']) ? $_POST['begin'] : null;
        $end = !empty($_POST['end']) ? $_POST['end'] : null;
        $output['success'] = $garage->createApp($_POST['name'], $token, $begin, $end);
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
      if (!empty($_POST['user_id']) && !empty($_POST['pin']) && !empty($_POST['first_name']) && !empty($_POST['role'])) {
        $last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : null;
        $pushover_user = !empty($_POST['pushover_user']) ? $_POST['pushover_user'] : null;
        $pushover_token = !empty($_POST['pushover_token']) ? $_POST['pushover_token'] : null;
        $pushover_priority = isset($_POST['pushover_priority']) ? $_POST['pushover_priority'] : null;
        $pushover_retry = isset($_POST['pushover_retry']) ? $_POST['pushover_retry'] : null;
        $pushover_expire = isset($_POST['pushover_expire']) ? $_POST['pushover_expire'] : null;
        $pushover_sound = !empty($_POST['pushover_sound']) ? $_POST['pushover_sound'] : null;
        $begin = !empty($_POST['begin']) ? $_POST['begin'] : null;
        $end = !empty($_POST['end']) ? $_POST['end'] : null;
        $output['success'] = $garage->updateUser($_POST['user_id'], $_POST['pin'], $_POST['first_name'], $last_name, $pushover_user, $pushover_token, $pushover_priority, $pushover_retry, $pushover_expire, $pushover_sound, $_POST['role'], $begin, $end);
        $log['user_id'] = $_POST['user_id'];
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
      if (!empty($_POST['app_id']) && !empty($_POST['name']) && !empty($_POST['token'])) {
        $begin = !empty($_POST['begin']) ? $_POST['begin'] : null;
        $end = !empty($_POST['end']) ? $_POST['end'] : null;
        $output['success'] = $garage->updateApp($_POST['app_id'], $_POST['name'], $_POST['token'], $begin, $end);
        $log['app_id'] = $_POST['app_id'];
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
      if (!empty($_POST['action']) && !empty($_POST['type']) && !empty($_POST['value'])) {
        $output['success'] = $garage->modifyObject($_POST['action'], $_POST['type'], $_POST['value']);
        $log['action'] = $_POST['action'];
        $log['type'] = $_POST['type'];
        $log['value'] = $_POST['value'];
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
    if ($garage->isValidSession() || (array_key_exists('token', $_POST) && $garage->isValidObject('token', $_POST['token']))) {
      if (!empty($_POST['device'])) {
        $output['success'] = $garage->doActivate($_POST['device']);
        $log['device'] = $_POST['device'];
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
  case 'suppressNotifications':
    if (array_key_exists('nonce', $_REQUEST) && $garage->isValidNonce('suppressNotifications', $_REQUEST['nonce'])) {
      if (!empty($_REQUEST['position'])) {
        $output['success'] = $garage->suppressNotifications($_REQUEST['position']);
        $garage->expireNonce('suppressNotifications', $_REQUEST['nonce']);
      } else {
        header('HTTP/1.1 400 Bad Request');
        $output['success'] = false;
        $output['message'] = 'No position supplied';
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
}

if ($putEvent) {
  $user_id = array_key_exists('authenticated', $_SESSION) ? $_SESSION['user_id'] : null;
  $garage->putEvent($user_id, $_REQUEST['func'], array_merge(array_intersect_key($output, $logFields), $log));
}

header('Content-Type: application/json');
echo json_encode($output);
?>
