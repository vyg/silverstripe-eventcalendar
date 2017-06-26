const $ = require('jquery');
const fullCalendar = require('fullcalendar');

let $calendar = $('#calendar');
if ($calendar.length) {

  $calendar.fullCalendar({
    events: $calendar.data('source')
  });
}
