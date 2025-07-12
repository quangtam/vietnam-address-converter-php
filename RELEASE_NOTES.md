# 🎉 Vietnam Address Converter PHP Library v1.0.0

**Initial Release** - Convert Vietnamese addresses according to Resolution 202/2025/QH15

## 🚀 Quick Start

```bash
composer require quangtam/vietnam-address-converter
```

```php
<?php
use Vietnam\AddressConverter\VietnamAddressConverter;

$converter = new VietnamAddressConverter();
$converter->initialize();

$result = $converter->convertAddress('Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh');
echo $result->getConvertedAddress()->getFormattedAddress();
// Output: Phường An Hội Tây, Thành phố Hồ Chí Minh
```

## ✨ Key Features

- 🇻🇳 **Address Conversion** - Convert old 3-level addresses to new 2-level format
- 📝 **Multiple Input Formats** - Support both string and object input
- 🗺️ **Ward Mapping** - Handle merged, renamed, and unchanged wards
- 🏢 **Province & Ward Lookup** - Query administrative data
- 🔍 **Smart Search** - Find mappings by keyword
- 📦 **Batch Processing** - Convert multiple addresses efficiently
- ⚠️ **Error Handling** - Comprehensive validation and error reporting

## 📊 Data Coverage

- **34 Provinces** with complete administrative data
- **3,321 Wards** in the new structure  
- **10,039+ Ward Mappings** for accurate conversion
- **UTF-8 Support** for Vietnamese characters

## 🛠️ Technical Highlights

- **PHP 8.0+** with modern type declarations
- **PSR-12** code style compliant
- **14 Unit Tests** with 239 assertions
- **Composer** package with PSR-4 autoloading
- **MIT License** for open source usage

## 📖 Documentation

- **Complete README** with usage examples
- **API Documentation** for all classes and methods
- **Example Scripts** in `/examples` directory
- **Unit Tests** demonstrating all features

## 🔧 Installation Requirements

- PHP >= 8.0
- Composer
- JSON extension (included in PHP by default)

## 📋 What's Included

This release includes:
- Core converter library
- Address parsing and validation
- Data models and result classes
- Comprehensive test suite
- Example usage scripts
- Complete documentation

---

**Full Changelog**: https://github.com/quangtam/vietnam-address-converter-php/blob/main/CHANGELOG.md
