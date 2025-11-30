<?php
// File: src/controllers/Admin/AdminPengajuanLpjController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php';

class AdminPengajuanLpjController extends Controller {
    
    private $model;

    public function __construct() {
        $this->model = new adminModel();
    }

    /**
     * Menampilkan HALAMAN LIST PENGAJUAN LPJ
     */
    public function index($data_dari_router = []) { 
        
        // Ambil data LPJ dari database
        $list_lpj = $this->model->getDashboardLPJ();
        
        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan LPJ',
            'list_lpj' => $list_lpj 
        ]);

        $this->view('pages/admin/pengajuan_lpj_list', $data, 'app');
    }

    public function show($id, $data_dari_router = []) {
        
        $ref = $_GET['ref'] ?? 'lpj'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-lpj';

        // Ambil detail LPJ dari database
        $lpj_detail = $this->model->getDetailLPJ($id);
        
        if (!$lpj_detail) {
            header("Location: $back_url");
            exit;
        }
        
        $status = $lpj_detail['status'];

        $kegiatan_data = [
            'nama_kegiatan' => $lpj_detail['nama_kegiatan'],
            'pengusul' => $lpj_detail['pengusul']
        ];
        
        // Ambil RAB items dari database (jika ada kakId)
        $rab_items_merged = [];
        if (!empty($lpj_detail['kakId'])) {
            $rab_items_merged = $this->model->getRABForLPJ($lpj_detail['kakId']);
        }
        
        // Jika tidak ada RAB, tampilkan array kosong
        if (empty($rab_items_merged)) {
            $rab_items_merged = [];
        }
        
        // Komentar revisi (jika status revisi)
        $komentar_revisi = [];
        if (strtolower($status) === 'revisi') {
            $komentar_revisi = [
                'pesan_umum' => 'Mohon perbaiki item yang diberi komentar dan upload ulang bukti yang sesuai.'
            ];
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data['nama_kegiatan']),
            'status' => $status,
            'kegiatan_data' => $kegiatan_data,
            'rab_items' => $rab_items_merged,
            'komentar_revisi' => $komentar_revisi,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_lpj', $data, 'app');
    }
    
    public function uploadBukti() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Bukti berhasil diupload',
            'filename' => 'bukti_' . time() . '.pdf'
        ]);
    }
    
    public function submitLpj() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'LPJ berhasil diajukan ke Bendahara'
        ]);
    }
    
    public function submitRevisi() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Revisi LPJ berhasil disubmit'
        ]);
    }
}