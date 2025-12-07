<?php

namespace App\Models\Kak;

use Exception;
use mysqli; // Ensure mysqli is available if not globally imported

class KakModel {
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection for database connection
     *
     * @param mysqli|null $db Database connection (optional for backward compatibility)
     */
    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Fallback to global db() helper function from bootstrap.php
            if (function_exists('db')) {
                $this->db = db();
            } else {
                throw new \Exception("Database connection not provided to KakModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    // ==== KAK METHODS ====

    /**
     * Insert main data into tbl_kak.
     *
     * @param int $kegiatanId
     * @param string $gambaranUmum
     * @param string $penerimaManfaat
     * @param string $metodePelaksanaan
     * @param string|null $indikatorKerjaUtamaRenstra
     * @return int|false New KAK ID on success, false on failure.
     */
    public function insertKAK($kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra = null) {
        $stmt = mysqli_prepare($this->db, "
            INSERT INTO tbl_kak (kegiatan_id, gambaran_umum, penerima_manfaat, metode_pelaksanaan, indikator_kerja_utama_renstra)
            VALUES (?, ?, ?, ?, ?)
        ");
        if ($stmt === false) {
            error_log('KakModel::insertKAK - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'issss', $kegiatanId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('KakModel::insertKAK - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Update main data in tbl_kak.
     *
     * @param int $kakId
     * @param string $gambaranUmum
     * @param string $penerimaManfaat
     * @param string $metodePelaksanaan
     * @param string|null $indikatorKerjaUtamaRenstra
     * @return bool True on success, false on failure.
     */
    public function updateKAK($kakId, $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra = null) {
        $stmt = mysqli_prepare($this->db, "
            UPDATE tbl_kak SET 
                gambaran_umum = ?, 
                penerima_manfaat = ?, 
                metode_pelaksanaan = ?, 
                indikator_kerja_utama_renstra = ?
            WHERE kak_id = ?
        ");
        if ($stmt === false) {
            error_log('KakModel::updateKAK - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ssssi', $gambaranUmum, $penerimaManfaat, $metodePelaksanaan, $indikatorKerjaUtamaRenstra, $kakId);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('KakModel::updateKAK - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Get single KAK data with all related indicators & stages.
     *
     * @param int $kakId
     * @return array|null KAK data array on success, null if not found.
     */
    public function getKAKWithRelationsById($kakId) {
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

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('KakModel::getKAKWithRelationsById - Prepare failed: ' . mysqli_error($this->db));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $kakId);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log('KakModel::getKAKWithRelationsById - Execute failed: ' . mysqli_stmt_error($this->db));
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
        
        return $kakData;
    }

    /**
     * Get all KAK data with complete indicators & stages (JOIN).
     *
     * @return array Array of KAK records.
     */
    public function getAllKAKWithRelations() {
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

        $result = mysqli_query($this->db, $query);
        if (!$result) {
            error_log('KakModel::getAllKAKWithRelations - Query failed: ' . mysqli_error($this->db));
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
                $tahapan_exists = array_column($kakList[$kakId]['tahapan_list'], 'tahapan_id');
                if (!in_array($row['tahapan_id'], $tahapan_exists)) {
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

    /**
     * Delete all KAK data and its relations (indicators & stages) using a transaction.
     *
     * @param int $kakId
     * @return bool True on success, false on failure.
     * @throws Exception If a database operation fails.
     */
    public function deleteKAK($kakId) {
        // Start Transaction
        mysqli_begin_transaction($this->db);

        try {
            // 1. Prepare & Execute Delete Stages
            $stmt1 = mysqli_prepare($this->db, "DELETE FROM tbl_kak_tahapan_pelaksanaan WHERE kak_id = ?");
            if ($stmt1 === false) { throw new Exception("KakModel::deleteKAK - Prepare tahapan failed: " . mysqli_error($this->db)); }
            mysqli_stmt_bind_param($stmt1, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt1)) { throw new Exception(mysqli_stmt_error($stmt1)); }
            mysqli_stmt_close($stmt1);

            // 2. Prepare & Execute Delete Indicators
            $stmt2 = mysqli_prepare($this->db, "DELETE FROM tbl_kak_indikator WHERE kak_id = ?");
            if ($stmt2 === false) { throw new Exception("KakModel::deleteKAK - Prepare indikator failed: " . mysqli_error($this->db)); }
            mysqli_stmt_bind_param($stmt2, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt2)) { throw new Exception(mysqli_stmt_error($stmt2)); }
            mysqli_stmt_close($stmt2);

            // 3. Prepare & Execute Delete Main KAK
            $stmt3 = mysqli_prepare($this->db, "DELETE FROM tbl_kak WHERE kak_id = ?");
            if ($stmt3 === false) { throw new Exception("KakModel::deleteKAK - Prepare main KAK failed: " . mysqli_error($this->db)); }
            mysqli_stmt_bind_param($stmt3, 'i', $kakId);
            if (!mysqli_stmt_execute($stmt3)) { throw new Exception(mysqli_stmt_error($stmt3)); }
            mysqli_stmt_close($stmt3);

            // If all successful, commit
            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            // If any fails, rollback
            mysqli_rollback($this->db);
            error_log('KakModel::deleteKAK - Transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    // ==== STAGE METHODS ====

    /**
     * Insert multiple stages into tbl_kak_tahapan_pelaksanaan.
     *
     * @param int $kakId
     * @param array $tahapanList
     * @return bool True on success, false on failure.
     */
    public function insertTahapanPelaksanaan($kakId, $tahapanList) {
        $stmt = mysqli_prepare($this->db, "
            INSERT INTO tbl_kak_tahapan_pelaksanaan (kak_id, nama_tahapan)
            VALUES (?, ?)
        ");
        if ($stmt === false) {
            error_log('KakModel::insertTahapanPelaksanaan - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        foreach ($tahapanList as $tahapan) {
            mysqli_stmt_bind_param($stmt, 'is', $kakId, $tahapan);
            if (!mysqli_stmt_execute($stmt)) {
                error_log('KakModel::insertTahapanPelaksanaan - Execute failed: ' . mysqli_stmt_error($this->db));
            }
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    // ==== KAK INDICATOR METHODS ====

    /**
     * Insert multiple indicators.
     *
     * @param int $kakId
     * @param array $indikatorList
     * @return bool True on success, false on failure.
     */
    public function insertIndikatorKinerja($kakId, $indikatorList) {
        $stmt = mysqli_prepare($this->db, "
            INSERT INTO tbl_kak_indikator (kak_id, bulan, indikator_keberhasilan, target_persen)
            VALUES (?, ?, ?, ?)
        ");
        if ($stmt === false) {
            error_log('KakModel::insertIndikatorKinerja - Prepare failed: ' . mysqli_error($this->db));
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
                error_log('KakModel::insertIndikatorKinerja - Execute failed: ' . mysqli_stmt_error($this->db));
            }
        }

        mysqli_stmt_close($stmt);
        return true;
    }
}
