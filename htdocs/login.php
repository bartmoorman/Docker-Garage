<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, false, false, true);
?>
<!DOCTYPE html>
<html class='h-100' lang='en'>
  <head>
    <title>Garage - Login</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no'>
    <meta name='apple-mobile-web-app-capable' content='yes'>
    <meta name='apple-mobile-web-app-title' content='Garage'>
    <meta name='apple-mobile-web-app-status-bar-style' content='black-translucent'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
    <style>
      input.id-digit {
        font-size: 32px;
        text-align: center;
      }
    </style>
  </head>
  <body class='h-100'>
    <div class='d-flex justify-content-center h-100'>
      <div class='align-self-center'>
        <div class='row justify-content-center'>
          <div class='input-group mb-4 mt-2'>
            <input class='form-control p-0 id-digit' size='1' readonly>
            <input class='form-control p-0 id-digit' size='1' readonly>
            <input class='form-control p-0 id-digit' size='1' readonly>
            <input class='form-control p-0 id-digit' size='1' readonly>
            <input class='form-control p-0 id-digit' size='1' readonly>
            <input class='form-control p-0 id-digit' size='1' readonly>
          </div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>1</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>2</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>3</h1></button></div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>4</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>5</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>6</h1></button></div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>7</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>8</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>9</h1></button></div>
        </div>
        <div class='row justify-content-end'>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>0</h1></button></div>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-danger btn-lg rounded-circle px-4 id-clear'><h1 class='my-1'>&lt;</h1></button></div>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        var pincode = '';
        var timer;
        $('button.id-number').click(function() {
          pincode = pincode + $(this).text().toString();
          var digit = pincode.length - 1;
          if (digit > 0) {
            clearTimeout(timer);
            $(`input.id-digit:eq(${digit - 1})`).val('*');
          }
          $(`input.id-digit:eq(${digit})`).addClass('border-success').val($(this).text().toString());
          timer = setTimeout(function() {
            $(`input.id-digit:eq(${digit})`).val('*');
          }, 365);
          if (pincode.length == 6) {
            $('button.id-number').prop('disabled', true);
            $.getJSON('src/action.php', {"func": "validatePinCode", "pincode": pincode})
              .done(function(data) {
                if (data.success) {
                  location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`validatePinCode failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              })
              .always(function() {
                pincode = '';
                clearTimeout(timer);
                $('input.id-digit').removeClass('border-success').val('');
                $('button.id-number').prop('disabled', false);
              });
          }
        });

        $('button.id-clear').click(function() {
          pincode = '';
          clearTimeout(timer);
          $('input.id-digit').removeClass('border-success').val('');
        });
      });
    </script>
  </body>
</html>
