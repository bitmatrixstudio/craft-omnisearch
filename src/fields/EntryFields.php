<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\elements\Entry;

class EntryFields extends BaseFields
{
    public function elementType()
    {
        return Entry::class;
    }

    public function builtInFields($source)
    {
        $nativeFields = [
            [
                'name'     => Craft::t('app', 'Post Date'),
                'handle'   => 'postDate',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('app', 'Author'),
                'handle'   => 'authorId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getUsersListData($source),
            ],
            [
                'name'     => Craft::t('app', 'Entry Type'),
                'handle'   => 'typeId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getEntryTypesListData($source),
            ],
        ];

        if (strpos($source, ':') !== false) {
            $section = $this->getSectionBySource($source);

            if ($section != null && $section->structureId != null) {
                $nativeFields[] = [
                    'name'     => Craft::t('app', 'Parent'),
                    'handle'   => 'structure:parent',
                    'dataType' => OmniSearch::DATATYPE_LIST,
                    'items'    => $this->getEntriesListData([$source]),
                ];

                $nativeFields[] = [
                    'name'     => Craft::t('app', 'Ancestor'),
                    'handle'   => 'structure:ancestor',
                    'dataType' => OmniSearch::DATATYPE_LIST,
                    'items'    => $this->getEntriesListData([$source]),
                ];
            }
        }

        return $nativeFields;
    }

    /**
     * @param Entry $element
     * @param $source
     * @return array
     */
    public function customFields(Element $element, $source)
    {
        $fields = [];

        $sectionsAndEntryTypes = $this->getSectionsAndEntryTypes($source);

        foreach ($sectionsAndEntryTypes as $sectionId => $entryTypes) {
            foreach ($entryTypes as $entryTypeId) {
                $element->sectionId = $sectionId;
                $element->typeId = $entryTypeId;

                $fields = array_merge($fields, $this->getCustomFieldsForElement($element));
            }
        }

        return $fields;
    }

    public static function fieldToColumnMap()
    {
        return [
            'postDate' => 'entries.postDate',
            'authorId' => 'entries.authorId',
            'typeId'   => 'entries.typeId',
        ];
    }
}