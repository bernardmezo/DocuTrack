<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Services\SuperAdminService;
use App\Services\LogStatusService; // Added

// Use Service instead of Model

class DashboardController extends Controller
{
    private $service; // Changed from $model to $service for clarity
    private LogStatusService $logStatusService; // Added

    public function __construct($db)
    {
        parent::__construct($db);
        $this->service = new SuperAdminService($this->db); // Instantiate Service
        $this->logStatusService = new LogStatusService($this->db); // Added
    }

    public function index($data_dari_router = [])
    {

        // Call methods via the service layer
        $stats = $this->service->getDashboardStats();
        $list_prodi = $this->service->getListProdi();
        $list_kak = $this->service->getListKegiatan(20);
        $list_lpj = $this->service->getListLPJ(10);

        // --- Ambil Notifikasi ---
        $userId = $_SESSION['user_id'] ?? 0; // Asumsi userId ada di session
        $notificationsData = $this->logStatusService->getNotificationsForUser($userId);
        // --- End Notifikasi ---

        $data = array_merge($data_dari_router, [
            'title' => 'Super Admin Dashboard',
            'stats' => $stats,
            'list_prodi' => $list_prodi,
            'list_kak' => $list_kak,
            'list_lpj' => $list_lpj,
            'notifications' => $notificationsData['items'], // Added
            'unread_notifications_count' => $notificationsData['unread_count'] // Added
        ]);

        $this->view('pages/superadmin/dashboard', $data, 'superadmin');
    }
}
