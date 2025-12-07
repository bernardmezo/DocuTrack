<?php
// File: kakModel.php
// Refactored: Menggunakan global $conn untuk konsistensi

include __DIR__ . '/../conn.php';

// ==== FUNGSI UNTUK KAK ====

/**
 * Insert data utama ke tbl_kak
 * @return int|false ID baru atau false jika gagal
 */

/**
 * Ambil data KAK berdasarkan kegiatan_id
 * @param int $kegiatanId
 * @return array|null Data KAK atau null jika tidak ditemukan
 */



if (!function_exists('getKAKByKegiatanId')) {
    function getKAKByKegiatanId($kegiatanId) {
        global $conn;
        
        $query = "
            SELECT 
                k.kak_id, 
                k.kegiatan_id, 
                k.gambaran_umum, 
                k.penerima_manfaat, 
                k.metode_pelaksanaan, 
                k.indikator_kerja_utama_renstra,
                i.indikator_id,
                i.bulan,
                i.indikator_keberhasilan,
                i.target_persen,
                t.tahapan_id,
                t.nama_tahapan
            FROM tbl_kak k
            LEFT JOIN tbl_kak_indikator i ON k.kak_id = i.kak_id
            LEFT JOIN tbl_kak_tahapan_pelaksanaan t ON k.kak_id = t.kak_id
            WHERE k.kegiatan_id = ?
            ORDER BY i.bulan ASC, t.tahapan_id ASC
        ";

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('getKAKByKegiatanId - Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $kegiatanId);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log('getKAKByKegiatanId - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $kakData = null;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($kakData === null) {
                $kakData = [
                    'kak_id' => $row['kak_id'],
                    'kegiatan_id' => $row['kegiatan_id'],
                    'gambaran_umum' => $row['gambaran_umum'],
                    'penerima_manfaat' => $row['penerima_manfaat'],
                    'metode_pelaksanaan' => $row['metode_pelaksanaan'],
                    'indikator_kerja_utama_renstra' => $row['indikator_kerja_utama_renstra'],
                    'indikator_list' => [],
                    'tahapan_list' => []
                ];
            }

            // Tambahkan indikator (hindari duplikasi)
            if (!empty($row['indikator_id'])) {
                $indikator_exists = array_column($kakData['indikator_list'], 'indikator_id');
                if (!in_array($row['indikator_id'], $indikator_exists)) {
                    $kakData['indikator_list'][] = [
                        'indikator_id' => $row['indikator_id'],
                        'bulan' => $row['bulan'],
                        'nama' => $row['indikator_keberhasilan'],
                        'target' => $row['target_persen']
                    ];
                }
            }

            // Tambahkan tahapan (hindari duplikasi)
            if (!empty($row['tahapan_id'])) {
                $tahapan_exists = array_column($kakData['tahapan_list'], 'tahapan_id');
                if (!in_array($row['tahapan_id'], $tahapan_exists)) {
                    $kakData['tahapan_list'][] = [
                        'tahapan_id' => $row['tahapan_id'],
                        'nama_tahapan' => $row['nama_tahapan']
                    ];
                }
            }
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        
        return $kakData;
    }
}

if (!function_exists('insertKAK')) {
    function insertKAK($kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra = null) {
        global $conn;
        
        $stmt = mysqli_prepare($conn, "
            INSERT INTO tbl_kak (kegiatan_id, gambaran_umum, penerima_manfaat, metode_pelaksanaan, indikator_kerja_utama_renstra)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        if ($stmt === false) {
            error_log('insertKAK - Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'issss', $kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('insertKAK - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

/**
 * Update data utama di tbl_kak
 * @return bool
 */
if (!function_exists('updateKAK')) {
    function updateKAK($kakId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra = null) {
        global $conn;
        
        $stmt = mysqli_prepare($conn, "
            UPDATE tbl_kak SET 
                gambaran_umum = ?, 
                penerima_manfaat = ?, 
                metode_pelaksanaan = ?, 
                indikator_kerja_utama_renstra = ?
            WHERE kak_id = ?
        ");
        
        if ($stmt === false) {
            error_log('updateKAK - Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssssi', $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra, $kakId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('updateKAK - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

/**
 * Ambil satu data KAK + semua indikator & tahapan-nya
 * @return array|null Data KAK atau null jika tidak ditemukan
 */
if (!function_exists('getKAKWithRelationsById')) {
    function getKAKWithRelationsById($kakId) {
        global $conn;
        
        $query = "
            SELECT 
                k.kak_id, 
                k.kegiatan_id, 
                k.gambaran_umum, 
                k.penerima_manfaat, 
                k.metode_pelaksanaan, 
                k.indikator_kerja_utama_renstra,
                i.indikator_id,
                i.bulan,
                i.indikator_keberhasilan,
                i.target_persen,
                t.tahapan_id,
                t.nama_tahapan
            FROM tbl_kak k
            LEFT JOIN tbl_kak_indikator i ON k.kak_id = i.kak_id
            LEFT JOIN tbl_kak_tahapan_pelaksanaan t ON k.kak_id = t.kak_id
            WHERE k.kak_id = ?
            ORDER BY i.bulan ASC, t.tahapan_id ASC
        ";

        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('getKAKWithRelationsById - Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $kakId);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log('getKAKWithRelationsById - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $kakData = null;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($kakData === null) {
                $kakData = [
                    'kak_id' => $row['kak_id'],
                    'kegiatan_id' => $row['kegiatan_id'],
                    'gambaran_umum' => $row['gambaran_umum'],
                    'penerima_manfaat' => $row['penerima_manfaat'],
                    'metode_pelaksanaan' => $row['metode_pelaksanaan'],
                    'indikator_kerja_utama_renstra' => $row['indikator_kerja_utama_renstra'],
                    'indikator_list' => [],
                    'tahapan_list' => []
                ];
            }

            // Tambahkan indikator (hindari duplikasi)
            if (!empty($row['indikator_id'])) {
                $indikator_exists = array_column($kakData['indikator_list'], 'indikator_id');
                if (!in_array($row['indikator_id'], $indikator_exists)) {
                    $kakData['indikator_list'][] = [
                        'indikator_id' => $row['indikator_id'],
                        'bulan' => $row['bulan'],
                        'nama' => $row['indikator_keberhasilan'], // Sesuai dengan view
                        'target' => $row['target_persen']
                    ];
                }
            }

            // Tambahkan tahapan (hindari duplikasi)
            if (!empty($row['tahapan_id'])) {
                $tahapan_exists = array_column($kakData['tahapan_list'], 'tahapan_id');
                if (!in_array($row['tahapan_id'], $tahapan_exists)) {
                    $kakData['tahapan_list'][] = [
                        'tahapan_id' => $row['tahapan_id'],
                        'nama_tahapan' => $row['nama_tahapan']
                    ];
                }
            }
        }

        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        
        return $kakData;
    }
}

/**
 * Ambil semua data KAK dengan indikator & tahapan lengkap
 * @return array Array of KAK data
 */
if (!function_exists('getAllKAKWithRelations')) {
    function getAllKAKWithRelations() {
        global $conn;
        
        $query = "
            SELECT 
                k.kak_id, 
                k.kegiatan_id, 
                k.gambaran_umum, 
                k.penerima_manfaat, 
                k.metode_pelaksanaan, 
                k.indikator_kerja_utama_renstra,
                i.indikator_id,
                i.bulan,
                i.indikator_keberhasilan,
                i.target_persen,
                t.tahapan_id,
                t.nama_tahapan
            FROM tbl_kak k
            LEFT JOIN tbl_kak_indikator i ON k.kak_id = i.kak_id
            LEFT JOIN tbl_kak_tahapan_pelaksanaan t ON k.kak_id = t.kak_id
            ORDER BY k.kak_id ASC, i.bulan ASC, t.tahapan_id ASC
        ";

        $result = mysqli_query($conn, $query);
        if (!$result) {
            error_log('getAllKAKWithRelations - Query failed: ' . mysqli_error($conn));
            return [];
        }

        $kakList = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $kakId = $row['kak_id'];

            if (!isset($kakList[$kakId])) {
                $kakList[$kakId] = [
                    'kak_id' => $row['kak_id'],
                    'kegiatan_id' => $row['kegiatan_id'],
                    'gambaran_umum' => $row['gambaran_umum'],
                    'penerima_manfaat' => $row['penerima_manfaat'],
                    'metode_pelaksanaan' => $row['metode_pelaksanaan'],
                    'indikator_kerja_utama_renstra' => $row['indikator_kerja_utama_renstra'],
                    'indikator_list' => [],
                    'tahapan_list' => []
                ];
            }

            // Tambahkan indikator (hindari duplikasi)
            if (!empty($row['indikator_id'])) {
                $exists = false;
                foreach ($kakList[$kakId]['indikator_list'] as $ind) {
                    if ($ind['indikator_id'] == $row['indikator_id']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $kakList[$kakId]['indikator_list'][] = [
                        'indikator_id' => $row['indikator_id'],
                        'bulan' => $row['bulan'],
                        'nama' => $row['indikator_keberhasilan'],
                        'target' => $row['target_persen']
                    ];
                }
            }

            // Tambahkan tahapan (hindari duplikasi)
            if (!empty($row['tahapan_id'])) {
                $exists = false;
                foreach ($kakList[$kakId]['tahapan_list'] as $tah) {
                    if ($tah['tahapan_id'] == $row['tahapan_id']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $kakList[$kakId]['tahapan_list'][] = [
                        'tahapan_id' => $row['tahapan_id'],
                        'nama_tahapan' => $row['nama_tahapan']
                    ];
                }
            }
        }

        mysqli_free_result($result);
        return array_values($kakList);
    }
}

/**
 * Hapus semua data KAK beserta relasinya (indikator & tahapan)
 * Menggunakan TRANSAKSI untuk menjamin integritas data
 * @return bool
 */
if (!function_exists('deleteKAK')) {
    function deleteKAK($kakId) {
        global $conn;
        
        // Mulai Transaksi
        mysqli_begin_transaction($conn);

        try {
            // 1. Hapus Tahapan
            $stmt1 = mysqli_prepare($conn, "DELETE FROM tbl_kak_tahapan_pelaksanaan WHERE kak_id = ?");
            if ($stmt1 === false) {
                throw new Exception('Prepare tahapan failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt1, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception('Execute tahapan failed: ' . mysqli_stmt_error($stmt1));
            }
            mysqli_stmt_close($stmt1);

            // 2. Hapus Indikator
            $stmt2 = mysqli_prepare($conn, "DELETE FROM tbl_kak_indikator WHERE kak_id = ?");
            if ($stmt2 === false) {
                throw new Exception('Prepare indikator failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt2, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt2)) {
                throw new Exception('Execute indikator failed: ' . mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);

            // 3. Hapus KAK Utama
            $stmt3 = mysqli_prepare($conn, "DELETE FROM tbl_kak WHERE kak_id = ?");
            if ($stmt3 === false) {
                throw new Exception('Prepare KAK failed: ' . mysqli_error($conn));
            }
            mysqli_stmt_bind_param($stmt3, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt3)) {
                throw new Exception('Execute KAK failed: ' . mysqli_stmt_error($stmt3));
            }
            mysqli_stmt_close($stmt3);

            // Jika semua berhasil, commit
            mysqli_commit($conn);
            return true;

        } catch (Exception $e) {
            // Jika ada satu saja yang gagal, rollback
            mysqli_rollback($conn);
            error_log('deleteKAK - Transaction failed: ' . $e->getMessage());
            return false;
        }
    }
}


// ==== FUNGSI UNTUK TAHAPAN PELAKSANAAN ====

/**
 * Insert beberapa tahapan pelaksanaan
 * @param int $kakId
 * @param array $tahapanList Array of string (nama tahapan)
 * @return bool
 */
if (!function_exists('insertTahapanPelaksanaan')) {
    function insertTahapanPelaksanaan($kakId, $tahapanList) {
        global $conn;
        
        if (empty($tahapanList)) {
            return true; // Tidak ada yang perlu diinsert
        }
        
        $stmt = mysqli_prepare($conn, "
            INSERT INTO tbl_kak_tahapan_pelaksanaan (kak_id, nama_tahapan)
            VALUES (?, ?)
        ");
        
        if ($stmt === false) {
            error_log('insertTahapanPelaksanaan - Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        $success = true;
        foreach ($tahapanList as $tahapan) {
            mysqli_stmt_bind_param($stmt, 'is', $kakId, $tahapan);
            if (!mysqli_stmt_execute($stmt)) {
                error_log('insertTahapanPelaksanaan - Execute failed: ' . mysqli_stmt_error($stmt));
                $success = false;
            }
        }

        mysqli_stmt_close($stmt);
        return $success;
    }
}

/**
 * Update tahapan pelaksanaan (hapus semua lalu insert ulang)
 * @param int $kakId
 * @param array $tahapanList Array of string (nama tahapan)
 * @return bool
 */
if (!function_exists('updateTahapanPelaksanaan')) {
    function updateTahapanPelaksanaan($kakId, $tahapanList) {
        global $conn;
        
        // Hapus semua tahapan lama
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM tbl_kak_tahapan_pelaksanaan WHERE kak_id = ?");
        if ($deleteStmt === false) {
            error_log('updateTahapanPelaksanaan - Delete prepare failed: ' . mysqli_error($conn));
            return false;
        }
        
        mysqli_stmt_bind_param($deleteStmt, 'i', $kakId);
        mysqli_stmt_execute($deleteStmt);
        mysqli_stmt_close($deleteStmt);
        
        // Insert tahapan baru
        return insertTahapanPelaksanaan($kakId, $tahapanList);
    }
}


// ==== FUNGSI UNTUK KAK INDIKATOR ====

/**
 * Insert beberapa indikator kinerja
 * @param int $kakId
 * @param array $indikatorList Array of assoc ['bulan' => int, 'indikator_keberhasilan' => string, 'target_persen' => int]
 * @return bool
 */
if (!function_exists('insertIndikatorKinerja')) {
    function insertIndikatorKinerja($kakId, $indikatorList) {
        global $conn;
        
        if (empty($indikatorList)) {
            return true; // Tidak ada yang perlu diinsert
        }
        
        $stmt = mysqli_prepare($conn, "
            INSERT INTO tbl_kak_indikator (kak_id, bulan, indikator_keberhasilan, target_persen)
            VALUES (?, ?, ?, ?)
        ");
        
        if ($stmt === false) {
            error_log('insertIndikatorKinerja - Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        $success = true;
        foreach ($indikatorList as $indikator) {
            $bulan = $indikator['bulan'] ?? 0;
            $indikatorKeberhasilan = $indikator['indikator_keberhasilan'] ?? $indikator['nama'] ?? '';
            $targetPersen = $indikator['target_persen'] ?? $indikator['target'] ?? 0;
            
            mysqli_stmt_bind_param($stmt, 'iisi',
                $kakId,
                $bulan,
                $indikatorKeberhasilan,
                $targetPersen
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                error_log('insertIndikatorKinerja - Execute failed: ' . mysqli_stmt_error($stmt));
                $success = false;
            }
        }

        mysqli_stmt_close($stmt);
        return $success;
    }
}

/**
 * Update indikator kinerja (hapus semua lalu insert ulang)
 * @param int $kakId
 * @param array $indikatorList
 * @return bool
 */
if (!function_exists('updateIndikatorKinerja')) {
    function updateIndikatorKinerja($kakId, $indikatorList) {
        global $conn;
        
        // Hapus semua indikator lama
        $deleteStmt = mysqli_prepare($conn, "DELETE FROM tbl_kak_indikator WHERE kak_id = ?");
        if ($deleteStmt === false) {
            error_log('updateIndikatorKinerja - Delete prepare failed: ' . mysqli_error($conn));
            return false;
        }
        
        mysqli_stmt_bind_param($deleteStmt, 'i', $kakId);
        mysqli_stmt_execute($deleteStmt);
        mysqli_stmt_close($deleteStmt);
        
        // Insert indikator baru
        return insertIndikatorKinerja($kakId, $indikatorList);
    }
}

?>