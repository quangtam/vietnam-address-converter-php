<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Models;

/**
 * Represents the result of address conversion
 */
class ConversionResult
{
    public function __construct(
        private readonly bool $success,
        private readonly FullAddress $originalAddress,
        private readonly ?NewAddress $convertedAddress = null,
        private readonly ?MappingInfo $mappingInfo = null,
        private readonly ?string $message = null
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getOriginalAddress(): FullAddress
    {
        return $this->originalAddress;
    }

    public function getConvertedAddress(): ?NewAddress
    {
        return $this->convertedAddress;
    }

    public function getMappingInfo(): ?MappingInfo
    {
        return $this->mappingInfo;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Convert to array
     *
     * @return array Result as array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'originalAddress' => $this->originalAddress->toArray(),
            'convertedAddress' => $this->convertedAddress?->toArray(),
            'mappingInfo' => $this->mappingInfo?->toArray(),
            'message' => $this->message
        ];
    }

    /**
     * Convert to JSON string
     *
     * @return string JSON representation
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
