<?php
/**
 * rabModel - RAB (Rencana Anggaran Biaya) Model
 * 
 * Model untuk mengelola tbl_rab_kategori dan tbl_rab_items dengan DI pattern.
 * 
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Converted from procedural to OOP
 */

class rabModel {
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli $db Database connection dari Database::getInstance()->getConnection()
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * Menyisipkan kategori RAB baru.
     *
     * @param int $kegiatan_id ID kegiatan
     * @param string $nama_kategori Nama kategori RAB
     * @return int|false ID kategori baru jika berhasil, false jika gagal
     */
    public function insertRabKategori($kegiatan_id, $nama_kategori) {
        $query = "INSERT INTO tbl_rab_kategori (kegiatan_id, nama_kategori) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $query);
        
        if ($stmt === false) {
            error_log('rabModel::insertRabKategori - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'is', $kegiatan_id, $nama_kategori);

        if (mysqli_stmt_execute($stmt)) {
            $newId = mysqli_insert_id($this->db);
            mysqli_stmt_close($stmt);
            return $newId;
        } else {
            error_log('rabModel::insertRabKategori - Execute failed: ' . mysqli_stmt_error($stmt));
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
    public function insertRabItem($kategori_id, $uraian, $volume, $satuan, $harga_satuan) {
        // Hitung sub_total
        $sub_total = floatval($volume) * floatval($harga_satuan);

        $query = "INSERT INTO tbl_rab_items (kategori_id, uraian, volume, satuan, harga_satuan, sub_total) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $query);
        
        if ($stmt === false) {
            error_log('rabModel::insertRabItem - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

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
            error_log('rabModel::insertRabItem - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}