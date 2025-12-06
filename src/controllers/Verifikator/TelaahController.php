<?php
// File: src/controllers/Verifikator/TelaahController.php

require_once '../src/core/Controller.php';
require_once '../src/model/verifikatorModel.php';

class VerifikatorTelaahController extends Controller {
    /**
     * Menampilkan halaman daftar antrian telaah.
     */
    public function index($data_dari_router = []) {
        $model = new verifikatorModel($this->db);
        $all_usulan = $model->getDashboardKAK();

        $list_usulan = [];
        $jurusan_set = [];

        foreach ($all_usulan as $usulan) {
            $status_lower = strtolower($usulan['status']);
            if ($status_lower === 'menunggu' || $status_lower === 'telah direvisi') {
                $list_usulan[] = $usulan;
                if (!empty($usulan['jurusan'])) {
                    $jurusan_set[$usulan['jurusan']] = true;
                }
            }
        }

        usort($list_usulan, function ($a, $b) {
            $priority = ['telah direvisi' => 1, 'menunggu' => 2];
            $a_status = strtolower($a['status']);
            $b_status = strtolower($b['status']);
            $a_prio = $priority[$a_status] ?? 99;
            $b_prio = $priority[$b_status] ?? 99;
            if ($a_prio === $b_prio) {
                return strtotime($b['tanggal_pengajuan']) - strtotime($a['tanggal_pengajuan']);
            }
            return $a_prio - $b_prio;
        });

        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Pengajuan Telaah',
            'list_usulan' => $list_usulan,
            'jurusan_list' => array_keys($jurusan_set),
            'jumlah_menunggu' => count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'menunggu')),
            'jumlah_telah_direvisi' => count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'telah direvisi'))
        ]);

        $this->view('pages/verifikator/pengajuan_telaah', $data, 'verifikator');
    }

    /**
     * Menampilkan detail telaah (KAK) untuk satu usulan berdasarkan ID.
     */
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? '';
        $base_url = '/docutrack/public/verifikator';

        switch ($ref) {
            case 'dashboard':
                $back_url = $base_url . '/dashboard';
                break;
            case 'riwayat-verifikasi':
                $back_url = $base_url . '/riwayat-verifikasi';
                break;
            default:
                $back_url = $base_url . '/pengajuan-telaah';
                break;
        }

        $model = new verifikatorModel($this->db);
        $dataDB = $model->getDetailKegiatan($id);

        if (!$dataDB) {
            echo 'Data tidak ditemukan.';
            return;
        }

        $kakId = $dataDB['kakId'];
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan = $model->getTahapanByKAK($kakId);
        $rab = $model->getRABByKAK($kakId);

        $tahapan_string = '';
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . '. ' . $t . "\n";
        }

        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];
        
        $surat_url = !empty($dataDB['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $dataDB['suratPengantar'] : '';

        $kegiatan_data = [
            'kegiatanId' => $dataDB['kegiatanId'],
            'nama_pengusul' => $dataDB['nama_pengusul'] ?? '-',
            'nim_pengusul' => $dataDB['nim_pelaksana'] ?? '-',
            'nama_pelaksana' => $dataDB['nama_pelaksana'] ?? '-',
            'nama_penanggung_jawab' => $dataDB['nama_pj'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nim_pj'] ?? '-',
            'jurusan' => $dataDB['jurusanPenyelenggara'],
            'prodi' => $dataDB['prodiPenyelenggara'] ?? '',
            'nama_kegiatan' => $dataDB['namaKegiatan'],
            'gambaran_umum' => $dataDB['gambaranUmum'],
            'penerima_manfaat' => $dataDB['penerimaMaanfaat'],
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'],
            'tahapan_kegiatan' => $tahapan_string,
            'surat_pengantar' => $dataDB['suratPengantar'] ?? '',
            'tanggal_mulai' => $dataDB['tanggalMulai'] ?? '',
            'tanggal_selesai' => $dataDB['tanggalSelesai'] ?? ''
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Telaah Usulan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'),
            'user_role' => $_SESSION['user_role'] ?? 'verifikator',
            'id' => $id,
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            'kode_mak' => $dataDB['buktiMAK'] ?? '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'surat_pengantar_url' => $surat_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/verifikator/telaah_detail', $data, 'verifikator');
    }

    /**
     * Menyetujui usulan dengan ID tertentu.
     */
    public function approve($routeId = null)
    {
        $kegiatanId = $routeId;
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $kegiatanId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? null);
            $kodeMak = trim($_POST['kode_mak'] ?? '');
            $umpanBalik = trim($_POST['umpan_balik'] ?? '');

            if (empty($kegiatanId)) {
                throw new Exception('ID kegiatan tidak ditemukan');
            }

            if ($kodeMak === '') {
                throw new Exception('Kode MAK wajib diisi.');
            }

            $model = new verifikatorModel($this->db);

            if ($model->approveUsulan($kegiatanId, $kodeMak, $umpanBalik)) {
                $_SESSION['flash_message'] = 'Usulan berhasil disetujui.';
                header('Location: /docutrack/public/verifikator/dashboard?msg=approved');
                exit;
            }

            throw new Exception('Gagal menyetujui usulan. Silakan coba lagi.');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $fallbackId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? '');
            header('Location: /docutrack/public/verifikator/telaah/show/'.$fallbackId.'?ref=dashboard');
            exit;
        }
    }

    /**
     * Menolak usulan dengan ID tertentu.
     */
    /**
 * Menolak usulan dengan ID tertentu.
 */
