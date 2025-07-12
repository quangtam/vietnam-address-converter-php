<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Parser;

/**
 * Parses Vietnamese address strings into components
 */
class AddressParser
{
    private array $provincePatterns;
    private array $districtPatterns;
    private array $wardPatterns;

    public function __construct()
    {
        $this->initializePatterns();
    }

    /**
     * Parse a Vietnamese address string into components
     *
     * @param string $address Address string to parse
     * @return array Parsed address components
     */
    public function parse(string $address): array
    {
        if (empty(trim($address))) {
            return [];
        }

        // Clean and normalize the address
        $cleanAddress = $this->cleanAddress($address);

        // Initialize components
        $components = [
            'street' => '',
            'ward' => '',
            'district' => '',
            'province' => '',
            'original' => $address
        ];

        // Parse province first (usually at the end)
        $components['province'] = $this->extractProvince($cleanAddress);
        $remaining = $this->removeProvince($cleanAddress, $components['province']);

        // Parse district (usually second to last)
        $components['district'] = $this->extractDistrict($remaining);
        $remaining = $this->removeDistrict($remaining, $components['district']);

        // Parse ward (usually third to last)
        $components['ward'] = $this->extractWard($remaining);
        $remaining = $this->removeWard($remaining, $components['ward']);

        // Remaining is likely the street address
        $components['street'] = trim($remaining);

        return $components;
    }

    /**
     * Clean and normalize address string
     *
     * @param string $address Raw address string
     * @return string Cleaned address string
     */
    private function cleanAddress(string $address): string
    {
        // Remove extra whitespace
        $cleaned = preg_replace('/\s+/', ' ', trim($address));

        // Normalize common separators
        $cleaned = str_replace([',', ';', '|'], ',', $cleaned);

        // Remove trailing commas and spaces
        $cleaned = rtrim($cleaned, ', ');

        return $cleaned;
    }

    /**
     * Extract province from address string
     *
     * @param string $address Address string
     * @return string Extracted province name
     */
    private function extractProvince(string $address): string
    {
        foreach ($this->provincePatterns as $pattern) {
            if (preg_match($pattern, $address, $matches)) {
                return trim($matches[1]);
            }
        }

        return '';
    }

    /**
     * Extract district from address string
     *
     * @param string $address Address string
     * @return string Extracted district name
     */
    private function extractDistrict(string $address): string
    {
        foreach ($this->districtPatterns as $pattern) {
            if (preg_match($pattern, $address, $matches)) {
                return trim($matches[1]);
            }
        }

        return '';
    }

    /**
     * Extract ward from address string
     *
     * @param string $address Address string
     * @return string Extracted ward name
     */
    private function extractWard(string $address): string
    {
        foreach ($this->wardPatterns as $pattern) {
            if (preg_match($pattern, $address, $matches)) {
                return trim($matches[1]);
            }
        }

        return '';
    }

    /**
     * Remove province from address string
     *
     * @param string $address Address string
     * @param string $province Province to remove
     * @return string Address string with province removed
     */
    private function removeProvince(string $address, string $province): string
    {
        if (empty($province)) {
            return $address;
        }

        foreach ($this->provincePatterns as $pattern) {
            $address = preg_replace($pattern, '', $address);
        }

        return trim($address, ', ');
    }

    /**
     * Remove district from address string
     *
     * @param string $address Address string
     * @param string $district District to remove
     * @return string Address string with district removed
     */
    private function removeDistrict(string $address, string $district): string
    {
        if (empty($district)) {
            return $address;
        }

        foreach ($this->districtPatterns as $pattern) {
            $address = preg_replace($pattern, '', $address);
        }

        return trim($address, ', ');
    }

    /**
     * Remove ward from address string
     *
     * @param string $address Address string
     * @param string $ward Ward to remove
     * @return string Address string with ward removed
     */
    private function removeWard(string $address, string $ward): string
    {
        if (empty($ward)) {
            return $address;
        }

        foreach ($this->wardPatterns as $pattern) {
            $address = preg_replace($pattern, '', $address);
        }

        return trim($address, ', ');
    }

    /**
     * Initialize regex patterns for parsing
     */
    private function initializePatterns(): void
    {
        // Province patterns (case-insensitive, unicode-aware)
        $this->provincePatterns = [
            '/,?\s*(?:Thành phố\s+)?([^,]*(?:Hồ Chí Minh|Ho Chi Minh|HCM))\s*$/iu',
            '/,?\s*(?:Thành phố\s+)?([^,]*(?:Hà Nội|Ha Noi|Hanoi))\s*$/iu',
            '/,?\s*(?:Thành phố\s+)?([^,]*(?:Đà Nẵng|Da Nang|Danang))\s*$/iu',
            '/,?\s*(?:Thành phố\s+)?([^,]*(?:Hải Phòng|Hai Phong|Haiphong))\s*$/iu',
            '/,?\s*(?:Thành phố\s+)?([^,]*(?:Cần Thơ|Can Tho|Cantho))\s*$/iu',
            '/,?\s*(?:Thành phố\s+)?([^,]*(?:Huế|Hue))\s*$/iu',
            '/,?\s*(?:Tỉnh\s+|Thành phố\s+)([^,]+)\s*$/iu',
        ];

        // District patterns (case-insensitive, unicode-aware)
        $this->districtPatterns = [
            '/,?\s*(?:Quận\s+|Q\.?\s*)([^,]+?)(?=\s*,|\s*$)/iu',
            '/,?\s*(?:Huyện\s+|H\.?\s+)([^,]+?)(?=\s*,|\s*$)/iu',
            '/,?\s*(?:Thành phố\s+|TP\.?\s*)([^,]+?)(?=\s*,|\s*$)/iu',
            '/,?\s*(?:Thị xã\s+|TX\.?\s*)([^,]+?)(?=\s*,|\s*$)/iu',
        ];

        // Ward patterns (case-insensitive, unicode-aware)
        $this->wardPatterns = [
            '/,?\s*(?:Phường\s+|P\.?\s*)([^,]+?)(?=\s*,|\s*$)/iu',
            '/,?\s*(?:Xã\s+|X\.?\s*)([^,]+?)(?=\s*,|\s*$)/iu',
            '/,?\s*(?:Thị trấn\s+|TT\.?\s*)([^,]+?)(?=\s*,|\s*$)/iu',
        ];
    }
}
