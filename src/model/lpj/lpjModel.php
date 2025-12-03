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
        
        $grand_total_default = 0.00;

        // Schema: lpjId, kegiatanId, grandTotalRealisasi, submittedAt, approvedAt, tenggatLpj
        $query = "INSERT INTO tbl_lpj (kegiatanId, grandTotalRealisasi, submittedAt, approvedAt) 
                  VALUES (?, ?, NULL, NULL)";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'id', $kegiatan_id, $grand_total_default);

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
 * Mengupdate grandTotalRealisasi di tbl_lpj.
 * Sebaiknya dipanggil setelah ada perubahan (insert/update/delete) pada item.
 *
 * @param int $lpj_id ID LPJ
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('updateLpjGrandTotal')) {
    function updateLpjGrandTotal($lpj_id) {
        global $conn;

        // Schema: tbl_lpj_item (singular), subtotal
        $query = "UPDATE tbl_lpj SET grandTotalRealisasi = 
                    (SELECT COALESCE(SUM(subtotal), 0) FROM tbl_lpj_item WHERE lpjId = ?)
                  WHERE lpjId = ?";
        
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
 * @param string $new_status Status baru (e.g., 'Submitted', 'Approved')
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('updateLpjStatus')) {
    function updateLpjStatus($lpj_id, $new_status) {
        global $conn;

        // NOTE: Schema tbl_lpj TIDAK punya kolom 'status_lpj'. 
        // Status ditentukan oleh submittedAt (Submitted) dan approvedAt (Approved).
        
        $query = "";
        if ($new_status == 'Submitted') {
            $query = "UPDATE tbl_lpj SET submittedAt = NOW() WHERE lpjId = ?";
        } else if ($new_status == 'Approved') {
            $query = "UPDATE tbl_lpj SET approvedAt = NOW() WHERE lpjId = ?";
        } else {
            // Jika status lain (misal Reset/Revisi), mungkin kita perlu null-kan timestamp?
            // Untuk saat ini kita anggap hanya submit/approve
            return false;
        }

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

        // Schema: tbl_lpj_item (singular)
        // Cols: lpjId, jenisBelanja, uraian, rincian, satuan, totalHarga, subtotal, fileBukti
        $query = "INSERT INTO tbl_lpj_item (lpjId, jenisBelanja, uraian, rincian, satuan, totalHarga, subtotal, fileBukti) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        foreach ($itemsList as $item) {
            // Pastikan key array sesuai dengan yang dikirim controller/caller
            // Mapping ke camelCase DB
            mysqli_stmt_bind_param($stmt, 'issssdds',
                $lpj_id,
                $item['jenis_belanja'],
                $item['uraian'],
                $item['rincian'],
                $item['satuan'],
                $item['total_harga'],
                $item['sub_total'],
                $item['file_bukti_nota'] // Caller mungkin masih pakai snake_case
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
 * Menghapus semua item LPJ berdasarkan lpjId.
 *
 * @param int $lpj_id ID LPJ
 * @return bool True jika berhasil, false jika gagal
 */
if (!function_exists('deleteLpjItemsByLpjId')) {
    function deleteLpjItemsByLpjId($lpj_id) {
        global $conn;
        
        $query = "DELETE FROM tbl_lpj_item WHERE lpjId = ?";
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

        // Schema: tbl_lpj l, tbl_lpj_item i
        $query = "SELECT l.*, i.* FROM tbl_lpj l
                  LEFT JOIN tbl_lpj_item i ON l.lpjId = i.lpjId
                  WHERE l.lpjId = ?";
        
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
                    'lpj_id' => $row['lpjId'],
                    'kegiatan_id' => $row['kegiatanId'],
                    // 'status_lpj' => $row['status_lpj'], // REMOVED: Not in schema
                    'grand_total_realisasi' => $row['grandTotalRealisasi'],
                    'submitted_at' => $row['submittedAt'],
                    'approved_at' => $row['approvedAt'],
                    'tenggat_lpj' => $row['tenggatLpj'],
                    'items' => []
                ];
            }

            // Tambahkan item jika ada (lpjItemId not null)
            if (!empty($row['lpjItemId'])) {
                $lpjData['items'][] = [
                    'lpj_item_id' => $row['lpjItemId'],
                    'jenis_belanja' => $row['jenisBelanja'],
                    'uraian' => $row['uraian'],
                    'rincian' => $row['rincian'],
                    'satuan' => $row['satuan'],
                    'total_harga' => $row['totalHarga'],
                    'sub_total' => $row['subtotal'],
                    'file_bukti_nota' => $row['fileBukti']
                ];
            }
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        
        return $lpjData; 
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

        $query_lpj_id = "SELECT lpjId FROM tbl_lpj WHERE kegiatanId = ? LIMIT 1";
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

            if ($lpj && !empty($lpj['lpjId'])) {
                // Jika LPJ ditemukan, panggil fungsi get by ID
                return getLpjWithItemsById($lpj['lpjId']);
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
            $stmt1 = mysqli_prepare($conn, "DELETE FROM tbl_lpj_item WHERE lpjId = ?");
            mysqli_stmt_bind_param($stmt1, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception(mysqli_stmt_error($stmt1));
            }
            mysqli_stmt_close($stmt1);

            // 2. Hapus LPJ induk
            $stmt2 = mysqli_prepare($conn, "DELETE FROM tbl_lpj WHERE lpjId = ?");
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