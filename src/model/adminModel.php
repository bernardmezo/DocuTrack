<?php
// File: src/models/ModelAdmin.php

class adminModel {
    private $db;

    public function __construct() {
        // Hubungkan ke database (sama seperti LoginModel)
        require_once __DIR__ . '/conn.php';
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Koneksi database gagal di ModelAdmin.");
        }
    }

    /**
     * Mengambil semua data pengajuan KAK untuk tabel antrian
     * Menggabungkan tabel kegiatan dan status
     */
    public function getAntrianKAK() {
        // Query ini mengambil data kegiatan dan memformat string 'pengusul' 
        // agar sesuai tampilan: "Nama (NIM), Prodi"
        
        // PENTING: Saya berasumsi ada tabel 'tbl_status' untuk statusUtamaId. 
        // Jika belum ada, sesuaikan LEFT JOIN-nya.
        
        $query = "SELECT
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    CONCAT(k.pemilikKegiatan, ' (', k.nimPelaksana, '), ', k.prodiPenyelenggara) as pengusul,
                    s.namaStatusUsulan as status
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  ORDER BY k.createdAt DESC"; 

        $result = mysqli_query($this->db, $query);

        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        return $data;
    }

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
            $rab_json = $data['rab_json'] ?? '[]'; 
            $budgetData = json_decode($rab_json, true);

            if (!empty($budgetData) && is_array($budgetData)) {
                
                $queryKategori = "INSERT INTO tbl_kategori_rab (namaKategori) VALUES (?)";
                $queryItemRAB  = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, satuan, volume, harga, totalHarga) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
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
                        $satuan  = $item['satuan'] ?? '';
                        $volume  = floatval($item['volume'] ?? 0);
                        $harga   = floatval($item['harga'] ?? 0);
                        $total   = $volume * $harga;

                        mysqli_stmt_bind_param($stmtItem, "iisssddd", $kakId, $kategoriId, $uraian, $rincian, $satuan, $volume, $harga, $total);
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
}