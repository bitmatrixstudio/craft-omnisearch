<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use Carbon\Carbon;

abstract class BaseDateFilter extends OmniSearchFilter
{
    /**
     * @param $value
     * @return \DateTime|null
     */
    protected function parseDate($value)
    {
        try {
            return new \DateTime($value, new \DateTimeZone(\Craft::$app->timeZone));
        } catch (\Exception $e) {
            return null;
        }
    }
}
