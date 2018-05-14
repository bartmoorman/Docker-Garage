<?php
require_once('inc/garage.class.php');

$garage = new Garage();

if ($garage->isConfigured()) {
  if ($garage->isValidSession()) {
    header('Location: ' . dirname($_SERVER['PHP_SELF']));
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
    <title>Garage - Login</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
    <style>
      span.id-digits {
        width: 27px;
        height: 27px;
      }
    </style>
  </head>
  <body class='h-100'>
    <div class='d-flex justify-content-center h-100'>
      <div class='align-self-center'>
        <div class='row justify-content-center'>
          <div class='col-auto mb-4 mt-2 mx-2 p-0'><span class='border border-secondary rounded-circle d-block id-digits'></span></div>
          <div class='col-auto mb-4 mt-2 mx-2 p-0'><span class='border border-secondary rounded-circle d-block id-digits'></span></div>
          <div class='col-auto mb-4 mt-2 mx-2 p-0'><span class='border border-secondary rounded-circle d-block id-digits'></span></div>
          <div class='col-auto mb-4 mt-2 mx-2 p-0'><span class='border border-secondary rounded-circle d-block id-digits'></span></div>
          <div class='col-auto mb-4 mt-2 mx-2 p-0'><span class='border border-secondary rounded-circle d-block id-digits'></span></div>
          <div class='col-auto mb-4 mt-2 mx-2 p-0'><span class='border border-secondary rounded-circle d-block id-digits'></span></div>
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
        <div class='row justify-content-center'>
          <div class='col-auto m-2 p-0'><button class='btn btn-outline-info btn-lg rounded-circle px-4 id-number'><h1 class='my-1'>0</h1></button></div>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        var pincode = '';

        $('button.id-number').click(function() {
          pincode = pincode + $(this).text().toString();
          $(`span.id-digits:eq(${pincode.length - 1})`).addClass('bg-success');

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
                $('span.id-digits').removeClass('bg-success');
                $('button.id-number').prop('disabled', false);
              });
          }
        });
      });
    </script>
  </body>
</html>
