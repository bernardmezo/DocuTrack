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
        $base_url = baseUrl('bendahara');
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

        // Hitung total dicairkan dan sisa dana
        $totalAnggaran = $kegiatan['total_rab'] ?? 0;
        $totalDicairkan = $this->pencairanService->getTotalDicairkanByKegiatan($id);
        $sisaDana = $totalAnggaran - $totalDicairkan;
        $bolehCairkanLagi = ($totalDicairkan < $totalAnggaran);

        $riwayatPencairan = $this->pencairanService->getRiwayatPencairanByKegiatan($id);

        $tahapan_string = "";
        if ($tahapan && is_array($tahapan)) {
            foreach ($tahapan as $idx => $t) {
                $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
            }
        }

        if ($totalDicairkan >= $totalAnggaran) {
            $status_display = 'Dana Diberikan';
        } elseif ($totalDicairkan > 0) {
            $status_display = 'Dana Belum Diberikan Semua';
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
                'penerima_manfaat' => $kegiatan['penerimaManfaat'] ?? '-',
                'metode_pelaksanaan' => $kegiatan['metodePelaksanaan'] ?? '-',
                'tahapan_kegiatan' => $tahapan_string ?: '-',
                'tanggal_mulai' => $kegiatan['tanggalMulai'] ?? '',
                'tanggal_selesai' => $kegiatan['tanggalSelesai'] ?? ''
            ],
            'iku_data' => $iku_data,
            'indikator_data' => $indikator_data,
            'rab_data' => $rab_data,
            'anggaran_disetujui' => $totalAnggaran ?? 0,
            'surat_pengantar_url' => !empty($kegiatan['suratPengantar']) ? baseUrl('uploads/surat/' . $kegiatan['suratPengantar']) : '',
            'jumlah_dicairkan' => $totalDicairkan ?? 0,
            'tanggal_pencairan' => $kegiatan['tanggalPencairan'] ?? null,
            'metode_pencairan' => $kegiatan['metodePencairan'] ?? 'uang_muka',
            'catatan_bendahara' => $kegiatan['catatanBendahara'] ?? '',
            'total_dicairkan' => $totalDicairkan,
            'sisa_dana' => $sisaDana,
            'boleh_cairkan_lagi' => $bolehCairkanLagi,
            'riwayat_pencairan' => $riwayatPencairan,
            'back_url' => $back_url,
            'back_text' => 'Kembali'
        ]);

        $this->view('pages/bendahara/pencairan-dana-detail', $data, 'bendahara');
    }

    /**
     * Proses Pencairan Dana Bertahap (Refactored to match Dev)
     */
    public function proses()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . baseUrl('bendahara/pencairan-dana'));
            exit;
        }

        $kegiatanId = (int) ($_POST['kegiatanId'] ?? 0);
        $action = $_POST['action'] ?? null;
        $userId = (int) ($_SESSION['user_id'] ?? 0);

        error_log("=== PROSES PENCAIRAN REQUEST ===");
        error_log("Kegiatan ID: " . $kegiatanId);
        error_log("Action: " . $action);

        if (!$kegiatanId || !$action) {
            $_SESSION['flash_error'] = 'Data tidak lengkap!';
            header('Location: ' . baseUrl('bendahara/pencairan-dana'));
            exit;
        }

        try {
            if ($action === 'cairkan') {
                // Ambil data dari POST
                $catatan = trim($_POST['catatan'] ?? '');
                $total_anggaran_raw = $_POST['total_anggaran'] ?? 0;
                $totalAnggaranDisetujui= (float) preg_replace('/\D/', '', $total_anggaran_raw);

                $totalDicairkan = $this->pencairanService->getTotalDicairkanByKegiatan($kegiatanId);

                // Ambil array tahapan dari form
                $tanggal_array = $_POST['tanggalTahapan'] ?? [];
                $termin_array = $_POST['terminTahapan'] ?? [];
                $nominal_array = $_POST['nominalTahapan'] ?? [];

                if (count($tanggal_array) !== count($termin_array) || count($tanggal_array) !== count($nominal_array)) {
                    throw new Exception('Data tahapan tidak konsisten');
                }

                // Build array tahapan
                $tahapan = [];
                $totalNominal = 0;

                for ($i = 0; $i < count($tanggal_array); $i++) {
                    $tanggal = trim($tanggal_array[$i]);
                    $termin = trim($termin_array[$i]);
                    $nominalRaw = $nominal_array[$i];

                    // Skip jika kosong
                    if (empty($tanggal) || empty($termin) || empty($nominalRaw)) {
                        continue;
                    }

                    // Clean nominal dari format rupiah (1.000.000 -> 1000000)
                    $nominal = (float) preg_replace('/\D/', '', $nominalRaw);

                    if ($nominal <= 0) {
                        throw new Exception("Nominal tahap ke-" . ($i + 1) . " harus lebih dari 0");
                    }

                    $tahapan[] = [
                        'tanggal' => $tanggal,
                        'termin' => $termin,
                        'nominal' => $nominal
                    ];
                    $totalNominal += $nominal;
                }

                // Validasi apakah ada tahapan yang valid
                if (empty($tahapan)) {
                    throw new Exception("Tidak ada data tahapan yang valid");
                }

                $sisaDana = $totalAnggaranDisetujui - $totalDicairkan;

                // Validasi total nominal (toleransi Rp 1)
                if ($totalNominal > $sisaDana + 1) {
                    throw new Exception(
                        "Total nominal tahapan (Rp " . number_format($totalNominal, 0, ',', '.') .
                        ") melebihi sisa dana (Rp " . number_format($sisaDana, 0, ',', '.') .
                        ")"
                    );
                }

                if ($totalNominal <= 0) {
                    throw new Exception("Total nominal pencairan harus lebih dari Rp 0");
                }

                // Prepare data untuk service
                $dataPencairan = [
                    'metode' => 'bertahap',
                    'jumlah' => $totalAnggaranDisetujui,
                    'tahapan' => $tahapan,
                    'tanggal' => $tahapan[0]['tanggal'], // Tanggal pencairan pertama
                    'catatan' => $catatan
                ];

                // Execute Service
                $result = $this->pencairanService->cairkanDana($kegiatanId, $dataPencairan);

                if (!$result) {
                    throw new Exception("Gagal memproses pencairan dana");
                }

                if (function_exists('logPencairan')) {
                    logPencairan($userId, $kegiatanId, $totalAnggaranDisetujui, 'bertahap', $catatan);
                }

                $_SESSION['flash_message'] = 'Dana berhasil dicairkan secara bertahap (' . count($tahapan) . ' tahap) dengan total Rp ' . number_format($totalNominal, 0, ',', '.');
                $_SESSION['flash_type'] = 'success';

            } elseif ($action === 'tolak') {
                $_SESSION['flash_error'] = 'Fitur tolak belum diaktifkan.';
            }

        } catch (Exception $e) {
            error_log("=== PENCAIRAN ERROR ===");
            error_log("Message: " . $e->getMessage());
            
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        // Redirect back to original URL
        header('Location: ' . baseUrl('bendahara/pencairan-dana'));
        exit;
    }
}