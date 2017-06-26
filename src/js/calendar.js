import $ from 'jquery';
import fullCalendar from 'fullcalendar';

const Calendar = function () {

  let $calendar = $('#calendar');
  if ($calendar.length) {

    $calendar.fullCalendar({
      events: $calendar.data('source')
    });
  }

};

export default Calendar;
