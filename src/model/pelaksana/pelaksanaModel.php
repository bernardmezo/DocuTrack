<?php
include __DIR__ . '/../conn.php'; // Pastikan path ini sesuai

// ==== PELAKSANA FUNCTIONS ====

// Menyisipkan data pelaksana kegiatan baru.

if (!function_exists('insertPelaksana')) {
    function insertPelaksana($rancangan_id, $nama_pelaksana, $nim_pelaksana, $nama_penanggung_jawab_kegiatan, $nim_penanggung_jawab) {
        global $conn;

        $query = "INSERT INTO tbl_pelaksana (rancangan_id, nama_pelaksana, nim_pelaksana, nama_penanggung_jawab_kegiatan, nim_penanggung_jawab) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: i = integer, s = string, s = string, s = string, s = string
        mysqli_stmt_bind_param($stmt, 'issss', $rancangan_id, $nama_pelaksana, $nim_pelaksana, $nama_penanggung_jawab_kegiatan, $nim_penanggung_jawab);

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

// ngambil semua data pelaksana.
if (!function_exists('getAllPelaksana')) {
    function getAllPelaksana() {
        global $conn;

        // Mungkin perlu JOIN dengan tbl_rancangan_kegiatan untuk info lebih
        $query = "SELECT * FROM tbl_pelaksana ORDER BY pelaksana_id DESC";
        $result = mysqli_query($conn, $query);

        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $pelaksana_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $pelaksana_list[] = $row;
        }

        mysqli_free_result($result);
        return $pelaksana_list;
    }
}

// Mengambil satu data pelaksana berdasarkan ID utamanya.
if (!function_exists('getPelaksanaById')) {
    function getPelaksanaById($pelaksana_id) {
        global $conn;

        $query = "SELECT * FROM tbl_pelaksana WHERE pelaksana_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $pelaksana_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $pelaksana = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $pelaksana;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
    }
}

// ngambil data pelaksana berdasarkan ID rancangan kegiatan (Foreign Key).
if (!function_exists('getPelaksanaByRancanganId')) {
    function getPelaksanaByRancanganId($rancangan_id) {
        global $conn;

        $query = "SELECT * FROM tbl_pelaksana WHERE rancangan_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'i', $rancangan_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $pelaksana_list = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $pelaksana_list[] = $row;
            }
            mysqli_stmt_close($stmt);
            return $pelaksana_list;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }
    }
}

// Mengupdate data pelaksana berdasarkan ID-nya.
if (!function_exists('updatePelaksana')) {
    function updatePelaksana($pelaksana_id, $rancangan_id, $nama_pelaksana, $nim_pelaksana, $nama_penanggung_jawab_kegiatan, $nim_penanggung_jawab) {
        global $conn;

        $query = "UPDATE tbl_pelaksana SET rancangan_id = ?, nama_pelaksana = ?, nim_pelaksana = ?, nama_penanggung_jawab_kegiatan = ?, nim_penanggung_jawab = ? WHERE pelaksana_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: i, s, s, s, s, i
        mysqli_stmt_bind_param($stmt, 'issssi', $rancangan_id, $nama_pelaksana, $nim_pelaksana, $nama_penanggung_jawab_kegiatan, $nim_penanggung_jawab, $pelaksana_id);

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

// Menghapus data pelaksana berdasarkan ID.
if (!function_exists('deletePelaksana')) {
    function deletePelaksana($pelaksana_id) {
        global $conn;

        $query = "DELETE FROM tbl_pelaksana WHERE pelaksana_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $pelaksana_id);

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