<?php

namespace App\Controllers\PPK;

use App\Core\Controller;
use App\Models\kegiatan\KegiatanModel;
use App\Models\PpkModel;
use App\Services\LogStatusService;
use App\Services\PpkService;
use App\Services\ValidationService;
use Exception;

class TelaahController extends Controller
{
    private PpkService $service;

    public function __construct()
    {
        parent::__construct();
        $this->service = new PpkService($this->db);
    }

    public function show($id, $data_dari_router = [])
    {
        $ref = $_GET['ref'] ?? 'dashboard';
        $base_url = "/docutrack/public/ppk";
        $back_url = $base_url . '/' . $ref;

        $dataDB = $this->safeModelCall($this->service, 'getDetailKegiatan', [$id], null);

        if (!$dataDB) {
            echo "Data tidak ditemukan.";
            return;
        }

        $kakId = $dataDB['kakId'];
        $indikator = $this->safeModelCall($this->service, 'getIndikatorByKAK', [$kakId], []);
        $tahapan   = $this->safeModelCall($this->service, 'getTahapanByKAK', [$kakId], []);
        $rab       = $this->safeModelCall($this->service, 'getRABByKAK', [$kakId], []);

        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }
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
            'penerima_manfaat' => $dataDB['penerimaManfaat'],
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

    public function approve($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $rekomendasi = trim($_POST['rekomendasi'] ?? '');

                if ($this->service->approveUsulan((int)$id, $rekomendasi)) {
                    $_SESSION['flash_message'] = 'Usulan berhasil disetujui dan diteruskan ke Wakil Direktur.';
                    header('Location: /docutrack/public/ppk/dashboard?msg=approved');
                    exit;
                } else {
                    throw new Exception('Gagal memproses persetujuan.');
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
                header('Location: /docutrack/public/ppk/telaah/show/' . $id);
                exit;
            }
        }
        // Jika bukan POST, redirect kembali
        header('Location: /docutrack/public/ppk/telaah/show/' . $id);
        exit;
    }

    public function reject($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $alasan = trim($_POST['alasan_penolakan'] ?? '');
                if (empty($alasan)) {
                    throw new Exception('Alasan penolakan wajib diisi.');
                }

                if ($this->service->rejectUsulan((int)$id, $alasan)) {
                    $_SESSION['flash_message'] = 'Usulan telah ditolak.';
                    header('Location: /docutrack/public/ppk/dashboard?msg=rejected');
                    exit;
                } else {
                    throw new Exception('Gagal menolak usulan.');
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
                header('Location: /docutrack/public/ppk/telaah/show/' . $id);
                exit;
            }
        }
        header('Location: /docutrack/public/ppk/telaah/show/' . $id);
        exit;
    }

    public function revise($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $komentar = trim($_POST['komentar_revisi'] ?? '');
                if (empty($komentar)) {
                    throw new Exception('Komentar revisi wajib diisi.');
                }

                if ($this->service->reviseUsulan((int)$id, $komentar)) {
                    $_SESSION['flash_message'] = 'Usulan telah dikembalikan untuk direvisi.';
                    header('Location: /docutrack/public/ppk/dashboard?msg=revised');
                    exit;
                } else {
                    throw new Exception('Gagal mengirim permintaan revisi.');
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
                header('Location: /docutrack/public/ppk/telaah/show/' . $id);
                exit;
            }
        }
        header('Location: /docutrack/public/ppk/telaah/show/' . $id);
        exit;
    }
}
