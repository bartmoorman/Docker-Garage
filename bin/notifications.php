#!/usr/bin/php
<?php
require_once('/var/www/localhost/htdocs/inc/garage.class.php');
$garage = new Garage(false, false, false, false);

while (true) {
  $messages = [];
  while (msg_receive($garage->queueConn, 0, $msgtype, $garage->queueSize, $message, true, MSG_IPC_NOWAIT)) {
    $messages[] = $message;
  }
  $garage->sendNotifications($messages);
  sleep(5);
}
?>
