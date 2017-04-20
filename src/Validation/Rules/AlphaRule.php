<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class AlphaRule is used to validate alphabetic values.
 *
 * @package Shulha\Framework\Validation\Rules
 */
class AlphaRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $field_name, $field_value, array $params): bool
    {
        if (is_string($field_value)) {
            return (preg_match("/^[a-zA-Z]+$/", $field_value));
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name should have alphabetic value";
    }
}