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
use craft\commerce\Plugin;
use craft\elements\User;

class OrderFields extends BaseFields
{
    public function elementType()
    {
        return Order::class;
    }

    public function builtInFields($source)
    {
        return [
            [
                'name'     => Craft::t('commerce', 'Reference'),
                'handle'   => 'order:reference',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Number'),
                'handle'   => 'order:number',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Status'),
                'handle'   => 'order:status',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getStatusListData(),
            ],
            [
                'name'     => Craft::t('commerce', 'Date Ordered'),
                'handle'   => 'order:dateOrdered',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('commerce', 'Date Paid'),
                'handle'   => 'order:datePaid',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('commerce', 'Date Authorized'),
                'handle'   => 'order:dateAuthorized',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('commerce', 'Total'),
                'handle'   => 'order:total',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Total Price'),
                'handle'   => 'order:totalPrice',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Total Paid'),
                'handle'   => 'order:totalPaid',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Total Discount'),
                'handle'   => 'order:totalDiscount',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Total Tax'),
                'handle'   => 'order:totalTax',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Total Included Tax'),
                'handle'   => 'order:totalTaxIncluded',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Total Shipping'),
                'handle'   => 'order:totalShippingCost',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('commerce', 'Paid Status'),
                'handle'   => 'order:paidStatus',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getPaidStatusListData(),
            ],
            [
                'name'     => Craft::t('commerce', 'Email'),
                'handle'   => 'order:email',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Shipping Method'),
                'handle'   => 'order:shippingMethodHandle',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getShippingMethodListData(),
            ],
            [
                'name'     => Craft::t('commerce', 'Gateway'),
                'handle'   => 'order:gatewayId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getGatewayListData(),
            ],
            [
                'name'     => Craft::t('commerce', 'Coupon Code'),
                'handle'   => 'order:couponCode',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Order Site'),
                'handle'   => 'order:orderSiteId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getOrderSiteListData(),
            ],
        ];
    }

