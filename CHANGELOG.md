# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2023-03-27
### Removed
- Removed support for Laravel 4.1 - Laravel 8.x. These are all EOL and will never change, so version 1.0.3 will always work for them.
- Removed support for PHP 5.5 - PHP 7.4. These are all EOL and will never change, so version 1.0.3 will always work for them.

### Changed
- Updated package dependencies to support new minimum Laravel and PHP versions.
- Updated CI configs to support new minimum Laravel and PHP versions.
- Updated the README to reflect the new version changes.

## [1.0.3] - 2023-03-24
### Changed
- Converted CI from Travis CI to Github Actions.
- Updated CI config to stop running tests in Scrutinizer.

## [1.0.2] - 2023-03-23
### Changed
- Updated readme to make copying the composer command easier. ([#8](https://github.com/shiftonelabs/laravel-cascade-deletes/pull/8))
- Updated readme with new version information.
- Updated tense in changelog.

## [1.0.1] - 2020-04-02
### Added
- New changelog.

### Changed
- Updated tests to work with all supported Laravel versions.
- Updated CI configs for increased test coverage across versions.
- Small code cleanup items across the code base.
- Updated readme with new version information.
- Sort the packages in composer.json.

### Fixed
- Fix count of soft deleted records to work with changes in Laravel >= 5.5.

## 1.0.0 - 2016-12-08
### Added
- Initial release!

[Unreleased]: https://github.com/shiftonelabs/laravel-cascade-deletes/compare/2.0.0...HEAD
[1.0.3]: https://github.com/shiftonelabs/laravel-cascade-deletes/compare/1.0.3...2.0.0
[1.0.3]: https://github.com/shiftonelabs/laravel-cascade-deletes/compare/1.0.2...1.0.3
[1.0.2]: https://github.com/shiftonelabs/laravel-cascade-deletes/compare/1.0.1...1.0.2
[1.0.1]: https://github.com/shiftonelabs/laravel-cascade-deletes/compare/1.0.0...1.0.1
