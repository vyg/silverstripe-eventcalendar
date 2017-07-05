<?php

class EventCalendarPage extends Page {
	private static $allowed_children = [
		'Event'
	];

	private static $hide_ancestor = 'EventHolder';

	private static $event_class = 'Event';

	protected $eventClass_cache,
			  $announcementClass_cache,
			  $datetimeClass_cache,
			  $dateToEventRelation_cache,
			  $announcementToCalendarRelation_cache,
			  $EventList_cache;

	public function getEventClass() {
		if($this->eventClass_cache) return $this->eventClass_cache;
		$this->eventClass_cache = $this->stat('event_class');
		return $this->eventClass_cache;
	}
	public function getDateTimeClass() {
		if($this->datetimeClass_cache) return $this->datetimeClass_cache;
		$this->datetimeClass_cache = singleton($this->getEventClass())->stat('datetime_class');
		return $this->datetimeClass_cache;
	}

	public function getDateToEventRelation() {
		if($this->dateToEventRelation_cache) return $this->dateToEventRelation_cache;
		$this->dateToEventRelation_cache = singleton($this->getDateTimeClass())->getReverseAssociation($this->getEventClass())."ID";
		return $this->dateToEventRelation_cache;
	}

	public function getEventList($start, $end, $filter = null) {
		$children = $this->AllChildren();
		$ids = $children->column('ID');
		$datetimeClass = $this->getDateTimeClass();
		$relation = $this->getDateToEventRelation();
		$eventClass = $this->getEventClass();



		$list = DataList::create($datetimeClass)
			->filter(array(
				$relation => $ids
			))
			->innerJoin($eventClass, "$relation = \"{$eventClass}\".\"ID\"")
			->innerJoin("SiteTree", "\"SiteTree\".\"ID\" = \"{$eventClass}\".\"ID\"")
			->where("Recurring != 1");

		if($start && $end) {
			$list = $list->where("
					(Start <= '$start' AND End >= '$end') OR
					(Start BETWEEN '$start' AND '$end') OR
					(End BETWEEN '$start' AND '$end')
					");
		}

		else if($start) {
			$list = $list->where("(Start >= '$start' OR End > '$start')");
		}

		else if($end) {
			$list = $list->where("(End <= '$end' OR Start < '$end')");
		}

		if($filter) {
			$list = $list->where($filter);
		}

		$this->extend('updateEventList', $list);

		return $list;
	}
}

class EventCalendarPage_Controller extends Page_Controller {

	private static $allowed_actions = [
		'eventdata'
	];

	public function init() {
		parent::init();
		Requirements::javascript('eventcalendar/dist/js/eventcalendar.js');
		Requirements::css('eventcalendar/dist/css/eventcalendar.css');
	}

	public function eventdata($request) {
		$start = $request->getVar('start');
		$end = $request->getVar('end');

		$events = $this->data()->getEventList(
			$start,
			$end
		);

		$eventsArray = [];

		if ($events) foreach ($events as $event) {
			$eventArray = [
				'id' => $event->ID,
				'title' => $event->getTitle(),
				'start' => $event->getFormattedStart(),
				'end' => $event->getFormattedEnd(),
				'startTime' => $event->getFormattedStartTime(),
				'endTime' => $event->getFormattedEndTime(),
				'allDay' => (bool) $event->AllDay,
				'url' => $event->Link(),
			];

			$eventsArray[] = $eventArray;
		}

		$response = new SS_HTTPResponse(Convert::array2json($eventsArray));
		$response->addHeader('Content-Type', 'application/json');

		return $response;
	}
}
