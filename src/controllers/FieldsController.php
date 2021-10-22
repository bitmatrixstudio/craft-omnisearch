<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\controllers;

use Craft;
use craft\base\Element;
use craft\base\Field;
use craft\commerce\elements\Product;
use craft\commerce\records\ProductType;
use craft\commerce\records\ShippingCategory;
use craft\commerce\records\TaxCategory;
use craft\elements\Asset;
use craft\elements\Category;
use craft\elements\Entry;
use craft\elements\Tag;
use craft\elements\User;
use craft\fields\BaseOptionsField;
use craft\fields\BaseRelationField;
use craft\fields\Categories;
use craft\fields\Date;
use craft\fields\Entries;
use craft\fields\Lightswitch;
use craft\fields\Number;
use craft\fields\Tags;
use craft\fields\Users;
use craft\helpers\Assets;
use craft\models\EntryType;
use craft\models\Section;
use craft\models\Site;
use craft\web\Controller;
use bitmatrix\omnisearch\OmniSearch;

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
        if (!Craft::$app->user->isGuest) {
            Craft::$app->language = Craft::$app->user->identity->getPreferredLanguage();
        }

        /** @var Element $element */
        $element = new $elementType;

        $fields = $this->getFields($element, $source);

        return $this->asJson($fields);
    }

    protected function getFields(Element $element, string $source)
    {
        $builtinFields = $this->getBuiltinFields($element, $source);

        $fields = [];
        if ($element instanceof Entry) {
            $fields = $this->getEntryFields($element, $source);
        } elseif ($element instanceof Category) {
            $fields = $this->getCategoryFields($element, $source);
        } elseif ($element instanceof Asset) {
            $fields = $this->getAssetFields($element, $source);
        } elseif ($element instanceof User) {
            $fields = $this->getUserFields($element);
        } elseif ($element instanceof Product) {
            $fields = $this->getProductFields($element, $source);
        }

        $uniqueFields = $this->getUniqueFields($fields);

        usort($uniqueFields, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return array_merge($builtinFields, $uniqueFields);
    }

    /**
     * @return array[]
     */
    protected function getBuiltinFields(Element $element, $source): array
    {
        $elementClass = get_class($element);

        $common = [
            [
                'name'     => Craft::t('app', 'ID'),
                'handle'   => 'id',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Title'),
                'handle'   => 'title',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Slug'),
                'handle'   => 'slug',
                'dataType' => OmniSearch::DATATYPE_TEXT,
            ],
            [
                'name'     => Craft::t('app', 'Date Created'),
                'handle'   => 'dateCreated',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('app', 'Date Updated'),
                'handle'   => 'dateUpdated',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('app', 'Enabled'),
                'handle'   => 'enabled',
                'dataType' => OmniSearch::DATATYPE_BOOLEAN,
            ],
        ];

        if ($elementClass !== User::class) {
            $common[] = [
                'name'     => Craft::t('app', 'Site'),
                'handle'   => 'site',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getSitesListData(),
            ];
        }

        $elementNativeFields = [];
        switch ($elementClass) {
            case Entry::class:
                $elementNativeFields = $this->getEntryNativeFields($source);
                break;
            case Category::class:
                $elementNativeFields = [
                    [
                        'name'     => Craft::t('app', 'Parent'),
                        'handle'   => 'structure:parent',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getCategoriesListData($source),
                    ],
                    [
                        'name'     => Craft::t('omnisearch', 'Ancestor'),
                        'handle'   => 'structure:ancestor',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getCategoriesListData($source),
                    ],
                ];
                break;
            case Asset::class:
                $elementNativeFields = [
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
                break;
            case User::class:
                $elementNativeFields = [
                    [
                        'name'     => Craft::t('app', 'Username'),
                        'handle'   => 'username',
                        'dataType' => OmniSearch::DATATYPE_TEXT,
                    ],
                    [
                        'name'     => Craft::t('app', 'Email'),
                        'handle'   => 'email',
                        'dataType' => OmniSearch::DATATYPE_TEXT,
                    ],
                    [
                        'name'     => Craft::t('app', 'First Name'),
                        'handle'   => 'firstName',
                        'dataType' => OmniSearch::DATATYPE_TEXT,
                    ],
                    [
                        'name'     => Craft::t('app', 'Last Name'),
                        'handle'   => 'lastName',
                        'dataType' => OmniSearch::DATATYPE_TEXT,
                    ],
                    [
                        'name'     => Craft::t('app', 'Full Name'),
                        'handle'   => 'fullName',
                        'dataType' => OmniSearch::DATATYPE_TEXT,
                    ],
                ];
                break;
            case 'craft\commerce\elements\Product':
                $elementNativeFields = [
                    [
                        'name'     => Craft::t('commerce', 'Free Shipping'),
                        'handle'   => 'product:freeShipping',
                        'dataType' => OmniSearch::DATATYPE_BOOLEAN,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Promotable'),
                        'handle'   => 'product:promotable',
                        'dataType' => OmniSearch::DATATYPE_BOOLEAN,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Available for purchase'),
                        'handle'   => 'product:availableForPurchase',
                        'dataType' => OmniSearch::DATATYPE_BOOLEAN,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Type'),
                        'handle'   => 'product:typeId',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getProductTypesListData()
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Tax Category'),
                        'handle'   => 'product:taxCategoryId',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getProductTaxCategoriesListData()
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Shipping Category'),
                        'handle'   => 'product:shippingCategoryId',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getProductShippingCategoriesListData()
                    ],
                    [
                        'name'     => Craft::t('commerce', 'SKU'),
                        'handle'   => 'variant:sku',
                        'dataType' => OmniSearch::DATATYPE_TEXT,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Stock'),
                        'handle'   => 'variant:stock',
                        'dataType' => OmniSearch::DATATYPE_NUMBER,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Length'),
                        'handle'   => 'variant:length',
                        'dataType' => OmniSearch::DATATYPE_NUMBER,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Width'),
                        'handle'   => 'variant:width',
                        'dataType' => OmniSearch::DATATYPE_NUMBER,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Height'),
                        'handle'   => 'variant:height',
                        'dataType' => OmniSearch::DATATYPE_NUMBER,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Weight'),
                        'handle'   => 'variant:weight',
                        'dataType' => OmniSearch::DATATYPE_NUMBER,
                    ],
                    [
                        'name'     => Craft::t('commerce', 'Price'),
                        'handle'   => 'variant:price',
                        'dataType' => OmniSearch::DATATYPE_NUMBER,
                    ],
                ];
                break;
            default:
                break;
        }


        return [
            [
                'name'     => $elementClass::displayName(),
                'handle'   => 'craft',
                'dataType' => OmniSearch::DATATYPE_MATRIX,
                'fields'   => array_merge($common, $elementNativeFields)
            ]
        ];
    }

    protected function getEntryFields(Entry $element, string $source)
    {
        $sectionsAndEntryTypes = $this->getSectionsAndEntryTypes($source);

        $fields = [];
        foreach ($sectionsAndEntryTypes as $sectionId => $entryTypes) {
            foreach ($entryTypes as $entryTypeId) {
                $element->sectionId = $sectionId;
                $element->typeId = $entryTypeId;

                $fields = array_merge($fields, $this->getFieldsForElement($element));
            }
        }

        return $fields;
    }

    protected function getCategoryFields(Category $category, string $source)
    {
        [, $uid] = explode(':', $source);

        $categoryGroup = \Craft::$app->categories->getGroupByUid($uid);
        $category->groupId = $categoryGroup->id;

        return $this->getFieldsForElement($category);
    }

    private function getAssetFields(Asset $asset, string $source)
    {
        [, $uid] = explode(':', $source);
        $folder = Craft::$app->assets->getFolderByUid($uid);

        $asset->volumeId = $folder->volumeId;
        $asset->folderId = $folder->id;

        return $this->getFieldsForElement($asset);
    }

    private function getUserFields(User $user)
    {
        return $this->getFieldsForElement($user);
    }

    private function getProductFields(Product $element, string $source)
    {
        [, $uid] = explode(':', $source);

        $productType = ProductType::find()->where(['uid' => $uid])->one();
        $element->typeId = $productType->id;

        return $this->getFieldsForElement($element);
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
            if (!$field->searchable) {
                continue;
            }

            if (OmniSearch::isMatrixField($field) || OmniSearch::isSuperTableField($field)) {
                $blockTypeFields = $field->getBlockTypeFields();
                $matrixFields = [];
                foreach ($blockTypeFields as $blockTypeField) {
                    if (OmniSearch::isMatrixField($blockTypeField) || OmniSearch::isSuperTableField($blockTypeField)) {
                        // nested matrix not supported yet
                        continue;
                    }

                    if (!$blockTypeField->searchable) {
                        continue;
                    }

                    $matrixFields[] = $this->createFieldConfig($element, $blockTypeField, $field->handle . '.');
                }

                $fieldConfig = [
                    'handle'   => $field->handle,
                    'name'     => $field->name,
                    'dataType' => OmniSearch::DATATYPE_MATRIX,
                    'fields'   => $matrixFields,
                ];
            } else {
                $fieldConfig = $this->createFieldConfig($element, $field, '');
            }

            $fields[] = $fieldConfig;
        }

        return $fields;
    }

    protected function createFieldConfig(Element $element, $field, $prefix = '')
    {
        $fieldConfig = [
            'handle'   => $prefix . $field->handle,
            'name'     => $field->name,
            'dataType' => $this->mapDataType($field),
        ];

        if ($field instanceof BaseOptionsField) {
            $fieldConfig['items'] = array_map(function ($item) {
                unset($item['default']);
                return $item;
            }, $field->options);
        } elseif ($field instanceof BaseRelationField) {
            $fieldConfig['items'] = $this->getRelationListItems($field);
        }

        return $fieldConfig;
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
        } elseif (strpos($source, 'section:') > -1) {
            $sectionUid = str_replace('section:', '', $source);
            $section = Craft::$app->getSections()->getSectionByUid($sectionUid);
            foreach (Craft::$app->getSections()->getEntryTypesBySectionId($section->id) as $entryType) {
                $sectionsAndEntryTypes[$section->id][] = $entryType->id;
            }
        }

        return $sectionsAndEntryTypes;
    }

    /**
     * @return string
     */
    protected function mapDataType(Field $field): string
    {
        if ($field instanceof Lightswitch) {
            return OmniSearch::DATATYPE_BOOLEAN;
        } elseif ($field instanceof Date) {
            return OmniSearch::DATATYPE_DATE;
        } elseif ($field instanceof BaseOptionsField || $field instanceof BaseRelationField) {
            return OmniSearch::DATATYPE_LIST;
        } elseif ($field instanceof Number) {
            return OmniSearch::DATATYPE_NUMBER;
        }

        return OmniSearch::DATATYPE_TEXT;
    }

    /**
     * @return array|\craft\base\ElementInterface[]|User[]|null
     */
    protected function getUsersListData($sources = []): array
    {
        $query = User::find()
            ->select([
                'users.id AS value',
                'COALESCE(CONCAT(users.firstName, " ", users.lastName), users.username) AS label',
            ]);

        if (is_array($sources) && count($sources) > 0) {
            $groupIds = array_filter(array_map(function ($source) {
                [, $uid] = explode(':', $source);
                $group = Craft::$app->userGroups->getGroupByUid($uid);
                return $group != null ? $group->id : null;
            }, $sources));

            $query->groupId($groupIds);
        }

        return $query
            ->asArray()
            ->all();
    }

    private function getEntryTypesListData($source)
    {
        $sectionsAndEntryTypes = $this->getSectionsAndEntryTypes($source);
        $sectionIds = array_keys($sectionsAndEntryTypes);

        $entryTypes = [];
        foreach ($sectionIds as $sectionId) {
            $section = Craft::$app->sections->getSectionById($sectionId);
            $entryTypes = array_merge($entryTypes, Craft::$app->sections->getEntryTypesBySectionId($section->id));
        }

        return array_map(function (EntryType $entryType) {
            return [
                'value' => $entryType->id,
                'label' => $entryType->name,
            ];
        }, $entryTypes);
    }

    /**
     * @return array
     */
    protected function getFileKindsListData(): array
    {
        $fileKinds = Assets::getFileKinds();

        $listData = [];
        foreach ($fileKinds as $id => $fileKind) {
            $listData[] = [
                'value' => $id,
                'label' => $fileKind['label'],
            ];
        }

        return $listData;
    }

    protected function getRelationListItems(BaseRelationField $field): array
    {
        $items = [];

        if ($field instanceof Tags) {
            $items = $this->getTagsListData($field);
        } elseif ($field instanceof Users) {
            $items = $this->getUsersListData($field->sources);
        } elseif ($field instanceof Entries) {
            $items = $this->getEntriesListData($field->sources);
        } elseif ($field instanceof \craft\fields\Assets) {
            $items = $this->getAssetsListData($field->sources);
        } elseif ($field instanceof Categories) {
            $items = $this->getCategoriesListData($field->source);
        }

        return $items;
    }

    protected function getTagsListData(Tags $field): array
    {
        [, $uid] = explode(':', $field->source);
        $tagGroup = Craft::$app->tags->getTagGroupByUid($uid);

        return Tag::find()
            ->select([
                'elements.id AS value',
                'title AS label',
            ])
            ->anyStatus()
            ->groupId($tagGroup->id)
            ->asArray()
            ->all();
    }

    protected function getEntriesListData($sources = []): array
    {
        $sections = $this->getSections($sources);

        $sectionIds = array_map(function (Section $section) {
            return $section->id;
        }, $sections);

        return Entry::find()
            ->select([
                'elements.id AS value',
                'title AS label',
                'COALESCE(level, 1) AS level',
            ])
            ->sectionId($sectionIds)
            ->asArray()
            ->all();
    }

    protected function getAssetsListData($sources = []): array
    {
        $volumeIds = [];
        if ($sources === '*') {
            $volumeIds = Craft::$app->volumes->getAllVolumeIds();
        } elseif (is_array($sources) && count($sources) > 0) {
            /** @var int[] $volumeIds */
            $volumeIds = array_map(function ($source) {
                [, $uid] = explode(':', $source);
                return Craft::$app->volumes->getVolumeByUid($uid)->id;
            }, $sources);
        }

        return Asset::find()
            ->select([
                'elements.id AS value',
                'title AS label',
            ])
            ->volumeId($volumeIds)
            ->asArray()
            ->all();
    }

    protected function getCategoriesListData($source): array
    {
        [, $uid] = explode(':', $source);
        $catGroup = Craft::$app->categories->getGroupByUid($uid);

        return Category::find()
            ->select([
                'elements.id AS value',
                'title AS label',
                'level',
            ])
            ->group($catGroup)
            ->asArray()
            ->all();
    }

    private function getProductTypesListData()
    {
        return ProductType::find()
            ->select([
                'id AS value',
                'name AS label',
            ])
            ->asArray()
            ->all();
    }

    private function getProductTaxCategoriesListData()
    {
        return TaxCategory::find()
            ->select([
                'id AS value',
                'name AS label',
            ])
            ->asArray()
            ->all();
    }

    private function getProductShippingCategoriesListData()
    {
        return ShippingCategory::find()
            ->select([
                'id AS value',
                'name AS label',
            ])
            ->asArray()
            ->all();
    }

    /**
     * @param array $fields
     * @return array
     */
    protected function getUniqueFields(array $fields): array
    {
        $uniqueFields = [];
        foreach ($fields as $field) {
            $uniqueFields[$field['handle']] = $field;
        }

        return array_values($uniqueFields);
    }

    private function getSitesListData()
    {
        $sites = \Craft::$app->sites->getAllSites();

        return array_map(function (Site $site) {
            return [
                'value' => $site->id,
                'label' => $site->name,
            ];
        }, $sites);
    }

    /**
     * @param $source
     * @return array[]
     */
    protected function getEntryNativeFields($source)
    {
        $nativeFields = [
            [
                'name'     => Craft::t('app', 'Post Date'),
                'handle'   => 'postDate',
                'dataType' => OmniSearch::DATATYPE_DATE,
            ],
            [
                'name'     => Craft::t('app', 'Author'),
                'handle'   => 'authorId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getUsersListData($source),
            ],
            [
                'name'     => Craft::t('app', 'Entry Type'),
                'handle'   => 'typeId',
                'dataType' => OmniSearch::DATATYPE_LIST,
                'items'    => $this->getEntryTypesListData($source),
            ],
        ];

        if (strpos($source, ':') !== false) {
            $section = $this->getSectionBySource($source);

            if ($section != null && $section->structureId != null) {
                $nativeFields[] = [
                    'name'     => Craft::t('app', 'Parent'),
                    'handle'   => 'structure:parent',
                    'dataType' => OmniSearch::DATATYPE_LIST,
                    'items'    => $this->getEntriesListData([$source]),
                ];

                $nativeFields[] = [
                    'name'     => Craft::t('app', 'Ancestor'),
                    'handle'   => 'structure:ancestor',
                    'dataType' => OmniSearch::DATATYPE_LIST,
                    'items'    => $this->getEntriesListData([$source]),
                ];
            }
        }

        return $nativeFields;
    }

    /**
     * @param $sources
     * @return array|Section[]
     */
    protected function getSections($sources)
    {
        $sections = [];
        if ($sources === '*') {
            $sections = Craft::$app->sections->getAllSections();
        } elseif (is_array($sources) && count($sources) > 0) {
            /** @var Section[] $sections */
            $sections = array_filter(array_map(function ($source) {
                return $this->getSectionBySource($source);
            }, $sources));
        }

        return $sections;
    }

    /**
     * @param $source
     * @return Section|null
     */
    protected function getSectionBySource($source)
    {
        [, $uid] = explode(':', $source);
        return Craft::$app->sections->getSectionByUid($uid);
    }
}
