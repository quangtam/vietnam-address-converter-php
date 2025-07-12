<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Models;

/**
 * Represents a new address without district level (new format)
 */
class NewAddress
{
    public function __construct(
        private readonly string $ward,
        private readonly string $province,
        private readonly string $street
    ) {
    }

    public function getWard(): string
    {
        return $this->ward;
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
        $parts = array_filter([$this->street, $this->ward, $this->province]);
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
            'province' => $this->province,
            'street' => $this->street
        ];
    }
}
