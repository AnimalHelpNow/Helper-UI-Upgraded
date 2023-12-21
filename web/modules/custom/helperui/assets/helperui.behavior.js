(function ($, Drupal) {
    Drupal.behaviors.helperui_global = {
        init: false,
        attach: function (context, settings) {
            if (Drupal.behaviors.helperui_global.init) { return; }
            Drupal.behaviors.helperui_global.init = true
            console.log('enther');

            $(document).ready(function() {
                Inputmask("(999) 999-9999", {
                    greedy: false,
                }).mask('.masked');
            });
        }
    }
})(jQuery, Drupal);
