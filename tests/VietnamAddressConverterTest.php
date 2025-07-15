<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Tests;

use PHPUnit\Framework\TestCase;
use Vietnam\AddressConverter\VietnamAddressConverter;
use Vietnam\AddressConverter\Models\{FullAddress, ConversionResult};

class VietnamAddressConverterTest extends TestCase
{
    private VietnamAddressConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new VietnamAddressConverter();
        $this->converter->initialize();
    }

    public function testInitialization(): void
    {
        $stats = $this->converter->getDataStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('provinces', $stats);
        $this->assertArrayHasKey('wards', $stats);
        $this->assertArrayHasKey('mappings', $stats);
        $this->assertArrayHasKey('version', $stats);
        $this->assertGreaterThan(0, $stats['provinces']);
        $this->assertGreaterThan(0, $stats['wards']);
        $this->assertGreaterThan(0, $stats['mappings']);
        $this->assertIsString($stats['version']);
    }

    public function testConvertStringAddressMerged(): void
    {
        // Test case where multiple wards are merged into one
        $address = "Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh";
        $result = $this->converter->convertAddress($address);

        $this->assertInstanceOf(ConversionResult::class, $result);
        $this->assertTrue($result->isSuccess());

        $converted = $result->getConvertedAddress();
        $this->assertNotNull($converted);
        $wardName = $converted->getWard();
        $this->assertGreaterThan(10, strlen($wardName)); // Ward name should be reasonably long
        $this->assertStringContainsString("An", $wardName); // Should contain "An"
        $this->assertEquals("Thành phố Hồ Chí Minh", $converted->getProvince());

        $mapping = $result->getMappingInfo();
        $this->assertNotNull($mapping);
        $this->assertEquals("merged", $mapping->getMappingType());
    }

    public function testConvertStringAddressMergedSecond(): void
    {
        // Test another ward that merges to the same result
        $address = "Phường 14, Quận Gò Vấp, Thành phố Hồ Chí Minh";
        $result = $this->converter->convertAddress($address);

        $this->assertTrue($result->isSuccess());

        $converted = $result->getConvertedAddress();
        $this->assertStringContainsString("An", $converted->getWard());
        $this->assertEquals("merged", $result->getMappingInfo()->getMappingType());
    }

    public function testConvertFullAddressObject(): void
    {
        $fullAddress = new FullAddress(
            "Phường 15",
            "Quận Gò Vấp",
            "Thành phố Hồ Chí Minh",
            "123 Nguyễn Văn Cừ"
        );

        $result = $this->converter->convertAddress($fullAddress);

        $this->assertTrue($result->isSuccess());

        $converted = $result->getConvertedAddress();
        $this->assertEquals("Phường An Hội Đông", $converted->getWard());
        $this->assertEquals("123 Nguyễn Văn Cừ", $converted->getStreet());
    }

    public function testConvertAddressNotFound(): void
    {
        $address = "Phường Không Tồn Tại, Quận Không Có, Thành phố Hư Cấu";
        $result = $this->converter->convertAddress($address);

        $this->assertFalse($result->isSuccess());
        $this->assertNotNull($result->getMessage());
        $this->assertNull($result->getConvertedAddress());
    }

    public function testGetProvinces(): void
    {
        $provinces = $this->converter->getProvinces();

        $this->assertIsArray($provinces);
        $this->assertNotEmpty($provinces);

        // Check structure of province data
        $firstProvince = reset($provinces);
        $this->assertArrayHasKey('id', $firstProvince);
        $this->assertArrayHasKey('name', $firstProvince);
        $this->assertArrayHasKey('province_code', $firstProvince);
    }

    public function testGetWardsByProvince(): void
    {
        // Test with Ho Chi Minh City province code
        $wards = $this->converter->getWardsByProvince('79');

        $this->assertIsArray($wards);
        $this->assertNotEmpty($wards);

        // Check that all wards belong to the correct province
        foreach ($wards as $ward) {
            $this->assertEquals('79', $ward['province_code']);
        }
    }

    public function testSearchMappings(): void
    {
        $mappings = $this->converter->searchMappings('Gò Vấp');

        $this->assertIsArray($mappings);
        $this->assertNotEmpty($mappings);

        // Check that search results contain the keyword
        foreach ($mappings as $mapping) {
            $found = stripos($mapping['old_ward_name'], 'Gò Vấp') !== false ||
                    stripos($mapping['old_district_name'], 'Gò Vấp') !== false ||
                    stripos($mapping['new_ward_name'], 'Gò Vấp') !== false;
            $this->assertTrue($found);
        }
    }

    public function testConversionResultToJson(): void
    {
        $address = "Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh";
        $result = $this->converter->convertAddress($address);

        $json = $result->toJson();
        $this->assertJson($json);

        $decoded = json_decode($json, true);
        $this->assertArrayHasKey('success', $decoded);
        $this->assertArrayHasKey('originalAddress', $decoded);
        $this->assertArrayHasKey('convertedAddress', $decoded);
        $this->assertArrayHasKey('mappingInfo', $decoded);
        $this->assertTrue($decoded['success']);
    }

    public function testConversionResultToArray(): void
    {
        $address = "Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh";
        $result = $this->converter->convertAddress($address);

        $array = $result->toArray();
        $this->assertIsArray($array);
        $this->assertArrayHasKey('success', $array);
        $this->assertArrayHasKey('originalAddress', $array);
        $this->assertArrayHasKey('convertedAddress', $array);
        $this->assertArrayHasKey('mappingInfo', $array);
    }

    public function testBatchProcessing(): void
    {
        $addresses = [
            "Phường 12, Quận Gò Vấp, Thành phố Hồ Chí Minh",
            "Phường 14, Quận Gò Vấp, Thành phố Hồ Chí Minh",
            "Phường 15, Quận Gò Vấp, Thành phố Hồ Chí Minh"
        ];

        $results = [];
        foreach ($addresses as $address) {
            $results[] = $this->converter->convertAddress($address);
        }

        $this->assertCount(3, $results);

        // All should be successful
        foreach ($results as $result) {
            $this->assertTrue($result->isSuccess());
            $this->assertNotNull($result->getConvertedAddress());
        }

        // First two should map to the same new ward (merged)
        $this->assertEquals(
            $results[0]->getConvertedAddress()->getWard(),
            $results[1]->getConvertedAddress()->getWard()
        );

        // Third should map to different ward
        $this->assertNotEquals(
            $results[0]->getConvertedAddress()->getWard(),
            $results[2]->getConvertedAddress()->getWard()
        );
    }

    public function testDatabaseIntegration(): void
    {
        // Test to ensure database integration works properly
        $converter = new VietnamAddressConverter();
        
        // This should not throw an exception with the new database
        $converter->initialize();
        
        $stats = $converter->getDataStats();
        $this->assertArrayHasKey('version', $stats);
        $this->assertIsString($stats['version']);
    }

    public function testUninitializedConverter(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Converter not initialized');

        $converter = new VietnamAddressConverter();
        $converter->convertAddress('test');
    }

    public function testEmptyStringAddress(): void
    {
        $result = $this->converter->convertAddress('');

        $this->assertFalse($result->isSuccess());
        $this->assertNotNull($result->getMessage());
    }
}
