<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LogStatusModel;
use App\Exceptions\BusinessLogicException;
use mysqli;

/**
 * WorkflowService - Centralized workflow management
 * 
 * Handles workflow transitions based on merged_schema.sql specifications:
 * 1. Admin (posisiId=1) - Initial submission
 * 2. Verifikator (posisiId=2) - Document verification  
 * 3. PPK (posisiId=4) - Budget approval
 * 4. Wadir (posisiId=3) - Director level approval
 * 5. Bendahara (posisiId=5) - Fund disbursement
 * 
 * Status flow:
 * 1. Menunggu → 2. Revisi → 3. Disetujui → 4. Ditolak → 5. Dana diberikan
 * 
 * @category Service
 * @package  DocuTrack
 * @version  3.0.0
 */
class WorkflowService
{
    private mysqli $db;
    private LogStatusModel $logStatusModel;
    
    /**
     * Workflow positions as per tbl_role
     */
    public const POSITION_ADMIN = 1;
    public const POSITION_VERIFIKATOR = 2;
    public const POSITION_WADIR = 3;
    public const POSITION_PPK = 4;
    public const POSITION_BENDAHARA = 5;
    
    /**
     * Status constants as per tbl_status_utama
     */
    public const STATUS_MENUNGGU = 1;
    public const STATUS_REVISI = 2;
    public const STATUS_DISETUJUI = 3;
    public const STATUS_DITOLAK = 4;
    public const STATUS_DANA_DIBERIKAN = 5;
    
    /**
     * Workflow routing map
     * [current_position => next_position_on_approve]
     */
    private const WORKFLOW_ROUTING = [
        self::POSITION_ADMIN => self::POSITION_VERIFIKATOR,      // Admin -> Verifikator
        self::POSITION_VERIFIKATOR => self::POSITION_ADMIN,      // Verifikator -> Admin (input penanggung jawab)
        self::POSITION_PPK => self::POSITION_WADIR,              // PPK -> Wadir
        self::POSITION_WADIR => self::POSITION_BENDAHARA,        // Wadir -> Bendahara
        self::POSITION_BENDAHARA => self::POSITION_BENDAHARA     // Bendahara (end of workflow)
    ];

    /**
     * Reverse workflow map for rollbacks
     * [current_position => previous_position]
     */
    private const WORKFLOW_REVERSE_ROUTING = [
        self::POSITION_VERIFIKATOR => self::POSITION_ADMIN,
        self::POSITION_PPK => self::POSITION_VERIFIKATOR,
        self::POSITION_WADIR => self::POSITION_PPK,
        self::POSITION_BENDAHARA => self::POSITION_WADIR
    ];
    
    /**
     * Role names for notifications
     */
    private const ROLE_NAMES = [
        self::POSITION_ADMIN => 'Admin',
        self::POSITION_VERIFIKATOR => 'Verifikator',
        self::POSITION_WADIR => 'Wakil Direktur',
        self::POSITION_PPK => 'PPK',
        self::POSITION_BENDAHARA => 'Bendahara'
    ];
    
    public function __construct(mysqli $db)
    {
        $this->db = $db;
        $this->logStatusModel = new LogStatusModel($db);
    }
    
    /**
     * Get next position in workflow after approval
     * 
     * @param int $currentPosition
     * @return int Next position ID
     * @throws BusinessLogicException
     */
    public function getNextPosition(int $currentPosition): int
    {
        if (!isset(self::WORKFLOW_ROUTING[$currentPosition])) {
            throw new BusinessLogicException("Invalid workflow position: {$currentPosition}");
        }
        
        return self::WORKFLOW_ROUTING[$currentPosition];
    }

    /**
     * Get previous position for rejection/rollback
     * 
     * @param int $currentPosition
     * @return int Previous position ID or POSITION_ADMIN if start
     */
    public function getPreviousPosition(int $currentPosition): int
    {
        return self::WORKFLOW_REVERSE_ROUTING[$currentPosition] ?? self::POSITION_ADMIN;
    }
    
    /**
     * Get position name
     */
    public function getPositionName(int $positionId): string
    {
        return self::ROLE_NAMES[$positionId] ?? 'Unknown';
    }
    
    /**
     * Check if position can approve
     */
    public function canApprove(int $positionId): bool
    {
        return isset(self::WORKFLOW_ROUTING[$positionId]);
    }
    
    /**
     * Check if kegiatan is at specific position
     */
    public function isAtPosition(array $kegiatan, int $positionId): bool
    {
        return (int)($kegiatan['posisiId'] ?? 0) === $positionId;
    }
    
    /**
     * Check if kegiatan has specific status
     */
    public function hasStatus(array $kegiatan, int $statusId): bool
    {
        return (int)($kegiatan['statusUtamaId'] ?? 0) === $statusId;
    }
    
