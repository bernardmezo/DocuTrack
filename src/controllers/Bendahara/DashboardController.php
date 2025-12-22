<?php

namespace App\Controllers\Bendahara;

use App\Core\Controller;
use App\Services\BendaharaService;
use App\Services\LogStatusService; // Added

class DashboardController extends Controller
{
    private $bendaharaService;
    private LogStatusService $logStatusService; // Added

    public function __construct($db)
    {
        parent::__construct($db);
        $this->bendaharaService = new BendaharaService($this->db);
        $this->logStatusService = new LogStatusService($this->db); // Added
    }

    public function index($data_dari_router = [])
    {
        $stats = $this->safeModelCall($this->bendaharaService, 'getDashboardStatistik', [], [
            'total' => 0, 'danaDiberikan' => 0, 'ditolak' => 0, 'menunggu' => 0
        ]);
        $list_kak = $this->safeModelCall($this->bendaharaService, 'getListKegiatanDashboard', [10], []);
        $list_lpj = $this->safeModelCall($this->bendaharaService, 'getAntrianLPJ', [], []);
        

        $success_msg = $_SESSION['flash_message'] ?? null;
        $error_msg = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        // --- Ambil Notifikasi ---
        $userId = $_SESSION['user_id'] ?? 0; // Asumsi userId ada di session
        $notificationsData = $this->logStatusService->getNotificationsForUser($userId);
        // --- End Notifikasi ---

        $data = array_merge($data_dari_router, [
            'title' => 'Bendahara Dashboard',
            'stats' => $stats,
            'list_kak' => $list_kak ?? [],
            'list_lpj' => $list_lpj ?? [],
            
            'success_message' => $success_msg,
            'error_message' => $error_msg,
            'notifications' => $notificationsData['items'], // Added
            'unread_notifications_count' => $notificationsData['unread_count'] // Added
        ]);

        $this->view('pages/bendahara/dashboard', $data, 'bendahara');
    }
}
