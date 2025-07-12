# 🎉 Vietnam Address Converter PHP v1.0.0

**Initial Release** - Convert Vietnamese addresses according to Resolution 202/2025/QH15

## 🚀 Installation

```bash
composer require quangtam/vietnam-address-converter
```

## ✨ Key Features

- 🇻🇳 **Address Conversion** - Convert old 3-level addresses to new 2-level format
- 📝 **Multiple Input Formats** - Support both string and object input
- 🗺️ **Ward Mapping** - Handle merged, renamed, and unchanged wards
- 🏢 **Administrative Data** - Query provinces and wards
- 🔍 **Smart Search** - Find mappings by keyword
- 📦 **Batch Processing** - Convert multiple addresses efficiently
- ⚠️ **Error Handling** - Comprehensive validation and error reporting

## 📊 Data Coverage

- **34 Provinces** with complete administrative data
- **3,321 Wards** in the new structure
- **10,039+ Ward Mappings** for accurate conversion

## 🛠️ Technical

- **PHP 8.0+** with modern type declarations
- **PSR-12** code style compliant
- **14 Unit Tests** with 239 assertions (100% pass)
- **Composer** package with PSR-4 autoloading
- **MIT License**

## 📝 Quick Example

```php
<?php
use Vietnam\AddressConverter\VietnamAddressConverter;

$converter = new VietnamAddressConverter();
$converter->initialize();

$result = $converter->convertAddress('Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh');
echo $result->getConvertedAddress()->getFormattedAddress();
// Output: Phường An Hội Tây, Thành phố Hồ Chí Minh
```

---

**Full documentation**: See [README.md](https://github.com/quangtam/vietnam-address-converter-php/blob/main/README.md)
**Changelog**: See [CHANGELOG.md](https://github.com/quangtam/vietnam-address-converter-php/blob/main/CHANGELOG.md)
