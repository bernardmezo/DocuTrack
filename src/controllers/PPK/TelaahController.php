<?php
// File: src/controllers/PPK/TelaahController.php

require_once '../src/core/Controller.php';
require_once '../src/model/ppkModel.php';

class PPKTelaahController extends Controller {

    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/ppk";
        $back_url = $base_url . '/' . $ref;

        // 1. Panggil Model
        $model = new ppkModel();
        
        // 2. Ambil Data Real dari DB
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) {
            echo "Data tidak ditemukan."; return;
        }

        $kakId = $dataDB['kakId'];

        // 3. Ambil Data Relasi
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan   = $model->getTahapanByKAK($kakId);
        $rab       = $model->getRABByKAK($kakId);

        // 4. Formatting Data
        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }

        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];
        $surat_url = !empty($dataDB['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $dataDB['suratPengantar'] : '';

        // Mapping Data untuk View
        $kegiatan_data = [
            'nama_pengusul' => $dataDB['pemilikKegiatan'],
            'nim_pengusul' => $dataDB['nimPelaksana'], // Sesuaikan kolom DB
            'nama_penanggung_jawab' => $dataDB['pemilikKegiatan'], // Default jika blm ada kolom khusus
            'nip_penanggung_jawab' => $dataDB['nimPelaksana'],   // Default
            'nama_kegiatan' => $dataDB['namaKegiatan'],
            'gambaran_umum' => $dataDB['gambaranUmum'],
            'penerima_manfaat' => $dataDB['penerimaMaanfaat'],
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'],
            'tahapan_kegiatan' => $tahapan_string,
            'surat_pengantar' => $dataDB['suratPengantar'] ?? '',
            'tanggal_mulai' => $dataDB['tanggalMulai'] ?? '',
            'tanggal_selesai' => $dataDB['tanggalSelesai'] ?? ''
        ];

        // 5. Kirim ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Telaah Usulan (PPK) - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'),
            'id' => $id,
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            'kode_mak' => $dataDB['buktiMAK'] ?? '', // PPK melihat Kode MAK yg diisi Verifikator
            'surat_pengantar_url' => $surat_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/ppk/telaah_detail', $data, 'ppk');
    }

    // Action Approve
    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new ppkModel();
            if($model->approveUsulan($id)) {
                header('Location: /docutrack/public/ppk/dashboard?msg=approved');
                exit;
            }
        }
        // Jika gagal/bukan POST, kembalikan ke detail
        header('Location: /docutrack/public/ppk/telaah/show/'.$id);
    }
}