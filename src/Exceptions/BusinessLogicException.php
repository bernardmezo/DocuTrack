<?php

namespace App\Exceptions;

use Exception;

/**
 * BusinessLogicException - Custom exception untuk business logic errors
 *
 * Exception ini digunakan ketika terjadi pelanggaran business rules atau
 * logical errors dalam proses bisnis aplikasi.
 *
 * @category Exception
 * @package  DocuTrack\Exceptions
 * @version  2.0.0
 */
class BusinessLogicException extends Exception
{
    /**
     * @var array Additional context data
     */
    private $context = [];

    /**
     * Constructor with optional context data
     *
     * @param string $message Error message
     * @param array $context Additional context information
     * @param int $code Error code
     */
    public function __construct($message = "Business logic error", $context = [], $code = 400)
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    /**
     * Get context data
     *
     * @return array
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Get specific context value
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getContextValue($key, $default = null)
    {
        return $this->context[$key] ?? $default;
    }
}
