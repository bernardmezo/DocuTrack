<?php
// Model untuk tbl_rab_kategori dan tbl_rab_items
// (Skema tabel ini diasumsikan berdasarkan form)

if (!function_exists('insertRabKategori')) {
    /**
     * Menyisipkan kategori RAB baru.
     * (Asumsi: tbl_rab_kategori punya 'kategori_id', 'kegiatan_id', 'nama_kategori')
     */
    function insertRabKategori($kegiatan_id, $nama_kategori) {
        global $conn;
        
        $query = "INSERT INTO tbl_rab_kategori (kegiatan_id, nama_kategori) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'is', $kegiatan_id, $nama_kategori);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            return $newId; // Mengembalikan ID kategori yg baru dibuat
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

if (!function_exists('insertRabItem')) {
    /**
     * Menyisipkan item RAB baru.
     * (Asumsi: tbl_rab_items punya 'item_id', 'kategori_id', 'uraian', 'volume', 'satuan', 'harga_satuan', 'sub_total')
     */
    function insertRabItem($kategori_id, $uraian, $volume, $satuan, $harga_satuan) {
        global $conn;

        // Hitung sub_total
        $sub_total = floatval($volume) * floatval($harga_satuan);

        $query = "INSERT INTO tbl_rab_items (kategori_id, uraian, volume, satuan, harga_satuan, sub_total) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Tipe data: i, s, i, s, d, d (integer, string, integer, string, double, double)
        // Sesuaikan 'i' untuk volume jika bisa desimal
        mysqli_stmt_bind_param($stmt, 'isssdd', 
            $kategori_id, 
            $uraian, 
            $volume, 
            $satuan, 
            $harga_satuan, 
            $sub_total
        );

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