<?php
require_once('inc/garage.class.php');
$garage = new Garage(false, true, false, true);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Garage - Setup</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
  </head>
  <body>
    <div class='modal d-block'>
      <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'>Garage Setup</h5>
            </div>
            <div class='modal-body'>
              <div class='row justify-content-center'>
                <div class='col-auto'>
                  <input class='form-control' id='pincode' type='tel' name='pincode' placeholder='Numeric Pin Code' minlength='6' maxlength='6' pattern='[0-9]{6}' required>
                  <input class='form-control' id='first_name' type='text' name='first_name' placeholder='First Name' required>
                  <input class='form-control' id='last_name' type='text' name='last_name' placeholder='Last Name (optional)'>
                  <input class='form-control' id='pushover_user' type='text' name='pushover_user' placeholder='Pushover User (optional)' minlegth='30' maxlength='30' pattern='[a-z0-9]{30}'>
                  <input class='form-control' id='pushover_token' type='text' name='pushover_token' placeholder='Pushover Token (optional)' minlegth='30' maxlength='30' pattern='[a-z0-9]{30}'>
                  <input class='form-control' id='role' type='hidden' name='role' value='admin' required>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='submit' class='btn btn-info'>Setup</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('form').submit(function(e) {
          e.preventDefault();
          $.getJSON('src/action.php', {"func": "createUser", "pincode": $('#pincode').val(), "first_name": $('#first_name').val(), "last_name": $('#last_name').val(), "pushover_user": $('#pushover_user').val(), "pushover_token": $('#pushover_token').val(), "role": $('#role').val()})
            .done(function(data) {
              if (data.success) {
                location.href = '<?php echo dirname($_SERVER['PHP_SELF']) ?>';
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`createUser failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });
      });
    </script>
  </body>
</html>
