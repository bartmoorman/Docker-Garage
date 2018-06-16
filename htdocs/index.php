<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, true, false, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Garage - Index</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
    <style>
      div.modal {
        z-index: auto;
      }
    </style>
  </head>
  <body>
<?php
if ($garage->isAdmin()) {
  $homeLoc = dirname($_SERVER['PHP_SELF']);
  echo "    <nav class='navbar fixed-top'>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-success id-nav' data-href='{$homeLoc}'>Home</button>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-info ml-auto mr-2 id-nav' data-href='users.php'>Users</button>" . PHP_EOL;
  echo "      <button class='btn btn-sm btn-outline-info id-nav' data-href='events.php'>Events</button>" . PHP_EOL;
  echo "    </nav>" . PHP_EOL;
}
?>
    <div class='modal d-block'>
      <div class='modal-dialog modal-sm modal-dialog-centered'>
        <div class='modal-content'>
          <div class='modal-body'>
<?php
foreach (['opener', 'light'] as $device) {
  if ($garage->isConfigured($device)) {
    $button = strtoupper($device);
    echo "            <div class='row justify-content-center'>" . PHP_EOL;
    echo "              <div class='col-auto my-2'><button class='btn btn-outline-info btn-lg id-activate' data-device='{$device}'><h1 class='my-1'>{$button}</h1></button></div>" . PHP_EOL;
    echo "            </div>" . PHP_EOL;
  }
}
?>
          </div>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('button.id-activate').click(function() {
          $('button.id-activate').prop('disabled', true);
          $.get('src/action.php', {"func": "doActivate", "device": $(this).data('device')})
            .done(function(data) {
              if (data.success) {
                alert('Success!');
              } else {
                alert('Failed!');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`doActivate failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            })
            .always(function() {
              $('button.id-activate').prop('disabled', false);
            });
        });

        $('button.id-nav').click(function() {
          location.href=$(this).data('href');
        });
      });
    </script>
  </body>
</html>
