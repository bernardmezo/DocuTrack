<?php

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Services\LogStatusService;
use Exception;

class NotifikasiController extends Controller
{
    private $logStatusService;

    public function __construct($db)
    {
        parent::__construct($db);
        $this->logStatusService = new LogStatusService($this->db);
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Helper to send JSON response.
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * Get notifications for the logged-in user.
     */
    public function get()
    {
        try {
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId === 0) {
                $this->jsonResponse(['error' => 'Unauthorized'], 401);
            }

            $notifications = $this->logStatusService->getNotificationsForUser($userId);
            $this->jsonResponse(['success' => true, 'data' => $notifications]);
        } catch (Exception $e) {
            error_log("API Notifikasi Error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id)
    {
        try {
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId === 0) {
                $this->jsonResponse(['error' => 'Unauthorized'], 401);
            }

            $success = $this->logStatusService->markNotificationAsRead((int)$id, $userId);
            $this->jsonResponse(['success' => $success]);
        } catch (Exception $e) {
            error_log("API Notifikasi Error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }

    /**
     * Mark all notifications as read for the logged-in user.
     */
    public function markAllAsRead()
    {
        try {
            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId === 0) {
                $this->jsonResponse(['error' => 'Unauthorized'], 401);
            }

            $success = $this->logStatusService->markAllAsRead($userId);
            $this->jsonResponse(['success' => $success]);
        } catch (Exception $e) {
            error_log("API Notifikasi Error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Internal Server Error'], 500);
        }
    }
}
