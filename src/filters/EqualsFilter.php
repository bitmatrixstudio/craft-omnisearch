<?php

namespace pohnean\omnisearch\filters;

use craft\db\Query;

class EqualsFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere([$this->getColumn() => $this->value]);
	}
}
