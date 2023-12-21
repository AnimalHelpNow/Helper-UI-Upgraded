// jshint maxerr:200
/**
 * @file
 * Javascript code for HelperUI module.
 * If you are getting "too many errors" in the cpanel editor,
 *  it really means too many warnings.
 * You can bypass this message by setting the first line of this file
 *  to // jshint maxerr:200
 *  because the default appears to be 100
 */

(function ($) {
  Drupal.behaviors.HelperUI = {
    attach: function (context, settings) {

      // Loading the Helper user edit page will trigger this
      // If the current user is logged in but does not have any
      //  privileged role, then it is a logged in Helper accessing
      //  their own usser account record.  Restrict their view of fields.
      //
      $("form#user-profile-form", context).once('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("#edit-submit").css("border", "3px solid red");

        // Initialize processing variables
        var fieldRestrictions = true;

        //  roles 3=administrator, 4=editor, 5=staff, 6=developer
        $.each(Drupal.settings.userRoles, function(userRolesIndex, userRolesValue) {
          //alert(userRolesIndex + ': ' + userRolesValue);
          if (userRolesIndex == '3') {
            fieldRestrictions = false;
          }
          if (userRolesIndex == '4') {
            fieldRestrictions = false;
          }
          if (userRolesIndex == '5') {
            fieldRestrictions = false;
          }
          if (userRolesIndex == '6') {
            //alert ('Hello Developer');
            fieldRestrictions = false;
          }
        });
     
        if (fieldRestrictions) {
          //alert ('your fields are restricted');
          // hide some fields so they are not visible
          $("div.form-item-timezone").css("display", "none");
        }

      });

      // Loading the Helper user register page will trigger this
      // If the current user is logged in but does not have any
      //  privileged role, then it is a logged in Helper accessing
      //  their own usser account record.  Restrict their view of fields.
      //
      $("form#user-register-form", context).once('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        $("div.confirm-parent").css("border", "3px solid red");
/*
        // Initialize processing variables
        var fieldRestrictions = true;

        //  roles 3=administrator, 4=editor, 5=staff, 6=developer
        $.each(Drupal.settings.userRoles, function(userRolesIndex, userRolesValue) {
          //alert(userRolesIndex + ': ' + userRolesValue);
          if (userRolesIndex == '3') {
            fieldRestrictions = false;
          }
          if (userRolesIndex == '4') {
            fieldRestrictions = false;
          }
          if (userRolesIndex == '5') {
            fieldRestrictions = false;
          }
          if (userRolesIndex == '6') {
            //alert ('Hello Developer');
            fieldRestrictions = false;
          }
        });
     
        if (fieldRestrictions) {
          //alert ('your fields are restricted');
          // disable some fields so they are visible but not editable
          // the identifiers are slightly different for different kinds of fields
          $("input#edit-field-donotdisplay-und").attr("disabled", "disabled");
          $("input#edit-field-donotdisplay-und").css("background", "#EEEEEE");
        

          // hide some fields so they are not visible
          $("fieldset.group-dummy").find("a.fieldset-title").attr("href", "");
          $("fieldset.group-dummy").find("span.fieldset-legend").css("background-image", "none");
          $("fieldset.group-dummy").find("div.fieldset-wrapper").css("display", "none");
          $("fieldset.group-dummy").find("div.fieldset-description").css("display", "none");
          $("fieldset.group-dummy").find("div.field-name-field-dummy").css("display", "none");
          
          $("fieldset.group-helper-record-details").css("display", "none");
          $("div#edit-field-oldhelperid").css("display", "none");

        }
*/
      });

    }
  };
})(jQuery);
