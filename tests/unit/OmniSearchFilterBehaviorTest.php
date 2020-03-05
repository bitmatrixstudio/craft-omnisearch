<?php

namespace tests;

use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use fixtures\EntryFixture;
use pohnean\omnisearch\behaviors\OmniSearchFilterBehavior;

class OmniSearchFilterBehaviorTest extends \Codeception\Test\Unit
{
	/**
	 * @var UnitTester
	 */
	protected $tester;

	/**
	 * @var EntryQuery|OmniSearchFilterBehavior
	 */
	protected $query;

	protected function _before()
	{
		$this->query = Entry::find();
	}

	protected function _after()
	{
	}

	public function _fixtures()
	{
		return [
			'entries' => [
				'class' => EntryFixture::class,
			]
		];
	}

	// tests
	public function testWithoutFilters()
	{
		$this->assertCount(5, $this->query->all());
	}

	public function testFilterTitleContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'contain',
				'value'    => 'Giant'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
	}

	public function testFilterSlugContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'slug',
				'operator' => 'contain',
				'value'    => '5am-club'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('The 5AM Club', $entries[0]->title);
	}

	public function testFilterTitleStartsWith()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'starts_with',
				'value'    => 'Awaken'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);


		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'starts_with',
				'value'    => 'Giant'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(0, $entries);
	}

	public function testFilterCustomTextFieldContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'summary',
				'operator' => 'contain',
				'value'    => 'children'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('The Gentle Parenting Book', $entries[0]->title);
	}

	public function testFilterTitleNotContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'not_contain',
				'value'    => 'giant'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(4, $entries);
		$this->assertEquals('The 5AM Club', $entries[0]->title);
		$this->assertEquals('The Gentle Parenting Book', $entries[1]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[2]->title);
		$this->assertEquals('Memoirs of a Geisha', $entries[3]->title);
	}

	public function testFilterTitleEquals()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'equals',
				'value'    => 'The 5AM Club'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('The 5AM Club', $entries[0]->title);
	}

	public function testFilterTitleNotEquals()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'not_equals',
				'value'    => 'The 5AM Club'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(4, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
		$this->assertEquals('The Gentle Parenting Book', $entries[1]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[2]->title);
		$this->assertEquals('Memoirs of a Geisha', $entries[3]->title);
	}

	public function testFilterIsPresent()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'remarks',
				'operator' => 'is_present',
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('The 5AM Club', $entries[0]->title);
	}

	public function testFilterIsNotPresent()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'remarks',
				'operator' => 'is_not_present',
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(4, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
		$this->assertEquals('The Gentle Parenting Book', $entries[1]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[2]->title);
		$this->assertEquals('Memoirs of a Geisha', $entries[3]->title);
	}

	public function testFilterGreaterThan()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'rating',
				'operator' => 'gt',
				'value'    => 9,
			]
		]);

		$entries = $this->query->all();

		$this->assertCount(1, $entries);
		$this->assertEquals('The 5AM Club', $entries[0]->title);
	}

	public function testFilterGreaterThanOrEqual()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'rating',
				'operator' => 'gte',
				'value'    => 7.9,
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(2, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
		$this->assertEquals('The 5AM Club', $entries[1]->title);
	}

	public function testFilterLessThan()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'rating',
				'operator' => 'lt',
				'value'    => 7,
			]
		]);

		$entries = $this->query->all();

		$this->assertCount(2, $entries);
		$this->assertEquals('The Gentle Parenting Book', $entries[0]->title);
		$this->assertEquals('Memoirs of a Geisha', $entries[1]->title);
	}

	public function testFilterLessThanOrEqual()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'rating',
				'operator' => 'lte',
				'value'    => 7.9,
			]
		]);

		$entries = $this->query->all();

		$this->assertCount(4, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
		$this->assertEquals('The Gentle Parenting Book', $entries[1]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[2]->title);
		$this->assertEquals('Memoirs of a Geisha', $entries[3]->title);
	}

	public function testFilterInSingleSelect()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'genre',
				'operator' => 'in',
				'value'    => ['parenting', 'biography'],
			]
		]);

		$entries = $this->query->all();

		$this->assertCount(2, $entries);
		$this->assertEquals('The Gentle Parenting Book', $entries[0]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[1]->title);
	}

	public function testFilterNotInSingleSelect()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'genre',
				'operator' => 'not_in',
				'value'    => ['fiction', 'parenting'],
			]
		]);

		$entries = $this->query->all();

		$this->assertCount(3, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
		$this->assertEquals('The 5AM Club', $entries[1]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[2]->title);
	}

	public function testFilterInMultiSelect()
	{
//		$this->query->setOmnisearchFilters([
//			[
//				'field'    => 'genre',
//				'operator' => 'in',
//				'value'    => ['parenting', 'biography'],
//			]
//		]);
//
//		$entries = $this->query->all();
//
//		$this->assertCount(2, $entries);
//		$this->assertEquals('The Gentle Parenting Book', $entries[0]->title);
//		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[1]->title);
	}

	public function testFilterNotInMultiSelect()
	{
	}

	public function testFilterPostDateBefore()
	{
	}

	public function testFilterCustomDateBefore()
	{
	}

	public function testFilterPostDateAfter()
	{
	}

	public function testFilterCustomDateAfter()
	{
	}

	public function testMultipleFilters()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'genre',
				'operator' => 'in',
				'value'    => ['productivity', 'biography'],
			],
			[
				'field'    => 'rating',
				'operator' => 'lt',
				'value'    => 8,
			],
		]);

		$entries = $this->query->all();

		$this->assertCount(2, $entries);
		$this->assertEquals('Awaken the Giant Within', $entries[0]->title);
		$this->assertEquals('Nelson Mandela: No Easy Walk to Freedom', $entries[1]->title);
	}
}
