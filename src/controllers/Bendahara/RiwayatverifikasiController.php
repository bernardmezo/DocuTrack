<?php

// File: src/controllers/Bendahara/RiwayatverifikasiController.php

namespace App\Controllers\Bendahara;

use App\Core\Controller;
use App\Services\BendaharaService;

class RiwayatverifikasiController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new BendaharaService($this->db);
    }

    /**
     * Menampilkan halaman Riwayat Verifikasi Bendahara.
     * Mengambil semua usulan yang telah diproses (Dana Diberikan, Revisi/Ditolak).
     */
    public function index($data_dari_router = [])
    {

        // ✅ AMBIL DATA DARI DATABASE (bukan dummy)
        $list_riwayat = $this->safeModelCall($this->model, 'getRiwayatVerifikasi', [], []);
        $jurusan_list = $this->safeModelCall($this->model, 'getListJurusan', [], []);

        $data = array_merge($data_dari_router, [
            'title' => 'Riwayat Verifikasi',
            'list_riwayat' => $list_riwayat,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/bendahara/riwayat-verifikasi', $data, 'bendahara');
    }

    /**
     * Menampilkan detail riwayat pencairan
     */
    public function show($id, $data_dari_router = [])
    {
        $ref = $_GET['ref'] ?? 'riwayat-verifikasi';
        $base_url = "/docutrack/public/bendahara";
        $back_url = $base_url . '/' . $ref;

        // ✅ AMBIL DATA DARI DATABASE
        $kegiatan = $this->safeModelCall($this->model, 'getDetailPencairan', [$id], null);

        if (!$kegiatan) {
            $_SESSION['flash_error'] = 'Data tidak ditemukan.';
            header('Location: ' . $back_url);
            exit;
        }

        // Ambil data relasi
        $rab_data = $this->safeModelCall($this->model, 'getRABByKegiatan', [$id], []);
        $iku_data = $this->safeModelCall($this->model, 'getIKUByKegiatan', [$id], []);
        $indikator_data = $this->safeModelCall($this->model, 'getIndikatorByKegiatan', [$id], []);
        $tahapan = $this->safeModelCall($this->model, 'getTahapanByKegiatan', [$id], []);

        // Format tahapan sebagai string bernomor
        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Riwayat - ' . htmlspecialchars($kegiatan['namaKegiatan']),
            'id' => $id,
            'status' => !empty($kegiatan['tanggalPencairan']) ? 'Dana Diberikan' : 'Ditolak',

            // Data Kegiatan
            'nama_kegiatan' => $kegiatan['namaKegiatan'],
            'nama_mahasiswa' => $kegiatan['pemilikKegiatan'],
            'nim' => $kegiatan['nimPelaksana'],
            'jurusan' => $kegiatan['jurusanPenyelenggara'] ?? '-',
            'prodi' => $kegiatan['prodiPenyelenggara'] ?? '-',
            'tanggal_pengajuan' => $kegiatan['createdAt'],
            'kode_mak' => $kegiatan['buktiMAK'] ?? '-',

            // Data KAK
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

            // Data IKU & Indikator
            'iku_data' => $iku_data,
            'indikator_data' => $indikator_data,

            // Data RAB
            'rab_data' => $rab_data,
            'anggaran_disetujui' => $kegiatan['total_rab'] ?? 0,

            // Surat Pengantar
            'surat_pengantar_url' => !empty($kegiatan['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $kegiatan['suratPengantar'] : '',

            // Data Pencairan
            'jumlah_dicairkan' => $kegiatan['jumlahDicairkan'] ?? 0,
            'tanggal_pencairan' => $kegiatan['tanggalPencairan'] ?? null,
            'metode_pencairan' => $kegiatan['metodePencairan'] ?? '-',
            'catatan_bendahara' => $kegiatan['catatanBendahara'] ?? '',

            'back_url' => $back_url,
            'back_text' => 'Kembali'
        ]);

        $this->view('pages/bendahara/riwayat-verifikasi', $data, 'bendahara');
    }
}
