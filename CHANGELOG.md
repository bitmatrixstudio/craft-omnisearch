# OmniSearch Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.0.1 - 2023-03-01
- Enh: Added support for CraftCMS 4.0
- Bugfix: Filter doesn't take selected site into account

## 1.2.9 - 2022-06-28
- Bugfix: Fixed a crash when creating an order in CraftCommerce ([GH Issue #29](https://github.com/bitmatrixstudio/craft-omnisearch/issues/29))
- Bugfix: Fixed issue where additional omnisearch queries are performed in the frontend.

## 1.2.8 - 2022-01-12
- Bugfix: Fixed field controller URL wrong when website install in subdirectory ([GH Issue #25](https://github.com/bitmatrixstudio/craft-omnisearch/issues/25))

## 1.2.7 - 2021-12-30
- Bugfix: Related elements of anyStatus are now shown instead of just the active ones ([GH Issue #24](https://github.com/bitmatrixstudio/craft-omnisearch/issues/24))
- Bugfix: Fixed "not present" filter in matrix/supertable fields returning incorrect results ([GH Issue #23](https://github.com/bitmatrixstudio/craft-omnisearch/issues/23))
- Bugfix: Fixed filter by parent error 500 ([GH Issue #22](https://github.com/bitmatrixstudio/craft-omnisearch/issues/22))

## 1.2.6 - 2021-12-09
- Bugfix: Regenerate missing css ([GH Issue #20](https://github.com/bitmatrixstudio/craft-omnisearch/issues/20))

## 1.2.5 - 2021-12-08
- Bugfix: Fixed an issue where custom fields are not translated ([GH Issue #19](https://github.com/bitmatrixstudio/craft-omnisearch/issues/19))

## 1.2.4 - 2021-11-30
- Bugfix: Prevent attaching omnisearch behaviour until all other CraftCMS plugins are loaded ([GH Issue #18](https://github.com/bitmatrixstudio/craft-omnisearch/issues/18))

## 1.2.3 - 2021-11-25
- Bugfix: Fixed a bug where filters are not working on channels in Craft CMS v3.7.22 ([GH Issue #15](https://github.com/bitmatrixstudio/craft-omnisearch/issues/15))
- Bugfix: Duplicate fields are now removed. ([GH Issue #17](https://github.com/bitmatrixstudio/craft-omnisearch/issues/17))

## 1.2.2 - 2021-11-19
- Bugfix: Fixed an error 500 when user's language is null ([GH Issue #13](https://github.com/bitmatrixstudio/craft-omnisearch/issues/13))

## 1.2.1 - 2021-10-29
- Bugfix: Fix bugs with Postgresql.

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
