<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\KegiatanService;
use App\Services\WorkflowService;
use Mpdf\Mpdf;
use Exception;

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

        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'admin');
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
            $this->view('pages/admin/detail_kegiatan', $data, 'admin');
        } else {
            $this->view('pages/admin/detail_kak', $data, 'admin');
        }
    }

    /**
     * Generate dan download PDF KAK
     * 
     * @param int $id - ID Kegiatan
     */
    public function downloadPDF($id)
    {
        error_log("=== downloadPDF START ===");
        
        try {
            $kegiatanId = (int)$id;
            if ($kegiatanId <= 0) {
                throw new Exception("ID kegiatan tidak valid");
            }

            // Ambil data kegiatan lengkap
            $kegiatanDB = $this->kegiatanService->getDetailLengkap($kegiatanId);
            
            if (!$kegiatanDB) {
                throw new Exception("Kegiatan dengan ID {$kegiatanId} tidak ditemukan");
            }

            // Prepare data untuk template
            $data = [
                // Template Data
                'kegiatan_data' => [
                    'nama_pengusul' => $kegiatanDB['pemilikKegiatan'] ?? '-',
                    'nim_pengusul' => $kegiatanDB['nimPelaksana'] ?? '-',
                    'jurusan' => $kegiatanDB['jurusanPenyelenggara'] ?? '-',
                    'prodi' => $kegiatanDB['prodiPenyelenggara'] ?? '-',
                    'nama_kegiatan' => $kegiatanDB['namaKegiatan'] ?? 'Tidak ada judul',
                    'gambaran_umum' => $kegiatanDB['gambaranUmum'] ?? '-',
                    'penerima_manfaat' => $kegiatanDB['penerimaMaanfaat'] ?? '-',
                    'metode_pelaksanaan' => $kegiatanDB['metodePelaksanaan'] ?? '-',
                    'tahapan_kegiatan' => $kegiatanDB['tahapanKegiatan'] ?? '-',
                    'tanggal_mulai' => $kegiatanDB['tanggalMulai'] ?? '',
                    'tanggal_selesai' => $kegiatanDB['tanggalSelesai'] ?? ''
                ],
                'iku_data' => $kegiatanDB['iku_array'] ?? [],
                'indikator_data' => $kegiatanDB['indikator_data'] ?? [],
                'rab_data' => $kegiatanDB['rab_data'] ?? [],
                'kode_mak' => $kegiatanDB['kodeMak'] ?? '',
                
                // Metadata
                'pdf_title' => 'KAK - ' . ($kegiatanDB['namaKegiatan'] ?? 'Untitled'),
                'pdf_author' => 'DocuTrack System'
            ];

            // Render template path
            $templatePath = __DIR__ . '/../../views/pdf/kak_template.php';
            
            // Generate filename
            $safe_filename = preg_replace('/[^A-Za-z0-9\-]/', '_', $kegiatanDB['namaKegiatan'] ?? 'doc');
            $filename = 'KAK_' . $safe_filename . '_' . date('Ymd') . '.pdf';

            // Init Service
            $pdfService = new \App\Services\PdfService();
            $pdfService->generate($templatePath, $data, $filename, 'D');
            
            error_log("=== downloadPDF END (SUCCESS) ===");
            exit;
            
        } catch (Exception $e) {
            error_log("ERROR downloadPDF: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Gagal generate PDF: ' . $e->getMessage();
            header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $id);
            exit;
        }
    }
}
