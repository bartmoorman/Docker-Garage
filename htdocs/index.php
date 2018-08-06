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
  echo "            <h4 class='modal-title text-muted mx-auto'>Garage is <strong class='text-secondary id-position'>LOADING</strong></h4>" . PHP_EOL;
  echo "          </div>" . PHP_EOL;
}

if ($garage->isConfigured('opener')) {
  echo "          <div class='modal-body text-center'>" . PHP_EOL;
  echo "            <button class='btn btn-lg btn-outline-info id-activate' data-device='opener'><h2 class='my-auto'>ACTIVATE</h2></button>" . PHP_EOL;
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
        var position = {"class": "text-secondary"};
        var activate = {"class": "btn-outline-info"};

        function getPosition() {
          $.get('src/action.php', {"func": "getPosition", "device": "sensor"})
            .done(function(data) {
              if (data.success) {
                switch (data.data.trim()) {
                  case '0':
                    if (position.state != 'open') {
                      $('strong.id-position').text('OPEN').toggleClass(`${position.class} text-warning`);
                      $('button.id-activate').toggleClass(`${activate.class} btn-outline-success`).children('h2').text('CLOSE');
                      position = {"state": "open", "class": "text-warning"};
                      activate = {"class": "btn-outline-success"};
                    }
                    break;
                  case '1':
                    if (position.state != 'closed') {
                      $('strong.id-position').text('CLOSED').toggleClass(`${position.class} text-success`);
                      $('button.id-activate').toggleClass(`${activate.class} btn-outline-warning`).children('h2').text('OPEN');
                      position = {"state": "closed", "class": "text-success"};
                      activate = {"class": "btn-outline-warning"};
                    }
                    break;
                  default:
                    if (position.state != 'unknown') {
                      $('strong.id-position').text('UNKNOWN').toggleClass(`${position.class} text-danger`);
                      $('button.id-activate').toggleClass(`${activate.class} btn-outline-info`).children('h2').text('ACTIVATE');
                      position = {"state": "unknown", "class": "text-danger"};
                      activate = {"class": "btn-outline-info"};
                    }
                }
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`getPosition failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            })
            .always(function() {
              setTimeout(getPosition, 2.5 * 1000);
            });
        }

        getPosition();

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
