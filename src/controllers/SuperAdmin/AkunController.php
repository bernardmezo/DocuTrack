<?php
namespace App\Controllers\SuperAdmin;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/super_admin/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/super_admin/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'super_admin';
    }
}
