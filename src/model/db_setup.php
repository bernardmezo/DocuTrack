<?php
/**
 * Database Auto-Setup Class - DocuTrack
 * ======================================
 * Automatically creates database, tables, and seed data
 * 
 * OPTIMIZED VERSION:
 * - tbl_posisi merged into tbl_role
 * - tbl_rancangan_kegiatan removed (duplicate)
 * - tbl_log_actions merged into tbl_activity_logs
 * 
 * Usage:
 *   $setup = new DatabaseSetup('localhost', 'root', '', 'db_docutrack2');
 *   $setup->run();
 * 
 * Date: December 2, 2025
 */

class DatabaseSetup {
    private $host;
    private $user;
    private $pass;
    private $dbName;
    private $conn;
    private $logs = [];
    
    // Default password hash for 'password123'
    const DEFAULT_PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
    
    public function __construct($host, $user, $pass, $dbName) {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbName = $dbName;
    }
    
    /**
     * Run full setup
     */
    public function run($silent = false) {
        try {
            $this->connectServer();
            $this->createDatabase();
            $this->selectDatabase();
            $this->createTables();
            $this->seedData();
            
            if (!$silent) {
                $this->outputLogs();
            }
            
            return true;
        } catch (Exception $e) {
            $this->log("ERROR: " . $e->getMessage(), 'error');
            if (!$silent) {
                $this->outputLogs();
            }
            return false;
        }
    }
    
    private function connectServer() {
        $this->conn = @mysqli_connect($this->host, $this->user, $this->pass);
        
        if (!$this->conn) {
            throw new Exception("Cannot connect to MySQL server: " . mysqli_connect_error());
        }
        
        mysqli_set_charset($this->conn, 'utf8mb4');
        $this->log("Connected to MySQL server", 'info');
    }
    
    private function createDatabase() {
        $sql = "CREATE DATABASE IF NOT EXISTS `{$this->dbName}` 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        
        if (mysqli_query($this->conn, $sql)) {
            $this->log("Database '{$this->dbName}' ready", 'info');
        } else {
            throw new Exception("Failed to create database: " . mysqli_error($this->conn));
        }
    }
    
    private function selectDatabase() {
        if (!mysqli_select_db($this->conn, $this->dbName)) {
            throw new Exception("Failed to select database: " . mysqli_error($this->conn));
        }
        $this->log("Selected database '{$this->dbName}'", 'info');
    }
    
    private function createTables() {
        $tables = $this->getTableDefinitions();
        
        mysqli_query($this->conn, "SET FOREIGN_KEY_CHECKS = 0");
        
        foreach ($tables as $tableName => $sql) {
            if (mysqli_query($this->conn, $sql)) {
                $this->log("Table '$tableName' created/verified", 'info');
            } else {
                $this->log("Failed to create '$tableName': " . mysqli_error($this->conn), 'warning');
            }
        }
        
        mysqli_query($this->conn, "SET FOREIGN_KEY_CHECKS = 1");
        $this->log("All tables created/verified", 'success');
    }
    
    private function seedData() {
        $seeds = $this->getSeedData();
        
        foreach ($seeds as $seedName => $queries) {
            foreach ($queries as $sql) {
                mysqli_query($this->conn, $sql);
            }
            $this->log("Seeded: $seedName", 'info');
        }
        
        $this->log("All seed data inserted", 'success');
    }
    
