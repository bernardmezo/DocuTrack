<?php
/**
 * superAdminModel - Super Admin Management Model
 * 
 * @category Model
 * @package  DocuTrack
 * @version  2.0.0 - Refactored to remove constructor trap
 */

class superAdminModel {
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli|null $db Database connection (optional for backward compatibility)
     */
    public function __construct($db = null) {
        if ($db !== null) {
            $this->db = $db;
        } else {
            // Backward compatibility
            require_once __DIR__ . '/conn.php';
            if (isset($conn)) {
                $this->db = $conn;
            } else {
                die("Error: Koneksi database gagal di superAdminModel.");
            }
        }
    }

    // =========================================================
    // 1. STATISTIK DASHBOARD
    // =========================================================
    
    /**
     * Hitung statistik untuk dashboard Super Admin
     */
    public function getDashboardStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 3 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId = 1 THEN 1 ELSE 0 END) as menunggu,
                    SUM(CASE WHEN statusUtamaId = 2 THEN 1 ELSE 0 END) as revisi
                  FROM tbl_kegiatan";
        
        $result = mysqli_query($this->db, $query);
        
        if ($result) {
            $row = mysqli_fetch_assoc($result);
            return [
                'total' => (int)($row['total'] ?? 0),
                'disetujui' => (int)($row['disetujui'] ?? 0),
                'ditolak' => (int)($row['ditolak'] ?? 0),
                'menunggu' => (int)($row['menunggu'] ?? 0),
                'revisi' => (int)($row['revisi'] ?? 0)
            ];
        }
        
        return ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'menunggu' => 0, 'revisi' => 0];
    }

    /**
     * Ambil daftar jurusan dari database
     */
    public function getListJurusan() {
        $query = "SELECT namaJurusan FROM tbl_jurusan ORDER BY namaJurusan ASC";
        
        $result = mysqli_query($this->db, $query);
        $list = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['namaJurusan'];
            }
        }
        
        return $list;
    }

    /**
     * Ambil daftar prodi dari database
     */
    public function getListProdi() {
        $query = "SELECT DISTINCT prodiPenyelenggara as prodi 
                  FROM tbl_kegiatan 
                  WHERE prodiPenyelenggara IS NOT NULL AND prodiPenyelenggara != ''
                  ORDER BY prodiPenyelenggara ASC";
        
        $result = mysqli_query($this->db, $query);
        $list = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $list[] = $row['prodi'];
            }
        }
        
        return $list;
    }

    // =========================================================
    // 2. LIST KEGIATAN (KAK) untuk Dashboard
    // =========================================================
    
    /**
     * Ambil list kegiatan untuk Dashboard Super Admin
     */
    public function getListKegiatan($limit = 20) {
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.jurusanPenyelenggara as jurusan,
                    k.prodiPenyelenggara as prodi,
                    k.createdAt as created_at,
                    DATE(k.createdAt) as tanggal,
                    k.pemilikKegiatan as pengusul,
                    
                    CASE 
                        WHEN k.statusUtamaId = 1 THEN 'menunggu'
                        WHEN k.statusUtamaId = 2 THEN 'revisi'
                        WHEN k.statusUtamaId = 3 THEN 'disetujui'
                        WHEN k.statusUtamaId = 4 THEN 'ditolak'
                        ELSE 'menunggu'
                    END as status
                    
                  FROM tbl_kegiatan k
                  ORDER BY k.createdAt DESC
                  LIMIT ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 3. LIST LPJ untuk Dashboard
    // =========================================================
    
    /**
     * Ambil list LPJ untuk Dashboard
     */
    public function getListLPJ($limit = 10) {
        $query = "SELECT 
                    l.lpjId as id,
                    k.namaKegiatan as nama,
                    k.pemilikKegiatan as nama_mahasiswa,
                    k.nimPelaksana as nim,
                    k.prodiPenyelenggara as prodi,
                    k.jurusanPenyelenggara as jurusan,
                    l.submittedAt as tanggal_pengajuan,
                    l.grandTotalRealisasi as total_realisasi,
                    
                    CASE 
                        WHEN l.approvedAt IS NOT NULL THEN 'Disetujui'
                        WHEN l.submittedAt IS NOT NULL THEN 'Menunggu'
                        ELSE 'Draft'
                    END as status
                    
                  FROM tbl_lpj l
                  JOIN tbl_kegiatan k ON l.kegiatanId = k.kegiatanId
                  ORDER BY l.createdAt DESC
                  LIMIT ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 4. MONITORING - List Proposal dengan Filter
    // =========================================================
    
    /**
     * Ambil semua proposal untuk monitoring dengan filter
     */
    public function getProposalMonitoring($filters = []) {
        $whereClause = "WHERE 1=1";
        $params = [];
        $types = "";
        
        // Filter status
        if (!empty($filters['status']) && $filters['status'] !== 'semua') {
            $statusMap = [
                'approved' => 3,
                'ditolak' => 4,
                'in process' => 1,
                'menunggu' => 1
            ];
            $statusId = $statusMap[strtolower($filters['status'])] ?? null;
            if ($statusId) {
                $whereClause .= " AND k.statusUtamaId = ?";
                $params[] = $statusId;
                $types .= "i";
            }
        }
        
        // Filter jurusan
        if (!empty($filters['jurusan']) && $filters['jurusan'] !== 'semua') {
            $whereClause .= " AND k.jurusanPenyelenggara = ?";
            $params[] = $filters['jurusan'];
            $types .= "s";
        }
        
        // Filter search
        if (!empty($filters['search'])) {
            $whereClause .= " AND (k.namaKegiatan LIKE ? OR k.pemilikKegiatan LIKE ?)";
            $searchTerm = "%" . $filters['search'] . "%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        
        $query = "SELECT 
                    k.kegiatanId as id,
                    k.namaKegiatan as nama,
                    CONCAT(k.pemilikKegiatan, ' (', k.nimPelaksana, ')') as pengusul,
                    k.jurusanPenyelenggara as jurusan,
                    k.posisiId,
                    k.statusUtamaId,
                    
                    -- Status text
                    CASE 
                        WHEN k.statusUtamaId = 3 THEN 'Approved'
                        WHEN k.statusUtamaId = 4 THEN 'Ditolak'
                        WHEN k.statusUtamaId = 2 THEN 'Revisi'
                        ELSE 'In Process'
                    END as status,
                    
                    -- Tahap sekarang berdasarkan posisiId
                    CASE 
                        WHEN k.tanggalPencairan IS NOT NULL THEN 'LPJ'
                        WHEN k.posisiId = 5 THEN 'Dana Cair'
                        WHEN k.posisiId = 4 THEN 'ACC PPK'
                        WHEN k.posisiId = 3 THEN 'ACC WD'
                        WHEN k.posisiId = 2 THEN 'Verifikasi'
                        ELSE 'Pengajuan'
                    END as tahap_sekarang
                    
                  FROM tbl_kegiatan k
                  $whereClause
                  ORDER BY k.createdAt DESC";
        
        if (!empty($params)) {
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
        } else {
            $result = mysqli_query($this->db, $query);
        }
        
        $data = [];
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 5. KELOLA AKUN - List Users
    // =========================================================
    
    /**
     * Ambil semua user untuk kelola akun
     */
    public function getAllUsers() {
        $query = "SELECT 
                    u.userId as id,
                    u.nama,
                    u.email,
                    r.namaRole as role,
                    u.namaJurusan as jurusan,
                    'Aktif' as status,
                    u.createdAt as last_login
                  FROM tbl_user u
                  LEFT JOIN tbl_role r ON u.roleId = r.roleId
                  ORDER BY u.userId ASC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $row['jurusan'] = $row['jurusan'] ?? '-';
                $data[] = $row;
            }
        }
        
        return $data;
    }

    /**
     * Ambil user berdasarkan ID
     */
    public function getUserById($userId) {
        $query = "SELECT u.*, r.namaRole 
                  FROM tbl_user u
                  LEFT JOIN tbl_role r ON u.roleId = r.roleId
                  WHERE u.userId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    }

    /**
     * Create new user
     */
    public function createUser($data) {
        $query = "INSERT INTO tbl_user (nama, email, password, roleId, namaJurusan) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "sssis", 
            $data['nama'], 
            $data['email'], 
            $data['password'],
            $data['roleId'],
            $data['namaJurusan']
        );
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Update user
     */
    public function updateUser($userId, $data) {
        $fields = [];
        $values = [];
        $types = "";
        
        if (!empty($data['nama'])) {
            $fields[] = "nama = ?";
            $values[] = $data['nama'];
            $types .= "s";
        }
        
        if (!empty($data['email'])) {
            $fields[] = "email = ?";
            $values[] = $data['email'];
            $types .= "s";
        }
        
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $values[] = $data['password'];
            $types .= "s";
        }
        
        if (isset($data['roleId'])) {
            $fields[] = "roleId = ?";
            $values[] = $data['roleId'];
            $types .= "i";
        }
        
        if (isset($data['namaJurusan'])) {
            $fields[] = "namaJurusan = ?";
            $values[] = $data['namaJurusan'];
            $types .= "s";
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $values[] = $userId;
        $types .= "i";
        
        $query = "UPDATE tbl_user SET " . implode(", ", $fields) . " WHERE userId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Delete user
     */
    public function deleteUser($userId) {
        $query = "DELETE FROM tbl_user WHERE userId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Ambil semua role
     */
    public function getAllRoles() {
        $query = "SELECT roleId, namaRole FROM tbl_role ORDER BY roleId ASC";
        
        $result = mysqli_query($this->db, $query);
        $data = [];
        
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $data[] = $row;
            }
        }
        
        return $data;
    }

    // =========================================================
    // 6. IKU (Indikator Kinerja Utama)
    // =========================================================
    
    /**
     * Ambil semua IKU dari tabel tbl_iku
     * Note: Jika tabel belum ada, return data default
     */
    public function getAllIKU() {
        // Check if table exists
        $checkTable = mysqli_query($this->db, "SHOW TABLES LIKE 'tbl_iku'");
        
        if (mysqli_num_rows($checkTable) == 0) {
            // Return default IKU data if table doesn't exist
            return [
                ['id' => 1, 'nama' => 'Mendapat Pekerjaan', 'deskripsi' => 'Lulusan berhasil mendapat pekerjaan'],
                ['id' => 2, 'nama' => 'Melanjutkan Studi', 'deskripsi' => 'Lulusan melanjutkan studi ke jenjang lebih tinggi'],
                ['id' => 3, 'nama' => 'Menjadi Wiraswasta', 'deskripsi' => 'Lulusan membuka usaha sendiri'],
                ['id' => 4, 'nama' => 'Menjalankan kegiatan pembelajaran di luar program studi', 'deskripsi' => 'Mahasiswa mengambil SKS di luar prodi'],
                ['id' => 5, 'nama' => 'Dosen berkegiatan di luar kampus', 'deskripsi' => 'Dosen praktisi atau magang industri'],
                ['id' => 6, 'nama' => 'Praktisi mengajar di dalam kampus', 'deskripsi' => 'Kelas kolaborasi dengan praktisi'],
                ['id' => 7, 'nama' => 'Hasil kerja dosen digunakan oleh masyarakat', 'deskripsi' => 'Pengabdian masyarakat atau paten'],
                ['id' => 8, 'nama' => 'Program studi bekerjasama dengan mitra kelas dunia', 'deskripsi' => 'Kerjasama internasional'],
            ];
        }
        
        // NOTE: tbl_iku doesn't exist in current schema
        // Return mock data for now until IKU management is implemented
        return $data;
    }

    /**
     * Create IKU - DISABLED: tbl_iku doesn't exist in schema
     */
    public function createIKU($nama, $deskripsi) {
        // NOTE: tbl_iku table not implemented yet
        error_log('superAdminModel::createIKU - Table tbl_iku does not exist in current schema');
        return false;
    }

    /**
     * Update IKU - DISABLED: tbl_iku doesn't exist in schema
     */
    public function updateIKU($id, $nama, $deskripsi) {
        // NOTE: tbl_iku table not implemented yet
        error_log('superAdminModel::updateIKU - Table tbl_iku does not exist in current schema');
        return false;
    }

    /**
     * Delete IKU - DISABLED: tbl_iku doesn't exist in schema
     */
    public function deleteIKU($id) {
        // NOTE: tbl_iku table not implemented yet
        error_log('superAdminModel::deleteIKU - Table tbl_iku does not exist in current schema');
        return false;
    }
}
