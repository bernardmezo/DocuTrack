<?php

namespace App\Models\Lpj;

use Exception;
use mysqli; // Ensure mysqli is available if not globally imported

/**
 * LpjModel - LPJ (Laporan Pertanggungjawaban) Management Model
 * 
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Converted from procedural to class-based with DI
 */

class LpjModel {
    private $db;

    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Fallback to global db() helper function from bootstrap.php
            if (function_exists('db')) {
                $this->db = db();
            } else {
                throw new \Exception("Database connection not provided to LpjModel. Ensure bootstrap.php is loaded.");
            }
        }
    }

    /**
     * Membuat record LPJ baru
     */
    public function insertLpj($kegiatan_id) {
        $grand_total_default = 0.00;
        $query = "INSERT INTO tbl_lpj (kegiatanId, grandTotalRealisasi, submittedAt, approvedAt) 
                  VALUES (?, ?, NULL, NULL)";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::insertLpj - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'id', $kegiatan_id, $grand_total_default);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('LpjModel::insertLpj - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengupdate grandTotalRealisasi di tbl_lpj
     */
    public function updateLpjGrandTotal($lpj_id) {
        $query = "UPDATE tbl_lpj SET grandTotalRealisasi = 
                    (SELECT COALESCE(SUM(subtotal), 0) FROM tbl_lpj_item WHERE lpjId = ?)
                  WHERE lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::updateLpjGrandTotal - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'ii', $lpj_id, $lpj_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::updateLpjGrandTotal - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengupdate status LPJ (submittedAt atau approvedAt)
     */
    public function updateLpjStatus($lpj_id, $new_status) {
        $statusId = null;
        $timestampColumn = null;

        switch ($new_status) {
            case 'Submitted':
                $statusId = 1; // Menunggu (LPJ submitted, awaiting review)
                $timestampColumn = 'submittedAt';
                break;
            case 'Approved':
                $statusId = 3; // Disetujui (LPJ approved)
                $timestampColumn = 'approvedAt';
                break;
            case 'Rejected':
                $statusId = 4; // Ditolak (LPJ rejected)
                break;
            case 'Revised':
                $statusId = 2; // Revisi (LPJ sent back for revision)
                break;
            default:
                error_log('LpjModel::updateLpjStatus - Invalid status provided: ' . $new_status);
                return false;
        }

        $query = "UPDATE tbl_lpj SET statusId = ?";
        $bind_params = 'ii';
        $bind_values = [$statusId, $lpj_id];

        if ($timestampColumn) {
            $query .= ", {$timestampColumn} = NOW()";
        }
        $query .= " WHERE lpjId = ?";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::updateLpjStatus - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, $bind_params, ...$bind_values);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::updateLpjStatus - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Memperbarui nama file bukti untuk item LPJ tertentu.
     *
     * @param int    $lpjItemId ID dari item LPJ.
     * @param string $filename  Nama file yang baru.
     * @return bool True jika berhasil, false jika gagal.
     */
    public function updateFileBukti(int $lpjItemId, string $filename): bool
    {
        $query = "UPDATE tbl_lpj_item SET fileBukti = ? WHERE lpjItemId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::updateFileBukti - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "si", $filename, $lpjItemId);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::updateFileBukti - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Menyisipkan BANYAK item LPJ (dari array)
     */
    public function insertLpjItems($lpj_id, $itemsList) {
        $query = "INSERT INTO tbl_lpj_item (lpjId, jenisBelanja, uraian, rincian, satuan, totalHarga, subtotal, fileBukti) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::insertLpjItems - Prepare failed: ' . mysqli_error($this->db));
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
                error_log('LpjModel::insertLpjItems - Execute failed: ' . mysqli_stmt_error($this->db));
                mysqli_stmt_close($stmt);
                return false;
            }
        }

        mysqli_stmt_close($stmt);
        return true;
    }

    /**
     * Menghapus semua item LPJ berdasarkan lpjId
     */
    public function deleteLpjItemsByLpjId($lpj_id) {
        $query = "DELETE FROM tbl_lpj_item WHERE lpjId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('LpjModel::deleteLpjItemsByLpjId - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $lpj_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::deleteLpjItemsByLpjId - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengambil satu data LPJ lengkap dengan semua item-itemnya
     */
    public function getLpjWithItemsById($lpj_id) {
        $query = "SELECT l.*, i.* FROM tbl_lpj l
                  LEFT JOIN tbl_lpj_item i ON l.lpjId = i.lpjId
                  WHERE l.lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::getLpjWithItemsById - Prepare failed: ' . mysqli_error($this->db));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $lpj_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log('LpjModel::getLpjWithItemsById - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $lpjData = null;

        while ($row = mysqli_fetch_assoc($result)) {
            if ($lpjData === null) {
                $lpjData = [
                    'lpj_id' => $row['lpjId'],
                    'kegiatan_id' => $row['kegiatanId'],
                    'grand_total_realisasi' => $row['grandTotalRealisasi'],
                    'submitted_at' => $row['submittedAt'],
                    'approved_at' => $row['approvedAt'],
                    'tenggat_lpj' => $row['tenggatLpj'],
                    'items' => []
                ];
            }

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

    /**
     * Mengambil data LPJ (dengan item) berdasarkan ID Kegiatan
     */
    public function getLpjWithItemsByKegiatanId($kegiatan_id) {
        $query_lpj_id = "SELECT lpjId FROM tbl_lpj WHERE kegiatanId = ? LIMIT 1";
        $stmt_find = mysqli_prepare($this->db, $query_lpj_id);
        
        if ($stmt_find === false) {
             error_log('LpjModel::getLpjWithItemsByKegiatanId - Prepare failed: ' . mysqli_error($this->db));
             return null;
        }
        
        mysqli_stmt_bind_param($stmt_find, 'i', $kegiatan_id);
        
        if (mysqli_stmt_execute($stmt_find)) {
            $result = mysqli_stmt_get_result($stmt_find);
            $lpj = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt_find);

            if ($lpj && !empty($lpj['lpjId'])) {
                return $this->getLpjWithItemsById($lpj['lpjId']);
            } else {
                return null;
            }
        } else {
            error_log('LpjModel::getLpjWithItemsByKegiatanId - Execute failed: ' . mysqli_stmt_error($stmt_find));
            mysqli_stmt_close($stmt_find);
            return null;
        }
    }

    /**
     * Menghapus LPJ dan SEMUA item-itemnya secara aman (Transactional Delete)
     */
    public function deleteLpjWithItems($lpj_id) {
        mysqli_begin_transaction($this->db);

        try {
            // 1. Hapus semua item
            $stmt1 = mysqli_prepare($this->db, "DELETE FROM tbl_lpj_item WHERE lpjId = ?");
            mysqli_stmt_bind_param($stmt1, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt1)) {
                throw new Exception(mysqli_stmt_error($stmt1));
            }
            mysqli_stmt_close($stmt1);

            // 2. Hapus LPJ induk
            $stmt2 = mysqli_prepare($this->db, "DELETE FROM tbl_lpj WHERE lpjId = ?");
            mysqli_stmt_bind_param($stmt2, 'i', $lpj_id);
            if (!mysqli_stmt_execute($stmt2)) {
                throw new Exception(mysqli_stmt_error($stmt2));
            }
            mysqli_stmt_close($stmt2);

            mysqli_commit($this->db);
            return true;

        } catch (Exception $e) {
            mysqli_rollback($this->db);
            error_log('LpjModel::deleteLpjWithItems - Transaction failed: ' . $e->getMessage());
            return false;
        }
    }

    public function tolakLpj(int $lpjId, string $komentar): bool {
        $query = "UPDATE tbl_lpj SET statusId = 4, komentarPenolakan = ?, submittedAt = NOW() WHERE lpjId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::tolakLpj - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'si', $komentar, $lpjId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::tolakLpj - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function submitRevisiLpj(int $lpjId, string $komentarRevisi): bool {
        // Asumsi struktur tabel untuk komentar revisi dan cara menyimpannya
        // Ini adalah contoh, perlu disesuaikan dengan struktur DB yang sebenarnya
        $query = "UPDATE tbl_lpj SET statusId = 2, komentarRevisi = ?, submittedAt = NOW() WHERE lpjId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('LpjModel::submitRevisiLpj - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }
        mysqli_stmt_bind_param($stmt, 'si', $komentarRevisi, $lpjId);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('LpjModel::submitRevisiLpj - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengambil data LPJ untuk tampilan dashboard.
     *
     * @return array Array berisi data LPJ.
     */
    public function getDashboardLPJ(): array
    {
        $query = "SELECT
                    lpj.lpjId,
                    lpj.kegiatanId,
                    keg.namaKegiatan,
                    lpj.grandTotalRealisasi,
                    lpj.submittedAt,
                    lpj.approvedAt,
                    lpj.tenggatLpj,
                    lpj.statusId,
                    su.namaStatusUsulan AS namaStatusLpj
                FROM
                    tbl_lpj AS lpj
                JOIN
                    tbl_kegiatan AS keg ON lpj.kegiatanId = keg.kegiatanId
                JOIN
                    tbl_status_utama AS su ON lpj.statusId = su.statusId
                ORDER BY
                    lpj.submittedAt DESC";

        $result = mysqli_query($this->db, $query);
        if ($result === false) {
            error_log('LpjModel::getDashboardLPJ - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $lpjData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $lpjData[] = $row;
        }

        mysqli_free_result($result);
        return $lpjData;
    }
}

