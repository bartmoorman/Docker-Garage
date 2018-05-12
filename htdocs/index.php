<?php
require_once('inc/garage.class.php');

$garage = new Garage();

if ($garage->isConfigured()) {
  if (!$garage->isValidSession()) {
    header('Location: login.php');
    exit;
  }
} else {
  header('Location: setup.php');
  exit;
}
?>
<!DOCTYPE html>
<html class='h-100' lang='en'>
  <head>
    <title>Garage - Index</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
  </head>
  <body class='h-100'>
    <div class='d-flex justify-content-center h-100'>
      <div class='align-self-center'>
        <div class='row justify-content-center'>
          <div class='col-auto'><button class='btn btn-outline-info btn-lg' id='trigger'><h1 class='my-1'>TRIGGER</h1></button></div>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('#trigger').click(function() {
          $('#trigger').prop('disabled', true);

          $.getJSON('src/action.php', {"func": "trigger"})
              .done(function(data) {
                if (data.success) {
                  alert('Success!');
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`validation failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              })
              .always(function() {
                $('#trigger').prop('disabled', false);
              });
        });
      });
    </script>
  </body>
</html>
