<?php

/**
 * Integration Example - WorkflowService Usage
 * 
 * This file demonstrates how to use the new WorkflowService
 * with existing models and services.
 */

// Example 1: Verifikator Approval
function exampleVerifikatorApproval($db, $kegiatanId, $kodeMak)
{
    $workflowService = new \App\Services\WorkflowService($db);
    $logService = new \App\Services\LogStatusService($db);
    
    try {
        // Move kegiatan from Verifikator to Admin (for rincian completion)
        $success = $workflowService->moveToNextPosition(
            $kegiatanId,
            \App\Services\WorkflowService::POSITION_VERIFIKATOR,
            \App\Services\WorkflowService::STATUS_DISETUJUI,
            $kodeMak
        );
        
        if ($success) {
            // Get kegiatan data for notification
            $sql = "SELECT userId FROM tbl_kegiatan WHERE kegiatanId = ?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('i', $kegiatanId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row) {
                $logService->createNotification(
                    $row['userId'],
                    $kegiatanId,
                    'Usulan Disetujui Verifikator',
                    'Silakan lengkapi rincian kegiatan Anda'
                );
            }
        }
        
        return $success;
        
    } catch (\Exception $e) {
        error_log("Approval Error: " . $e->getMessage());
        return false;
    }
}

// Example 2: PPK Approval (Move to Wadir)
function examplePpkApproval($db, $kegiatanId)
{
    $workflowService = new \App\Services\WorkflowService($db);
    
    return $workflowService->moveToNextPosition(
        $kegiatanId,
        \App\Services\WorkflowService::POSITION_PPK,
        \App\Services\WorkflowService::STATUS_DISETUJUI
    );
}

// Example 3: Reject at any position
function exampleReject($db, $kegiatanId, $currentPosition, $reason)
{
    $workflowService = new \App\Services\WorkflowService($db);
    
    // Reject and send back to Admin
    return $workflowService->reject(
        $kegiatanId,
        $currentPosition,
        $reason
    );
}

// Example 4: Request Revision
function exampleRequestRevision($db, $kegiatanId, $currentPosition, $comments)
{
    $workflowService = new \App\Services\WorkflowService($db);
    
    return $workflowService->requestRevision(
        $kegiatanId,
        $currentPosition,
        $comments
    );
}

// Example 5: Check workflow status
function exampleCheckStatus($db, $kegiatanId)
{
    $workflowService = new \App\Services\WorkflowService($db);
    
    // Get kegiatan data
    $sql = "SELECT * FROM tbl_kegiatan WHERE kegiatanId = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param('i', $kegiatanId);
    $stmt->execute();
    $result = $stmt->get_result();
    $kegiatan = $result->fetch_assoc();
    
    if ($kegiatan) {
        $status = [
            'is_approved' => $workflowService->isApproved($kegiatan),
            'is_pending' => $workflowService->isPending($kegiatan),
            'is_rejected' => $workflowService->isRejected($kegiatan),
            'needs_revision' => $workflowService->needsRevision($kegiatan),
            'progress_percentage' => $workflowService->getProgress($kegiatan),
            'current_position' => $workflowService->getPositionName((int)$kegiatan['posisiId'])
        ];
        
        return $status;
    }
    
    return null;
}

// Example 6: Using in Service Layer
class ExampleKegiatanService
{
    private $db;
    private $workflowService;
    
    public function __construct($db)
    {
        $this->db = $db;
        $this->workflowService = new \App\Services\WorkflowService($db);
    }
    
    public function submitToNextApprover($kegiatanId, $currentPosition)
    {
        // Validate kegiatan is ready
        $kegiatan = $this->getKegiatan($kegiatanId);
        
        if (!$this->validateReadyForSubmission($kegiatan)) {
            throw new \Exception("Kegiatan belum siap untuk diajukan");
        }
        
        // Move to next position
        return $this->workflowService->moveToNextPosition(
            $kegiatanId,
            $currentPosition,
            \App\Services\WorkflowService::STATUS_MENUNGGU
        );
    }
    
    public function getKegiatanWithWorkflowInfo($kegiatanId)
    {
        $kegiatan = $this->getKegiatan($kegiatanId);
        
        if ($kegiatan) {
            // Enrich with workflow information
            $kegiatan['workflow_info'] = [
                'position_name' => $this->workflowService->getPositionName((int)$kegiatan['posisiId']),
                'can_approve' => $this->workflowService->canApprove((int)$kegiatan['posisiId']),
                'is_approved' => $this->workflowService->isApproved($kegiatan),
                'progress' => $this->workflowService->getProgress($kegiatan)
            ];
        }
        
        return $kegiatan;
    }
    
    private function getKegiatan($id)
    {
        $sql = "SELECT * FROM tbl_kegiatan WHERE kegiatanId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
    private function validateReadyForSubmission($kegiatan)
    {
        // Add validation logic here
        return !empty($kegiatan['namaKegiatan']);
    }
}

// Example 7: Using in Controller
class ExampleController extends \App\Core\Controller
{
    private $workflowService;
    private $kegiatanService;
    
    public function __construct()
    {
        parent::__construct();
        $this->workflowService = new \App\Services\WorkflowService($this->db);
        $this->kegiatanService = new \App\Services\KegiatanService($this->db);
    }
    
    public function show($id)
    {
        $kegiatan = $this->kegiatanService->getDetailLengkap((int)$id);
        
        if (!$kegiatan) {
            $_SESSION['flash_error'] = 'Kegiatan tidak ditemukan';
            return redirect('/dashboard');
        }
        
        $data = [
            'kegiatan' => $kegiatan,
            'workflow' => $this->workflowService,
            'workflow_progress' => $this->workflowService->getProgress($kegiatan),
            'position_name' => $this->workflowService->getPositionName((int)$kegiatan['posisiId']),
            'can_approve' => $this->workflowService->canApprove((int)$kegiatan['posisiId'])
        ];
        
        $this->view('kegiatan/detail', $data);
    }
    
    public function approve($id)
    {
        // Get current user's position
        $userPosition = $_SESSION['user_role_id'] ?? 0;
        
        try {
            $success = $this->workflowService->moveToNextPosition(
                (int)$id,
                $userPosition,
                \App\Services\WorkflowService::STATUS_DISETUJUI
            );
            
            if ($success) {
                $_SESSION['flash_success'] = 'Kegiatan berhasil disetujui';
            } else {
                $_SESSION['flash_error'] = 'Gagal menyetujui kegiatan';
            }
            
        } catch (\Exception $e) {
            error_log("Approval error: " . $e->getMessage());
            $_SESSION['flash_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }
        
        return redirect('/dashboard');
    }
}

// Example 8: Using BaseModel
class ExampleKegiatanModel extends \App\Models\Base\BaseModel
{
    protected string $table = 'tbl_kegiatan';
    protected string $primaryKey = 'kegiatanId';
    
    protected array $fillable = [
        'namaKegiatan',
        'userId',
        'statusUtamaId',
        'posisiId',
        'jurusanPenyelenggara',
        'wadirTujuan'
    ];
    
    // Custom methods beyond CRUD
    public function getByWorkflowPosition(int $positionId, int $statusId = null): array
    {
        $where = ['posisiId' => $positionId];
        
        if ($statusId !== null) {
            $where['statusUtamaId'] = $statusId;
        }
        
        return $this->all($where, 'createdAt DESC');
    }
    
    public function countByStatus(int $statusId): int
    {
        return $this->count(['statusUtamaId' => $statusId]);
    }
}
