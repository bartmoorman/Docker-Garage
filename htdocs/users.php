<?php
require_once('inc/garage.class.php');

$garage = new Garage();

if ($garage->isConfigured()) {
  if (!$garage->isValidSession()) {
    header('Location: login.php');
    exit;
  } elseif (!$garage->isAdmin()) {
    header('Location: ' . dirname($_SERVER['PHP_SELF']));
    exit;
  }
} else {
  header('Location: setup.php');
  exit;
}
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title>Garage - Users</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
    <link rel='stylesheet' href='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css' integrity='sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm' crossorigin='anonymous'>
    <link rel='stylesheet' href='//bootswatch.com/4/darkly/bootstrap.min.css'>
    <link rel='stylesheet' href='//use.fontawesome.com/releases/v5.0.12/css/all.css' integrity='sha384-G0fIWCsCzJIMAVNQPfjH08cyYaUtMwjJwqiRKxxE/rx96Uroj1BtIQ6MLJuheaO9' crossorigin='anonymous'>
  </head>
  <body>
    <nav class='navbar'></nav>
    <div class='container' id='users'>
      <table class='table table-striped table-hover table-sm'>
        <thead>
          <tr>
            <th>Pin</th>
            <th>First</th>
            <th>Last</th>
            <th>Email</th>
            <th>Role</th>
            <th>Begin</th>
            <th>End</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
<?php
foreach ($garage->getUser() as $pin => $user) {
  echo "          <tr>" . PHP_EOL;
  echo "            <th>{$pin}</th>" . PHP_EOL;
  echo "            <td>{$user['first_name']}</td>" . PHP_EOL;
  echo "            <td>{$user['last_name']}</td>" . PHP_EOL;
  echo "            <td>{$user['email']}</td>" . PHP_EOL;
  echo "            <td>{$user['role']}</td>" . PHP_EOL;
  echo "            <td>{$user['begin']}</td>" . PHP_EOL;
  echo "            <td>{$user['end']}</td>" . PHP_EOL;
  echo "            <td>" . PHP_EOL;
  echo "              <button type='button' class='btn btn-sm btn-outline-info' data-toggle='modal' data-target='#editModal' data-pincode='{$pin}'>Edit</button>" . PHP_EOL;
  echo "              <button type='button' class='btn btn-sm btn-outline-danger' data-pincode='{$pin}'>Delete</button>" . PHP_EOL;
  echo "            </td>" . PHP_EOL;
  echo "          </tr>" . PHP_EOL;
}
?>
        </tbody>
      </table>
    </div>
    <div class='modal fade' id='editModal'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <form id='setup' method='post'>
            <div class='modal-header'>
              <h5 class='modal-title'>Edit User</h5>
              <button type='button' class='close' data-dismiss='modal'><span>&times;</span></button>
            </div>
            <div class='modal-body'>
              <div class='form-row justify-content-center'>
                <div class='col-auto'>
                  <input class='form-control' type='tel' name='pincode' placeholder='Numeric Pin Code' maxlength='6' pattern='[0-9]{6}' required disabled>
                  <input class='form-control' type='text' name='first_name' placeholder='First Name' required>
                  <input class='form-control' type='text' name='last_name' placeholder='Last Name' required>
                  <input class='form-control' type='email' name='email' placeholder='Email' required>
                  <select class='form-control' name='role' required>
                    <option disabled hidden>Role</option>
                    <option value='user'>user</option>
                    <option value='admin'>admin</option>
                  </select>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
              <button type='submit' class='btn btn-primary'>Save changes</button>
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
        $('#users button[data-toggle=modal]').click(function() {
          $.getJSON('src/user.php', {"action": "retrieve", "pincode": $(this).data('pincode')})
              .done(function(data) {
                if (data.success) {
                  user = data.data;
                  $('#editModal input[name=pincode]').val();
                  $('#editModal input[name=first_name]').val(user.first_name);
                  $('#editModal input[name=last_name]').val(user.last_name);
                  $('#editModal input[name=email]').val(user.email);
                  $('#editModal select[name=role]').val(user.role);
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`validation failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              });
        });

        $('#setup').submit(function(event) {
          event.preventDefault();
        });
      });
    </script>
  </body>
</html>
