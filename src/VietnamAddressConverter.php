<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter;

use Vietnam\AddressConverter\Models\{ConversionResult, FullAddress, NewAddress, MappingInfo};
use Vietnam\AddressConverter\Parser\AddressParser;
use Vietnam\AddressConverter\Validator\AddressValidator;

/**
 * Main Vietnam Address Converter class
 *
 * Converts Vietnamese administrative addresses according to Resolution 202/2025/QH15
 * Removes district level and maps old wards to new ward structure
 */
class VietnamAddressConverter
{
    private array $provinces = [];
    private array $wards = [];
    private array $wardMappings = [];
    private bool $initialized = false;
    private AddressParser $parser;
    private AddressValidator $validator;

    public function __construct()
    {
        $this->parser = new AddressParser();
        $this->validator = new AddressValidator();
    }

    /**
     * Initialize converter with data from JSON file
     *
     * @param string|null $dataPath Path to address data JSON file
     * @throws \RuntimeException If data file cannot be loaded
     */
    public function initialize(?string $dataPath = null): void
    {
        $dataPath = $dataPath ?? __DIR__ . '/../data/address.json';

        if (!file_exists($dataPath)) {
            throw new \RuntimeException("Address data file not found: {$dataPath}");
        }

        $jsonContent = file_get_contents($dataPath);
        if ($jsonContent === false) {
            throw new \RuntimeException("Cannot read address data file: {$dataPath}");
        }

        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON in address data file: " . json_last_error_msg());
        }

