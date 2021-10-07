<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\behaviors;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\elements\db\ElementQuery;
use craft\events\CancelableEvent;
use craft\fields\Matrix;
use bitmatrix\omnisearch\filters\OmniSearchFilter;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;

class OmniSearchFilterBehavior extends Behavior
{
    /**
     * @var array
     */
    protected $omnisearchFilters = [];

    protected $customFieldMap = [];

    /**
     * @var OmniSearchFilter[]
     */
    private $filters = null;

    /**
     * @var Field
     */
    private $customFields;

    public function events()
    {
        return [
            ElementQuery::EVENT_BEFORE_PREPARE => [$this, 'beforePrepare'],
            ElementQuery::EVENT_AFTER_PREPARE  => [$this, 'afterPrepare'],
        ];
    }

    public function beforePrepare(CancelableEvent $event)
    {
        /** @var ElementQuery $entryQuery */
        $entryQuery = $event->sender;

        $this->customFields = $this->getCustomFields($entryQuery);
        if ($this->customFields != null) {
            $this->customFieldMap = $this->mapCustomFields($this->customFields);
        }
        $this->filters = $this->getFilters();

        foreach ($this->filters as $filter) {
            if ($this->isSiteIdFilter($filter)) {
                $entryQuery->siteId = $filter->value;
            }
        }
    }

    public function afterPrepare(CancelableEvent $event)
    {
        /** @var ElementQuery $entryQuery */
        $entryQuery = $event->sender;

        foreach ($this->filters as $filter) {
            if ($this->isSiteIdFilter($filter)) {
                continue;
            }

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
            $customFieldMap[$customField->handle] = $customField;

            if ($customField instanceof Matrix) {
                foreach ($customField->getBlockTypeFields() as $blockTypeField) {
                    $key = $customField->handle . '.' . $blockTypeField->handle;
                    $customFieldMap[$key] = $blockTypeField;
                }
            }
        }

        return $customFieldMap;
    }

    protected function getCustomFields(ElementQuery $query)
    {
        /** @var Element $class */
        $class = $query->elementType;
        if ($class::hasContent() && $query->contentTable !== null) {
            if (Craft::$app->getUpdates()->getIsCraftDbMigrationNeeded()) {
                return [];
            }

            $contentService = Craft::$app->getContent();
            $originalFieldContext = $contentService->fieldContext;
            $contentService->fieldContext = 'global';
            $fields = Craft::$app->getFields()->getAllFields();
            $contentService->fieldContext = $originalFieldContext;

            return $fields;
        } else {
            return [];
        }
    }

    /**
     * @return OmniSearchFilter[]
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFilters(): array
    {
        if ($this->filters === null) {
            $this->filters = array_map(function ($config) {
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
        }

        return $this->filters;
    }

    protected function isSiteIdFilter(OmniSearchFilter $filter)
    {
        return $filter->field === 'site';
    }
}
