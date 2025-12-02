<?php
// File: src/controllers/Admin/AdminPengajuanKegiatanController.php

require_once '../src/core/Controller.php';
require_once '../src/model/adminModel.php';
require_once '../src/helpers/logger_helper.php';

class AdminPengajuanKegiatanController extends Controller {
    
    /**
     * Menampilkan halaman daftar pengajuan kegiatan dengan filtering role-based.
     */
    public function index($data_dari_router = []) { 
        $model = new adminModel();
        
        $userRole = $_SESSION['user_role'] ?? '';
        $userJurusan = $_SESSION['user_jurusan'] ?? null;
        
        if ($userRole === 'super-admin' || $userRole === 'superadmin') {
            $all_kegiatan = $model->getDashboardKAK();
        } else {
            $all_kegiatan = $model->getDashboardKAKByJurusan($userJurusan);
        }
        
        $list_kegiatan_disetujui = array_filter($all_kegiatan, function($item) {
            $posisi = $item['posisi'] ?? null;
            $statusId = $item['statusUtamaId'] ?? null;
            $status = strtolower($item['status'] ?? '');
            
            return ($posisi == 1 && ($statusId == 3 || $status === 'usulan disetujui'));
        });

        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan Kegiatan',
            'list_kegiatan' => array_values($list_kegiatan_disetujui)
        ]);

        $this->view('pages/admin/pengajuan_kegiatan_list', $data, 'app');
    }

    /**
     * Menampilkan halaman detail atau rincian kegiatan.
     */
    public function show($id, $data_dari_router = []) {
        $mode = $_GET['mode'] ?? 'detail';
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        $model = new adminModel();
        
        $kegiatanDB = $model->getDetailKegiatan($id);

        if (!$kegiatanDB) {
            echo "<h1>404 - Kegiatan dengan ID $id tidak ditemukan.</h1>";
            exit;
        }
        
        if ($mode === 'rincian') {
            $data = array_merge($data_dari_router, [
                'title' => 'Rincian Kegiatan - ' . htmlspecialchars($kegiatanDB['namaKegiatan']),
                'kegiatan_id' => $id,
                'namaKeg' => $kegiatanDB['namaKegiatan'],
                'back_url' => $back_url
            ]);

            $this->view('pages/admin/detail_kegiatan', $data, 'app');
            return;
        }
        
        $kakId = $kegiatanDB['kakId'];
        
        $iku_string = $kegiatanDB['iku'] ?? '';
        $iku_array = !empty($iku_string) ? explode(',', $iku_string) : [];
        
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan = $model->getTahapanByKAK($kakId);
        $rab = $model->getRABByKAK($kakId);

        $surat_pengantar_url = '';
        if (!empty($kegiatanDB['suratPengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $kegiatanDB['suratPengantar'];
        }

        $kak_formatted = [
            'nama_pengusul' => $kegiatanDB['pemilikKegiatan'],
            'nim' => $kegiatanDB['nimPelaksana'],
            'jurusan' => $kegiatanDB['jurusanPenyelenggara'],
            'prodi' => $kegiatanDB['prodiPenyelenggara'],
            'nama_kegiatan' => $kegiatanDB['namaKegiatan'],
            'gambaran_umum' => $kegiatanDB['gambaranUmum'],
            'penerima_manfaat' => $kegiatanDB['penerimaMaanfaat'],
            'metode_pelaksanaan' => $kegiatanDB['metodePelaksanaan'],
            'tanggal_mulai' => $kegiatanDB['tanggalMulai'] ?? '-',
            'tanggal_selesai' => $kegiatanDB['tanggalSelesai'] ?? '-',
            'surat_pengantar' => $kegiatanDB['suratPengantar'] ?? ''
        ];

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatanDB['namaKegiatan']),
            'status' => ucfirst($kegiatanDB['status_text'] ?? 'Menunggu'),
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            'kegiatan_data' => $kak_formatted,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'tahapan_data' => $tahapan,
            'rab_data' => $rab,
            'kode_mak' => $kegiatanDB['buktiMAK'] ?? '-',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kegiatan', $data, 'app');
    }

    /**
     * Menangani submit rincian kegiatan (upload surat & update DB).
     */
    public function submitRincian() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405); echo "Method not allowed"; return;
        }

        $kegiatan_id = $_POST['kegiatan_id'] ?? null;
        
        $data_update = [
            'namaPj'        => $_POST['penanggung_jawab'] ?? '',
            'nimNipPj'         => $_POST['nim_nip_pj'] ?? '',
            'tgl_mulai'   => $_POST['tanggal_mulai'] ?? null,
            'tgl_selesai' => $_POST['tanggal_selesai'] ?? null
        ];

        $file_name = null;
        
        if (isset($_FILES['surat_pengantar']) && $_FILES['surat_pengantar']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['surat_pengantar']['tmp_name'];
            $file_original_name = $_FILES['surat_pengantar']['name'];
            $file_ext = strtolower(pathinfo($file_original_name, PATHINFO_EXTENSION));
            
            $allowed_extensions = ['pdf', 'doc', 'docx'];
            if (in_array($file_ext, $allowed_extensions)) {
                $file_name = 'surat_' . $kegiatan_id . '_' . time() . '.' . $file_ext;
                $upload_dir = __DIR__ . '/../../../public/uploads/surat';
                
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                
                if (!move_uploaded_file($file_tmp, $upload_dir . $file_name)) {
                    header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatan_id . '?mode=rincian&error=upload_failed');
                    exit;
                }
            }
        }

        $model = new adminModel();
        
        if ($model->updateRincianKegiatan($kegiatan_id, $data_update, $file_name)) {
            header('Location: /docutrack/public/admin/pengajuan-kegiatan/show/' . $kegiatan_id);
            exit;
        } else {
            echo "Gagal update database.";
        }
    }

    /**
     * Mendownload surat pengantar dengan validasi keamanan.
     */
    public function downloadSurat($filename) {
        $safe_filename = basename($filename);
        $upload_dir = realpath(__DIR__ . '/../../../public/uploads/surat');
        
        if ($upload_dir === false) {
            http_response_code(500);
            echo "Direktori upload tidak ditemukan.";
            return;
        }
        
        $file_path = realpath($upload_dir . DIRECTORY_SEPARATOR . $safe_filename);
        
        if ($file_path === false || !file_exists($file_path)) {
            http_response_code(404);
            echo "File tidak ditemukan.";
            return;
        }
        
        if (strpos($file_path, $upload_dir) !== 0) {
            http_response_code(403);
            error_log("[SECURITY] Path traversal attempt blocked: {$filename} from IP: {$_SERVER['REMOTE_ADDR']}");
            echo "Akses ditolak.";
            return;
        }
        
        $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $extension = strtolower(pathinfo($safe_filename, PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed_extensions)) {
            http_response_code(403);
            echo "Tipe file tidak diizinkan.";
            return;
        }
        
        $mime_types = [
            'pdf'  => 'application/pdf',
            'doc'  => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png'
        ];
        
        header('Content-Type: ' . ($mime_types[$extension] ?? 'application/octet-stream'));
        header('Content-Disposition: attachment; filename="' . $safe_filename . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');
        
        readfile($file_path);
        exit;
    }
}