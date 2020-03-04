<?php

namespace pohnean\omnisearch\filters;

use craft\db\Query;

class IsPresentFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['not', [$this->getColumn() => null]]);
	}
}
