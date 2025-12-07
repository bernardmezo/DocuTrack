<?php

namespace App\Controllers\Direktur;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/direktur/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/direktur/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'direktur';
    }
}
