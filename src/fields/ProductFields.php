<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\commerce\elements\Product;
use craft\commerce\records\ProductType;
use craft\commerce\records\ShippingCategory;
use craft\commerce\records\TaxCategory;

class ProductFields extends BaseFields
{
    public function elementType()
    {
        return Product::class;
    }

    public function builtInFields($source)
    {
        return [
            [
                'name'     => Craft::t('commerce', 'Free Shipping'),
                'handle'   => 'product:freeShipping',
                'dataType' => OmniSearch::DATATYPE_BOOLEAN,
            ],
            [
                'name'     => Craft::t('commerce', 'Promotable'),
                'handle'   => 'product:promotable',
                'dataType' => OmniSearch::DATATYPE_BOOLEAN,
            ],
            [
                'name'     => Craft::t('commerce', 'Available for purchase'),
                'handle'   => 'product:availableForPurchase',
                'dataType' => OmniSearch::DATATYPE_BOOLEAN,
            ],
            [
                'name'     => Craft::t('commerce', 'Type'),
                'handle'   => 'product:typeId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getProductTypesListData()
            ],
            [
                'name'     => Craft::t('commerce', 'Tax Category'),
                'handle'   => 'product:taxCategoryId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getProductTaxCategoriesListData()
            ],
            [
                'name'     => Craft::t('commerce', 'Shipping Category'),
                'handle'   => 'product:shippingCategoryId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getProductShippingCategoriesListData()
            ],
            [
                'name'     => Craft::t('commerce', 'SKU'),
                'handle'   => 'variant:sku',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Stock'),
                'handle'   => 'variant:stock',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Length'),
                'handle'   => 'variant:length',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Width'),
                'handle'   => 'variant:width',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Height'),
                'handle'   => 'variant:height',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Weight'),
                'handle'   => 'variant:weight',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Price'),
                'handle'   => 'variant:price',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
        ];
    }

    /**
     * @param Product $element
     * @param $source
     * @return array
     */
    public function customFields(Element $element, $source)
    {
        $query = ProductType::find();

        $uid = $this->extractUidFromSource($source);
        if ($uid != null) {
            $query->where(['uid' => $uid]);
        }

        $fields = [];

        /** @var ProductType $productTypes */
        $productTypes = $query->each();
        foreach ($productTypes as $productType) {
            $element->typeId = $productType->id;

            $fields = array_merge($fields, $this->getCustomFieldsForElement($element));
        }

        return $fields;
    }

    private function getProductTypesListData()
    {
        return ProductType::find()
            ->select([
                'id AS value',
                'name AS label',
            ])
            ->asArray()
            ->all();
    }

    private function getProductTaxCategoriesListData()
    {
        return TaxCategory::find()
            ->select([
                'id AS value',
                'name AS label',
            ])
            ->asArray()
            ->all();
    }

    private function getProductShippingCategoriesListData()
    {
        return ShippingCategory::find()
            ->select([
                'id AS value',
                'name AS label',
            ])
            ->asArray()
            ->all();
    }

    public static function fieldToColumnMap()
    {
        return [
            'product:freeShipping'         => 'commerce_products.freeShipping',
            'product:promotable'           => 'commerce_products.promotable',
            'product:availableForPurchase' => 'commerce_products.availableForPurchase',
            'product:typeId'               => 'commerce_products.typeId',
            'product:taxCategoryId'        => 'commerce_products.taxCategoryId',
            'product:shippingCategoryId'   => 'commerce_products.shippingCategoryId',
            'variant:sku'                  => 'commerce_variants.sku',
            'variant:stock'                => 'IF(hasUnlimitedStock = 1, 99999999999, commerce_variants.stock)',
            'variant:length'               => 'commerce_variants.length',
            'variant:height'               => 'commerce_variants.height',
            'variant:price'                => 'commerce_variants.price',
            'variant:weight'               => 'commerce_variants.weight',
        ];
    }
}