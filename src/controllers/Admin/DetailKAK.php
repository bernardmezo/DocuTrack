<?php
// File: src/controllers/Admin/AdminDetailKAKController.php (Sesuaikan nama file/class)

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php'; // Load Model

class AdminDetailKAKController extends Controller {
    
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        // 1. Panggil Model
        $model = new adminModel();
        
        // 2. Ambil Data Utama Kegiatan
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) {
            // Tampilkan 404 jika ID tidak ada di database
            echo "Kegiatan dengan ID $id tidak ditemukan.";
            return;
        }

        $kakId = $dataDB['kakId']; // ID ini dipakai untuk ambil anak-anak data (RAB, Indikator, dll)

        // 3. Ambil Data Pendukung (Relasi)
        $indikator  = $model->getIndikatorByKAK($kakId);
        $tahapan    = $model->getTahapanByKAK($kakId);
        $rab        = $model->getRABByKAK($kakId);
        $komentar   = $model->getKomentarTerbaru($id); // Masih array kosong

        // 4. Formatting Data untuk View
        // Kita harus mengubah format data dari DB agar cocok dengan variabel di 'detail_kak.php'
        
        // Format Tahapan: Ubah array menjadi string bernomor (1. A \n 2. B)
        $tahapan_string = "";
        foreach ($tahapan as $index => $tahap) {
            $tahapan_string .= ($index + 1) . ". " . $tahap . "\n";
        }

        // Format IKU: Di DB disimpan string "A,B,C", ubah jadi Array
        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];

        // Mapping data ke struktur yang diharapkan View
        $kegiatan_data = [
            'nama_pengusul' => $dataDB['pemilikKegiatan'], // Mapping kolom DB ke View
            'nim_pengusul' => $dataDB['nimPelaksana'],
            'nama_penanggung_jawab' => $dataDB['namaPenanggungJawab'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nip'] ?? '-',
            'jurusan' => $dataDB['jurusanPenyelenggara'],
            'nama_kegiatan' => $dataDB['namaKegiatan'],
            'gambaran_umum' => $dataDB['gambaranUmum'],
            'penerima_manfaat' => $dataDB['penerimaMaanfaat'], // Sesuaikan ejaan kolom DB
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'],
            'tahapan_kegiatan' => $tahapan_string, // String hasil loop tadi
            'surat_pengantar' => '', // Belum ada di DB upload surat?
            'tanggal_mulai' => '', // Jika ada kolom tglMulai di DB, masukkan sini
            'tanggal_selesai' => ''
        ];

        // Data Final untuk dikirim ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'), // Status dari DB
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            
            // Data hasil olahan di atas
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            
            'kode_mak' => $dataDB['buktiMAK'] ?? '', // Kolom buktiMAK di DB
            'komentar_revisi' => $komentar,
            'komentar_penolakan' => '', // Ambil dari DB jika ada
            'surat_pengantar_url' => '#', 
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kak', $data, 'app');
    }
}