<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\commerce\elements\Order;
use craft\elements\User;

class OrderFields extends BaseFields
{
    public function elementType()
    {
        return Order::class;
    }

    public function nativeFields($source)
    {
        return [
            [
                'name'     => Craft::t('commerce', 'Reference'),
                'handle'   => 'order:reference',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
        ];
    }

    /**
     * @param User $element
     * @param $source
     * @return array
     */
    public function customFields(Element $element, $source)
    {
        return $this->getCustomFieldsForElement($element);
    }
}