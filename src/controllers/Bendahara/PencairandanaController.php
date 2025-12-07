<?php

namespace App\Controllers\Bendahara;

use App\Core\Controller;
use App\Services\PencairanService;
use Exception;

require_once __DIR__ . '/../../helpers/logger_helper.php';

class PencairandanaController extends Controller
{
    private $pencairanService;

    public function __construct()
    {
        parent::__construct();
        $this->pencairanService = new PencairanService($this->db);
    }

    /**
     * Halaman List Pencairan Dana
     */
    public function index($data_dari_router = [])
    {

        $stats = $this->pencairanService->getDashboardStats();
        $list_antrian = $this->pencairanService->getAntrianPencairan();
        $jurusan_list = $this->pencairanService->getListJurusan();

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
    public function show($id, $data_dari_router = [])
    {
        $ref = $_GET['ref'] ?? 'pencairan-dana';
        $base_url = "/docutrack/public/bendahara";
        $back_url = $base_url . '/' . $ref;

        $kegiatan = $this->pencairanService->getDetailPencairan($id);

        if (!$kegiatan) {
            $_SESSION['flash_error'] = 'Data tidak ditemukan.';
            header('Location: ' . $back_url);
            exit;
        }

        $rab_data = $this->pencairanService->getRABByKegiatan($id);
        $iku_data = $this->pencairanService->getIKUByKegiatan($id);
        $indikator_data = $this->pencairanService->getIndikatorByKegiatan($id);
        $tahapan = $this->pencairanService->getTahapanByKegiatan($id);

        $tahapan_string = "";
        if ($tahapan && is_array($tahapan)) {
            foreach ($tahapan as $idx => $t) {
                $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
            }
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
                'nama_pengusul' => $kegiatan['nama_pengusul'] ?? '-',
                'nim_pengusul' => $kegiatan['nim_pelaksana'] ?? '-',
                'nama_pelaksana' => $kegiatan['nama_pelaksana'] ?? '-',
                'nama_penanggung_jawab' => $kegiatan['nama_pj'] ?? '-',
                'nip_penanggung_jawab' => $kegiatan['nim_pj'] ?? '-',
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
     * Menggunakan unified model method: cairkanDana()
     */
    public function proses()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        $kak_id = (int) ($_POST['kak_id'] ?? 0);
        $action = $_POST['action'] ?? null;
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        if (!$kak_id || !$action) {
            $_SESSION['flash_error'] = 'Data tidak lengkap!';
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        try {
            if ($action === 'cairkan') {
                $metode_pencairan = $_POST['metode_pencairan'] ?? 'penuh';
                $catatan = trim($_POST['catatan'] ?? '');

                $dataPencairan = [
                    'metode' => $metode_pencairan,
                    'catatan' => $catatan,
                    'tanggal' => date('Y-m-d') // Default today, can be overridden
                ];

                // 1. Logika Pencairan Penuh
                if ($metode_pencairan === 'penuh') {
                    $jumlah = (float) ($_POST['jumlah_dicairkan'] ?? 0);
                    if ($jumlah <= 0) {
                        throw new Exception('Jumlah pencairan harus lebih dari 0');
                    }

                    $dataPencairan['jumlah'] = $jumlah;
                    $dataPencairan['tanggal'] = date('Y-m-d'); // Pencairan penuh selalu hari ini

                // 2. Logika Pencairan Bertahap
                } elseif ($metode_pencairan === 'bertahap') {
                    $total_anggaran = (float) ($_POST['total_anggaran'] ?? 0);
                    $jumlah_tahap = (int) ($_POST['jumlah_tahap'] ?? 0);

                    if ($jumlah_tahap < 2 || $jumlah_tahap > 5) {
                        throw new Exception('Jumlah tahap harus antara 2-5');
                    }
                    if ($total_anggaran <= 0) {
                        throw new Exception('Total anggaran tidak valid');
                    }

                    $tahapan = [];
                    $totalPersentase = 0;

                    for ($i = 1; $i <= $jumlah_tahap; $i++) {
                        $tanggal = $_POST["tanggal_tahap_{$i}"] ?? null;
                        $persentase = (float) ($_POST["persentase_tahap_{$i}"] ?? 0);

                        if (empty($tanggal)) {
                            throw new Exception("Tanggal tahap {$i} wajib diisi");
                        }
                        if ($persentase <= 0 || $persentase > 100) {
                            throw new Exception("Persentase tahap {$i} tidak valid");
                        }

                        $tahapan[] = [
                            'tanggal' => $tanggal,
                            'persentase' => $persentase
                        ];
                        $totalPersentase += $persentase;
                    }

                    if (abs($totalPersentase - 100) > 0.01) {
                        throw new Exception("Total persentase harus 100%");
                    }

                    $dataPencairan['jumlah'] = $total_anggaran;
                    $dataPencairan['tahapan'] = $tahapan;
                    // Tanggal pencairan utama diambil dari tahap pertama
                    $dataPencairan['tanggal'] = $tahapan[0]['tanggal'];
                }

                // Execute Service
                if ($this->pencairanService->cairkanDana($kak_id, $dataPencairan)) {
                    if (function_exists('logPencairan')) {
                        logPencairan($userId, $kak_id, $dataPencairan['jumlah'], $metode_pencairan, $catatan);
                    }
                    $_SESSION['flash_message'] = 'Dana berhasil dicairkan.';
                    $_SESSION['flash_type'] = 'success';
                } else {
                    throw new Exception('Gagal memproses pencairan dana.');
                }
            } elseif ($action === 'tolak') {
                // TODO: Implement rejection logic in model/service
                $_SESSION['flash_message'] = 'Fitur tolak belum diaktifkan di controller baru.';
            }
        } catch (Exception $e) {
            error_log("Pencairan Error: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        header('Location: /docutrack/public/bendahara/pencairan-dana');
        exit;
    }
}
