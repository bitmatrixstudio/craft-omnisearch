<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\fields;

use bitmatrix\omnisearch\OmniSearch;
use Craft;
use craft\base\Element;
use craft\elements\Asset;

class AssetFields extends BaseFields
{
    public function elementType()
    {
        return Asset::class;
    }

    public function builtInFields($source)
    {
        return [
            [
                'name'     => Craft::t('app', 'Filename'),
                'handle'   => 'filename',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'File Kind'),
                'handle'   => 'kind',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getFileKindsListData()
            ],
            [
                'name'     => Craft::t('app', 'Image Width'),
                'handle'   => 'width',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('app', 'Image Height'),
                'handle'   => 'height',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('app', 'File Size'),
                'handle'   => 'size',
                'dataType' => OmniSearch::DATATYPE_NUMBER,
            ],
            [
                'name'     => Craft::t('app', 'Uploaded by'),
                'handle'   => 'uploaderId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getUsersListData($source),
            ],
        ];
    }

    /**
     * @param Asset $element
     * @param $source
     * @return array
     */
    public function customFields(Element $element, $source)
    {
        $uid = $this->extractUidFromSource($source);
        $folder = Craft::$app->assets->getFolderByUid($uid);

        $element->volumeId = $folder->volumeId;
        $element->folderId = $folder->id;

        return $this->getCustomFieldsForElement($element);
    }

    public static function fieldToColumnMap()
    {
        return [
            'filename'   => 'assets.filename',
            'kind'       => 'assets.kind',
            'width'      => 'assets.width',
            'height'     => 'assets.height',
            'size'       => 'assets.size',
            'uploaderId' => 'assets.uploaderId',
        ];
    }
}