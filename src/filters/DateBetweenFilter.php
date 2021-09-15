<?php

namespace pohnean\omnisearch\filters;

use Carbon\Carbon;
use craft\db\Query;

class DateBetweenFilter extends BaseDateFilter
{
    public function modifyQuery(Query $query): Query
    {
        list($start, $end) = $this->parseDateRange($this->value);

        if ($start != null) {
            $query->andWhere(['>=', $this->getColumn(), $start->format('Y-m-d')]);
        }

        if ($end != null) {
            $query->andWhere(['<=', $this->getColumn(), $end->format('Y-m-d')]);
        }

        return $query;
    }

    /**
     * @param $value
     * @return \DateTime[]|null[]
     */
    private function parseDateRange($value)
    {
        $start = null;
        $end = null;

        list($t1, $t2) = explode(',', $value);

        if ($t1 != null) {
            $start = $this->parseDate($t1);
        }

        if ($t2 != null) {
            $end = $this->parseDate($t2);
        }

        return [$start, $end];
    }
}
