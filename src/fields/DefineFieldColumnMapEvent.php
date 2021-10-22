<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\filters\OmniSearchFilter;
use yii\base\Event;

class DefineFieldColumnMapEvent extends Event
{
    /**
     * @var array The field definitions
     */
    public $fieldToColumnMap = [];

    /**
     * @var OmniSearchFilter the sender of this event. If not set, this property will be
     * set as the object whose `trigger()` method is called.
     * This property may also be a `null` when this event is a
     * class-level event which is triggered in a static context.
     */
    public $sender;
}