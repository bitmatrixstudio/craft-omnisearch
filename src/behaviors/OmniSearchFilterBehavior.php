<?php

namespace pohnean\omnisearch\behaviors;

use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use yii\base\Behavior;

class OmniSearchFilterBehavior extends Behavior
{
	public $filters = [];

	public function events()
	{
		return [
			EntryQuery::EVENT_AFTER_PREPARE => [$this, 'onAfterPrepare'],
		];
	}

	public function init()
	{
		\Craft::info('OmniSearchFilterBehavior init()', __METHOD__);

		parent::init();
	}

	public function onAfterPrepare(CancelableEvent $event)
	{
		\Craft::info('onAfterPrepare: ' . print_r($this->filters, true), __METHOD__);
	}
}
