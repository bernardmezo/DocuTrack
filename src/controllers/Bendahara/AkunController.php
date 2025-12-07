<?php
namespace App\Controllers\Bendahara;

use App\Controllers\Base\BaseAkunController;

class AkunController extends BaseAkunController
{
    protected function getAkunViewPath(): string
    {
        return 'pages/bendahara/akun';
    }

    protected function getAkunRedirectUrl(): string
    {
        return '/docutrack/public/bendahara/akun';
    }

    protected function getAkunLayout(): string
    {
        return 'bendahara';
    }
}
