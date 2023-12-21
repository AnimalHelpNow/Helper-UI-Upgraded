(function($, Drupal) {
  Drupal.behaviors.helper = {
    attach: function (context, settings) {
      $('.node-helper-form', context).once('helper').each(function() {
        var sameAsPhysAddress = $('input[name="field_copy_physicaladdress[value]"]');
        var mailingAddress = $('select[name="field_mailing_address[0][address][country_code]"]');

        // Helper Mailing Address form
        // If the Same as Physical Address checkbox is clicked hide the mailing address
        // and set the value in the backend.
        sameAsPhysAddress.click(function() {
          var isChecked = $(this).is(":checked");

          if(isChecked) {
            $('div[data-drupal-selector="edit-field-mailing-address-0"]').hide();
            $('button[value="Save"]').removeAttr('disabled');
          } else {
            $('div[data-drupal-selector="edit-field-mailing-address-0"]').show();

            if(mailingAddress.val() == '') {
              $('button[value="Save"]').attr('disabled', true);
            }
          }
        });

        // Add validation to mailing address field
        // If Same as Physical Address field is not checked
        // Prevent users from filling the form until the field is filled.
        $('button[value="Save"]').attr('disabled', true);
        if(sameAsPhysAddress.is(":checked") || mailingAddress.val() != '') {
          $('button[value="Save"]').removeAttr('disabled');
        }

        mailingAddress.on('change', function() {
          if($(this).val() != '' || sameAsPhysAddress.is(":checked")) {
            $('button[value="Save"]').removeAttr('disabled');   
          } else {
            $('button[value="Save"]').attr('disabled', true);
          }
        });
      });
    }
  };
})(jQuery, Drupal);