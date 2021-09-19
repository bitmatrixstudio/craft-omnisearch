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

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(1, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
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

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(1, $titles);
		$this->assertContains('The 5AM Club', $titles);
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

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(1, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);


		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'starts_with',
				'value'    => 'Giant'
			]
		]);

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(0, $titles);
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

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(1, $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
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

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(4, $titles);
		$this->assertContains('The 5AM Club', $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
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

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(1, $titles);
		$this->assertContains('The 5AM Club', $titles);
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

		$titles = $this->query->select(['title'])->column();

		$this->assertCount(4, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
	}

	public function testFilterIsPresent()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'remarks',
				'operator' => 'is_present',
			]
		]);

		$titles = $this->query->select(['title'])->column();
		$this->assertCount(1, $titles);
		$this->assertContains('The 5AM Club', $titles);
	}

	public function testFilterIsNotPresent()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'remarks',
				'operator' => 'is_not_present',
			]
		]);

        $titles = $this->query->select(['title'])->column();
		$this->assertCount(4, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
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

		$titles = $this->query->select(['title'])->column();

		$this->assertCount(1, $titles);
		$this->assertContains('The 5AM Club', $titles);
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

        $titles = $this->query->select(['title'])->column();
		$this->assertCount(2, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The 5AM Club', $titles);
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

        $titles = $this->query->select(['title'])->column();

		$this->assertCount(2, $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
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

        $titles = $this->query->select(['title'])->column();

		$this->assertCount(4, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
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

        $titles = $this->query->select(['title'])->column();

		$this->assertCount(2, $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
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

        $titles = $this->query->select(['title'])->column();

		$this->assertCount(3, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The 5AM Club', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
	}

	public function testFilterInCheckboxList()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'languagesAvailable',
				'operator' => 'in',
				'value'    => ['chinese', 'german'],
			]
		]);

        $titles = $this->query->select(['title'])->column();

		$this->assertCount(2, $titles);
		$this->assertContains('The 5AM Club', $titles);
		$this->assertContains('The Gentle Parenting Book', $titles);
	}

	public function testFilterNotInCheckboxList()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'languagesAvailable',
				'operator' => 'not_in',
				'value'    => ['german'],
			]
		]);

        $titles = $this->query->select(['title'])->column();

		$this->assertCount(4, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The 5AM Club', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
	}

	public function testBooleanTrue()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'isFeatured',
				'operator' => 'equals',
				'value'    => 1,
			]
		]);

        $titles = $this->query->select(['title'])->column();
		$this->assertContains('The Gentle Parenting Book', $titles);
		$this->assertContains('Memoirs of a Geisha', $titles);
	}

	public function testBooleanFalse()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'isFeatured',
				'operator' => 'equals',
				'value'    => 0,
			]
		]);

		$titles = $this->query->select(['title'])->column();

		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('The 5AM Club', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
	}

    public function testFilterPostDateBetween()
    {
        $this->query->setOmnisearchFilters([
            [
                'field'    => 'postDate',
                'operator' => 'date_between',
                'value'    => '2020-11-30,9999-12-31',
            ]
        ]);

        $titles = $this->query->select(['title'])->column();
        $this->assertCount(3, $titles);
    }

	public function testFilterPostDateBefore()
	{
        $this->query->setOmnisearchFilters([
            [
                'field'    => 'postDate',
                'operator' => 'date_before',
                'value'    => '2020-01-01',
            ]
        ]);

        $titles = $this->query->select(['title'])->column();
        $this->assertCount(1, $titles);
        $this->assertContains('Awaken the Giant Within', $titles);
	}

	public function testFilterPostDateAfter()
	{
        $this->query->setOmnisearchFilters([
            [
                'field'    => 'postDate',
                'operator' => 'date_after',
                'value'    => '2020-11-30',
            ]
        ]);

        $titles = $this->query->select(['title'])->column();
        $this->assertCount(3, $titles);
	}

    public function testFilterCustomDateBetween()
    {
        $this->query->setOmnisearchFilters([
            [
                'field'    => 'publishingDate',
                'operator' => 'date_between',
                'value'    => '2018-12-31,2020-12-31',
            ]
        ]);

        $titles = $this->query->select(['title'])->column();

        $this->assertCount(2, $titles);
        $this->assertContains('The Gentle Parenting Book', $titles);
        $this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
    }

	public function testFilterCustomDateBefore()
	{
        $this->query->setOmnisearchFilters([
            [
                'field'    => 'publishingDate',
                'operator' => 'date_before',
                'value'    => '2020-12-31',
            ]
        ]);

        $titles = $this->query->select(['title'])->column();

        $this->assertCount(4, $titles);
        $this->assertContains('The Gentle Parenting Book', $titles);
        $this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
        $this->assertContains('The 5AM Club', $titles);
        $this->assertContains('Awaken the Giant Within', $titles);
	}

	public function testFilterCustomDateAfter()
	{
        $this->query->setOmnisearchFilters([
            [
                'field'    => 'publishingDate',
                'operator' => 'date_after',
                'value'    => '2020-12-31',
            ]
        ]);

        $titles = $this->query->select(['title'])->column();

        $this->assertCount(1, $titles);
        $this->assertContains('Memoirs of a Geisha', $titles);
	}

//	public function testMatrixField()
//	{
//	}

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

		$titles = $this->query->select(['title'])->column();

		$this->assertCount(2, $titles);
		$this->assertContains('Awaken the Giant Within', $titles);
		$this->assertContains('Nelson Mandela: No Easy Walk to Freedom', $titles);
	}
}
