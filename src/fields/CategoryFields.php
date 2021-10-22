<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\elements\Category;

class CategoryFields extends BaseFields
{
    public function elementType()
    {
        return Category::class;
    }

    public function nativeFields($source)
    {
        return [
            [
                'name'     => Craft::t('app', 'Parent'),
                'handle'   => 'structure:parent',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getCategoriesListData($source),
            ],
            [
                'name'     => Craft::t('omnisearch', 'Ancestor'),
                'handle'   => 'structure:ancestor',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getCategoriesListData($source),
            ],
        ];
    }

    /**
     * @param Category $element
     * @param $source
     * @return array
     */
    public function customFields(Element $element, $source)
    {
        $uid = $this->extractUidFromSource($source);

        $categoryGroup = \Craft::$app->categories->getGroupByUid($uid);
        $element->groupId = $categoryGroup->id;

        return $this->getCustomFieldsForElement($element);
    }

    public static function fieldToColumnMap()
    {
        return [
            'category:parent' => 'assets.filename',
        ];
    }
}