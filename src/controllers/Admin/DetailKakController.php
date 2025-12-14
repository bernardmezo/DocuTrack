<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Admin\AdminModel;

// Corrected to use namespaced AdminModel

class DetailKakController extends Controller
{
 // Renamed class to match file naming convention

    private $model;

    public function __construct()
    {
        parent::__construct(); // Initialize $this->db from parent Controller
        // Use the fully qualified class name for AdminModel
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
}
