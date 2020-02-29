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
use craft\controllers\ElementIndexesController;
use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\elements\db\EntryQuery;
use craft\events\CancelableEvent;
use craft\events\DefineBehaviorsEvent;
use craft\helpers\Json;
use craft\web\Controller;
use craft\web\View;
use pohnean\omnisearch\assetbundles\OmniSearch\OmniSearchAsset;
use pohnean\omnisearch\behaviors\OmniSearchFilterBehavior;
use pohnean\omnisearch\models\Settings;
use pohnean\omnisearch\services\OmniSearchService as OmniSearchServiceService;
use yii\base\ActionEvent;
use yii\base\Event;

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

			$this->registerFilters();
		}

		Event::on(EntryQuery::class, EntryQuery::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
			Craft::info('Attach OmniSearch behavior for EntryQuery...', 'omnisearch');

			/** @var EntryQuery $sender */
			$sender = $event->sender;
			$sender->attachBehavior('omnisearch', new OmniSearchFilterBehavior());
		});
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
			'omnisearch/settings',
			[
				'settings' => $this->getSettings()
			]
		);
	}

	private function registerFilters()
	{
		$filters = [
			[
				'fieldName' => 'Title',
			],
			[
				'fieldName' => 'Post Date',
			],
		];

		Craft::$app->getView()->registerJs("window.omnisearchFilters = " . Json::encode($filters) . ";", View::POS_HEAD);
	}
}
