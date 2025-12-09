<?php
/**
 * DocuTrack Application Entry Point
 *
 * This file initializes the application, sets up global error handling,
 * and dispatches requests to the router.
 */

// 1. Bootstrap the application
// This file handles configuration, autoloading, session, and database connection.
require_once __DIR__ . '/../src/bootstrap.php';

use App\Core\Router;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;
use App\Exceptions\BusinessLogicException;

// 2. Set up Global Exception Handling
require_once __DIR__ . '/../src/helpers/error_logger_helper.php';

/**
 * Handles all uncaught exceptions in the application.
 * Logs fatal errors and displays user-friendly error pages.
 *
 * @param Throwable $exception The exception that was thrown.
 */
function globalExceptionHandler(Throwable $exception) {
    // Clear any previously buffered output
    if (ob_get_level() > 0) {
        ob_end_clean();
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    // Determine log level based on exception type
    $logLevel = 'ERROR';
    if ($exception instanceof ValidationException) {
        $logLevel = 'WARNING';
    } elseif ($exception instanceof ForbiddenException) {
        $logLevel = 'WARNING';
    } elseif ($exception instanceof BusinessLogicException) {
        $logLevel = 'INFO'; // Business logic errors might be expected, not necessarily critical
    }

    // Log the error for all exception types except for 404s
    if (!($exception instanceof NotFoundException)) {
        log_error(
            $logLevel,
            $exception->getCode(),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $request_uri
        );
    }

    if (getenv('APP_ENV') === 'development') {
        http_response_code(500);
        echo "<h1>Exception Occurred</h1>";
        echo "<p><strong>Type:</strong> " . get_class($exception) . "</p>";
        echo "<p><strong>Message:</strong> " . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<p><strong>File:</strong> " . htmlspecialchars($exception->getFile()) . " on line " . $exception->getLine() . "</p>";
        echo "<hr><h3>Stack Trace:</h3><pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
        exit;
    }

    switch (get_class($exception)) {
        case ValidationException::class:
            http_response_code(422);
            if (isset($_SERVER['HTTP_REFERER'])) {
                $_SESSION['flash_errors'] = $exception->getErrors();
                $_SESSION['old_input'] = $_POST;
                header('Location: ' . $_SERVER['HTTP_REFERER']);
            } else {
                include __DIR__ . '/../src/views/pages/errors/500.php';
            }
            break;

        case NotFoundException::class:
            http_response_code(404);
            include __DIR__ . '/../src/views/pages/errors/404.php';
            break;

        case ForbiddenException::class:
            http_response_code(403);
            include __DIR__ . '/../src/views/pages/errors/403.php';
            break;

        default:
            http_response_code(500);
            include __DIR__ . '/../src/views/pages/errors/500.php';
            break;
    }
    exit;
}

set_exception_handler('globalExceptionHandler');

/**
 * Gets the request path from the URL, stripping the base directory and query string.
 *
 * @return string The clean request path (e.g., '/admin/dashboard').
 */
function get_request_path(): string
{
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
        return '/';
    }

    return $request_path;
}

// 3. Get the database connection from the bootstrap helper.
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

        // ✅ TAMBAHKAN ROUTE BARU INI
        case 'simpan-revisi':
            error_log("=== ROUTE simpan-revisi TRIGGERED ===");
            error_log("param1 (ID): " . ($param1 ?? 'NULL'));
            
            require_once '../src/controllers/Admin/DetailKAK.php';
            $controller = new AdminDetailKAKController($db); 
            
            if (isset($param1) && !empty($param1)) {
                error_log("Calling simpanRevisi($param1)");
                $controller->simpanRevisi($param1);
            } else {
                error_log("ERROR: No ID provided");
                $_SESSION['error_message'] = "ID kegiatan tidak ditemukan.";
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
            
            if (isset($param1) && $param1 === 'show' && isset($param2)) {
                $controller->show($param2, ['active_page' => $base_admin_path . '/pengajuan-lpj']);
            } elseif (isset($param1) && $param1 === 'upload-bukti') {
                $controller->uploadBukti();
            } elseif (isset($param1) && $param1 === 'submit') {
                $controller->submitLpj();
            } else {
                $controller->index(['active_page' => $base_admin_path . '/pengajuan-lpj']);
            }
            break;

        default:
            not_found("Page Admin '/{$sub_route}' not found.");
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
