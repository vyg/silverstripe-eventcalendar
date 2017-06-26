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

	public function getEventList() {
		$children = $this->AllChildren();
		$ids = $children->column('ID');
		$datetimeClass = $this->getDateTimeClass();
		$relation = $this->getDateToEventRelation();
		$eventClass = $this->getEventClass();

		$list = DataList::create($datetimeClass)
			->innerJoin($eventClass, "$relation = \"{$eventClass}\".\"ID\"")
			->innerJoin("SiteTree", "\"SiteTree\".\"ID\" = \"{$eventClass}\".\"ID\"");

		return $list;
	}
}

class EventCalendarPage_Controller extends Page_Controller {


	private static $allowed_actions = [
		'eventdata'
	];

	public function init() {
		parent::init();
		Requirements::css('eventcalendar/dist/css/main.css');
	}

	public function eventdata($request) {
		$start = $request->getVar('start');
		$end = $request->getVar('end');

		$events = $this->data()->getEventList();

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

	public function Events() {
		$list = $this->data()->getEventList();
		return $list;
	}
}
