<?php

namespace App\Models\Rab;

use mysqli;

// Ensure mysqli is available if not globally imported

/**
 * RabModel - RAB (Rencana Anggaran Biaya) Model
 *
 * Model untuk mengelola tbl_rab_kategori dan tbl_rab_items dengan DI pattern.
 *
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Converted from procedural to OOP
 */

class RabModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli $db Database connection dari Database::getInstance()->getConnection()
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Menyisipkan kategori RAB baru.
     *
     * @param int $kegiatan_id ID kegiatan
     * @param string $nama_kategori Nama kategori RAB
     * @return int|false ID kategori baru jika berhasil, false jika gagal
     */
    public function insertRabKategori($kegiatan_id, $nama_kategori)
    {
        $query = "INSERT INTO tbl_rab_kategori (kegiatan_id, nama_kategori) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('RabModel::insertRabKategori - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'is', $kegiatan_id, $nama_kategori);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('RabModel::insertRabKategori - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Menyisipkan item RAB baru.
     *
     * @param int $kategori_id ID kategori RAB
     * @param string $uraian Uraian item
     * @param float $volume Volume/jumlah
     * @param string $satuan Satuan (unit)
     * @param float $harga_satuan Harga per satuan
     * @return bool True jika berhasil, false jika gagal
     */
    public function insertRabItem($kategori_id, $uraian, $volume, $satuan, $harga_satuan)
    {
        // Hitung sub_total
        $sub_total = floatval($volume) * floatval($harga_satuan);

        $query = "INSERT INTO tbl_rab_items (kategori_id, uraian, volume, satuan, harga_satuan, sub_total) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('RabModel::insertRabItem - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            'isssdd',
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
            error_log('RabModel::insertRabItem - Execute failed: ' . mysqli_stmt_error($this->db));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Retrieve RAB data by Kegiatan ID (including categories and items).
     *
     * @param int $kegiatanId
     * @return array Structured array of RAB data [CategoryId => [CategoryName, Items => []]]
     */
    public function getRabByKegiatanId($kegiatanId)
    {
        $query = "SELECT 
                    k.kategoriRabId, 
                    k.namaKategori, 
                    i.rabItemId, 
                    i.uraian, 
                    i.volume, 
                    i.satuan, 
                    i.hargaSatuan, 
                    i.subTotal 
                  FROM tbl_rab_kategori k
                  LEFT JOIN tbl_rab_items i ON k.kategoriRabId = i.kategoriId
                  WHERE k.kegiatanId = ?
                  ORDER BY k.kategoriRabId, i.rabItemId";

        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('RabModel::getRabByKegiatanId - Prepare failed: ' . mysqli_error($this->db));
            return [];
        }

        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $rabData = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $catId = $row['kategoriRabId'];
            if (!isset($rabData[$catId])) {
                $rabData[$catId] = [
                    'nama_kategori' => $row['namaKategori'],
                    'items' => []
                ];
            }
            // Only add item if it exists (LEFT JOIN might return nulls for items)
            if (!empty($row['rabItemId'])) {
                $rabData[$catId]['items'][] = [
                    'rabItemId' => $row['rabItemId'],
                    'uraian' => $row['uraian'],
                    'vol1' => $row['volume'], // Mapping to 'vol1' as expected by View/Legacy logic
                    'sat1' => $row['satuan'], // Mapping to 'sat1'
                    // Note: Legacy DB structure might have vol1, vol2, sat1, sat2.
                    // Current Model insert uses 'volume' and 'satuan'.
                    // If the View expects vol1/vol2, we need to clarify the schema.
                    // Assuming for now 'volume' maps to 'vol1' roughly or standardizing.
                    'harga' => $row['hargaSatuan'],
                    'subTotal' => $row['subTotal']
                ];
            }
        }
        mysqli_stmt_close($stmt);
        return $rabData;
    }
}
