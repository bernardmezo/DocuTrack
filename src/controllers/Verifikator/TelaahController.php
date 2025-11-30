<?php
// File: src/controllers/Verifikator/TelaahController.php

require_once '../src/core/Controller.php';
require_once '../src/model/verifikatorModel.php'; // Load Model

class VerifikatorTelaahController extends Controller {
    
    /**
     * METHOD: index()
     * Menampilkan halaman daftar antrian telaah (Data Real dari DB).
     */
    public function index($data_dari_router = []) {
        
        // 1. Panggil Model
        $model = new verifikatorModel();

        // 2. Ambil Semua Data KAK dari Database
        // Kita gunakan fungsi yang sudah ada: getDashboardKAK()
        // Fungsi ini sudah mengambil kolom id, nama, pengusul, status, tanggal, dll.
        $all_usulan = $model->getDashboardKAK();

        $allJuru = $model->getListJurusan();

        // 3. Filter Data (Hanya ambil yang butuh aksi Verifikator)
        // Status: 'Menunggu' (Baru masuk) atau 'Telah Direvisi' (Balikan dari Admin)
        $list_usulan = [];
        $jurusan_set = []; // Untuk filter dropdown di view
        
        foreach ($all_usulan as $usulan) {
            $status_lower = strtolower($usulan['status']);
            
            // Filter Status
            if ($status_lower === 'menunggu' || $status_lower === 'telah direvisi') {
                $list_usulan[] = $usulan;
                
                // Kumpulkan jurusan untuk filter dropdown
                if (!empty($usulan['jurusan'])) {
                    $jurusan_set[$usulan['jurusan']] = true;
                }
            }
        }
        
        // 4. Sorting (Opsional): Prioritaskan "Telah Direvisi" di paling atas
        usort($list_usulan, function($a, $b) {
            $priority = ['telah direvisi' => 1, 'menunggu' => 2];
            $a_status = strtolower($a['status']);
            $b_status = strtolower($b['status']);
            
            $a_prio = $priority[$a_status] ?? 99;
            $b_prio = $priority[$b_status] ?? 99;
            
            // Jika prioritas sama, urutkan berdasarkan tanggal (terbaru di atas)
            if ($a_prio === $b_prio) {
                return strtotime($b['tanggal_pengajuan']) - strtotime($a['tanggal_pengajuan']);
            }
            return $a_prio - $b_prio;
        });

        // 5. Kirim Data ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Pengajuan Telaah',
            'list_usulan' => $list_usulan, 
            'jurusan_list' => array_keys($jurusan_set),
            'jumlah_menunggu' => count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'menunggu')),
            'jumlah_telah_direvisi' => count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'telah direvisi'))
        ]);

        // Tampilkan View (Bukan Redirect lagi)
        $this->view('pages/verifikator/pengajuan_telaah', $data, 'verifikator');
    }

    /**
        * METHOD: show($id)
        * nampilkan detail telaah(KAK) buat satu usulan berdasarkan ID. 
    */ 
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? '';
        $base_url = "/docutrack/public/verifikator";
        
        // Logika tombol kembali
        switch ($ref) {
            case 'dashboard': $back_url = $base_url . '/dashboard'; break;
            case 'riwayat-verifikasi': $back_url = $base_url . '/riwayat-verifikasi'; break;
            default: $back_url = $base_url . '/pengajuan-telaah'; break;
        }

        // 1. Panggil Model
        $model = new verifikatorModel();
        
        // 2. Ambil Data Real dari DB
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) {
            // Handle jika ID tidak ditemukan
            echo "Data tidak ditemukan."; return;
        }

        $kakId = $dataDB['kakId'];

        // 3. Ambil Data Relasi (RAB, Indikator, Tahapan)
        $indikator = $model->getIndikatorByKAK($kakId);
        $tahapan   = $model->getTahapanByKAK($kakId);
        $rab       = $model->getRABByKAK($kakId);

        // 4. FORMATTING DATA (Mapping DB -> View)
        
        // Format Tahapan (Array -> String dengan nomor)
        $tahapan_string = "";
        foreach ($tahapan as $idx => $t) {
            $tahapan_string .= ($idx + 1) . ". " . $t . "\n";
        }

        // Format IKU (String CSV -> Array)
        $iku_array = !empty($dataDB['iku']) ? explode(',', $dataDB['iku']) : [];

        // Siapkan array kegiatan_data sesuai nama variabel di View
        $kegiatan_data = [
            'kegiatanId' => $dataDB['kegiatanId'],
            'nama_pengusul' => $dataDB['pemilikKegiatan'],
            'nim_pengusul' => $dataDB['nimPelaksana'],
            'nama_penanggung_jawab' => $dataDB['namaPenanggungJawab'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nip'] ?? '-',
            'jurusan' => $dataDB['jurusanPenyelenggara'],
            'nama_kegiatan' => $dataDB['namaKegiatan'],
            'gambaran_umum' => $dataDB['gambaranUmum'],
            'penerima_manfaat' => $dataDB['penerimaMaanfaat'], // Sesuaikan ejaan DB
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'],
            'tahapan_kegiatan' => $tahapan_string,
            'surat_pengantar' => '', // Sesuaikan jika ada kolom file
            'tanggal_mulai' => '',
            'tanggal_selesai' => ''
        ];

        // 5. Kirim ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Telaah Usulan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            
            // KUNCI LOGIKA TAMPILAN: Status diambil real dari DB
            'status' => ucfirst($dataDB['status_text'] ?? 'Menunggu'), 
            
            'user_role' => $_SESSION['user_role'] ?? 'verifikator',
            'id' => $id,
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            
            // KUNCI LOGIKA KODE MAK: Ambil dari DB
            'kode_mak' => $dataDB['buktiMAK'] ?? '', 
            
            'komentar_revisi' => [], // Belum ada tabel komentar di ERD, kosongkan dulu
            'komentar_penolakan' => '',
            'surat_pengantar_url' => '#',
            'back_url' => $back_url
        ]);

        $this->view('pages/verifikator/telaah_detail', $data, 'verifikator');
    }

    /** 
     * METHOD: approve($id)
     * buat menyetujui usulan dengan ID tertentu.
     */
    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kode_mak = $_POST['kode_mak'] ?? '';
            $model = new verifikatorModel();
            
            // Update DB: Status jadi Disetujui (ID 3) & Simpan MAK
            if($model->approveUsulan($id, $kode_mak)) {
                // Redirect Sukses
                header('Location: /docutrack/public/verifikator/dashboard?msg=approved');
                exit;
            }
        }
        header('Location: /docutrack/public/verifikator/telaah/show/'.$id.'?ref=dashboard');
    }

    /** 
     * METHOD: reject
     *  buat nolak usulan dengan ID tertentu.
     */
    public function reject($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new verifikatorModel();
            // Update DB: Status jadi Ditolak (ID 4)
            if($model->updateStatus($id, 4)) {
                header('Location: /docutrack/public/verifikator/dashboard?msg=rejected');
                exit;
            }
        }
    }

    /**
     * METHOD: revise($id)
     * buat mengirim usulan yang udah direvisi dengan ID tertentu.
     */
    public function revise($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $model = new verifikatorModel();
            // Update DB: Status jadi Revisi (ID 2)
            // Note: Idealnya simpan juga komentar revisinya ke tabel history/komentar
            if($model->updateStatus($id, 2)) {
                header('Location: /docutrack/public/verifikator/dashboard?msg=revised');
                exit;
            }
        }
    }
}