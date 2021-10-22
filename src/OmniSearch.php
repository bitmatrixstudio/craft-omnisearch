<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch;

use bitmatrix\omnisearch\controllers\FieldsController;
use bitmatrix\omnisearch\fields\AssetFields;
use bitmatrix\omnisearch\fields\BaseFields;
use bitmatrix\omnisearch\fields\CategoryFields;
use bitmatrix\omnisearch\fields\DefineFieldColumnMapEvent;
use bitmatrix\omnisearch\fields\DefineFieldsEvent;
use bitmatrix\omnisearch\fields\EntryFields;
use bitmatrix\omnisearch\fields\OrderFields;
use bitmatrix\omnisearch\fields\ProductFields;
use bitmatrix\omnisearch\fields\UserFields;
use bitmatrix\omnisearch\filters\OmniSearchFilter;
use Craft;
use craft\base\FieldInterface;
use craft\base\Plugin;
use craft\commerce\elements\Order;
use craft\commerce\elements\Product;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\db\ElementQuery;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\elements\User;
use craft\events\DefineBehaviorsEvent;
use bitmatrix\omnisearch\assetbundles\omnisearch\OmniSearchAsset;
use bitmatrix\omnisearch\behaviors\OmniSearchFilterBehavior;
use bitmatrix\omnisearch\services\OmniSearchService as OmniSearchServiceService;
use craft\fields\Matrix;
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

    /**
     * @var array
     */
    public static $fieldTypes = [
        Entry::class    => EntryFields::class,
        Category::class => CategoryFields::class,
        Asset::class    => AssetFields::class,
        User::class     => UserFields::class,
        Product::class  => ProductFields::class,
        Order::class    => OrderFields::class,
    ];

    // Public Methods
    // =========================================================================
    public static function isMatrixField(FieldInterface $field): bool
    {
        return $field instanceof Matrix;
    }

    public static function isSuperTableField(FieldInterface $field): bool
    {
        return class_exists('\verbb\supertable\fields\SuperTableField') && $field instanceof \verbb\supertable\fields\SuperTableField;
    }

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
            $sender->attachBehavior('omnisearch', new OmniSearchFilterBehavior([
                'elementType' => $sender->elementType,
            ]));
        });

        Event::on(FieldsController::class, FieldsController::EVENT_DEFINE_FIELDS, function (DefineFieldsEvent $event) {
            if (!array_key_exists($event->elementType, static::$fieldTypes)) {
                return;
            }

            $elementFieldClass = static::$fieldTypes[$event->elementType];

            /** @var BaseFields $elementField */
            $elementField = new $elementFieldClass();

            $event->fields = $elementField->getFields($event->source);
        });

        Event::on(OmniSearchFilter::class, OmniSearchFilter::EVENT_DEFINE_FIELD_COLUMN_MAP, function (DefineFieldColumnMapEvent $event) {
            $elementType = $event->sender->elementType;
            if (!array_key_exists($elementType, static::$fieldTypes)) {
                return;
            }

            $elementFieldClass = static::$fieldTypes[$elementType];
            $event->fieldToColumnMap = $elementFieldClass::fieldToColumnMap();
        });
    }
}
