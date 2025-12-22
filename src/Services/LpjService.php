<?php

namespace App\Services;

use App\Models\Lpj\LpjModel;
use App\Services\FileUploadService;
use Exception;

class LpjService
{
    private $db;
    private $lpjModel;
    private $fileUploadService;

    public function __construct($db)
    {
        $this->db = $db;
        $this->lpjModel = new LpjModel($this->db);
        $this->fileUploadService = new FileUploadService();
    }

    /**
 * Memproses pengajuan atau pembaruan LPJ beserta item-itemnya.
 * Mengelola transaksi database untuk memastikan integritas data.
 *
 * ✅ FIXED: Proper validation and logging for realisasi updates
 *
 * @param int $kegiatanId
 * @param array $items
 * @return array
 * @throws Exception
 */
public function submitLpj(int $kegiatanId, array $items): array
{
    try {
        $this->db->begin_transaction();

        error_log("🚀 submitLpj START: kegiatanId={$kegiatanId}, items=" . json_encode($items));

        // [SECURITY FIX] Validate Disbursed Funds
        $disbursedAmount = $this->lpjModel->getDisbursedAmount($kegiatanId);
        if ($disbursedAmount <= 0) {
             throw new Exception("LPJ tidak dapat diajukan. Dana belum dicairkan untuk kegiatan ini.");
        }

        // Get or create LPJ record
        $existingLpj = $this->lpjModel->getLpjWithItemsByKegiatanId($kegiatanId);
        $lpjId = $existingLpj ? $existingLpj['lpj_id'] : $this->lpjModel->insertLpj($kegiatanId);

        if (!$lpjId) {
            throw new Exception("Gagal memproses record LPJ.");
        }

        error_log("📋 LPJ Record: lpjId={$lpjId}, kegiatanId={$kegiatanId}");

        // ✅ [VALIDATION] Cek apakah LPJ sudah pernah di-submit sebelumnya
        $lpjData = $this->lpjModel->getLpjWithItemsById($lpjId);
        $isRevision = isset($lpjData['status_id']) && $lpjData['status_id'] == 2;

        if ($lpjData && !empty($lpjData['submitted_at']) && !$isRevision) {
            throw new Exception("LPJ sudah pernah disubmit sebelumnya pada " . date('d/m/Y H:i', strtotime($lpjData['submitted_at'])) . ". Tidak dapat submit ulang.");
        }

        // [VALIDATION] Cek apakah semua bukti sudah diupload
        $uploadStatus = $this->lpjModel->getUploadBuktiStatus($lpjId);
        error_log("📊 Upload Status: " . json_encode($uploadStatus));
        
        if ($uploadStatus['total'] > 0 && $uploadStatus['uploaded'] < $uploadStatus['total']) {
            throw new Exception("Mohon upload semua bukti terlebih dahulu. (" . $uploadStatus['uploaded'] . "/" . $uploadStatus['total'] . " item sudah diupload)");
        }

        // ✅ VALIDATION with DECIMAL string parsing
        if (!empty($items)) {
            $validationErrors = [];
            $totalRealisasi = 0;
            
            $lpjData = $this->lpjModel->getLpjWithItemsById($lpjId);
            $kakId = $lpjData['kakId'] ?? 0;
            
            if (!$kakId) {
                throw new Exception("KAK tidak ditemukan untuk LPJ ini.");
            }
            
            foreach ($items as $index => $item) {
                $rabItemId = (int)($item['id'] ?? 0);
                
                // ✅ Parse realisasi as decimal string
                $realisasiRaw = $item['realisasi'] ?? $item['total'] ?? "0.00";
                $realisasiFloat = floatval($realisasiRaw);
                
                error_log("✅ Item #{$index} validated: rabItemId={$rabItemId}, realisasi=\"{$realisasiRaw}\" ({$realisasiFloat})");
                
                if ($rabItemId <= 0) {
                    $validationErrors[] = "Item #{$index}: ID tidak valid";
                    continue;
                }
                
                if ($realisasiFloat < 0) {
                    $validationErrors[] = "Item #{$index}: Realisasi tidak boleh negatif";
                    continue;
                }
                
                $totalRealisasi += $realisasiFloat;
            }
            
            if (!empty($validationErrors)) {
                throw new Exception("Validasi gagal:\n" . implode("\n", $validationErrors));
            }
            
            // ✅ Validate total (with tolerance)
            $totalAnggaran = $this->lpjModel->getTotalAnggaranByKakId($kakId);
            
            error_log("📊 Validation Summary: Total Anggaran=Rp " . number_format($totalAnggaran, 2) . ", Total Realisasi=Rp " . number_format($totalRealisasi, 2));
            
            if (abs($totalRealisasi - $totalAnggaran) > 0.01) {
                throw new Exception(
                    "Total realisasi tidak sesuai dengan total anggaran!\n\n" .
                    "Total Anggaran KAK: Rp " . number_format($totalAnggaran, 2, ',', '.') . "\n" .
                    "Total Realisasi: Rp " . number_format($totalRealisasi, 2, ',', '.') . "\n" .
                    "Selisih: Rp " . number_format(abs($totalRealisasi - $totalAnggaran), 2, ',', '.') . "\n\n" .
                    "Mohon sesuaikan nilai realisasi."
                );
            }
            
            error_log("✅ Total validation passed");

            // ✅ Update items
            if (!$this->lpjModel->updateLpjItemsRealisasi((int)$lpjId, $items)) {
                throw new Exception("Gagal memperbarui item realisasi LPJ.");
            }
            
            error_log("✅ All items updated successfully");
        }

        // Update grand total
        $this->lpjModel->updateLpjGrandTotal($lpjId);
        
        // ✅ UPDATE STATUS KE 'Submitted'
        if (!$this->lpjModel->updateLpjStatus($lpjId, 'Submitted')) {
            throw new Exception("Gagal mengupdate status LPJ.");
        }

        $this->db->commit();

        error_log("✅ LPJ SUBMITTED SUCCESSFULLY");
        return ['success' => true, 'message' => 'LPJ berhasil diajukan ke Bendahara'];
        
    } catch (Exception $e) {
        if ($this->db->in_transaction) {
            $this->db->rollback();
        }
        error_log("❌ LpjService::submitLpj Error: " . $e->getMessage());
        throw $e;
    }
}

