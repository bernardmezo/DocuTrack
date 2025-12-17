<?php

namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Services\AiSecurityService;
use App\Services\AiLogService;
use Exception;
use PDO;

class AiMonitoringController extends Controller
{
    private $aiSecurityService;
    private $aiLogService;

    public function __construct()
    {
        parent::__construct();
        $this->aiSecurityService = new AiSecurityService();
        $this->aiLogService = new AiLogService();
    }

    public function index()
    {
        $data = [
            'alerts' => [],
            'summary' => null,
            'error_message' => null,
            'security_mode' => 'off' // Default
        ];

        try {
            // Check if tables exist before querying to avoid crashes if migration wasn't run
            $stmt = $this->db->query("SHOW TABLES LIKE 'ai_security_alerts'");
            if ($stmt->num_rows > 0) {
                // Fetch Mode
                $data['security_mode'] = $this->aiSecurityService->getMode();

                // Fetch latest 10 alerts
                $query = "SELECT * FROM ai_security_alerts ORDER BY created_at DESC LIMIT 10";
                $result = $this->db->query($query);
                
                $alerts = [];
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        $alerts[] = $row;
                    }
                }
                $data['alerts'] = $alerts;

                // Fetch latest summary
                $querySum = "SELECT * FROM ai_log_summaries ORDER BY created_at DESC LIMIT 1";
                $resultSum = $this->db->query($querySum);
                if ($resultSum && $resultSum->num_rows > 0) {
                    $data['summary'] = $resultSum->fetch_assoc();
                }
            } else {
                $data['error_message'] = "AI Tables not found. Please run the migration.";
            }

        } catch (Exception $e) {
            $data['error_message'] = "Error loading AI Dashboard: " . $e->getMessage();
            error_log($e->getMessage());
        }

        $this->view('pages/superadmin/ai_monitoring', $data, 'superadmin');
    }

    public function toggleMode()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mode = $_POST['mode'] ?? 'silent';
            if ($this->aiSecurityService->setMode($mode)) {
                $this->setFlashMessage('success', "Security Engine set to: " . strtoupper($mode));
            } else {
                $this->setFlashMessage('error', "Failed to update security mode.");
            }
        }
        $this->redirect('/docutrack/public/superadmin/ai-monitoring');
    }

    public function triggerScan()
    {
        try {
            // 1. Analyze Logs
            $logText = $this->aiLogService->analyzeLogs(50);
            
            // 2. Generate AI Summary
            $summary = $this->aiLogService->generateSummary($logText);
            
            // 3. Save to Database
            // Check if table exists first
            $stmt = $this->db->query("SHOW TABLES LIKE 'ai_log_summaries'");
            if ($stmt->num_rows > 0) {
                $stmt = $this->db->prepare("INSERT INTO ai_log_summaries (summary_text, error_count) VALUES (?, ?)");
                
                // Simple error counting (occurrence of 'error' or 'exception')
                $errorCount = substr_count(strtolower($logText), 'error') + substr_count(strtolower($logText), 'exception');
                
                $stmt->bind_param("si", $summary, $errorCount);
                $stmt->execute();
                
                $this->setFlashMessage('success', 'AI Scan completed successfully.');
            } else {
                $this->setFlashMessage('error', 'AI Tables missing.');
            }

        } catch (Exception $e) {
            $this->setFlashMessage('error', 'Scan failed: ' . $e->getMessage());
            error_log("AI Scan Error: " . $e->getMessage());
        }

        $this->redirect('/docutrack/public/superadmin/ai-monitoring');
    }
}
