<?php
namespace App\Controllers\Admin;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/admin/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/admin/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'app';
    }
}
