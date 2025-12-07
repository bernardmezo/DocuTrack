<?php
namespace App\Controllers\Wadir;

use App\Core\Controller;
use App\Services\WadirService;

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/logger_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/logger_helper.php';
}

class TelaahController extends Controller {
    
    private $model;

    public function __construct() {
        parent::__construct();
        $this->model = new WadirService($this->db);
    }

    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/wadir";
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
        
        $status_asli = ucfirst($dataDB['status_text'] ?? 'Menunggu');
        $posisi_saat_ini = $dataDB['posisiId'];
        $role_wadir = 3;

        if ($posisi_saat_ini != $role_wadir && $status_asli != 'Ditolak') {
            $status_tampilan = 'Disetujui';
        } else {
            $status_tampilan = $status_asli;
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
            $userId = $_SESSION['user_id'] ?? 0;
            
            $kegiatan = $this->model->getDetailKegiatan($id);
            $oldStatusId = $kegiatan['statusUtamaId'] ?? null;
            
            if($this->model->approveUsulan($id)) {
                if(function_exists('logApproval')) {
                    logApproval($userId, $id, 'WADIR', true, 
                        'Kegiatan: ' . ($kegiatan['namaKegiatan'] ?? 'Unknown'),
                        $oldStatusId, 3);
                }
                
                header('Location: /docutrack/public/wadir/dashboard?msg=approved');
                exit;
            }
        }
        header('Location: /docutrack/public/wadir/telaah/show/'.$id);
    }
}