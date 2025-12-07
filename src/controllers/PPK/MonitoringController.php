<?php

namespace App\Controllers\PPK;

use App\Core\Controller;
use App\Services\PpkService;
use Exception;

class MonitoringController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new PpkService($this->db);
    }

    public function index($data_dari_router = [])
    {
        $list_jurusan = $this->safeModelCall($this->model, 'getListJurusanDistinct', [], []);

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_jurusan' => $list_jurusan
        ]);

        $this->view('pages/ppk/monitoring', $data, 'ppk');
    }

    public function getData()
    {
        error_reporting(0);
        ini_set('display_errors', 0);
        header('Content-Type: application/json');

        try {
            $page = (int)($_GET['page'] ?? 1);
            $status_filter = isset($_GET['status']) ? strtolower(urldecode($_GET['status'])) : 'semua';
            $jurusan_filter = isset($_GET['jurusan']) ? urldecode($_GET['jurusan']) : 'semua';
            $search_text = isset($_GET['search']) ? trim(urldecode($_GET['search'])) : '';
            $per_page = 5;

            $result = $this->model->getMonitoringData($page, $per_page, $search_text, $status_filter, $jurusan_filter);

            $proposals = $result['data'] ?? [];
            $total_items = $result['totalItems'] ?? 0;

            $total_pages = ($total_items > 0) ? ceil($total_items / $per_page) : 1;

            $offset = ($page - 1) * $per_page;
            $showingFrom = $total_items > 0 ? $offset + 1 : 0;
            $showingTo = $total_items > 0 ? min($offset + count($proposals), $total_items) : 0;

            echo json_encode([
                'proposals' => $proposals,
                'pagination' => [
                    'currentPage' => $page,
                    'totalPages' => $total_pages,
                    'totalItems' => $total_items,
                    'perPage' => $per_page,
                    'showingFrom' => $showingFrom,
                    'showingTo' => $showingTo
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }
}
