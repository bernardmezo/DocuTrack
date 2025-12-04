<?php
// Model untuk tbl_kegiatan

// ==== KEGIATAN FUNCTIONS ====

if (!function_exists('insertKegiatan')) {
    /**
     * Menyisipkan data kegiatan baru.
     * (Sesuai skema image_90fc45.png)
     */
    function insertKegiatan($pengusul_id, $diinput_oleh_user_id, $nama_kegiatan, $kode_mak) {
        $conn = $this->db; // Refactored: use instance property instead of global
        // Status default: 1=Menunggu, posisi: 1=Admin
        $statusUtamaId = 1; // Menunggu
        $posisiId = 1; // Admin
        $wadirTujuan = 1; // Default Wadir 1

        $query = "INSERT INTO tbl_kegiatan (
                    userId, namaKegiatan, buktiMAK, 
                    statusUtamaId, posisiId, wadirTujuan
                  ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'issiii', 
            $pengusul_id, // userId
            $nama_kegiatan, // namaKegiatan
            $kode_mak, // buktiMAK (code MAK)
            $statusUtamaId,
            $posisiId,
            $wadirTujuan
        );

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            return $newId; // Mengembalikan ID kegiatan yg baru dibuat
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

if (!function_exists('getAllKegiatanForAntrian')) {

    function getAllKegiatanForAntrian() {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "SELECT 
                    k.kegiatan_id as id,
                    k.nama_kegiatan,
                    k.statusUtamaId as status,
                    u.nama as nama_pengusul,
                    k.prodiPenyelenggara as nama_prodi
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_user u ON k.userId = u.userId
                  ORDER BY k.createdAt DESC";

        $result = mysqli_query($conn, $query);
        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $antrian = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $antrian[] = $row;
        }
        mysqli_free_result($result);
        return $antrian;
    }
}

// Mengambil semua data kegiatan berdasarkan ID pengusul.
if (!function_exists('getKegiatanByPengusulId')) {
    function getKegiatanByPengusulId($pengusul_id) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "SELECT * FROM tbl_kegiatan WHERE userId = ? ORDER BY createdAt DESC";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $pengusul_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $kegiatan_list = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $kegiatan_list[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $kegiatan_list;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }
    }
}

// Mengupdate data utama kegiatan (yang bisa diubah oleh pengusul, misal saat revisi)
if (!function_exists('updateNamaKegiatanMak')) {
    function updateNamaKegiatanMak($kegiatan_id, $nama_kegiatan, $kode_mak) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "UPDATE tbl_kegiatan SET namaKegiatan = ?, buktiMAK = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssi', $nama_kegiatan, $kode_mak, $kegiatan_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

// --- FUNGSI UPDATE STATUS (WORKFLOW) ---

// Mengupdate kolom status_global.
if (!function_exists('updateStatusGlobal')) {
    function updateStatusGlobal($kegiatan_id, $status_utama_id) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "UPDATE tbl_kegiatan SET statusUtamaId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'ii', $status_utama_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Update position to Verifikator (posisiId=2)
if (!function_exists('updateStatusVerifikator')) {
    function updateStatusVerifikator($kegiatan_id, $posisi_id = 2) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Update position to Wadir (posisiId=3)
if (!function_exists('updateStatusWadir')) {
    function updateStatusWadir($kegiatan_id, $posisi_id = 3) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Update position to PPK (posisiId=4)
if (!function_exists('updateStatusPpk')) {
    function updateStatusPpk($kegiatan_id, $posisi_id = 4) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Update position to Bendahara (posisiId=5)
if (!function_exists('updateStatusPencairan')) {
    function updateStatusPencairan($kegiatan_id, $posisi_id = 5) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "UPDATE tbl_kegiatan SET posisiId = ? WHERE kegiatanId = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'ii', $posisi_id, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Update pencairan dana info
if (!function_exists('updatePencairanDana')) {
    function updatePencairanDana($kegiatan_id, $jumlah_dana_dicairkan, $metode_pencairan = 'dana_penuh', $catatan = '') {
        $conn = $this->db; // Refactored: use instance property instead of global

        $query = "UPDATE tbl_kegiatan 
                  SET jumlahDicairkan = ?, 
                      metodePencairan = ?, 
                      tanggalPencairan = NOW(),
                      catatanBendahara = ?,
                      statusUtamaId = 3,
                      posisiId = 5
                  WHERE kegiatanId = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: d = double, s = string, s = string, s = string, i = integer
        mysqli_stmt_bind_param($stmt, 'dsssi', $jumlah_dana_dicairkan, $file_bukti_pencairan, $status_pencairan, $status_global, $kegiatan_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

/**
 * Menghapus data kegiatan berdasarkan ID.
 *
 * @param int $kegiatan_id ID kegiatan yang akan dihapus
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('deleteKegiatan')) {
    function deleteKegiatan($kegiatan_id) {
        $conn = $this->db; // Refactored: use instance property instead of global
        $query = "DELETE FROM tbl_kegiatan WHERE kegiatan_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $kegiatan_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}


?>