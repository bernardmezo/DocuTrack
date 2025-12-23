<?php

namespace App\Controllers\Direktur;

use App\Core\Controller;
use App\Models\DirekturModel;
use Exception;

class MonitoringController extends Controller
{
    private DirekturModel $direkturModel;

    public function __construct()
    {
        parent::__construct();
        $this->direkturModel = new DirekturModel();
    }

    public function index($data_dari_router = [])
    {
        $list_jurusan = $this->safeModelCall($this->direkturModel, 'getListJurusanDistinct', [], []);

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_jurusan' => $list_jurusan
        ]);

        $this->view('pages/Direktur/monitoring', $data, 'direktur');
    }

    /**
     * API endpoint untuk AJAX - menggunakan pola yang sama dengan PPK
     */
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

            $result = $this->direkturModel->getMonitoringData($page, $per_page, $search_text, $status_filter, $jurusan_filter);

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
