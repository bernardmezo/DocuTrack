<?php

// File: src/controllers/Bendahara/PengajuanlpjController.php

namespace App\Controllers\Bendahara;

use App\Core\Controller;
use App\Services\BendaharaService;
use App\Services\LogStatusService; // Added
use Exception;

class PengajuanLpjController extends Controller
{
    private $model;
    private LogStatusService $logStatusService; // Added

    public function __construct()
    {
        parent::__construct();
        $this->model = new BendaharaService($this->db);
        $this->logStatusService = new LogStatusService($this->db); // Added
    }

    /**
     * Halaman List LPJ - HANYA MENUNGGU
     */
    public function index($data_dari_router = [])
    {
        // ✅ AMBIL DATA DARI DATABASE dengan type safety
        $list_lpj = $this->safeModelCall($this->model, 'getAntrianLPJ', [], []);

        // Get flash messages from session
        $success_msg = $_SESSION['flash_message'] ?? null;
        $error_msg = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan LPJ - Bendahara',
            'list_lpj' => $list_lpj ?? [],
            'success_message' => $success_msg,
            'error_message' => $error_msg
        ]);

        $this->view('pages/bendahara/pengajuan-lpj', $data, 'bendahara');
    }

    /**
     * Method untuk mendapatkan data LPJ (untuk Dashboard)
     */
    public function getLPJData()
    {
        // ✅ AMBIL DATA DARI DATABASE
        return $this->safeModelCall($this->model, 'getAntrianLPJ', [], []);
    }

    /**
     * Halaman Detail LPJ untuk Verifikasi
     */
    public function show($id, $data_dari_router = [])
    {
        // Ambil referrer dari query string
        $ref = $_GET['ref'] ?? 'lpj';
        $base_url = "/docutrack/public/bendahara";

        if ($ref === 'dashboard') {
            $back_url = $base_url . '/dashboard';
        } else {
            $back_url = $base_url . '/pengajuan-lpj';
        }

        // ✅ Ambil data LPJ dari database
        $lpj = $this->safeModelCall($this->model, 'getDetailLPJ', [$id], null);

        if (!$lpj) {
            $_SESSION['flash_error'] = 'Data LPJ tidak ditemukan.';
            header("Location: $back_url");
            exit;
        }

        // ✅ Ambil item-item LPJ
        $lpj_items = $this->safeModelCall($this->model, 'getLPJItems', [$id], []);

        // ✅ Group items by jenisBelanja (kategori)
        $rab_items = [];
        foreach ($lpj_items as $item) {
            $kategori = $item['jenisBelanja'] ?? 'Lainnya';

            $rab_items[$kategori][] = [
                'id' => $item['lpjItemId'],
                'uraian' => $item['uraian'] ?? '-',
                'rincian' => $item['rincian'] ?? '-',
                'vol1' => $item['vol1'] ?? '-',
                'sat1' => $item['sat1'] ?? '-',
                'vol2' => $item['vol2'] ?? '-',
                'sat2' => $item['sat2'] ?? '-',
                'harga_satuan' => $item['totalHarga'] ?? 0, // Harga per item
                'harga_plan' => $item['subTotal'] ?? 0,     // Total realisasi
                'subtotal' => $item['subTotal'] ?? 0,
                'bukti_file' => $item['fileBukti'] ?? null,
                'komentar' => $item['komentar'] ?? null
            ];
        }

        // ✅ Tentukan status
        $status = 'Draft';
        if (!empty($lpj['approvedAt'])) {
            $status = 'Disetujui';
        } elseif (!empty($lpj['submittedAt'])) {
            $status = 'Menunggu';
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($lpj['namaKegiatan']),
            'status' => $status,
            'kegiatan_data' => [
                'id' => $lpj['lpjId'],
                'nama_kegiatan' => $lpj['namaKegiatan'],
                'nama_mahasiswa' => $lpj['pemilikKegiatan'],
                'nim' => $lpj['nimPelaksana'],
                'prodi' => $lpj['prodiPenyelenggara'] ?? '-',
                'jurusan' => $lpj['jurusanPenyelenggara'] ?? '-',
                'pengusul' => $lpj['pemilikKegiatan']
            ],
            'rab_items' => $rab_items,
            'grand_total_realisasi' => $lpj['grandTotalRealisasi'] ?? 0,
            'tanggal_persetujuan' => $lpj['approvedAt'] ?? null,
            'tanggal_pengajuan' => $lpj['submittedAt'] ?? null,
            'tenggat_lpj' => $lpj['tenggatLpj'] ?? null,
            'back_url' => $back_url
        ]);

        $this->view('pages/bendahara/pengajuan-lpj-detail', $data, 'bendahara');
    }

    /**
     * Proses Verifikasi LPJ (Setuju atau Revisi)
     */
    public function proses()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pengajuan-lpj');
            exit;
        }

        $lpj_id = isset($_POST['lpj_id']) ? intval($_POST['lpj_id']) : 0;
        $action = $_POST['action'] ?? null;

        if (!$lpj_id || !$action) {
            $_SESSION['flash_message'] = 'Data tidak lengkap!';
            $_SESSION['flash_type'] = 'error';
            header('Location: /docutrack/public/bendahara/pengajuan-lpj');
            exit;
        }

        try {
            if ($action === 'setuju') {
                // ✅ APPROVE LPJ
                // Validasi dilakukan di model: LPJ harus sudah di-submit terlebih dahulu
                if ($this->safeModelCall($this->model, 'approveLPJ', [$lpj_id], false)) {
                    $_SESSION['flash_message'] = 'LPJ berhasil disetujui!';
                    $_SESSION['flash_type'] = 'success';

                    // --- Notifikasi ke Pengusul ---
                    $lpjData = $this->model->getDetailLPJ($lpj_id);
                    if ($lpjData && isset($lpjData['userId'])) {
                        $pengusulId = $lpjData['userId'];
                        $namaKegiatan = $lpjData['namaKegiatan'] ?? 'Kegiatan';
                        $pesan = "LPJ untuk kegiatan '{$namaKegiatan}' telah disetujui oleh Bendahara.";
                        $this->logStatusService->createNotification($pengusulId, 'APPROVAL', $pesan, $lpj_id, null, $lpjData['kegiatanId']);
                    }
                    // --- End Notifikasi ---
                } else {
                    throw new Exception('Gagal menyetujui LPJ. Pastikan LPJ sudah diajukan oleh pengusul terlebih dahulu.');
                }
            } elseif ($action === 'revisi') {
                $komentar = $_POST['komentar'] ?? [];
                $catatan_umum = trim($_POST['catatan_umum'] ?? '');

                // Validasi: Minimal ada 1 komentar
                $hasComment = false;
                foreach ($komentar as $kategori => $comment) {
                    if (!empty(trim($comment))) {
                        $hasComment = true;
                        break;
                    }
                }

                if (!$hasComment && empty($catatan_umum)) {
                    $_SESSION['flash_message'] = 'Mohon isi minimal 1 komentar untuk meminta revisi!';
                    $_SESSION['flash_type'] = 'error';
                    header('Location: /docutrack/public/bendahara/pengajuan-lpj/show/' . $lpj_id);
                    exit;
                }

                // ✅ IMPLEMENTASI: Simpan komentar revisi
                if ($this->safeModelCall($this->model, 'reviseLPJ', [$lpj_id, $komentar, $catatan_umum], false)) {
                    $_SESSION['flash_message'] = 'Permintaan revisi berhasil dikirim ke Admin!';
                    $_SESSION['flash_type'] = 'success';

                    // --- Notifikasi ke Pengusul ---
                    $lpjData = $this->model->getDetailLPJ($lpj_id);
                    if ($lpjData && isset($lpjData['userId'])) {
                        $pengusulId = $lpjData['userId'];
                        $namaKegiatan = $lpjData['namaKegiatan'] ?? 'Kegiatan';
                        $pesan = "LPJ untuk kegiatan '{$namaKegiatan}' memerlukan revisi. \nCatatan: " . ($catatan_umum ?: "Lihat detail LPJ untuk komentar item.");
                        $this->logStatusService->createNotification($pengusulId, 'REVISION', $pesan, $lpj_id, null, $lpjData['kegiatanId']);
                    }
                } else {
                    throw new Exception('Gagal memproses permintaan revisi.');
                }
            } else {
                throw new Exception('Action tidak valid');
            }
        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }

        header('Location: /docutrack/public/bendahara/pengajuan-lpj');
        exit;
    }
}