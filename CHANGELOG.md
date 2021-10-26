# OmniSearch Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.2.0 - 2021-10-26
- Ehn: Craft Commerce Support.

## 1.1.1 - 2021-10-22
- New Feature: Added ability to filter by parents and ancestors ([GH Issue #3](https://github.com/bitmatrixstudio/craft-omnisearch/issues/3))

## 1.1.0 - 2021-10-21
- New Feature: Added omnisearch filtering to the 'entries' selection modal ([GH Issue #10](https://github.com/bitmatrixstudio/craft-omnisearch/issues/10))

## 1.0.9 - 2021-10-21
- Bugfix: Fixed a bug retrieving field list when related entry's source is "All entries" ([GH Issue #11](https://github.com/bitmatrixstudio/craft-omnisearch/issues/11)).
- Ehn: German translation added ([GH Issue #7](https://github.com/bitmatrixstudio/craft-omnisearch/issues/7)). Kudos to [@outline4](https://github.com/outline4) for helping with the translation

## 1.0.8 - 2021-10-16
- Enh: Added i18n support ([GH Issue #7](https://github.com/bitmatrixstudio/craft-omnisearch/issues/7))
- Enh: Reordering will now be disabled when omnisearch filters are active so as to prevent messing up of the structure's orders ([GH Issue #8](https://github.com/bitmatrixstudio/craft-omnisearch/issues/8))

## 1.0.7 - 2021-10-15
- Bug fix: fixed an issue where omnisearch is appearing in the field layout page.
- Bug fix: initial filters are now shown only after fields are fetched.
- Bug fix: list of users will now show the username instead if user does not have first or last name.
- Enh: Added ability to filter Assets by uploader ([GH Issue #9](https://github.com/bitmatrixstudio/craft-omnisearch/issues/9))

## 1.0.6 - 2021-10-08
### Changes
- Enh: Added Super Table support. Note: Nested matrix not yet supported.

## 1.0.5 - 2021-10-08
### Changes
- Bug fix: Incorrect records returned for 'is not present' filter for related category fields.

## 1.0.4 - 2021-10-08
### Changes
- Enh: Added support for CraftCMS 3.7.8 columnPrefixes. 

## 1.0.3 - 2021-10-07
### Changes
- Enh: When filtering by related user, the list data now shows the full name of the user instead of the username ([GH Issue #2](https://github.com/bitmatrixstudio/craft-omnisearch/issues/2)) 
- Enh: Added ability to search across multiple sites ([GH Issue #5](https://github.com/bitmatrixstudio/craft-omnisearch/issues/5)) 
- Bug fix: Fields not showing when 'All entries' is selected as source.
- Bug fix: Field list no longer shows duplicated fields in 'all entries'.

## 1.0.2 - 2021-10-06
### Added
- Basic Craft Commerce Support: Product search

## 1.0.1 - 2021-09-30
### Added
- Initial release
