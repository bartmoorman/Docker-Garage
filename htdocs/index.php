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
      nav.navbar {
        z-index: 1051;
      }
    </style>
  </head>
  <body>
<?php
include_once('header.php');
?>
    <div class='modal fade'>
      <div class='modal-dialog modal-sm modal-dialog-centered'>
        <div class='modal-content'>
<?php
if ($garage->isConfigured('sensor')) {
  echo "          <div class='modal-header'>" . PHP_EOL;
  if ($position = $garage->getPosition('sensor')) {
    switch ($position) {
      case 0:
        $class = 'text-warning';
        $status = 'OPEN';
        break;
      case 1:
        $class = 'text-success';
        $status = 'CLOSED';
        break;
    }
    echo "            <h4 class='modal-title text-muted mx-auto'>Garage is <em class='{$class}'>{$status}</em></h4>" . PHP_EOL;
  } else {
    echo "            <h4 class='modal-title text-danger mx-auto'>Unable to read position!</h4>" . PHP_EOL;
  }
  echo "          </div>" . PHP_EOL;
}

if ($garage->isConfigured('opener')) {
  echo "          <div class='modal-body text-center'>" . PHP_EOL;
  if ($garage->isConfigured('sensor') && $position = $garage->getPosition('sensor')) {
    switch ($position) {
      case 0:
        $class = 'success';
        $action = 'CLOSE';
        break;
      case 1:
        $class = 'warning';
        $action = 'OPEN';
        break;
    }
    echo "            <button class='btn btn-outline-{$class} btn-lg id-activate' data-device='opener'><h2 class='my-auto'>{$action}</h2></button>" . PHP_EOL;
  } else {
    echo "            <button class='btn btn-outline-info btn-lg id-activate' data-device='opener'><h2 class='my-auto'>OPENER</h2></button>" . PHP_EOL;
  }
  echo "          </div>" . PHP_EOL;
}
?>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.3.1.min.js' integrity='sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js' integrity='sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('div.modal').modal({backdrop: false, keyboard: false});

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
