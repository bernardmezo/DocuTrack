<?php
include __DIR__ . '/../conn.php'; // Pastikan path ini sesuai

// ==== PROGRAM STUDI FUNCTIONS ====

// Menyisipkan data program studi baru.
if (!function_exists('insertProdi')) {
    function insertProdi($nama_prodi) {
        global $conn;

        $query = "INSERT INTO tbl_program_studi (nama_prodi) VALUES (?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: s = string
        mysqli_stmt_bind_param($stmt, 's', $nama_prodi);

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

// Mengambil semua data program studi.
if (!function_exists('getAllProdi')) {
    function getAllProdi() {
        global $conn;

        $query = "SELECT * FROM tbl_program_studi ORDER BY nama_prodi ASC";
        $result = mysqli_query($conn, $query);

        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $prodi_list = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $prodi_list[] = $row;
        }

        mysqli_free_result($result);
        return $prodi_list;
    }
}

// Mengambil satu data program studi berdasarkan ID.
if (!function_exists('getProdiById')) {
    function getProdiById($prodi_id) {
        global $conn;

        $query = "SELECT * FROM tbl_program_studi WHERE prodi_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $prodi_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $prodi = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $prodi;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
    }
}

// Mengupdate data program studi berdasarkan ID.
if (!function_exists('updateProdi')) {
    function updateProdi($prodi_id, $nama_prodi) {
        global $conn;

        $query = "UPDATE tbl_program_studi SET nama_prodi = ? WHERE prodi_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: s = string, i = integer
        mysqli_stmt_bind_param($stmt, 'si', $nama_prodi, $prodi_id);

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

// Menghapus data program studi berdasarkan ID.
if (!function_exists('deleteProdi')) {
    function deleteProdi($prodi_id) {
        global $conn;

        $query = "DELETE FROM tbl_program_studi WHERE prodi_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $prodi_id);

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