<?php

declare(strict_types=1);

namespace Vietnam\AddressConverter\Validator;

/**
 * Validates Vietnamese address strings and components
 */
class AddressValidator
{
    /**
     * Validate if an address string is in valid format
     *
     * @param string $address Address string to validate
     * @return bool True if valid, false otherwise
     */
    public function isValid(string $address): bool
    {
        if (empty(trim($address))) {
            return false;
        }

        // Basic validation - should have some content and reasonable length
        $trimmed = trim($address);

        if (strlen($trimmed) < 3 || strlen($trimmed) > 500) {
            return false;
        }

        // Check for valid characters (letters, numbers, spaces, common punctuation, Vietnamese characters)
        if (!preg_match('/^[a-zA-ZÀ-ỹ0-9\s\.\-\/,]+$/u', $trimmed)) {
            return false;
        }

        return true;
    }

    /**
     * Validate address components
     *
     * @param array $components Address components array
     * @return bool True if components are valid, false otherwise
     */
    public function validateComponents(array $components): bool
    {
        // At minimum, we should have either a ward or province
        if (empty($components['ward']) && empty($components['province'])) {
            return false;
        }

        // Validate individual components
        foreach (['ward', 'district', 'province', 'street'] as $component) {
            if (!empty($components[$component]) && !$this->isValidComponent($components[$component])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate individual address component
     *
     * @param string $component Component value to validate
     * @return bool True if valid, false otherwise
     */
    private function isValidComponent(string $component): bool
    {
        $trimmed = trim($component);

        if (empty($trimmed) || strlen($trimmed) > 200) {
            return false;
        }

        // Check for valid characters
        return preg_match('/^[a-zA-ZÀ-ỹ0-9\s\.\-\/]+$/u', $trimmed);
    }
}
