<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\filters;

use craft\base\Field;
use craft\base\FieldInterface;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\MatrixBlock;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Lightswitch;
use craft\fields\Matrix;
use craft\fields\Tags;
use craft\helpers\ArrayHelper;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\db\Exception;

abstract class OmniSearchFilter extends BaseObject
{
    /**
     * @var string
     */
    public $field;

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

    protected static $fieldToColumnMap = [
        'id'          => 'elements.id',
        'title'       => 'content.title',
        'slug'        => 'elements_sites.slug',
        'dateCreated' => 'elements.dateCreated',
        'dateUpdated' => 'elements.dateUpdated',
        // Entries
        'postDate'    => 'entries.postDate',
        'authorId'    => 'entries.authorId',
        'typeId'      => 'entries.typeId',
        // Categories
        // Assets
        'filename'    => 'assets.filename',
        'kind'        => 'assets.kind',
        'width'       => 'assets.width',
        'height'      => 'assets.height',
        'size'        => 'assets.size',
        // Users
        'username'    => 'users.username',
        'email'       => 'users.email',
        'firstName'   => 'users.firstName',
        'lastName'    => 'users.lastName',
        'fullName'    => 'CONCAT(users.firstName, " ", users.lastName)',
    ];

    protected static $hasJsonSupport;

    abstract public function modifyQuery(Query $query): Query;

    public function modifyElementQuery(Query $query): Query
    {
        if ($this->isMatrixField()) {
            $matrixBlockQuery = MatrixBlock::find()
                ->select('matrixblocks.ownerId')
                ->fieldId($this->parentField->id);

            if ($this->isRelationField()) {
                $matrixBlockQuery = $this->applyRelationQuery($matrixBlockQuery);
            } else {
                $this->modifyQuery($matrixBlockQuery);
            }

            return $query->andWhere([
                'in',
                'elements.id',
                $matrixBlockQuery
            ]);
        } elseif ($this->isRelationField()) {
            return $this->applyRelationQuery($query);
        }

        return $this->modifyQuery($query);
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
            $relationSubQuery
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

    public function isMatrixField(): bool
    {
        return strpos($this->field, '.') > -1;
    }

    public function isRelationField(): bool
    {
        return $this->isCustomField() && $this->customField instanceof BaseRelationField;
    }

    protected function getColumn(): string
    {
        if ($this->isRelationField()) {
            return 'targetId';
        } elseif ($this->isCustomField()) {
            $column = 'content.' . $this->_getFieldContentColumnName($this->customField);

            if ($this->customField instanceof Lightswitch) {
                $column = 'COALESCE(' . $column . ', ' . ($this->customField->default ? '1' : '0') . ')';
            }

            return $column;
        } else {
            return self::$fieldToColumnMap[$this->field];
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
     * @param FieldInterface $field
     * @return string
     */
    private function _getFieldContentColumnName(FieldInterface $field): string
    {
        /** @var Field $field */
        return ($field->columnPrefix ?: 'field_') . $field->handle;
    }
}
