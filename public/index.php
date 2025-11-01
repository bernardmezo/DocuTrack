<?php
// public/index.php
// =================================================================
// FRONT CONTROLLER (ROUTER)
// =================================================================

// 1. Mulai Session
// Wajib ada di baris paling atas untuk menangani login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Muat File Inti & Middleware
// -----------------------------------------------------------------
require_once '../src/core/Controller.php'; // Base Controller
// Muat semua middleware di awal
require_once '../src/middleware/AuthMiddleware.php';
require_once '../src/middleware/AdminMiddleware.php';
require_once '../src/middleware/VerifikatorMiddleware.php';
require_once '../src/middleware/WadirMiddleware.php';
require_once '../src/middleware/PpkMiddleware.php';
require_once '../src/middleware/BendaharaMiddleware.php';
// -----------------------------------------------------------------


// 3. Fungsi Helper Routing
// -----------------------------------------------------------------
/**
 * Membersihkan URL request agar routing berfungsi baik di subfolder (XAMPP)
 * maupun di root domain. Menghapus base path dan query string.
 * @return string Path request yang bersih (e.g., '/', '/admin/dashboard').
 */
function get_request_path() {
    $request_uri = $_SERVER['REQUEST_URI']; // cth: /docutrack/public/admin/dashboard?id=1
    $script_name = $_SERVER['SCRIPT_NAME']; // cth: /docutrack/public/index.php

    $base_path = dirname($script_name); // cth: /docutrack/public
    
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '';
    }

    $request_path = $request_uri;
    if ($base_path && strpos($request_uri, $base_path) === 0) {
        $request_path = substr($request_uri, strlen($base_path)); // cth: /admin/dashboard?id=1
    }
    
    $request_path = parse_url($request_path, PHP_URL_PATH); // cth: /admin/dashboard

    if (empty($request_path) || $request_path === '/index.php') {
        $request_path = '/';
    }
    
    return $request_path;
}

/**
 * Fungsi untuk menampilkan halaman 404 Not Found.
 * @param string $message Pesan error yang akan ditampilkan.
 */
function not_found($message = "Halaman tidak ditemukan.") {
    http_response_code(404);
    // Anda bisa memuat view 404 yang lebih bagus jika ada
    // require '../src/views/pages/errors/404.php';
    echo "<h2>404 Not Found</h2>";
    echo "<p>" . htmlspecialchars($message) . "</p>";
    exit; // Hentikan eksekusi
}
// -----------------------------------------------------------------


// 4. Proses Routing Utama
// -----------------------------------------------------------------
$path = get_request_path(); // Dapatkan path bersih (e.g., '/admin/dashboard')

$parts = explode('/', trim($path, '/'));

$main_route = $parts[0] ?? '';   // 'admin', 'wadir', '', 'login', dll.
$sub_route  = $parts[1] ?? 'index'; // 'dashboard', 'users', 'index' (default)
$param1     = $parts[2] ?? null;  // Parameter tambahan 1 (e.g., 'show', ID)
$param2     = $parts[3] ?? null;  // Parameter tambahan 2 (e.g., ID)