    /**
     * ✅ Upload bukti HANYA untuk kolom fileBukti
     * Tidak menyentuh kolom lain (realisasi akan diisi saat submit)
     * 
     * @param int $lpjId ID LPJ yang sedang dikerjakan
     * @param int $rabItemId ID item dari tbl_rab
     * @param array $fileData $_FILES['file']
     * @return array
     */
    public function uploadLpjBukti(int $lpjId, int $rabItemId, array $fileData): array
    {
        try {
            error_log("🔄 uploadLpjBukti: lpjId=$lpjId, rabItemId=$rabItemId");
            
            // ✅ Validasi input
            if ($lpjId <= 0 || $rabItemId <= 0) {
                throw new Exception('LPJ ID atau RAB Item ID tidak valid');
            }
            
            if (!isset($fileData) || $fileData['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('File tidak valid atau tidak ditemukan');
            }

            // ✅ Validasi ukuran file (max 5MB)
            if ($fileData['size'] > 5 * 1024 * 1024) {
                throw new Exception('Ukuran file maksimal 5MB');
            }

            // ✅ Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
            $fileType = mime_content_type($fileData['tmp_name']);
            if (!in_array($fileType, $allowedTypes)) {
                throw new Exception('Format file tidak didukung. Gunakan JPG/PNG/PDF. Detected: ' . $fileType);
            }

            // ✅ Generate unique filename
            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
            $filename = 'lpj_bukti_' . $rabItemId . '_' . time() . '.' . $extension;
            
            // ✅ Path tujuan upload
            $uploadDir = __DIR__ . '/../../public/uploads/lpj/';
            
            // ✅ Buat direktori jika belum ada
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    throw new Exception('Gagal membuat direktori upload');
                }
            }
            
            $targetPath = $uploadDir . $filename;
            
            // ✅ Upload file
            if (!move_uploaded_file($fileData['tmp_name'], $targetPath)) {
                throw new Exception('Gagal menyimpan file ke server');
            }
            
            error_log("✅ File uploaded: $filename");
            
            // ✅ TAMBAHAN BARU: Ambil data RAB item
            $rabItemData = $this->lpjModel->getRABItemById($rabItemId);
            
            if (!$rabItemData) {
                // Rollback file jika data RAB tidak ditemukan
                @unlink($targetPath);
                throw new Exception('Data RAB tidak ditemukan untuk item ID: ' . $rabItemId);
            }
            
            // 3. Insert/Update ke tbl_lpj_item (HANYA fileBukti)
            $insertResult = $this->lpjModel->upsertLpjItemBukti(
                $lpjId, 
                $rabItemId, 
                $filename, 
                $rabItemData
            );
            
            if (!$insertResult) {
                // Rollback file jika insert gagal
                @unlink($targetPath);
                throw new Exception('Gagal menyimpan data bukti ke database');
            }
            
            error_log("✅ Database updated for lpjId=$lpjId, rabItemId=$rabItemId, filename=$filename");
            
            return [
                'success' => true,
                'message' => 'Bukti berhasil diupload dan tersimpan',
                'filename' => $filename,
                'path' => 'uploads/lpj/' . $filename
            ];

        } catch (Exception $e) {
            error_log("❌ LpjService::uploadLpjBukti Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function verifikasiLpj(int $lpjId): bool
    {
        $result = $this->lpjModel->updateLpjStatus($lpjId, 'Verified'); // Menggunakan status baru 'Verified'
        if (!$result) {
            throw new Exception("Gagal memverifikasi LPJ.");
        }
        return $result;
    }

    public function tolakLpj(int $lpjId, string $komentar): bool
    {
        // Asumsi LpjModel memiliki metode untuk menyimpan komentar penolakan dan memperbarui status
        $result = $this->lpjModel->tolakLpj($lpjId, $komentar); // Perlu metode tolakLpj di LpjModel
        if (!$result) {
            throw new Exception("Gagal menolak LPJ.");
        }
        return $result;
    }

    public function submitRevisiLpj(int $lpjId, array $komentarRevisi): bool
    {
        // Convert array comments to string or JSON if needed, or update model to handle array.
        // Assuming simple string for now or first item.
        $komentarStr = is_array($komentarRevisi) ? json_encode($komentarRevisi) : $komentarRevisi;
        
        $result = $this->lpjModel->submitRevisiLpj($lpjId, $komentarStr); 
        if (!$result) {
            throw new Exception("Gagal mengirim revisi LPJ.");
        }
        return $result;
    }

    /**
     * Mengambil data LPJ untuk tampilan dashboard.
     *
     * @return array Array berisi data LPJ.
     */
    public function getDashboardLPJ(): array
    {
        return $this->lpjModel->getDashboardLPJ();
    }
}