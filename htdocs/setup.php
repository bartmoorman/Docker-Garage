<?php
require_once('inc/garage.class.php');
$garage = new Garage(false, true, false, true);
?>
<!DOCTYPE html>
<html lang='en'>
  <head>
    <title><?php echo $garage->appName ?> - Setup</title>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1, shrink-to-fit=no'>
<?php require_once('include.css'); ?>
  </head>
  <body>
    <div class='modal fade'>
      <div class='modal-dialog modal-dialog-centered'>
        <div class='modal-content'>
          <form>
            <div class='modal-header'>
              <h5 class='modal-title'>Garage Setup</h5>
            </div>
            <div class='modal-body'>
              <div class='form-row'>
                <div class='form-group col'>
                  <label>PIN <sup class='text-danger' data-toggle='tooltip' title='Required'>*</sup></label>
                  <input class='form-control' id='pin' type='tel' name='pin' minlength='6' maxlength='6' pattern='[0-9]{6}' autofocus required>
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
                  <input class='form-control' id='role' type='text' name='role' value='admin' readonly required>
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
              <button type='submit' class='btn btn-info'>Setup</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<?php require_once('include.js'); ?>
    <script>
      $(document).ready(function() {
        $('[data-toggle="tooltip"]').tooltip();

        $('select.id-pushover_priority').val(0);
        $('div.id-required').addClass('d-none');
        $('input.id-pushover_retry').val(60);
        $('input.id-pushover_expire').val(3600);

        $('div.modal').modal({backdrop: false, keyboard: false});

        $('select.id-pushover_priority').change(function() {
          $('div.id-required').toggleClass('d-none', $(this).val() != 2 ? true : false);
        });

        $('form').submit(function(e) {
          e.preventDefault();
          $.post('src/action.php', {"func": "createUser", "pin": $('#pin').val(), "first_name": $('#first_name').val(), "last_name": $('#last_name').val(), "pushover_user": $('#pushover_user').val(), "pushover_token": $('#pushover_token').val(), "pushover_priority": $('#pushover_priority').val(), "pushover_retry": $('#pushover_retry').val(), "pushover_expire": $('#pushover_expire').val(), "pushover_sound": $('#pushover_sound').val(), "role": $('#role').val()})
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
