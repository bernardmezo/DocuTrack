<?php
require_once __DIR__ . '/../src/bootstrap.php';

require_once __DIR__ . '/../src/middleware/AuthMiddleware.php';
require_once __DIR__ . '/../src/middleware/RegisterMiddleware.php';
require_once __DIR__ . '/../src/middleware/AdminMiddleware.php';
require_once __DIR__ . '/../src/middleware/VerifikatorMiddleware.php';
require_once __DIR__ . '/../src/middleware/WadirMiddleware.php';
require_once __DIR__ . '/../src/middleware/PpkMiddleware.php';
require_once __DIR__ . '/../src/middleware/BendaharaMiddleware.php';
require_once __DIR__ . '/../src/middleware/SuperAdminMiddleware.php';

function get_request_path() {
    $request_uri = $_SERVER['REQUEST_URI'];
    $script_name = $_SERVER['SCRIPT_NAME'];

    $base_path = dirname($script_name);
    
    if ($base_path === '/' || $base_path === '\\') {
        $base_path = '';
    }

    $request_path = $request_uri;
    if ($base_path && strpos($request_uri, $base_path) === 0) {
        $request_path = substr($request_uri, strlen($base_path));
    }
    
    $request_path = parse_url($request_path, PHP_URL_PATH);

    if (empty($request_path) || $request_path === '/index.php') {
        $request_path = '/';
    }
    
    $request_path = strtolower($request_path);

    return $request_path;
}

function not_found($message = "Page not found.") {
    http_response_code(404);
    echo "<h2>404 Not Found</h2>";
    echo "<p>" . htmlspecialchars($message) . "</p>";
    exit;
}

$path = get_request_path();

$parts = explode('/', trim($path, '/'));

$main_route = $parts[0] ?? '';
$sub_route  = $parts[1] ?? 'index';
$param1     = $parts[2] ?? null;
$param2     = $parts[3] ?? null;

$db = db();

