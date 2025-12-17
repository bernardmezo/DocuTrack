<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Models\SuperAdminModel;
use App\Services\LogStatusService;
use App\Services\AiLogService;
use Throwable;

class DashboardController extends Controller
{
    private $model;
    private $logStatusService;
    private $aiLogService;

    /**
     * Constructor with Manual Dependency Instantiation.
     * 
     * @param mixed $db Database connection (optional)
     */
    public function __construct($db = null)
    {
        // 1. Initialize Parent (Controller) to set $this->db
        parent::__construct($db);
        
        // 2. Manual Instantiation of Model
        // We pass $this->db which is set by parent::__construct
        $this->model = new SuperAdminModel($this->db);
        
        // 3. Manual Instantiation of Services
        // LogStatusService requires DB connection
        $this->logStatusService = new LogStatusService($this->db);
        
        // AiLogService now requires DB for caching
        $this->aiLogService = new AiLogService($this->db);
    }

    public function index($data_dari_router = [])
    {
        // Initialize Default Data
        $stats = ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0, 'revisi' => 0];
        $notifications = [];
        $unread_count = 0;
        $monitoring_kegiatan = [];
        $monitoring_lpj = [];
        $system_health = [];
        // AI Summary is now loaded via AJAX
        $ai_summary = ""; 

        // 1. Fetch Core Dashboard Stats
        try {
            $stats = $this->model->getDashboardStats();
        } catch (Throwable $e) {
            error_log("[Dashboard] Stats Error: " . $e->getMessage());
        }

        // 2. Command Center: System Health Check
        try {
            $system_health = [
                'php_version' => phpversion(),
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'memory_usage' => round(memory_get_usage() / 1024 / 1024, 2) . ' MB',
                'db_connection' => $this->db && mysqli_ping($this->db),
                'timestamp' => date('Y-m-d H:i:s')
            ];
        } catch (Throwable $e) {
            $system_health = ['db_connection' => false, 'error' => $e->getMessage()];
        }

        // 3. Command Center: Real-time Monitoring Data (Limit 5)
        try {
            $monitoring_kegiatan = $this->model->getGlobalMonitoringKegiatan(5);
            $monitoring_lpj = $this->model->getGlobalMonitoringLPJ(5);
        } catch (Throwable $e) {
            error_log("[Dashboard] Monitoring Data Error: " . $e->getMessage());
        }

        // 4. Fetch Notifications
        try {
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId) {
                $notifData = $this->logStatusService->getNotificationsForUser($userId);
                $notifications = $notifData['items'] ?? [];
                $unread_count = $notifData['unread_count'] ?? 0;
            }
        } catch (Throwable $e) {
            error_log("[Dashboard] Notification Error: " . $e->getMessage());
        }

        // 5. Prepare View Data
        $data = array_merge($data_dari_router, [
            'title' => 'Command Center',
            'user' => $_SESSION['user_data'] ?? [],
            'stats' => $stats,
            'notifications' => $notifications,
            'unread_notifications_count' => $unread_count,
            'system_health' => $system_health,
            'ai_summary' => $ai_summary, // Empty initially
            'monitoring_kegiatan' => $monitoring_kegiatan,
            'monitoring_lpj' => $monitoring_lpj
        ]);

        // 6. Render View
        $this->view('pages/superadmin/dashboard', $data, 'superadmin');
    }

    /**
     * AJAX Endpoint: Lazy Load AI Analysis
     * Returns JSON
     */
    public function getAiAnalysis()
    {
        // Security: Ensure only SuperAdmin can access
        // (Middleware usually handles this, but good to check if this is a public API route)
        // In this architecture, routing middleware covers it.

        header('Content-Type: application/json');

        try {
            // Use 1 hour cache (3600 seconds)
            // Analyze last 30 lines (increased slightly from 20 as we are now async)
            $result = $this->aiLogService->getSmartSummary(30, 3600);
            
            echo json_encode([
                'status' => 'success',
                'data' => $result['summary'],
                'model' => $result['model']
            ]);
        } catch (Throwable $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Analysis failed: ' . $e->getMessage()
            ]);
        }
        exit;
    }
}
