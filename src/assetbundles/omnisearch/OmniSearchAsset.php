<?php
/**
 * OmniSearch plugin for Craft CMS 3.x
 *
 * A powerful control panel search filter replacement for CraftCMS
 *
 * @link      github.com/pohnean
 * @copyright Copyright (c) 2020 Tai Poh Nean
 */

namespace pohnean\omnisearch\assetbundles\omnisearch;

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
        $this->sourcePath = "@pohnean/omnisearch/assetbundles/omnisearch/dist";

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
