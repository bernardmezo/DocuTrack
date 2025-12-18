<?php

namespace App\Controllers\Direktur;

use App\Core\Controller;
use App\Services\LogStatusService;
use App\Models\DirekturModel;

class DashboardController extends Controller
{
    private LogStatusService $logStatusService;
    private DirekturModel $direkturModel;

    public function __construct()
    {
        parent::__construct();
        $this->logStatusService = new LogStatusService($this->db);
        $this->direkturModel = new DirekturModel();
    }

    public function index($data_dari_router = [])
    {
        // Get statistik umum
        $stats = $this->direkturModel->getStatistikUmum();

        // Get list jurusan untuk filter
        $list_jurusan = $this->direkturModel->getListJurusan();

        // Get data kegiatan untuk chart (dengan created_at)
        $list_kak = $this->direkturModel->getDataKegiatanForChart();

        // Get notifikasi user
        $userId = $_SESSION['user_id'] ?? 0;
        $notificationsData = $this->logStatusService->getNotificationsForUser($userId);

        $data = array_merge($data_dari_router, [
            'title' => 'Direktur Dashboard',
            'stats' => $stats,
            'list_jurusan' => $list_jurusan,
            'list_kak' => $list_kak,
            'list_lpj' => [], // Kosongkan untuk saat ini
            'notifications' => $notificationsData['items'],
            'unread_notifications_count' => $notificationsData['unread_count']
        ]);

        $this->view('pages/Direktur/dashboard', $data, 'direktur');
    }

    /**
     * API Endpoint: Get usulan per jurusan berdasarkan periode
     */
    public function apiUsulanPerJurusan()
    {
        header('Content-Type: application/json');
        
        try {
            $periode = $_GET['periode'] ?? 'all';
            $allowedPeriods = ['today', 'week', 'month', 'year', 'all'];
            
            if (!in_array($periode, $allowedPeriods)) {
                $periode = 'all';
            }

            $chartData = $this->direkturModel->getUsulanPerJurusan($periode);
            $summary = $this->direkturModel->getSummaryStatistik($periode);

            echo json_encode([
                'success' => true,
                'data' => $chartData,
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * API Endpoint: Get total dana per jurusan
     */
    public function apiDanaPerJurusan()
    {
        header('Content-Type: application/json');
        
        try {
            $chartData = $this->direkturModel->getTotalDanaPerJurusan();

            echo json_encode([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * API Endpoint: Get daftar pengajuan dengan pagination
     */
    public function apiDaftarPengajuan()
    {
        header('Content-Type: application/json');
        
        try {
            $page = (int)($_GET['page'] ?? 1);
            $search = $_GET['search'] ?? null;
            $jurusan = $_GET['jurusan'] ?? null;

            // Validasi page number
            if ($page < 1) {
                $page = 1;
            }

            $result = $this->direkturModel->getDaftarPengajuan($page, 5, $search, $jurusan);

            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
}
