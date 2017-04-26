<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class MaxFloatRule is used to check whether current float value is not less than max.
 *
 * @package Shulha\Framework\Validation\Rules
 */
class MaxFloatRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $field_name, $field_value, array $params): bool
    {
        return floatval($field_value) <= floatval($params[0]);
    }

    /**
     * @inheritdoc
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name should be lesser than " . $params[0];
    }
}