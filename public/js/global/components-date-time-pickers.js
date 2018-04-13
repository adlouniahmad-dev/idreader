var ComponentsDateTimePickers = function () {

    var handleDatePickers = function () {

        if (jQuery().datepicker) {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                orientation: "right",
                autoclose: true
            });
        }
    }

    var handleTimePickers = function () {

        if (jQuery().timepicker) {

            $('.timepicker-24').timepicker({
                autoclose: true,
                minuteStep: 1,
                showSeconds: false,
                showMeridian: false,
                defaultTime: '',
            });

            // handle input group button click
            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function(e){
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });
        }
    };

    return {
        init: function () {
            handleDatePickers();
            handleTimePickers();
        }
    };
}();

if (App.isAngularJsApp() === false) { 
    jQuery(document).ready(function() {    
        ComponentsDateTimePickers.init(); 
    });
}