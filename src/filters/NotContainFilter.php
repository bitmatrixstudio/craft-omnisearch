<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace pohnean\omnisearch\filters;

use craft\db\Query;

class NotContainFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['not like', $this->getColumn(), $this->value]);
	}
}
