<?php

// File: src/controllers/Admin/PengajuanUsulanController.php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\AdminService;

// Removed: use App\Services\ValidationService;

class PengajuanUsulanController extends Controller
{
    private $model;
    // validationService is now inherited from base Controller

    public function __construct($db)
    {
 // Added $db parameter
        parent::__construct($db); // Passed $db to parent constructor
        $this->model = new AdminService($this->db);
        // $this->validationService is already set in parent::__construct()
    }

    public function index($data_dari_router = [])
    {
        // Default: Tampilkan list
        $antrian_kak = $this->safeModelCall($this->model, 'getDashboardKAK', [], []);

        // Support feedback messages
        $success_msg = $_SESSION['flash_message'] ?? null;
        $error_msg = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan Usulan KAK',
            'antrian_kak' => $antrian_kak ?? [],
            'success_message' => $success_msg,
            'error_message' => $error_msg
        ]);

        $this->view('pages/admin/pengajuan_usulan', $data, 'admin');
    }

    /**
     * Detail Pengajuan Usulan
     */
    public function detail($id, $data_dari_router = [])
    {
        $kegiatan = $this->safeModelCall($this->model, 'getDetailKegiatan', [$id], null);

        if (!$kegiatan) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'error',
                'Data tidak ditemukan'
            );
        }

        // Ambil data pendukung
        $rab_data = $this->safeModelCall($this->model, 'getRABByKAK', [$kegiatan['kakId'] ?? 0], []);
        $indikator_data = $this->safeModelCall($this->model, 'getIndikatorByKAK', [$kegiatan['kakId'] ?? 0], []);
        $tahapan_data = $this->safeModelCall($this->model, 'getTahapanByKAK', [$kegiatan['kakId'] ?? 0], []);

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Pengajuan - ' . ($kegiatan['namaKegiatan'] ?? 'Unknown'),
            'kegiatan' => $kegiatan,
            'rab_data' => $rab_data,
            'indikator_data' => $indikator_data,
            'tahapan_data' => $tahapan_data,
            'back_url' => '/docutrack/public/admin/pengajuan-usulan'
        ]);

        $this->view('pages/admin/pengajuan_usulan_detail', $data, 'admin');
    }

    /**
     * Edit Pengajuan Usulan (form edit)
     */
    public function edit($id, $data_dari_router = [])
    {
        $kegiatan = $this->safeModelCall($this->model, 'getDetailKegiatan', [$id], null);

        if (!$kegiatan) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'error',
                'Data tidak ditemukan'
            );
        }

        // Tampilkan form edit
        $data = array_merge($data_dari_router, [
            'title' => 'Edit Pengajuan - ' . ($kegiatan['namaKegiatan'] ?? 'Unknown'),
            'kegiatan' => $kegiatan,
            'back_url' => '/docutrack/public/admin/pengajuan-usulan'
        ]);

        $this->view('pages/admin/pengajuan_usulan_edit', $data, 'admin');
    }

    /**
     * Update Pengajuan Usulan (proses form edit)
     */
    public function update($id)
    {
 // Changed to public
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'error',
                'Method not allowed'
            );
        }

        // TODO: Implementasi update logic di Model
        // $result = $this->model->updatePengajuan($id, $_POST);

        $this->redirectWithMessage(
            '/docutrack/public/admin/pengajuan-usulan',
            'success',
            'Data berhasil diupdate'
        );
    }

    /**
     * Delete Pengajuan Usulan
     */
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Removed !isset($_GET['confirm'])
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'error',
                'Konfirmasi delete diperlukan'
            );
        }

        $result = $this->safeModelCall($this->model, 'softDeleteKegiatan', [$id], false);

        if ($result) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'success',
                'Data berhasil dihapus (soft-delete)'
            );
        } else {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'error',
                'Gagal menghapus data'
            );
        }
    }

    /**
     * Store new pengajuan
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/admin/pengajuan-usulan');
            exit;
        }

        // Validasi file upload jika ada
        if (isset($_FILES['surat_pengantar']) && $_FILES['surat_pengantar']['error'] === UPLOAD_ERR_OK) {
            require_once '../src/helpers/security_helper.php';

            $validation = validateFileUpload($_FILES['surat_pengantar'], [
                'allowed_types' => ['application/pdf'],
                'max_size' => 2 * 1024 * 1024, // 2MB
                'allowed_extensions' => ['pdf']
            ]);

            if (!$validation['valid']) {
                $this->redirectWithMessage(
                    '/docutrack/public/admin/pengajuan-usulan',
                    'error',
                    $validation['error']
                );
            }
        }

        $berhasil = $this->safeModelCall($this->model, 'simpanPengajuan', [$_POST], false);

        if ($berhasil) {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'success',
                'Pengajuan berhasil disimpan'
            );
        } else {
            $this->redirectWithMessage(
                '/docutrack/public/admin/pengajuan-usulan',
                'error',
                'Gagal menyimpan pengajuan'
            );
        }
    }
}
