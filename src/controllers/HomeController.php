<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $data = [
            'title' => 'Docutrack PNJ - Selamat Datang'
        ];

        $this->view('pages/landing', $data, 'guest');
    }
}
