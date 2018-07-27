<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, true, false, false);

if ($garage->deauthenticateSession()) {
  header('Location: login.php');
} else {
  header('Location: index.php');
}
?>
