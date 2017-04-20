<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class RequiredRule is used to make sure that value is not blank.
 *
 * @package Shulha\Framework\Validation\Rules
 */
class RequiredRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $field_name, $field_value, array $params): bool
    {
        return !is_null($field_value) && $field_value !== "";
    }

    /**
     * @inheritdoc
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name is required";
    }
}