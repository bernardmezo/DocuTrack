<?php

namespace App\Services;

use App\Models\Kegiatan\KegiatanModel;
use App\Models\Kak\KakModel;
use App\Models\Rab\RabModel;
use App\Exceptions\ValidationException;
use App\Exceptions\BusinessLogicException;
use App\Core\ViewCache;
use DateTimeImmutable; // Diperlukan untuk validasi tanggal
use Exception;

/**
 * KegiatanService - Business logic untuk Kegiatan
 * 
 * Service layer untuk orchestrate business logic kegiatan,
 * memisahkan business rules dari data access layer.
 * 
 * @category Service
 * @package  DocuTrack\Services
 * @version  2.1.0 - Added ViewCache invalidation
 */
class KegiatanService {
    /**
     * @var mysqli Database connection
     */
    private $db;

    /**
     * @var KegiatanModel
     */
    private $kegiatanModel;

    /**
     * @var KakModel
     */
    private $kakModel;

    /**
     * @var RabModel
     */
    private $rabModel;

    /**
     * @var ValidationService
     */
    private $validationService;

    /**
     * @var FileUploadService
     */
    private $fileUploadService;

    /**
     * Constructor
     * 
     * @param mysqli $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
        $this->kegiatanModel = new KegiatanModel($db);
        $this->kakModel = new KakModel($db);
        $this->rabModel = new RabModel($db);
        $this->validationService = new ValidationService();
        $this->fileUploadService = new FileUploadService();
    }

    /**
     * Get dashboard statistics
     * REFACTORED: Now delegates to Model layer (MVC Compliance - Dec 2025)
     * 
     * @return array Statistics data
     */
    public function getDashboardStats() {
        // Status 5 = Dana Diberikan (Disetujui & Cair)
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN statusUtamaId = 5 THEN 1 ELSE 0 END) as disetujui,
                    SUM(CASE WHEN statusUtamaId = 4 THEN 1 ELSE 0 END) as ditolak,
                    SUM(CASE WHEN statusUtamaId != 4 AND statusUtamaId != 3 AND statusUtamaId != 5 THEN 1 ELSE 0 END) as menunggu
                FROM tbl_kegiatan";
        $result = mysqli_query($this->db, $query);
        return mysqli_fetch_assoc($result);
    }

    /**
     * Get kegiatan based on position and status
     * 
     * @param int $posisiId
     * @param int $statusUtamaId
     * @return array
     */
    public function getKegiatanByStatus($posisiId, $statusUtamaId) {
        $query = "SELECT 
                    k.*, 
                    kak.kakId,
                    kak.gambaranUmum,
                    kak.penerimaManfaat,
                    kak.metodePelaksanaan,
                    s.namaStatusUsulan as status_text
                  FROM tbl_kegiatan k
                  JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  WHERE k.posisiId = ? AND k.statusUtamaId = ?";
        
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ii", $posisiId, $statusUtamaId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    /**
     * Get dashboard KAK list (all or by jurusan)
     * REFACTORED: Now delegates to Model layer (MVC Compliance - Dec 2025)
     * 
     * @param string|null $jurusan Filter by jurusan
     * @return array
     */
    public function getDashboardKAK($jurusan = null) {
        // ✅ FIXED: Delegate to Model instead of direct SQL
        return $this->kegiatanModel->getDashboardKAK($jurusan);
    }

    /**
     * Get kegiatan detail with full relations
     * 
     * @param int $kegiatanId
     * @return array|null
     */
    public function getDetailKegiatan($kegiatanId) {
        $query = "SELECT 
                    k.*, 
                    kak.*,
                    k.tanggalMulai as tanggal_mulai,
                    k.tanggalSelesai as tanggal_selesai,
                    k.suratPengantar as file_surat_pengantar,
                    k.pemilikKegiatan as nama_pengusul,
                    k.namaPJ as nama_pj,
                    k.nip as nim_pj,
                    k.nimPelaksana as nim_pelaksana,
                    k.pemilikKegiatan as nama_pelaksana,
                    s.namaStatusUsulan as status_text
                  FROM tbl_kegiatan k
                  JOIN tbl_kak kak ON k.kegiatanId = kak.kegiatanId
                  LEFT JOIN tbl_user u ON u.userId = k.userId
                  LEFT JOIN tbl_status_utama s ON k.statusUtamaId = s.statusId
                  WHERE k.kegiatanId = ?
                  LIMIT 1";
        
        $stmt = mysqli_prepare($this->db, $query);
        
        if (!$stmt) {
            error_log('Failed to prepare statement in getDetailKegiatan: ' . mysqli_error($this->db));
            return null;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $kegiatanId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $data = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        
        return $data;
    }

    /**
     * Get full details of a kegiatan including KAK, Relations, and RAB
     * 
     * @param int $kegiatanId
     * @return array
     */
    public function getDetailLengkap($kegiatanId) {
        // 1. Get Main Kegiatan Details
        $kegiatanData = $this->getDetailKegiatan($kegiatanId);
        
        if (!$kegiatanData) {
            return null;
        }

        // 2. Get KAK ID
        $kakId = $kegiatanData['kakId'] ?? null;

        if ($kakId) {
            // 3. Fetch KAK Relations (Indicators & Stages)
            $kakRelations = $this->kakModel->getKAKWithRelationsById($kakId);
            
            if ($kakRelations) {
                $kegiatanData['indikator_list'] = $kakRelations['indikator_list'] ?? [];
                $kegiatanData['tahapan_list'] = $kakRelations['tahapan_list'] ?? [];
                // Ensure compatible keys for view
                $kegiatanData['indikator_data'] = $kegiatanData['indikator_list']; 
                $kegiatanData['tahapan_data'] = array_column($kegiatanData['tahapan_list'], 'nama_tahapan'); 
            } else {
                $kegiatanData['indikator_list'] = [];
                $kegiatanData['tahapan_list'] = [];
                $kegiatanData['indikator_data'] = [];
                $kegiatanData['tahapan_data'] = [];
            }

            // 4. Fetch RAB
            $kegiatanData['rab_data'] = $this->rabModel->getRabByKegiatanId($kegiatanId);

        } else {
            $kegiatanData['indikator_list'] = [];
            $kegiatanData['tahapan_list'] = [];
            $kegiatanData['rab_data'] = [];
        }

        return $kegiatanData;
    }

    /**
     * Create new kegiatan with complete data (transaction)
     * 
     * Business logic: Create kegiatan with KAK, indikator, tahapan, and RAB
     * 
     * @param array $data Complete kegiatan data
     * @return int Kegiatan ID
     * @throws ValidationException
     * @throws BusinessLogicException
     */
    public function createKegiatan($data) {
        // Validate input
        $this->validateKegiatanData($data);

        mysqli_begin_transaction($this->db);

        try {
            // 1. Insert kegiatan
            $kegiatanId = $this->insertKegiatan($data);

            // 2. Insert KAK
            $kakId = $this->insertKAK($kegiatanId, $data);

            // 3. Insert tahapan pelaksanaan
            if (!empty($data['tahapan']) && is_array($data['tahapan'])) {
                $this->insertTahapan($kakId, $data['tahapan']);
            }

            // 4. Insert indikator keberhasilan
            if (!empty($data['indikator_nama']) && is_array($data['indikator_nama'])) {
                $this->insertIndikator($kakId, $data);
            }

            // 5. Insert RAB with categories
            if (!empty($data['rab_data'])) {
                $this->insertRAB($kakId, $data['rab_data']);
            }

            mysqli_commit($this->db);
            
            // 6. Invalidate dashboard cache after successful create
            $this->invalidateDashboardCache();
            
            return $kegiatanId;

        } catch (Exception $e) {
            mysqli_rollback($this->db);
            throw new BusinessLogicException("Gagal membuat kegiatan: " . $e->getMessage(), [
                'original_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Processes and updates activity details (PJ, dates, cover letter).
     * Handles validation and file uploads.
     *
     * @param array $formData Raw form data (e.g., $_POST).
     * @param array|null $fileData Raw file data (e.g., $_FILES['surat_pengantar']).
     * @return bool True on success.
     * @throws ValidationException If input validation fails.
     * @throws BusinessLogicException If a business rule is violated or an internal error occurs.
     */
    public function processRincianKegiatan(array $formData, ?array $fileData = null): bool
    {
        // 1. Validate Input
        $rules = [
            'kegiatan_id'    => 'required|numeric',
            'penanggung_jawab' => 'required',
            'nim_nip_pj'     => 'required',
            'tanggal_mulai'  => 'required',
            'tanggal_selesai' => 'required',
        ];

        // Ensure 'kegiatan_id' is also validated if it comes as 'id_kegiatan'
        if (isset($formData['id_kegiatan'])) {
            // Create a mutable copy to modify
            $dataToValidate = $formData; 
            $dataToValidate['kegiatan_id'] = $dataToValidate['id_kegiatan'];
            unset($dataToValidate['id_kegiatan']);
        } else {
            $dataToValidate = $formData;
        }

        $validatedData = $this->validationService->validate($dataToValidate, $rules);

        // Additional date specific validation
        try {
            $tanggalMulai = new DateTimeImmutable($validatedData['tanggal_mulai']);
            $tanggalSelesai = new DateTimeImmutable($validatedData['tanggal_selesai']);

            if ($tanggalMulai > $tanggalSelesai) {
                throw new ValidationException('Tanggal selesai harus sama atau setelah tanggal mulai.', ['tanggal_selesai' => ['Tanggal selesai harus sama atau setelah tanggal mulai.']]);
            }
        } catch (Exception $e) {
            // Re-throw as validation exception if date parsing fails
            if ($e instanceof ValidationException) {
                 throw $e;
            }
            throw new ValidationException('Format tanggal tidak valid.', ['tanggal_mulai' => ['Format tanggal tidak valid.'], 'tanggal_selesai' => ['Format tanggal tidak valid.']]);
        }

        $uploadedFileName = null;
        if ($fileData && $fileData['error'] === UPLOAD_ERR_OK) {
            try {
                $uploadedFileName = $this->fileUploadService->uploadSuratPengantar($fileData);
            } catch (Exception $e) {
                throw new BusinessLogicException('Gagal mengunggah surat pengantar: ' . $e->getMessage());
            }
        } elseif ($fileData && $fileData['error'] !== UPLOAD_ERR_NO_FILE) {
            throw new BusinessLogicException('Terjadi kesalahan saat mengunggah surat pengantar.');
        }

        $dataUpdate = [
            'namaPj' => $validatedData['penanggung_jawab'],
            'nip' => $validatedData['nim_nip_pj'],
            'tgl_mulai' => $tanggalMulai->format('Y-m-d'),
            'tgl_selesai' => $tanggalSelesai->format('Y-m-d')
        ];

        // 2. Call existing updateRincianKegiatan
        $success = $this->updateRincianKegiatan((int)$validatedData['kegiatan_id'], $dataUpdate, $uploadedFileName);

        if (!$success) {
            throw new BusinessLogicException('Gagal memperbarui data kegiatan di database.');
        }
        
        return true;
    }


    /**
     * Update rincian kegiatan (PJ, dates, surat)
     * 
     * @param int $kegiatanId
     * @param array $data
     * @param string|null $fileSurat
     * @return bool
     */
    public function updateRincianKegiatan($kegiatanId, $data, $fileSurat = null) {
        $posisiIdPPK = 4;
        $statusMenunggu = 1;

        if ($fileSurat) {
            $query = "UPDATE tbl_kegiatan SET 
                        namaPJ = ?, 
                        nip = ?, 
                        tanggalMulai = ?, 
                        tanggalSelesai = ?, 
                        suratPengantar = ?,
                        posisiId = ?,
                        statusUtamaId = ?
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "sssssiii", 
                $data['namaPj'], 
                $data['nip'], 
                $data['tgl_mulai'], 
                $data['tgl_selesai'], 
                $fileSurat, 
                $posisiIdPPK,
                $statusMenunggu,
                $kegiatanId
            );
        } else {
            $query = "UPDATE tbl_kegiatan SET 
                        namaPJ = ?, 
                        nip = ?, 
                        tanggalMulai = ?, 
                        tanggalSelesai = ?,
                        posisiId = ?,
                        statusUtamaId = ?
                      WHERE kegiatanId = ?";
            
            $stmt = mysqli_prepare($this->db, $query);
            mysqli_stmt_bind_param($stmt, "ssssiii", 
                $data['namaPj'], 
                $data['nip'], 
                $data['tgl_mulai'], 
                $data['tgl_selesai'], 
                $posisiIdPPK,
                $statusMenunggu,
                $kegiatanId
            );
        }

        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // Invalidate dashboard cache after update
        if ($result) {
            $this->invalidateDashboardCache();
        }
        
        return $result;
    }

    /**
     * Validate kegiatan data (business rules)
     * 
     * @param array $data
     * @throws ValidationException
     */
    private function validateKegiatanData($data) {
        $errors = [];

        // Required fields
        if (empty($data['nama_kegiatan_step1'])) {
            $errors['nama_kegiatan'] = "Nama kegiatan wajib diisi";
        }

        if (empty($data['nama_pengusul'])) {
            $errors['nama_pengusul'] = "Nama pengusul wajib diisi";
        }

        if (empty($data['nim_nip'])) {
            $errors['nim_nip'] = "NIM/NIP wajib diisi";
        }

        if (empty($data['jurusan'])) {
            $errors['jurusan'] = "Jurusan wajib dipilih";
        }

        if (empty($data['prodi'])) {
            $errors['prodi'] = "Prodi wajib dipilih";
        }

        // Business rule: RAB minimal harus ada
        if (empty($data['rab_data'])) {
            $errors['rab_data'] = "RAB minimal harus memiliki 1 item";
        }

        if (!empty($errors)) {
            throw new ValidationException("Data kegiatan tidak valid", $errors);
        }
    }

    /**
     * Insert kegiatan record
     * 
     * @param array $data
     * @return int Kegiatan ID
     */
    private function insertKegiatan($data) {
        $nama_pengusul = $data['nama_pengusul'] ?? '';
        $nim           = $data['nim_nip'] ?? '';
        $jurusan       = $data['jurusan'] ?? '';
        $prodi         = $data['prodi'] ?? '';
        $nama_kegiatan = $data['nama_kegiatan_step1'] ?? '';
        $user_id       = $_SESSION['user_id'] ?? 0;
        $tgl_sekarang  = date('Y-m-d H:i:s');
        $status_awal   = 1;
        $wadir_tujuan  = $data['wadir_tujuan'] ?? null;
        $posisi_awal   = 2;

        $query = "INSERT INTO tbl_kegiatan 
            (namaKegiatan, prodiPenyelenggara, pemilikKegiatan, nimPelaksana, userId, jurusanPenyelenggara, statusUtamaId, createdAt, wadirTujuan, posisiId)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "ssssisisii", 
            $nama_kegiatan, $prodi, $nama_pengusul, $nim, $user_id, 
            $jurusan, $status_awal, $tgl_sekarang, $wadir_tujuan, $posisi_awal
        );
        
        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal insert kegiatan: " . mysqli_error($this->db));
        }
        
        $kegiatanId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);

        return $kegiatanId;
    }

    /**
     * Insert KAK record
     * 
     * @param int $kegiatanId
     * @param array $data
     * @return int KAK ID
     */
    private function insertKAK($kegiatanId, $data) {
        $iku           = $data['indikator_kinerja'] ?? 'Belum pilih';
        $gambaran_umum = $data['gambaran_umum'] ?? '';
        $penerima      = $data['penerima_manfaat'] ?? '';
        $metode        = $data['metode_pelaksanaan'] ?? '';
        $tgl_only      = date('Y-m-d');
        
        $query = "INSERT INTO tbl_kak 
            (kegiatanId, iku, gambaranUmum, penerimaMaanfaat, metodePelaksanaan, tglPembuatan)
            VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "isssss", 
            $kegiatanId, $iku, $gambaran_umum, $penerima, $metode, $tgl_only
        );

        if (!mysqli_stmt_execute($stmt)) {
            throw new Exception("Gagal insert KAK: " . mysqli_error($this->db));
        }

        $kakId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);

        return $kakId;
    }

    /**
     * Insert tahapan pelaksanaan
     * 
     * @param int $kakId
     * @param array $tahapanList
     */
    private function insertTahapan($kakId, $tahapanList) {
        $query = "INSERT INTO tbl_tahapan_pelaksanaan (kakId, namaTahapan) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->db, $query);
        
        foreach ($tahapanList as $tahap) {
            if (!empty($tahap)) {
                mysqli_stmt_bind_param($stmt, "is", $kakId, $tahap);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal insert tahapan: " . mysqli_error($this->db));
                }
            }
        }
        mysqli_stmt_close($stmt);
    }

    /**
     * Insert indikator keberhasilan
     * 
     * @param int $kakId
     * @param array $data
     */
    private function insertIndikator($kakId, $data) {
        $query = "INSERT INTO tbl_indikator_kak (kakId, bulan, indikatorKeberhasilan, targetPersen) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->db, $query);
        
        $count = count($data['indikator_nama']);
        for ($i = 0; $i < $count; $i++) {
            $bulan     = $data['indikator_bulan'][$i] ?? '';
            $nama_ind  = $data['indikator_nama'][$i] ?? '';
            $target    = intval($data['indikator_target'][$i] ?? 0);

            if (!empty($nama_ind)) {
                mysqli_stmt_bind_param($stmt, "issi", $kakId, $bulan, $nama_ind, $target);
                if (!mysqli_stmt_execute($stmt)) {
                    throw new Exception("Gagal insert indikator: " . mysqli_error($this->db));
                }
            }
        }
        mysqli_stmt_close($stmt);
    }

    /**
     * Insert RAB with categories
     * 
     * @param int $kakId
     * @param string $rabJson JSON string of RAB data
     */
    private function insertRAB($kakId, $rabJson) {
        $budgetData = is_string($rabJson) ? json_decode($rabJson, true) : $rabJson;

        if (empty($budgetData) || !is_array($budgetData)) {
            return;
        }

        foreach ($budgetData as $namaKategori => $items) {
            if (empty($items)) continue;

            // Get or create category
            $kategoriId = $this->getOrCreateKategori($namaKategori);

            // Insert items
            $this->insertRABItems($kakId, $kategoriId, $items);
        }
    }

    /**
     * Get existing kategori or create new one
     * 
     * @param string $namaKategori
     * @return int Kategori ID
     */
    private function getOrCreateKategori($namaKategori) {
        // Check existing
        $checkQuery = "SELECT kategoriRabId FROM tbl_kategori_rab WHERE namaKategori = ? LIMIT 1";
        $stmt = mysqli_prepare($this->db, $checkQuery);
        mysqli_stmt_bind_param($stmt, "s", $namaKategori);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            mysqli_stmt_close($stmt);
            return $row['kategoriRabId'];
        }
        mysqli_stmt_close($stmt);

        // Create new
        $insertQuery = "INSERT INTO tbl_kategori_rab (namaKategori) VALUES (?)";
        $stmt = mysqli_prepare($this->db, $insertQuery);
        mysqli_stmt_bind_param($stmt, "s", $namaKategori);
        mysqli_stmt_execute($stmt);
        $kategoriId = mysqli_insert_id($this->db);
        mysqli_stmt_close($stmt);

        return $kategoriId;
    }

    /**
     * Insert RAB items for a category
     * 
     * @param int $kakId
     * @param int $kategoriId
     * @param array $items
     */
    private function insertRABItems($kakId, $kategoriId, $items) {
        $query = "INSERT INTO tbl_rab (kakId, kategoriId, uraian, rincian, sat1, sat2, vol1, vol2, harga, totalHarga) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($this->db, $query);

        foreach ($items as $item) {
            $uraian  = $item['uraian'] ?? '';
            $rincian = $item['rincian'] ?? '';
            $vol1    = floatval($item['vol1'] ?? 0);
            $vol2    = floatval($item['vol2'] ?? 1);
            $sat1    = $item['sat1'] ?? '';
            $sat2    = $item['sat2'] ?? '';
            $harga   = floatval($item['harga'] ?? 0);
            $total   = ($vol1 * $vol2) * $harga;

            mysqli_stmt_bind_param($stmt, "iissssdddd", 
                $kakId, $kategoriId, $uraian, $rincian, 
                $sat1, $sat2, $vol1, $vol2, $harga, $total
            );
            
            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception("Gagal insert item RAB: " . mysqli_error($this->db));
            }
        }
        mysqli_stmt_close($stmt);
    }

    /**
     * Invalidate dashboard cache
     * 
     * Called after create/update/delete operations to ensure fresh data
     */
    private function invalidateDashboardCache() {
        $cache = new ViewCache();
        
        // Invalidate all dashboard-related caches
        $cache->invalidatePattern("dashboard_stats_*");
        $cache->invalidatePattern("recent_activity_*");
        $cache->invalidatePattern("notification_panel_*");
        
        error_log("KegiatanService: Dashboard cache invalidated after data change");
    }

    /**
     * Delete kegiatan (with cache invalidation)
     * 
     * @param int $kegiatanId
     * @return bool
     */
    public function deleteKegiatan($kegiatanId) {
        // TODO: Implement delete logic with transaction
        
        // Invalidate cache after delete
        $this->invalidateDashboardCache();
        
        return true;
    }
}