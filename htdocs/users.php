<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, true, true, false);
$currentPage = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
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
    <nav class='navbar'>
      <button class='btn btn-sm btn-outline-success id-nav' data-href='<?php echo dirname($_SERVER['PHP_SELF']) ?>'>Home</button>
      <button class='btn btn-sm btn-outline-info ml-auto mr-2 id-nav' data-href='users.php'>Users</button>
      <button class='btn btn-sm btn-outline-info id-nav' data-href='events.php'>Events</button>
    </nav>
    <div class='container'>
      <table class='table table-striped table-hover table-sm'>
        <thead>
          <tr>
            <th><button type='button' class='btn btn-sm btn-outline-success id-add'>Add</button></th>
            <th>Pin Code</th>
            <th>User Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Begin</th>
            <th>End</th>
          </tr>
        </thead>
        <tbody>
<?php
foreach ($garage->getUsers() as $user) {
  $user_name = !empty($user['last_name']) ? sprintf('%2$s, %1$s', $user['first_name'], $user['last_name']) : $user['first_name'];
  $begin = !empty($user['begin']) ? date('m/d/Y, h:i A', $user['begin']) : null;
  $end = !empty($user['end']) ? date('m/d/Y, h:i A', $user['end']) : null;
  $tableClass = $user['disabled'] ? 'text-danger' : 'table-default';
  echo "          <tr class='{$tableClass}'>" . PHP_EOL;
  if ($user['disabled']) {
    echo "            <td><button type='button' class='btn btn-sm btn-outline-warning id-modify' data-action='enable' data-user_id='{$user['user_id']}'>Enable</button></td>" . PHP_EOL;
  } else {
    echo "            <td><button type='button' class='btn btn-sm btn-outline-info id-edit' data-user_id='{$user['user_id']}'>Edit</button></td>" . PHP_EOL;
  }
  echo "            <td>{$user['pincode']}</td>" . PHP_EOL;
  echo "            <td>{$user_name}</td>" . PHP_EOL;
  echo "            <td>{$user['email']}</td>" . PHP_EOL;
  echo "            <td>{$user['role']}</td>" . PHP_EOL;
  echo "            <td>{$begin}</td>" . PHP_EOL;
  echo "            <td>{$end}</td>" . PHP_EOL;
  echo "          </tr>" . PHP_EOL;
}
?>
        </tbody>
      </table>
    </div>
    <nav>
      <nav>
        <ul class='pagination justify-content-center'>
<?php
$pages = ceil($garage->getCount('users') / $garage->pageLimit);
$group = ceil($currentPage / 5);
$previousPage = $currentPage - 1;
$nextPage = $currentPage + 1;

if ($previousPage <= 0) {
  echo "        <li class='page-item disabled'><a class='page-link'>Previous</a></li>" . PHP_EOL;
} else {
  echo "        <li class='page-item'><a class='page-link id-page' data-page='{$previousPage}'>Previous</a></li>" . PHP_EOL;
}

for ($i=1; $i<=$pages; $i++) {
  if ($currentPage == $i) {
    echo "        <li class='page-item disabled'><a class='page-link bg-secondary id-page' data-page='{$i}'>{$i}</a></li>" . PHP_EOL;
  } elseif (ceil($i / 5) == $group) {
    echo "        <li class='page-item'><a class='page-link id-page' data-page='{$i}'>{$i}</a></li>" . PHP_EOL;
  }
}

