<?php

namespace App\Models;

use App\Core\Database;
use mysqli;

class DirekturModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Get statistik umum dashboard
     */
    public function getStatistikUmum(): array
    {
        $query = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN statusUtamaId = 3 THEN 1 ELSE 0 END) as disetujui,
                SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                SUM(CASE WHEN statusUtamaId = 1 THEN 1 ELSE 0 END) as menunggu,
                SUM(CASE WHEN statusUtamaId = 2 THEN 1 ELSE 0 END) as revisi
            FROM tbl_kegiatan
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }

        return [
            'total' => 0,
            'disetujui' => 0,
            'ditolak' => 0,
            'menunggu' => 0,
            'revisi' => 0
        ];
    }

    /**
     * Get data usulan per jurusan dengan filter periode
     */
    public function getUsulanPerJurusan(string $periode = 'all'): array
    {
        $whereClause = $this->getPeriodeWhereClause($periode);

        $query = "
            SELECT 
                j.namaJurusan,
                COUNT(k.kegiatanId) as jumlah_usulan
            FROM tbl_jurusan j
            LEFT JOIN tbl_kegiatan k ON j.namaJurusan = k.jurusanPenyelenggara
            $whereClause
            GROUP BY j.namaJurusan
            ORDER BY jumlah_usulan DESC
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $labels = [];
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['namaJurusan'];
                $data[] = (int)$row['jumlah_usulan'];
            }
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get total dana keluar per jurusan
     */
    public function getTotalDanaPerJurusan(): array
    {
        // Query yang diperbaiki untuk menangani NULL values
        $query = "
            SELECT 
                j.namaJurusan,
                COALESCE(SUM(k.jumlahDicairkan), 0) as total_dana
            FROM tbl_jurusan j
            LEFT JOIN tbl_kegiatan k ON j.namaJurusan = k.jurusanPenyelenggara 
                AND k.statusUtamaId = 3 
                AND k.jumlahDicairkan IS NOT NULL
                AND k.jumlahDicairkan > 0
            GROUP BY j.namaJurusan
            HAVING total_dana > 0
            ORDER BY total_dana DESC
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $labels = [];
        $data = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $labels[] = $row['namaJurusan'];
                $data[] = (int)$row['total_dana'];
            }
        }

        // Jika tidak ada data, return array kosong dengan pesan
        if (empty($labels)) {
            return [
                'labels' => ['Belum ada data'],
                'data' => [0]
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Get list semua jurusan
     */
    public function getListJurusan(): array
    {
        $query = "SELECT namaJurusan FROM tbl_jurusan ORDER BY namaJurusan ASC";
        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $jurusanList = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $jurusanList[] = $row['namaJurusan'];
            }
        }

        return $jurusanList;
    }

    /**
     * Get data kegiatan untuk chart (dengan timestamp)
     */
    public function getDataKegiatanForChart(): array
    {
        $query = "
            SELECT 
                k.kegiatanId,
                k.namaKegiatan,
                k.jurusanPenyelenggara,
                k.createdAt,
                s.namaStatusUsulan as status
            FROM tbl_kegiatan k
            LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
            ORDER BY k.createdAt DESC
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        $kegiatanList = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $kegiatanList[] = $row;
            }
        }

        return $kegiatanList;
    }

    /**
     * Get summary statistik untuk info boxes chart
     */
    public function getSummaryStatistik(string $periode = 'all'): array
    {
        $whereClause = $this->getPeriodeWhereClause($periode);

        $query = "
            SELECT 
                COUNT(DISTINCT k.jurusanPenyelenggara) as total_jurusan,
                COUNT(*) as total_usulan,
                COALESCE(MAX(jumlah_per_jurusan), 0) as max_usulan,
                COALESCE(AVG(jumlah_per_jurusan), 0) as avg_usulan
            FROM (
                SELECT 
                    k.jurusanPenyelenggara,
                    COUNT(*) as jumlah_per_jurusan
                FROM tbl_kegiatan k
                $whereClause
                GROUP BY k.jurusanPenyelenggara
            ) as sub
        ";

        $result = $this->db->query($query);
        
        if (!$result) {
            throw new \Exception("Database error: " . $this->db->error);
        }
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return [
                'total_jurusan' => (int)($row['total_jurusan'] ?? 0),
                'total_usulan' => (int)($row['total_usulan'] ?? 0),
                'max_usulan' => (int)($row['max_usulan'] ?? 0),
                'avg_usulan' => round($row['avg_usulan'] ?? 0, 1)
            ];
        }

        return [
            'total_jurusan' => 0,
            'total_usulan' => 0,
            'max_usulan' => 0,
            'avg_usulan' => 0
        ];
    }

    /**
     * Get daftar pengajuan dengan pagination
     */
    public function getDaftarPengajuan(int $page = 1, int $perPage = 5, ?string $search = null, ?string $jurusan = null): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];
        $types = '';

        if (!empty($search)) {
            $conditions[] = "(k.namaKegiatan LIKE ? OR k.namaPJ LIKE ? OR k.nimPelaksana LIKE ?)";
            $searchParam = "%$search%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'sss';
        }

        if (!empty($jurusan)) {
            $conditions[] = "k.jurusanPenyelenggara = ?";
            $params[] = $jurusan;
            $types .= 's';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k $whereClause";
        
        if (!empty($params)) {
            $countStmt = $this->db->prepare($countQuery);
            if (!$countStmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalItems = (int)$countResult->fetch_assoc()['total'];
            $countStmt->close();
        } else {
            $countResult = $this->db->query($countQuery);
            if (!$countResult) {
                throw new \Exception("Query failed: " . $this->db->error);
            }
            $totalItems = (int)$countResult->fetch_assoc()['total'];
        }

        // Get paginated data with RAB total
        $query = "
            SELECT 
                k.kegiatanId,
                k.namaKegiatan,
                k.namaPJ,
                k.nimPelaksana,
                k.prodiPenyelenggara,
                k.jurusanPenyelenggara,
                COALESCE(SUM(r.totalHarga), 0) as estimasi_dana
            FROM tbl_kegiatan k
            LEFT JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
            LEFT JOIN tbl_rab r ON kak.kakId = r.kakId
            $whereClause
            GROUP BY k.kegiatanId, k.namaKegiatan, k.namaPJ, k.nimPelaksana, k.prodiPenyelenggara, k.jurusanPenyelenggara
            ORDER BY k.createdAt DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->db->error);
        }
        
        // Build bind params
        $bindParams = $params;
        $bindParams[] = $perPage;
        $bindParams[] = $offset;
        $bindTypes = $types . 'ii';
        
        if (!empty($bindParams)) {
            $stmt->bind_param($bindTypes, ...$bindParams);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();

        return [
            'items' => $items,
            'total' => $totalItems,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 0
        ];
    }

    /**
     * Get data monitoring proposal dengan filter dan pagination
     */
    public function getMonitoringProposal(int $page = 1, int $perPage = 10, array $filters = []): array
    {
        $offset = ($page - 1) * $perPage;
        $conditions = [];
        $params = [];
        $types = '';

        // Filter by status
        if (!empty($filters['status']) && $filters['status'] !== 'Semua') {
            if ($filters['status'] === 'Ditolak') {
                $conditions[] = "k.statusUtamaId = 4";
            } elseif ($filters['status'] === 'Approved') {
                $conditions[] = "k.posisiId >= 5 AND k.statusUtamaId != 4";
            } elseif ($filters['status'] === 'Menunggu') {
                $conditions[] = "k.statusUtamaId = 1";
            } elseif ($filters['status'] === 'In Process') {
                $conditions[] = "k.statusUtamaId != 4 AND k.posisiId < 5";
            }
        }

        // Filter by jurusan
        if (!empty($filters['jurusan']) && $filters['jurusan'] !== 'semua') {
            $conditions[] = "k.jurusanPenyelenggara = ?";
            $params[] = $filters['jurusan'];
            $types .= 's';
        }

        // Filter by search
        if (!empty($filters['search'])) {
            $conditions[] = "(k.namaKegiatan LIKE ? OR k.namaPJ LIKE ?)";
            $searchParam = "%{$filters['search']}%";
            $params[] = $searchParam;
            $params[] = $searchParam;
            $types .= 'ss';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM tbl_kegiatan k $whereClause";

        if (!empty($params)) {
            $countStmt = $this->db->prepare($countQuery);
            if (!$countStmt) {
                throw new \Exception("Prepare failed: " . $this->db->error);
            }
            $countStmt->bind_param($types, ...$params);
            $countStmt->execute();
            $countResult = $countStmt->get_result();
            $totalItems = (int)$countResult->fetch_assoc()['total'];
            $countStmt->close();
        } else {
            $countResult = $this->db->query($countQuery);
            if (!$countResult) {
                throw new \Exception("Query failed: " . $this->db->error);
            }
            $totalItems = (int)$countResult->fetch_assoc()['total'];
        }

        // Get paginated data
        $query = "
            SELECT 
                k.kegiatanId,
                k.namaKegiatan as nama,
                k.namaPJ as pengusul,
                k.jurusanPenyelenggara as jurusan,
                k.posisiId,
                k.statusUtamaId,
                CASE 
                    WHEN k.statusUtamaId = 4 THEN 'Ditolak' 
                    WHEN k.posisiId = 1 THEN 'Pengajuan'
                    WHEN k.posisiId = 2 THEN 'Verifikasi'
                    WHEN k.posisiId = 4 THEN 'ACC PPK'
                    WHEN k.posisiId = 3 THEN 'ACC WD'
                    WHEN k.posisiId = 5 THEN 'Dana Cair'
                    WHEN k.posisiId = 6 THEN 'LPJ'
                    ELSE 'Unknown'
                END as tahap_sekarang,
                CASE 
                    WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                    WHEN k.posisiId >= 6 THEN 'Approved'
                    WHEN k.statusUtamaId = 1 THEN 'Menunggu'
                    ELSE 'In Process'
                END as status
            FROM tbl_kegiatan k
            $whereClause
            ORDER BY k.createdAt DESC
            LIMIT ? OFFSET ?
        ";

        $stmt = $this->db->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->db->error);
        }

        // Build bind params
        $bindParams = $params;
        $bindParams[] = $perPage;
        $bindParams[] = $offset;
        $bindTypes = $types . 'ii';

        if (!empty($bindParams)) {
            $stmt->bind_param($bindTypes, ...$bindParams);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row;
            }
        }
        $stmt->close();

        $totalPages = $totalItems > 0 ? (int)ceil($totalItems / $perPage) : 0;
        $showingFrom = $totalItems > 0 ? $offset + 1 : 0;
        $showingTo = min($offset + $perPage, $totalItems);

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'showing_from' => $showingFrom,
                'showing_to' => $showingTo
            ]
        ];
    }

    /**
     * Helper: Generate WHERE clause untuk filter periode
     */
    private function getPeriodeWhereClause(string $periode): string
    {
        switch ($periode) {
            case 'today':
                return "WHERE DATE(k.createdAt) = CURDATE()";
            case 'week':
                return "WHERE k.createdAt >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
            case 'month':
                return "WHERE k.createdAt >= DATE_SUB(CURDATE(), INTERVAL 1 MONTH)";
            case 'year':
                return "WHERE k.createdAt >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
            default:
                return "";
        }
    }
}