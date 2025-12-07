<?php
namespace App\Controllers\Verifikator;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/verifikator/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/verifikator/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'verifikator';
    }
}