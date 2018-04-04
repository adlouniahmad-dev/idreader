var AppCalendar = function() {

    return {
        init: function() {
            this.initCalendar();
        },

        initCalendar: function() {

            if (!jQuery().fullCalendar) {
                return;
            }

            let h = {};
            const $calendar = $('#calendar');

            if (App.isRTL()) {
                if ($calendar.parents(".portlet").width() <= 720) {
                    $calendar.addClass("mobile");
                    h = {
                        left: 'agendaWeek'
                    };
                } else {
                    $calendar.removeClass("mobile");
                    h = {
                        left: 'agendaWeek'
                    };
                }
            } else {
                if ($calendar.parents(".portlet").width() <= 720) {
                    $calendar.addClass("mobile");
                    h = {
                        right: 'agendaWeek'
                    };
                } else {
                    $calendar.removeClass("mobile");
                    h = {
                        right: 'agendaWeek'
                    };
                }
            }

            $calendar.fullCalendar('destroy');
            $calendar.fullCalendar({
                header: h,
                defaultView: 'agendaWeek',
                slotMinutes: 15,
                editable: false,
                droppable: false,
                eventSources: [{
                    url: '/api/getGuardSchedule/' + $calendar.data('id'),
                    type: 'post',
                    color: 'red',
                    textColor: 'black',
                }],
            });

        }

    };

}();

jQuery(document).ready(function() {    
   AppCalendar.init(); 
});