<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Vietnam\AddressConverter\VietnamAddressConverter;
use Vietnam\AddressConverter\Models\FullAddress;

echo "=== Vietnam Address Converter Examples ===\n\n";

// Create converter instance
$converter = new VietnamAddressConverter();

try {
    // Initialize with data
    $converter->initialize();
    
    echo "✅ Converter initialized successfully\n";
    
    // Display data statistics
    $stats = $converter->getDataStats();
    echo "📊 Data Statistics:\n";
    echo "   - Provinces: {$stats['provinces']}\n";
    echo "   - Wards: {$stats['wards']}\n";
    echo "   - Mappings: {$stats['mappings']}\n\n";
    
} catch (Exception $e) {
    echo "❌ Initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo str_repeat("-", 60) . "\n\n";

// Example 1: Convert string address (merged case)
echo "1. Converting merged wards example:\n";
$hcmAddress = "Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh";
echo "📍 Input: {$hcmAddress}\n";

try {
    $result = $converter->convertAddress($hcmAddress);
    
    if ($result->isSuccess()) {
        $converted = $result->getConvertedAddress();
        $mapping = $result->getMappingInfo();
        
        echo "✅ Output: {$converted->getFormattedAddress()}\n";
        echo "🔄 Mapping Type: {$mapping->getMappingType()}\n";
        echo "🆔 Ward Codes: {$mapping->getOldWardCode()} → {$mapping->getNewWardCode()}\n";
    } else {
        echo "❌ Conversion failed: {$result->getMessage()}\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// Example 2: Convert using FullAddress object
echo "2. Converting using FullAddress object:\n";
$addressObject = new FullAddress(
    "Phường 14",
    "Quận Gò Vấp", 
    "Thành phố Hồ Chí Minh",
    "123 Nguyễn Văn Cừ"
);

echo "📍 Input: {$addressObject->getFormattedAddress()}\n";

try {
    $result = $converter->convertAddress($addressObject);
    
    if ($result->isSuccess()) {
        $converted = $result->getConvertedAddress();
        $mapping = $result->getMappingInfo();
        
        echo "✅ Output: {$converted->getFormattedAddress()}\n";
        echo "🔄 Mapping Type: {$mapping->getMappingType()}\n";
        
        // Show JSON output
        echo "📄 JSON Result:\n";
        echo $result->toJson() . "\n";
    } else {
        echo "❌ Conversion failed: {$result->getMessage()}\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// Example 3: Batch processing
echo "3. Batch processing multiple addresses:\n";
$addresses = [
    "Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh",
    "Phường 14, Quận Gò Vấp, Thành phố Hồ Chí Minh", 
    "Phường 15, Quận Gò Vấp, Thành phố Hồ Chí Minh",
    "Phường An Lạc, Quận Bình Tân, Thành phố Hồ Chí Minh",
    "Phường An Lạc A, Quận Bình Tân, Thành phố Hồ Chí Minh"
];

foreach ($addresses as $index => $address) {
    echo "📍 Address " . ($index + 1) . ": {$address}\n";
    
    try {
        $result = $converter->convertAddress($address);
        
        if ($result->isSuccess()) {
            $converted = $result->getConvertedAddress();
            $mapping = $result->getMappingInfo();
            
            echo "  ✅ Output: {$converted->getFormattedAddress()}\n";
            echo "  🔄 Type: {$mapping->getMappingType()}\n";
        } else {
            echo "  ❌ Failed: {$result->getMessage()}\n";
        }
    } catch (Exception $e) {
        echo "  ❌ Error: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo str_repeat("-", 60) . "\n\n";

// Example 4: Search mappings
echo "4. Searching mappings by keyword:\n";
$searchKeyword = "Gò Vấp";
echo "🔍 Searching for: {$searchKeyword}\n";

try {
    $mappings = $converter->searchMappings($searchKeyword);
    echo "📋 Found " . count($mappings) . " mappings:\n";
    
    foreach (array_slice($mappings, 0, 5) as $mapping) {
        echo "  • {$mapping['old_ward_name']}, {$mapping['old_district_name']} → {$mapping['new_ward_name']}\n";
    }
    
    if (count($mappings) > 5) {
        echo "  ... and " . (count($mappings) - 5) . " more\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 60) . "\n\n";

// Example 5: Get wards by province
echo "5. Getting wards by province:\n";
$provinceCode = "79"; // Ho Chi Minh City
echo "🏙️ Province code: {$provinceCode} (Ho Chi Minh City)\n";

try {
    $wards = $converter->getWardsByProvince($provinceCode);
    echo "📋 Found " . count($wards) . " wards in the province\n";
    
    foreach (array_slice($wards, 0, 10) as $ward) {
        echo "  • {$ward['name']}\n";
    }
    
    if (count($wards) > 10) {
        echo "  ... and " . (count($wards) - 10) . " more\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== End of Examples ===\n";
