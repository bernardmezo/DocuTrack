<?php
// src/controllers/HomeController.php

require_once '../src/core/Controller.php';

class HomeController extends Controller {
    
    public function index() {
        $data = [
            'title' => 'Docutrack PNJ - Selamat Datang' 
            // Anda bisa tambahkan data lain jika perlu
        ];
        
        // Panggil view 'pages/landing' dan gunakan layout 'guest'
        $this->view('pages/landing', $data, 'guest'); 
    }
}