<?php

namespace pohnean\omnisearch\behaviors;

use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use yii\base\Behavior;

class OmniSearchFilterBehavior extends Behavior
{
	/**
	 * @var array
	 */
	public $omnisearchFilters = [];

	public function events()
	{
		return [
			EntryQuery::EVENT_BEFORE_PREPARE => [$this, 'beforePrepare'],
		];
	}

	public function init()
	{
		\Craft::info('OmniSearchFilterBehavior init()', __METHOD__);

		parent::init();
	}


	public function beforePrepare(CancelableEvent $event)
	{
		$filters = $this->omnisearchFilters;

		/** @var EntryQuery $entryQuery */
		$entryQuery = $event->sender;

		$elementClass = $entryQuery->elementType;

		foreach ($filters as $filter) {
			$field = $filter['field'] ?? null;
			$operator = $filter['operator'] ?? null;
			$value = $filter['value'] ?? null;

			// TODO: Refactor to strategy pattern
			switch ($operator) {
				case 'contain':
					$entryQuery->subQuery->andWhere(['like', 'content.title', $value]);
					break;
				case 'not_contain':
					$entryQuery->subQuery->andWhere(['not like', 'content.title', $value]);
					break;
			}
//			codecept_debug($query);
//			codecept_debug($field);
//			codecept_debug($operator);
//			codecept_debug($value);
		}

//		\Craft::info('onAfterPrepare: ' . print_r($this->omnisearchFilters, true), __METHOD__);
	}

	/**
	 * @param array $omnisearchFilters
	 */
	public function setOmnisearchFilters(array $omnisearchFilters)
	{
		$this->omnisearchFilters = $omnisearchFilters;
	}
}
