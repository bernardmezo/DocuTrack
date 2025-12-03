<?php
// File: src/controllers/Bendahara/PencairandanaController.php

require_once '../src/core/Controller.php';
require_once '../src/model/bendaharaModel.php';
require_once '../src/helpers/logger_helper.php';

class BendaharaPencairandanaController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new bendaharaModel();
    }

    /**
     * Halaman List Pencairan Dana
     */
    public function index($data_dari_router = []) {
        
        $stats = $this->safeModelCall($this->model, 'getDashboardStats', [], [
            'total' => 0,
            'menunggu' => 0,
            'dicairkan' => 0
        ]);
        
        $list_antrian = $this->safeModelCall($this->model, 'getAntrianPencairan', [], []);
        $jurusan_list = $this->safeModelCall($this->model, 'getListJurusan', [], []);

        // Support untuk feedback messages dari proses
        $success_msg = $_SESSION['flash_message'] ?? null;
        $error_msg = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_message'], $_SESSION['flash_error']);

        $data = array_merge($data_dari_router, [
            'title' => 'List Pencairan Dana',
            'stats' => [
                'total' => $stats['total'] ?? 0,
                'menunggu' => $stats['menunggu'] ?? 0,
                'dicairkan' => $stats['dicairkan'] ?? 0
            ],
            'list_kak' => $list_antrian ?? [],
            'jurusan_list' => $jurusan_list ?? [],
            'success_message' => $success_msg,
            'error_message' => $error_msg
        ]);

        $this->view('pages/bendahara/pencairan-dana', $data, 'bendahara');
    }

    /**
     * Halaman Detail Pencairan
     */
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'pencairan-dana';
        $base_url = "/docutrack/public/bendahara";
        $back_url = $base_url . '/' . $ref;

        $kegiatan = $this->model->getDetailPencairan($id);
        
        if (!$kegiatan) {
            $_SESSION['flash_error'] = 'Data tidak ditemukan.';
            header('Location: ' . $back_url);
            exit;
        }
        
        $rab_data = $this->model->getRABByKegiatan($id);
        $iku_data = $this->model->getIKUByKegiatan($id);
        $indikator_data = $this->model->getIndikatorByKegiatan($id);
        $tahapan = $this->model->getTahapanByKegiatan($id);
        
        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }
        
        $is_sudah_dicairkan = !empty($kegiatan['tanggalPencairan']);
        
        if ($is_sudah_dicairkan) {
            $status_display = 'Dana Diberikan';
        } else {
            $status_display = 'Menunggu';
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Pencairan - ' . htmlspecialchars($kegiatan['namaKegiatan']),
            'id' => $id,
            'status' => $status_display,
            
            'nama_kegiatan' => $kegiatan['namaKegiatan'],
            'nama_mahasiswa' => $kegiatan['pemilikKegiatan'],
            'nim' => $kegiatan['nimPelaksana'],
            'jurusan' => $kegiatan['jurusanPenyelenggara'] ?? '-',
            'prodi' => $kegiatan['prodiPenyelenggara'] ?? '-',
            'tanggal_pengajuan' => $kegiatan['createdAt'],
            'kode_mak' => $kegiatan['buktiMAK'] ?? '-',
            
            'kegiatan_data' => [
                'id' => $id,
                'nama_pengusul' => $kegiatan['pemilikKegiatan'] ?? '-',
                'nim_pengusul' => $kegiatan['nimPelaksana'] ?? '-',
                'nama_penanggung_jawab' => $kegiatan['namaPJ'] ?? '-',
                'nip_penanggung_jawab' => $kegiatan['nip'] ?? '-',
                'nama_kegiatan' => $kegiatan['namaKegiatan'] ?? '-',
                'gambaran_umum' => $kegiatan['gambaranUmum'] ?? '-',
                'penerima_manfaat' => $kegiatan['penerimaMaanfaat'] ?? '-',
                'metode_pelaksanaan' => $kegiatan['metodePelaksanaan'] ?? '-',
                'tahapan_kegiatan' => $tahapan_string ?: '-',
                'tanggal_mulai' => $kegiatan['tanggalMulai'] ?? '',
                'tanggal_selesai' => $kegiatan['tanggalSelesai'] ?? ''
            ],
            
            'iku_data' => $iku_data,
            'indikator_data' => $indikator_data,
            
            'rab_data' => $rab_data,
            'anggaran_disetujui' => $kegiatan['total_rab'] ?? 0,
            
            'surat_pengantar_url' => !empty($kegiatan['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $kegiatan['suratPengantar'] : '',
            
            'jumlah_dicairkan' => $kegiatan['jumlahDicairkan'] ?? 0,
            'tanggal_pencairan' => $kegiatan['tanggalPencairan'] ?? null,
            'metode_pencairan' => $kegiatan['metodePencairan'] ?? 'uang_muka',
            'catatan_bendahara' => $kegiatan['catatanBendahara'] ?? '',
            
            'back_url' => $back_url,
            'back_text' => 'Kembali'
        ]);

        $this->view('pages/bendahara/pencairan-dana-detail', $data, 'bendahara');
    }

    /**
     * Proses Pencairan Dana (Penuh atau Bertahap) dengan Audit Logging.
     *
     * Method ini menangani dua jenis pencairan:
     * 1. Pencairan Penuh: Seluruh dana dicairkan sekaligus
     * 2. Pencairan Bertahap: Dana dicairkan dalam beberapa tahap dengan persentase tertentu
     *
     * Security Measures:
     * - CSRF token validation (should be implemented in view)
     * - Input sanitization dan type casting
     * - Transaction-based processing
     * - Comprehensive error logging
     *
     * @return void Redirect ke halaman pencairan dengan flash message
     */
    public function proses()
    {
        // Security: Hanya accept POST request
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        $kak_id = (int) ($_POST['kak_id'] ?? 0);
        $action = $_POST['action'] ?? null;
        $userId = (int) ($_SESSION['user_id'] ?? 0);
        
        // Validasi input minimal
        if (!$kak_id || !$action) {
            $_SESSION['flash_error'] = 'Data tidak lengkap!';
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        try {
            if ($action === 'cairkan') {
                $metode_pencairan = $_POST['metode_pencairan'] ?? 'penuh';
                
                // PENCAIRAN PENUH
                if ($metode_pencairan === 'penuh') {
                    $jumlah_dicairkan = (float) ($_POST['jumlah_dicairkan'] ?? 0);
                    $catatan = trim($_POST['catatan'] ?? '');
                    $tenggat_lpj = $_POST['tenggat_lpj'] ?? null;
                    
                    // Validasi jumlah
                    if ($jumlah_dicairkan <= 0) {
                        throw new Exception('Jumlah pencairan harus lebih dari 0');
                    }
                    
                    // Calculate batas LPJ: default 14 hari dari sekarang
                    if (empty($tenggat_lpj)) {
                        $tenggat_lpj = date('Y-m-d', strtotime('+14 days'));
                    } else {
                        // Validasi: Tenggat tidak boleh di masa lalu
                        if (strtotime($tenggat_lpj) < strtotime('today')) {
                            throw new Exception('Tenggat LPJ tidak boleh di masa lalu.');
                        }
                    }
                    
                    // Proses pencairan penuh
                    if ($this->model->prosesPencairan($kak_id, $jumlah_dicairkan, 'penuh', $catatan, $tenggat_lpj)) {
                        // Log aktivitas
                        if (function_exists('logPencairan')) {
                            logPencairan($userId, $kak_id, $jumlah_dicairkan, 'penuh', $catatan);
                        }
                        
                        $_SESSION['flash_message'] = 'Dana berhasil dicairkan sebesar Rp ' 
                            . number_format($jumlah_dicairkan, 0, ",", ".") 
                            . '. Batas pengumpulan LPJ: ' . date('d/m/Y', strtotime($tenggat_lpj));
                        $_SESSION['flash_type'] = 'success';
                    } else {
                        throw new Exception('Gagal memproses pencairan penuh');
                    }
                }
                // PENCAIRAN BERTAHAP
                elseif ($metode_pencairan === 'bertahap') {
                    $total_anggaran = (float) ($_POST['total_anggaran'] ?? 0);
                    $jumlah_tahap = (int) ($_POST['jumlah_tahap'] ?? 0);
                    
                    // Validasi jumlah tahap (2-5 tahap)
                    if ($jumlah_tahap < 2 || $jumlah_tahap > 5) {
                        throw new Exception('Jumlah tahap harus antara 2-5');
                    }
                    
                    if ($total_anggaran <= 0) {
                        throw new Exception('Total anggaran tidak valid');
                    }
                    
                    // Build array tahap data
                    $tahapData = [];
                    $totalPersentase = 0;
                    
                    for ($i = 1; $i <= $jumlah_tahap; $i++) {
                        $tanggal = $_POST["tanggal_tahap_{$i}"] ?? null;
                        $persentase = (float) ($_POST["persentase_tahap_{$i}"] ?? 0);
                        
                        // Validasi setiap tahap
                        if (empty($tanggal)) {
                            throw new Exception("Tanggal tahap {$i} wajib diisi");
                        }
                        
                        if ($persentase <= 0 || $persentase > 100) {
                            throw new Exception("Persentase tahap {$i} tidak valid (harus 1-100)");
                        }
                        
                        // Validasi tanggal tidak di masa lalu (kecuali tahap 1 boleh hari ini)
                        $minDate = ($i === 1) ? 'today' : 'tomorrow';
                        if (strtotime($tanggal) < strtotime($minDate)) {
                            throw new Exception("Tanggal tahap {$i} tidak boleh di masa lalu");
                        }
                        
                        $tahapData[] = [
                            'tanggal' => $tanggal,
                            'persentase' => $persentase
                        ];
                        
                        $totalPersentase += $persentase;
                    }
                    
                    // Validasi: Total persentase harus 100%
                    if (abs($totalPersentase - 100) > 0.01) { // Allow small floating point diff
                        throw new Exception("Total persentase harus 100% (saat ini: {$totalPersentase}%)");
                    }
                    
                    // Proses pencairan bertahap
                    $this->model->prosesPencairanBertahap($kak_id, $total_anggaran, $tahapData);
                    
                    // Log aktivitas
                    if (function_exists('writeLog')) {
                        writeLog($userId, 'PENCAIRAN_BERTAHAP', 
                            "Pencairan bertahap {$jumlah_tahap} tahap untuk kegiatan ID: {$kak_id}",
                            'kegiatan', $kak_id);
                    }
                    
                    // Calculate batas LPJ dari tanggal terakhir
                    $tanggalTerakhir = end($tahapData)['tanggal'];
                    $batasLpj = date('d/m/Y', strtotime($tanggalTerakhir . ' +14 days'));
                    
                    $_SESSION['flash_message'] = "Pencairan bertahap berhasil dijadwalkan ({$jumlah_tahap} tahap). "
                        . "Batas pengumpulan LPJ: {$batasLpj}";
                    $_SESSION['flash_type'] = 'success';
                    
                } else {
                    throw new Exception('Metode pencairan tidak valid');
                }
                
            } elseif ($action === 'tolak') {
                $catatan = trim($_POST['catatan'] ?? '');
                
                if (empty($catatan)) {
                    $_SESSION['flash_error'] = 'Catatan penolakan wajib diisi!';
                    header('Location: /docutrack/public/bendahara/pencairan-dana/show/' . $kak_id);
                    exit;
                }
                
                // Log penolakan
                if (function_exists('writeLog')) {
                    writeLog($userId, 'PENCAIRAN_REJECT', 
                        "Menolak pencairan untuk kegiatan ID: {$kak_id}. Alasan: {$catatan}",
                        'kegiatan', $kak_id);
                }
                
                $_SESSION['flash_message'] = 'Pencairan ditolak.';
                $_SESSION['flash_type'] = 'warning';
                
            } else {
                throw new Exception('Action tidak valid');
            }

        } catch (Exception $e) {
            // Log error
            if (function_exists('writeLog')) {
                writeLog($userId, 'PENCAIRAN_ERROR', 
                    "Error proses pencairan kegiatan ID: {$kak_id} - " . $e->getMessage(),
                    'kegiatan', $kak_id);
            }
            
            error_log("PencairandanaController::proses() Error: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        header('Location: /docutrack/public/bendahara/pencairan-dana');
        exit;
    }
}
