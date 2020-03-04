<?php

namespace pohnean\omnisearch\filters;

use craft\db\Query;

class GreaterThanFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['>', $this->getColumn(), $this->value]);
	}
}
