<?php
// File: src/controllers/PPK/TelaahController.php

require_once '../src/core/Controller.php';
require_once '../src/model/ppkModel.php';

class PPKTelaahController extends Controller {

    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/ppk";
        $back_url = $base_url . '/' . $ref;

        $model = new ppkModel();
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) { echo "Data tidak ditemukan."; return; }

        $kakId = $dataDB['kakId'];
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan   = $model->getTahapanByKAK($kakId);
        $rab       = $model->getRABByKAK($kakId);

        // --- formatting ---
        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) { $tahapan_string .= ($idx + 1) . ". " . $t . "\n"; }
        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];
        $surat_url = !empty($dataDB['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $dataDB['suratPengantar'] : '';

        // LOGIKA STATUS BUAT PPK
        
        // Ambil status asli dan posisi dokumen saat ini
        $status_asli = ($dataDB['status_text'] ?? 'Menunggu');
        $posisi_saat_ini = $dataDB['posisiId'];
        $role_ppk = 4; // ID Role PPK

        // Jika dokumen SUDAH LEWAT dari meja PPK (posisi bukan 4), dan tidak ditolak
        // Maka bagi PPK, statusnya adalah "Disetujui" (Read Only)
        if ($posisi_saat_ini != $role_ppk && $status_asli != 'Ditolak') {
            $status_tampilan = 'Disetujui';
        } else {
            // Jika masih di meja PPK (4), gunakan status asli (Menunggu)
            $status_tampilan = $status_asli;
        }
        // ==============================

        $kegiatan_data = [
            'nama_pengusul' => $dataDB['pemilikKegiatan'],
            'nim_pengusul' => $dataDB['nimPelaksana'],
            'nama_penanggung_jawab' => $dataDB['pemilikKegiatan'],
            'nip_penanggung_jawab' => $dataDB['nimPelaksana'],
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
            'title' => 'Telaah Usulan (PPK) - ' . htmlspecialchars($dataDB['namaKegiatan']),
            
            'status' => $status_tampilan, // <--- Kirim status yang sudah dimanipulasi
            
            'id' => $id,
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            'kode_mak' => $dataDB['buktiMAK'] ?? '',
            'surat_pengantar_url' => $surat_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/ppk/telaah_detail', $data, 'ppk');
    }

    // Approve Action
    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new ppkModel();
            if($model->approveUsulan($id)) {
                header('Location: /docutrack/public/ppk/dashboard?msg=approved');
                exit;
            }
        }
        header('Location: /docutrack/public/ppk/telaah/show/'.$id);
    }
}
?>