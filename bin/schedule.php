#!/usr/bin/php
<?php
require_once('/var/www/localhost/htdocs/inc/garage.class.php');
$garage = new Garage(false, false, false, false);

if ($garage->isConfigured('sensor')) {
  while (true) {
    $sunrise = date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $garage->astro['latitude'], $garage->astro['longitude'], $garage->astro['zenith']['sunrise']);
    $sunset = date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $garage->astro['latitude'], $garage->astro['longitude'], $garage->astro['zenith']['sunset']);
    if (time() < $sunrise || time() > $sunset) {
      if ($garage->getPosition('sensor') == 0 && !$garage->memcacheConn->get('notifiedOpen')) {
        $message = sprintf('Garage is OPEN');
        msg_send($garage->queueConn, 2, $message);
        $garage->memcacheConn->set('notifiedOpen', time(), 60 * 30);
      } elseif ($garage->getPosition('sensor') == 1 && $garage->memcacheConn->get('notifiedOpen')) {
        $message = sprintf('Garage is CLOSED');
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
