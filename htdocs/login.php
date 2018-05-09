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
    <link rel='stylesheet' href='css/bootstrap-pincode-input.css'>
  </head>
  <body class='h-100'>
    <div class='container h-100'>
      <div class='form-row h-100 justify-content-center'>
        <div class='col-auto align-self-center'>
          <input type='text' id='pincode'>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script src='js/bootstrap-pincode-input.js'></script>
    <script>
      $(document).ready(function() {
        $('#pincode').pincodeInput({inputs:6, complete:function(value, e, errorElement) {
          $.getJSON('src/user.php', {"action": "validate", "pincode": value})
            .done(function(data) {
              if (data.success) {
                location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
              } else {
                $(errorElement).html('Invalid Pin!');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              $(errorElement).html('This is awkward...');
              console.log(`validation failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            })
            .always(function() {
              $('#pincode').pincodeInput().data('plugin_pincodeInput').clear();
              $('#pincode').pincodeInput().data('plugin_pincodeInput').focus();
            });
        }});
        $('#pincode').pincodeInput().data('plugin_pincodeInput').focus();
      });
    </script>
  </body>
</html>
