<?php
// File: src/models/adminModel.php

class ModelAdmin {
    private $db;

    public function __construct() {
        // Hubungkan ke database
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Koneksi database gagal di ModelAdmin.");
        }
    }

    /**
     * 1. MENGAMBIL DATA STATISTIK (KARTU ATAS DASHBOARD)
     */
    public function getDashboardStats() {
        // Menghitung total berdasarkan statusUtamaId di tbl_kegiatan
        // Asumsi ID Status: 
        // 1 = Menunggu, 2 = Revisi (Pending Group)
        // 3 = Disetujui
        // 4 = Ditolak
        
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 3 THEN 1 ELSE 0 END) as disetujui,
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
     * 2. MENGAMBIL LIST KAK (UNTUK TABEL KAK)
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
                    
                    -- Ambil nama status dari tabel relasi tbl_status_utama
                    s.namaStatusUsulan as status

                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
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
     * 3. MENGAMBIL LIST LPJ (UNTUK TABEL LPJ)
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

    // --- FUNCTION SIMPAN PENGAJUAN (DARI SEBELUMNYA) ---
    public function simpanPengajuan($data) {
        // 1. Mulai Transaksi
        mysqli_begin_transaction($this->db);

        try {
            // A. INSERT KE tbl_kegiatan
            $jurusan       = $data['jurusan'] ?? ''; 
            $prodi         = $data['prodi'] ?? '';   
            $nama_pengusul = $data['nama_pengusul_step1'] ?? ''; 
            $nim           = $data['nim_nip'] ?? '';
            $nama_kegiatan = $data['nama_kegiatan_step1'] ?? '';
            $wadir_tujuan  = $data['wadir_tujuan'] ?? null; 
            $user_id       = $_SESSION['user_id'] ?? 0;
            $tgl_sekarang  = date('Y-m-d H:i:s');
            $status_awal   = 1; // ID Status Awal (Menunggu)

            $queryKegiatan = "INSERT INTO tbl_kegiatan 
            (namaKegiatan, prodiPenyelenggara, pemilikKegiatan, nimPelaksana, userId, jurusanPenyelenggara, statusUtamaId, wadirTujuan, createdAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
            $stmt = mysqli_prepare($this->db, $queryKegiatan);
            
            // Kode binding: ssssisiiBs -> sssisiis
            // nama(s), prodi(s), pemilik(s), nim(s), user(i), jurusan(s), status(i), wadir(i), tgl(s)
            mysqli_stmt_bind_param($stmt, "ssssisiis", 
                $nama_kegiatan, 
                $prodi,          
                $nama_pengusul, 
                $nim, 
                $user_id, 
                $jurusan,        
                $status_awal, 
                $wadir_tujuan, 
                $tgl_sekarang
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal insert kegiatan: " . mysqli_error($this->db));
            }
            
            $kegiatanId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);

            // B. INSERT KE tbl_kak
            $iku           = $data['indikator_kinerja'] ?? '';
            $gambaran_umum = $data['gambaran_umum'] ?? '';
            $penerima      = $data['penerima_manfaat'] ?? '';
            $metode        = $data['metode_pelaksanaan'] ?? '';
            $tgl_only      = date('Y-m-d');
            
            // Perhatikan nama kolom 'penerimaManfaat' vs 'penerimaMaanfaat' di DB
            // Di sini saya pakai 'penerimaManfaat' (ejaan benar), sesuaikan jika DB typo
            $queryKAK = "INSERT INTO tbl_kak 
                (kegiatanId, iku, gambaranUmum, penerimaManfaat, metodePelaksanaan, tglPembuatan)
                VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = mysqli_prepare($this->db, $queryKAK);
            mysqli_stmt_bind_param($stmt, "isssss", $kegiatanId, $iku, $gambaran_umum, $penerima, $metode, $tgl_only);

            if (!mysqli_stmt_execute($stmt)) {
                 throw new Exception("Gagal insert KAK: " . mysqli_error($this->db));
            }
            $kakId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);

            // C. INSERT TAHAPAN PELAKSANAAN
            if (!empty($data['tahapan']) && is_array($data['tahapan'])) {
                $queryTahapan = "INSERT INTO tbl_tahapan_pelaksanaan (kakId, namaTahapan) VALUES (?, ?)";
                $stmt = mysqli_prepare($this->db, $queryTahapan);
                foreach ($data['tahapan'] as $tahap) {
                    if (!empty($tahap)) {
                        mysqli_stmt_bind_param($stmt, "is", $kakId, $tahap);
                        if (!mysqli_stmt_execute($stmt)) throw new Exception("Gagal insert tahapan.");
                    }
                }
                mysqli_stmt_close($stmt);
            }

            // D. INSERT INDIKATOR KAK
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
                        if (!mysqli_stmt_execute($stmt)) throw new Exception("Gagal insert indikator.");
                    }
                }
                mysqli_stmt_close($stmt);
            }

            // E. INSERT RAB & KATEGORI
            $rab_json = $data['rab_json'] ?? '[]'; 
            $budgetData = json_decode($rab_json, true);

            if (!empty($budgetData) && is_array($budgetData)) {
                $queryKategori = "INSERT INTO tbl_kategori_rab (namaKategori) VALUES (?)";
                // Asumsi kolom tbl_rab: kakId, kategoriId, uraian, rincian, satuan, volume, harga, totalHarga
                // Sesuai ERD, tabel RAB memiliki kolom-kolom tersebut
                $queryItemRAB  = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, satuan, volume, harga, totalHarga) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                foreach ($budgetData as $namaKategori => $items) {
                    if (empty($items)) continue; 

                    // 1. Cek Kategori
                    $kategoriId = 0;
                    $checkKat = mysqli_prepare($this->db, "SELECT kategoriRabId FROM tbl_kategori_rab WHERE namaKategori = ? LIMIT 1");
                    mysqli_stmt_bind_param($checkKat, "s", $namaKategori);
                    mysqli_stmt_execute($checkKat);
                    $resKat = mysqli_stmt_get_result($checkKat);
                    if ($rowKat = mysqli_fetch_assoc($resKat)) {
                        $kategoriId = $rowKat['kategoriRabId'];
                    }
                    mysqli_stmt_close($checkKat); 

                    // Insert Kategori Baru jika belum ada
                    if ($kategoriId == 0) {
                        $stmtKat = mysqli_prepare($this->db, $queryKategori);
                        mysqli_stmt_bind_param($stmtKat, "s", $namaKategori);
                        if (!mysqli_stmt_execute($stmtKat)) throw new Exception("Gagal insert kategori.");
                        $kategoriId = mysqli_insert_id($this->db);
                        mysqli_stmt_close($stmtKat);
                    }

                    // 2. Insert Item RAB
                    $stmtItem = mysqli_prepare($this->db, $queryItemRAB);
                    foreach ($items as $item) {
                        $uraian  = $item['uraian'] ?? '';
                        $rincian = $item['rincian'] ?? '';
                        
                        // Logika Volume Ganda (Vol1 * Vol2) jika diperlukan, atau pakai Vol1 saja
                        // Di ERD kolom volume cuma satu. Kita bisa kalikan vol1 * vol2 atau simpan vol1 saja.
                        // Di sini saya asumsikan vol1 * vol2 = total volume
                        $vol1 = floatval($item['vol1'] ?? 1);
                        $vol2 = floatval($item['vol2'] ?? 1);
                        $volumeFinal = $vol1 * $vol2;
                        
                        // Satuan gabungan (misal: "Org/Kali") atau sat1 saja
                        $satuan  = ($item['sat1'] ?? '') . '/' . ($item['sat2'] ?? '');
                        
                        $harga   = floatval($item['harga'] ?? 0);
                        $total   = $volumeFinal * $harga;

                        // Bind: iisssddd (i=int, s=string, d=double/decimal)
                        mysqli_stmt_bind_param($stmtItem, "iisssddd", $kakId, $kategoriId, $uraian, $rincian, $satuan, $volumeFinal, $harga, $total);
                        if (!mysqli_stmt_execute($stmtItem)) throw new Exception("Gagal insert item RAB.");
                    }
                    mysqli_stmt_close($stmtItem);
                }
            }

            // F. COMMIT
            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log("Gagal Simpan Pengajuan: " . $e->getMessage());
            return false;
        }
    }
}