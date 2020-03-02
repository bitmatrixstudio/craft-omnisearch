<?php

namespace pohnean\omnisearch\filters;

use craft\base\FieldInterface;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\ArrayHelper;
use craft\base\Field;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;

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

		$filterClassName = str_replace('_', '', ucwords($operator, '_'));
		$filterClass = "\\pohnean\\omnisearch\\filters\\{$filterClassName}Filter";

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
}
