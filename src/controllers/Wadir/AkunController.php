<?php

namespace App\Controllers\Wadir;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/wadir/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/wadir/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'wadir';
    }
}
