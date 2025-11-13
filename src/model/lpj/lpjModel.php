<?php
include __DIR__ . '/../conn.php'; // Pastikan path ini sesuai

// ==== LPJ (LAPORAN PERTANGGUNG JAWABAN) FUNCTIONS ====

/**
 * Membuat record LPJ baru.
 * Status awal otomatis 'Draft', grand_total 0.
 *
 * @param int $kegiatan_id ID kegiatan yang terkait
 * @return int|false ID LPJ yang baru dibuat, atau false jika gagal.
 */
if (!function_exists('insertLpj')) {
    function insertLpj($kegiatan_id) {
        global $conn;
        
        $status_default = 'Draft';
        $grand_total_default = 0.00;

        $query = "INSERT INTO tbl_lpj (kegiatan_id, status_lpj, grand_total_realisasi, submitted_at, approved_at) 
                  VALUES (?, ?, ?, NULL, NULL)";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: i = integer, s = string, d = double
        mysqli_stmt_bind_param($stmt, 'isd', $kegiatan_id, $status_default, $grand_total_default);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

/**
 * Mengupdate grand_total_realisasi di tbl_lpj.
 * Sebaiknya dipanggil setelah ada perubahan (insert/update/delete) pada item.
 *
 * @param int $lpj_id ID LPJ
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('updateLpjGrandTotal')) {
    function updateLpjGrandTotal($lpj_id) {
        global $conn;

        // Query ini menghitung total dari sub_total item dan meng-update tabel induk
        $query = "UPDATE tbl_lpj SET grand_total_realisasi = 
                    (SELECT COALESCE(SUM(sub_total), 0) FROM tbl_lpj_items WHERE lpj_id = ?)
                  WHERE lpj_id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $lpj_id, $lpj_id);

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
 * Mengupdate status LPJ dan timestamp terkait.
 *
 * @param int $lpj_id ID LPJ
 * @param string $new_status Status baru (e.g., 'Submitted', 'Approved', 'Revision')
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('updateLpjStatus')) {
    function updateLpjStatus($lpj_id, $new_status) {
        global $conn;

        // Menyiapkan query dasar
        $query = "UPDATE tbl_lpj SET status_lpj = ?";
        $params = [$new_status];
        $types = 's';

        // Menambahkan timestamp otomatis berdasarkan status baru
        if ($new_status == 'Submitted') {
            $query .= ", submitted_at = NOW()";
        } else if ($new_status == 'Approved') {
            $query .= ", approved_at = NOW()";
        }

        $query .= " WHERE lpj_id = ?";
        $params[] = $lpj_id;
        $types .= 'i';

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, $types, ...$params);

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

// ==== LPJ ITEMS FUNCTIONS ====

/**
 * Menyisipkan BANYAK item LPJ (dari array).
 *
 * @param int $lpj_id ID LPJ induk
 * @param array $itemsList Array berisi data item, 
 * Contoh: [['jenis_belanja' => '...', 'uraian' => '...'], [...]]
 * @return bool True jika semua berhasil, false jika ada yg gagal
 */
if (!function_exists('insertLpjItems')) {
    function insertLpjItems($lpj_id, $itemsList) {
        global $conn;

        $query = "INSERT INTO tbl_lpj_items (lpj_id, jenis_belanja, uraian, rincian, satuan, total_harga, sub_total, file_bukti_nota) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        foreach ($itemsList as $item) {
            mysqli_stmt_bind_param($stmt, 'issssdds',
                $lpj_id,
                $item['jenis_belanja'],
                $item['uraian'],
                $item['rincian'],
                $item['satuan'],
                $item['total_harga'],
                $item['sub_total'],
                $item['file_bukti_nota']
            );

            if (!mysqli_stmt_execute($stmt)) {
                error_log('Execute failed: ' . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return false; // Hentikan jika ada satu saja yg gagal
            }
        }

        mysqli_stmt_close($stmt);
        return true;
    }
}

/**
 * Menghapus semua item LPJ berdasarkan lpj_id.
 * (Fungsi helper untuk alur "Hapus-lalu-Insert" saat update)
 *
 * @param int $lpj_id ID LPJ
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('deleteLpjItemsByLpjId')) {
    function deleteLpjItemsByLpjId($lpj_id) {
        global $conn;
        
        $query = "DELETE FROM tbl_lpj_items WHERE lpj_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $lpj_id);

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

// ==== FUNGSI PENGAMBILAN DATA (GET) GABUNGAN ====

/**
 * Mengambil satu data LPJ lengkap dengan semua item-itemnya.
 *
 * @param int $lpj_id ID LPJ
 * @return array|null Data LPJ (termasuk array 'items') atau null jika tidak ketemu.
 */
if (!function_exists('getLpjWithItemsById')) {
    function getLpjWithItemsById($lpj_id) {
        global $conn;

        $query = "SELECT l.*, i.* FROM tbl_lpj l
                  LEFT JOIN tbl_lpj_items i ON l.lpj_id = i.lpj_id
                  WHERE l.lpj_id = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $lpj_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $lpjData = null;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($lpjData === null) {
                // Ini adalah baris pertama, set data induk LPJ
                $lpjData = [
                    'lpj_id' => $row['lpj_id'],
                    'kegiatan_id' => $row['kegiatan_id'],
                    'status_lpj' => $row['status_lpj'],
                    'grand_total_realisasi' => $row['grand_total_realisasi'],
                    'submitted_at' => $row['submitted_at'],
                    'approved_at' => $row['approved_at'],
                    'items' => [] // Siapkan array untuk item-item
                ];
            }

            // Tambahkan item jika ada (jika tidak, 'items' akan tetap jadi array kosong)
            if (!empty($row['lpj_item_id'])) {
                $lpjData['items'][] = [
                    'lpj_item_id' => $row['lpj_item_id'],
                    'jenis_belanja' => $row['jenis_belanja'],
                    'uraian' => $row['uraian'],
                    'rincian' => $row['rincian'],
                    'satuan' => $row['satuan'],
                    'total_harga' => $row['total_harga'],
                    'sub_total' => $row['sub_total'],
                    'file_bukti_nota' => $row['file_bukti_nota']
                ];
            }
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        
        return $lpjData; // Mengembalikan data LPJ tunggal atau null
    }
}

/**
 * Mengambil data LPJ (dengan item) berdasarkan ID Kegiatan.
 *
 * @param int $kegiatan_id ID Kegiatan
 * @return array|null Sama seperti getLpjWithItemsById, atau null.
 */
if (!function_exists('getLpjWithItemsByKegiatanId')) {
    function getLpjWithItemsByKegiatanId($kegiatan_id) {
        global $conn;

        // Asumsi 1 kegiatan = 1 LPJ. Jika bisa lebih, fungsi ini perlu diubah
        // untuk mengembalikan array LPJ. Saat ini, saya anggap 1:1.
        $query_lpj_id = "SELECT lpj_id FROM tbl_lpj WHERE kegiatan_id = ? LIMIT 1";
        $stmt_find = mysqli_prepare($conn, $query_lpj_id);
        
        if ($stmt_find === false) {
             error_log('Prepare failed: ' . mysqli_error($conn));
             return null;
        }
        
        mysqli_stmt_bind_param($stmt_find, 'i', $kegiatan_id);
        
        if (mysqli_stmt_execute($stmt_find)) {
            $result = mysqli_stmt_get_result($stmt_find);
            $lpj = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt_find);

            if ($lpj && !empty($lpj['lpj_id'])) {
                // Jika LPJ ditemukan, panggil fungsi get by ID
                return getLpjWithItemsById($lpj['lpj_id']);
            } else {
                return null; // Tidak ada LPJ untuk kegiatan ini
            }
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt_find));
            mysqli_stmt_close($stmt_find);
            return null;
        }
    }
}

/**
 * Menghapus LPJ dan SEMUA item-itemnya secara aman (Transactional Delete).
 *
 * @param int $lpj_id ID LPJ yang akan dihapus
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('deleteLpjWithItems')) {
    function deleteLpjWithItems($lpj_id) {
        global $conn;

        // Mulai Transaksi
        mysqli_begin_transaction($conn);

        try {
            // 1. Hapus semua item
            $stmt1 = mysqli_prepare($conn, "DELETE FROM tbl_lpj_items WHERE lpj_id = ?");
            mysqli_stmt_bind_param($stmt1, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception(mysqli_stmt_error($stmt1));
            }
            mysqli_stmt_close($stmt1);

            // 2. Hapus LPJ induk
            $stmt2 = mysqli_prepare($conn, "DELETE FROM tbl_lpj WHERE lpj_id = ?");
            mysqli_stmt_bind_param($stmt2, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt2)) {
                throw new Exception(mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);

            // Jika semua berhasil, commit
            mysqli_commit($conn);
            return true;

        } catch (Exception $e) {
            // Jika ada satu saja yang gagal, rollback
            mysqli_rollback($conn);
            error_log('Transaction failed: ' . $e->getMessage());
            return false;
        }
    }
}

?>