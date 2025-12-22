<?php

namespace App\Controllers\Wadir;

use App\Core\Controller;
use App\Services\WadirService;

if (file_exists(DOCUTRACK_ROOT . '/src/helpers/logger_helper.php')) {
    require_once DOCUTRACK_ROOT . '/src/helpers/logger_helper.php';
}

class TelaahController extends Controller
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new WadirService($this->db);
    }

    public function show($id, $data_dari_router = [])
    {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/wadir";
        $back_url = $base_url . '/' . $ref;

        // Debug logging
        error_log("WadirController::show - Requested ID: {$id}");
        error_log("WadirController::show - Model class: " . get_class($this->model));
        error_log("WadirController::show - Method exists: " . (method_exists($this->model, 'getDetailKegiatan') ? 'YES' : 'NO'));
        
        $dataDB = $this->safeModelCall($this->model, 'getDetailKegiatan', [$id], null);
        
        error_log("WadirController::show - Data returned: " . ($dataDB ? 'YES' : 'NO/NULL'));
        if ($dataDB) {
            error_log("WadirController::show - Kegiatan: " . ($dataDB['namaKegiatan'] ?? 'N/A'));
        }

        if (!$dataDB) {
            error_log("WadirController::show - RETURNING: Data tidak ditemukan");
            echo "Data tidak ditemukan.";
            return;
        }

        $kakId = $dataDB['kakId'];
        $indikator = $this->safeModelCall($this->model, 'getIndikatorByKAK', [$kakId], []);
        $tahapan   = $this->safeModelCall($this->model, 'getTahapanByKAK', [$kakId], []);
        $rab       = $this->safeModelCall($this->model, 'getRABByKAK', [$kakId], []);

        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }
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
            'penerima_manfaat' => $dataDB['penerimaManfaat'],
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

    public function approve($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->model->approveUsulan($id)) {
                header('Location: /docutrack/public/wadir/dashboard?msg=approved');
                exit;
            }
        }
        header('Location: /docutrack/public/wadir/telaah/show/' . $id);
        exit;
    }

    public function reject($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $alasan = trim($_POST['alasan_penolakan'] ?? '');
                if (empty($alasan)) {
                    throw new \Exception('Alasan penolakan wajib diisi.');
                }

                if ($this->model->rejectUsulan((int)$id, $alasan)) {
                    $_SESSION['flash_message'] = 'Usulan telah ditolak.';
                    header('Location: /docutrack/public/wadir/dashboard?msg=rejected');
                    exit;
                } else {
                    throw new \Exception('Gagal menolak usulan.');
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
                header('Location: /docutrack/public/wadir/telaah/show/' . $id);
                exit;
            }
        }
        header('Location: /docutrack/public/wadir/telaah/show/' . $id);
        exit;
    }

    public function revise($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $komentar = trim($_POST['komentar_revisi'] ?? '');
                if (empty($komentar)) {
                    throw new \Exception('Komentar revisi wajib diisi.');
                }

                if ($this->model->reviseUsulan((int)$id, $komentar)) {
                    $_SESSION['flash_message'] = 'Usulan telah dikembalikan untuk direvisi.';
                    header('Location: /docutrack/public/wadir/dashboard?msg=revised');
                    exit;
                } else {
                    throw new \Exception('Gagal mengirim permintaan revisi.');
                }
            } catch (\Exception $e) {
                $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
                header('Location: /docutrack/public/wadir/telaah/show/' . $id);
                exit;
            }
        }
        header('Location: /docutrack/public/wadir/telaah/show/' . $id);
        exit;
    }
}
