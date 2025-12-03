<?php
// src/core/Controller.php

class Controller {
    
    /**
     * Safe wrapper untuk memanggil Model method dengan error handling
     * 
     * @param object $model Instance dari model
     * @param string $method Nama method yang akan dipanggil
     * @param array $params Parameter untuk method
     * @param mixed $defaultReturn Nilai default jika error (default: [])
     * @return mixed Hasil dari method atau $defaultReturn jika error
     */
    protected function safeModelCall($model, string $method, array $params = [], $defaultReturn = []) {
        try {
            if (!method_exists($model, $method)) {
                error_log("Method {$method} tidak ditemukan di " . get_class($model));
                return $defaultReturn;
            }
            
            $result = call_user_func_array([$model, $method], $params);
            
            // Pastikan return array jika expected array
            if (is_array($defaultReturn) && $result === null) {
                return [];
            }
            
            return $result ?? $defaultReturn;
            
        } catch (Exception $e) {
            error_log("Error di Model " . get_class($model) . "::{$method} - " . $e->getMessage());
            return $defaultReturn;
        }
    }
    
    /**
     * Set flash message untuk feedback user
     * 
     * @param string $type Tipe pesan: 'success', 'error', 'warning', 'info'
     * @param string $message Pesan yang akan ditampilkan
     */
    protected function setFlashMessage(string $type, string $message) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['flash_' . $type] = $message;
    }
    
    /**
     * Redirect dengan flash message
     * 
     * @param string $url URL tujuan redirect
     * @param string $type Tipe pesan flash
     * @param string $message Pesan flash
     */
    protected function redirectWithMessage(string $url, string $type, string $message) {
        $this->setFlashMessage($type, $message);
        header("Location: {$url}");
        exit;
    }
    
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