<?php

namespace App\Services;

use App\Exceptions\BusinessLogicException;
use App\Models\kegiatan\KegiatanModel;
use App\Models\VerifikatorModel;
use App\Services\LogStatusService;
use App\Services\ValidationService;
use Throwable;

class VerifikatorService
{
    private VerifikatorModel $verifikatorModel;
    private LogStatusService $logStatusService;
    private ValidationService $validationService;
    private KegiatanModel $kegiatanModel;

    public function __construct(
        VerifikatorModel $verifikatorModel,
        LogStatusService $logStatusService,
        ValidationService $validationService,
        KegiatanModel $kegiatanModel
    ) {
        $this->verifikatorModel = $verifikatorModel;
        $this->logStatusService = $logStatusService;
        $this->validationService = $validationService;
        $this->kegiatanModel = $kegiatanModel;
    }

    public function getDashboardStats()
    {
        return $this->verifikatorModel->getDashboardStats();
    }

    public function getDashboardKAK()
    {
        return $this->verifikatorModel->getDashboardKAK();
    }

    public function getListJurusan()
    {
        return $this->verifikatorModel->getListJurusan();
    }

    public function getRiwayat()
    {
        return $this->verifikatorModel->getRiwayat();
    }

    public function getDetailKegiatan($kegiatanId)
    {
        return $this->verifikatorModel->getDetailKegiatan($kegiatanId);
    }

    public function getIndikatorByKAK($kakId)
    {
        return $this->verifikatorModel->getIndikatorByKAK($kakId);
    }

    public function getTahapanByKAK($kakId)
    {
        return $this->verifikatorModel->getTahapanByKAK($kakId);
    }

    public function getRABByKAK($kakId)
    {
        return $this->verifikatorModel->getRABByKAK($kakId);
    }

    public function getProposalMonitoring()
    {
        return $this->verifikatorModel->getProposalMonitoring();
    }

    /**
     * Menyetujui usulan.
     * Moved from VerifikatorModel to VerifikatorService for business logic and notification trigger.
     */
    public function approveUsulan(int $kegiatanId, string $kodeMak, ?string $catatan = null): bool
    {
        $result = $this->verifikatorModel->updateKegiatanApprovalStatus($kegiatanId, $kodeMak, $catatan);

        if ($result) {
            try {
                $kegiatan = $this->verifikatorModel->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'APPROVAL',
                        "Proposal kegiatan \"{$kegiatan['namaKegiatan']}\" Anda telah disetujui oleh Verifikator.",
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                error_log("Gagal membuat notifikasi persetujuan untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }
        return $result;
    }

    /**
     * Menolak usulan.
     * Moved from VerifikatorModel to VerifikatorService for business logic and notification trigger.
     */
    public function rejectUsulan(int $kegiatanId, string $alasanPenolakan = ''): bool
    {
        $result = $this->verifikatorModel->updateKegiatanRejectionStatus($kegiatanId, $alasanPenolakan);

        if ($result) {
            try {
                $kegiatan = $this->verifikatorModel->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'REJECTION',
                        "Proposal kegiatan \"{$kegiatan['namaKegiatan']}\" Anda ditolak oleh Verifikator. Alasan: " . ($alasanPenolakan ?: 'Tidak ada.'),
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                error_log("Gagal membuat notifikasi penolakan untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }
        return $result;
    }

    /**
     * Mengirim usulan untuk direvisi oleh Verifikator.
     *
     * @param int $kegiatanId ID Kegiatan
     * @param array $komentarRevisi Komentar revisi
     * @return bool
     * @throws BusinessLogicException
     */
    public function reviseUsulan(int $kegiatanId, array $komentarRevisi): bool
    {
        $this->validationService->validate(['kegiatan_id' => $kegiatanId, 'komentar' => $komentarRevisi], [
            'kegiatan_id' => 'required|numeric',
            'komentar' => 'required|array'
        ]);

        $dbResult = $this->verifikatorModel->updateKegiatanRevisionStatus($kegiatanId, $komentarRevisi);

        if ($dbResult) {
            try {
                // Dapatkan userId dari pengusul kegiatan
                $kegiatan = $this->verifikatorModel->getDetailKegiatan($kegiatanId);
                if ($kegiatan && isset($kegiatan['userId'])) {
                    $this->logStatusService->createNotification(
                        (int) $kegiatan['userId'],
                        'REVISION',
                        "Proposal kegiatan \"{$kegiatan['namaKegiatan']}\" Anda perlu direvisi.",
                        $kegiatanId
                    );
                }
            } catch (Throwable $e) {
                // Jika notifikasi gagal, jangan gagalkan seluruh proses, tapi catat errornya.
                error_log("Gagal membuat notifikasi revisi untuk kegiatan ID {$kegiatanId}: " . $e->getMessage());
            }
        }

        return $dbResult;
    }
}
