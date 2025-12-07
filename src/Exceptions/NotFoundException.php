<?php

namespace App\Exceptions;

class NotFoundException extends \Exception {
    protected $message = 'Halaman yang Anda cari tidak ditemukan.';
    protected $code = 404;
}
