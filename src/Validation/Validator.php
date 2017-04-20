<?php

namespace Shulha\Framework\Validation;

use Shulha\Framework\Validation\Exception\RuleClassNotFoundException;
use Shulha\Framework\Validation\Exception\RuleNotFoundException;
use Shulha\Framework\Validation\Rules\AbstractValidationRule;

/**
 * Class Validator
 * @package Shulha\Framework\Validation
 */
class Validator
{
    /**
     * @var object request
     */
    private $request;

    /**
     * @var array $rules
     */
    private $rules = [];

    /**
     * @var array $_errorList Validation errors
     */
    private $errorList = [];

    /**
     * Path to directory with rules
     */
    const DIR_RULE = 'Shulha\Framework\Validation\Rules\\';

    private static $known_rules = [
        'alpha' => self::DIR_RULE . 'AlphaRule',
        'length_between' => self::DIR_RULE . 'BetweenLengthRule',
        'email' => self::DIR_RULE . 'EmailRule',
        'max' => self::DIR_RULE . 'MaxFloatRule',
        'min' => self::DIR_RULE . 'MinFloatRule',
        'numeric' => self::DIR_RULE . 'NumericRule',
        'required' => self::DIR_RULE . 'RequiredRule'
    ];

    /**
     * Validator constructor.
     *
     * Example:
     *
     * $validator = new Validator($request, [
     *  "field_name" => [field_rule:field_params [, field_rule:field_params]]
     *  "title" => ["required", "length_between:3,100"],
     *  "price" => ["required", "numeric", "min:0"]
     * ]);
     *
     * @param $request
     * @param array $rules
     */
    public function __construct($request, array $rules)
    {
        $this->request = $request;
        $this->resetErrorList();

        foreach ($rules as $field_name => $field_rules) {
            foreach ($field_rules as $field_rule) {

                $field_params = explode(":", $field_rule);
                $rule_key = $this->cleanStr($field_params[0]);

                if (isset($field_params[1])) {
                    $rule_params = explode(",", $field_params[1]);
                    $rule_params = array_map(array($this, 'cleanStr'), $rule_params);

                    if (!empty($this->rules[$field_name]) and key_exists($rule_key, $this->rules[$field_name]))
                        die("Field rule \"$rule_key\" already exists");

                    $this->rules[$field_name][$rule_key] = $rule_params;

                } else {
                    if (!empty($this->rules[$field_name]) and key_exists($rule_key, $this->rules[$field_name]))
                        die("Field rule \"$rule_key\" already exists");

                    $this->rules[$field_name][$rule_key] = [];
                }
            }
        }
    }

    /**
     * Validates specified object by rules
     *
     * @return bool
     * @throws RuleClassNotFoundException
     * @throws RuleNotFoundException
     */
    public function validate(): bool
    {
        $result = true;
        foreach ($this->rules as $field_name => $field_rules) {
            foreach ($field_rules as $field_rule => $field_params) {

                if (array_key_exists($field_rule, self::$known_rules)) {

                    if (class_exists(self::$known_rules[$field_rule]) and
                        is_subclass_of(self::$known_rules[$field_rule], 'Shulha\Framework\Validation\Rules\AbstractValidationRule')) {

                        /** @var $validation_class AbstractValidationRule */
                        $validation_class = new self::$known_rules[$field_rule];
                        $field_value = isset($this->request->$field_name) ? $this->request->$field_name : null;

                        if (!$validation_class->check($field_name, $field_value, $field_params)) {
                            $result = false;
                            $this->errorList[$field_name][] = $validation_class->getError($field_name, $field_value, $field_params);
                        }
                    } else
                        throw new RuleClassNotFoundException("Class " . self::$known_rules[$field_rule] . " not found or don't extends AbstractValidationRule");
                } else
                    throw new RuleNotFoundException("Rule \"$field_rule\" not found");
            }
        }
        return $result;

    }

    /**
     * Get all validation errors.
     *
     * @return array Validation errors.
     */
    public function getErrorList(): array
    {
        return $this->errorList;
    }

    /**
     * Reset error list
     */
    public function resetErrorList()
    {
        $this->errorList = array();
    }

    /**
     * Adds new validation rules
     *
     * @param string $key
     * @param string $class_namespace
     * @return bool
     */
    public static function addValidationRule(string $key, string $class_namespace): bool
    {
        if (class_exists($class_namespace)) {
            self::$known_rules[$key] = $class_namespace;
            return true;
        }

        return false;
    }

    /**
     * Strip whitespace and tags from a string
     *
     * @param $data
     * @return string
     */
    private function cleanStr($data)
    {
        return trim(strip_tags($data));
    }
}
