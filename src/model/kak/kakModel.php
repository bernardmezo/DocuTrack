<!-- QUERY DML -->
<?php
    include __DIR__ . '/../conn.php';

    // ==== FUNGSI UNTUK KAK ====

    // Insert data utama ke tbl_kak
    if (!function_exists('insertKAK')) {
        function insertKAK($kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra = null) {
        $conn = $this->db; // Refactored: use instance property instead of global
            $stmt = mysqli_prepare($conn, "
                INSERT INTO tbl_kak (kegiatan_id, gambaran_umum, penerima_manfaat, metode_pelaksanaan, indikator_kerja_utama_renstra)
                VALUES (?, ?, ?, ?, ?)
            ");
            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, 'issss', $kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra);

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

    // update data utama di tbl_kak.
    if (!function_exists('updateKAK')) {
        function updateKAK($kakId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra = null) {
        $conn = $this->db; // Refactored: use instance property instead of global
            $stmt = mysqli_prepare($conn, "
                UPDATE tbl_kak SET 
                    gambaran_umum = ?, 
                    penerima_manfaat = ?, 
                    metode_pelaksanaan = ?, 
                    indikator_kerja_utama_renstra = ?
                WHERE kak_id = ?
            ");
            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, 'ssssi', $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra, $kakId);

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

    // Ambil satu data KAK + semua indikator & tahapan-nya.
    if (!function_exists('getKAKWithRelationsById')) {
        function getKAKWithRelationsById($kakId) {
        $conn = $this->db; // Refactored: use instance property instead of global
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
                ORDER BY k.kak_id ASC
            ";

            $stmt = mysqli_prepare($conn, $query);
            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($conn));
                return null;
            }

            mysqli_stmt_bind_param($stmt, 'i', $kakId);
            
            if (!mysqli_stmt_execute($stmt)) {
                error_log('Execute failed: ' . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return null;
            }

            $result = mysqli_stmt_get_result($stmt);
            $kakData = null; // Gunakan null, bukan array kosong

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

                // Gunakan array_key_exists untuk cek duplikasi (lebih aman)
                if (!empty($row['indikator_id'])) {
                    $indikator_exists = array_column($kakData['indikator_list'], 'indikator_id');
                    if (!in_array($row['indikator_id'], $indikator_exists)) {
                        $kakData['indikator_list'][] = [
                            'indikator_id' => $row['indikator_id'],
                            'bulan' => $row['bulan'],
                            'indikator_keberhasilan' => $row['indikator_keberhasilan'],
                            'target_persen' => $row['target_persen']
                        ];
                    }
                }

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
            
            return $kakData; // Mengembalikan satu objek KAK, atau null
        }
    }

    // Ambil semua data KAK dengan indikator & tahapan lengkap (JOIN)
    if (!function_exists('getAllKAKWithRelations')) {
        function getAllKAKWithRelations() {
        $conn = $this->db; // Refactored: use instance property instead of global
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
                ORDER BY k.kak_id ASC
            ";

            $result = mysqli_query($conn, $query);
            if (!$result) {
                error_log('Query failed: ' . mysqli_error($conn));
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

                if (!empty($row['indikator_id'])) {
                    $kakList[$kakId]['indikator_list'][] = [
                        'indikator_id' => $row['indikator_id'],
                        'bulan' => $row['bulan'],
                        'indikator_keberhasilan' => $row['indikator_keberhasilan'],
                        'target_persen' => $row['target_persen']
                    ];
                }

                if (!empty($row['tahapan_id'])) {
                    $kakList[$kakId]['tahapan_list'][] = [
                        'tahapan_id' => $row['tahapan_id'],
                        'nama_tahapan' => $row['nama_tahapan']
                    ];
                }
            }

            mysqli_free_result($result);
            return array_values($kakList);
        }
    }

    // Hapus semua data KAK beserta relasinya (indikator & tahapan) MENGGUNAKAN TRANSAKSI untuk menjamin integritas data.
    if (!function_exists('deleteKAK')) {
        function deleteKAK($kakId) {
        $conn = $this->db; // Refactored: use instance property instead of global
            // Mulai Transaksi
            mysqli_begin_transaction($conn);

            try {
                // 1. Siapkan & Eksekusi Hapus Tahapan
                $stmt1 = mysqli_prepare($conn, "DELETE FROM tbl_kak_tahapan_pelaksanaan WHERE kak_id = ?");
                mysqli_stmt_bind_param($stmt1, 'i', $kakId);
                if (!mysqli_stmt_execute($stmt1)) {
                    throw new Exception(mysqli_stmt_error($stmt1));
                }
                mysqli_stmt_close($stmt1);

                // 2. Siapkan & Eksekusi Hapus Indikator
                $stmt2 = mysqli_prepare($conn, "DELETE FROM tbl_kak_indikator WHERE kak_id = ?");
                mysqli_stmt_bind_param($stmt2, 'i', $kakId);
                if (!mysqli_stmt_execute($stmt2)) {
                    throw new Exception(mysqli_stmt_error($stmt2));
                }
                mysqli_stmt_close($stmt2);

                // 3. Siapkan & Eksekusi Hapus KAK Utama
                $stmt3 = mysqli_prepare($conn, "DELETE FROM tbl_kak WHERE kak_id = ?");
                mysqli_stmt_bind_param($stmt3, 'i', $kakId);
                if (!mysqli_stmt_execute($stmt3)) {
                    throw new Exception(mysqli_stmt_error($stmt3));
                }
                mysqli_stmt_close($stmt3);

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


    // ==== FUNGSI UNTUK TAHAPAN PELAKSANAAN ====

    // Insert beberapa tahapan pelaksanaan (tbl_kak_tahapan_pelaksanaan)
    if (!function_exists('insertTahapanPelaksanaan')) {
        function insertTahapanPelaksanaan($kakId, $tahapanList) {
        $conn = $this->db; // Refactored: use instance property instead of global
            $stmt = mysqli_prepare($conn, "
                INSERT INTO tbl_kak_tahapan_pelaksanaan (kak_id, nama_tahapan)
                VALUES (?, ?)
            ");
            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($conn));
                return false;
            }

            foreach ($tahapanList as $tahapan) {
                mysqli_stmt_bind_param($stmt, 'is', $kakId, $tahapan);
                if (!mysqli_stmt_execute($stmt)) {
                    error_log('Execute failed: ' . mysqli_stmt_error($stmt));
                }
            }

            mysqli_stmt_close($stmt);
            return true;
        }
    }


    // ==== FUNGSI UNTUK KAK INDIKATOR  ====

    // Insert beberapa indikator 
    if (!function_exists('insertIndikatorKinerja')) {
        function insertIndikatorKinerja($kakId, $indikatorList) {
        $conn = $this->db; // Refactored: use instance property instead of global
            $stmt = mysqli_prepare($conn, "
                INSERT INTO tbl_kak_indikator (kak_id, bulan, indikator_keberhasilan, target_persen)
                VALUES (?, ?, ?, ?)
            ");
            if ($stmt === false) {
                error_log('Prepare failed: ' . mysqli_error($conn));
                return false;
            }

            foreach ($indikatorList as $indikator) {
                mysqli_stmt_bind_param($stmt, 'iisi',
                    $kakId,
                    $indikator['bulan'],
                    $indikator['indikator_keberhasilan'],
                    $indikator['target_persen']
                );
                if (!mysqli_stmt_execute($stmt)) {
                    error_log('Execute failed: ' . mysqli_stmt_error($stmt));
                }
            }

            mysqli_stmt_close($stmt);
            return true;
        }
    }

    // Hapus semua data KAK beserta relasinya
    if (!function_exists('deleteKAK')) {
        function deleteKAK($kakId) {
        $conn = $this->db; // Refactored: use instance property instead of global
            mysqli_query($conn, "DELETE FROM tbl_kak_tahapan_pelaksanaan WHERE kak_id = " . intval($kakId));
            mysqli_query($conn, "DELETE FROM tbl_kak_indikator WHERE kak_id = " . intval($kakId));
            mysqli_query($conn, "DELETE FROM tbl_kak WHERE kak_id = " . intval($kakId));
        }
    }
?>
