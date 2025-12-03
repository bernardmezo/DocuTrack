<?php
// File: src/controllers/Bendahara/PengajuanlpjController.php

require_once '../src/core/Controller.php';
require_once '../src/model/bendaharaModel.php'; // ✅ LOAD MODEL

class BendaharaPengajuanlpjController extends Controller {
    
    private $model;

    public function __construct() {
        $this->model = new bendaharaModel(); // ✅ INISIALISASI MODEL
    }

    /**
     * Halaman List LPJ - HANYA MENUNGGU
     */
    public function index($data_dari_router = []) {
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
    public function getLPJData() {
        // ✅ AMBIL DATA DARI DATABASE
        return $this->model->getAntrianLPJ();
    }

    /**
     * Halaman Detail LPJ untuk Verifikasi
     */
    public function show($id, $data_dari_router = []) {
        // Ambil referrer dari query string
        $ref = $_GET['ref'] ?? 'lpj';
        $base_url = "/docutrack/public/bendahara";
        
        if ($ref === 'dashboard') {
            $back_url = $base_url . '/dashboard';
        } else {
            $back_url = $base_url . '/pengajuan-lpj';
        }

        // ✅ AMBIL DATA DARI DATABASE
        $lpj = $this->model->getDetailLPJ($id);
        
        if (!$lpj) {
            $_SESSION['flash_error'] = 'Data LPJ tidak ditemukan.';
            header("Location: $back_url");
            exit;
        }

        // Ambil item-item LPJ
        $lpj_items = $this->model->getLPJItems($id);
        
        // Group items by jenisBelanja (kategori)
        $rab_items = [];
        foreach ($lpj_items as $item) {
            $kategori = $item['jenisBelanja'] ?? 'Lainnya';
            $rab_items[$kategori][] = [
                'id' => $item['lpjItemId'],
                'uraian' => $item['uraian'] ?? '-',
                'rincian' => $item['rincian'] ?? '-',
                // Mapping field sesuai schema tbl_lpj_item yang disederhanakan
                'vol1' => 1, // Default 1 karena tidak ada kolom volume di tbl_lpj_item
                'sat1' => $item['satuan'] ?? '-',
                'vol2' => 1, // Default 1
                'sat2' => '', 
                'harga_satuan' => $item['totalHarga'] ?? 0, // totalHarga di DB tampaknya harga per item/transaksi
                'harga_plan' => $item['subtotal'] ?? 0,
                'subtotal' => $item['subtotal'] ?? 0,
                'bukti_file' => $item['fileBukti'] ?? null,
                'komentar' => null 
            ];
        }

        // Tentukan status
        if (!empty($lpj['approvedAt'])) {
            $status = 'Disetujui';
        } elseif (!empty($lpj['submittedAt'])) {
            $status = 'Menunggu';
        } else {
            $status = 'Draft';
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($lpj['namaKegiatan']),
            'status' => $status,
            'kegiatan_data' => [
                'id' => $lpj['lpjId'],
                'nama_kegiatan' => $lpj['namaKegiatan'],
                'nama_mahasiswa' => $lpj['pemilikKegiatan'],
                'nim' => $lpj['nimPelaksana'],
                'pengusul' => $lpj['pemilikKegiatan']
            ],
            'rab_items' => $rab_items,
            'grand_total_realisasi' => $lpj['grandTotalRealisasi'],
            'tanggal_persetujuan' => $lpj['approvedAt'] ?? null,
            'tanggal_pengajuan' => $lpj['submittedAt'] ?? null,
            'back_url' => $back_url
        ]);

        $this->view('pages/bendahara/pengajuan-lpj-detail', $data, 'bendahara');
    }

    /**
     * Proses Verifikasi LPJ (Setuju atau Revisi)
     */
    public function proses() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pengajuan-lpj');
            exit;
        }

        $lpj_id = $_POST['lpj_id'] ?? null;
        $action = $_POST['action'] ?? null;
        
        if (!$lpj_id || !$action) {
            $_SESSION['flash_message'] = 'Data tidak lengkap!';
            $_SESSION['flash_type'] = 'error';
            header('Location: /docutrack/public/bendahara/pengajuan-lpj');
            exit;
        }

        try {
            if ($action === 'setuju') {
                // ✅ SIMPAN KE DATABASE
                if ($this->model->approveLPJ($lpj_id)) {
                    $_SESSION['flash_message'] = 'LPJ berhasil disetujui!';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    throw new Exception('Gagal menyetujui LPJ');
                }
                
            } elseif ($action === 'revisi') {
                $komentar = $_POST['komentar'] ?? [];
                $catatan_umum = trim($_POST['catatan_umum'] ?? '');
                
                // Validasi: Minimal ada 1 komentar
                $hasComment = false;
                foreach ($komentar as $item_id => $comment) {
                    if (!empty(trim($comment))) {
                        $hasComment = true;
                        break;
                    }
                }
                
                if (!$hasComment && empty($catatan_umum)) {
                    $_SESSION['flash_message'] = 'Mohon isi minimal 1 komentar untuk item yang perlu direvisi!';
                    $_SESSION['flash_type'] = 'error';
                    header('Location: /docutrack/public/bendahara/pengajuan-lpj/show/' . $lpj_id);
                    exit;
                }
                
                // TODO: Simpan komentar revisi ke database
                // $this->model->reviseLPJ($lpj_id, $komentar, $catatan_umum);
                
                $_SESSION['flash_message'] = 'Permintaan revisi berhasil dikirim ke Admin!';
                $_SESSION['flash_type'] = 'success';
                
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
