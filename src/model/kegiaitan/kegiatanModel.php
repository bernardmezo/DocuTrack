<?php
// Model untuk tbl_kegiatan

// ==== KEGIATAN FUNCTIONS ====

if (!function_exists('insertKegiatan')) {
    /**
     * Menyisipkan data kegiatan baru.
     * (Sesuai skema image_90fc45.png)
     */
    function insertKegiatan($pengusul_id, $diinput_oleh_user_id, $nama_kegiatan, $kode_mak) {
        global $conn;

        // Status default saat pertama kali dibuat
        $status_global = 'Menunggu Verifikasi'; 
        $status_default = 'Pending';

        $query = "INSERT INTO tbl_kegiatan (
                    pengusul_id, diinput_oleh_user_id, nama_kegiatan, kode_mak, 
                    status_global, status_verifikator, status_wadir, status_ppk, status_pencairan, 
                    created_at, updated_at
                  ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'iisssssss', 
            $pengusul_id, 
            $diinput_oleh_user_id, 
            $nama_kegiatan, 
            $kode_mak,
            $status_global,
            $status_default, // status_verifikator
            $status_default, // status_wadir
            $status_default, // status_ppk
            $status_default  // status_pencairan
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
        global $conn;

        $query = "SELECT 
                    k.kegiatan_id as id,
                    k.nama_kegiatan,
                    k.status_global as status,
                    u.username as nama_pengusul,
                    p.nama_prodi
                  FROM tbl_kegiatan k
                  LEFT JOIN tbl_users u ON k.pengusul_id = u.user_id
                  LEFT JOIN tbl_program_studi p ON u.prodi_id = p.prodi_id
                  ORDER BY k.created_at DESC";

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
        global $conn;

        $query = "SELECT * FROM tbl_kegiatan WHERE pengusul_id = ? ORDER BY created_at DESC";
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
if (!function_exists('updateKegiatan')) {
    function updateKegiatan($kegiatan_id, $nama_kegiatan, $kode_mak) {
        global $conn;

        $query = "UPDATE tbl_kegiatan SET nama_kegiatan = ?, kode_mak = ?, updated_at = NOW() WHERE kegiatan_id = ?";
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
    function updateStatusGlobal($kegiatan_id, $status_global) {
        global $conn;
        $query = "UPDATE tbl_kegiatan SET status_global = ?, updated_at = NOW() WHERE kegiatan_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'si', $status_global, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Mengupdate kolom status_verifikator.
if (!function_exists('updateStatusVerifikator')) {
    function updateStatusVerifikator($kegiatan_id, $status_verifikator) {
        global $conn;
        $query = "UPDATE tbl_kegiatan SET status_verifikator = ?, updated_at = NOW() WHERE kegiatan_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'si', $status_verifikator, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Mengupdate kolom status_wadir.
if (!function_exists('updateStatusWadir')) {
    function updateStatusWadir($kegiatan_id, $status_wadir) {
        global $conn;
        $query = "UPDATE tbl_kegiatan SET status_wadir = ?, updated_at = NOW() WHERE kegiatan_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'si', $status_wadir, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Mengupdate kolom status_ppk.
if (!function_exists('updateStatusPpk')) {
    function updateStatusPpk($kegiatan_id, $status_ppk) {
        global $conn;
        $query = "UPDATE tbl_kegiatan SET status_ppk = ?, updated_at = NOW() WHERE kegiatan_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'si', $status_ppk, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Mengupdate kolom status_pencairan.
if (!function_exists('updateStatusPencairan')) {
    function updateStatusPencairan($kegiatan_id, $status_pencairan) {
        global $conn;
        $query = "UPDATE tbl_kegiatan SET status_pencairan = ?, updated_at = NOW() WHERE kegiatan_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) { error_log('Prepare failed: ' . mysqli_error($conn)); return false; }
        mysqli_stmt_bind_param($stmt, 'si', $status_pencairan, $kegiatan_id);
        if (mysqli_stmt_execute($stmt)) { mysqli_stmt_close($stmt); return true; } 
        else { error_log('Execute failed: ' . mysqli_stmt_error($stmt)); mysqli_stmt_close($stmt); return false; }
    }
}

// Mengupdate info pencairan dana (jumlah, file, dan set status ke 'Disbursed').
if (!function_exists('updatePencairanDana')) {
    function updatePencairanDana($kegiatan_id, $jumlah_dana_dicairkan, $file_bukti_pencairan) {
        global $conn;
        
        $status_pencairan = 'Disbursed'; // Otomatis set status
        $status_global = 'Dana Dicairkan'; // Otomatis set status global

        $query = "UPDATE tbl_kegiatan 
                  SET jumlah_dana_dicairkan = ?, 
                      file_bukti_pencairan = ?, 
                      status_pencairan = ?,
                      status_global = ?,
                      updated_at = NOW() 
                  WHERE kegiatan_id = ?";
        
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
        global $conn;

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