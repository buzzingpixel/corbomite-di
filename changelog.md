# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.2.0] - 2019-03-30
### Deprecated
- Deprecated static `get` method (use the PHP-DI container)
- Deprecated `getFromDefinition` method (use the PHP-DI container)
- Deprecated static `make` method (use the PHP-DI container)
- Deprecated `makeFromDefinition` method (use the PHP-DI container)
- Deprecated static `has` method (use the PHP-DI container)
- Deprecated `hasDefinition` method (use the PHP-DI container)

### Added
- Add new configuration options for the container to build with compiling and proxies enabled.
- Added all applicable configuration options as settings in the `extra` object of a project's `composer.json` file.

## [1.1.0] - 2019-02-24
### Added
- Added ability to enable auto wiring with config
- Added ability to enable enable annotations with config
- Added ability to build container directly and directly send an array config of definitions to be additionally built in

## [1.0.1] - 2019-01-11
### Changed
- Internal update to use the Corbomite Config Collector package

## [1.0.0] - 2018-12-29
### New
- Initial Release
