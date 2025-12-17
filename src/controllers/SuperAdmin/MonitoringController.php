<?php

declare(strict_types=1);

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Models\SuperAdminModel;
use App\Services\LogStatusService;
use Throwable;

class MonitoringController extends Controller
{
    private $model;
    private $logStatusService;

    public function __construct($db = null)
    {
        parent::__construct($db);
        $this->model = new SuperAdminModel($this->db);
        $this->logStatusService = new LogStatusService($this->db);
    }

    public function index($data_dari_router = [])
    {
        // 1. Initialize Default Data
        $list_kegiatan = [];
        $list_lpj = [];
        $notifications = [];
        $unread_count = 0;
        $limit = 100; // Increase limit for monitoring page

        // 2. Fetch Global Data (God Mode)
        try {
            $list_kegiatan = $this->model->getGlobalMonitoringKegiatan($limit);
            $list_lpj = $this->model->getGlobalMonitoringLPJ($limit);
        } catch (Throwable $e) {
            error_log("[MonitoringController] Model Error: " . $e->getMessage());
        }

        // 3. Fetch Notifications (Service Layer)
        try {
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId) {
                $notifData = $this->logStatusService->getNotificationsForUser($userId);
                $notifications = $notifData['items'] ?? [];
                $unread_count = $notifData['unread_count'] ?? 0;
            }
        } catch (Throwable $e) {
            error_log("[MonitoringController] Notification Error: " . $e->getMessage());
        }

        // 4. Prepare View Data
        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Global',
            'user' => $_SESSION['user_data'] ?? [],
            'list_kegiatan' => $list_kegiatan,
            'list_lpj' => $list_lpj,
            'notifications' => $notifications,
            'unread_notifications_count' => $unread_count
        ]);

        // 5. Render View
        $this->view('pages/superadmin/monitoring', $data, 'superadmin');
    }
}