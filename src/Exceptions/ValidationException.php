<?php

namespace App\Exceptions;

use Exception;

/**
 * ValidationException - Custom exception untuk validation errors
 * 
 * Exception ini digunakan ketika data tidak memenuhi business rules atau validation rules.
 * 
 * @category Exception
 * @package  DocuTrack\Exceptions
 * @version  2.0.0
 */
class ValidationException extends Exception {
    /**
     * @var array Field-specific validation errors
     */
    private $errors = [];

    /**
     * Constructor with optional field errors
     * 
     * @param string $message General error message
     * @param array $errors Field-specific errors [field => error_message]
     * @param int $code Error code
     */
    public function __construct($message = "Validation failed", $errors = [], $code = 422) {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Get field-specific errors
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Check if specific field has error
     * 
     * @param string $field
     * @return bool
     */
    public function hasError($field) {
        return isset($this->errors[$field]);
    }

    /**
     * Get error for specific field
     * 
     * @param string $field
     * @return string|null
     */
    public function getError($field) {
        return $this->errors[$field] ?? null;
    }
}
