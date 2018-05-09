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
    <title>Log in</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
    <style>
      #pincode {
        display: block;
      }
      #pincode span {
        width: 15px;
        height: 15px;
        display: inline-block;
      }
    </style>
  </head>
  <body class='h-100'>
    <div class='d-flex justify-content-center h-100' id='pincode'>
      <div class='align-self-center'>
        <div class='row' justify-content-center>
          <div class='col-auto mb-4 mt-1 mx-2 p-0'><span class='border border-secondary rounded-circle'></span></div>
          <div class='col-auto mb-4 mt-1 mx-2 p-0'><span class='border border-secondary rounded-circle'></span></div>
          <div class='col-auto mb-4 mt-1 mx-2 p-0'><span class='border border-secondary rounded-circle'></span></div>
          <div class='col-auto mb-4 mt-1 mx-2 p-0'><span class='border border-secondary rounded-circle'></span></div>
          <div class='col-auto mb-4 mt-1 mx-2 p-0'><span class='border border-secondary rounded-circle'></span></div>
          <div class='col-auto mb-4 mt-1 mx-2 p-0'><span class='border border-secondary rounded-circle'></span></div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>1</button></div>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>2</button></div>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>3</button></div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>4</button></div>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>5</button></div>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>6</button></div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>7</button></div>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>8</button></div>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>9</button></div>
        </div>
        <div class='row justify-content-center'>
          <div class='col-auto m-1 p-0'><button class='btn btn-outline-primary btn-lg rounded-circle'>0</button></div>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        var pincode = '';

        $('#pincode button').click(function() {
          pincode = pincode + $(this).text().toString();
          $(`#pincode span:eq(${pincode.length - 1})`).addClass('bg-primary');

          if (pincode.length == 6) {
            $('#pincode button').prop('disabled', true);

            $.getJSON('src/user.php', {"action": "validate", "pincode": pincode})
              .done(function(data) {
                if (data.success) {
                  location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
                } else {
                  pincode = '';
                  $('#pincode span').removeClass('bg-primary');
                  $('#pincode button').prop('disabled', false);
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`validation failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              });
          }
        });
      });
    </script>
  </body>
</html>
