<?php
include __DIR__ . '/../conn.php'; // Pastikan path ini sesuai dengan struktur folder kamu

// ==== RANCANGAN KEGIATAN FUNCTIONS ====

// Insert rancangan kegiatan
if (!function_exists('insertRancanganKegiatan')) {
    function insertRancanganKegiatan($kegiatan_id, $file_surat_pengantar, $tanggal_mulai, $tanggal_selesai) {
        global $conn;

        $query = "INSERT INTO tbl_rancangan_kegiatan (kegiatan_id, file_surat_pengantar, tanggal_mulai, tanggal_selesai) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: i = integer, s = string, s = string, s = string
        mysqli_stmt_bind_param($stmt, 'isss', $kegiatan_id, $file_surat_pengantar, $tanggal_mulai, $tanggal_selesai);

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

// Mengambil semua data rancangan kegiatan.
if (!function_exists('getAllRancanganKegiatan')) {
    function getAllRancanganKegiatan() {
        global $conn;

        // Pertimbangkan untuk JOIN dengan tabel kegiatan jika perlu nama kegiatannya
        $query = "SELECT * FROM tbl_rancangan_kegiatan ORDER BY rancangan_id DESC";
        $result = mysqli_query($conn, $query);

        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $rancangan_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $rancangan_list[] = $row;
        }

        mysqli_free_result($result);
        return $rancangan_list;
    }
}

// Mengambil satu data rancangan kegiatan berdasarkan ID.
if (!function_exists('getRancanganKegiatanById')) {
    function getRancanganKegiatanById($rancangan_id) {
        global $conn;

        $query = "SELECT * FROM tbl_rancangan_kegiatan WHERE rancangan_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $rancangan_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $rancangan = mysqli_fetch_assoc($result); // Ambil satu baris
            mysqli_stmt_close($stmt);
            return $rancangan; // Mengembalikan data (array) atau null
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
    }
}

// Mengupdate data rancangan kegiatan berdasarkan ID.
if (!function_exists('updateRancanganKegiatan')) {
    function updateRancanganKegiatan($rancangan_id, $kegiatan_id, $file_surat_pengantar, $tanggal_mulai, $tanggal_selesai) {
        global $conn;

        $query = "UPDATE tbl_rancangan_kegiatan SET kegiatan_id = ?, file_surat_pengantar = ?, tanggal_mulai = ?, tanggal_selesai = ? WHERE rancangan_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: i, s, s, s, i
        mysqli_stmt_bind_param($stmt, 'isssi', $kegiatan_id, $file_surat_pengantar, $tanggal_mulai, $tanggal_selesai, $rancangan_id);

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

// Menghapus data rancangan kegiatan berdasarkan ID.
if (!function_exists('deleteRancanganKegiatan')) {
    function deleteRancanganKegiatan($rancangan_id) {
        global $conn;

        $query = "DELETE FROM tbl_rancangan_kegiatan WHERE rancangan_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $rancangan_id);

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

// ngambil rancangan kegiatan + pelaksananya
if (!function_exists('getRancanganWithPelaksanaById')) {
    function getRancanganWithPelaksanaById($rancangan_id) {
        global $conn;

        $query = "SELECT 
                    rk.*, 
                    p.pelaksana_id, 
                    p.nama_pelaksana, 
                    p.nim_pelaksana, 
                    p.nama_penanggung_jawab_kegiatan, 
                    p.nim_penanggung_jawab
                  FROM 
                    tbl_rancangan_kegiatan rk
                  LEFT JOIN 
                    tbl_pelaksana p ON rk.rancangan_id = p.rancangan_id
                  WHERE 
                    rk.rancangan_id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $rancangan_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $data = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $data; // Akan berisi 1 atau lebih baris jika ada pelaksana
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }
    }
}

// Mengambil SEMUA data rancangan kegiatan beserta data pelaksana terkait.
if (!function_exists('getAllRancanganWithPelaksana')) {
    function getAllRancanganWithPelaksana() {
        global $conn;

        $query = "SELECT 
                    rk.*, 
                    p.pelaksana_id, 
                    p.nama_pelaksana, 
                    p.nim_pelaksana, 
                    p.nama_penanggung_jawab_kegiatan, 
                    p.nim_penanggung_jawab
                  FROM 
                    tbl_rancangan_kegiatan rk
                  LEFT JOIN 
                    tbl_pelaksana p ON rk.rancangan_id = p.rancangan_id
                  ORDER BY 
                    rk.rancangan_id DESC, p.pelaksana_id ASC";

        $result = mysqli_query($conn, $query);

        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        mysqli_free_result($result);
        return $data;
    }
}
?>