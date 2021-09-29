<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use craft\db\Query;

class ContainFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere(['like', $this->getColumn(), $this->value]);
	}
}
