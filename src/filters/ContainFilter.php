<?php

namespace pohnean\omnisearch\filters;

use craft\db\Query;

class ContainFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['like', $this->getColumn(), $this->value]);
	}
}
