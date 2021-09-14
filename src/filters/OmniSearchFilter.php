<?php

namespace pohnean\omnisearch\filters;

use craft\base\Field;
use craft\base\FieldInterface;
use craft\db\Query;
use craft\fields\BaseOptionsField;
use craft\fields\Lightswitch;
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
     * @var Field|null
     */
    public $customField;

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
    ];

    protected static $fieldToColumnMap = [
        'title'      => 'content.title',
        'postDate'   => 'entries.postDate',
        'slug'       => 'elements_sites.slug',
    ];

    protected static $hasJsonSupport;

    abstract public function modifyQuery(Query $query): Query;

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

    protected function getColumn(): string
    {
        if ($this->isCustomField()) {
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
