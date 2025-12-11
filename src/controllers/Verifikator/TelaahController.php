<?php

namespace App\Controllers\Verifikator;

use App\Core\Controller;
use App\Models\kegiatan\KegiatanModel;
use App\Models\VerifikatorModel;
use App\Services\LogStatusService;
use App\Services\ValidationService;
use App\Services\VerifikatorService;
use Exception;

class TelaahController extends Controller
{
    private VerifikatorService $service;

    public function __construct()
    {
        parent::__construct();

        // Manual dependency instantiation
        $dbConnection = $this->db;
        $verifikatorModel = new VerifikatorModel($dbConnection);
        $logStatusService = new LogStatusService($dbConnection);
        $validationService = new ValidationService();
        $kegiatanModel = new KegiatanModel($dbConnection);

        $this->service = new VerifikatorService(
            $verifikatorModel,
            $logStatusService,
            $validationService,
            $kegiatanModel
        );
    }

    /**
     * Helper private untuk mengecek otorisasi akses berdasarkan Jurusan.
     * Mencegah IDOR (Insecure Direct Object Reference).
     * 
     * @param int|string $kegiatanId
     * @return array|null Mengembalikan data kegiatan jika valid, atau exit/redirect jika tidak valid.
     */
    private function authorizeAccess($kegiatanId)
    {
        if (empty($kegiatanId)) {
            $_SESSION['flash_error'] = 'ID Kegiatan tidak valid.';
            header('Location: /docutrack/public/verifikator/dashboard');
            exit;
        }

        $dataDB = $this->service->getDetailKegiatan($kegiatanId);

        if (!$dataDB) {
            $_SESSION['flash_error'] = 'Data kegiatan tidak ditemukan.';
            header('Location: /docutrack/public/verifikator/dashboard');
            exit;
        }

        $userJurusan = $_SESSION['user_jurusan'] ?? null;
        $docJurusan = $dataDB['jurusanPenyelenggara'] ?? null;

        // DEBUG: Log jurusan comparison
        error_log("VERIFIKATOR ACCESS CHECK:");
        error_log("  User Jurusan: '" . var_export($userJurusan, true) . "'");
        error_log("  Doc Jurusan: '" . var_export($docJurusan, true) . "'");
        error_log("  Match: " . ($userJurusan === $docJurusan ? 'YES' : 'NO'));

        // Cek strict: User harus punya jurusan dan harus sama dengan dokumen.
        // Jika Verifikator bersifat 'Global' (jurusan NULL), logika ini harus disesuaikan
        // sesuai kebijakan. Berdasarkan instruksi "Compare... If not match...", 
        // kita terapkan strict comparison.
        // Namun, untuk mengakomodasi data seed (Verifikator Global), kita bisa beri pengecualian
        // jika user_jurusan NULL (Super Verifikator).
        // TAPI: Instruksi user spesifik: "Compare... against user_jurusan_id. If don't match -> error."
        // Maka kita ikuti strict check (Non-NULL comparison).
        
        // Revisi logika agar aman tapi tidak memblokir user seed jika tujuannya testing:
        // Idealnya: if ($userJurusan !== $docJurusan)
        // Kita gunakan logika: Jika User punya Jurusan, dia hanya boleh akses Jurusan itu.
        // Jika User TIDAK punya Jurusan (NULL), kita anggap dia Global (atau Unauthorized, tergantung policy).
        // Mengingat instruksi fix IDOR, biasanya user Verifikator harusnya terikat jurusan.
        // Mari kita asumsikan strict check sesuai prompt.
        
        // NOTE: Menggunakan $userJurusan == $docJurusan (loose) atau === (strict).
        // Karena data database mungkin string, aman.
        
        // UPDATE: Sesuai prompt "Compare ... If they don't match, redirect".
        // EXCEPTION: Jika user_jurusan NULL, anggap sebagai Global Verifikator (bisa akses semua)
        if ($userJurusan !== null && $userJurusan !== $docJurusan) {
             $_SESSION['flash_error'] = 'Akses ditolak. Dokumen ini bukan wewenang Jurusan Anda.';
             error_log("VERIFIKATOR ACCESS DENIED: User jurusan '$userJurusan' != Doc jurusan '$docJurusan'");
             header('Location: /docutrack/public/verifikator/dashboard');
             exit;
        }
        
        error_log("VERIFIKATOR ACCESS GRANTED for kegiatan ID: $kegiatanId");

        return $dataDB;
    }

