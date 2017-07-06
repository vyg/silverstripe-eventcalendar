<?php

class EventDateTime extends DataObject {
	private static $db = [
		'Start' => 'SS_Datetime',
		'End' => 'SS_Datetime',
		'AllDay' => 'Boolean(0)'
	];

	private static $has_one = [
		'Event' => 'Event'
	];

	private static $summary_fields = [
		'FormattedStartDateTime' => 'Start Time',
		'FormattedEndDateTime' => 'End Time'
	];

	public function getCMSFields() {
		$fields = parent::getCMSFields();
		$fields->removeByName('EventID');

		$fields->addFieldsToTab('Root.Main', [
			$start = DatetimeField::create('Start', 'Start time'),
			$end = DatetimeField::create('End', 'End time'),
			CheckboxField::create('AllDay', 'Is this event an all day event?')
		]);

		$start->dateField->setConfig('showcalendar', true)->setRightTitle('e.g. 2017-06-22');
		$end->dateField->setConfig('showcalendar', true)->setRightTitle('e.g. 2017-06-22');

		$start->timeField->setRightTitle('e.g. 11:04');
		$end->timeField->setRightTitle('e.g. 11:04');

		return $fields;
	}

	public function Link() {
		return $this->Event()->Link();
	}

	public function getTitle() {
		return $this->Event()->Title;
	}

	public function getFormattedStartDateTime() {
		if(!$this->Start) return "--";
		return $this->obj('Start')->Format('d M Y g:ia');
	}

	public function getFormattedEndDateTime() {
		if(!$this->End) return "--";
		return $this->obj('End')->Format('d M Y g:ia');
	}

	public function getFormattedStart() {
		if(!$this->Start) return "--";
		return $this->obj('Start')->Format('Y-m-d H:i:s');
	}

	public function getFormattedEnd() {
		if(!$this->End) return "--";
		return $this->obj('End')->Format('Y-m-d H:i:s');
	}

	public function getFormattedStartTime() {
		if(!$this->Start) return "--";
		return $this->obj('Start')->Time24();
	}

	public function getFormattedEndTime() {
		if(!$this->End) return "--";
		return $this->obj('End')->Time24();
	}
}
