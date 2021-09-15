<?php

namespace pohnean\omnisearch\filters;

use Carbon\Carbon;
use craft\db\Query;

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
