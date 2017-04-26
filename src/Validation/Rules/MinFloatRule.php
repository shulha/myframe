<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class MinFloatRule is used to check whether current float value is no more than min.
 *
 * @package Shulha\Framework\Validation\Rules
 */
class MinFloatRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $field_name, $field_value, array $params): bool
    {
        return floatval($field_value) >= floatval($params[0]);
    }

    /**
     * @inheritdoc
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name should be greater than " . $params[0];
    }
}