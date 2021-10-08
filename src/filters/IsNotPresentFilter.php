<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use craft\db\Query;

class IsNotPresentFilter extends OmniSearchFilter
{
    public function modifyQuery(Query $query): Query
    {
        return $query->andWhere([$this->getColumn() => null]);
    }

    public function applyRelationQuery(Query $query): Query
    {
        $relationSubQuery = (new Query())
            ->select(['sourceId'])
            ->from('{{%relations}}')
            ->where(['fieldId' => $this->customField->id])
            ->andWhere('sourceId = elements.id');

        return $query->andWhere([
            'not exists',
            $relationSubQuery
        ]);
    }
}
