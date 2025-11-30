<?php
// File: src/controllers/PPK/MonitoringController.php
require_once '../src/core/Controller.php';
require_once '../src/model/ppkModel.php'; 

class PPKMonitoringController extends Controller {
    
    public function index($data_dari_router = []) { 
        $model = new ppkModel();
        $list_jurusan = $model->getListJurusanDistinct();

        $data = array_merge($data_dari_router, [
            'title' => 'Monitoring Proposal',
            'list_jurusan' => $list_jurusan
        ]);
        
        $this->view('pages/ppk/monitoring', $data, 'ppk');
    }

    public function getData() {
        // Matikan output error HTML agar tidak merusak JSON
        error_reporting(0);
        ini_set('display_errors', 0);
        header('Content-Type: application/json');

        try {
            $page = (int)($_GET['page'] ?? 1);
            $status_filter = strtolower($_GET['status'] ?? 'semua');
            $jurusan_filter = $_GET['jurusan'] ?? 'semua';
            $search_text = trim($_GET['search'] ?? '');
            $per_page = 5;

            $model = new ppkModel();
            
            // Panggil Fungsi dari Model
            $result = $model->getMonitoringData($page, $per_page, $search_text, $status_filter, $jurusan_filter);

            $proposals = $result['data'];
            $total_items = $result['totalItems'];
            $total_pages = max(1, ceil($total_items / $per_page));
            
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
?>