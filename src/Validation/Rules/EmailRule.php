<?php

namespace Shulha\Framework\Validation\Rules;

/**
 * Class EmailRule is used to validate email.
 *
 * @package Shulha\Framework\Validation\Rules
 */
class EmailRule extends AbstractValidationRule
{
    /**
     * @inheritdoc
     */
    function check(string $field_name, $field_value, array $params): bool
    {
        return (filter_var($field_value, FILTER_VALIDATE_EMAIL));
    }

    /**
     * @inheritdoc
     */
    public function getError(string $field_name, $field_value, array $params): string
    {
        return "Field $field_name should be e-mail";
    }
}