        $this->loadData($data);
        $this->initialized = true;
    }

    /**
     * Convert address from old format to new format
     *
     * @param string|FullAddress $address Address to convert
     * @return ConversionResult Conversion result
     */
    public function convertAddress(string|FullAddress $address): ConversionResult
    {
        $this->ensureInitialized();

        try {
            // Handle empty string input
            if (is_string($address) && trim($address) === '') {
                return new ConversionResult(
                    false,
                    new FullAddress('', '', '', ''),
                    null,
                    null,
                    'Address cannot be empty'
                );
            }

            if (is_string($address)) {
                $parsedAddress = $this->parser->parse($address);
                $originalAddress = new FullAddress(
                    $parsedAddress['ward'] ?? '',
                    $parsedAddress['district'] ?? '',
                    $parsedAddress['province'] ?? '',
                    $parsedAddress['street'] ?? ''
                );
            } else {
                $originalAddress = $address;
            }

            // Find matching ward mapping
            $mapping = $this->findWardMapping($originalAddress);

            if ($mapping !== null) {
                $newAddress = new NewAddress(
                    $mapping['new_ward_name'],
                    $mapping['new_province_name'],
                    $originalAddress->getStreet()
                );

                $mappingInfo = new MappingInfo(
                    $mapping['old_ward_code'],
                    $mapping['new_ward_code'],
                    $this->determineMappingType($mapping)
                );

                return new ConversionResult(
                    true,
                    $originalAddress,
                    $newAddress,
                    $mappingInfo
                );
            }

            // If no mapping found, try to match with existing wards (unchanged case)
            $existingWard = $this->findExistingWard($originalAddress);
            if ($existingWard !== null) {
                $newAddress = new NewAddress(
                    $existingWard['name'],
                    $this->getProvinceNameByCode($existingWard['province_code']),
                    $originalAddress->getStreet()
                );

                $mappingInfo = new MappingInfo(
                    null,
                    $existingWard['ward_code'],
                    'unchanged'
                );

                return new ConversionResult(
                    true,
                    $originalAddress,
                    $newAddress,
                    $mappingInfo
                );
            }

            return new ConversionResult(
                false,
                $originalAddress,
                null,
                null,
                'Address not found in mapping data'
            );
        } catch (\Exception $e) {
            return new ConversionResult(
                false,
                $originalAddress ?? new FullAddress('', '', '', ''),
                null,
                null,
                'Error during conversion: ' . $e->getMessage()
            );
        }
    }

    /**
     * Get data statistics
     *
     * @return array Statistics array
     */
    public function getDataStats(): array
    {
        $this->ensureInitialized();

        return [
            'provinces' => count($this->provinces),
            'wards' => count($this->wards),
            'mappings' => count($this->wardMappings)
        ];
    }

    /**
     * Get all provinces
     *
     * @return array Array of provinces
     */
    public function getProvinces(): array
    {
        $this->ensureInitialized();
        return $this->provinces;
    }

    /**
     * Get wards by province code
     *
     * @param string $provinceCode Province code
     * @return array Array of wards
     */
    public function getWardsByProvince(string $provinceCode): array
    {
        $this->ensureInitialized();

        return array_filter($this->wards, function ($ward) use ($provinceCode) {
            return $ward['province_code'] === $provinceCode;
        });
    }

    /**
     * Search mappings by keyword
     *
     * @param string $keyword Search keyword
     * @return array Array of matching mappings
     */
    public function searchMappings(string $keyword): array
    {
        $this->ensureInitialized();

        $keyword = strtolower($keyword);

        return array_filter($this->wardMappings, function ($mapping) use ($keyword) {
            $oldWardName = $mapping['old_ward_name'] ?? '';
            $oldDistrictName = $mapping['old_district_name'] ?? '';
            $newWardName = $mapping['new_ward_name'] ?? '';

            return (!empty($oldWardName) && stripos($oldWardName, $keyword) !== false) ||
                   (!empty($oldDistrictName) && stripos($oldDistrictName, $keyword) !== false) ||
                   (!empty($newWardName) && stripos($newWardName, $keyword) !== false);
        });
    }

    /**
     * Load data from parsed JSON
     *
     * @param array $data Parsed JSON data
     */
    private function loadData(array $data): void
    {
        foreach ($data as $table) {
            if (!isset($table['type']) || $table['type'] !== 'table') {
                continue;
            }

            switch ($table['name']) {
                case 'provinces':
                    $this->provinces = $table['data'];
                    break;
                case 'wards':
                    $this->wards = $table['data'];
                    break;
                case 'ward_mappings':
                    $this->wardMappings = $table['data'];
                    break;
            }
        }
    }

    /**
     * Find ward mapping for given address
     *
     * @param FullAddress $address Address to find mapping for
     * @return array|null Mapping data or null if not found
     */
    private function findWardMapping(FullAddress $address): ?array
    {
        foreach ($this->wardMappings as $mapping) {
            if ($this->isAddressMatch($address, $mapping)) {
                return $mapping;
            }
        }

        return null;
    }

    /**
     * Check if address matches mapping record
     *
     * @param FullAddress $address Address to check
     * @param array $mapping Mapping record
     * @return bool True if matches
     */
    private function isAddressMatch(FullAddress $address, array $mapping): bool
    {
        return $this->normalizeText($address->getWard()) === $this->normalizeText($mapping['old_ward_name']) &&
               $this->normalizeText($address->getDistrict()) === $this->normalizeText($mapping['old_district_name']) &&
               $this->normalizeText($address->getProvince()) === $this->normalizeText($mapping['old_province_name']);
    }

    /**
     * Find existing ward that hasn't changed
     *
     * @param FullAddress $address Address to find ward for
     * @return array|null Ward data or null if not found
     */
    private function findExistingWard(FullAddress $address): ?array
    {
        foreach ($this->wards as $ward) {
            if ($this->normalizeText($address->getWard()) === $this->normalizeText($ward['name'])) {
                $provinceName = $this->getProvinceNameByCode($ward['province_code']);
                if ($this->normalizeText($address->getProvince()) === $this->normalizeText($provinceName)) {
                    return $ward;
                }
            }
        }

        return null;
    }

    /**
     * Get province name by code
     *
     * @param string $provinceCode Province code
     * @return string Province name
     */
    private function getProvinceNameByCode(string $provinceCode): string
    {
        foreach ($this->provinces as $province) {
            if ($province['province_code'] === $provinceCode) {
                return $province['name'];
            }
        }

        return '';
    }

    /**
     * Determine mapping type based on mapping data
     *
     * @param array $mapping Mapping data
     * @return string Mapping type
     */
    private function determineMappingType(array $mapping): string
    {
        // Check if multiple old wards map to the same new ward (merged)
        $sameNewWardMappings = array_filter($this->wardMappings, function ($m) use ($mapping) {
            return $m['new_ward_code'] === $mapping['new_ward_code'];
        });

        if (count($sameNewWardMappings) > 1) {
            return 'merged';
        }

        // Check if ward name changed (renamed)
        if ($mapping['old_ward_name'] !== $mapping['new_ward_name']) {
            return 'renamed';
        }

        // Check if it's an exact match
        if ($mapping['old_ward_code'] === $mapping['new_ward_code']) {
            return 'exact';
        }

        return 'mapped';
    }

    /**
     * Normalize text for comparison
     *
     * @param string|null $text Text to normalize
     * @return string Normalized text
     */
    private function normalizeText(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        // Remove common prefixes and suffixes
        $normalized = preg_replace('/^(Thành phố|Tỉnh|Quận|Huyện|Phường|Xã|Thị xã|Thị trấn)\s+/u', '', $text);

        // Convert to lowercase for comparison
        return mb_strtolower(trim($normalized), 'UTF-8');
    }

    /**
     * Ensure converter is initialized
     *
     * @throws \RuntimeException If not initialized
     */
    private function ensureInitialized(): void
    {
        if (!$this->initialized) {
            throw new \RuntimeException('Converter not initialized. Call initialize() first.');
        }
    }
}
