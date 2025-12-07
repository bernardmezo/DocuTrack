<?php
namespace App\Controllers\PPK;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/ppk/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/ppk/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'ppk';
    }
}