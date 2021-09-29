<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace pohnean\omnisearch\filters;


use craft\db\Query;

class NotInFilter extends OmniSearchFilter
{
	public function modifyQuery(Query $query): Query
	{
		$values = $this->ensureArray($this->value);

		$conditions = [];
		if ($this->isMultiSelect()) {
			$conditions = ['or'];
			$params = [];

			if ($this->dbSupportsJsonContains()) {
				// Use JSON_CONTAINS condition
				foreach ($values as $i => $value) {
					$paramKey = ':param' . $i;
					$conditions[] = sprintf('JSON_CONTAINS(%s, %s)', \Yii::$app->db->quoteColumnName($this->getColumn()), $paramKey);
					$params[$paramKey] = json_encode($value);
				}
			} else {
				// Use LIKE condition
				foreach ($values as $value) {
					$conditions[] = ['like', $this->getColumn(), "\"{$value}\""];
				}
			}

			return $query->andWhere(array_merge(['not'], [$conditions]), $params);
		} else {
			return $query->andWhere(['not in', $this->getColumn(), $values]);
		}
	}
}
