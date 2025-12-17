<?php

namespace App\Exceptions;

use Exception;

/**
 * UploadException - Custom exception untuk file upload errors
 *
 * Exception ini digunakan ketika file upload gagal karena validation
 * atau error lainnya saat proses upload.
 *
 * @category Exception
 * @package  DocuTrack\Exceptions
 * @version  1.0.0
 */
class UploadException extends Exception
{
    /**
     * @var array Field-specific upload errors
     */
    private $errors = [];

    /**
     * Constructor with optional field errors
     *
     * @param string $message General error message
     * @param array $errors Field-specific errors [field => error_message]
     * @param int $code Error code
     */
    public function __construct($message = "Upload failed", $errors = [], $code = 400)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Get field-specific errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Check if specific field has error
     *
     * @param string $field
     * @return bool
     */
    public function hasError($field)
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get error for specific field
     *
     * @param string $field
     * @return string|null
     */
    public function getError($field)
    {
        return $this->errors[$field] ?? null;
    }

    /**
     * Add error for specific field
     *
     * @param string $field
     * @param string $error
     * @return void
     */
    public function addError($field, $error)
    {
        $this->errors[$field] = $error;
    }

    /**
     * Check if has any errors
     *
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->errors);
    }
}
