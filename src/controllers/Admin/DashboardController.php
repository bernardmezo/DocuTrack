<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\LpjService;
use App\Services\KegiatanService;
use App\Services\LogStatusService; // Added

class DashboardController extends Controller
{
    private $lpjService;
    private $kegiatanService;
    private LogStatusService $logStatusService; // Added

    public function __construct($db)
    {
        parent::__construct($db);
        $this->lpjService = new LpjService($this->db);
        $this->kegiatanService = new KegiatanService($this->db);
        $this->logStatusService = new LogStatusService($this->db); // Added
    }

    public function index($data_dari_router = [])
    {
        $stats = $this->kegiatanService->getDashboardStats();
        // Admin tidak perlu filter jurusan - harus melihat semua data
        $list_kak = $this->kegiatanService->getDashboardKAK(null);
        $list_lpj = $this->lpjService->getDashboardLPJ();
        $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC PPK', 'ACC WD', 'Dana Cair'];
        $tahap_sekarang_kak = 'Pengajuan';
        $icons_kak = [
            'Pengajuan' => 'fa-file-alt', 'Validasi' => 'fa-check-double', 'ACC PPK' => 'fa-stamp',
            'ACC WD' => 'fa-user-check', 'Dana Cair' => 'fa-wallet'
        ];
        $tahapan_lpj = ['Upload Bukti', 'Validasi', 'ACC Bendahara', 'Selesai'];
        $tahap_sekarang_lpj = 'Validasi';
        $icons_lpj = [
            'Upload Bukti' => 'fa-upload', 'Validasi' => 'fa-check-double',
            'ACC Bendahara' => 'fa-file-invoice-dollar', 'Selesai' => 'fa-flag-checkered'
        ];

        // --- Ambil Notifikasi ---
        $userId = $_SESSION['user_id'] ?? 0; // Asumsi userId ada di session
        $notificationsData = $this->logStatusService->getNotificationsForUser($userId);
        // --- End Notifikasi ---

        $data = array_merge($data_dari_router, [
            'title' => 'Admin Dashboard',
            'stats' => $stats,
            'tahapan_kak' => $tahapan_kak,
            'tahap_sekarang_kak' => $tahap_sekarang_kak,
            'icons_kak' => $icons_kak,
            'tahapan_lpj' => $tahapan_lpj,
            'tahap_sekarang_lpj' => $tahap_sekarang_lpj,
            'icons_lpj' => $icons_lpj,
            'list_kak' => $list_kak,
            'list_lpj' => $list_lpj,
            'notifications' => $notificationsData['items'], // Added
            'unread_notifications_count' => $notificationsData['unread_count'] // Added
        ]);

        $this->view('pages/admin/dashboard', $data, 'admin');
    }
}
