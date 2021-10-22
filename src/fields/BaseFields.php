<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\controllers\FieldsController;
use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\Tag;
use craft\elements\User;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Categories;
use craft\fields\Date;
use craft\fields\Entries;
use craft\fields\Lightswitch;
use craft\fields\Number;
use craft\fields\Tags;
use craft\fields\Users;
use craft\helpers\Assets;
use craft\models\EntryType;
use craft\models\Section;
use yii\base\Event;

abstract class BaseFields
{
    public function commonFields()
    {
        return [
            [
                'name'     => Craft::t('app', 'ID'),
                'handle'   => 'id',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Title'),
                'handle'   => 'title',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Slug'),
                'handle'   => 'slug',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Date Created'),
                'handle'   => 'dateCreated',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('app', 'Date Updated'),
                'handle'   => 'dateUpdated',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('app', 'Enabled'),
                'handle'   => 'enabled',
                'dataType' => OmniSearch::DATATYPE_BOOLEAN,
            ],
        ];
    }

    abstract public function nativeFields($source);

    abstract public function customFields(Element $element, $source);

    abstract public function elementType();

    public static function fieldToColumnMap()
    {
        return [];
    }

    /**
     * @return Element
     */
    public function createElement()
    {
        $elementType = static::elementType();
        return new $elementType();
    }

    public function getFields($source)
    {
        $elementType = static::elementType();
        $element = $this->createElement();

        $builtInFields = [
            [
                'name'     => $elementType::displayName(),
                'handle'   => 'craft',
                'dataType' => OmniSearch::DATATYPE_MATRIX,
                'fields'   => array_merge($this->commonFields(), $this->nativeFields($source)),
            ]
        ];

        $customFields = $this->customFields($element, $source);

        return array_merge($builtInFields, $customFields);
    }

    /**
     * @return array
     */
    protected function getCustomFieldsForElement(Element $element)
    {
        $fieldLayout = $element->getFieldLayout();
        if (!$element::hasContent() || $fieldLayout === null) {
            return [];
        }

        $fields = [];

        /** @var Field $field */
        foreach ($fieldLayout->getFields() as $field) {
            if (!$field->searchable) {
                continue;
            }

            if (OmniSearch::isMatrixField($field) || OmniSearch::isSuperTableField($field)) {
                $blockTypeFields = $field->getBlockTypeFields();
                $matrixFields = [];
                foreach ($blockTypeFields as $blockTypeField) {
                    if (OmniSearch::isMatrixField($blockTypeField) || OmniSearch::isSuperTableField($blockTypeField)) {
                        // nested matrix not supported yet
                        continue;
                    }

                    if (!$blockTypeField->searchable) {
                        continue;
                    }

                    $matrixFields[] = $this->createFieldConfig($blockTypeField, $field->handle . '.');
                }

                $fieldConfig = [
                    'handle'   => $field->handle,
                    'name'     => $field->name,
                    'dataType' => OmniSearch::DATATYPE_MATRIX,
                    'fields'   => $matrixFields,
                ];
            } else {
                $fieldConfig = $this->createFieldConfig($field, '');
            }

            $fields[] = $fieldConfig;
        }

        return $fields;
    }

    protected function createFieldConfig($field, $prefix = '')
    {
        $fieldConfig = [
            'handle'   => $prefix . $field->handle,
            'name'     => $field->name,
            'dataType' => $this->mapDataType($field),
        ];

        if ($field instanceof BaseOptionsField) {
            $fieldConfig['items'] = array_map(function ($item) {
                unset($item['default']);
                return $item;
            }, $field->options);
        } elseif ($field instanceof BaseRelationField) {
            $fieldConfig['items'] = $this->getRelationListItems($field);
        }

        return $fieldConfig;
    }

    /**
     * @return string
     */
    protected function mapDataType(Field $field): string
    {
        if ($field instanceof Lightswitch) {
            return OmniSearch::DATATYPE_BOOLEAN;
        } elseif ($field instanceof Date) {
            return OmniSearch::DATATYPE_DATE;
        } elseif ($field instanceof BaseOptionsField || $field instanceof BaseRelationField) {
            return OmniSearch::DATATYPE_LIST;
        } elseif ($field instanceof Number) {
            return OmniSearch::DATATYPE_NUMBER;
        }

        return OmniSearch::DATATYPE_TEXT;
    }

    /**
     * @param $sources
     * @return array|Section[]
     */
    protected function getSections($sources)
    {
        $sections = [];
        if ($sources === '*') {
            $sections = Craft::$app->sections->getAllSections();
        } elseif (is_array($sources) && count($sources) > 0) {
            /** @var Section[] $sections */
            $sections = array_filter(array_map(function ($source) {
                return $this->getSectionBySource($source);
            }, $sources));
        }

        return $sections;
    }

    /**
     * @param $source
     * @return Section|null
     */
    protected function getSectionBySource($source)
    {
        $uid = $this->extractUidFromSource($source);
        return Craft::$app->sections->getSectionByUid($uid);
    }

