<?php

namespace App\Exceptions;

use Exception;

class MethodNotAllowedException extends Exception
{
    protected $code = 405;
    protected $message = 'Method Not Allowed';
}