public function reject($routeId = null)
{
    // Enable error reporting untuk debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $kegiatanId = $routeId;
    
    try {
        // Debug: Log semua data yang masuk
        error_log("=== REJECT DEBUG START ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("routeId: " . ($routeId ?? 'NULL'));
        error_log("POST data: " . print_r($_POST, true));
        error_log("SESSION data: " . print_r($_SESSION, true));
        
        // Validasi request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
        }

        // Ambil kegiatan ID dan CAST ke integer
        $kegiatanId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? null);
        $kegiatanId = (int) $kegiatanId; // PENTING: Cast ke integer
        
        if (!$kegiatanId || $kegiatanId <= 0) {
            error_log("ERROR: ID kegiatan tidak valid");
            throw new Exception('ID kegiatan tidak valid');
        }
        
        error_log("Kegiatan ID: " . $kegiatanId);

        // Ambil alasan penolakan
        $alasanPenolakan = trim($_POST['alasan_penolakan'] ?? '');
        
        if ($alasanPenolakan === '') {
            error_log("ERROR: Alasan penolakan kosong");
            throw new Exception('Alasan penolakan wajib diisi');
        }
        
        error_log("Alasan: " . $alasanPenolakan);

        // Cek koneksi database
        if (!$this->db) {
            error_log("ERROR: Database connection is null");
            throw new Exception('Database connection failed');
        }
        
        error_log("Database connection OK");

        // Instantiate model
        $model = new verifikatorModel($this->db);
        error_log("Model instantiated OK");

        // Panggil method reject
        error_log("Calling rejectUsulan...");
        $result = $model->rejectUsulan($kegiatanId, $alasanPenolakan);
        error_log("rejectUsulan result: " . ($result ? 'TRUE' : 'FALSE'));

        if ($result) {
            $_SESSION['flash_message'] = 'Usulan berhasil ditolak.';
            error_log("SUCCESS: Redirecting to dashboard");
            header('Location: /docutrack/public/verifikator/dashboard?msg=rejected');
            exit;
        }

        throw new Exception('Gagal menolak usulan. Silakan coba lagi.');
        
    } catch (Exception $e) {
        error_log("EXCEPTION CAUGHT: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        error_log("=== REJECT DEBUG END ===");
        
        $_SESSION['flash_error'] = $e->getMessage();
        $fallbackId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? '');
        
        if (!empty($fallbackId)) {
            header('Location: /docutrack/public/verifikator/telaah/show/'.$fallbackId.'?ref=dashboard');
        } else {
            header('Location: /docutrack/public/verifikator/dashboard');
        }
        exit;
    }
}

    /**
     * Mengirim usulan untuk direvisi.
     */
    // Ganti method revise di TelaahController.php dengan ini:

    /**
     * Mengirim usulan untuk direvisi.
     */
    public function revise($routeId = null)
{
    // Enable error reporting untuk debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $kegiatanId = $routeId;
    
    try {
        // Debug: Log semua data yang masuk
        error_log("=== REVISE DEBUG START ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("routeId: " . ($routeId ?? 'NULL'));
        error_log("POST data: " . print_r($_POST, true));
        
        // Validasi request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
        }

        // Ambil kegiatan ID dan CAST ke integer
        $kegiatanId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? null);
        $kegiatanId = (int) $kegiatanId; // PENTING: Cast ke integer
        
        if (!$kegiatanId || $kegiatanId <= 0) {
            error_log("ERROR: ID kegiatan tidak valid");
            throw new Exception('ID kegiatan tidak valid');
        }
        
        error_log("Kegiatan ID: " . $kegiatanId);

        // Ambil komentar revisi dari form
        $rawKomentar = $_POST['komentar'] ?? [];
        error_log("Raw komentar: " . print_r($rawKomentar, true));
        
        $komentarRevisi = [];

        // Filter komentar yang tidak kosong
        foreach ($rawKomentar as $targetKolom => $komentar) {
            $trimmedKomentar = trim($komentar);
            if (!empty($trimmedKomentar)) {
                $komentarRevisi[] = [
                    'targetKolom' => $targetKolom,
                    'targetTabel' => 'tbl_kegiatan',
                    'komentar' => $trimmedKomentar
                ];
            }
        }
        
        error_log("Filtered komentar: " . print_r($komentarRevisi, true));

        // Validasi minimal 1 komentar
        if (empty($komentarRevisi)) {
            error_log("ERROR: Tidak ada komentar revisi");
            throw new Exception('Minimal isi satu catatan revisi');
        }

        // Cek koneksi database
        if (!$this->db) {
            error_log("ERROR: Database connection is null");
            throw new Exception('Database connection failed');
        }
        
        error_log("Database connection OK");

        // Instantiate model
        $model = new verifikatorModel($this->db);
        error_log("Model instantiated OK");

        // Panggil method revise
        error_log("Calling reviseUsulan...");
        $result = $model->reviseUsulan($kegiatanId, $komentarRevisi);
        error_log("reviseUsulan result: " . ($result ? 'TRUE' : 'FALSE'));

        if ($result) {
            $_SESSION['flash_message'] = 'Usulan dikembalikan untuk revisi.';
            error_log("SUCCESS: Redirecting to dashboard");
            header('Location: /docutrack/public/verifikator/dashboard?msg=revised');
            exit;
        }

        throw new Exception('Gagal mengirim revisi. Silakan coba lagi.');
        
    } catch (Exception $e) {
        error_log("EXCEPTION CAUGHT: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        error_log("=== REVISE DEBUG END ===");
        
        $_SESSION['flash_error'] = $e->getMessage();
        $fallbackId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? '');
        
        if (!empty($fallbackId)) {
            header('Location: /docutrack/public/verifikator/telaah/show/'.$fallbackId.'?ref=dashboard');
        } else {
            header('Location: /docutrack/public/verifikator/dashboard');
        }
        exit;
    }
}
}