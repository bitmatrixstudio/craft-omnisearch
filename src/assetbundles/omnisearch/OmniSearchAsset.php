<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\assetbundles\omnisearch;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\View;

/**
 * @author    Tai Poh Nean
 * @package   OmniSearch
 * @since     1.0.0
 */
class OmniSearchAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@bitmatrix/omnisearch/assetbundles/omnisearch/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'omnisearch.umd.min.js',
        ];

        $this->css = [
            'omnisearch.css',
        ];

        parent::init();
    }

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $view->registerTranslations('omnisearch', [
                'Add Filter',
                'Choose Field',
                'Search attributes...',
                'Search...',
                'True',
                'False',
                'contains',
                'does not contain',
                'includes',
                'does not include',
                'starts with',
                'equals',
                'does not equal',
                'greater than',
                'greater than or equal',
                'less than',
                'less than or equal',
                'is present',
                'is not present',
                'is between',
                'is before',
                'is after',
                '{start} to {end}',
            ]);
        }
    }
}
