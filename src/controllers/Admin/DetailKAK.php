<?php
// File: src/controllers/Admin/AdminDetailKAKController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php';

class AdminDetailKAKController extends Controller {
    
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        $model = new adminModel();
        
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) {
            echo "Kegiatan dengan ID $id tidak ditemukan.";
            return;
        }

        $kakId = $dataDB['kakId'];

        $indikator  = $model->getIndikatorByKAK($kakId);
        $tahapan    = $model->getTahapanByKAK($kakId);
        $rab        = $model->getRABByKAK($kakId);
        $komentar   = $model->getKomentarTerbaru($id);
        $komentarPenolakan = $model->getKomentarPenolakan($id);

        $tahapan_string = "";
        foreach ($tahapan as $index => $tahap) {
            $tahapan_string .= ($index + 1) . ". " . $tahap . "\n";
        }

        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];

        $kegiatan_data = [
            'nama_pengusul' => $dataDB['pemilikKegiatan'],
            'nim_pengusul' => $dataDB['nimPelaksana'],
            'nama_penanggung_jawab' => $dataDB['namaPenanggungJawab'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nip'] ?? '-',
            'jurusan' => $dataDB['jurusanPenyelenggara'],
            'nama_kegiatan' => $dataDB['namaKegiatan'],
            'gambaran_umum' => $dataDB['gambaranUmum'],
            'penerima_manfaat' => $dataDB['penerimaMaanfaat'],
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'],
            'tahapan_kegiatan' => $tahapan_string,
            'surat_pengantar' => '',
            'tanggal_mulai' => '',
            'tanggal_selesai' => ''
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'),
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            
            'kode_mak' => $dataDB['buktiMAK'] ?? '',
            'komentar_revisi' => $komentar,
            'komentar_penolakan' => $komentarPenolakan,
            'surat_pengantar_url' => '#', 
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kak', $data, 'app');
    }
}
