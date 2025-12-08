<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\KegiatanService;
use App\Services\WorkflowService;

class PengajuanKegiatanController extends Controller
{
    private $kegiatanService;
    private $workflowService;

    public function __construct()
    {
        parent::__construct();
        $this->kegiatanService = new KegiatanService($this->db);
        $this->workflowService = new WorkflowService($this->db);
    }

    public function index($data_dari_router = [])
    {
        // Get kegiatan at Admin position with Approved status (ready for rincian)
        $list_kegiatan_disetujui = $this->kegiatanService->getKegiatanByStatus(
            WorkflowService::POSITION_ADMIN,
            WorkflowService::STATUS_DISETUJUI
        );

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan Kegiatan',
            'list_kegiatan' => $list_kegiatan_disetujui,
            'workflow' => $this->workflowService
        ]);

        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
    }

    public function show($id, $data_dari_router = [])
    {
        // Validasi ID
        $kegiatanId = (int)$id;
        if ($kegiatanId <= 0) {
            error_log("ERROR PengajuanKegiatanController::show - Invalid ID: {$id}");
            $_SESSION['flash_error'] = 'ID kegiatan tidak valid.';
            header('Location: /docutrack/public/admin/pengajuan-kegiatan');
            exit;
        }

        $kegiatanDB = $this->kegiatanService->getDetailLengkap($kegiatanId);

        if (!$kegiatanDB) {
            error_log("ERROR PengajuanKegiatanController::show - Kegiatan ID {$kegiatanId} not found in database");
            $_SESSION['flash_error'] = 'Kegiatan tidak ditemukan atau belum memiliki data KAK lengkap.';
            header('Location: /docutrack/public/admin/pengajuan-kegiatan');
            exit;
        }

        $data = [
             'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatanDB['namaKegiatan']),
             'kegiatan_data' => $kegiatanDB,
             'kegiatan_id' => $kegiatanId,
             'namaKeg' => $kegiatanDB['namaKegiatan'] ?? 'N/A',
             'status' => $kegiatanDB['status_text'] ?? 'Disetujui',
             'user_role' => 'admin',
             'komentar_revisi' => [],
             'komentar_penolakan' => '',
             'iku_data' => $kegiatanDB['indikator_list'] ?? [],
             'indikator_data' => $kegiatanDB['indikator_data'] ?? [],
             'workflow' => $this->workflowService,
             'workflow_progress' => $this->workflowService->getProgress($kegiatanDB),
             'rab_data' => $kegiatanDB['rab_data'] ?? [],
             'kode_mak' => $kegiatanDB['kodeMak'] ?? '',
             'back_url' => '/docutrack/public/admin/pengajuan-kegiatan',
             'surat_pengantar_url' => !empty($kegiatanDB['suratPengantar']) 
                 ? '/docutrack/public/assets/uploads/' . $kegiatanDB['suratPengantar'] 
                 : '',
        ];

        if (($_GET['mode'] ?? '') === 'rincian') {
            $this->view('pages/admin/detail_kegiatan', $data, 'app');
        } else {
            $this->view('pages/admin/detail_kak', $data, 'app');
        }
    }
}
