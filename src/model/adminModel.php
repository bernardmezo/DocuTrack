<?php
// File: src/models/adminModel.php

class adminModel {
    private $db;

    public function __construct() {
        // Hubungkan ke database
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Koneksi database gagal di adminModel.");
        }
    }

    /**
     * ====================================================
     * 1. MENGAMBIL DATA STATISTIK (KARTU ATAS DASHBOARD)
     * ====================================================
     */
    public function getDashboardStats() {
        // Menghitung total berdasarkan statusUtamaId di tbl_kegiatan
        // Asumsi ID Status: 
        // 1 = Menunggu, 2 = Revisi (Pending Group)
        // 3 = Disetujui
        // 4 = Ditolak
        
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 5 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId = 1 OR statusUtamaId = 2 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";   
        
        $result = mysqli_query($this->db, $query);
        if ($result) {
            return mysqli_fetch_assoc($result);
        }
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0];
    }

    /**
     * ====================================================
     * 2. MENGAMBIL LIST KAK (UNTUK TABEL KAK)
     * ====================================================
     */
    public function getDashboardKAK() {
        // Mengambil data kegiatan join dengan status
        // Menggunakan CONCAT untuk menggabungkan nama pengusul sesuai format tabel dashboard
        
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    CONCAT(k.pemilikKegiatan, ' (', k.nimPelaksana, '), ', k.prodiPenyelenggara) as pengusul,
                    k.createdAt as tanggal_pengajuan,
                    k.posisiId as posisi,
                    
                    -- Ambil nama status dari tabel relasi tbl_status_utama
                    s.namaStatusUsulan as status

                FROM tbl_kegiatan k
                LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                JOIN tbl_role r ON k.posisiId = r.roleId
                ORDER BY k.createdAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                // Opsional: Memperbaiki format status (Huruf Besar Awal)
                if (isset($row['status'])) {
                    $row['status'] = ucfirst($row['status']); 
                } else {
                    $row['status'] = 'Menunggu'; // Default jika null
                }
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * ====================================================
     * 3. MENGAMBIL LIST LPJ (UNTUK TABEL LPJ)
     * ====================================================
     */
    public function getDashboardLPJ() {
        // Mengambil data dari tbl_lpj dan join ke tbl_kegiatan untuk info detail
        // Sesuai ERD: tbl_lpj terhubung ke tbl_kegiatan via kegiatanId
        
        $query = "SELECT 
                    l.lpjId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    
                    -- Gunakan submittedAt sebagai tanggal pengajuan LPJ
                    l.submittedAt as tanggal_pengajuan,
                    
                    -- Logika Status LPJ berdasarkan kolom tanggal (sesuai ERD)
                    -- Jika approvedAt terisi = 'Setuju'
                    -- Jika submittedAt terisi tapi belum approved = 'Menunggu'
                    -- Default jika baru draft = 'Menunggu_Upload'
                    CASE 
                        WHEN l.approvedAt IS NOT NULL THEN 'Setuju'
                        WHEN l.submittedAt IS NOT NULL THEN 'Menunggu'
                        ELSE 'Menunggu_Upload'
                    END as status

                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  ORDER BY l.submittedAt DESC";

        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

    /**
     * ====================================================
     * 4. BUAT NGAMBIL DETAIL KAK TERTENTU
     * ====================================================
     */

    // 1. ambil detail KAK
    public function getDetailKegiatan($kegiatanId) {
        $query = "SELECT 
                    k.*, 
                    kak.*, -- Mengambil semua kolom dari tbl_kak (kakId, iku, gambaranUmum, dll)
                    s.namaStatusUsulan as status_text
                  FROM tbl_kegiatan k
                  -- Gunakan JOIN karena setiap kegiatan pasti punya KAK (one-to-one)
                  JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  WHERE k.kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        return mysqli_fetch_assoc($result);
    }

    // 2. ambil Indikator KAK
    public function getIndikatorByKAK($kakId) {
        $query = "SELECT 
                    bulan, 
                    indikatorKeberhasilan as nama, 
                    targetPersen as target 
                FROM tbl_indikator_kak 
                WHERE kakId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    // 3. ambil tahapan pelaksanaan
    public function getTahapanByKAK($kakId) {
        $query = "SELECT namaTahapan FROM tbl_tahapan_pelaksanaan WHERE kakId = ? ORDER BY tahapanId ASC";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row['namaTahapan'];
        }
        return $data; // Mengembalikan array string sederhana
    }

    // 4. ambil RAB (di grup dengan kategoti)
    public function getRABByKAK($kakId) {
        $query = "SELECT 
                    r.*, 
                    cat.namaKategori 
                FROM tbl_rab r
                JOIN tbl_kategori_rab cat ON r.kategoriId = cat.kategoriRabId
                WHERE r.kakId = ?
                ORDER BY cat.kategoriRabId ASC, r.rabItemId ASC";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $kakId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            
            $data[$row['namaKategori']][] = $row;
        }
        return $data;
    }

    // 5. ambil komentar revisi (kalo ada)
    public function getKomentarTerbaru($kegiatanId) {
        // Mengambil komentar dari revisi terakhir
        // Query ini mengasumsikan struktur tabel revisi_comment yang terhubung ke history
        // Jika belum ada fitur komentar aktif, return array kosong dulu
        
        /* $query = "SELECT targetKolom, komentarRevisi FROM tbl_revisi_comment ...";
        */
        
        return []; // Placeholder: Return kosong dulu agar tidak error
    }

    /**
     * ====================================================
     * 5. BUAT INSERT KAK LENGKAP
     * ====================================================
     */
    public function simpanPengajuan($data) {
        // $data adalah isi dari $_POST

        // 1. Mulai Transaksi
        mysqli_begin_transaction($this->db);

        try {
            // ==========================================
            // A. INSERT KE tbl_kegiatan (Tabel Induk)
            // ==========================================
            $nama_pengusul = $data['nama_pengusul'] ?? '';
            $nim           = $data['nim_nip'] ?? '';
            $jurusan       = $data['jurusan'] ?? '';
            $prodi         = $data['prodi'] ?? '';
            $nama_kegiatan = $data['nama_kegiatan_step1'] ?? ''; // Pastikan key sesuai form (step1)
            $user_id       = $_SESSION['user_id'] ?? 0;
            $tgl_sekarang  = date('Y-m-d H:i:s');
            $status_awal   = 1; 
            $wadir_tujuan  = $data['wadir_tujuan'] ?? null; 

            $queryKegiatan = "INSERT INTO tbl_kegiatan 
            (namaKegiatan, prodiPenyelenggara, pemilikKegiatan, nimPelaksana, userId, jurusanPenyelenggara, statusUtamaId, createdAt, wadirTujuan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = mysqli_prepare($this->db, $queryKegiatan);
            // Bind: s=string, i=integer
            mysqli_stmt_bind_param($stmt, "ssssisisi", $nama_kegiatan, $prodi, $nama_pengusul, $nim, $user_id, $jurusan, $status_awal, $tgl_sekarang, $wadir_tujuan);
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal insert kegiatan: " . mysqli_error($this->db));
            }
            
            $kegiatanId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);


            // ==========================================
            // B. INSERT KE tbl_kak (Anak dari Kegiatan)
            // ==========================================
            $iku           = $data['indikator_kinerja'] ?? 'Belum pilih';
            $gambaran_umum = $data['gambaran_umum'] ?? '';
            $penerima      = $data['penerima_manfaat'] ?? '';
            $metode        = $data['metode_pelaksanaan'] ?? '';
            $tgl_only      = date('Y-m-d');
            
            $queryKAK = "INSERT INTO tbl_kak 
                (kegiatanId, iku, gambaranUmum, penerimaMaanfaat, metodePelaksanaan, tglPembuatan)
                VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($this->db, $queryKAK);
            mysqli_stmt_bind_param(
                $stmt, "isssss", $kegiatanId, $iku, $gambaran_umum, $penerima, $metode, $tgl_only
            );

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal insert KAK: " . mysqli_error($this->db));
            }

            $kakId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);


            // ==========================================
            // C. INSERT TAHAPAN PELAKSANAAN (Looping)
            // ==========================================
            if (!empty($data['tahapan']) && is_array($data['tahapan'])) {
                $queryTahapan = "INSERT INTO tbl_tahapan_pelaksanaan (kakId, namaTahapan) VALUES (?, ?)";
                $stmt = mysqli_prepare($this->db, $queryTahapan);
                
                foreach ($data['tahapan'] as $tahap) {
                    if (!empty($tahap)) {
                        mysqli_stmt_bind_param($stmt, "is", $kakId, $tahap);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Gagal insert tahapan: " . mysqli_error($this->db));
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }

            // ==========================================
            // D. INSERT INDIKATOR (Looping)
            // ==========================================
            if (!empty($data['indikator_nama']) && is_array($data['indikator_nama'])) {
                $queryIndikator = "INSERT INTO tbl_indikator_kak (kakId, bulan, indikatorKeberhasilan, targetPersen) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($this->db, $queryIndikator);
                
                $count = count($data['indikator_nama']);
                
                for ($i = 0; $i < $count; $i++) {
                    $bulan  = $data['indikator_bulan'][$i] ?? '';
                    $nama   = $data['indikator_nama'][$i] ?? '';
                    $target = $data['indikator_target'][$i] ?? '';
                    
                    if (!empty($nama)) {
                        mysqli_stmt_bind_param($stmt, "isss", $kakId, $bulan, $nama, $target);
                        if (!mysqli_stmt_execute($stmt)) {
                            throw new Exception("Gagal insert indikator: " . mysqli_error($this->db));
                        }
                    }
                }
                mysqli_stmt_close($stmt);
            }

            // ==========================================
            // E. INSERT RAB & KATEGORI RAB (BARU)
            // ==========================================
            $rab_json = $data['rab_data'] ?? '[]'; 
            $budgetData = json_decode($rab_json, true);

            if (!empty($budgetData) && is_array($budgetData)) {
                
                $queryKategori = "INSERT INTO tbl_kategori_rab (namaKategori) VALUES (?)";
                $queryItemRAB  = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, sat1, sat2, vol1, vol2, harga, totalHarga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                foreach ($budgetData as $namaKategori => $items) {
                    if (empty($items)) continue; 

                    // 1. Cek / Insert Kategori
                    $kategoriId = 0;
                    
                    $checkKat = mysqli_prepare($this->db, "SELECT kategoriRabId FROM tbl_kategori_rab WHERE namaKategori = ? LIMIT 1");
                    mysqli_stmt_bind_param($checkKat, "s", $namaKategori);
                    mysqli_stmt_execute($checkKat);
                    $resKat = mysqli_stmt_get_result($checkKat);
                    
                    if ($rowKat = mysqli_fetch_assoc($resKat)) {
                        $kategoriId = $rowKat['kategoriRabId'];
                    } 
                    
                    // [PENTING] Tutup statement cek kategori SEBELUM membuat statement baru untuk insert
                    mysqli_stmt_close($checkKat); 

                    // Jika tidak ditemukan, Insert Baru
                    if ($kategoriId == 0) {
                        $stmtKat = mysqli_prepare($this->db, $queryKategori);
                        mysqli_stmt_bind_param($stmtKat, "s", $namaKategori);
                        if (!mysqli_stmt_execute($stmtKat)) {
                            throw new Exception("Gagal insert kategori RAB: " . mysqli_error($this->db));
                        }
                        $kategoriId = mysqli_insert_id($this->db);
                        mysqli_stmt_close($stmtKat);
                    }

                    // 2. Insert Item RAB
                    $stmtItem = mysqli_prepare($this->db, $queryItemRAB);
                    foreach ($items as $item) {
                        $uraian  = $item['uraian'] ?? '';
                        $rincian = $item['rincian'] ?? '';
                        
                        // --- PERBAIKAN LOGIC VOLUME & SATUAN ---
                        
                        // Ambil data dari key JS yang baru (vol1, vol2, sat1, sat2)
                        $vol1 = floatval($item['vol1'] ?? 0);
                        $vol2 = floatval($item['vol2'] ?? 1); // Default 1 agar perkalian aman
                        $sat1 = $item['sat1'] ?? '';
                        $sat2 = $item['sat2'] ?? '';

                        // LOGIKA 1: Jika di database kolom volume cuma satu, kita kalikan totalnya
                        $volume = $vol1 * $vol2; 
                        
                        // Harga Satuan
                        $harga   = floatval($item['harga'] ?? 0);
                        
                        // Total Harga
                        $total   = $volume * $harga;

                        // Bind parameter (Pastikan urutan tipe data sesuai dengan query INSERT kamu)
                        // Query kamu: (kakId, kategoriId, uraian, rincian, satuan, volume, harga, totalHarga)
                        // Tipe data:  i, i, s, s, s, d, d, d
                        mysqli_stmt_bind_param($stmtItem, "iissssdddd", $kakId, $kategoriId, $uraian, $rincian, $sat1, $sat2, $vol1, $vol2, $harga, $total);
                        if (!mysqli_stmt_execute($stmtItem)) {
                            throw new Exception("Gagal insert item RAB: " . mysqli_error($this->db));
                        }
                    }
                    mysqli_stmt_close($stmtItem);
                }
            }

            // ==========================================
            // F. SELESAI - COMMIT TRANSAKSI
            // ==========================================
            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            // Jika ada ERROR satu saja, batalkan semua perubahan (Rollback)
            mysqli_rollback($this->db);
            // Log error untuk developer
            var_dump($e->getMessage()); 
            die();
            error_log("Gagal Simpan Pengajuan: " . $e->getMessage());
            
            // (Opsional) Uncomment ini jika ingin melihat pesan error di layar saat debugging
            // echo "DEBUG ERROR: " . $e->getMessage(); die;
            
            return false;
        }
    }

    /**
     * ====================================================
     * 6. TAMBAHAN: UPDATE SURAT PENGANTAR
     * ====================================================
     */
    public function updateSuratPengantar($kegiatanId, $fileName) {
        // Asumsi kolom di database bernama 'suratPengantar' di tabel 'tbl_kegiatan'
        $query = "UPDATE tbl_kegiatan SET suratPengantar = ? WHERE kegiatanId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $fileName, $kegiatanId);
            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }
        return false;
    }

    /**
     * 7. Update Rincian Lengkap (PJ, Tanggal, Surat)
     */
    public function updateRincianKegiatan($id, $data, $fileSurat = null) {
        // $data berisi: ['nama', 'nim', 'tgl_mulai', 'tgl_selesai']
        
        if ($fileSurat) {
            // Jika ada file baru yang diupload, update kolom suratPengantar juga
            $query = "UPDATE tbl_kegiatan SET 
                        namaPJ = ?, 
                        nip = ?, 
                        tanggalMulai = ?, 
                        tanggalSelesai = ?, 
                        suratPengantar = ?,
                        posisiId = 4
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "sssssi", 
                $data['namaPj'], 
                $data['nimNipPj'], 
                $data['tgl_mulai'], 
                $data['tgl_selesai'], 
                $fileSurat, 
                $id
            );
        } else {
            // Jika TIDAK ada file (hanya update data teks), jangan timpa suratPengantar dengan null
            $query = "UPDATE tbl_kegiatan SET 
                        namaPJ = ?, 
                        nip = ?, 
                        tanggalMulai = ?, 
                        tanggalSelesai = ? 
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "ssssi", 
                $data['namaPJ'], 
                $data['nimNipPj'], 
                $data['tgl_mulai'], 
                $data['tgl_selesai'], 
                $id
            );
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }
}