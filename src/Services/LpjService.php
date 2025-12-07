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
     * @param int $kegiatanId
     * @param array $items
     * @return array
     * @throws Exception
     */
    public function submitLpj(int $kegiatanId, array $items): array
    {
        try {
            $this->db->begin_transaction();

            $existingLpj = $this->lpjModel->getLpjWithItemsByKegiatanId($kegiatanId);
            $lpjId = $existingLpj ? $existingLpj['lpj_id'] : $this->lpjModel->insertLpj($kegiatanId);

            if (!$lpjId) {
                throw new Exception("Gagal memproses draft LPJ.");
            }

            if ($existingLpj) {
                $this->lpjModel->deleteLpjItemsByLpjId($lpjId);
            }

            if (!empty($items)) {
                $dbItems = array_map(fn($item) => [
                    'jenis_belanja' => $item['kategori'] ?? 'Lainnya',
                    'uraian' => $item['uraian'] ?? '',
                    'rincian' => $item['rincian'] ?? '',
                    'satuan' => $item['satuan'] ?? '',
                    'total_harga' => floatval($item['harga_satuan'] ?? 0),
                    'sub_total' => floatval($item['total'] ?? 0),
                    'file_bukti_nota' => $item['file_bukti'] ?? null
                ], $items);

                if (!$this->lpjModel->insertLpjItems($lpjId, $dbItems)) {
                    throw new Exception("Gagal menyimpan item LPJ.");
                }
            }

            $this->lpjModel->updateLpjGrandTotal($lpjId);
            $this->lpjModel->updateLpjStatus($lpjId, 'Submitted');

            $this->db->commit();

            return ['success' => true, 'message' => 'LPJ berhasil diajukan ke Bendahara'];
        } catch (Exception $e) {
            if ($this->db->in_transaction) {
                $this->db->rollback();
            }
            // Re-throw the exception to be caught by the controller or global handler
            throw $e;
        }
    }

    /**
     * Mengunggah file bukti untuk item LPJ dan memperbarui database.
     *
     * @param int $itemId
     * @param array $fileData
     * @return array
     * @throws Exception
     */
    public function uploadLpjBukti(int $itemId, array $fileData): array
    {
        // Validasi dasar
        if ($itemId <= 0) {
            throw new Exception('Item ID tidak valid');
        }
        if (!isset($fileData) || $fileData['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('File tidak valid atau tidak ditemukan');
        }

        // Gunakan FileUploadService untuk mengunggah
        $filename = $this->fileUploadService->uploadLpjDocument($fileData, $itemId);

        // Perbarui database melalui model
        $updated = $this->lpjModel->updateFileBukti($itemId, $filename);
        if (!$updated) {
            // Seharusnya ada mekanisme untuk menghapus file jika DB update gagal
            throw new Exception('Gagal memperbarui database dengan file bukti baru.');
        }

        return [
            'success' => true,
            'message' => 'Bukti berhasil diunggah',
            'filename' => $filename
        ];
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
        // Asumsi LpjModel memiliki metode untuk menyimpan komentar revisi dan memperbarui status
        $result = $this->lpjModel->submitRevisiLpj($lpjId, $komentarRevisi); // Perlu metode submitRevisiLpj di LpjModel
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