switch ($main_route) {

    case '':
        require_once '../src/controllers/HomeController.php';
        $controller = new HomeController($db);
        $controller->index();
        break;

    case 'login':
        require_once '../src/controllers/AuthController.php';
        $controller = new AuthController($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleLogin();
        } else {
            header('Location: /docutrack/public/');
            exit;
        }
        break;
    
    case 'logout':
        require_once '../src/controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->logout();
        break;

    case 'admin':
        AuthMiddleware::check();
        AdminMiddleware::check();
        
        $base_admin_path = '/admin';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Admin/DashboardController.php';
                $controller = new AdminDashboardController($db); 
                $controller->index(['active_page' => $base_admin_path . '/dashboard']);
                break;

            case 'akun':
                require_once '../src/controllers/Admin/AkunController.php';
                $controller = new AdminAkunController($db);
                
                if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                } else {
                    $controller->index(['active_page' => $base_admin_path . '/akun']);
                }
                break;

            case 'detail-kak':
                require_once '../src/controllers/Admin/DetailKAK.php';
                $controller = new AdminDetailKAKController($db); 
                
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $controller->show($param2, ['active_page' => $base_admin_path . '/dashboard']);
                } else {
                    header('Location: /docutrack/public/admin/dashboard');
                    exit;
                }
                break;

            case 'pengajuan-usulan':
                require_once '../src/controllers/Admin/PengajuanUsulanController.php';
                $controller = new AdminPengajuanUsulanController($db); 

                if (isset($param1) && $param1 === 'store') {
                    $controller->store(); 
                } else {
                    $controller->index(['active_page' => $base_admin_path . '/pengajuan-usulan']);
                }
                break;

            case 'pengajuan-kegiatan':
                if (isset($param1) && $param1 === 'submitrincian') {
                    require_once '../src/controllers/Admin/AdminController.php';
                    $controller = new Controllers\Admin\AdminController($db);
                    $controller->submitRincian();
                    break;
                }

                require_once '../src/controllers/Admin/PengajuanKegiatanController.php';
                $controller = new AdminPengajuanKegiatanController($db); 

                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $controller->show($param2, ['active_page' => $base_admin_path . '/pengajuan-kegiatan']);
                } else {
                    $controller->index(['active_page' => $base_admin_path . '/pengajuan-kegiatan']);
                }
                break;

            case 'pengajuan-lpj':
                require_once '../src/controllers/Admin/AdminPengajuanLpjController.php';
                $controller = new AdminPengajuanLpjController($db); 
                
                // Debug routing
                error_log("Route: /admin/pengajuan-lpj");
                error_log("param1: " . ($param1 ?? 'null'));
                error_log("param2: " . ($param2 ?? 'null'));
                
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    // Route: /admin/pengajuan-lpj/show/{id}
                    error_log("Calling show() with ID: " . $param2);
                    $controller->show($param2, ['active_page' => $base_admin_path . '/pengajuan-lpj']);
                    
                } elseif (isset($param1) && $param1 === 'upload-bukti') {
                    // Route: /admin/pengajuan-lpj/upload-bukti
                    error_log("Calling uploadBukti()");
                    $controller->uploadBukti();
                    
                } elseif (isset($param1) && $param1 === 'submit') {
                    // Route: /admin/pengajuan-lpj/submit
                    error_log("Calling submitLpj()");
                    $controller->submitLpj();
                    
                } else {
                    // Route: /admin/pengajuan-lpj (default list)
                    error_log("Calling index()");
                    $controller->index(['active_page' => $base_admin_path . '/pengajuan-lpj']);
                }
                break;
        }
        break;

    case 'verifikator':
        AuthMiddleware::check();
        VerifikatorMiddleware::check();
        
        $base_verifikator_path = '/verifikator';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Verifikator/DashboardController.php';
                $controller = new VerifikatorDashboardController($db); 
                $controller->index(['active_page' => $base_verifikator_path . '/dashboard']);
                break;

            case 'akun':
                require_once '../src/controllers/Verifikator/AkunController.php';
                $controller = new VerifikatorAkunController($db);
                
                if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                } else {
                    $controller->index(['active_page' => $base_verifikator_path . '/akun']);
                }
                break;

            case 'pengajuan-telaah':
                require_once '../src/controllers/Verifikator/TelaahController.php';
                $controller = new VerifikatorTelaahController($db);
                $controller->index(['active_page' => $base_verifikator_path . '/pengajuan-telaah']);
                break;
                
            case 'telaah': 
                require_once '../src/controllers/Verifikator/TelaahController.php';
                $controller = new VerifikatorTelaahController($db);
                
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $controller->show($param2, ['active_page' => $base_verifikator_path . '/pengajuan-telaah']);
                    
                } elseif (isset($param1) && $param1 === 'approve' && isset($param2)) {
                    $controller->approve($param2);

                } elseif (isset($param1) && $param1 === 'reject' && isset($param2)) {
                    $controller->reject($param2);

                } elseif (isset($param1) && $param1 === 'revise' && isset($param2)) {
                    $controller->revise($param2);

                } else {
                    $controller->index(['active_page' => $base_verifikator_path . '/pengajuan-telaah']);
                }   
                break;

            case 'riwayat-verifikasi':
                require_once '../src/controllers/Verifikator/RiwayatController.php';
                $controller = new VerifikatorRiwayatController($db);
                $controller->index(['active_page' => $base_verifikator_path . '/riwayat-verifikasi']);
                break;

            default:
                not_found("Page Verifikator '/{$sub_route}' not found.");
        }
        break;

    case 'wadir':
        AuthMiddleware::check();
        WadirMiddleware::check();
        
        $base_wadir_path = '/wadir';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Wadir/DashboardController.php';
                $controller = new WadirDashboardController($db); 
                $controller->index(['active_page' => $base_wadir_path . '/dashboard']);
                break;

            case 'akun':
                require_once '../src/controllers/Wadir/AkunController.php';
                $controller = new WadirAkunController($db);
                
                if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                } else {
                    $controller->index(['active_page' => $base_wadir_path . '/akun']);
                }
                break;

            case 'pengajuan-kegiatan':
                require_once '../src/controllers/Wadir/PengajuanKegiatanController.php';
                $controller = new WadirPengajuanKegiatanController($db);
                $controller->index(['active_page' => $base_wadir_path . '/pengajuan-kegiatan']);
                break;
                
            case 'telaah': 
                require_once '../src/controllers/Wadir/TelaahController.php';
                $controller = new WadirTelaahController($db);
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $controller->show($param2, ['active_page' => $base_wadir_path . '/pengajuan-kegiatan']);
                } elseif (isset($param1) && $param1 === 'approve' && isset($param2)) {
                    $controller->approve($param2);
                } else {
                    header('Location: /docutrack/public/wadir/dashboard');
                }
                break;
            
            case 'monitoring':
                require_once '../src/controllers/Wadir/MonitoringController.php';
                $controller = new WadirMonitoringController($db);
                
                if (isset($param1) && $param1 === 'data') {
                    $controller->getData();
                } else {
                    $controller->index(['active_page' => $base_wadir_path . '/monitoring']);
                }
                break;

            case 'riwayat-verifikasi':
                require_once '../src/controllers/Wadir/RiwayatController.php';
                $controller = new WadirRiwayatController($db);
                $controller->index(['active_page' => $base_wadir_path . '/riwayat-verifikasi']);
                break;

            default:
                not_found("Page Wadir '/{$sub_route}' not found.");
        }
        break;

    case 'ppk':
        AuthMiddleware::check();
        PpkMiddleware::check();
        
        $base_ppk_path = '/ppk';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/PPK/DashboardController.php';
                $controller = new PPKDashboardController($db); 
                $controller->index(['active_page' => $base_ppk_path . '/dashboard']);
                break;

            case 'akun':
                require_once '../src/controllers/PPK/AkunController.php';
                $controller = new PPKAkunController($db);
                
                if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                } else {
                    $controller->index(['active_page' => $base_ppk_path . '/akun']);
                }
                break;

            case 'pengajuan-kegiatan':
                require_once '../src/controllers/PPK/PengajuanKegiatanController.php';
                $controller = new PPKPengajuanKegiatanController($db);
                $controller->index(['active_page' => $base_ppk_path . '/pengajuan-kegiatan']);
                break;

            case 'monitoring':
                require_once '../src/controllers/PPK/MonitoringController.php';
                $controller = new PPKMonitoringController($db);
                if (isset($param1) && $param1 === 'data') {
                    $controller->getData();
                } else {
                    $controller->index(['active_page' => $base_ppk_path . '/monitoring']);
                }
                break;

            case 'riwayat-verifikasi':
                require_once '../src/controllers/PPK/RiwayatController.php';
                $controller = new PPKRiwayatController($db);
                $controller->index(['active_page' => $base_ppk_path . '/riwayat-verifikasi']);
                break;
                
            case 'telaah': 
                require_once '../src/controllers/PPK/TelaahController.php';
                $controller = new PPKTelaahController($db);
                if (isset($param1) && $param1 === 'show' && isset($param2)) {
                    $ref = $_GET['ref'] ?? 'dashboard';
                    $active_page = $base_ppk_path . '/' . $ref;
                    $controller->show($param2, ['active_page' => $active_page]);

                } elseif (isset($param1) && $param1 === 'approve' && isset($param2)) {
                    $controller->approve($param2); 

                } else {
                    header('Location: /docutrack/public/ppk/dashboard');
                }
                break;
            