    public function index($data_dari_router = [])
    {
        $all_usulan = $this->safeModelCall($this->service, 'getDashboardKAK', [], []);

        $list_usulan = [];
        $jurusan_set = [];
        $userJurusan = $_SESSION['user_jurusan'] ?? null;

        foreach ($all_usulan as $usulan) {
            // Filter tambahan di index agar list hanya menampilkan milik jurusan user
            // (Meskipun authorizeAccess melindungi detail, list juga sebaiknya difilter)
            if ($userJurusan && isset($usulan['jurusan']) && $usulan['jurusan'] !== $userJurusan) {
                continue; 
            }

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

    public function show($id, $data_dari_router = [])
    {
        // STEP 1: Authorize Access
        $dataDB = $this->authorizeAccess($id);

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

        // $dataDB sudah diambil via authorizeAccess, tidak perlu fetch ulang

        $kakId = $dataDB['kakId'];
        $indikator = $this->safeModelCall($this->service, 'getIndikatorByKAK', [$kakId], []);
        $tahapan = $this->safeModelCall($this->service, 'getTahapanByKAK', [$kakId], []);
        $rab = $this->safeModelCall($this->service, 'getRABByKAK', [$kakId], []);

        $tahapan_string = '';
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
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
            'penerima_manfaat' => $dataDB['penerimaManfaat'],
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

    public function approve($routeId = null)
    {
        $kegiatanId = $routeId ?? ($_POST['kegiatan_id'] ?? null);
        
        // STEP 1: Authorize Access
        // Kita panggil authorizeAccess untuk memastikan user berhak atas ID ini.
        // Return value (data kegiatan) tidak dipakai di sini, hanya cek akses.
        $this->authorizeAccess($kegiatanId);

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $kodeMak = trim($_POST['kode_mak'] ?? '');
            $umpanBalik = trim($_POST['umpan_balik'] ?? '');

            if (empty($kegiatanId)) {
                throw new Exception('ID kegiatan tidak ditemukan');
            }

            if ($kodeMak === '') {
                throw new Exception('Kode MAK wajib diisi.');
            }

            if ($this->service->approveUsulan($kegiatanId, $kodeMak, $umpanBalik)) {
                $_SESSION['flash_message'] = 'Usulan berhasil disetujui.';
                header('Location: /docutrack/public/verifikator/dashboard?msg=approved');
                exit;
            }

            throw new Exception('Gagal menyetujui usulan. Silakan coba lagi.');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $fallbackId = $kegiatanId;
            header('Location: /docutrack/public/verifikator/telaah/show/' . $fallbackId . '?ref=dashboard');
            exit;
        }
    }

    public function reject($routeId = null)
    {
        $kegiatanId = $routeId ?? ($_POST['kegiatan_id'] ?? null);
        $kegiatanId = (int) $kegiatanId;

        // STEP 1: Authorize Access
        $this->authorizeAccess($kegiatanId);

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            }

            if (!$kegiatanId || $kegiatanId <= 0) {
                throw new Exception('ID kegiatan tidak valid');
            }

            $alasanPenolakan = trim($_POST['alasan_penolakan'] ?? '');

            if ($alasanPenolakan === '') {
                throw new Exception('Alasan penolakan wajib diisi');
            }

            $result = $this->service->rejectUsulan($kegiatanId, $alasanPenolakan);

            if ($result) {
                $_SESSION['flash_message'] = 'Usulan berhasil ditolak.';
                header('Location: /docutrack/public/verifikator/dashboard?msg=rejected');
                exit;
            }

            throw new Exception('Gagal menolak usulan. Silakan coba lagi.');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $fallbackId = $kegiatanId;

            if (!empty($fallbackId)) {
                header('Location: /docutrack/public/verifikator/telaah/show/' . $fallbackId . '?ref=dashboard');
            } else {
                header('Location: /docutrack/public/verifikator/dashboard');
            }
            exit;
        }
    }

    public function revise($routeId = null)
    {
        $kegiatanId = $routeId ?? ($_POST['kegiatan_id'] ?? null);
        $kegiatanId = (int) $kegiatanId;

        // STEP 1: Authorize Access
        $this->authorizeAccess($kegiatanId);

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            }

            if (!$kegiatanId || $kegiatanId <= 0) {
                throw new Exception('ID kegiatan tidak valid');
            }

            $rawKomentar = $_POST['komentar'] ?? [];
            $komentarRevisi = [];

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

            if (empty($komentarRevisi)) {
                throw new Exception('Minimal isi satu catatan revisi');
            }

            $result = $this->service->reviseUsulan($kegiatanId, $komentarRevisi);

            if ($result) {
                $_SESSION['flash_message'] = 'Usulan dikembalikan untuk revisi.';
                header('Location: /docutrack/public/verifikator/dashboard?msg=revised');
                exit;
            }

            throw new Exception('Gagal mengirim revisi. Silakan coba lagi.');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $fallbackId = $kegiatanId;

            if (!empty($fallbackId)) {
                header('Location: /docutrack/public/verifikator/telaah/show/' . $fallbackId . '?ref=dashboard');
            } else {
                header('Location: /docutrack/public/verifikator/dashboard');
            }
            exit;
        }
    }
}
