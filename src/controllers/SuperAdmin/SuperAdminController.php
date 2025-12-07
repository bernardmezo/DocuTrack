<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;

class SuperAdminController extends Controller
{
    public function index()
    {
        // Redirect to the dashboard as a default action for the SuperAdmin base route
        $this->redirect('/super_admin/dashboard');
    }
}
