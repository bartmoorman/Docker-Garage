#!/usr/bin/php
<?php
require_once('/var/www/localhost/htdocs/inc/garage.class.php');
$garage = new Garage(false, false, false, false);

if ($garage->isConfigured('sensor')) {
  $latitude = getenv('LATITUDE') ?: ini_get('date.default_latitude');
  $longitude = getenv('LONGITUDE') ?: ini_get('date.default_longitude');
  $riseZenith = getenv('RISE_ZENITH') ?: ini_get('date.sunrise_zenith');
  $setZenith = getenv('SET_ZENITH') ?: ini_get('date.sunset_zenith');
  while (true) {
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, $riseZenith);
    $sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $latitude, $longitude, $setZenith);
    if (time() < $sunrise || time() > $sunset) {
      if ($garage->getPosition('sensor') == 0 && !$garage->memcacheConn->get('notifiedOpen')) {
        $message = sprintf('The garage is OPEN');
        msg_send($garage->queueConn, 2, $message);
        $garage->memcacheConn->set('notifiedOpen', time(), 60 * 30);
      } elseif ($garage->getPosition('sensor') == 1 && $garage->memcacheConn->get('notifiedOpen')) {
        $message = sprintf('The garage now is CLOSED');
        msg_send($garage->queueConn, 2, $message);
        $garage->memcacheConn->delete('notifiedOpen');
      }
    }
    sleep(15);
  }
} else {
  echo 'Sensor is not configured. Exiting.' . PHP_EOL;
  exit;
}
?>
