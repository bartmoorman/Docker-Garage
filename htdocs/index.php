<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, true, false, false);

$sunrise = date('h:i A', date_sunrise(time(), SUNFUNCS_RET_TIMESTAMP, $garage->astro['latitude'], $garage->astro['longitude'], $garage->astro['zenith']['sunrise']));
$sunset = date('h:i A', date_sunset(time(), SUNFUNCS_RET_TIMESTAMP, $garage->astro['latitude'], $garage->astro['longitude'], $garage->astro['zenith']['sunset']));
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title><?php echo $garage->appName ?> - Index</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no'>
    <meta name='apple-mobile-web-app-capable' content='yes'>
    <meta name='apple-mobile-web-app-title' content='<?php echo $garage->appName ?>'>
    <meta name='apple-mobile-web-app-status-bar-style' content='black-translucent'>
    <link rel='apple-touch-icon' sizes='180x180' href='apple-touch-icon.png'>
<?php require_once('include.css'); ?>
    <style>
      nav.navbar {
        z-index: 1051;
      }
    </style>
  </head>
  <body>
<?php require_once('header.php'); ?>
    <div class='modal fade'>
      <div class='modal-dialog modal-sm modal-dialog-centered'>
        <div class='modal-content'>
<?php
if ($garage->isConfigured('sensor')) {
  echo "          <div class='modal-header'>" . PHP_EOL;
  echo "            <h4 class='modal-title text-muted mx-auto'><?php echo $garage->appName ?> is <strong class='text-secondary id-position'>LOADING</strong></h4>" . PHP_EOL;
  echo "          </div>" . PHP_EOL;
}

if ($garage->isConfigured('opener')) {
  echo "          <div class='modal-body text-center'>" . PHP_EOL;
  echo "            <button class='btn btn-lg btn-outline-info id-activate' data-device='opener'><h2 class='my-auto'>ACTIVATE</h2></button>" . PHP_EOL;
  echo "          </div>" . PHP_EOL;
}

echo "          <div class='modal-footer'>" . PHP_EOL;
echo "            <h5 class='modal-title text-muted mx-auto'><span class='fa fa-fw fa-sun'></span> {$sunrise}</h5>" . PHP_EOL;
echo "            <h5 class='modal-title text-muted mx-auto'><span class='fa fa-fw fa-moon'></span> {$sunset}</h5>" . PHP_EOL;
echo "          </div>" . PHP_EOL;
?>
        </div>
      </div>
    </div>
<?php require_once('include.js'); ?>
    <script>
      $(document).ready(function() {
        var position = {"class": "text-secondary"};
        var activate = {"class": "btn-outline-info"};

        function getPosition() {
          $.get('src/action.php', {"func": "getPosition", "device": "sensor"})
            .done(function(data) {
              if (data.success) {
                switch (data.data) {
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
              if (jqxhr.status == 403) {
                location.reload();
              } else {
                console.log(`getPosition failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              }
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
              if (jqxhr.status == 403) {
                location.reload();
              } else {
                console.log(`doActivate failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              }
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
