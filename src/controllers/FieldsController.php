<?php
/*
 * @copyright Copyright (c) 2021 Bitmatrix Studio
 * @license https://craftcms.github.io/license/
 */

namespace bitmatrix\omnisearch\controllers;

use Craft;
use craft\base\Element;
use craft\base\Field;
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
use craft\fields\Matrix;
use craft\fields\Number;
use craft\fields\Tags;
use craft\fields\Users;
use craft\helpers\Assets;
use craft\models\EntryType;
use craft\models\Section;
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
        }

        usort($fields, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return array_merge($builtinFields, $fields);
    }

    /**
     * @return array[]
     */
    protected function getBuiltinFields(Element $element, $source): array
    {
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
        ];

        $elementNativeFields = [];
        $elementClass = get_class($element);

        switch ($elementClass) {
            case Entry::class:
                [, $uid] = explode(':', $source);

                $elementNativeFields = [
                    [
                        'name'     => Craft::t('app', 'Post Date'),
                        'handle'   => 'postDate',
                        'dataType' => OmniSearch::DATATYPE_DATE,
                    ],
                    [
                        'name'     => Craft::t('app', 'Author'),
                        'handle'   => 'authorId',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getUsersListData(),
                    ],
                    [
                        'name'     => Craft::t('app', 'Entry Type'),
                        'handle'   => 'typeId',
                        'dataType' => OmniSearch::DATATYPE_LIST,
                        'items'    => $this->getEntryTypesListData($uid),
                    ],
                ];
                break;
            case Category::class:
                // Parent
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
            if ($field->searchable) {
                if ($field instanceof Matrix) {
                    $matrixFields = array_map(function ($item) use ($element, $field) {
                        return $this->createFieldConfig($element, $item, $field->handle . '.');
                    }, $field->getBlockTypeFields());

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
                'users.username AS label',
            ]);

        if (is_array($sources) && count($sources) > 0) {
            $groupIds = array_map(function($source) {
                [, $uid] = explode(':', $source);
                return Craft::$app->userGroups->getGroupByUid($uid)->id;
            }, $sources);

            $query->groupId($groupIds);
        }

        return $query
            ->asArray()
            ->all();
    }

    private function getEntryTypesListData($uid)
    {
        $section = Craft::$app->sections->getSectionByUid($uid);
        $entryTypes = Craft::$app->sections->getEntryTypesBySectionId($section->id);

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
        $sections = [];
        if ($sources === '*') {
            $sections = Craft::$app->volumes->getAllVolumeIds();
        } elseif (is_array($sources) && count($sources) > 0) {
            /** @var Section[] $sections */
            $sections = array_map(function($source) {
                [, $uid] = explode(':', $source);
                return Craft::$app->sections->getSectionByUid($uid);
            }, $sources);
        }

        $sectionIds = array_map(function(Section $section) {
            return $section->id;
        }, $sections);

        return Entry::find()
            ->select([
                'elements.id AS value',
                'title AS label',
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
            $volumeIds = array_map(function($source) {
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
            ])
            ->group($catGroup)
            ->asArray()
            ->all();
    }
}
