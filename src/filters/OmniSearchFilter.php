<?php

namespace pohnean\omnisearch\filters;

use craft\base\Field;
use craft\base\FieldInterface;
use craft\db\Query;
use craft\helpers\ArrayHelper;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;

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
	 * @var Field[]|null
	 */
	public $customFields;

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
		'title' => 'content.title',
		'slug'  => 'elements_sites.slug',
	];

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

	protected function getColumn(): string
	{
		$columnMap = $this->getColumnMap();
		if (!array_key_exists($this->field, $columnMap)) {
			throw new InvalidArgumentException('Invalid field: "' . $this->field . '"');
		}

		return $columnMap[$this->field];
	}

	/**
	 * @return array
	 */
	protected function getColumnMap(): array
	{
		$columnMap = static::$fieldToColumnMap;
		if (!empty($this->customFields)) {
			foreach ($this->customFields as $field) {
				if ($field->hasContentColumn()) {
					$columnMap[$field->handle] = 'content.' . $this->_getFieldContentColumnName($field);
				}
			}
		}

		return $columnMap;
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

	protected function ensureArray($value)
	{
		if (!is_array($value)) {
			$value = [$value];
		}

		return $value;
	}
}