    /**
     * Get table definitions - OPTIMIZED
     */
    private function getTableDefinitions() {
        return [
            // OPTIMIZED: tbl_role includes urutan & deskripsi (merged from tbl_posisi)
            'tbl_role' => "CREATE TABLE IF NOT EXISTS `tbl_role` (
                `roleId` INT NOT NULL AUTO_INCREMENT,
                `namaRole` VARCHAR(50) NOT NULL,
                `urutan` TINYINT UNSIGNED DEFAULT NULL COMMENT 'Urutan dalam workflow',
                `deskripsi` VARCHAR(200) DEFAULT NULL COMMENT 'Deskripsi peran',
                PRIMARY KEY (`roleId`),
                UNIQUE KEY `namaRole` (`namaRole`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_status_utama' => "CREATE TABLE IF NOT EXISTS `tbl_status_utama` (
                `statusId` INT NOT NULL AUTO_INCREMENT,
                `namaStatusUsulan` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`statusId`),
                UNIQUE KEY `namaStatusUsulan` (`namaStatusUsulan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_wadir' => "CREATE TABLE IF NOT EXISTS `tbl_wadir` (
                `wadirId` INT NOT NULL AUTO_INCREMENT,
                `namaWadir` VARCHAR(20) DEFAULT NULL,
                PRIMARY KEY (`wadirId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_kategori_rab' => "CREATE TABLE IF NOT EXISTS `tbl_kategori_rab` (
                `kategoriRabId` INT NOT NULL AUTO_INCREMENT,
                `namaKategori` VARCHAR(50) DEFAULT NULL,
                PRIMARY KEY (`kategoriRabId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_jurusan' => "CREATE TABLE IF NOT EXISTS `tbl_jurusan` (
                `namaJurusan` VARCHAR(50) NOT NULL,
                PRIMARY KEY (`namaJurusan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_prodi' => "CREATE TABLE IF NOT EXISTS `tbl_prodi` (
                `namaProdi` VARCHAR(50) NOT NULL,
                `namaJurusan` VARCHAR(50) NOT NULL,
                PRIMARY KEY (`namaProdi`),
                KEY `fk_namaJurusan` (`namaJurusan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_user' => "CREATE TABLE IF NOT EXISTS `tbl_user` (
                `userId` INT NOT NULL AUTO_INCREMENT,
                `nama` VARCHAR(100) NOT NULL,
                `email` VARCHAR(100) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `roleId` INT NOT NULL,
                `namaJurusan` VARCHAR(50) DEFAULT NULL,
                PRIMARY KEY (`userId`),
                UNIQUE KEY `email` (`email`),
                KEY `roleId` (`roleId`),
                KEY `namaJurusan` (`namaJurusan`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_kegiatan' => "CREATE TABLE IF NOT EXISTS `tbl_kegiatan` (
                `kegiatanId` INT NOT NULL AUTO_INCREMENT,
                `namaKegiatan` VARCHAR(255) NOT NULL,
                `prodiPenyelenggara` VARCHAR(50) DEFAULT NULL,
                `pemilikKegiatan` VARCHAR(255) DEFAULT NULL,
                `nimPelaksana` VARCHAR(255) DEFAULT NULL,
                `nip` VARCHAR(255) DEFAULT NULL,
                `namaPJ` VARCHAR(255) DEFAULT NULL,
                `danaDiCairkan` DECIMAL(15,2) DEFAULT NULL,
                `buktiMAK` VARCHAR(255) DEFAULT NULL,
                `userId` INT NOT NULL,
                `jurusanPenyelenggara` VARCHAR(50) DEFAULT NULL,
                `statusUtamaId` INT NOT NULL DEFAULT 1,
                `createdAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `uploadAt` TIMESTAMP NULL DEFAULT NULL,
                `wadirTujuan` INT NOT NULL,
                `suratPengantar` VARCHAR(255) DEFAULT NULL,
                `tanggalMulai` DATE DEFAULT NULL,
                `tanggalSelesai` DATE DEFAULT NULL,
                `posisiId` INT NOT NULL DEFAULT 1,
                `tanggalPencairan` DATETIME DEFAULT NULL,
                `jumlahDicairkan` DECIMAL(15,2) DEFAULT NULL,
                `metodePencairan` VARCHAR(50) DEFAULT NULL,
                `catatanBendahara` TEXT DEFAULT NULL,
                `umpanBalikVerifikator` TEXT DEFAULT NULL COMMENT 'Umpan balik/instruksi dari Verifikator untuk Admin',
                PRIMARY KEY (`kegiatanId`),
                KEY `idx_nimPelaksana` (`nimPelaksana`),
                KEY `idx_nip` (`nip`),
                KEY `fk_kegiatan_user` (`userId`),
                KEY `fk_status_kegiatan` (`statusUtamaId`),
                KEY `idx_posisi` (`posisiId`),
                KEY `idx_created_at` (`createdAt`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_kak' => "CREATE TABLE IF NOT EXISTS `tbl_kak` (
                `kakId` INT NOT NULL AUTO_INCREMENT,
                `kegiatanId` INT NOT NULL,
                `iku` VARCHAR(200) DEFAULT NULL,
                `penerimaMaanfaat` TEXT DEFAULT NULL,
                `gambaranUmum` TEXT DEFAULT NULL,
                `metodePelaksanaan` TEXT DEFAULT NULL,
                `tglPembuatan` DATE DEFAULT NULL,
                PRIMARY KEY (`kakId`),
                KEY `fk_kak_kegiatan` (`kegiatanId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_indikator_kak' => "CREATE TABLE IF NOT EXISTS `tbl_indikator_kak` (
                `indikatorId` INT NOT NULL AUTO_INCREMENT,
                `kakId` INT DEFAULT NULL,
                `bulan` TINYINT UNSIGNED DEFAULT NULL,
                `indikatorKeberhasilan` VARCHAR(250) DEFAULT NULL,
                `targetPersen` TINYINT UNSIGNED DEFAULT NULL,
                PRIMARY KEY (`indikatorId`),
                KEY `fk_indikator_kak` (`kakId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_tahapan_pelaksanaan' => "CREATE TABLE IF NOT EXISTS `tbl_tahapan_pelaksanaan` (
                `tahapanId` INT NOT NULL AUTO_INCREMENT,
                `kakId` INT DEFAULT NULL,
                `namaTahapan` VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (`tahapanId`),
                KEY `fk_tahapan_kak` (`kakId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_rab' => "CREATE TABLE IF NOT EXISTS `tbl_rab` (
                `rabItemId` INT NOT NULL AUTO_INCREMENT,
                `kakId` INT NOT NULL,
                `kategoriId` INT NOT NULL,
                `uraian` TEXT DEFAULT NULL,
                `rincian` TEXT DEFAULT NULL,
                `sat1` VARCHAR(50) DEFAULT NULL,
                `sat2` VARCHAR(50) DEFAULT NULL,
                `vol1` DECIMAL(10,2) NOT NULL,
                `vol2` DECIMAL(10,2) NOT NULL,
                `harga` DECIMAL(15,2) NOT NULL,
                `totalHarga` DECIMAL(15,2) DEFAULT NULL,
                `subtotal` DECIMAL(15,2) DEFAULT NULL,
                PRIMARY KEY (`rabItemId`),
                KEY `fk_rab_kak` (`kakId`),
                KEY `fk_rab_kategori` (`kategoriId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_lpj' => "CREATE TABLE IF NOT EXISTS `tbl_lpj` (
                `lpjId` INT NOT NULL AUTO_INCREMENT,
                `kegiatanId` INT NOT NULL,
                `grandTotalRealisasi` DECIMAL(15,2) DEFAULT NULL,
                `submittedAt` TIMESTAMP NULL DEFAULT NULL,
                `approvedAt` TIMESTAMP NULL DEFAULT NULL,
                `tenggatLpj` DATE DEFAULT NULL,
                PRIMARY KEY (`lpjId`),
                KEY `fk_lpj_kegiatan` (`kegiatanId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_lpj_item' => "CREATE TABLE IF NOT EXISTS `tbl_lpj_item` (
                `lpjItemId` INT NOT NULL AUTO_INCREMENT,
                `lpjId` INT NOT NULL,
                `jenisBelanja` VARCHAR(150) DEFAULT NULL,
                `uraian` TEXT DEFAULT NULL,
                `rincian` TEXT DEFAULT NULL,
                `satuan` VARCHAR(50) DEFAULT NULL,
                `totalHarga` DECIMAL(15,2) DEFAULT NULL,
                `subtotal` DECIMAL(15,2) DEFAULT NULL,
                `fileBukti` VARCHAR(255) DEFAULT NULL,
                PRIMARY KEY (`lpjItemId`),
                KEY `fk_item_ke_lpj` (`lpjId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_progress_history' => "CREATE TABLE IF NOT EXISTS `tbl_progress_history` (
                `progressHistoryId` INT NOT NULL AUTO_INCREMENT,
                `kegiatanId` INT NOT NULL,
                `statusId` INT NOT NULL,
                `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `changedByUserId` INT DEFAULT NULL,
                PRIMARY KEY (`progressHistoryId`),
                KEY `fk_history_kegiatan` (`kegiatanId`),
                KEY `fk_history_status` (`statusId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            'tbl_revisi_comment' => "CREATE TABLE IF NOT EXISTS `tbl_revisi_comment` (
                `revisiCommentId` INT NOT NULL AUTO_INCREMENT,
                `progressHistoryId` INT NOT NULL,
                `komentarRevisi` TEXT DEFAULT NULL,
                `targetTabel` VARCHAR(100) DEFAULT NULL,
                `targetKolom` VARCHAR(100) DEFAULT NULL,
                PRIMARY KEY (`revisiCommentId`),
                KEY `fk_comment_to_history` (`progressHistoryId`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            
            // OPTIMIZED: tbl_activity_logs now includes category (merged from tbl_log_actions)
            'tbl_activity_logs' => "CREATE TABLE IF NOT EXISTS `tbl_activity_logs` (
                `logId` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `userId` INT UNSIGNED NOT NULL,
                `action` VARCHAR(50) NOT NULL,
                `category` ENUM('authentication', 'workflow', 'document', 'financial', 'user_management', 'security') NOT NULL DEFAULT 'workflow',
                `entityType` VARCHAR(50) DEFAULT NULL,
                `entityId` INT UNSIGNED DEFAULT NULL,
                `description` TEXT DEFAULT NULL,
                `oldValue` JSON DEFAULT NULL,
                `newValue` JSON DEFAULT NULL,
                `ipAddress` VARCHAR(45) DEFAULT NULL,
                `userAgent` VARCHAR(500) DEFAULT NULL,
                `createdAt` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                KEY `idx_user_action` (`userId`, `action`),
                KEY `idx_category` (`category`),
                KEY `idx_entity` (`entityType`, `entityId`),
                KEY `idx_created_at` (`createdAt`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        ];
    }
    
    /**
     * Get seed data - OPTIMIZED
     */
    private function getSeedData() {
        $pwHash = self::DEFAULT_PASSWORD_HASH;
        
        return [
            // OPTIMIZED: roles include urutan & deskripsi
            'roles' => [
                "INSERT IGNORE INTO `tbl_role` (`roleId`, `namaRole`, `urutan`, `deskripsi`) VALUES 
                    (1, 'Admin', 1, 'Posisi awal - Admin membuat/mengedit pengajuan'),
                    (2, 'Verifikator', 2, 'Verifikasi dokumen dan kelengkapan'),
                    (3, 'Wadir', 4, 'Wakil Direktur - approval tingkat direktur'),
                    (4, 'PPK', 3, 'Pejabat Pembuat Komitmen - approval anggaran'),
                    (5, 'Bendahara', 5, 'Pencairan dana'),
                    (6, 'Super Admin', NULL, 'Administrator sistem')"
            ],
            
            'status' => [
                "INSERT IGNORE INTO `tbl_status_utama` (`statusId`, `namaStatusUsulan`) VALUES 
                    (1, 'Menunggu'), (2, 'Revisi'), (3, 'Disetujui'), (4, 'Ditolak')"
            ],
            
            'wadir' => [
                "INSERT IGNORE INTO `tbl_wadir` (`wadirId`, `namaWadir`) VALUES 
                    (1, 'Wadir 1'), (2, 'Wadir 2'), (3, 'Wadir 3'), (4, 'Wadir 4')"
            ],
            
            'kategori_rab' => [
                "INSERT IGNORE INTO `tbl_kategori_rab` (`kategoriRabId`, `namaKategori`) VALUES 
                    (4, 'Belanja Barang'), (5, 'Belanja Perjalanan'), (6, 'Belanja Jasa')"
            ],
            
            'jurusan' => [
                "INSERT IGNORE INTO `tbl_jurusan` (`namaJurusan`) VALUES 
                    ('Administrasi Niaga'), ('Akuntansi'), ('Pascasarjana'),
                    ('Teknik Elektro'), ('Teknik Grafika dan Penerbitan'),
                    ('Teknik Informatika dan Komputer'), ('Teknik Mesin'), ('Teknik Sipil')"
            ],
            
            'prodi' => [
                "INSERT IGNORE INTO `tbl_prodi` (`namaProdi`, `namaJurusan`) VALUES 
                    ('D3 Administrasi Bisnis', 'Administrasi Niaga'),
                    ('D4 Administrasi Bisnis Terapan', 'Administrasi Niaga'),
                    ('D3 Akuntansi', 'Akuntansi'),
                    ('D4 Akuntansi Keuangan', 'Akuntansi'),
                    ('D4 Manajemen Keuangan', 'Akuntansi'),
                    ('S2 Magister Terapan Teknik Elektro', 'Pascasarjana'),
                    ('D3 Teknik Elektronika Industri', 'Teknik Elektro'),
                    ('D3 Teknik Listrik', 'Teknik Elektro'),
                    ('D4 Teknik Instrumentasi dan Kontrol Industri', 'Teknik Elektro'),
                    ('D3 Teknik Grafika', 'Teknik Grafika dan Penerbitan'),
                    ('D4 Desain Grafis', 'Teknik Grafika dan Penerbitan'),
                    ('D4 Teknik Informatika', 'Teknik Informatika dan Komputer'),
                    ('D4 Teknik Multimedia dan Jaringan', 'Teknik Informatika dan Komputer'),
                    ('D3 Teknik Mesin', 'Teknik Mesin'),
                    ('D3 Alat Berat', 'Teknik Mesin'),
                    ('D4 Teknologi Rekayasa Manufaktur', 'Teknik Mesin'),
                    ('D3 Konstruksi Gedung', 'Teknik Sipil'),
                    ('D3 Konstruksi Sipil', 'Teknik Sipil'),
                    ('D4 Manajemen Konstruksi', 'Teknik Sipil')"
            ],
            
            'users' => [
                "INSERT IGNORE INTO `tbl_user` (`userId`, `nama`, `email`, `password`, `roleId`, `namaJurusan`) VALUES 
                    (1, 'Admin TI', 'adminti@gmail.com', '{$pwHash}', 1, 'Teknik Informatika dan Komputer'),
                    (2, 'Admin Teknik Elektro', 'adminelektro@gmail.com', '{$pwHash}', 1, 'Teknik Elektro'),
                    (3, 'Admin Teknik Sipil', 'adminsipil@gmail.com', '{$pwHash}', 1, 'Teknik Sipil'),
                    (4, 'Admin Teknik Mesin', 'adminmesin@gmail.com', '{$pwHash}', 1, 'Teknik Mesin'),
                    (5, 'Admin Grafika', 'admintgp@gmail.com', '{$pwHash}', 1, 'Teknik Grafika dan Penerbitan'),
                    (6, 'Admin Akuntansi', 'adminakt@gmail.com', '{$pwHash}', 1, 'Akuntansi'),
                    (7, 'Admin Adm Niaga', 'adminan@gmail.com', '{$pwHash}', 1, 'Administrasi Niaga'),
                    (8, 'Admin Pascasarjana', 'adminpasca@gmail.com', '{$pwHash}', 1, 'Pascasarjana'),
                    (9, 'Verifikator', 'verifikator@gmail.com', '{$pwHash}', 2, NULL),
                    (10, 'Wakil Direktur', 'wadir@gmail.com', '{$pwHash}', 3, NULL),
                    (11, 'PPK', 'ppk@gmail.com', '{$pwHash}', 4, NULL),
                    (12, 'Bendahara', 'bendahara@gmail.com', '{$pwHash}', 5, NULL),
                    (13, 'Super Admin', 'superadmin@gmail.com', '{$pwHash}', 6, NULL)"
            ]
        ];
    }
    
    /**
     * Check if setup is needed
     */
    public function needsSetup() {
        try {
            $this->connectServer();
            
            $result = mysqli_query($this->conn, "SHOW DATABASES LIKE '{$this->dbName}'");
            if (mysqli_num_rows($result) == 0) {
                return true;
            }
            
            mysqli_select_db($this->conn, $this->dbName);
            $result = mysqli_query($this->conn, "SHOW TABLES LIKE 'tbl_user'");
            if (mysqli_num_rows($result) == 0) {
                return true;
            }
            
            $result = mysqli_query($this->conn, "SELECT COUNT(*) as cnt FROM tbl_user");
            $row = mysqli_fetch_assoc($result);
            if ($row['cnt'] == 0) {
                return true;
            }
            
            return false;
            
        } catch (Exception $e) {
            return true;
        }
    }
    
    public function getConnection() {
        if (!$this->conn) {
            $this->connectServer();
            $this->selectDatabase();
        }
        return $this->conn;
    }
    
    private function log($message, $type = 'info') {
        $this->logs[] = ['message' => $message, 'type' => $type];
    }
    
    private function outputLogs() {
        if (php_sapi_name() === 'cli') {
            foreach ($this->logs as $log) {
                echo "[{$log['type']}] {$log['message']}\n";
            }
        } else {
            echo "<!DOCTYPE html><html><head><title>DocuTrack Setup</title>";
            echo "<style>body{font-family:Arial;padding:20px;max-width:800px;margin:0 auto}";
            echo ".log{padding:10px;margin:5px 0;border-radius:5px}";
            echo ".info{background:#e3f2fd}.success{background:#e8f5e9;color:#2e7d32}";
            echo ".warning{background:#fff3e0}.error{background:#ffebee;color:#c62828}</style></head><body>";
            echo "<h1>🗄️ DocuTrack Database Setup</h1>";
            
            foreach ($this->logs as $log) {
                $icon = match($log['type']) {
                    'success' => '✅',
                    'error' => '❌',
                    'warning' => '⚠️',
                    default => 'ℹ️'
                };
                echo "<div class='log {$log['type']}'>{$icon} {$log['message']}</div>";
            }
            
            echo "<p><a href='index.php'>← Go to Application</a></p>";
            echo "</body></html>";
        }
    }
}
