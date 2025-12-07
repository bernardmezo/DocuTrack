<?php

namespace App\Models;

use App\Core\Model;

class LogStatusModel extends Model
{
    /**
     * Get unread notifications for a user.
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUnreadNotifications(int $userId, int $limit = 10): array
    {
        $sql = "SELECT * FROM tbl_log_status 
                WHERE user_id = ? AND tipe_log LIKE 'NOTIFIKASI_%'
                ORDER BY created_at DESC
                LIMIT ?";
        return $this->db->fetchAll($sql, [$userId, $limit]);
    }

    /**
     * Get the count of unread notifications for a user.
     * @param int $userId
     * @return int
     */
    public function getUnreadNotificationCount(int $userId): int
    {
        $sql = "SELECT COUNT(id) as count FROM tbl_log_status 
                WHERE user_id = ? AND tipe_log LIKE 'NOTIFIKASI_%' AND status = 'BELUM_DIBACA'";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'] ?? 0;
    }

    /**
     * Create a new log status entry.
     * @param array $data
     * @return int|false The ID of the new record, or false on failure.
     */
    public function create(array $data)
    {
        return $this->db->insert('tbl_log_status', $data);
    }

    /**
     * Mark a specific notification as read.
     * @param int $logId
     * @param int $userId
     * @return bool
     */
    public function markAsRead(int $logId, int $userId): bool
    {
        $data = ['status' => 'DIBACA'];
        $where = ['id' => $logId, 'user_id' => $userId]; // Ensure user can only mark their own
        return $this->db->update('tbl_log_status', $data, $where);
    }

    /**
     * Mark all notifications for a user as read.
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        $data = ['status' => 'DIBACA'];
        $where = ['user_id' => $userId, 'status' => 'BELUM_DIBACA'];
        return $this->db->update('tbl_log_status', $data, $where);
    }

    /**
     * Get User Info for Email
     * @param int $userId
     * @return array|false
     */
    public function getUserInfo(int $userId)
    {
        $sql = "SELECT nama, email FROM tbl_user WHERE userId = ?";
        return $this->db->fetch($sql, [$userId]);
    }

    /**
     * Get Kegiatan Info for Email
     * @param int $kegiatanId
     * @return array|false
     */
    public function getKegiatanInfo(int $kegiatanId)
    {
        $sql = "SELECT namaKegiatan, pemilikKegiatan, createdAt FROM tbl_kegiatan WHERE kegiatanId = ?";
        return $this->db->fetch($sql, [$kegiatanId]);
    }
}