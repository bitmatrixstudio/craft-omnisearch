<?php
/**
 * OmniSearch plugin for Craft CMS 3.x
 *
 * A powerful control panel search filter replacement for CraftCMS
 *
 * @link      github.com/pohnean
 * @copyright Copyright (c) 2020 Tai Poh Nean
 */

namespace pohnean\omnisearch\controllers;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\web\Controller;

/**
 * @author    Tai Poh Nean
 * @package   OmniSearch
 * @since     1.0.0
 */
class FieldsController extends Controller
{
	/**
	 * @return mixed
	 */
	public function actionIndex($elementType, $source)
	{
		/** @var Element $element */
		$element = new $elementType;

		$entryFields = $this->getEntryFields($element, $source);

		return $this->asJson(array_merge([
			[
				'name'   => Craft::t('app', 'Title'),
				'handle' => 'title',
			],
			[
				'name'   => Craft::t('app', 'Slug'),
				'handle' => 'slug',
			],
		], $entryFields));
	}

	protected function getEntryFields(Element $element, string $source)
	{
		$sectionsAndEntryTypes = $this->getSectionsAndEntryTypes($source);

		$fields = [];
		foreach ($sectionsAndEntryTypes as $sectionId => $entryTypes) {
			foreach ($entryTypes as $entryTypeId) {
				$element->sectionId = $sectionId;
				$element->typeId = $entryTypeId;

				$elementFields = $this->getFieldsForElement($element);

				$fields = array_merge($fields, $elementFields);
			}
		}

		return $fields;
	}

	/**
	 * @return array
	 */
	protected function getFieldsForElement(Element $element): array
	{
		$fieldLayout = $element->getFieldLayout();
		if (!$element::hasContent() || $fieldLayout === null) {
			return [];
		}

		$fields = [];

		/** @var Field $field */
		foreach ($fieldLayout->getFields() as $field) {
			if ($field->searchable) {
				$fields[] = [
					'handle' => $field->handle,
					'name'   => $field->name,
				];
			}
		}

		return $fields;
	}

	/**
	 * @param $source
	 * @return array
	 */
	protected function getSectionsAndEntryTypes(string $source): array
	{
		$sectionsAndEntryTypes = [];

		if ($source === '*') {
			foreach (Craft::$app->getSections()->getAllSections() as $section) {
				foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
					$sectionsAndEntryTypes[$section->id][] = $entryType->id;
				}
			}
		} else {
			$sectionUid = str_replace('section:', '', $source);
			$section = Craft::$app->getSections()->getSectionByUid($sectionUid);
			foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
				$sectionsAndEntryTypes[$section->id][] = $entryType->id;
			}
		}

		return $sectionsAndEntryTypes;
	}
}