default:
                not_found("Page PPK '/{$sub_route}' not found.");
        }
        break;

    case 'bendahara':
        AuthMiddleware::check();
        BendaharaMiddleware::check();
        
        $base_bendahara_path = '/bendahara';
    
    switch ($sub_route) {
        case 'index': 
        case 'dashboard': 
            require_once '../src/controllers/Bendahara/DashboardController.php';
            $controller = new BendaharaDashboardController($db); 
            $controller->index(['active_page' => $base_bendahara_path . '/dashboard']);
            break;

        case 'akun':
            require_once '../src/controllers/Bendahara/AkunController.php';
            $controller = new BendaharaAkunController($db);
            
            if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->update();
            } else {
                $controller->index(['active_page' => $base_bendahara_path . '/akun']);
            }
            break;

        case 'pencairan-dana': 
            require_once '../src/controllers/Bendahara/PencairandanaController.php';
            $controller = new BendaharaPencairandanaController($db);
            
            if (isset($param1) && $param1 === 'show' && isset($param2)) {
                $ref = $_GET['ref'] ?? 'pencairan-dana';
                
                if ($ref === 'dashboard') {
                    $active_page = $base_bendahara_path . '/dashboard';
                } else {
                    $active_page = $base_bendahara_path . '/pencairan-dana';
                }
                
                $controller->show($param2, ['active_page' => $active_page]);
            } elseif (isset($param1) && $param1 === 'proses') {
                $controller->proses();
            } else {
                $controller->index(['active_page' => $base_bendahara_path . '/pencairan-dana']);
            }
            break;

        case 'pengajuan-lpj': 
            require_once '../src/controllers/Bendahara/PengajuanlpjController.php';
            $controller = new BendaharaPengajuanlpjController($db);
            
            if (isset($param1) && $param1 === 'show' && isset($param2)) {
                $controller->show($param2, ['active_page' => $base_bendahara_path . '/pengajuan-lpj']);
            } elseif (isset($param1) && $param1 === 'proses') {
                $controller->proses();
            } else {
                $controller->index(['active_page' => $base_bendahara_path . '/pengajuan-lpj']);
            }
            break;
        
        case 'riwayat-verifikasi': 
            require_once '../src/controllers/Bendahara/RiwayatverifikasiController.php';
            $controller = new BendaharaRiwayatverifikasiController($db);
            
            if (isset($param1) && $param1 === 'show' && isset($param2)) {
                $controller->show($param2, ['active_page' => $base_bendahara_path . '/riwayat-verifikasi']);
            } else {
                $controller->index(['active_page' => $base_bendahara_path . '/riwayat-verifikasi']);
            }
            break;
        
