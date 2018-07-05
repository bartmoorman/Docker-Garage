<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, false, false, true);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Garage - Login</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no'>
    <meta name='apple-mobile-web-app-capable' content='yes'>
    <meta name='apple-mobile-web-app-title' content='Garage'>
    <meta name='apple-mobile-web-app-status-bar-style' content='black-translucent'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css' integrity='sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB' crossorigin='anonymous'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootswatch/4.1.1/darkly/bootstrap.min.css' integrity='sha384-ae362vOLHy2F1EfJtpMbNW0i9pNM1TP2l5O4VGYYiLJKsaejqVWibbP6BSf0UU5i' crossorigin='anonymous'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.1.0/css/all.css' integrity='sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt' crossorigin='anonymous'>
    <style>
      input.id-digit {
        width: 42px;
        height: 52px;
        font-size: 32px;
      }
      button.id-number,button.id-clear {
        width: 72px;
        height: 72px;
      }
    </style>
  </head>
  <body>
    <div class='modal d-block'>
      <div class='modal-dialog modal-sm modal-dialog-centered'>
        <div class='modal-content'>
          <div class='modal-body'>
            <div class='form-row justify-content-center'>
<?php
for ($i=0; $i<6; $i++) {
  echo "              <div class='form-group my-2 p-0'><input class='form-control bg-secondary text-center text-white p-0 id-digit' size='1' disabled></div>" . PHP_EOL;
}
?>
            </div>
            <div class='row justify-content-center'>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='1'><h2 class='my-auto'>1</h2><h5>&nbsp;</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='2'><h2 class='my-auto'>2</h2><h5>abc</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='3'><h2 class='my-auto'>3</h2><h5>def</h5></button></div>
            </div>
            <div class='row justify-content-center'>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='4'><h2 class='my-auto'>4</h2><h5>ghi</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='5'><h2 class='my-auto'>5</h2><h5>jkl</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='6'><h2 class='my-auto'>6</h2><h5>mno</h5></button></div>
            </div>
            <div class='row justify-content-center'>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='7'><h2 class='my-auto'>7</h2><h5>pqrs</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='8'><h2 class='my-auto'>8</h2><h5>tuv</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='9'><h2 class='my-auto'>9</h2><h5>wxyz</h5></button></div>
            </div>
            <div class='row justify-content-center'>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-secondary rounded-circle border-0 id-number' disabled></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-info rounded-circle id-number' data-number='0'><h2 class='my-auto'>0</h2><h5>&nbsp;</h5></button></div>
              <div class='col-auto m-2 p-0'><button class='btn btn-outline-danger rounded-circle id-clear'><h5 class='my-auto'>clear</h5></button></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.3.1.min.js' integrity='sha384-tsQFqpEReu7ZLhBV2VZlAu7zcOV+rXbYlF2cqB8txI/8aZajjp4Bqd+V6D5IgvKT' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js' integrity='sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js' integrity='sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        var pin = '';
        var timer;

        function addNumberToPin(number) {
          pin += number;
          var digit = pin.length - 1;
          if (digit > 0) {
            clearTimeout(timer);
            $(`input.id-digit:eq(${digit - 1})`).val('*');
          }
          $(`input.id-digit:eq(${digit})`).addClass('border-success').val(number);
          timer = setTimeout(function() {
            $(`input.id-digit:eq(${digit})`).val('*');
          }, 750);
          if (pin.length == 6) {
            $('button.id-number').prop('disabled', true);
            $('button.id-clear').prop('disabled', true);
            $.post('src/action.php', {"func": "authenticateSession", "pin": pin})
              .done(function(data) {
                if (data.success) {
                  location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`authenticateSession failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              })
              .always(function() {
                clearTimeout(timer);
                pin = '';
                $('input.id-digit').removeClass('border-success').val('');
                $('button.id-number').prop('disabled', false);
                $('button.id-clear').prop('disabled', false);
              });
          }
        }

        function clearPin() {
          clearTimeout(timer);
          pin = '';
          $('input.id-digit').removeClass('border-success').val('');
        }

        $(document).keyup(function(event) {
          switch (true) {
            case /^[1]$/.test(event.key):
              addNumberToPin(1);
              break;
            case /^[2abc]$/.test(event.key):
              addNumberToPin(2);
              break;
            case /^[3def]$/.test(event.key):
              addNumberToPin(3);
              break;
            case /^[4ghi]$/.test(event.key):
              addNumberToPin(4);
              break;
            case /^[5jkl]$/.test(event.key):
              addNumberToPin(5);
              break;
            case /^[6mno]$/.test(event.key):
              addNumberToPin(6);
              break;
            case /^[7pqrs]$/.test(event.key):
              addNumberToPin(7);
              break;
            case /^[8tuv]$/.test(event.key):
              addNumberToPin(8);
              break;
            case /^[9wxyz]$/.test(event.key):
              addNumberToPin(9);
              break;
            case /^[0]$/.test(event.key):
              addNumberToPin(0);
              break;
            case /^[Bb]ackspace$/.test(event.key):
            case /^[Dd]elete$/.test(event.key):
              clearPin();
              break;
            }
        });

        $('button.id-number').click(function() {
          addNumberToPin($(this).data('number'));
        });

        $('button.id-clear').click(function() {
          clearPin();
        });
      });
    </script>
  </body>
</html>
