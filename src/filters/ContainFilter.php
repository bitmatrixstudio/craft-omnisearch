<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use yii\db\Query;

class ContainFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		return $query->andWhere([$this->likeOperator(), $this->getColumn(), $this->value]);
	}
}
