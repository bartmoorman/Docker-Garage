<?php
require_once('inc/garage.class.php');
$garage = new Garage(true, true, true, false);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title><?php echo $garage->appName ?> - Users</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
<?php require_once('include.css'); ?>
  </head>
  <body>
<?php require_once('header.php'); ?>
    <div class='container'>
      <table class='table table-striped table-hover table-sm'>
        <thead>
          <tr>
            <th><button type='button' class='btn btn-sm btn-outline-success id-add'>Add</button></th>
            <th>User ID</th>
            <th>User Name</th>
            <th>Role</th>
            <th>Begin</th>
            <th>End</th>
            <th>Notifications</th>
          </tr>
        </thead>
        <tbody>
<?php
foreach ($garage->getObjects('users') as $user) {
  $user_name = !empty($user['last_name']) ? sprintf('%2$s, %1$s', $user['first_name'], $user['last_name']) : $user['first_name'];
  $begin = !empty($user['begin']) ? date('m/d/Y, h:i A', $user['begin']) : '&infin;';
  $end = !empty($user['end']) ? date('m/d/Y, h:i A', $user['end']) : '&infin;';
  $tableClass = $user['disabled'] ? 'text-warning' : 'table-default';
  echo "          <tr class='{$tableClass}'>" . PHP_EOL;
  echo "            <td><button type='button' class='btn btn-sm btn-outline-info id-details' data-user_id='{$user['user_id']}'>Details</button></td>" . PHP_EOL;
  echo "            <td>{$user['user_id']}</td>" . PHP_EOL;
  echo "            <td>{$user_name}</td>" . PHP_EOL;
  echo "            <td>{$user['role']}</td>" . PHP_EOL;
  echo "            <td>{$begin}</td>" . PHP_EOL;
  echo "            <td>{$end}</td>" . PHP_EOL;
  if (!empty($user['pushover_user']) && !empty($user['pushover_token'])) {
    echo "            <td><input type='checkbox' checked disabled></td>" . PHP_EOL;
  } else {
    echo "            <td><input type='checkbox' disabled></td>" . PHP_EOL;
  }
  echo "          </tr>" . PHP_EOL;
}
?>
        </tbody>
      </table>
    </div>
    <div class='modal fade id-modal'>
      <div class='modal-dialog'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'></h5>
            </div>
            <div class='modal-body'>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>PIN <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control' id='pin' type='tel' name='pin' minlegth='6' maxlength='6' pattern='[0-9]{6}' required>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>First Name <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control' id='first_name' type='text' name='first_name' required>
                </div>
                <div class='form-group col'>
                  <label>Last Name</label>
                  <input class='form-control' id='last_name' type='text' name='last_name'>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Role <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <select class='form-control' id='role' name='role' required>
                    <option value='user'>user</option>
                    <option value='admin'>admin</option>
                  </select>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Begin</label>
                  <input class='form-control' id='begin' type='datetime-local' name='begin'>
                </div>
                <div class='form-group col'>
                  <label>End</label>
                  <input class='form-control' id='end' type='datetime-local' name='end'>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Pushover User Key</label>
                  <input class='form-control' id='pushover_user' type='text' name='pushover_user' minlegth='30' maxlength='30' pattern='[A-Za-z0-9]{30}'>
                </div>
                <div class='form-group col'>
                  <label>Pushover App. Token</label>
                  <input class='form-control' id='pushover_token' type='text' name='pushover_token' minlegth='30' maxlength='30' pattern='[A-Za-z0-9]{30}'>
                </div>
              </div>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>Pushover Sound <sup><a target='_blank' href='https://pushover.net/api#sounds'>Listen</a></sup></label>
                  <select class='form-control' id='pushover_sound' name='pushover_sound'>
                    <option value=''>User Default</option>
<?php
foreach ($garage->getSounds() as $value => $text) {
  echo "                    <option value='{$value}'>{$text}</option>" . PHP_EOL;
}
?>
                  </select>
                </div>
                <div class='form-group col'>
                  <label>Pushover Priority</label>
                  <select class='form-control id-pushover_priority' id='pushover_priority' name='pushover_priority'>
