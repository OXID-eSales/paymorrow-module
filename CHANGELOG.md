# Change Log for OXID Paymorrow Payments Module

All notable changes to this project will be documented in this file.
The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [2.1.0] - 2021-06-07

### Changed
- Move database modifications from onActivate event to migration

## [2.0.4] - 2021-04-13

### Removed
- Removed WaitTimeMultiplier in tests

## [2.0.3] - 2020-03-13

### Removed
- Removed WaitTimeMultiplier in tests

## [2.0.2] - 2019-07-22

### Added

### Changed
 - Acceptance tests adjusted to run on OXID Testing Library v3.0+
 - LICENSE file with GNU GENERAL PUBLIC LICENSE Version 3 
 - Ensure compatibility to Order::validatePayment()

## [2.0.1] - 2018-07-12

### Changed
 - Improved payment data workflows in checkout process

## 2.0.0 - 2017-07-14

### Added
 - Compatibility with OXID eShop v6.x
 
### Changed
 - Acceptance and unit tests adjusted to run on OXID Testing Library v2.1.0+
 - All files encoding changed to UTF-8
 
### Fixed
 - Payment ID handling and Wrapping Cost VAT fixes for the eShop v6

## 1.0.1

### Added
 - User manuals extended for information on module integration in highly customized shops

### Changed
 - Paymorrow gateway updated to version "patch_20150129"
 
## 1.0.0
Initial release.

[2.1.0]: https://github.com/OXID-eSales/paymorrow-module/compare/v2.0.4...v2.1.0
[2.0.4]: https://github.com/OXID-eSales/paymorrow-module/compare/v2.0.3...v2.0.4
[2.0.3]: https://github.com/OXID-eSales/paymorrow-module/compare/v2.0.2...v2.0.3
[2.0.2]: https://github.com/OXID-eSales/paymorrow-module/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/OXID-eSales/paymorrow-module/compare/v2.0.0...v2.0.1
