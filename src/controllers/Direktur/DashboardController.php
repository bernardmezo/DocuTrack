<?php

namespace App\Controllers\Direktur;

use App\Core\Controller;
use App\Services\LogStatusService;

class DashboardController extends Controller
{
    private LogStatusService $logStatusService;

    public function __construct()
    {
        parent::__construct();
        $this->logStatusService = new LogStatusService($this->db);
    }

    public function index($data_dari_router = [])
    {

        // Data masih dummy
        $stats = [
            'total' => 255, 'disetujui' => 10, 'ditolak' => 2, 'menunggu' => 3, 'revisi' => 1
        ];
        $list_prodi = ['Teknik Informatika', 'Sistem Informasi', 'Desain Grafis', 'Manajemen'];
        $list_kak_dummy = [];
        $list_lpj_dummy = [];

        // --- Ambil Notifikasi ---
        $userId = $_SESSION['user_id'] ?? 0; // Asumsi userId ada di session
        $notificationsData = $this->logStatusService->getNotificationsForUser($userId);
        // --- End Notifikasi ---

        $data = array_merge($data_dari_router, [
            'title' => 'Direktur Dashboard',
            'stats' => $stats,
            'list_prodi' => $list_prodi,
            'list_kak' => $list_kak_dummy,
            'list_lpj' => $list_lpj_dummy,
            'notifications' => $notificationsData['items'], // Added
            'unread_notifications_count' => $notificationsData['unread_count'] // Added
        ]);

        $this->view('pages/Direktur/dashboard', $data, 'direktur');
    }
}
