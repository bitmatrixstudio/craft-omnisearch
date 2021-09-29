<?php

namespace pohnean\omnisearch\behaviors;

use craft\base\Field;
use craft\base\FieldInterface;
use craft\elements\db\ElementQuery;
use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use craft\fields\Matrix;
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
			ElementQuery::EVENT_AFTER_PREPARE => [$this, 'afterPrepare'],
		];
	}


	public function afterPrepare(CancelableEvent $event)
	{
		/** @var ElementQuery $entryQuery */
		$entryQuery = $event->sender;

		if ($entryQuery->customFields != null) {
            $this->customFieldMap = $this->mapCustomFields($entryQuery->customFields);
        }

		/** @var OmniSearchFilter[] $filters */
		$filters = array_map(function ($config) {
			$field = $config['field'] ?? null;

			if ($field == null) {
				throw new InvalidArgumentException('Invalid field: "' . $field . '"');
			}

			$customField = array_key_exists($field, $this->customFieldMap) ? $this->customFieldMap[$field] : null;

            $parentField = null;
            if (strpos($field, '.') > -1) {
                $parentFieldId = explode('.', $field)[0];
                $parentField = array_key_exists($parentFieldId, $this->customFieldMap) ? $this->customFieldMap[$parentFieldId] : null;
            }

			return OmniSearchFilter::create(array_merge($config, [
				'customField' => $customField,
				'parentField' => $parentField,
			]));
		}, $this->omnisearchFilters);

		foreach ($filters as $filter) {
			$filter->modifyElementQuery($entryQuery->subQuery);
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
	protected function mapCustomFields(array $customFields)
	{
		$customFieldMap = [];
		foreach ($customFields as $customField) {
		    if ($customField instanceof Matrix) {
                $customFieldMap[$customField->handle] = $customField;

                foreach ($customField->getBlockTypeFields() as $blockTypeField) {
                    $key = $customField->handle . '.' . $blockTypeField->handle;
                    $customFieldMap[$key] = $blockTypeField;
                }
            }

			if ($customField->hasContentColumn()) {
				$customFieldMap[$customField->handle] = $customField;
			}
		}

		return $customFieldMap;
	}
}
