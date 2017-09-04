/**
 * Created by on 04-Sep-17.
 */

jQuery(document).ready(function ($)
{
  // local
  var wpajax_url = document.location.protocol + '//' + document.location.host + '/wordpress/email-list-builder' + '/wp-admin/admin-ajax.php';
  // remote
  // var wpajax_url = document.location.protocol + '//' + document.location.host + '/wp-admin/admin-ajax.php';

  // url of save_subscription function
  var email_capture_url = wpajax_url += '?action=elb_save_subscription';

  $('form.elb-form').bind('submit', function ()
  {
    var $form = $(this);

    // turn form element into string
    var form_data = $form.serialize();

    $.ajax({
      method:   'post',
      url:      email_capture_url,
      data:     form_data,
      dataType: 'json',
      cache:    false,
      success:  function (response, textStatus)
      {
        // success
        if (response.status == 1) {
          // extract html form element, then reset
          $form[0].reset();
          alert(response.message);
        }
        // error
        else {
          var message = response.message + '\r' + response.error + '\r';

          $.each(response.errors, function (error)
          {
            message += '\r';
            message += '- ' + error;
          });
          alert(message);
        }
      },
      error:    function (jqXHR, textStatus, errorThrown)
      {

      }
    });

    // prevent form submission
    return false;
  });

});