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

    public function index($data_dari_router = [])
    {
        $all_usulan = $this->safeModelCall($this->service, 'getDashboardKAK', [], []);

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

    public function show($id, $data_dari_router = [])
    {
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

        $dataDB = $this->safeModelCall($this->service, 'getDetailKegiatan', [$id], null);

        if (!$dataDB) {
            echo 'Data tidak ditemukan.';
            return;
        }

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

            if ($this->service->approveUsulan($kegiatanId, $kodeMak, $umpanBalik)) {
                $_SESSION['flash_message'] = 'Usulan berhasil disetujui.';
                header('Location: /docutrack/public/verifikator/dashboard?msg=approved');
                exit;
            }

            throw new Exception('Gagal menyetujui usulan. Silakan coba lagi.');
        } catch (Exception $e) {
            $_SESSION['flash_error'] = $e->getMessage();
            $fallbackId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? '');
            header('Location: /docutrack/public/verifikator/telaah/show/' . $fallbackId . '?ref=dashboard');
            exit;
        }
    }

    public function reject($routeId = null)
    {
        $kegiatanId = $routeId;

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            }

            $kegiatanId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? null);
            $kegiatanId = (int) $kegiatanId;

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
            $fallbackId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? '');

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
        $kegiatanId = $routeId;

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method: ' . $_SERVER['REQUEST_METHOD']);
            }

            $kegiatanId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? null);
            $kegiatanId = (int) $kegiatanId;

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
            $fallbackId = $kegiatanId ?? ($_POST['kegiatan_id'] ?? '');

            if (!empty($fallbackId)) {
                header('Location: /docutrack/public/verifikator/telaah/show/' . $fallbackId . '?ref=dashboard');
            } else {
                header('Location: /docutrack/public/verifikator/dashboard');
            }
            exit;
        }
    }
}