    /**
     * @param string $source
     * @return null|string
     */
    protected function extractUidFromSource(string $source)
    {
        if (strpos($source, ':') === false) {
            return null;
        }

        [, $uid] = explode(':', $source);
        return $uid;
    }

    /**
     * @param string $source
     * @return array
     */
    protected function getSectionsAndEntryTypes(string $source)
    {
        $sectionsAndEntryTypes = [];

        if ($source === '*') {
            foreach (Craft::$app->getSections()->getAllSections() as $section) {
                foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
                    $sectionsAndEntryTypes[$section->id][] = $entryType->id;
                }
            }
        } elseif (strpos($source, 'section:') > -1) {
            $sectionUid = str_replace('section:', '', $source);
            $section = Craft::$app->getSections()->getSectionByUid($sectionUid);
            foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
                $sectionsAndEntryTypes[$section->id][] = $entryType->id;
            }
        }

        return $sectionsAndEntryTypes;
    }

    /**
     * @return array|\craft\base\ElementInterface[]|User[]|null
     */
    protected function getUsersListData($sources = [])
    {
        $query = User::find()
            ->select([
                'users.id AS value',
                '(IF(LENGTH(TRIM(CONCAT(users.firstName, " ", users.lastName))), TRIM(CONCAT(users.firstName, " ", users.lastName)), username)) AS label',
            ]);

        if (is_array($sources) && count($sources) > 0) {
            $groupIds = array_filter(array_map(function ($source) {
                $uid = $this->extractUidFromSource($source);
                $group = Craft::$app->userGroups->getGroupByUid($uid);
                return $group != null ? $group->id : null;
            }, $sources));

            $query->groupId($groupIds);
        }

        return $query
            ->asArray()
            ->all();
    }

    protected function getEntryTypesListData($source)
    {
        $sectionsAndEntryTypes = $this->getSectionsAndEntryTypes($source);
        $sectionIds = array_keys($sectionsAndEntryTypes);

        $entryTypes = [];
        foreach ($sectionIds as $sectionId) {
            $section = Craft::$app->sections->getSectionById($sectionId);
            $entryTypes = array_merge($entryTypes, Craft::$app->sections->getEntryTypesBySectionId($section->id));
        }

        return array_map(function (EntryType $entryType) {
            return [
                'value' => $entryType->id,
                'label' => $entryType->name,
            ];
        }, $entryTypes);
    }

    /**
     * @return array
     */
    protected function getFileKindsListData()
    {
        $fileKinds = Assets::getFileKinds();

        $listData = [];
        foreach ($fileKinds as $id => $fileKind) {
            $listData[] = [
                'value' => $id,
                'label' => $fileKind['label'],
            ];
        }

        return $listData;
    }

    protected function getRelationListItems(BaseRelationField $field)
    {
        $items = [];

        if ($field instanceof Tags) {
            $items = $this->getTagsListData($field);
        } elseif ($field instanceof Users) {
            $items = $this->getUsersListData($field->sources);
        } elseif ($field instanceof Entries) {
            $items = $this->getEntriesListData($field->sources);
        } elseif ($field instanceof \craft\fields\Assets) {
            $items = $this->getAssetsListData($field->sources);
        } elseif ($field instanceof Categories) {
            $items = $this->getCategoriesListData($field->source);
        }

        return $items;
    }

    protected function getTagsListData(Tags $field)
    {
        $uid = $this->extractUidFromSource($field->source);
        $tagGroup = Craft::$app->tags->getTagGroupByUid($uid);

        return Tag::find()
            ->select([
                'elements.id AS value',
                'title AS label',
            ])
            ->anyStatus()
            ->groupId($tagGroup->id)
            ->asArray()
            ->all();
    }

    protected function getEntriesListData($sources = [])
    {
        $sections = $this->getSections($sources);

        $sectionIds = array_map(function (Section $section) {
            return $section->id;
        }, $sections);

        return Entry::find()
            ->select([
                'elements.id AS value',
                'title AS label',
                'COALESCE(level, 1) AS level',
            ])
            ->sectionId($sectionIds)
            ->asArray()
            ->all();
    }

    protected function getAssetsListData($sources = [])
    {
        $volumeIds = [];
        if ($sources === '*') {
            $volumeIds = Craft::$app->volumes->getAllVolumeIds();
        } elseif (is_array($sources) && count($sources) > 0) {
            /** @var int[] $volumeIds */
            $volumeIds = array_map(function ($source) {
                $uid = $this->extractUidFromSource($source);
                return Craft::$app->volumes->getVolumeByUid($uid)->id;
            }, $sources);
        }

        return Asset::find()
            ->select([
                'elements.id AS value',
                'title AS label',
            ])
            ->volumeId($volumeIds)
            ->asArray()
            ->all();
    }

    protected function getCategoriesListData($source)
    {
        $uid = $this->extractUidFromSource($source);
        $catGroup = Craft::$app->categories->getGroupByUid($uid);

        return Category::find()
            ->select([
                'elements.id AS value',
                'title AS label',
                'level',
            ])
            ->group($catGroup)
            ->asArray()
            ->all();
    }
}