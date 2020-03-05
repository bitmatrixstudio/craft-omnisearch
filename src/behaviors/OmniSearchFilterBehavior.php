<?php

namespace pohnean\omnisearch\behaviors;

use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use pohnean\omnisearch\filters\OmniSearchFilter;
use yii\base\Behavior;

class OmniSearchFilterBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $omnisearchFilters = [];

	public function events()
	{
		return [
			EntryQuery::EVENT_AFTER_PREPARE => [$this, 'afterPrepare'],
		];
	}


	public function afterPrepare(CancelableEvent $event)
	{
		/** @var EntryQuery $entryQuery */
		$entryQuery = $event->sender;

		/** @var OmniSearchFilter[] $filters */
		$filters = array_map(function ($config) use ($entryQuery) {
			return OmniSearchFilter::create(array_merge($config, [
				'customFields' => $entryQuery->customFields,
			]));
		}, $this->omnisearchFilters);

		foreach ($filters as $filter) {
			$filter->modifyQuery($entryQuery->subQuery);
		}
	}

	/**
	 * @param array $omnisearchFilters
	 */
	public function setOmnisearchFilters(array $omnisearchFilters)
	{
		$this->omnisearchFilters = $omnisearchFilters;
	}
}