    public function extraBuiltInFields($source)
    {
        return [
            [
                'name'     => Craft::t('commerce', 'Billing Address'),
                'handle'   => 'billingAddress',
                'dataType' => OmniSearch::DATATYPE_MATRIX,
                'fields'   => $this->getAddressFields('billingAddress'),
            ],
            [
                'name'     => Craft::t('commerce', 'Shipping Address'),
                'handle'   => 'shippingAddress',
                'dataType' => OmniSearch::DATATYPE_MATRIX,
                'fields'   => $this->getAddressFields('shippingAddress'),
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

    public static function fieldToColumnMap()
    {
        return array_merge(
            [
                'order:reference'            => 'commerce_orders.reference',
                'order:number'               => 'commerce_orders.number',
                'order:status'               => 'commerce_orders.orderStatusId',
                'order:dateOrdered'          => 'commerce_orders.dateOrdered',
                'order:datePaid'             => 'commerce_orders.datePaid',
                'order:dateAuthorized'       => 'commerce_orders.dateAuthorized',
                'order:total'                => 'commerce_orders.total',
                'order:totalPrice'           => 'commerce_orders.totalPrice',
                'order:totalPaid'            => 'commerce_orders.totalPaid',
                'order:totalDiscount'        => 'commerce_orders.totalDiscount',
                'order:totalTax'             => 'commerce_orders.totalTax',
                'order:totalTaxIncluded'     => 'commerce_orders.totalTaxIncluded',
                'order:totalShippingCost'    => 'commerce_orders.totalShippingCost',
                'order:paidStatus'           => 'commerce_orders.paidStatus',
                'order:email'                => 'commerce_orders.email',
                'order:shippingMethodHandle' => 'commerce_orders.shippingMethodHandle',
                'order:gatewayId'            => 'commerce_orders.gatewayId',
                'order:couponCode'           => 'commerce_orders.couponCode',
                'order:orderSiteId'          => 'commerce_orders.orderSiteId',
            ],
            static::getAddressColumns('billingAddress', 'billing_address'),
            static::getAddressColumns('shippingAddress', 'shipping_address')
        );
    }

    private function getStatusListData()
    {
        return self::mapListData(
            Plugin::getInstance()->orderStatuses->getAllOrderStatuses(),
            'id',
            'name'
        );
    }

    private function getPaidStatusListData()
    {
        return [
            [
                'value' => Order::PAID_STATUS_UNPAID,
                'label' => Craft::t('commerce', 'Unpaid'),
            ],
            [
                'value' => Order::PAID_STATUS_PARTIAL,
                'label' => Craft::t('commerce', 'Partial'),
            ],
            [
                'value' => Order::PAID_STATUS_PAID,
                'label' => Craft::t('commerce', 'Paid'),
            ],
            [
                'value' => Order::PAID_STATUS_OVERPAID,
                'label' => Craft::t('commerce', 'Overpaid'),
            ],
        ];
    }

    private function getGatewayListData()
    {
        return self::mapListData(
            Plugin::getInstance()->gateways->getAllGateways(),
            'id',
            'name'
        );
    }

    private function getShippingMethodListData()
    {
        return self::mapListData(
            Plugin::getInstance()->shippingMethods->getAllShippingMethods(),
            'handle',
            'name'
        );
    }

    private function getCountryListData()
    {
        return self::mapListData(
            Plugin::getInstance()->countries->getAllCountries(),
            'id',
            'name'
        );
    }

    private function getOrderSiteListData()
    {
        return self::mapListData(
            Craft::$app->sites->getAllSites(),
            'id',
            'name'
        );
    }

    /**
     * @return array[]
     */
    protected function getAddressFields($prefix)
    {
        return [
            [
                'name'     => $prefix === 'billingAddress' ? Craft::t('commerce', 'Billing Full Name') : Craft::t('commerce', 'Shipping Full Name'),
                'handle'   => $prefix . ':fullName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => $prefix === 'billingAddress' ? Craft::t('commerce', 'Billing First Name') : Craft::t('commerce', 'Shipping First Name'),
                'handle'   => $prefix . ':firstName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => $prefix === 'billingAddress' ? Craft::t('commerce', 'Billing Last Name') : Craft::t('commerce', 'Shipping Last Name'),
                'handle'   => $prefix . ':lastName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Title'),
                'handle'   => $prefix . ':title',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Attention'),
                'handle'   => $prefix . ':attention',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Address Line 1'),
                'handle'   => $prefix . ':address1',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Address Line 2'),
                'handle'   => $prefix . ':address2',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Address Line 3'),
                'handle'   => $prefix . ':address3',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'City'),
                'handle'   => $prefix . ':city',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Zip Code'),
                'handle'   => $prefix . ':zipCode',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'State'),
                'handle'   => $prefix . ':stateName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Country'),
                'handle'   => $prefix . ':countryId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getCountryListData(),
            ],
            [
                'name'     => Craft::t('commerce', 'Phone'),
                'handle'   => $prefix . ':phone',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Alternative Phone'),
                'handle'   => $prefix . ':alternativePhone',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Business Name'),
                'handle'   => $prefix . ':businessName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Business ID'),
                'handle'   => $prefix . ':businessId',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Business Tax ID'),
                'handle'   => $prefix . ':businessTaxId',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Notes'),
                'handle'   => $prefix . ':notes',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Label'),
                'handle'   => $prefix . ':label',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Custom 1'),
                'handle'   => $prefix . ':custom1',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Custom 2'),
                'handle'   => $prefix . ':custom2',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Custom 3'),
                'handle'   => $prefix . ':custom3',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('commerce', 'Custom 4'),
                'handle'   => $prefix . ':custom4',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
        ];
    }

    /**
     * @return array[]
     */
    protected static function getAddressColumns($prefix, $tableAlias)
    {
        $attributes = [
            'title',
            'attention',
            'fullName',
            'firstName',
            'lastName',
            'address1',
            'address2',
            'address3',
            'city',
            'stateName',
            'countryId',
            'phone',
            'alternativePhone',
            'businessName',
            'businessId',
            'businessTaxId',
            'notes',
            'label',
            'custom1',
            'custom2',
            'custom3',
            'custom4',
        ];

        $fields = [];
        foreach ($attributes as $attribute) {
            $fields[$prefix . ':' . $attribute] = $tableAlias . '.' . $attribute;
        }

        return $fields;
    }
}