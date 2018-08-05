#!/usr/bin/php
<?php
require_once('/var/www/localhost/htdocs/inc/garage.class.php');
$garage = new Garage(false, false, false, false);

if ($garage->isConfigured('sensor') && $lat = getenv('LAT') && $long = getenv('LONG')) {
  while (true) {
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $long);
    $sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $lat, $long);
    if ($garage->getPosition('sensor') == 0 && (time() < $sunrise || time() > $sunset) && !$garage->memcacheConn->get('notifiedOpen')) {
      $message = sprintf('The garage door is OPEN!');
      msg_send($garage->queueConn, 2, $message, true, false);
      $garage->memcacheConn->set('notifiedOpen', time(), 60 * 30);
    }
    sleep(15);
  }
} else {
  echo 'Sensor and/or lat/long are not configured. Exiting.' . PHP_EOL;
  exit;
}
?>
