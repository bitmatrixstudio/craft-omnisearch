<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use bitmatrix\omnisearch\fields\DefineFieldColumnMapEvent;
use bitmatrix\omnisearch\OmniSearch;
use craft\base\Field;
use craft\base\FieldInterface;
use craft\commerce\elements\Variant;
use craft\elements\db\ElementQuery;
use yii\db\Query;
use craft\elements\MatrixBlock;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Lightswitch;
use craft\helpers\ArrayHelper;
use craft\records\StructureElement;
use verbb\supertable\elements\SuperTableBlockElement;
use yii\base\Component;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\Exception;

abstract class OmniSearchFilter extends Component
{
    const EVENT_DEFINE_FIELD_COLUMN_MAP = 'defineFieldColumnMap';

    /**
     * @var string
     */
    public $elementType;

    /**
     * @var string
     */
    public $field;

    /**
     * @var int|null
     */
    public $structureId;

    /**
     * @var int|string|array
     */
    public $value;

    /**
     * @var Field
     */
    public $customField;

    /**
     * @var Field|null
     */
    public $parentField;

    public static $filterClassMap = [
        'contain'        => ContainFilter::class,
        'not_contain'    => NotContainFilter::class,
        'equals'         => EqualsFilter::class,
        'not_equals'     => NotEqualsFilter::class,
        'is_present'     => IsPresentFilter::class,
        'is_not_present' => IsNotPresentFilter::class,
        'gt'             => GreaterThanFilter::class,
        'gte'            => GreaterThanOrEqualFilter::class,
        'lt'             => LessThanFilter::class,
        'lte'            => LessThanOrEqualFilter::class,
        'in'             => InFilter::class,
        'not_in'         => NotInFilter::class,
        'starts_with'    => StartsWithFilter::class,
        'date_between'   => DateBetweenFilter::class,
        'date_before'    => DateBeforeFilter::class,
        'date_after'     => DateAfterFilter::class,
    ];

    public $fieldToColumnMap = [];

    protected static $hasJsonSupport;

    abstract public function modifyQuery(Query $query): Query;

    public function modifyElementQuery(Query $query)
    {
        if ($this->parentField != null && (OmniSearch::isMatrixField($this->parentField) || OmniSearch::isSuperTableField($this->parentField))) {
            if (OmniSearch::isSuperTableField($this->parentField)) {
                $blockQuery = SuperTableBlockElement::find()
                    ->select('supertableblocks.ownerId')
                    ->fieldId($this->parentField->id);
            } else {
                $blockQuery = MatrixBlock::find()
                    ->select('matrixblocks.ownerId')
                    ->fieldId($this->parentField->id);
            }

            $this->applyMatrixQuery($blockQuery, $query);
        } elseif ($this->isProductVariantField()) {
            $productVariantSubQuery = Variant::find()->select('commerce_variants.productId');
            $this->modifyQuery($productVariantSubQuery);

            $query->andWhere([
                'in',
                'elements.id',
                $productVariantSubQuery->column(),
            ]);
        } elseif ($this->isStructureAncestorField()) {
            $this->applyAncestorQuery($query);
        } elseif ($this->isStructureParentField()) {
            $this->applyParentQuery($query);
        } elseif ($this->isRelationField()) {
            $this->applyRelationQuery($query);
        } else {
            $this->modifyQuery($query);
        }
    }

    /**
     * @param $blockQuery
     * @param Query $query
     */
    public function applyMatrixQuery(ElementQuery $blockQuery, Query $query): void
    {
        if ($this->isRelationField()) {
            $blockQuery = $this->applyRelationQuery($blockQuery);
        } else {
            $this->modifyQuery($blockQuery);
        }

        $query->andWhere([
            'in',
            'elements.id',
            $blockQuery->column(),
        ]);
    }

    public function applyRelationQuery(Query $query): Query
    {
        $relationSubQuery = (new Query())
            ->select(['sourceId'])
            ->from('{{%relations}}')
            ->where(['fieldId' => $this->customField->id]);

        $this->modifyQuery($relationSubQuery);

        return $query->andWhere([
            'in',
            'elements.id',
            $relationSubQuery->column()
        ]);
    }

    private function applyParentQuery(Query $query)
    {
        return $this->applyStructureQuery($query, 1);
    }

    private function applyAncestorQuery(Query $query)
    {
        return $this->applyStructureQuery($query, -1);
    }

