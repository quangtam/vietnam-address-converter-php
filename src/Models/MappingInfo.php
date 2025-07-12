<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Models;

/**
 * Represents mapping information for address conversion
 */
class MappingInfo
{
    public function __construct(
        private readonly ?string $oldWardCode,
        private readonly ?string $newWardCode,
        private readonly string $mappingType
    ) {
    }

    public function getOldWardCode(): ?string
    {
        return $this->oldWardCode;
    }

    public function getNewWardCode(): ?string
    {
        return $this->newWardCode;
    }

    public function getMappingType(): string
    {
        return $this->mappingType;
    }

    /**
     * Convert to array
     *
     * @return array Mapping info as array
     */
    public function toArray(): array
    {
        return [
            'oldWardCode' => $this->oldWardCode,
            'newWardCode' => $this->newWardCode,
            'mappingType' => $this->mappingType
        ];
    }
}
