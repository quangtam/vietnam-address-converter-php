<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Models;

/**
 * Represents a full address with district (old format)
 */
class FullAddress
{
    public function __construct(
        private readonly string $ward,
        private readonly string $district,
        private readonly string $province,
        private readonly string $street
    ) {
    }

    public function getWard(): string
    {
        return $this->ward;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getProvince(): string
    {
        return $this->province;
    }

    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * Get formatted address string
     *
     * @return string Formatted address
     */
    public function getFormattedAddress(): string
    {
        $parts = array_filter([$this->street, $this->ward, $this->district, $this->province]);
        return implode(', ', $parts);
    }

    /**
     * Convert to array
     *
     * @return array Address as array
     */
    public function toArray(): array
    {
        return [
            'ward' => $this->ward,
            'district' => $this->district,
            'province' => $this->province,
            'street' => $this->street
        ];
    }
}
