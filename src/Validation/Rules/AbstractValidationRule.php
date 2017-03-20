<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class AbstractValidationRule is a superclass for all validation constraints.
 * @package Shulha\Framework\Validation\Rules
 */
abstract class AbstractValidationRule
{
    /**
     * Checks validation rule
     *
     * Format {key}:param1,param2,param3
     *
     * @param string $field_name name of field you want to validate (key)
     * @param object $field_value current value
     * @param array $params parameters of value [param1,param2,param3]
     * @return bool
     */
    abstract function check(string $field_name, $field_value, array $params): bool;

    /**
     * Returns validation error
     *
     * @param string $field_name
     * @param $field_value
     * @param array $params
     * @return string
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name validation error";
    }
}