<?php

namespace pohnean\omnisearch\filters;

use craft\db\Query;

class InFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['in', $this->getColumn(), $this->ensureArray($this->value)]);
	}
}
