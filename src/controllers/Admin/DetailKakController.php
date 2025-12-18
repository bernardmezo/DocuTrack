<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Admin\AdminModel;
use App\Services\PdfService;

class DetailKakController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new AdminModel($this->db);
    }

    public function show($id, $data_dari_router = [])
    {
        // Get ref from query parameter (e.g., ?ref=dashboard)
        $ref = $_GET['ref'] ?? 'kegiatan';
        
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';


        $dataDB = $this->model->getDetailKegiatan($id); // Use $this->model

        if (!$dataDB) {
            echo "Kegiatan dengan ID $id tidak ditemukan.";
            return;
        }

        $kakId = $dataDB['kakId'];

        $indikator  = $this->model->getIndikatorByKAK($kakId);
        $tahapan    = $this->model->getTahapanByKAK($kakId);
        $rab        = $this->model->getRABByKAK($kakId);
        $komentar   = $this->model->getKomentarTerbaru($id);
        $komentarPenolakan = $this->model->getKomentarPenolakan($id);

        $tahapan_string = "";
        foreach ($tahapan as $index => $tahap) {
            $tahapan_string .= ($index + 1) . ". " . $tahap . "\n";
        }

        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];

        // Extract data kegiatan dengan null coalescing untuk keamanan data
        // Note: Query sudah return alias yang tepat dari JOIN tbl_user
        $kegiatan_data = [
            'kegiatanId' => $id, // Tambahkan ID untuk form edit
            'nama_pengusul' => $dataDB['nama_pengusul'] ?? '-',          // dari u.nama (user yang buat kegiatan)
            'nim_pengusul' => $dataDB['nim_pelaksana'] ?? '-',           // dari k.nimPelaksana (NIM pelaksana)
            'nama_pelaksana' => $dataDB['nama_pelaksana'] ?? '-',        // dari k.pemilikKegiatan (nama pelaksana)
            'nama_penanggung_jawab' => $dataDB['nama_pj'] ?? '-',        // dari k.namaPJ (nama PJ)
            'nip_penanggung_jawab' => $dataDB['nim_pj'] ?? '-',          // dari k.nip (NIP PJ)
            'jurusan' => $dataDB['jurusanPenyelenggara'] ?? '-',
            'nama_kegiatan' => $dataDB['namaKegiatan'] ?? 'Tidak ada judul',
            'gambaran_umum' => $dataDB['gambaranUmum'] ?? '-',
            'penerima_manfaat' => $dataDB['penerimaManfaat'] ?? '-',
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'] ?? '-',
            'tahapan_kegiatan' => $tahapan_string,
            'file_surat_pengantar' => $dataDB['file_surat_pengantar'] ?? null,
            'tanggal_mulai' => $dataDB['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $dataDB['tanggal_selesai'] ?? null
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'),
            'user_role' => $_SESSION['user_role'] ?? 'admin',

            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,

            'kode_mak' => $dataDB['buktiMAK'] ?? '-',
            'komentar_revisi' => $komentar,
            'komentar_penolakan' => $komentarPenolakan,

            // Generate URL untuk surat pengantar (jika ada)
            'surat_pengantar_url' => !empty($dataDB['file_surat_pengantar'])
                ? '/docutrack/public/uploads/surat/' . basename($dataDB['file_surat_pengantar'])
                : null,

            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kak', $data, 'admin');
    }

    public function downloadPdf($id)
    {
        try {
            // Ambil data kegiatan
            $dataDB = $this->model->getDetailKegiatan($id);

            if (!$dataDB) {
                http_response_code(404);
                die("Kegiatan dengan ID $id tidak ditemukan.");
            }

            $kakId = $dataDB['kakId'];

            // Ambil data pendukung
            $indikator  = $this->model->getIndikatorByKAK($kakId);
            $tahapan    = $this->model->getTahapanByKAK($kakId);
            $rab        = $this->model->getRABByKAK($kakId);

            // Format tahapan
            $tahapan_string = "";
            foreach ($tahapan as $index => $tahap) {
                $tahapan_string .= ($index + 1) . ". " . $tahap . "\n";
            }

            $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];

            // Prepare data untuk PDF
            $kegiatan_data = [
                'nama_pengusul' => $dataDB['nama_pengusul'] ?? '-',
                'nim_pengusul' => $dataDB['nim_pelaksana'] ?? '-',
                'nama_pelaksana' => $dataDB['nama_pelaksana'] ?? '-',
                'nama_penanggung_jawab' => $dataDB['nama_pj'] ?? '-',
                'nip_penanggung_jawab' => $dataDB['nim_pj'] ?? '-',
                'jurusan' => $dataDB['jurusanPenyelenggara'] ?? '-',
                'prodi' => $dataDB['prodi'] ?? '-', // Tambahkan prodi
                'nama_kegiatan' => $dataDB['namaKegiatan'] ?? 'Tidak ada judul',
                'gambaran_umum' => $dataDB['gambaranUmum'] ?? '-',
                'penerima_manfaat' => $dataDB['penerimaManfaat'] ?? '-',
                'metode_pelaksanaan' => $dataDB['metodePelaksanaan'] ?? '-',
                'tahapan_kegiatan' => $tahapan_string,
                'tanggal_mulai' => $dataDB['tanggal_mulai'] ?? null,
                'tanggal_selesai' => $dataDB['tanggal_selesai'] ?? null
            ];

            // Hitung grand total RAB
            $grand_total_rab = 0;
            foreach ($rab as $items) {
                if (is_array($items)) {
                    foreach ($items as $item) {
                        $vol1 = $item['vol1'] ?? 0;
                        $vol2 = $item['vol2'] ?? 1;
                        $harga = $item['harga'] ?? 0;
                        $grand_total_rab += ($vol1 * $vol2 * $harga);
                    }
                }
            }

            // Data untuk template PDF
            $pdfData = [
                'kegiatan_data' => $kegiatan_data,
                'iku_data' => $iku_array,
                'indikator_data' => $indikator,
                'rab_data' => $rab,
                'grand_total_rab' => $grand_total_rab,
                'kode_mak' => $dataDB['buktiMAK'] ?? '-',
                'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'),
                'pdf_title' => 'KAK - ' . $dataDB['namaKegiatan'],
                'pdf_author' => 'DocuTrack System'
            ];

            // Generate PDF
            $pdfService = new PdfService();
            
            // Path yang benar ke template
            $templatePath = dirname(__DIR__) . '/../views/pdf/kak_template.php';
            
            // Debug: Cek apakah file ada
            if (!file_exists($templatePath)) {
                throw new \Exception("Template PDF tidak ditemukan di: " . $templatePath);
            }
            
            // Nama file: KAK_{nama_kegiatan}_{tanggal}.pdf
            $namaKegiatan = preg_replace('/[^a-zA-Z0-9_-]/', '_', $dataDB['namaKegiatan']);
            $filename = 'KAK_' . substr($namaKegiatan, 0, 30) . '_' . date('Ymd') . '.pdf';
            
            // Konfigurasi mPDF
            $config = [
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_left' => 25,
                'margin_right' => 25,
                'margin_top' => 25,
                'margin_bottom' => 25,
                'orientation' => 'P'
            ];
            
            // Mode 'D' = Download
            $pdfService->generate($templatePath, $pdfData, $filename, 'D', $config);
            
        } catch (\Exception $e) {
            // Log error untuk debugging
            error_log("PDF Generation Error: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            
            http_response_code(500);
            
            // Tampilkan error detail di development mode
            if (getenv('APP_ENV') === 'development') {
                echo "<h1>Error Generating PDF</h1>";
                echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . " on line " . $e->getLine() . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                echo "Terjadi kesalahan saat membuat PDF. Silakan hubungi administrator.";
            }
        }
    }

    /**
     * Form edit usulan untuk revisi
     */
    public function editUsulan($id, $data_dari_router = [])
    {
        $ref = $_GET['ref'] ?? 'kegiatan';
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/detail-kak/' . $id;

        $dataDB = $this->model->getDetailKegiatan($id);

        if (!$dataDB) {
            $_SESSION['flash_error'] = 'Data kegiatan tidak ditemukan.';
            header('Location: ' . $back_url);
            exit;
        }

        // Cek apakah status revisi
        $statusLower = strtolower($dataDB['status_text'] ?? '');
        if ($statusLower !== 'revisi') {
            $_SESSION['flash_error'] = 'Usulan ini tidak dalam status revisi.';
            header('Location: ' . $back_url);
            exit;
        }

        $kakId = $dataDB['kakId'];

        $indikator  = $this->model->getIndikatorByKAK($kakId);
        $tahapan    = $this->model->getTahapanByKAK($kakId);
        $rab        = $this->model->getRABByKAK($kakId);
        $komentar   = $this->model->getKomentarTerbaru($id);

        $tahapan_string = "";
        foreach ($tahapan as $index => $tahap) {
            $tahapan_string .= ($index + 1) . ". " . $tahap . "\n";
        }

        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];

        $kegiatan_data = [
            'kegiatanId' => $id,
            'kakId' => $kakId,
            'nama_pengusul' => $dataDB['nama_pengusul'] ?? '',
            'nim_pengusul' => $dataDB['nim_pelaksana'] ?? '',
            'nama_pelaksana' => $dataDB['nama_pelaksana'] ?? '',
            'nama_penanggung_jawab' => $dataDB['nama_pj'] ?? '',
            'nip_penanggung_jawab' => $dataDB['nim_pj'] ?? '',
            'jurusan' => $dataDB['jurusanPenyelenggara'] ?? '',
            'prodi' => $dataDB['prodi'] ?? '',
            'nama_kegiatan' => $dataDB['namaKegiatan'] ?? '',
            'gambaran_umum' => $dataDB['gambaranUmum'] ?? '',
            'penerima_manfaat' => $dataDB['penerimaManfaat'] ?? '',
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'] ?? '',
            'tahapan_kegiatan' => $tahapan_string,
            'tanggal_mulai' => $dataDB['tanggal_mulai'] ?? '',
            'tanggal_selesai' => $dataDB['tanggal_selesai'] ?? ''
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Edit Usulan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => 'Revisi',
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            'komentar_revisi' => $komentar,
            'tahapan_array' => $tahapan,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/edit_usulan', $data, 'admin');
    }

    /**
     * Resubmit usulan setelah revisi
     */
    public function resubmitUsulan($id)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['flash_error'] = 'Invalid request method.';
            header('Location: /docutrack/public/admin/detail-kak/' . $id);
            exit;
        }

        try {
            $dataDB = $this->model->getDetailKegiatan($id);

            if (!$dataDB) {
                throw new \Exception('Data kegiatan tidak ditemukan.');
            }

            // Cek apakah status revisi
            $statusLower = strtolower($dataDB['status_text'] ?? '');
            if ($statusLower !== 'revisi') {
                throw new \Exception('Usulan ini tidak dalam status revisi.');
            }

            // Update usulan dengan data baru
            $result = $this->model->updateUsulanRevisi($id, $_POST);

            if ($result) {
                $_SESSION['flash_message'] = 'Usulan berhasil diperbarui dan dikirim ulang untuk verifikasi.';
                header('Location: /docutrack/public/admin/dashboard');
            } else {
                throw new \Exception('Gagal memperbarui usulan.');
            }
        } catch (\Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            header('Location: /docutrack/public/admin/detail-kak/' . $id . '/edit-usulan');
        }
        exit;
    }
}
