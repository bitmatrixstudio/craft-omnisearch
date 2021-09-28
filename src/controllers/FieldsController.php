<?php
/**
 * OmniSearch plugin for Craft CMS 3.x
 *
 * A powerful control panel search filter replacement for CraftCMS
 *
 * @link      github.com/pohnean
 * @copyright Copyright (c) 2020 Tai Poh Nean
 */

namespace pohnean\omnisearch\controllers;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\fields\BaseOptionsField;
use craft\fields\Date;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\Number;
use craft\web\Controller;
use pohnean\omnisearch\OmniSearch;

/**
 * @author    Tai Poh Nean
 * @package   OmniSearch
 * @since     1.0.0
 */
class FieldsController extends Controller
{
    /**
     * @return mixed
     */
    public function actionIndex($elementType, $source)
    {
        /** @var Element $element */
        $element = new $elementType;

        $entryFields = $this->getEntryFields($element, $source);

        return $this->asJson(array_merge([
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
                'name'     => Craft::t('app', 'Post Date'),
                'handle'   => 'postDate',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
        ], $entryFields));
    }

    protected function getEntryFields(Element $element, string $source)
    {
        $sectionsAndEntryTypes = $this->getSectionsAndEntryTypes($source);

        $fields = [];
        foreach ($sectionsAndEntryTypes as $sectionId => $entryTypes) {
            foreach ($entryTypes as $entryTypeId) {
                $element->sectionId = $sectionId;
                $element->typeId = $entryTypeId;

                $fields = array_merge($fields, $this->getFieldsForElement($element));
            }
        }

        return $fields;
    }

    /**
     * @return array
     */
    protected function getFieldsForElement(Element $element): array
    {
        $fieldLayout = $element->getFieldLayout();
        if (!$element::hasContent() || $fieldLayout === null) {
            return [];
        }

        $fields = [];

        /** @var Field $field */
        foreach ($fieldLayout->getFields() as $field) {
            if ($field->searchable) {
                if ($field instanceof Matrix) {
                    $matrixFields = array_map(function ($item) use ($field) {
                        return $this->createFieldConfig($item, $field->handle . '.');
                    }, $field->getBlockTypeFields());

                    $fieldConfig = [
                        'handle'   => $field->handle,
                        'name'     => $field->name,
                        'dataType' => OmniSearch::DATATYPE_MATRIX,
                        'fields'   => $matrixFields,
                    ];
                } else {
                    $fieldConfig = $this->createFieldConfig($field);
                }

                $fields[] = $fieldConfig;
            }
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
        }

        return $fieldConfig;
    }

    /**
     * @param $source
     * @return array
     */
    protected function getSectionsAndEntryTypes(string $source): array
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
     * @return string
     */
    protected function mapDataType(Field $field): string
    {
        if ($field instanceof Lightswitch) {
            return OmniSearch::DATATYPE_BOOLEAN;
        } elseif ($field instanceof Date) {
            return OmniSearch::DATATYPE_DATE;
        } elseif ($field instanceof BaseOptionsField) {
            return OmniSearch::DATATYPE_LIST;
        } elseif ($field instanceof Number) {
            return OmniSearch::DATATYPE_NUMBER;
        }

        return OmniSearch::DATATYPE_TEXT;
    }
}
