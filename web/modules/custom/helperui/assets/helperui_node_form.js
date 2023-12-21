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
      $("form#helper-node-form", context).once('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("#edit-field-name-pub-und-0-value").css("border", "3px solid red");

        // Make the Animal Types field group label bold (now that it is no longer collapsible)
        $("fieldset.group-animalgroups").find("span.fieldset-legend").css("font-weight", "700");

        //20220104 Tena Murphy do not display oldhelperid field, per Elena
        //20220114 Tena Murphy display oldhelperid field, per Elena
        //$("div#edit-field-oldhelperid").css("display", "none");

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

          // style an extra submit button at the top of the form
          $("input#edit-submit-top").css("float", "right");
          $("input#edit-submit-top").css("padding", "10px");
          $("input#edit-submit-top").css("font-size", "20px");
          $("input#edit-submit-top").css("margin-right", "80px");

          // style the regular submit button at the bottom of the form
          $("input#edit-submit").css("padding", "10px");
          $("input#edit-submit").css("font-size", "20px");

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
          
          //$("fieldset.group-helper-record-details").css("display", "none");
          $("div#edit-field-oldhelperid").css("display", "none");
          $("div#edit-field-helpertype-id").css("display", "none");
          $("div#edit-field-notes-internal").css("display", "none");
          $("div#edit-field-vethelpswildlife").css("display", "none");
          $("div#edit-field-donotsendbulkahnowupdates").css("display", "none");
          $("div#edit-field-donotcontact").css("display", "none");
          $("div#edit-field-donotdisplay").css("display", "none");
          $("div#edit-field-dnd-reason").css("display", "none");
          //$("div#edit-field-donotdisplay-dates").css("display", "none");
          //$("div#edit-field-donotdisplay-reason").css("display", "none");
          $("div#edit-field-displayintopresults").css("display", "none");
          $("div#edit-field-gfascertified").css("display", "none");
          $("div#edit-field-wrencertified").css("display", "none");
          $("div#edit-field-hours").css("display", "none");
          $("div#edit-field-hoursknown").css("display", "none");
          $("fieldset.group-jurisdictional-helper").css("display", "none");
          $("div#edit-field-geoposition").css("display", "none");
          $("fieldset.group-licensee-info").css("display", "none");
          $("fieldset.group-hidden-fields").css("display", "none");
          $("input#edit-preview").css("display", "none");
          $("input#edit-preview-changes").css("display", "none");
          $("div.vertical-tabs").css("display", "none");

          // set some fields even though they are not visible
          $("input#edit-field-hoursknown-und").attr("checked", "checked");
        } else {
          // disable an extra submit button at the top of the form
          $("input#edit-submit-top").css("display", "none");
        }

      });


      // Loading the Helper node edit page will trigger this
      // If the daily office hours have not yet been populated,
      //  it will use the coded hours field to populate them
      // This will be useful to populate the daily office hours
      //  fields by attrition whenever each record is edited and saved
      //
      $("input#edit-field-hours-und-0-value", context).once('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("#edit-field-name-pub-und-0-value").css("border", "3px solid red");

        // Initialize processing variables
        var codedHoursString = "";
        var continueProcess = false;
        var startHourStr = "";

        // Get the coded hours field value
        codedHoursString = $("input#edit-field-hours-und-0-value").val();

        if (codedHoursString > "") {
          continueProcess = true;
          // Check if the daily hours values are populated at all
          for (i = 0; i < 14; i++) {
            startHourStr = $("select#edit-field-hours-available-und-" + i + "-starthours-hours").val();
            if (startHourStr > "") {
              continueProcess = false;
            }
          }
        }
        if (continueProcess) {
          // Execute the function to copy the coded hours info into
          //  the daily hours fields
          coded_to_daily();
        }
      });


      // Any change to primary contact first name fields will trigger this
      // If primary contact preferred name is empty, it updates the
      //  primary contact preferred name to match the primary contact
      //  first name field
      //
      $("input#edit-field-contactfirst-und-0-value", context).change('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("input#edit-field-contactlast-und-0-value").css("border", "3px solid red");

        var contactFirstString = "";
        var contactPreferredString = "";

        // Get the primary contact preferred name field value
        contactPreferredString = $("input#edit-field-contactpreferred-und-0-value").val();

        if (contactPreferredString > "") {
          // Do nothing
          contactFirstString = "";
        } else {
          // Get the primary contact first name field value
          contactFirstString = $("input#edit-field-contactfirst-und-0-value").val();
  
          // Populate the primary contact preferred name field
          $("input#edit-field-contactpreferred-und-0-value").val(contactFirstString);
        }
      });

      // Any change to any of the daily office hours fields will trigger this
      // It updates the coded hours field to match the changes to the
      //  daily office hours fields and disables the coded hours field
      // MUST ENABLE THE CODED HOURS FIELD UPON SUBMIT
      //   OTHERWISE CHANGES WILL NOT BE SAVED IN THE CODED HOURS FIELD
      // SEE ("#helper-node-form", context).submit('HelperUI'.......
      //
      $("div#edit-field-hours-available", context).change('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("input#edit-field-hours-und-0-value").css("border", "3px solid red");

        // Execute the function to copy the daily hours info into
        //  the coded hours field
        daily_to_coded();

        // Disable the coded hours fields because user is using the daily hours fields.
        // If the field is disabled during form submit, the data will NOT
        //  be sent for save to database
        // MUST ENABLE THE CODED HOURS FIELD UPON SUBMIT
        //   OTHERWISE CHANGES WILL NOT BE SAVED IN THE CODED HOURS FIELD
        // SEE ("#helper-node-form", context).submit('HelperUI'.......
        //
        $("input#edit-field-hours-und-0-value").attr("disabled", "disabled");
        $("input#edit-field-hours-und-0-value").css("background", "#EEEEEE");

      });

      // Clicking "Remove" on the daily office hours fields will trigger this
      // It is identical to the function triggered by data change above
      //
      $("a.oh-clear-link", context).click('HelperUI', function () {
        daily_to_coded();
        $("input#edit-field-hours-und-0-value").attr("disabled", "disabled");
        $("input#edit-field-hours-und-0-value").css("background", "#EEEEEE");
      });

      // Clicking "Same as above" on the daily office hours fields will trigger this
      // It is identical to the function triggered by data change above
      //
      $("a.oh-same-link", context).click('HelperUI', function () {
        daily_to_coded();
        $("input#edit-field-hours-und-0-value").attr("disabled", "disabled");
        $("input#edit-field-hours-und-0-value").css("background", "#EEEEEE");
      });

      // Clicking "Add new Time Range" on the daily office hours fields will trigger this
      // It is identical to the function triggered by data change above
      //
      $("a.oh-add-more-link", context).click('HelperUI', function () {
        daily_to_coded();
        $("input#edit-field-hours-und-0-value").attr("disabled", "disabled");
        $("input#edit-field-hours-und-0-value").css("background", "#EEEEEE");
      });


      // Any change to the coded hours field will trigger this
      // It verifies the validity of the coded hours field first
      // It updates the daily office hours fields to match the changes to the
      //  coded hours field and disables all of the daily office hours fields
      // MUST ENABLE THE DAILY OFFICE HOURS FIELDS UPON SUBMIT
      //   OTHERWISE CHANGES WILL NOT BE SAVED IN THE DAILY OFFICE HOURS FIELDS
      // SEE ("#helper-node-form", context).submit('HelperUI'.......
      //
      $("input#edit-field-hours-und-0-value", context).change('HelperUI', function () {
        // Apply an effect to an element to verify the function is called.
        //$("#edit-field-hours").css("border", "3px solid red");

        // Initialize processing variables
        var codedHoursString = "";
        var continueProcess = true;
        var dayhourArray = [];
        var dayhourStr = "";
        var spaceIndex = 0;
        var dayfirstStr = "";
        var daylastStr = "";
        var hourrangeStr = "";
        var dashIndex = 0;
        var hourfirstStr = "00";
        var hourfirstInt = 0;
        var hourlastStr = "24";
        var hourlastInt = 0;
        var dotIndex = 0;
        var minfirstStr = "00";
        var minlastStr = "00";
        var ampmfirstStr = "am";
        var ampmlastStr = "am";
        var testFirstHoursSet = "";

        // Set up array of days
        var daysArray = [];
        daysArray[0] = "M";
        daysArray[1] = "T";
        daysArray[2] = "W";
        daysArray[3] = "R";
        daysArray[4] = "F";
        daysArray[5] = "S";
        daysArray[6] = "U";  // in officehours module, this is index 0, not 7

        // Get the coded hours field value
        codedHoursString = $("input#edit-field-hours-und-0-value").val();
        dayhourArray = codedHoursString.split(";");
        
        //$.each(dayhourArray, function(dayhourIndex, dayhourValue) {
        //  alert(dayhourIndex + ': ' + dayhourValue);
        //});

        // Loop through the coded hours field values which are separated by semicolon
        // Semicolon separates different "dayrange hourrange" values
        // Example: M 08-19;T-W 08-20;R-F 08-19;S 08-16;U 08-14

        // This loop checks for valid values
        // If everything is valid, then the process will continue
        //  after this verification loop
        // IMPORTANT!!!  IF YOU MAKE ANY CHANGES IN THIS VALIDATION LOOP,
        //   YOU MAY (OR MAY NOT) NEED TO MAKE CORRESPONDING CHANGES
        //   IN THE coded_to_daily FUNCTION BELOW!!!
        $.each(dayhourArray, function(dayhourIndex, dayhourValue) {
          if (continueProcess) {
            dayhourStr = $.trim(dayhourValue);
            //alert(dayhourIndex + ': ' + dayhourStr);
            // Find the space within this "dayrange hourrange"
            spaceIndex = dayhourStr.indexOf(" ");
            if ((spaceIndex !== 1) && (spaceIndex !== 3)) {
              // Error: Must have semicolon between day/hour groups and space between day range and hour range
              alert('Incorrect format.  Seek help.  Example: M 09-17;T-W 08.5-20.5;R-F 08-12;S 09-13;U 13-17');
              continueProcess = false;
            }
            
            if (continueProcess) {
              dayfirstStr = dayhourStr.substring(0,1);  // first character
              if (spaceIndex === 1) {
                // This is a single day, not a range of days
                // Set end of range = start of range so the loop goes once
                daylastStr = dayfirstStr;
                hourrangeStr = dayhourStr.substring(2);   // remainder of string
              } else if (spaceIndex === 3) {
                // This is a range of days
                // Get last character as end of range
                daylastStr = dayhourStr.substring(2,3);  // last character
                hourrangeStr = dayhourStr.substring(4);   // remainder of string
              }
            
              if ((daysArray.indexOf(dayfirstStr) == -1) || (daysArray.indexOf(daylastStr) == -1)) {
                // Error: Days must be in the valid list of codes
                alert('Incorrect format.  Seek help.  Valid day codes are MTWRFSU');
                continueProcess = false;
              }
            }
 
            if (continueProcess) {
              if (spaceIndex === 3 && ((dayfirstStr == "U") || (daylastStr == "M"))) {
                // Error: Day range must not start with Sunday or end with Monday
                alert('Incorrect format.  Seek help.  Day range must not start with Sunday or end with Monday.');
                continueProcess = false;
              }
            }
            
            if (continueProcess) {
              //alert('dayfirstStr ' + dayhourIndex + ': ' + dayfirstStr);
              //alert('daylastStr ' + dayhourIndex + ': ' + daylastStr);
              //alert('hourrangeStr ' + dayhourIndex + ': ' + hourrangeStr);

              dashIndex = hourrangeStr.indexOf("-");
              if (dashIndex == -1) {
                // Error: Hour range must have a dash for the range
                alert('Incorrect format.  Seek help.  Hour range must contain a dash between start and end time.');
                continueProcess = false;
              }
            }

            if (continueProcess) {
              hourfirstStr = hourrangeStr.substring(0,dashIndex);
              //alert('hourfirstStr ' + dayhourIndex + ': ' + hourfirstStr);

              // Check if hour string contains float and nothing extraneous
              // This test does not fail for half hour format like 17.5
              if ((isNaN(parseFloat(hourfirstStr))) || ((hourfirstStr - 0) != hourfirstStr)) {
                // Error: Must have a number before dash
                alert('Incorrect format.  Seek help.  Start Time must be a valid number.');
                continueProcess = false;
              }
            }

            if (continueProcess) {
              hourfirstInt = parseInt(hourfirstStr);
              if ((hourfirstInt > 23) || (hourfirstInt < 0)) {
                // Error: Start Hour must be within 0 to 23
                alert('Incorrect format.  Seek help.  Valid Start Times are 0 through 23.5');
                continueProcess = false;
              }
            }

            if (continueProcess) {
              hourlastStr = hourrangeStr.substring(dashIndex + 1);
              //alert('hourlastStr ' + dayhourIndex + ': ' + hourlastStr);

              // Check if hour string contains float and nothing extraneous
              // This test does not fail for half hour format like 17.5
              if ((isNaN(parseFloat(hourlastStr))) || ((hourlastStr - 0) != hourlastStr)) {
                // Error: Must have a number after dash
                alert('Incorrect format.  Seek help.  Example: M 09-17;T-W 08.5-20.5;R-F 08-12;S 09-13;U 13-17');
                continueProcess = false;
              }
            }

            if (continueProcess) {
              hourlastInt = parseInt(hourlastStr);
              if ((hourlastInt <= hourfirstInt) || (hourlastInt > 24) || (hourlastInt < 0)) {
                // Error: Hour must be within 0 to 24.  End hour must be > begin hour.
                alert('Incorrect format.  Seek help.  Valid End Times are 0.5 through 24.  End Time must be > Start Time');
                continueProcess = false;
              }
            }

          }
        }); // end loop which checks for valid values


        // If no errors were found in the coded hours format, then continue the process by
        //  setting the daily hours which are given in the coded hours field
        if (continueProcess) {

          // Execute the function to copy the coded hours info into
          //  the daily hours fields
          coded_to_daily();

          // Disable the daily hours fields because user is using the coded hours field.
          // If the select lists are disabled during form submit, the data will NOT
          //  be sent for save to database
          // MUST ENABLE THE DAILY OFFICE HOURS FIELDS UPON SUBMIT
          //   OTHERWISE CHANGES WILL NOT BE SAVED IN THE DAILY OFFICE HOURS FIELDS
          // SEE ("#helper-node-form", context).submit('HelperUI'.......
          //
          for (i = 0; i < 14; i++) {
            $("select#edit-field-hours-available-und-"+i+"-starthours-hours").attr("disabled", "disabled");
            $("select#edit-field-hours-available-und-"+i+"-starthours-hours").css("background", "#EEEEEE");
            $("select#edit-field-hours-available-und-"+i+"-starthours-minutes").attr("disabled", "disabled");
            $("select#edit-field-hours-available-und-"+i+"-starthours-minutes").css("background", "#EEEEEE");
            $("select#edit-field-hours-available-und-"+i+"-starthours-ampm").attr("disabled", "disabled");
            $("select#edit-field-hours-available-und-"+i+"-starthours-ampm").css("background", "#EEEEEE");
            $("select#edit-field-hours-available-und-"+i+"-endhours-hours").attr("disabled", "disabled");
            $("select#edit-field-hours-available-und-"+i+"-endhours-hours").css("background", "#EEEEEE");
            $("select#edit-field-hours-available-und-"+i+"-endhours-minutes").attr("disabled", "disabled");
            $("select#edit-field-hours-available-und-"+i+"-endhours-minutes").css("background", "#EEEEEE");
            $("select#edit-field-hours-available-und-"+i+"-endhours-ampm").attr("disabled", "disabled");
            $("select#edit-field-hours-available-und-"+i+"-endhours-ampm").css("background", "#EEEEEE");
          }
          $("a.oh-clear-link").css("display", "none");
          $("a.oh-same-link").css("display", "none");
          $("a.oh-add-more-link").css("display", "none");
        }
      });


      // If a field is disabled during form submit, the data will NOT
      //  be sent for save to database
      //  Must remove the disable before submit
      $("#helper-node-form", context).submit('HelperUI', function () {
        // Make sure the daily office hours fields are not disabled for submit
        for (i = 0; i < 14; i++) {
          $("select#edit-field-hours-available-und-"+i+"-starthours-hours").removeAttr("disabled");
          $("select#edit-field-hours-available-und-"+i+"-starthours-minutes").removeAttr("disabled");
          $("select#edit-field-hours-available-und-"+i+"-starthours-ampm").removeAttr("disabled");
          $("select#edit-field-hours-available-und-"+i+"-endhours-hours").removeAttr("disabled");
          $("select#edit-field-hours-available-und-"+i+"-endhours-minutes").removeAttr("disabled");
          $("select#edit-field-hours-available-und-"+i+"-endhours-ampm").removeAttr("disabled");
        }
        // Make sure the coded hours field is not disabled for submit
        $("input#edit-field-hours-und-0-value").removeAttr("disabled");
      });




      // BELOW THIS COMMENT..........
      // PROCESSING FUNCTIONS WHICH CAN BE REUSED
      //  IN VARIOUS BEHAVIORS ABOVE

      // Function to use the daily office hours field info
      //  to populate the coded hours field
      function daily_to_coded() {
        // Apply an effect to an element to verify the function is called.
        //$("input#edit-field-hours-und-0-value").css("border", "3px solid red");

        var codedHoursString = "";
        var dayStr = "";
        var startHourStr = "";
        var startHourInt = 0;
        var startHourPad = "";
        var startMinuteStr = "";
        var startAmpmStr = "";
        var endHourStr = "";
        var endHourInt = 0;
        var endHourPad = "";
        var endMinuteStr = "";
        var endAmpmStr = "";

        // Set up array of days
        var daysArray = [];
        daysArray[0] = "M";
        daysArray[1] = "M";
        daysArray[2] = "T";
        daysArray[3] = "T";
        daysArray[4] = "W";
        daysArray[5] = "W";
        daysArray[6] = "R";
        daysArray[7] = "R";
        daysArray[8] = "F";
        daysArray[9] = "F";
        daysArray[10] = "S";
        daysArray[11] = "S";
        daysArray[12] = "U";
        daysArray[13] = "U";

        // Translate the new daily hours values into a coded hours value
        for (i = 0; i < 14; i++) {
          dayStr = daysArray[i];
          //startHourStr = $("select#edit-field-hours-available-und-" + i + "-starthours-hours").val();
          startHourStr = $("div.form-item-field-hours-available-und-" + i + "-starthours-hours select").val();
          startHourInt = parseInt(startHourStr) / 100;
          //startMinuteStr = $("select#edit-field-hours-available-und-" + i + "-starthours-minutes").val();
          startMinuteStr = $("div.form-item-field-hours-available-und-" + i + "-starthours-minutes select").val();
          if (startMinuteStr == "30") {
            startMinuteStr = ".5";
          } else {
            startMinuteStr = "";
          }
          //startAmpmStr = $("select#edit-field-hours-available-und-" + i + "-starthours-ampm").val();
          startAmpmStr = $("div.form-item-field-hours-available-und-" + i + "-starthours-ampm select").val();
          // Convert 12hr to 24hr
          if ((startHourInt == 12) && (startAmpmStr == "am")) {
            startHourInt = 0;
          } else if ((startHourInt < 12) && (startAmpmStr == "pm")) {
            startHourInt = startHourInt + 12;
          }
          if (startHourInt < 10) {
            startHourPad = "0";
          } else {
            startHourPad = "";
          }
          //endHourStr = $("select#edit-field-hours-available-und-" + i + "-endhours-hours").val();
          endHourStr = $("div.form-item-field-hours-available-und-" + i + "-endhours-hours select").val();
          endHourInt = parseInt(endHourStr) / 100;
          //endMinuteStr = $("select#edit-field-hours-available-und-" + i + "-endhours-minutes").val();
          endMinuteStr = $("div.form-item-field-hours-available-und-" + i + "-endhours-minutes select").val();
          if (endMinuteStr == "30") {
            endMinuteStr = ".5";
          } else {
            endMinuteStr = "";
          }
          //endAmpmStr = $("select#edit-field-hours-available-und-" + i + "-endhours-ampm").val();
          endAmpmStr = $("div.form-item-field-hours-available-und-" + i + "-endhours-ampm select").val();
          // Convert 12hr to 24hr
          if ((endHourInt == 12) && (endAmpmStr == "am")) {
            endHourInt = 24;
          } else if ((endHourInt < 12) && (endAmpmStr == "pm")) {
            endHourInt = endHourInt + 12;
          }
          if (endHourInt < 10) {
            endHourPad = "0";
          } else {
            endHourPad = "";
          }

          if (startHourStr > "") {
            if (codedHoursString > "") {
              codedHoursString = codedHoursString + ";";
            }
            codedHoursString = codedHoursString + dayStr + " " + startHourPad + startHourInt + startMinuteStr + "-" + endHourPad + endHourInt + endMinuteStr;
          }
        }

        // Populate the coded hours field
        $("input#edit-field-hours-und-0-value").val(codedHoursString);
      }  // END function daily_to_coded


      // Function to use the coded office hours field info
      //  to populate the daily hours fields
      function coded_to_daily() {
        // Apply an effect to an element to verify the function is called.
        //$("#edit-field-hours").css("border", "3px solid red");

        // Initialize processing variables
        var codedHoursString = "";
        var continueProcess = true;
        var dayhourArray = [];
        var dayhourStr = "";
        var spaceIndex = 0;
        var dayfirstStr = "";
        var daylastStr = "";
        var hourrangeStr = "";
        var dashIndex = 0;
        var hourfirstStr = "00";
        var hourfirstInt = 0;
        var hourlastStr = "24";
        var hourlastInt = 0;
        var dotIndex = 0;
        var minfirstStr = "00";
        var minlastStr = "00";
        var ampmfirstStr = "am";
        var ampmlastStr = "am";
        var testFirstHoursSet = "";

        // Set up array of days
        var daysArray = [];
        daysArray[0] = "M";
        daysArray[1] = "T";
        daysArray[2] = "W";
        daysArray[3] = "R";
        daysArray[4] = "F";
        daysArray[5] = "S";
        daysArray[6] = "U";  // in officehours module, this is index 0, not 7

        // Get the coded hours field value
        codedHoursString = $("input#edit-field-hours-und-0-value").val();
        dayhourArray = codedHoursString.split(";");
        
        // Null out all of the daily hours array values
        for (i = 0; i < 14; i++) {
          $("select#edit-field-hours-available-und-" + i + "-starthours-hours").val("");
          $("select#edit-field-hours-available-und-" + i + "-starthours-minutes").val("");
          $("select#edit-field-hours-available-und-" + i + "-starthours-ampm").val("");
          $("select#edit-field-hours-available-und-" + i + "-endhours-hours").val("");
          $("select#edit-field-hours-available-und-" + i + "-endhours-minutes").val("");
          $("select#edit-field-hours-available-und-" + i + "-endhours-ampm").val("");
        }

        // Loop through the coded hours field values which are separated by semicolon
        // Semicolon separates different "dayrange hourrange" values
        // Example: M 08-19;T-W 08-20;R-F 08-19;S 08-16;U 08-14
        $.each(dayhourArray, function(dayhourIndex, dayhourValue) {
            dayhourStr = $.trim(dayhourValue);
            //alert(dayhourIndex + ': ' + dayhourStr);
            // Find the space within this "dayrange hourrange"
            spaceIndex = dayhourStr.indexOf(" ");
            
            dayfirstStr = dayhourStr.substring(0,1);  // first character
            if (spaceIndex === 1) {
              // This is a single day, not a range of days
              // Set end of range = start of range so the loop goes once
              daylastStr = dayfirstStr;
              hourrangeStr = dayhourStr.substring(2);   // remainder of string
            } else if (spaceIndex === 3) {
              // This is a range of days
              // Get last character as end of range
              daylastStr = dayhourStr.substring(2,3);  // last character
              hourrangeStr = dayhourStr.substring(4);   // remainder of string
            }
            
            //alert('dayfirstStr ' + dayhourIndex + ': ' + dayfirstStr);
            //alert('daylastStr ' + dayhourIndex + ': ' + daylastStr);
            //alert('hourrangeStr ' + dayhourIndex + ': ' + hourrangeStr);

            dashIndex = hourrangeStr.indexOf("-");
            hourfirstStr = hourrangeStr.substring(0,dashIndex);
            //alert('hourfirstStr ' + dayhourIndex + ': ' + hourfirstStr);

            // Check for a half hour such as 17.5
            // This check must happen before parseInt
            dotIndex = hourfirstStr.indexOf(".");
            if (dotIndex > 0) {
              minfirstStr = "30";
            } else {
              minfirstStr = "00";
            }

            // Convert 24hr to 12hr
            hourfirstInt = parseInt(hourfirstStr);
            if ((hourfirstInt == 24) || (hourfirstInt === 0)) {
              hourfirstStr = "12";
              ampmfirstStr = "am";
            } else if (hourfirstInt == 12) {
              hourfirstStr = "12";
              ampmfirstStr = "pm";
            } else if ((hourfirstInt > 12) && (hourfirstInt < 24)) {
              hourfirstInt = hourfirstInt - 12;
              hourfirstStr = hourfirstInt.toString();
              ampmfirstStr = "pm";
            } else if (hourfirstInt < 12) {
              hourfirstStr = hourfirstInt.toString();
              ampmfirstStr = "am";
            }
            hourfirstStr = hourfirstStr + "00";

            hourlastStr = hourrangeStr.substring(dashIndex + 1);
            //alert('hourlastStr ' + dayhourIndex + ': ' + hourlastStr);

            // Check for a half hour such as 17.5
            // This check must happen before parseInt
            dotIndex = hourlastStr.indexOf(".");
            if (dotIndex > 0) {
              minlastStr = "30";
            } else {
              minlastStr = "00";
            }

            // Convert 24hr to 12hr
            hourlastInt = parseInt(hourlastStr);
            if ((hourlastInt == 24) || (hourlastInt === 0)) {
              hourlastStr = "12";
              ampmlastStr = "am";
            } else if (hourlastInt == 12) {
              hourlastStr = "12";
              ampmlastStr = "pm";
            } else if ((hourlastInt > 12) && (hourlastInt < 24)) {
              hourlastInt = hourlastInt - 12;
              hourlastStr = hourlastInt.toString();
              ampmlastStr = "pm";
            } else if (hourlastInt < 12) {
              hourlastStr = hourlastInt.toString();
              ampmlastStr = "am";
            }
            hourlastStr = hourlastStr + "00";

            //alert('hourfirstStr ' + dayhourIndex + ': ' + hourfirstStr);
            //alert('minfirstStr ' + dayhourIndex + ': ' + minfirstStr);
            //alert('ampmfirstStr ' + dayhourIndex + ': ' + ampmfirstStr);
            //alert('hourlastStr ' + dayhourIndex + ': ' + hourlastStr);
            //alert('minlastStr ' + dayhourIndex + ': ' + minlastStr);
            //alert('ampmlastStr ' + dayhourIndex + ': ' + ampmlastStr);

            // Set the daily hours array values
            for (i = daysArray.indexOf(dayfirstStr); i <= daysArray.indexOf(daylastStr); i++) {
              testFirstHoursSet = $("div.form-item-field-hours-available-und-"+(i*2)+"-starthours-hours select").val();
              if (testFirstHoursSet > "") {
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-starthours-hours select").parent().parent().parent().children("a.oh-add-more-link").hide();
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-starthours-hours select").parent().parent("div.office-hours-block").fadeIn("slow");
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-starthours-hours select").val(hourfirstStr);
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-starthours-minutes select").val(minfirstStr);
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-starthours-ampm select").val(ampmfirstStr);
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-endhours-hours select").val(hourlastStr);
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-endhours-minutes select").val(minlastStr);
                $("div.form-item-field-hours-available-und-"+((i*2)+1)+"-endhours-ampm select").val(ampmlastStr);
              } else {
                $("div.form-item-field-hours-available-und-"+(i*2)+"-starthours-hours select").val(hourfirstStr);
                $("div.form-item-field-hours-available-und-"+(i*2)+"-starthours-minutes select").val(minfirstStr);
                $("div.form-item-field-hours-available-und-"+(i*2)+"-starthours-ampm select").val(ampmfirstStr);
                $("div.form-item-field-hours-available-und-"+(i*2)+"-endhours-hours select").val(hourlastStr);
                $("div.form-item-field-hours-available-und-"+(i*2)+"-endhours-minutes select").val(minlastStr);
                $("div.form-item-field-hours-available-und-"+(i*2)+"-endhours-ampm select").val(ampmlastStr);
              }
            }

        });
      }  // END function coded_to_daily

    }
  };
})(jQuery);
