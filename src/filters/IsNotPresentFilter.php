<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use craft\elements\db\ElementQuery;
use yii\db\Query;

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

    public function applyMatrixQuery(ElementQuery $blockQuery, Query $query): void
    {
        $isPresentFilter = new IsPresentFilter($this->getConfig());
        if ($this->isRelationField()) {
            $blockQuery = $isPresentFilter->applyRelationQuery($blockQuery);
        } else {
            $isPresentFilter->modifyQuery($blockQuery);
        }

        $query->andWhere([
            'not in',
            'elements.id',
            $blockQuery->column()
        ]);
    }
}
