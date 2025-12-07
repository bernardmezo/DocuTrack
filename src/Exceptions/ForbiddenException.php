<?php

namespace App\Exceptions;

class ForbiddenException extends \Exception {
    protected $message = 'Anda tidak memiliki izin untuk mengakses halaman ini.';
    protected $code = 403;
}
