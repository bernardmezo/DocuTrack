<?php

namespace App\Controllers\Direktur;

use App\Core\Controller;
use App\Models\DirekturModel;

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
        // Get filters from query params
        $filters = [
            'status' => $_GET['status'] ?? 'Semua',
            'jurusan' => $_GET['jurusan'] ?? 'semua',
            'search' => $_GET['search'] ?? ''
        ];

        // Get page number
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10; // Items per page

        // Get data from model
        $result = $this->direkturModel->getMonitoringProposal($page, $perPage, $filters);
        $list_jurusan = $this->direkturModel->getListJurusan();

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_proposal' => $result['items'],
            'list_jurusan' => $list_jurusan,
            'pagination' => $result['pagination'],
            'filters' => $filters
        ]);

        $this->view('pages/Direktur/monitoring', $data, 'direktur');
    }

    /**
     * API endpoint untuk AJAX filtering (optional)
     */
    public function getData()
    {
        header('Content-Type: application/json');
        
        try {
            $filters = [
                'status' => $_GET['status'] ?? 'Semua',
                'jurusan' => $_GET['jurusan'] ?? 'semua',
                'search' => $_GET['search'] ?? ''
            ];

            $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
            $perPage = 10;

            $result = $this->direkturModel->getMonitoringProposal($page, $perPage, $filters);

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