default:
            not_found("Page Bendahara not found.");
    }
    break;

    case 'super_admin':
        AuthMiddleware::check();
        SuperAdminMiddleware::check();
        
        $base_super_admin_path = '/super_admin';
        
        switch ($sub_route) {
            case 'index': 
            case 'dashboard': 
                require_once '../src/controllers/Super_Admin/DashboardController.php';
                $controller = new SuperadminDashboardController($db); 
                $controller->index();
                break;

            case 'akun':
                require_once '../src/controllers/Super_Admin/AkunController.php';
                $controller = new SuperAdminAkunController($db);
                
                if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->update();
                } else {
                    $controller->index(['active_page' => $base_super_admin_path . '/akun']);
                }
                break;

            case 'kelola-akun': 
                require_once '../src/controllers/Super_Admin/KelolaakunController.php';
                $controller = new SuperadminKelolaakunController($db); 
                $controller->index();
                break;
            case 'monitoring': 
                require_once '../src/controllers/Super_Admin/MonitoringController.php';
                $controller = new SuperadminMonitoringController($db); 
                $controller->index();
                break;
            case 'buat-iku': 
                require_once '../src/controllers/Super_Admin/BuatikuController.php';
                $controller = new SuperadminBuatikuController($db); 
                $controller->index();
                break;

            default:
                not_found("Page Super Admin not found.");
        }
        break; 

    case 'direktur':
    AuthMiddleware::check(); // ✅ Aktifkan
    // DirekturMiddleware::check(); // Opsional jika sudah dibuat
    
    $base_direktur_path = '/direktur';
    
    switch ($sub_route) {
        case 'index': 
        case 'dashboard': 
            require_once '../src/controllers/Direktur/DashboardController.php';
            $controller = new DirekturDashboardController(); 
            $controller->index(['active_page' => $base_direktur_path . '/dashboard']);
            break;

        case 'akun':
            require_once '../src/controllers/Direktur/AkunController.php';
            $controller = new DirekturAkunController();
            
            if (isset($param1) && $param1 === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->update();
            } else {
                $controller->index(['active_page' => $base_direktur_path . '/akun']);
            }
            break;
            // ============================================

            case 'monitoring': 
                require_once '../src/controllers/Direktur/MonitoringController.php';
                $controller = new DirekturMonitoringController(); 
                $controller->index();
                break;

            default:
                not_found("Halaman Direktur tidak ditemukan.");
        }
        break; 

    // --- Jika Rute Utama Tidak Dikenali ---
    default:
        not_found("Rute utama '/{$main_route}' tidak valid.");
}
