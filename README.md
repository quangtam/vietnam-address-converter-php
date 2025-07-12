# Vietnam Address Converter PHP

Thư viện PHP để tự động chuyển đổi địa chỉ hành chính Việt Nam từ cũ sang mới theo Nghị quyết số 202/2025/QH15 của Quốc hội.

[![Packagist Version](https://img.shields.io/packagist/v/quangtam/vietnam-address-converter.svg)](https://packagist.org/packages/quangtam/vietnam-address-converter)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.0-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Tests](https://img.shields.io/badge/tests-passing-brightgreen.svg)](tests/)

## 🌐 Multi-Language Support

Vietnam Address Converter hiện có sẵn cho nhiều ngôn ngữ lập trình:

- 🟨 **JavaScript/TypeScript**: [vietnam-address-converter](https://github.com/quangtam/vietnam-address-converter)
- 🟦 **PHP**: [vietnam-address-converter-php](https://github.com/quangtam/vietnam-address-converter-php) - Thư viện PHP với API tương tự (repo này)
- 🔴 **Python**: Coming soon...
- 🟩 **Go**: Coming soon...

💡 **Tất cả implementations đều sử dụng cùng dữ liệu mapping và logic chuyển đổi để đảm bảo tính nhất quán.**

## ✨ Tính năng chính

- 🔄 **Chuyển đổi địa chỉ tự động**: Chuyển đổi địa chỉ cũ sang địa chỉ mới theo quy định mới nhất
- 📊 **Dữ liệu mapping thực tế**: Sử dụng dữ liệu mapping chính thức từ cơ sở dữ liệu hành chính
- 🎯 **Cấu trúc hành chính mới**: Loại bỏ cấp quận/huyện theo Nghị quyết 202/2025/QH15
- 🔍 **Tìm kiếm thông minh**: Tìm kiếm và mapping địa chỉ với độ chính xác cao
- ⚡ **Hiệu suất cao**: Xử lý nhanh với caching tối ưu
- 📱 **Dễ sử dụng**: API đơn giản và dễ tích hợp

## 📈 Thay đổi quan trọng

### Loại bỏ cấp Quận/Huyện

Theo Nghị quyết 202/2025/QH15, cấu trúc hành chính mới không còn cấp quận/huyện:

**Trước (3 cấp):**
```
Tỉnh/Thành phố → Quận/Huyện → Phường/Xã
```

**Sau (2 cấp):**
```
Tỉnh/Thành phố → Phường/Xã
```

**Ví dụ chuyển đổi:**

Input:
```
Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh
```

Output:
```
Phường An Hội Tây, Thành phố Hồ Chí Minh  // Không còn "Quận Gò Vấp"
```

## 🚀 Cài đặt

### Yêu cầu hệ thống
- PHP >= 8.0
- Extension: json

### Cài đặt qua Composer

```bash
composer require quangtam/vietnam-address-converter
```

### Cài đặt thủ công

1. Clone repository:
```bash
git clone https://github.com/quangtam/vietnam-address-converter-php
cd vietnam-address-converter-php
```

2. Cài đặt dependencies:
```bash
composer install
```

## 🔧 Sử dụng cơ bản

### 1. Khởi tạo Converter

```php
<?php

require_once 'vendor/autoload.php';

use Vietnam\AddressConverter\VietnamAddressConverter;

// Khởi tạo converter
$converter = new VietnamAddressConverter();

// Tải dữ liệu từ file JSON
$converter->initialize(); // Sử dụng file data/address.json mặc định

// Hoặc sử dụng file dữ liệu tùy chỉnh
$converter->initialize('/path/to/custom/address.json');
```

### 2. Chuyển đổi địa chỉ từ string

```php
// Chuyển đổi địa chỉ từ string
$result = $converter->convertAddress('Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh');

if ($result->isSuccess()) {
    $converted = $result->getConvertedAddress();
    $mapping = $result->getMappingInfo();
    
    echo "Địa chỉ cũ: " . $result->getOriginalAddress()->getFormattedAddress() . "\n";
    echo "Địa chỉ mới: " . $converted->getFormattedAddress() . "\n";
    echo "Loại chuyển đổi: " . $mapping->getMappingType() . "\n";
} else {
    echo "Lỗi: " . $result->getMessage() . "\n";
}
```

### 3. Chuyển đổi từ object

```php
use Vietnam\AddressConverter\Models\FullAddress;

// Địa chỉ cũ (có Quận/Huyện)
$addressObject = new FullAddress(
    'Phường 12',        // ward
    'Quận Gò Vấp',     // district  
    'Thành phố Hồ Chí Minh', // province
    '123 Nguyễn Văn Cừ'     // street
);

$result = $converter->convertAddress($addressObject);

if ($result->isSuccess()) {
    $converted = $result->getConvertedAddress();
    // Kết quả: Phường An Hội Tây, Thành phố Hồ Chí Minh (không có district)
}
```

### 4. Xuất kết quả dưới dạng JSON

```php
$result = $converter->convertAddress('Phường 12, Quận Gò Vấp, TP.HCM');

// Xuất JSON
echo $result->toJson();

// Xuất array
$array = $result->toArray();
print_r($array);
```

## 🔄 Các loại chuyển đổi

### 1. Merged (Gộp)
Nhiều phường/xã cũ được gộp thành một phường/xã mới:

```php
// Phường 12 và Phường 14 → Phường An Hội Tây
$converter->convertAddress('Phường 12, Quận Gò Vấp, TP.HCM');
// mappingType: 'merged'
```

### 2. Renamed (Đổi tên)
Phường/xã giữ nguyên ranh giới nhưng đổi tên:

```php
$converter->convertAddress('Phường cũ, Quận ABC, Tỉnh XYZ');
// mappingType: 'renamed'
```

### 3. Unchanged (Không đổi)
Phường/xã không có thay đổi:

```php
$converter->convertAddress('Phường An Lạc, Quận Bình Tân, TP.HCM');
// mappingType: 'unchanged'
```

## 📊 API Reference

### VietnamAddressConverter

#### `initialize(?string $dataPath = null): void`
Khởi tạo converter với dữ liệu từ file JSON.

**Tham số:**
- `$dataPath`: Đường dẫn tới file dữ liệu JSON (tùy chọn)

#### `convertAddress(string|FullAddress $address): ConversionResult`
Chuyển đổi địa chỉ từ định dạng cũ sang mới.

**Tham số:**
- `$address`: Địa chỉ cần chuyển đổi (string hoặc FullAddress object)

**Kết quả trả về:**
- `ConversionResult`: Kết quả chuyển đổi

#### `getDataStats(): array`
Lấy thống kê dữ liệu.

#### `getProvinces(): array`
Lấy danh sách tất cả tỉnh/thành phố.

#### `getWardsByProvince(string $provinceCode): array`
Lấy danh sách phường/xã theo mã tỉnh.

#### `searchMappings(string $keyword): array`
Tìm kiếm mapping theo từ khóa.

### Models

#### ConversionResult
```php
interface ConversionResult {
    public function isSuccess(): bool;
    public function getOriginalAddress(): FullAddress;
    public function getConvertedAddress(): ?NewAddress;
    public function getMappingInfo(): ?MappingInfo;
    public function getMessage(): ?string;
    public function toArray(): array;
    public function toJson(): string;
}
```

#### FullAddress (Địa chỉ cũ có district)
```php
interface FullAddress {
    public function getWard(): string;
    public function getDistrict(): string;
    public function getProvince(): string;
    public function getStreet(): string;
    public function getFormattedAddress(): string;
    public function toArray(): array;
}
```

#### NewAddress (Địa chỉ mới không có district)
```php
interface NewAddress {
    public function getWard(): string;
    public function getProvince(): string;
    public function getStreet(): string;
    public function getFormattedAddress(): string;
    public function toArray(): array;
}
```

## 💻 Ví dụ hoàn chỉnh

```php
<?php

require_once 'vendor/autoload.php';

use Vietnam\AddressConverter\VietnamAddressConverter;

async function demo() {
    $converter = new VietnamAddressConverter();
    $converter->initialize();
    
    $testAddresses = [
        'Phường 12, Quận Gò Vấp, TP.HCM',
        'Phường 14, Quận Gò Vấp, TP.HCM',
        'Phường An Lạc, Quận Bình Tân, TP.HCM'
    ];
    
    foreach ($testAddresses as $address) {
        $result = $converter->convertAddress($address);
        
        if ($result->isSuccess()) {
            $converted = $result->getConvertedAddress();
            $mapping = $result->getMappingInfo();
            
            echo "Input:  {$address}\n";
            echo "Output: {$converted->getFormattedAddress()}\n";
            echo "Type:   {$mapping->getMappingType()}\n\n";
        } else {
            echo "Error:  {$result->getMessage()}\n\n";
        }
    }
}

demo();
```

## 📊 Dữ liệu

Thư viện bao gồm:

- **34 tỉnh/thành phố** theo cấu trúc hành chính mới
- **3,300+ phường/xã** đã được cập nhật  
- **10,000+ mapping records** cho việc chuyển đổi

Dữ liệu được cập nhật theo Nghị quyết số 202/2025/QH15 của Quốc hội về việc sắp xếp đơn vị hành chính.

## 🛠️ Phát triển

### Chạy tests

```bash
composer test
```

### Kiểm tra code style

```bash
composer cs-check
```

### Sửa code style

```bash
composer cs-fix
```

### Chạy ví dụ

```bash
php examples/basic_usage.php
```

## 🤝 Đóng góp

Chúng tôi hoan nghênh mọi đóng góp! Vui lòng:

1. Fork dự án
2. Tạo feature branch (`git checkout -b feature/amazing-feature`)
3. Commit thay đổi (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Mở Pull Request

## 📄 License

[MIT License](LICENSE)

## 📞 Liên hệ

- Issues: [GitHub Issues](https://github.com/quangtam/vietnam-address-converter-php/issues)
- Email: quangtamvu@gmail.com

## 🙏 Cảm ơn

- Dữ liệu từ [thanhtrungit97/dvhcvn](https://github.com/thanhtrungit97/dvhcvn)
- Nghị quyết số 202/2025/QH15 của Quốc hội
- Tham khảo từ [quangtam/vietnam-address-converter](https://github.com/quangtam/vietnam-address-converter)

---

Made with ❤️ for Vietnam developers
