<?php


namespace pohnean\omnisearch\filters;


use craft\db\Query;

class NotInFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['not in', $this->getColumn(), $this->ensureArray($this->value)]);
	}
}
