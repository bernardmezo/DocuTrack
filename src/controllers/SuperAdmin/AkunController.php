<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/superadmin/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/superadmin/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'superadmin';
    }
}
