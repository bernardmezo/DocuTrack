<?php
// File: src/controllers/Wadir/TelaahController.php

require_once '../src/core/Controller.php';
require_once '../src/model/wadirModel.php';
require_once '../src/helpers/logger_helper.php';

class WadirTelaahController extends Controller {
    
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/wadir";
        $back_url = $base_url . '/' . $ref;

        $model = new wadirModel();
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) { echo "Data tidak ditemukan."; return; }

        $kakId = $dataDB['kakId'];
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan   = $model->getTahapanByKAK($kakId);
        $rab       = $model->getRABByKAK($kakId);

        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) { $tahapan_string .= ($idx + 1) . ". " . $t . "\n"; }
        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];
        
        $status_asli = ucfirst($dataDB['status_text'] ?? 'Menunggu');
        $posisi_saat_ini = $dataDB['posisiId'];
        $role_wadir = 3;

        if ($posisi_saat_ini != $role_wadir && $status_asli != 'Ditolak') {
            $status_tampilan = 'Disetujui';
        } else {
            $status_tampilan = $status_asli;
        }

        $kegiatan_data = [
            'nama_pengusul' => $dataDB['pemilikKegiatan'],
            'nim_pengusul' => $dataDB['nimPelaksana'],
            'nama_penanggung_jawab' => $dataDB['namaPJ'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nip'] ?? '-',
            'jurusan' => $dataDB['jurusanPenyelenggara'] ?? '',
            'prodi' => $dataDB['prodiPenyelenggara'] ?? '',
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
            'title' => 'Persetujuan Wadir - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => $status_tampilan,
            'user_role' => 'Wadir',
            'id' => $id,
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            'kode_mak' => $dataDB['buktiMAK'] ?? '',
            'surat_pengantar_url' => !empty($dataDB['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $dataDB['suratPengantar'] : '',
            'back_url' => $back_url
        ]);

        $this->view('pages/wadir/telaah_detail', $data, 'wadir');
    }

    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new wadirModel();
            $userId = $_SESSION['user_id'] ?? 0;
            
            $kegiatan = $model->getDetailKegiatan($id);
            $oldStatusId = $kegiatan['statusUtamaId'] ?? null;
            
            if($model->approveUsulan($id)) {
                logApproval($userId, $id, 'WADIR', true, 
                    'Kegiatan: ' . ($kegiatan['namaKegiatan'] ?? 'Unknown'),
                    $oldStatusId, 3);
                
                header('Location: /docutrack/public/wadir/dashboard?msg=approved');
                exit;
            }
        }
        header('Location: /docutrack/public/wadir/telaah/show/'.$id);
    }
}
