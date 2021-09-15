<?php

namespace pohnean\omnisearch\filters;

use Carbon\Carbon;
use craft\db\Query;

class DateAfterFilter extends BaseDateFilter
{
    public function modifyQuery(Query $query): Query
    {
        $date = $this->parseDate($this->value);
        if ($date != null) {
            $query->andWhere(['>', $this->getColumn(), $date->format('Y-m-d')]);
        }

        return $query;
    }
}