    private function applyStructureQuery(Query $query, $dist = -1)
    {
        $subQuery = StructureElement::find();

        $onCondition = 'children.lft >= parent.lft AND children.rgt <= parent.rgt';
        if ($dist > 0) {
            $onCondition .= ' AND ABS(children.level - parent.level) <= :dist';
            $subQuery->addParams([':dist' => $dist]);
        }

        $subQuery
            ->select(['children.elementId'])
            ->alias('parent')
            ->innerJoin('{{%structureelements}} children', $onCondition)
            ->andWhere([
                'parent.structureId'   => $this->structureId,
                'children.structureId' => $this->structureId,
            ]);

        $this->modifyQuery($subQuery);

        return $query->andWhere([
            'in',
            'elements.id',
            $subQuery->column()
        ]);
    }

    public static function create(array $config): OmniSearchFilter
    {
        $operator = ArrayHelper::remove($config, 'operator', null);
        if ($operator == null) {
            throw new InvalidArgumentException('$operator is not defined');
        }

        if (!array_key_exists($operator, static::$filterClassMap)) {
            throw new InvalidConfigException('Unsupported operator: ' . $operator);
        }

        $filterClass = static::$filterClassMap[$operator];

        return new $filterClass($config);
    }

    protected function isCustomField(): bool
    {
        return $this->customField != null;
    }

    public function isRelationField(): bool
    {
        return $this->isCustomField() && $this->customField instanceof BaseRelationField;
    }

    protected function getColumn(): string
    {
        if ($this->isRelationField()) {
            return 'targetId';
        } elseif ($this->isStructureParentField()) {
            return 'parent.elementId';
        } elseif ($this->isCustomField()) {
            $column = 'content.' . $this->_getFieldContentColumnName($this->customField);

            if ($this->customField instanceof Lightswitch) {
                $column = 'COALESCE(' . $column . ', ' . ($this->customField->default ? '1' : '0') . ')';
            }

            return $column;
        } else {
            $fieldToColumnMap = $this->getFieldToColumnMap();
            return $fieldToColumnMap[$this->field];
        }
    }

    protected function ensureArray($value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return $value;
    }

    protected function isMultiSelect(): bool
    {
        if (!$this->isCustomField()) {
            return false;
        }

        return ($this->customField instanceof BaseOptionsField) && $this->customField->getIsMultiOptionsField();
    }

    protected function dbSupportsJsonContains(): bool
    {
        $schema = \Craft::$app->db->schema;
        if (!($schema instanceof \yii\db\mysql\Schema)) {
            // Must be MySql or MariaDB
            return false;
        }

        if (static::$hasJsonSupport === null) {
            try {
                \Craft::$app->db->createCommand("SELECT JSON_CONTAINS('[\"test\"]', '\"test\"')")->queryScalar();

                static::$hasJsonSupport = true;
            } catch (Exception $e) {
                if (strpos($e->getMessage(), 'JSON_CONTAINS does not exist') !== false) {
                    static::$hasJsonSupport = false;
                } else {
                    throw $e;
                }
            }
        }

        return static::$hasJsonSupport;
    }

    /**
     * Returns a field’s corresponding content column name.
     *
     * @param FieldInterface|Field $field
     * @return string
     */
    private function _getFieldContentColumnName(FieldInterface $field): string
    {
        $prefix = $field->columnPrefix ?: 'field_';
        $suffix = '';

        if ($field->hasProperty('columnSuffix') && $field->columnSuffix != null) {
            $suffix = '_' . $field->columnSuffix;
        }

        return $prefix . $field->handle . $suffix;
    }

    private function isProductVariantField()
    {
        [$prefix] = explode(':', $this->field);

        return $prefix === 'variant';
    }

    private function isStructureParentField()
    {
        return $this->field === 'structure:parent';
    }

    private function isStructureAncestorField()
    {
        return $this->field === 'structure:ancestor';
    }

    protected function getFieldToColumnMap()
    {
        if (empty($this->fieldToColumnMap)) {
            $event = new DefineFieldColumnMapEvent();
            $this->trigger(self::EVENT_DEFINE_FIELD_COLUMN_MAP, $event);

            $common = [
                'id'          => 'elements.id',
                'title'       => 'content.title',
                'slug'        => 'elements_sites.slug',
                'dateCreated' => 'elements.dateCreated',
                'dateUpdated' => 'elements.dateUpdated',
                'enabled'     => 'elements.enabled',
            ];

            $this->fieldToColumnMap = array_merge($common, $event->fieldToColumnMap);
        }

        return $this->fieldToColumnMap;
    }

    protected function getConfig()
    {
        return [
            'elementType' => $this->elementType,
            'customField' => $this->customField,
            'parentField' => $this->parentField,
            'structureId' => $this->structureId,
        ];
    }
}
