<!-- QUERY DML -->
<?php
include __DIR__ . '/../conn.php';

// ==== PENGUSUL FUNCTIONS ====

if (!function_exists('insertPengusul')) {
    function insertPengusul($namaPengusul, $nim, $prodiId) {
        global $conn;

        // Siapkan pernyataan SQL dengan prepared statement
        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_pengusul_kak (nama_pengusul, nim, prodi_id) VALUES (?, ?, ?)");
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter
        mysqli_stmt_bind_param($stmt, 'ssi', $namaPengusul, $nim, $prodiId);

        // Eksekusi pernyataan
        if (mysqli_stmt_execute($stmt)) {
            // Ambil id terakhir yang baru dimasukkan
            $lastId = mysqli_insert_id($conn);

            mysqli_stmt_close($stmt);

            // Redirect ke halaman dengan membawa id_pengusul di URL
            header("Location: /docutrack/public/admin/pengajuan-usulan?id_pengusul=" . $lastId);
            exit(); // penting untuk hentikan eksekusi setelah redirect

        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}
?>
