<?php
namespace App\Controllers\PPK;

use App\Core\Controller;
use App\Services\PpkService;

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/logger_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/logger_helper.php';
}

class TelaahController extends Controller {

    private $model;

    public function __construct() {
        parent::__construct();
        $this->model = new PpkService($this->db);
    }

    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/ppk";
        $back_url = $base_url . '/' . $ref;

        $dataDB = $this->safeModelCall($this->model, 'getDetailKegiatan', [$id], null);
        
        if (!$dataDB) { echo "Data tidak ditemukan."; return; }

        $kakId = $dataDB['kakId'];
        $indikator = $this->safeModelCall($this->model, 'getIndikatorByKAK', [$kakId], []);
        $tahapan   = $this->safeModelCall($this->model, 'getTahapanByKAK', [$kakId], []);
        $rab       = $this->safeModelCall($this->model, 'getRABByKAK', [$kakId], []);

        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) { $tahapan_string .= ($idx + 1) . ". " . $t . "\n"; }
        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];
        $surat_url = !empty($dataDB['suratPengantar']) ? '/docutrack/public/uploads/surat/' . $dataDB['suratPengantar'] : '';

        $status_asli = ($dataDB['status_text'] ?? 'Menunggu');
        $posisi_saat_ini = $dataDB['posisiId'];
        $role_ppk = 4;

        $temp_status = 'Menunggu';

        if ($posisi_saat_ini != $role_ppk && $status_asli != 'Ditolak') {
            $status_tampilan = 'Disetujui';
        } else {
            $status_tampilan = $temp_status;
        }

        $kegiatan_data = [
            'nama_pengusul' => $dataDB['nama_pengusul'] ?? '-',
            'nim_pengusul' => $dataDB['nim_pelaksana'] ?? '-',
            'nama_pelaksana' => $dataDB['nama_pelaksana'] ?? '-',
            'nama_penanggung_jawab' => $dataDB['nama_pj'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nim_pj'] ?? '-',
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
            'title' => 'Telaah Usulan (PPK) - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => $status_tampilan,
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

    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = $_SESSION['user_id'] ?? 0;
            
            $kegiatan = $this->model->getDetailKegiatan($id);
            $oldStatusId = $kegiatan['statusUtamaId'] ?? null;
            
            if($this->model->approveUsulan($id)) {
                if (function_exists('logApproval')) {
                    logApproval($userId, $id, 'PPK', true, 
                        'Kegiatan: ' . ($kegiatan['namaKegiatan'] ?? 'Unknown'),
                        $oldStatusId, 3);
                }
                
                header('Location: /docutrack/public/ppk/dashboard?msg=approved');
                exit;
            }
        }
        header('Location: /docutrack/public/ppk/telaah/show/'.$id);
    }
}