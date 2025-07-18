# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2025-07-16

### Changed
- 🔄 **Database Integration**: Migrated from local JSON data to `quangtam/vietnam-address-database` package
- 📦 **Improved Performance**: Direct access to optimized database methods
- 🗄️ **Centralized Data**: Shared database across all Vietnam Address Converter implementations
- 📊 **Enhanced Stats**: Added database version information to data statistics

### Added
- 📈 **Database Version Tracking**: Show database version in `getDataStats()`
- 🔧 **Better Error Handling**: Improved error messages for database initialization

### Removed
- 📁 **Local Data Files**: Removed `data/address.json` and `data/` directory
- 🔧 **File Path Parameter**: Simplified `initialize()` method (no longer accepts file path)

### Technical
- **Dependency**: Added `quangtam/vietnam-address-database: ^1.0`
- **Data Source**: Now uses centralized database with 10,977 mapping records
- **API Compatibility**: Maintained backward compatibility for all public methods

## [1.0.0] - 2025-07-12

### Added
- 🎉 **Initial release** of Vietnam Address Converter PHP Library
- 🇻🇳 **Address conversion** according to Resolution 202/2025/QH15
- 📝 **Multiple input formats**: String addresses and `FullAddress` objects
- 🗺️ **Ward mapping** with support for merged, renamed, and unchanged types
- 🏢 **Province and ward lookup** functionality
- 🔍 **Search mappings** by keyword with intelligent filtering
- 📦 **Batch processing** support for multiple addresses
- ⚠️ **Comprehensive error handling** and validation
- 🧪 **Complete test suite** with 14 unit tests and 239 assertions
- 📖 **Full documentation** with examples and usage guides
- 🎯 **PSR-12 code style** compliance
- 📄 **MIT License** for open source usage

### Technical Features
- **PHP 8.0+** requirement with modern type declarations
- **Composer package**: `quangtam/vietnam-address-converter`
- **PSR-4 autoloading** for clean namespace organization
- **PHPUnit testing** with comprehensive coverage
- **PHP CodeSniffer** integration for code quality
- **Address parsing** with intelligent regex patterns
- **UTF-8 support** for Vietnamese characters
- **JSON data source** with 10,039+ ward mappings

### Package Structure
- `VietnamAddressConverter` - Main converter class
- `Models/` - Data models (FullAddress, NewAddress, ConversionResult, MappingInfo)
- `Parser/` - Address parsing logic
- `Validator/` - Input validation
- `examples/` - Usage examples
- `tests/` - Unit test suite
- `data/` - Address mapping data

[1.1.0]: https://github.com/quangtam/vietnam-address-converter-php/releases/tag/v1.1.0
[1.0.0]: https://github.com/quangtam/vietnam-address-converter-php/releases/tag/v1.0.0