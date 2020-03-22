<?php

namespace pohnean\omnisearch\behaviors;

use craft\base\Field;
use craft\base\FieldInterface;
use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use pohnean\omnisearch\filters\OmniSearchFilter;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;

class OmniSearchFilterBehavior extends Behavior
{
	/**
	 * @var array
	 */
	protected $omnisearchFilters = [];

	protected $customFieldMap = [];

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
		$this->setCustomFieldMap($entryQuery->customFields);

		/** @var OmniSearchFilter[] $filters */
		$filters = array_map(function ($config) {
			$field = $config['field'] ?? null;

			if ($field == null) {
				throw new InvalidArgumentException('Invalid field: "' . $field . '"');
			}

			$customField = array_key_exists($field, $this->customFieldMap) ? $this->customFieldMap[$field] : null;

			return OmniSearchFilter::create(array_merge($config, [
				'customField' => $customField
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

	/**
	 * @param Field[] $customFields
	 */
	protected function setCustomFieldMap(array $customFields)
	{
		$customFieldMap = [];
		foreach ($customFields as $customField) {
			if ($customField->hasContentColumn()) {
				$customFieldMap[$customField->handle] = $customField;
			}
		}

		$this->customFieldMap = $customFieldMap;
	}
}
