<?php
// File: src/controllers/Bendahara/PencairandanaController.php

require_once '../src/core/Controller.php';
require_once '../src/model/bendaharaModel.php';
require_once '../src/helpers/logger_helper.php';

class BendaharaPencairandanaController extends Controller {
    
    private $model;
    
    public function __construct() {
        $this->model = new bendaharaModel();
    }

    /**
     * Halaman List Pencairan Dana
     */
    public function index($data_dari_router = []) {
        
        $stats = $this->model->getDashboardStats();
        $list_antrian = $this->model->getAntrianPencairan();
        $jurusan_list = $this->model->getListJurusan();

        $data = array_merge($data_dari_router, [
            'title' => 'List Pencairan Dana',
            'stats' => [
                'total' => $stats['total'] ?? 0,
                'menunggu' => $stats['menunggu'] ?? 0,
                'dicairkan' => $stats['dicairkan'] ?? 0
            ],
            'list_kak' => $list_antrian,
            'jurusan_list' => $jurusan_list
        ]);

        $this->view('pages/bendahara/pencairan-dana', $data, 'bendahara');
    }

    /**
     * Halaman Detail Pencairan
     */
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'pencairan-dana';
        $base_url = "/docutrack/public/bendahara";
        $back_url = $base_url . '/' . $ref;

        $kegiatan = $this->model->getDetailPencairan($id);
        
        if (!$kegiatan) {
            $_SESSION['flash_error'] = 'Data tidak ditemukan.';
            header('Location: ' . $back_url);
            exit;
        }
        
        $rab_data = $this->model->getRABByKegiatan($id);
        $iku_data = $this->model->getIKUByKegiatan($id);
        $indikator_data = $this->model->getIndikatorByKegiatan($id);
        $tahapan = $this->model->getTahapanByKegiatan($id);
        
        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }
        
        $is_sudah_dicairkan = !empty($kegiatan['tanggalPencairan']);
        
        if ($is_sudah_dicairkan) {
            $status_display = 'Dana Diberikan';
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
                'nama_pengusul' => $kegiatan['pemilikKegiatan'] ?? '-',
                'nim_pengusul' => $kegiatan['nimPelaksana'] ?? '-',
                'nama_penanggung_jawab' => $kegiatan['namaPenanggungJawab'] ?? '-',
                'nip_penanggung_jawab' => $kegiatan['nip'] ?? '-',
                'nama_kegiatan' => $kegiatan['namaKegiatan'] ?? '-',
                'gambaran_umum' => $kegiatan['gambaranUmum'] ?? '-',
                'penerima_manfaat' => $kegiatan['penerimaMaanfaat'] ?? '-',
                'metode_pelaksanaan' => $kegiatan['metodePelaksanaan'] ?? '-',
                'tahapan_kegiatan' => $tahapan_string ?: '-',
                'tanggal_mulai' => $kegiatan['tanggalMulai'] ?? '',
                'tanggal_selesai' => $kegiatan['tanggalSelesai'] ?? ''
            ],
            
            'iku_data' => $iku_data,
            'indikator_data' => $indikator_data,
            
            'rab_data' => $rab_data,
            'anggaran_disetujui' => $kegiatan['total_rab'] ?? 0,
            
            'surat_pengantar_url' => $kegiatan['suratPengantarUrl'] ?? '',
            
            'jumlah_dicairkan' => $kegiatan['jumlahDicairkan'] ?? 0,
            'tanggal_pencairan' => $kegiatan['tanggalPencairan'] ?? null,
            'metode_pencairan' => $kegiatan['metodePencairan'] ?? 'uang_muka',
            'catatan_bendahara' => $kegiatan['catatanBendahara'] ?? '',
            
            'back_url' => $back_url,
            'back_text' => 'Kembali'
        ]);

        $this->view('pages/bendahara/pencairan-dana-detail', $data, 'bendahara');
    }

    /**
     * Proses Pencairan Dana dengan Audit Logging.
     */
    public function proses() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        $kak_id = $_POST['kak_id'] ?? null;
        $action = $_POST['action'] ?? null;
        $userId = $_SESSION['user_id'] ?? 0;
        
        if (!$kak_id || !$action) {
            $_SESSION['flash_error'] = 'Data tidak lengkap!';
            header('Location: /docutrack/public/bendahara/pencairan-dana');
            exit;
        }

        try {
            if ($action === 'cairkan') {
                $jumlah_dicairkan = floatval($_POST['jumlah_dicairkan'] ?? 0);
                $metode_pencairan = $_POST['metode_pencairan'] ?? 'uang_muka';
                $catatan = trim($_POST['catatan'] ?? '');
                $tenggat_lpj = $_POST['tenggat_lpj'] ?? null;
                
                if ($jumlah_dicairkan <= 0) {
                    throw new Exception('Jumlah pencairan harus lebih dari 0');
                }
                
                if (empty($tenggat_lpj)) {
                    throw new Exception('Tanggal batas LPJ wajib diisi');
                }
                
                if ($this->model->prosesPencairan($kak_id, $jumlah_dicairkan, $metode_pencairan, $catatan, $tenggat_lpj)) {
                    
                    logPencairan($userId, $kak_id, $jumlah_dicairkan, $metode_pencairan, $catatan);
                    
                    $_SESSION['flash_message'] = 'Dana berhasil dicairkan sebesar Rp ' . number_format($jumlah_dicairkan, 0, ",", ".") . '. Batas pengumpulan LPJ: ' . $tenggat_lpj;
                    $_SESSION['flash_type'] = 'success';
                } else {
                    throw new Exception('Gagal memproses pencairan');
                }
                
            } elseif ($action === 'tolak') {
                $catatan = trim($_POST['catatan'] ?? '');
                
                if (empty($catatan)) {
                    $_SESSION['flash_error'] = 'Catatan penolakan wajib diisi!';
                    header('Location: /docutrack/public/bendahara/pencairan-dana/show/' . $kak_id);
                    exit;
                }
                
                writeLog($userId, 'PENCAIRAN_REJECT', 
                    "Menolak pencairan untuk kegiatan ID: $kak_id. Alasan: $catatan",
                    'kegiatan', $kak_id);
                
                $_SESSION['flash_message'] = 'Pencairan ditolak.';
                $_SESSION['flash_type'] = 'warning';
                
            } else {
                throw new Exception('Action tidak valid');
            }

        } catch (Exception $e) {
            writeLog($userId, 'PENCAIRAN_PROSES', 
                "Error proses pencairan kegiatan ID: $kak_id - " . $e->getMessage(),
                'kegiatan', $kak_id);
            
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }

        header('Location: /docutrack/public/bendahara/pencairan-dana');
        exit;
    }
}
