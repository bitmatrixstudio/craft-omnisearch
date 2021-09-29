<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch;

use Craft;
use craft\base\Plugin;
use craft\elements\db\ElementQuery;
use craft\elements\db\EntryQuery;
use craft\events\DefineBehaviorsEvent;
use bitmatrix\omnisearch\assetbundles\omnisearch\OmniSearchAsset;
use bitmatrix\omnisearch\behaviors\OmniSearchFilterBehavior;
use bitmatrix\omnisearch\services\OmniSearchService as OmniSearchServiceService;
use yii\base\Event;

/**
 * Class OmniSearch
 *
 * @author    Bitmatrix Studio
 * @package   OmniSearch
 * @since     1.0.0
 *
 * @property  OmniSearchServiceService $omniSearchService
 */
class OmniSearch extends Plugin
{
    const DATATYPE_TEXT = 'text';
    const DATATYPE_NUMBER = 'number';
    const DATATYPE_DATE = 'date';
    const DATATYPE_BOOLEAN = 'boolean';
    const DATATYPE_LIST = 'list';
    const DATATYPE_MATRIX = 'matrix';

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

        Event::on(ElementQuery::class, EntryQuery::EVENT_DEFINE_BEHAVIORS, function (DefineBehaviorsEvent $event) {
            /** @var ElementQuery $sender */
            $sender = $event->sender;
            $sender->attachBehavior('omnisearch', new OmniSearchFilterBehavior());
        });
    }
}
