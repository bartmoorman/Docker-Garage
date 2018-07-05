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
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' integrity='sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB' crossorigin='anonymous'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css' integrity='sha384-ae362vOLHy2F1EfJtpMbNW0i9pNM1TP2l5O4VGYYiLJKsaejqVWibbP6BSf0UU5i' crossorigin='anonymous'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.1.0/css/all.css' integrity='sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt' crossorigin='anonymous'>
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
    <script src='//code.jquery.com/jquery-3.3.1.min.js' integrity='sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js' integrity='sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T' crossorigin='anonymous'></script>
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
