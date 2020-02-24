<?php
/**
 * OmniSearch plugin for Craft CMS 3.x
 *
 * A powerful control panel search filter replacement for CraftCMS
 *
 * @link      github.com/pohnean
 * @copyright Copyright (c) 2020 Tai Poh Nean
 */

namespace pohnean\omnisearch;

use Craft;
use craft\base\Plugin;
use pohnean\omnisearch\assetbundles\OmniSearch\OmniSearchAsset;
use pohnean\omnisearch\models\Settings;
use pohnean\omnisearch\services\OmniSearchService as OmniSearchServiceService;

/**
 * Class OmniSearch
 *
 * @author    Tai Poh Nean
 * @package   OmniSearch
 * @since     1.0.0
 *
 * @property  OmniSearchServiceService $omniSearchService
 */
class OmniSearch extends Plugin
{
	// Static Properties
	// =========================================================================

	/**
	 * @var OmniSearch
	 */
	public static $plugin;

	// Public Properties
	// =========================================================================

	/**
	 * @var string
	 */
	public $schemaVersion = '1.0.0';

	// Public Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	public function init()
	{
		parent::init();
		self::$plugin = $this;

		if (Craft::$app->getRequest()->isCpRequest) {
			Craft::$app->getView()->registerAssetBundle(OmniSearchAsset::class);
		}
	}

	// Protected Methods
	// =========================================================================

	/**
	 * @inheritdoc
	 */
	protected function createSettingsModel()
	{
		return new Settings();
	}

	/**
	 * @inheritdoc
	 */
	protected function settingsHtml(): string
	{
		return Craft::$app->view->renderTemplate(
			'omni-search/settings',
			[
				'settings' => $this->getSettings()
			]
		);
	}
}
