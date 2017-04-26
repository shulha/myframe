<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class NumericRule is used to validate numeric values.
 *
 * @package Shulha\Framework\Validation\Rules
 */
class NumericRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $field_name, $field_value, array $params): bool
    {
        return is_numeric($field_value);
    }

    /**
     * @inheritdoc
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name should be numeric";
    }
}