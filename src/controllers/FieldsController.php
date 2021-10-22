<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\controllers;

use bitmatrix\omnisearch\fields\DefineFieldsEvent;
use Craft;
use craft\web\Controller;

/**
 * @author    Tai Poh Nean
 * @package   OmniSearch
 * @since     1.0.0
 */
class FieldsController extends Controller
{
    /**
     * @event DefineBehaviorsEvent The event that is triggered when defining the class behaviors
     * @see behaviors()
     */
    const EVENT_DEFINE_FIELDS = 'defineFields';

    /**
     * @return mixed
     */
    public function actionIndex($elementType, $source)
    {
        if (!Craft::$app->user->isGuest) {
            Craft::$app->language = Craft::$app->user->identity->getPreferredLanguage();
        }

        $event = new DefineFieldsEvent([
            'elementType' => $elementType,
            'source'      => $source,
        ]);
        $this->trigger(self::EVENT_DEFINE_FIELDS, $event);

        return $this->asJson($event->fields);
    }
}
