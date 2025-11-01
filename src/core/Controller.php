<?php
// src/core/Controller.php

class Controller {
    
    /**
     * Fungsi ini memuat view dan layout yang sesuai
     *
     * @param string $view Nama file view (misal: 'pages/admin/dashboard')
     * @param array $data Data yang ingin dikirim ke view
     * @param string $layout Nama layout ('app' untuk internal, 'guest' untuk publik)
     */
    public function view($view, $data = [], $layout = 'app') { // Default ke layout 'app'
        
        // Ekstrak data menjadi variabel
        extract($data);

        // Tentukan path header dan footer berdasarkan layout
        $header_file = '../src/views/layouts/' . $layout . '/header.php';
        $footer_file = '../src/views/layouts/' . $layout . '/footer.php';
        $view_file = '../src/views/' . $view . '.php';

        // Cek apakah semua file ada
        if (file_exists($header_file) && file_exists($footer_file) && file_exists($view_file)) {
            
            // Gabungkan semua bagian
            require_once $header_file;
            require_once $view_file;
            require_once $footer_file;

        } else {
            // Tampilkan error jika ada file yang hilang
            $missing = [];
            if (!file_exists($header_file)) $missing[] = "Header ({$layout})";
            if (!file_exists($footer_file)) $missing[] = "Footer ({$layout})";
            if (!file_exists($view_file)) $missing[] = "View ({$view})";
            die("Error: File tidak ditemukan: " . implode(', ', $missing));
        }
    }
}