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

      // Loading the Helper node edit page will trigger this
      // If the current user is logged in but does not have any
      //  privileged role, then it is a logged in Helper accessing
      //  only their own record.  Restrict their view of fields.
      //
      $("article.node-helper", context).once('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("div#breadcrumb").css("border", "3px solid red");

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
          // hide some fields so they are not visible
          $("fieldset.group-dummy").find("a.fieldset-title").attr("href", "");
          $("fieldset.group-dummy").find("span.fieldset-legend").css("background-image", "none");
          $("fieldset.group-dummy").find("div.fieldset-wrapper").css("display", "none");
          $("fieldset.group-dummy").find("div.fieldset-description").css("display", "none");
          $("fieldset.group-dummy").find("div.field-name-field-dummy").css("display", "none");
          
          $("div.field-name-field-oldhelperid").css("display", "none");
          $("div.field-name-field-helpertype-id").css("display", "none");
          $("div.field-name-field-notes-internal").css("display", "none");
          $("div.field-name-field-vethelpswildlife").css("display", "none");
          $("div.field-name-field-donotsendbulkahnowupdates").css("display", "none");
          $("div.field-name-field-donotcontact").css("display", "none");
          $("div.field-name-field-donotdisplay").css("display", "none");
          $("div.field-name-field-dnd-reason").css("display", "none");
          $("div.field-name-field-donotdisplay-dates").css("display", "none");
          $("div.field-name-field-donotdisplay-reason").css("display", "none");
          $("div.field-name-field-displayintopresults").css("display", "none");
          $("div.field-name-field-gfascertified").css("display", "none");
          $("div.field-name-field-wrencertified").css("display", "none");
          $("div.field-name-field-hours").css("display", "none");
          $("div.field-name-field-hoursknown").css("display", "none");
          $("fieldset.group-jurisdictional-helper").css("display", "none");
          $("div.field-name-field-geoposition").css("display", "none");
          $("fieldset.group-licensee-info").css("display", "none");
          $("fieldset.group-hidden-fields").css("display", "none");
          $("footer.submitted").css("display", "none");
        }

      });

    }
  };
})(jQuery);
