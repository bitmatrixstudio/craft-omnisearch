<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\User;

class UserFields extends BaseFields
{
    public function elementType()
    {
        return User::class;
    }

    public function builtInFields($source)
    {
        return [
            [
                'name'     => Craft::t('app', 'Username'),
                'handle'   => 'username',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Email'),
                'handle'   => 'email',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'First Name'),
                'handle'   => 'firstName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Last Name'),
                'handle'   => 'lastName',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Full Name'),
                'handle'   => 'fullName',
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

    public static function fieldToColumnMap()
    {
        return [
            'username'  => 'users.username',
            'email'     => 'users.email',
            'firstName' => 'users.firstName',
            'lastName'  => 'users.lastName',
            'fullName'  => 'CONCAT(users.firstName, " ", users.lastName)',
        ];
    }
}