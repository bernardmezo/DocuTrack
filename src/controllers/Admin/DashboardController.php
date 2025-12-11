<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\LpjService;
use App\Services\KegiatanService;
use App\Services\LogStatusService;

class DashboardController extends Controller
{
    private $lpjService;
    private $kegiatanService;
    private LogStatusService $logStatusService;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->lpjService = new LpjService($this->db);
        $this->kegiatanService = new KegiatanService($this->db);
        $this->logStatusService = new LogStatusService($this->db);
    }

    public function index($data_dari_router = [])
    {
        $stats = $this->kegiatanService->getDashboardStats();
        
        // PERBAIKAN: Admin harus melihat SEMUA data (null = tidak ada filter jurusan)
        // Jika user role admin, tidak perlu filter jurusan
        $userRole = $_SESSION['user_role'] ?? null;
        
        // Admin melihat semua, user lain filter by jurusan
        if ($userRole === 'admin' || $userRole === 'verifikator' || $userRole === 'ppk' || $userRole === 'wadir' || $userRole === 'bendahara') {
            $jurusan = null; // Admin sees ALL data
        } else {
            $jurusan = $_SESSION['user_jurusan'] ?? null;
        }
        
        $list_kak = $this->kegiatanService->getDashboardKAK($jurusan);
        $list_lpj = $this->lpjService->getDashboardLPJ();
        
        // DEBUG LOG - hapus setelah testing
        error_log("=== DEBUG DASHBOARD CONTROLLER ===");
        error_log("User Role: " . ($userRole ?? 'NULL'));
        error_log("Filter Jurusan: " . ($jurusan ?? 'NULL (show all)'));
        error_log("Total KAK: " . count($list_kak));
        error_log("Total LPJ: " . count($list_lpj));
        error_log("KAK Data: " . json_encode($list_kak));
        error_log("===================================");
        
        $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC PPK', 'ACC WD', 'Dana Cair'];
        $tahap_sekarang_kak = 'Pengajuan';
        $icons_kak = [
            'Pengajuan' => 'fa-file-alt', 
            'Validasi' => 'fa-check-double', 
            'ACC PPK' => 'fa-stamp',
            'ACC WD' => 'fa-user-check', 
            'Dana Cair' => 'fa-wallet'
        ];
        
        $tahapan_lpj = ['Upload Bukti', 'Validasi', 'ACC Bendahara', 'Selesai'];
        $tahap_sekarang_lpj = 'Validasi';
        $icons_lpj = [
            'Upload Bukti' => 'fa-upload', 
            'Validasi' => 'fa-check-double',
            'ACC Bendahara' => 'fa-file-invoice-dollar', 
            'Selesai' => 'fa-flag-checkered'
        ];

        // --- Ambil Notifikasi ---
        $userId = $_SESSION['user_id'] ?? 0;
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
            'notifications' => $notificationsData['items'],
            'unread_notifications_count' => $notificationsData['unread_count']
        ]);

        $this->view('pages/admin/dashboard', $data, 'app');
    }
}