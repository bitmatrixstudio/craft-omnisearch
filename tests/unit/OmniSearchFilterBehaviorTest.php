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
		$this->assertCount(2, $this->query->all());
	}

	public function testFilterTitleContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'contain',
				'value'    => 'Legendary'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('Chapter 2: A Daily Philosophy of Becoming Legendary', $entries[0]->title);
	}

	public function testFilterSlugContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'slug',
				'operator' => 'contain',
				'value'    => 'daily-philosophy'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('Chapter 2: A Daily Philosophy of Becoming Legendary', $entries[0]->title);
	}

	public function testFilterCustomTextFieldContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'bodyContent',
				'operator' => 'contain',
				'value'    => 'Lorem ipsum'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('Chapter 1: A Dangerous Deed', $entries[0]->title);
	}

	public function testFilterTitleNotContain()
	{
		$this->query->setOmnisearchFilters([
			[
				'field'    => 'title',
				'operator' => 'not_contain',
				'value'    => 'Legendary'
			]
		]);

		$entries = $this->query->all();
		$this->assertCount(1, $entries);
		$this->assertEquals('Chapter 1: A Dangerous Deed', $entries[0]->title);
	}

	// filter "contain" other variables
	// filter "not_contain" other variables
	//
}
