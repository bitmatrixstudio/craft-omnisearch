<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use yii\base\Event;

class DefineFieldsEvent extends Event
{
    /**
     * @var array The field definitions
     */
    public $fields = [];

    /**
     * @var string
     */
    public $elementType;

    /**
     * @var string
     */
    public $source;
}