<?php
for ($priority = -2; $priority <= 2; $priority++) {
  echo "                    <option value='{$priority}'>{$priority}</option>" . PHP_EOL;
}
?>
                  </select>
                </div>
              </div>
              <div class='form-row id-required'>
                <div class='form-group col'>
                  <label>Pushover Retry <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control id-pushover_retry' id='pushover_retry' type='number' name='pushover_retry' min='30' required>
                </div>
                <div class='form-group col'>
                  <label>Pushover Expire <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control id-pushover_expire' id='pushover_expire' type='number' name='pushover_expire' max='10800' required>
                </div>
              </div>
            </div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-outline-warning id-modify id-volatile'></button>
              <button type='button' class='btn btn-outline-danger mr-auto id-modify' data-action='delete'>Delete</button>
              <button type='button' class='btn btn-secondary' data-dismiss='modal'>Close</button>
              <button type='submit' class='btn id-submit'></button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php require_once('include.js'); ?>
    <script>
      $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $('button.id-add').click(function() {
          $('h5.modal-title').text('Add User');
          $('form').removeData('user_id').data('func', 'createUser').trigger('reset');
          $('select.id-pushover_priority').val(0);
          $('div.id-required').addClass('d-none');
          $('input.id-pushover_retry').val(60);
          $('input.id-pushover_expire').val(3600);
          $('button.id-modify').addClass('d-none').removeData('user_id');
          $('button.id-submit').removeClass('btn-info').addClass('btn-success').text('Add');
          $('div.id-modal').modal('toggle');
        });

        $('button.id-details').click(function() {
          $('h5.modal-title').text('User Details');
          $('form').removeData('user_id').data('func', 'updateUser').trigger('reset');
          $('button.id-modify').removeClass('d-none').removeData('user_id');
          $('button.id-submit').removeClass('btn-success').addClass('btn-info').text('Save');
          $.get('src/action.php', {"func": "getObjectDetails", "type": "user", "value": $(this).data('user_id')})
            .done(function(data) {
              if (data.success) {
                user = data.data;
                $('form').data('user_id', user.user_id);
                $('#pin').val(user.pin);
                $('#first_name').val(user.first_name);
                $('#last_name').val(user.last_name);
                $('#pushover_user').val(user.pushover_user);
                $('#pushover_token').val(user.pushover_token);
                $('#pushover_priority').val(user.pushover_priority);
                $('div.id-required').toggleClass('d-none', user.pushover_priority != 2 ? true : false)
                $('#pushover_retry').val(user.pushover_retry);
                $('#pushover_expire').val(user.pushover_expire);
                $('#pushover_sound').val(user.pushover_sound);
                $('#role').val(user.role);
                $('#begin').val(user.begin);
                $('#end').val(user.end);
                $('button.id-modify.id-volatile').data('action', user.disabled ? 'enable' : 'disable').text(user.disabled ? 'Enable' : 'Disable');
                $('button.id-modify').data('user_id', user.user_id);
                $('div.id-modal').modal('toggle');
              }
            })
            .fail(function(jqxhr, textStatus, errorThrown) {
              console.log(`getObjectDetails failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
            });
        });

        $('button.id-modify').click(function() {
          if (confirm(`Want to ${$(this).data('action').toUpperCase()} user ${$(this).data('user_id')}?`)) {
            $.get('src/action.php', {"func": "modifyObject", "action": $(this).data('action'), "type": "user_id", "value": $(this).data('user_id')})
              .done(function(data) {
                if (data.success) {
                  location.reload();
                }
              })
              .fail(function(jqxhr, textStatus, errorThrown) {
                console.log(`modifyObject failed: ${jqxhr.status} (${jqxhr.statusText}), ${textStatus}, ${errorThrown}`);
              });
          }
        });

        $('select.id-pushover_priority').change(function() {
          $('div.id-required').toggleClass('d-none', $(this).val() != 2 ? true : false);
        });

        $('form').submit(function(e) {
          e.preventDefault();
          $.post('src/action.php', {"func": $(this).data('func'), "user_id": $(this).data('user_id'), "pin": $('#pin').val(), "first_name": $('#first_name').val(), "last_name": $('#last_name').val(), "pushover_user": $('#pushover_user').val(), "pushover_token": $('#pushover_token').val(), "pushover_priority": $('#pushover_priority').val(), "pushover_retry": $('#pushover_retry').val(), "pushover_expire": $('#pushover_expire').val(), "pushover_sound": $('#pushover_sound').val(), "role": $('#role').val(), "begin": $('#begin').val(), "end": $('#end').val()})
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
      });
    </script>
  </body>
</html>
