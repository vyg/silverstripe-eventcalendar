<?php

class Event extends Page {

	private static $db = [
		'Recurring' => 'Boolean(0)'
	];

	private static $has_many = [
		'EventDateTimes' => 'EventDateTime'
	];

	private static $description = "An individual event entry";

	private static $can_be_root = false;

	private static $datetime_class = "EventDateTime";

	public function getCMSFields() {
		$fields = parent::getCMSFields();

		$fields->addFieldToTab("Root.EventTimes",
			GridField::create(
				"DateTimes",
				'Add dates for this event',
				$this->EventDateTimes(),
				GridFieldConfig_RecordEditor::create()
			)
		);

		$fields->addFieldsToTab('Root.Recurring', [
			CheckboxField::create('Recurring', 'Is this event recurring?')
		]);

		return $fields;
	}
}

class Event_Controller extends Page_Controller {

}