    /**
     * Check if ready for next step (Disetujui status)
     */
    public function isApproved(array $kegiatan): bool
    {
        return $this->hasStatus($kegiatan, self::STATUS_DISETUJUI);
    }
    
    /**
     * Check if needs revision
     */
    public function needsRevision(array $kegiatan): bool
    {
        return $this->hasStatus($kegiatan, self::STATUS_REVISI);
    }
    
    /**
     * Check if rejected
     */
    public function isRejected(array $kegiatan): bool
    {
        return $this->hasStatus($kegiatan, self::STATUS_DITOLAK);
    }
    
    /**
     * Check if waiting for approval
     */
    public function isPending(array $kegiatan): bool
    {
        return $this->hasStatus($kegiatan, self::STATUS_MENUNGGU);
    }
    
    /**
     * Update workflow position after approval
     * 
     * @param int $kegiatanId
     * @param int $currentPosition
     * @param int $newStatus Default STATUS_DISETUJUI
     * @param string|null $kodeMak Optional MAK code (for Verifikator)
     * @return bool
     * @throws BusinessLogicException
     */
    public function moveToNextPosition(
        int $kegiatanId, 
        int $currentPosition, 
        int $newStatus = self::STATUS_DISETUJUI,
        array $additionalData = []
    ): bool {
        $nextPosition = $this->getNextPosition($currentPosition);
        
        $this->db->begin_transaction();
        
        try {
            // Lock row for update
            $lockSql = "SELECT kegiatanId FROM tbl_kegiatan WHERE kegiatanId = ? FOR UPDATE";
            $lockStmt = $this->db->prepare($lockSql);
            $lockStmt->bind_param('i', $kegiatanId);
            $lockStmt->execute();
            $result = $lockStmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new BusinessLogicException("Kegiatan not found: {$kegiatanId}");
            }
            $lockStmt->close();
            
            // Update position and status
            $updateSql = "UPDATE tbl_kegiatan 
                         SET posisiId = ?, statusUtamaId = ?";
            
            $params = [$nextPosition, $newStatus];
            $types = 'ii';
            
            // Add additional data based on current position
            if ($currentPosition === self::POSITION_VERIFIKATOR) {
                if (isset($additionalData['kodeMak'])) {
                    $updateSql .= ", buktiMAK = ?";
                    $params[] = $additionalData['kodeMak'];
                    $types .= 's';
                }
                if (isset($additionalData['danaDisetujui'])) {
                    $updateSql .= ", danaDisetujui = ?";
                    $params[] = $additionalData['danaDisetujui'];
                    $types .= 'd';
                }
                if (isset($additionalData['umpanBalik'])) {
                    $updateSql .= ", umpanBalikVerifikator = ?";
                    $params[] = $additionalData['umpanBalik'];
                    $types .= 's';
                }
            }
            
            $updateSql .= " WHERE kegiatanId = ?";
            $params[] = $kegiatanId;
            $types .= 'i';
            
            $updateStmt = $this->db->prepare($updateSql);
            $updateStmt->bind_param($types, ...$params);
            
            if (!$updateStmt->execute()) {
                throw new BusinessLogicException("Failed to update workflow: " . $updateStmt->error);
            }
            
            $updateStmt->close();
            
            // Record history
            $this->recordHistory($kegiatanId, $newStatus, $currentPosition, $nextPosition);
            
            $this->db->commit();
            return true;
            
        } catch (\Throwable $e) {
            $this->db->rollback();
            error_log("WorkflowService::moveToNextPosition Error: " . $e->getMessage());
            throw new BusinessLogicException("Workflow transition failed: " . $e->getMessage());
        }
    }
    
    /**
     * Reject kegiatan and send back to Previous Role (Rollback) or Admin
     * 
     * @param int $kegiatanId
     * @param int $currentPosition
     * @param string $reason Rejection reason
     * @param int|null $targetPosition Optional explicit target position
     * @return bool
     */
    public function reject(int $kegiatanId, int $currentPosition, string $reason, ?int $targetPosition = null): bool
    {
        $this->db->begin_transaction();
        
        try {
            // Determine target position: Explicit -> Previous Step -> Admin
            // UNTUK REJECT: Posisi tetap di current (tidak rollback), status jadi DITOLAK
            $backToPosition = $currentPosition; // TETAP DI POSISI VERIFIKATOR
            
            $sql = "UPDATE tbl_kegiatan 
                   SET posisiId = ?, statusUtamaId = ? 
                   WHERE kegiatanId = ?";
            
            $stmt = $this->db->prepare($sql);
            $statusDitolak = self::STATUS_DITOLAK;
            
            // FIX: Gunakan variabel yang benar, bukan hardcoded!
            $stmt->bind_param('iii', $backToPosition, $statusDitolak, $kegiatanId);
            
            if (!$stmt->execute()) {
                throw new BusinessLogicException("Failed to reject: " . $stmt->error);
            }
            
            error_log("WORKFLOW REJECT - Updated kegiatanId=$kegiatanId to posisi=$backToPosition, status=$statusDitolak");
            
            $stmt->close();
            
            // Record rejection in history
            $this->recordHistory($kegiatanId, $statusDitolak, $currentPosition, $backToPosition, $reason);
            
            // Send notification to Admin (Owner) - Logic could be expanded to notify previous role
            $this->notifyRejection($kegiatanId, $currentPosition, $reason);
            
            $this->db->commit();
            return true;
            
        } catch (\Throwable $e) {
            $this->db->rollback();
            error_log("WorkflowService::reject Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Request revision and send back to Admin
     */
    public function requestRevision(int $kegiatanId, int $currentPosition, string $comments): bool
    {
        $this->db->begin_transaction();
        
        try {
            $sql = "UPDATE tbl_kegiatan 
                   SET posisiId = ?, statusUtamaId = ? 
                   WHERE kegiatanId = ?";
            
            $stmt = $this->db->prepare($sql);
            $backToAdmin = self::POSITION_ADMIN;
            $statusRevisi = self::STATUS_REVISI;
            
            $stmt->bind_param('iii', $backToAdmin, $statusRevisi, $kegiatanId);
            
            if (!$stmt->execute()) {
                throw new BusinessLogicException("Failed to request revision: " . $stmt->error);
            }
            
            $stmt->close();
            
            // Record revision request
            $this->recordHistory($kegiatanId, $statusRevisi, $currentPosition, $backToAdmin, $comments);
            
            // Send notification
            $this->notifyRevision($kegiatanId, $currentPosition, $comments);
            
            $this->db->commit();
            return true;
            
        } catch (\Throwable $e) {
            $this->db->rollback();
            error_log("WorkflowService::requestRevision Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Record workflow history in tbl_progress_history
     */
    private function recordHistory(
        int $kegiatanId, 
        int $statusId, 
        int $fromPosition, 
        int $toPosition,
        ?string $notes = null
    ): void {
        $userId = $_SESSION['user_id'] ?? null;
        
        $sql = "INSERT INTO tbl_progress_history 
                (kegiatanId, statusId, changedByUserId, timestamp) 
                VALUES (?, ?, ?, NOW())";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('iii', $kegiatanId, $statusId, $userId);
        $stmt->execute();
        $stmt->close();
    }
    
    /**
     * Send notification about approval/rejection
     */
    private function notifyRejection(int $kegiatanId, int $rejectedBy, string $reason): void
    {
        // Get Admin user IDs for the kegiatan
        $sql = "SELECT userId FROM tbl_kegiatan WHERE kegiatanId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $kegiatanId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row) {
            $roleName = $this->getPositionName($rejectedBy);
            $this->logStatusModel->createNotification(
                $row['userId'],
                'REJECTION', // Corrected: Notification type as string
                "Pengajuan ditolak oleh {$roleName}. Alasan: {$reason}", // Combined message and reason
                $kegiatanId // Corrected: Reference ID
            );
        }
    }
    
    /**
     * Send notification about revision request
     */
    private function notifyRevision(int $kegiatanId, int $requestedBy, string $comments): void
    {
        $sql = "SELECT userId FROM tbl_kegiatan WHERE kegiatanId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $kegiatanId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if ($row) {
            $roleName = $this->getPositionName($requestedBy);
            $this->logStatusModel->createNotification(
                $row['userId'],
                'REVISION', // Corrected: Notification type as string
                "Revisi diminta oleh {$roleName}. Catatan: {$comments}", // Combined message and comments
                $kegiatanId // Corrected: Reference ID
            );
        }
    }
    
    /**
     * Get workflow progress percentage
     */
    public function getProgress(array $kegiatan): int
    {
        $position = (int)($kegiatan['posisiId'] ?? 1);
        $status = (int)($kegiatan['statusUtamaId'] ?? 1);
        
        // Rejected or needs revision
        if ($status === self::STATUS_DITOLAK || $status === self::STATUS_REVISI) {
            return 0;
        }
        
        // Calculate based on position (5 positions total)
        $progressMap = [
            self::POSITION_ADMIN => 20,
            self::POSITION_VERIFIKATOR => 40,
            self::POSITION_PPK => 60,
            self::POSITION_WADIR => 80,
            self::POSITION_BENDAHARA => 100
        ];
        
        return $progressMap[$position] ?? 0;
    }
}
