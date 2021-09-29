<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\assetbundles\omnisearch;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

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
}