if ($nextPage > $pages) {
  echo "        <li class='page-item disabled'><a class='page-link'>Next</a></li>" . PHP_EOL;
} else {
  echo "        <li class='page-item'><a class='page-link id-page' data-page='{$nextPage}'>Next</a></li>" . PHP_EOL;
}
?>
        </ul>
      </nav>
    </nav>
    <div class='modal fade id-modal'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'></h5>
            </div>
            <div class='modal-body'>
              <div class='form-row justify-content-center'>
                <div class='col-auto'>
                  <input class='form-control' id='pincode' type='tel' name='pincode' placeholder='Numeric Pin Code' minlegth='6' maxlength='6' pattern='[0-9]{6}' required>
                  <input class='form-control' id='first_name' type='text' name='first_name' placeholder='First Name' required>
                  <input class='form-control' id='last_name' type='text' name='last_name' placeholder='Last Name (optional)'>
                  <input class='form-control' id='email' type='email' name='email' placeholder='Email (optional)'>
                  <select class='form-control' id='role' name='role' required>
                    <option disabled>Role</option>
                    <option value='user'>user</option>
                    <option value='admin'>admin</option>
                  </select>
                  <input class='form-control' id='begin' type='datetime-local' name='begin'>
                  <input class='form-control' id='end' type='datetime-local' name='end'>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-outline-warning id-modify id-volatile' data-action='disable'>Disable</button>
              <button type='button' class='btn btn-outline-danger mr-auto id-modify id-volatile' data-action='delete'>Delete</button>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
              <button type='submit' class='btn id-submit'></button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src='//code.jquery.com/jquery-3.2.1.min.js' integrity='sha384-xBuQ/xzmlsLoJpyjoggmTEz8OWUFM0/RC5BsqQBDX2v5cMvDHcMakNTNrHIW2I5f' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js' integrity='sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q' crossorigin='anonymous'></script>
    <script src='//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js' integrity='sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/URI.js/1.19.1/URI.min.js' integrity='sha384-p+MfR+v7kwvUVHmsjMiBK3x45fpY3zmJ5X2FICvDqhVP5YJHjfbFDc9f5U1Eba88' crossorigin='anonymous'></script>
    <script src='//cdnjs.cloudflare.com/ajax/libs/URI.js/1.19.1/jquery.URI.min.js' integrity='sha384-zdBrwYVf1Tu1JfO1GKzBAmCOduwha4jbqoCt2886bKrIFyAslJauxsn9JUKj6col' crossorigin='anonymous'></script>
    <script>
      $(document).ready(function() {
        $('button.id-add').click(function() {
          $('h5.modal-title').text('Add User');
          $('form').removeData('user_id').data('func', 'createUser').trigger('reset');
          $('button.id-modify.id-volatile').addClass('d-none').removeData('user_id');
          $('button.id-submit').removeClass('btn-info').addClass('btn-success').text('Add');
          $('div.id-modal').modal('toggle');
        });

        $('button.id-edit').click(function() {
          $('h5.modal-title').text('Edit User');
          $('form').removeData('user_id').data('func', 'updateUser').trigger('reset');
          $('button.id-modify.id-volatile').removeClass('d-none').removeData('user_id');
          $('button.id-submit').removeClass('btn-success').addClass('btn-info').text('Save');
          $.getJSON('src/action.php', {"func": "userDetails", "user_id": $(this).data('user_id')})
            .done(function(data) {
              if (data.success) {
                user = data.data;
                $('form').data('user_id', user.user_id);
                $('#pincode').val(user.pincode);
                $('#first_name').val(user.first_name);
                $('#last_name').val(user.last_name);
                $('#email').val(user.email);
                $('#role').val(user.role);
                $('#begin').val(user.begin);
                $('#end').val(user.end);
                $('button.id-modify.id-volatile').data('user_id', user.user_id);
                $('div.id-modal').modal('toggle');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`userDetails failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });

        $('button.id-modify').click(function() {
          if (confirm(`Want to ${$(this).data('action').toUpperCase()} user ${$(this).data('user_id')}?`)) {
            $.getJSON('src/action.php', {"func": "modifyUser", "action": $(this).data('action'), "user_id": $(this).data('user_id')})
              .done(function(data) {
                if (data.success) {
                  location.reload();
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`removeUser failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              });
          }
        });

        $('form').submit(function(e) {
          e.preventDefault();
          $.getJSON('src/action.php', {"func": $(this).data('func'), "user_id": $(this).data('user_id'), "pincode": $('#pincode').val(), "first_name": $('#first_name').val(), "last_name": $('#last_name').val(), "email": $('#email').val(), "role": $('#role').val(), "begin": $('#begin').val(), "end": $('#end').val()})
            .done(function(data) {
              if (data.success) {
                location.reload();
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`${$(this).data('func')} failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });

        $('button.id-nav').click(function() {
          location.href=$(this).data('href');
        });

        $('a.id-page').click(function() {
          location.href=URI().removeQuery('page').addQuery('page', $(this).data('page'));
        });
      });
    </script>
  </body>
</html>
