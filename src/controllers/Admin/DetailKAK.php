<?php
/**
 * AdminDetailKAKController - FINAL FIXED VERSION
 * 
 * @category Controller
 * @package  DocuTrack
 * @version  2.2.0 - Column Name Fixed
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../model/adminModel.php';

class AdminDetailKAKController extends Controller {
    
    /**
     * Menampilkan halaman detail KAK
     */
    public function show($id, $data_dari_router = []) {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /docutrack/public/login');
            exit;
        }
        
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';
        
        $model = new adminModel($this->db);
        $dataDB = $model->getDetailKegiatan($id);
        
        if (!$dataDB) {
            $_SESSION['error_message'] = "Kegiatan dengan ID $id tidak ditemukan.";
            header("Location: $back_url");
            exit;
        }
        
        $kakId = $dataDB['kakId'];
        
        $indikator  = $model->getIndikatorByKAK($kakId);
        $tahapan    = $model->getTahapanByKAK($kakId);
        $rab        = $model->getRABByKAK($kakId);
        $komentar   = $model->getKomentarTerbaru($id);
        $komentarPenolakan = $model->getKomentarPenolakan($id);
        
        $tahapan_string = "";
        if (!empty($tahapan) && is_array($tahapan)) {
            foreach ($tahapan as $index => $tahap) {
                $tahapan_string .= ($index + 1) . ". " . $tahap . "\n";
            }
        }
        
        $iku_array = [];
        if (!empty($dataDB['iku'])) {
            $iku_array = array_map('trim', explode(',', $dataDB['iku']));
        }
        
        $kegiatan_data = [
            'id' => $dataDB['kegiatanId'],
            'nama_pengusul' => $dataDB['nama_pengusul'] ?? '-',
            'nim_pengusul' => $dataDB['nim_pelaksana'] ?? '-',
            'nama_pelaksana' => $dataDB['nama_pelaksana'] ?? '-',
            'nama_penanggung_jawab' => $dataDB['nama_pj'] ?? '-',
            'nip_penanggung_jawab' => $dataDB['nim_pj'] ?? '-',
            'jurusan' => $dataDB['jurusanPenyelenggara'] ?? '-',
            'prodi' => $dataDB['prodiPenyelenggara'] ?? '-',
            'nama_kegiatan' => $dataDB['namaKegiatan'] ?? 'Tidak ada judul',
            'gambaran_umum' => $dataDB['gambaranUmum'] ?? '-',
            'penerima_manfaat' => $dataDB['penerimaMaanfaat'] ?? '-',
            'metode_pelaksanaan' => $dataDB['metodePelaksanaan'] ?? '-',
            'tahapan_kegiatan' => $tahapan_string,
            'surat_pengantar' => $dataDB['file_surat_pengantar'] ?? null,
            'tanggal_mulai' => $dataDB['tanggal_mulai'] ?? null,
            'tanggal_selesai' => $dataDB['tanggal_selesai'] ?? null
        ];
        
        $surat_pengantar_url = null;
        if (!empty($dataDB['file_surat_pengantar'])) {
            $filename = basename($dataDB['file_surat_pengantar']);
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $filename;
        }
        
        $status_display = ucfirst($dataDB['status_text'] ?? 'Menunggu');
        
        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($dataDB['namaKegiatan']),
            'status' => $status_display,
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            'kegiatan_data' => $kegiatan_data,
            'iku_data' => $iku_array,
            'indikator_data' => $indikator,
            'rab_data' => $rab,
            'kode_mak' => $dataDB['buktiMAK'] ?? '-',
            'komentar_revisi' => $komentar,
            'komentar_penolakan' => $komentarPenolakan,
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);
        
        $this->view('pages/admin/detail_kak', $data, 'app');
    }
    
    /**
     * Helper: Deteksi nama kolom timestamp di tbl_progress_history
     */
    private function getProgressHistoryTimestampColumn() {
        $possibleColumns = ['createdAt', 'created_at', 'timestamp', 'tanggal', 'date_created'];
        
        // Query untuk mendapatkan struktur tabel
        $query = "SHOW COLUMNS FROM tbl_progress_history";
        $result = mysqli_query($this->db, $query);
        
        if ($result) {
            $columns = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $columns[] = strtolower($row['Field']);
            }
            
            // Cek kolom mana yang ada
            foreach ($possibleColumns as $col) {
                if (in_array(strtolower($col), $columns)) {
                    error_log("✓ Found timestamp column: $col");
                    return $col;
                }
            }
        }
        
        error_log("✗ No timestamp column found, using NULL");
        return null;
    }
    
    /**
     * Menyimpan hasil revisi KAK
     * ✅ FIXED: Column name detection
     */
    public function simpanRevisi($id) {
        error_log("=== simpanRevisi() CALLED with ID: $id ===");
        error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
        error_log("SESSION user_id: " . ($_SESSION['user_id'] ?? 'NOT SET'));
        
        if (!isset($_SESSION['user_id'])) {
            error_log("ERROR: No session user_id");
            $_SESSION['error_message'] = "Anda harus login terlebih dahulu.";
            header('Location: /docutrack/public/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("ERROR: Not POST method");
            $_SESSION['error_message'] = "Method tidak diizinkan.";
            header("Location: /docutrack/public/admin/detail-kak/show/$id?ref=dashboard");
            exit;
        }
        
        error_log("POST keys: " . implode(', ', array_keys($_POST)));
        
        $adminModel = new adminModel($this->db);
        
        try {
            if (!mysqli_begin_transaction($this->db)) {
                throw new Exception("Gagal memulai transaksi: " . mysqli_error($this->db));
            }
            
            $dataDB = $adminModel->getDetailKegiatan($id);
            if (!$dataDB) {
                throw new Exception("Kegiatan dengan ID $id tidak ditemukan.");
            }
            
            $kakId = $dataDB['kakId'];
            if (empty($kakId)) {
                throw new Exception("KAK ID tidak ditemukan untuk kegiatan ini.");
            }
            
            error_log("KAK ID: $kakId");
            
            // ========================================
            // 1. UPDATE TBL_KEGIATAN
            // ========================================
            $nama_kegiatan = $_POST['nama_kegiatan'] ?? $dataDB['namaKegiatan'];
            $nama_pengusul = $_POST['nama_pengusul'] ?? $dataDB['nama_pelaksana'];
            $nim_pengusul = $_POST['nim_pengusul'] ?? $dataDB['nim_pelaksana'];
            $nama_pj = $_POST['nama_penanggung_jawab'] ?? $dataDB['nama_pj'];
            $nip_pj = $_POST['nip_penanggung_jawab'] ?? $dataDB['nim_pj'];
            
            $queryKegiatan = "UPDATE tbl_kegiatan SET 
                                namaKegiatan = ?,
                                pemilikKegiatan = ?,
                                nimPelaksana = ?,
                                namaPJ = ?,
                                nip = ?
                              WHERE kegiatanId = ?";
            
            $stmtKegiatan = mysqli_prepare($this->db, $queryKegiatan);
            if (!$stmtKegiatan) {
                throw new Exception("Prepare kegiatan failed: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($stmtKegiatan, "sssssi", 
                $nama_kegiatan,
                $nama_pengusul,
                $nim_pengusul,
                $nama_pj,
                $nip_pj,
                $id
            );
            
            if (!mysqli_stmt_execute($stmtKegiatan)) {
                throw new Exception("Update kegiatan failed: " . mysqli_stmt_error($stmtKegiatan));
            }
            mysqli_stmt_close($stmtKegiatan);
            error_log("✓ Update tbl_kegiatan SUCCESS");
            
            // ========================================
            // 2. UPDATE TBL_KAK
            // ========================================
            $gambaran_umum = $_POST['gambaran_umum'] ?? $dataDB['gambaranUmum'];
            $penerima_manfaat = $_POST['penerima_manfaat'] ?? $dataDB['penerimaMaanfaat'];
            $metode_pelaksanaan = $_POST['metode_pelaksanaan'] ?? $dataDB['metodePelaksanaan'];
            $iku_string = $_POST['indikator_kinerja'] ?? $dataDB['iku'];
            
            $queryKAK = "UPDATE tbl_kak SET 
                            gambaranUmum = ?,
                            penerimaMaanfaat = ?,
                            metodePelaksanaan = ?,
                            iku = ?
                         WHERE kakId = ?";
            
            $stmtKAK = mysqli_prepare($this->db, $queryKAK);
            if (!$stmtKAK) {
                throw new Exception("Prepare KAK failed: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($stmtKAK, "ssssi",
                $gambaran_umum,
                $penerima_manfaat,
                $metode_pelaksanaan,
                $iku_string,
                $kakId
            );
            
            if (!mysqli_stmt_execute($stmtKAK)) {
                throw new Exception("Update KAK failed: " . mysqli_stmt_error($stmtKAK));
            }
            mysqli_stmt_close($stmtKAK);
            error_log("✓ Update tbl_kak SUCCESS");
            
            // ========================================
            // 3. UPDATE INDIKATOR KINERJA
            // ========================================
            if (isset($_POST['indikator']) && is_array($_POST['indikator'])) {
                $queryDeleteIndikator = "DELETE FROM tbl_indikator_kak WHERE kakId = ?";
                $stmtDeleteIndikator = mysqli_prepare($this->db, $queryDeleteIndikator);
                
                if ($stmtDeleteIndikator) {
                    mysqli_stmt_bind_param($stmtDeleteIndikator, "i", $kakId);
                    mysqli_stmt_execute($stmtDeleteIndikator);
                    mysqli_stmt_close($stmtDeleteIndikator);
                }
                
                $queryInsertIndikator = "INSERT INTO tbl_indikator_kak (kakId, bulan, indikatorKeberhasilan, targetPersen) VALUES (?, ?, ?, ?)";
                $stmtInsertIndikator = mysqli_prepare($this->db, $queryInsertIndikator);
                
                if ($stmtInsertIndikator) {
                    $insertCount = 0;
                    foreach ($_POST['indikator'] as $ind) {
                        $bulan = $ind['bulan'] ?? '';
                        $nama = $ind['nama'] ?? '';
                        $target = intval($ind['target'] ?? 0);
                        
                        if (!empty($nama)) {
                            mysqli_stmt_bind_param($stmtInsertIndikator, "issi", $kakId, $bulan, $nama, $target);
                            
                            if (mysqli_stmt_execute($stmtInsertIndikator)) {
                                $insertCount++;
                            }
                        }
                    }
                    mysqli_stmt_close($stmtInsertIndikator);
                    error_log("✓ Insert $insertCount indikator SUCCESS");
                }
            }
            
            // ========================================
            // 4. UPDATE RAB
            // ========================================
            if (isset($_POST['rab']) && is_array($_POST['rab'])) {
                $rabUpdateCount = 0;
                
                foreach ($_POST['rab'] as $kategori => $items) {
                    if (empty($items) || !is_array($items)) {
                        continue;
                    }
                    
                    $queryKategori = "SELECT kategoriRabId FROM tbl_kategori_rab WHERE namaKategori = ? LIMIT 1";
                    $stmtKategori = mysqli_prepare($this->db, $queryKategori);
                    
                    if (!$stmtKategori) {
                        continue;
                    }
                    
                    mysqli_stmt_bind_param($stmtKategori, "s", $kategori);
                    mysqli_stmt_execute($stmtKategori);
                    $resultKategori = mysqli_stmt_get_result($stmtKategori);
                    $rowKategori = mysqli_fetch_assoc($resultKategori);
                    mysqli_stmt_close($stmtKategori);
                    
                    if (!$rowKategori) {
                        continue;
                    }
                    
                    $kategoriId = $rowKategori['kategoriRabId'];
                    
                    $queryDeleteRAB = "DELETE FROM tbl_rab WHERE kakId = ? AND kategoriId = ?";
                    $stmtDeleteRAB = mysqli_prepare($this->db, $queryDeleteRAB);
                    
                    if ($stmtDeleteRAB) {
                        mysqli_stmt_bind_param($stmtDeleteRAB, "ii", $kakId, $kategoriId);
                        mysqli_stmt_execute($stmtDeleteRAB);
                        mysqli_stmt_close($stmtDeleteRAB);
                    }
                    
                    $queryInsertRAB = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, vol1, sat1, vol2, sat2, harga, totalHarga) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmtInsertRAB = mysqli_prepare($this->db, $queryInsertRAB);
                    
                    if (!$stmtInsertRAB) {
                        continue;
                    }
                    
                    foreach ($items as $item) {
                        $uraian = $item['uraian'] ?? '';
                        $rincian = $item['rincian'] ?? '';
                        $vol1 = floatval($item['vol1'] ?? 0);
                        $sat1 = $item['sat1'] ?? '';
                        $vol2 = floatval($item['vol2'] ?? 1);
                        $sat2 = $item['sat2'] ?? '';
                        $harga = floatval($item['harga'] ?? 0);
                        $totalHarga = $vol1 * $vol2 * $harga;
                        
                        mysqli_stmt_bind_param($stmtInsertRAB, "iissdsdsdd", 
                            $kakId, $kategoriId, $uraian, $rincian, 
                            $vol1, $sat1, $vol2, $sat2, $harga, $totalHarga
                        );
                        
                        if (mysqli_stmt_execute($stmtInsertRAB)) {
                            $rabUpdateCount++;
                        }
                    }
                    mysqli_stmt_close($stmtInsertRAB);
                }
                error_log("✓ Update $rabUpdateCount RAB items SUCCESS");
            }
            
            // ========================================
            // 5. UPDATE STATUS (Kembali ke Verifikator)
            // ========================================
            $queryUpdateStatus = "UPDATE tbl_kegiatan SET statusUtamaId = 1, posisiId = 2 WHERE kegiatanId = ?";
            $stmtUpdateStatus = mysqli_prepare($this->db, $queryUpdateStatus);
            
            if (!$stmtUpdateStatus) {
                throw new Exception("Prepare status update failed: " . mysqli_error($this->db));
            }
            
            mysqli_stmt_bind_param($stmtUpdateStatus, "i", $id);
            
            if (!mysqli_stmt_execute($stmtUpdateStatus)) {
                throw new Exception("Update status failed: " . mysqli_stmt_error($stmtUpdateStatus));
            }
            mysqli_stmt_close($stmtUpdateStatus);
            error_log("✓ Update status SUCCESS");
            
            // ========================================
            // 6. LOG PROGRESS HISTORY (dengan deteksi kolom)
            // ========================================
            $timestampColumn = $this->getProgressHistoryTimestampColumn();
            
            if ($timestampColumn) {
                // Ada kolom timestamp
                $queryHistory = "INSERT INTO tbl_progress_history (kegiatanId, statusId, $timestampColumn) VALUES (?, 1, NOW())";
            } else {
                // Tidak ada kolom timestamp (akan gunakan default atau auto timestamp)
                $queryHistory = "INSERT INTO tbl_progress_history (kegiatanId, statusId) VALUES (?, 1)";
            }
            
            $stmtHistory = mysqli_prepare($this->db, $queryHistory);
            
            if ($stmtHistory) {
                mysqli_stmt_bind_param($stmtHistory, "i", $id);
                if (mysqli_stmt_execute($stmtHistory)) {
                    error_log("✓ Insert progress history SUCCESS");
                } else {
                    error_log("WARNING: Insert progress history failed: " . mysqli_stmt_error($stmtHistory));
                }
                mysqli_stmt_close($stmtHistory);
            }
            
            // Commit transaksi
            if (!mysqli_commit($this->db)) {
                throw new Exception("Commit failed: " . mysqli_error($this->db));
            }
            
            error_log("=== SIMPAN REVISI SUCCESS ===");
            
            $_SESSION['success_message'] = "Revisi berhasil disimpan dan dikirim untuk verifikasi ulang.";
            header("Location: /docutrack/public/admin/detail-kak/show/$id?ref=dashboard");
            exit;
            
        } catch (Exception $e) {
            mysqli_rollback($this->db);
            
            error_log("=== SIMPAN REVISI FAILED ===");
            error_log("Error: " . $e->getMessage());
            error_log("File: " . $e->getFile() . " Line: " . $e->getLine());
            
            $_SESSION['error_message'] = "Gagal menyimpan revisi: " . $e->getMessage();
            header("Location: /docutrack/public/admin/detail-kak/show/$id?ref=dashboard");
            exit;
        }
    }
}