// Gunakan switch untuk menentukan controller berdasarkan segmen utama
switch ($main_route) {

    // --- Rute Landing Page (Publik) ---
    case '': // Jika path hanya '/'
        require_once '../src/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    // --- Rute Login/Logout (Publik) ---
    case 'login':
        require_once '../src/controllers/AuthController.php';
        $controller = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleLogin();
        } else {
            // Jika akses GET ke /login, redirect ke home (karena pakai popup)
            header('Location: /docutrack/public/');
            exit;
        }
        break;

    case 'logout':
        require_once '../src/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    // --- Rute ADMIN (Dilindungi Middleware) ---
    case 'admin':
        AuthMiddleware::check();  // 1. Pastikan sudah login
        AdminMiddleware::check(); // 2. Pastikan role adalah 'admin'
        
        $base_admin_path = '/admin';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Admin/DashboardController.php';
                $controller = new AdminDashboardController(); 
                $controller->index(['active_page' => $base_admin_path . '/dashboard']);
                break;

            case 'pengajuan-usulan':
                require_once '../src/controllers/Admin/PengajuanUsulanController.php';
                $controller = new AdminPengajuanUsulanController(); 
                $controller->index(['active_page' => $base_admin_path . '/pengajuan-usulan']);
                break;

            case 'pengajuan-kegiatan':
                require_once '../src/controllers/Admin/PengajuanKegiatanController.php';
                $controller = new AdminPengajuanKegiatanController(); 
                
                // Cek apakah ini rute 'show' (e.g., /admin/pengajuan-kegiatan/show/123)
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    // Panggil method show() dengan ID
                    $controller->show($param2, ['active_page' => $base_admin_path . '/pengajuan-kegiatan']);
                } else {
                    // Jika tidak, panggil method index() (halaman list)
                    $controller->index(['active_page' => $base_admin_path . '/pengajuan-kegiatan']);
                }
                break;

            case 'pengajuan-lpj':
                require_once '../src/controllers/Admin/AdminPengajuanLpjController.php';
                $controller = new AdminPengajuanLpjController(); 
                
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    // Rute: /admin/pengajuan-lpj/show/[ID]
                    $controller->show($param2, ['active_page' => $base_admin_path . '/pengajuan-lpj']);
                } else {
                    // Rute: /admin/pengajuan-lpj
                    $controller->index(['active_page' => $base_admin_path . '/pengajuan-lpj']);
                }
                break;

            default:
                not_found("Halaman Admin '/{$sub_route}' tidak ditemukan.");
        }
        break; // Akhir dari case 'admin'
        
    // --- Rute VERIFIKATOR (Dilindungi Middleware) ---
    case 'verifikator':
        AuthMiddleware::check();
        VerifikatorMiddleware::check();
        
        $base_verifikator_path = '/verifikator';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Verifikator/DashboardController.php';
                $controller = new VerifikatorDashboardController(); 
                $controller->index(['active_page' => $base_verifikator_path . '/dashboard']);
                break;

            // --- RUTE UNTUK HALAMAN TELAAN BARU ---
            case 'pengajuan-telaah':
                require_once '../src/controllers/Verifikator/TelaahController.php';
                $controller = new VerifikatorTelaahController();
                // Rute: /verifikator/pengajuan-telaah (List)
                $controller->index(['active_page' => $base_verifikator_path . '/pengajuan-telaah']);
                break;
                
            case 'telaah': 
                require_once '../src/controllers/Verifikator/TelaahController.php';
                $controller = new VerifikatorTelaahController();
                
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    // Rute: /verifikator/telaah/show/[ID]
                    $controller->show($param2, ['active_page' => $base_verifikator_path . '/pengajuan-telaah']);
                } else {
                    // Fallback jika hanya /verifikator/telaah
                    $controller->index(['active_page' => $base_verifikator_path . '/pengajuan-telaah']);
                }
                break;
            // --- AKHIR RUTE TELAAN ---

            // --- RUTE BARU RIWAYAT ---
            case 'riwayat-verifikasi':
                // GANTI NAMA FILE DI SINI:
                require_once '../src/controllers/Verifikator/RiwayatController.php';
                $controller = new RiwayatController(); // Nama class juga harus cocok
                $controller->index(['active_page' => $base_verifikator_path . '/riwayat-verifikasi']);
                break;

            default:
                not_found("Halaman Verifikator '/{$sub_route}' tidak ditemukan.");
        }
        break; // Akhir dari case 'verifikator'

    // --- Rute WADIR (Dilindungi Middleware) ---
    case 'wadir':
        AuthMiddleware::check();
        WadirMiddleware::check();
        
        $base_wadir_path = '/wadir';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Wadir/DashboardController.php';
                $controller = new WadirDashboardController(); 
                $controller->index(['active_page' => $base_wadir_path . '/dashboard']);
                break;

            case 'pengajuan-kegiatan':
                require_once '../src/controllers/Wadir/PengajuanKegiatanController.php';
                $controller = new WadirPengajuanKegiatanController();
                $controller->index(['active_page' => $base_wadir_path . '/pengajuan-kegiatan']);
                break;
                
            case 'telaah': 
                require_once '../src/controllers/Wadir/TelaahController.php';
                $controller = new WadirTelaahController();
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $controller->show($param2, ['active_page' => $base_wadir_path . '/pengajuan-kegiatan']);
                } else {
                    header('Location: /docutrack/public/wadir/dashboard');
                }
                break;
            
            case 'monitoring':
                require_once '../src/controllers/Wadir/MonitoringController.php';
                $controller = new WadirMonitoringController();
                
                if (isset($param1) && $param1 === 'data') {
                    // Rute API: /wadir/monitoring/data
                    $controller->getData();
                } else {
                    // Rute Halaman: /wadir/monitoring
                    $controller->index(['active_page' => $base_wadir_path . '/monitoring']);
                }
                break;

            case 'riwayat-verifikasi':
                require_once '../src/controllers/Wadir/RiwayatController.php';
                $controller = new WadirRiwayatController();
                $controller->index(['active_page' => $base_wadir_path . '/riwayat-verifikasi']);
                break;

            default:
                not_found("Halaman Wadir '/{$sub_route}' tidak ditemukan.");
        }
        break;

            // --- Rute PPK (BARU) ---
    case 'ppk':
        AuthMiddleware::check();
        PPKMiddleware::check(); // <-- Pastikan Anda membuat Middleware ini
        
        $base_ppk_path = '/ppk';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/PPK/DashboardController.php';
                $controller = new PPKDashboardController(); 
                $controller->index(['active_page' => $base_ppk_path . '/dashboard']);
                break;

            case 'pengajuan-kegiatan':
                require_once '../src/controllers/PPK/PengajuanKegiatanController.php';
                $controller = new PPKPengajuanKegiatanController();
                $controller->index(['active_page' => $base_ppk_path . '/pengajuan-kegiatan']);
                break;

            case 'monitoring':
                require_once '../src/controllers/PPK/MonitoringController.php';
                $controller = new PPKMonitoringController();
                if (isset($param1) && $param1 === 'data') {
                    $controller->getData(); // Rute API
                } else {
                    $controller->index(['active_page' => $base_ppk_path . '/monitoring']); // Rute Halaman
                }
                break;

            case 'riwayat-verifikasi':
                require_once '../src/controllers/PPK/RiwayatController.php';
                $controller = new PPKRiwayatController();
                $controller->index(['active_page' => $base_ppk_path . '/riwayat-verifikasi']);
                break;
                
            case 'telaah': 
                require_once '../src/controllers/PPK/TelaahController.php';
                $controller = new PPKTelaahController();
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $ref = $_GET['ref'] ?? 'dashboard';
                    $active_page = $base_ppk_path . '/' . $ref;
                    $controller->show($param2, ['active_page' => $active_page]);
                } else {
                    header('Location: /docutrack/public/ppk/dashboard'); // Fallback
                }
                break;
            
            default:
                not_found("Halaman PPK '/{$sub_route}' tidak ditemukan.");
        }
        break;

    // --- Rute PPK (Dilindungi Middleware) ---
    case 'ppk':
        AuthMiddleware::check();
        PpkMiddleware::check();
        echo "Halaman PPK (Dalam Pengembangan)";
        // ... (Tambahkan switch $sub_route untuk ppk) ...
        break; 

    // --- Rute BENDAHARA (Dilindungi Middleware) ---
    case 'bendahara':
        AuthMiddleware::check();
        BendaharaMiddleware::check();
        echo "Halaman Bendahara (Dalam Pengembangan)";
        // ... (Tambahkan switch $sub_route untuk bendahara) ...
        break; 

    // --- Jika Rute Utama Tidak Dikenali ---
    default:
        not_found("Rute utama '/{$main_route}' tidak valid.");
}
// -----------------------------------------------------------------