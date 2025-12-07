<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * HealthCheckController
 * 
 * Controller untuk health check system - database, session, storage
 * Menggantikan file cek_koneksi.php yang melanggar MVC pattern
 * 
 * @category Controller
 * @package  DocuTrack
 * @version  1.0.0 - Created Dec 2025 (MVC Compliance Refactoring)
 */
class HealthCheckController extends Controller {
    
    /**
     * Main health check page
     * Menampilkan status sistem secara keseluruhan
     */
    public function index() {
        // Check semua komponen sistem
        $checks = [
            'database' => $this->checkDatabase(),
            'session' => $this->checkSession(),
            'storage' => $this->checkStorage(),
            'php_version' => $this->checkPhpVersion()
        ];
        
        // Hitung overall status
        $allOk = true;
        foreach ($checks as $check) {
            if ($check['status'] !== 'OK') {
                $allOk = false;
                break;
            }
        }
        
        $data = [
            'title' => 'System Health Check - DocuTrack',
            'checks' => $checks,
            'overall_status' => $allOk ? 'HEALTHY' : 'UNHEALTHY',
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Render view
        $this->view('pages/health_check', $data, 'guest');
    }
    
    /**
     * JSON API endpoint untuk monitoring tools
     */
    public function api() {
        header('Content-Type: application/json');
        
        $checks = [
            'database' => $this->checkDatabase(),
            'session' => $this->checkSession(),
            'storage' => $this->checkStorage(),
            'php_version' => $this->checkPhpVersion()
        ];
        
        $allOk = true;
        foreach ($checks as $check) {
            if ($check['status'] !== 'OK') {
                $allOk = false;
                break;
            }
        }
        
        $response = [
            'status' => $allOk ? 'healthy' : 'unhealthy',
            'timestamp' => date('c'), // ISO 8601 format
            'checks' => $checks
        ];
        
        // Set HTTP status code
        http_response_code($allOk ? 200 : 503);
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        exit;
    }
    
    /**
     * Check database connection dan tabel
     * 
     * @return array Status information
     */
    private function checkDatabase() {
        try {
            // Test koneksi
            $result = mysqli_query($this->db, "SELECT 1");
            
            if (!$result) {
                throw new \Exception("Query test failed: " . mysqli_error($this->db));
            }
            
            // Hitung jumlah tabel
            $tables = [];
            $tablesResult = mysqli_query($this->db, "SHOW TABLES");
            
            if ($tablesResult) {
                while ($row = mysqli_fetch_array($tablesResult)) {
                    $tables[] = $row[0];
                }
                mysqli_free_result($tablesResult);
            }
            
            // Check critical tables
            $criticalTables = [
                'tbl_user', 
                'tbl_role', 
                'tbl_kegiatan', 
                'tbl_kak'
            ];
            
            $missingTables = [];
            foreach ($criticalTables as $table) {
                if (!in_array($table, $tables)) {
                    $missingTables[] = $table;
                }
            }
            
            return [
                'status' => empty($missingTables) ? 'OK' : 'WARNING',
                'message' => empty($missingTables) 
                    ? 'Database connected successfully' 
                    : 'Missing tables: ' . implode(', ', $missingTables),
                'details' => [
                    'total_tables' => count($tables),
                    'tables' => $tables,
                    'missing_tables' => $missingTables,
                    'server_info' => mysqli_get_server_info($this->db)
                ]
            ];
            
        } catch (\Exception $e) {
            return [
                'status' => 'ERROR',
                'message' => 'Database connection failed',
                'details' => [
                    'error' => $e->getMessage()
                ]
            ];
        }
    }
    
    /**
     * Check session functionality
     * 
     * @return array Status information
     */
    private function checkSession() {
        $sessionActive = session_status() === PHP_SESSION_ACTIVE;
        
        return [
            'status' => $sessionActive ? 'OK' : 'ERROR',
            'message' => $sessionActive 
                ? 'Session is active' 
                : 'Session is not active',
            'details' => [
                'session_status' => session_status(),
                'session_id' => $sessionActive ? session_id() : 'N/A',
                'session_name' => session_name()
            ]
        ];
    }
    
    /**
     * Check storage directories writable
     * 
     * @return array Status information
     */
    private function checkStorage() {
        $uploadDir = __DIR__ . '/../../public/uploads';
        $logsDir = __DIR__ . '/../../logs';
        
        $checks = [
            'uploads' => [
                'path' => $uploadDir,
                'exists' => is_dir($uploadDir),
                'writable' => is_writable($uploadDir)
            ],
            'logs' => [
                'path' => $logsDir,
                'exists' => is_dir($logsDir),
                'writable' => is_writable($logsDir)
            ]
        ];
        
        $allOk = true;
        $messages = [];
        
        foreach ($checks as $name => $check) {
            if (!$check['exists']) {
                $allOk = false;
                $messages[] = "$name directory does not exist";
            } elseif (!$check['writable']) {
                $allOk = false;
                $messages[] = "$name directory is not writable";
            }
        }
        
        return [
            'status' => $allOk ? 'OK' : 'ERROR',
            'message' => $allOk 
                ? 'All storage directories are writable' 
                : implode(', ', $messages),
            'details' => $checks
        ];
    }
    
    /**
     * Check PHP version
     * 
     * @return array Status information
     */
    private function checkPhpVersion() {
        $currentVersion = phpversion();
        $minVersion = '7.4.0';
        $versionOk = version_compare($currentVersion, $minVersion, '>=');
        
        return [
            'status' => $versionOk ? 'OK' : 'WARNING',
            'message' => $versionOk 
                ? "PHP version is $currentVersion" 
                : "PHP version $currentVersion is below recommended $minVersion",
            'details' => [
                'current' => $currentVersion,
                'recommended' => $minVersion,
                'extensions' => get_loaded_extensions()
            ]
        ];
    }
}
