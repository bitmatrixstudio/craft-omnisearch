<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\behaviors;

use bitmatrix\omnisearch\fields\DefineFieldColumnMapEvent;
use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\elements\db\ElementQuery;
use craft\events\CancelableEvent;
use bitmatrix\omnisearch\filters\OmniSearchFilter;
use yii\base\Behavior;
use yii\base\InvalidArgumentException;

class OmniSearchFilterBehavior extends Behavior
{
    public $elementType;
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

        $this->customFields = $this->getOmnisearchCustomFields($entryQuery);
        if ($this->customFields != null) {
            $this->customFieldMap = $this->mapCustomFields($this->customFields);
        }
        $this->filters = $this->getOmnisearchFilters($event);

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

            if (OmniSearch::isMatrixField($customField) || OmniSearch::isSuperTableField($customField)) {
                foreach ($customField->getBlockTypeFields() as $blockTypeField) {
                    $key = $customField->handle . '.' . $blockTypeField->handle;
                    $customFieldMap[$key] = $blockTypeField;
                }
            }
        }

        return $customFieldMap;
    }

    private function getOmnisearchCustomFields(ElementQuery $query)
    {
        /** @var Element $class */
        $class = $query->elementType;
        if ($class::hasContent() && $query->contentTable !== null) {
            if (Craft::$app->getUpdates()->getAreMigrationsPending()) {
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
    private function getOmnisearchFilters(CancelableEvent $event): array
    {
        /** @var ElementQuery $entryQuery */
        $entryQuery = $event->sender;
        $structureId = $entryQuery->structureId;

        if ($this->filters === null) {
            $this->filters = array_map(function ($config) use ($structureId) {
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
                    'elementType' => $this->elementType,
                    'customField' => $customField,
                    'parentField' => $parentField,
                    'structureId' => $structureId,
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
