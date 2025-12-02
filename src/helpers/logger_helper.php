<?php
/**
 * Logger Helper Functions - DocuTrack
 * =====================================
 * File ini berisi fungsi-fungsi untuk audit logging ke database.
 * Semua aktivitas penting user akan dicatat untuk akuntabilitas.
 * 
 * Ref: DATABASE_AUDIT.md - Pilar 3: Auditability
 *      ANALYSIS_REPORT.md - Poin 3.C: Centralized Audit Log
 * Date: December 2, 2025
 */

/**
 * Write log ke tabel tbl_activity_logs
 * 
 * @param int $userId ID user yang melakukan aksi (0 jika anonymous/system)
 * @param string $action Kode aksi (LOGIN_SUCCESS, PPK_APPROVE, PENCAIRAN_SUCCESS, dll)
 * @param string $description Deskripsi aksi dalam bahasa manusia
 * @param string|null $entityType Tipe entity yang dimodifikasi (kegiatan, user, lpj, dll)
 * @param int|null $entityId ID entity yang dimodifikasi
 * @param array|null $oldValue Nilai sebelum perubahan (untuk audit trail)
 * @param array|null $newValue Nilai setelah perubahan
 * @return bool Success status
 * 
 * @example
 * // Login berhasil
 * writeLog($userId, 'LOGIN_SUCCESS', 'User berhasil login');
 * 
 * // Approval kegiatan
 * writeLog($userId, 'PPK_APPROVE', 'Menyetujui pengajuan kegiatan: Lomba X', 'kegiatan', 123, 
 *     ['statusUtamaId' => 1], ['statusUtamaId' => 3]);
 * 
 * // Pencairan dana
 * writeLog($userId, 'PENCAIRAN_SUCCESS', 'Pencairan Rp 5.000.000 untuk kegiatan ID 45', 
 *     'kegiatan', 45, null, ['jumlahDicairkan' => 5000000]);
 */
function writeLog(
    int $userId,
    string $action,
    string $description,
    ?string $entityType = null,
    ?int $entityId = null,
    ?array $oldValue = null,
    ?array $newValue = null
): bool {
    // Dapatkan koneksi database
    $conn = getLogDbConnection();
    
    if (!$conn) {
        // Fallback ke error_log jika DB tidak tersedia
        error_log("[AUDIT] [$action] User:$userId - $description");
        return false;
    }
    
    try {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO tbl_activity_logs 
            (userId, action, entityType, entityId, description, oldValue, newValue, ipAddress, userAgent, createdAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        if (!$stmt) {
            error_log("[AUDIT ERROR] Failed to prepare statement: " . mysqli_error($conn));
            return false;
        }
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : 'Unknown';
        $oldValueJson = $oldValue ? json_encode($oldValue) : null;
        $newValueJson = $newValue ? json_encode($newValue) : null;
        
        mysqli_stmt_bind_param($stmt, 'issssssss',
            $userId,
            $action,
            $entityType,
            $entityId,
            $description,
            $oldValueJson,
            $newValueJson,
            $ipAddress,
            $userAgent
        );
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
        
    } catch (Exception $e) {
        // Jangan throw error untuk logging - hanya catat ke file sebagai fallback
        error_log("[AUDIT ERROR] Failed to write log: " . $e->getMessage());
        return false;
    }
}

/**
 * Shortcut untuk log aksi approval/rejection
 * 
 * @param int $userId User yang melakukan approval
 * @param int $kegiatanId ID kegiatan yang di-approve/reject
 * @param string $role Role user (PPK, WADIR, VERIFIKATOR, dll)
 * @param bool $isApprove True jika approve, false jika reject
 * @param string $keterangan Keterangan/catatan tambahan
 * @param int|null $oldStatusId Status lama
 * @param int|null $newStatusId Status baru
 * @return bool Success status
 */
function logApproval(
    int $userId, 
    int $kegiatanId, 
    string $role, 
    bool $isApprove,
    string $keterangan = '',
    ?int $oldStatusId = null,
    ?int $newStatusId = null
): bool {
    $action = strtoupper($role) . ($isApprove ? '_APPROVE' : '_REJECT');
    $actionText = $isApprove ? 'menyetujui' : 'menolak';
    
    $description = ucfirst($role) . " $actionText kegiatan ID: $kegiatanId";
    if (!empty($keterangan)) {
        $description .= ". Catatan: $keterangan";
    }
    
    $oldValue = $oldStatusId !== null ? ['statusUtamaId' => $oldStatusId] : null;
    $newValue = $newStatusId !== null ? ['statusUtamaId' => $newStatusId] : null;
    
    return writeLog(
        $userId,
        $action,
        $description,
        'kegiatan',
        $kegiatanId,
        $oldValue,
        $newValue
    );
}

/**
 * Log pencairan dana
 * 
 * @param int $userId User bendahara yang memproses
 * @param int $kegiatanId ID kegiatan
 * @param float $jumlahDicairkan Jumlah dana yang dicairkan
 * @param string $metodePencairan Metode pencairan (uang_muka, pelunasan, dll)
 * @param string|null $catatan Catatan tambahan
 * @return bool Success status
 */
function logPencairan(
    int $userId,
    int $kegiatanId,
    float $jumlahDicairkan,
    string $metodePencairan,
    ?string $catatan = null
): bool {
    $jumlahFormatted = 'Rp ' . number_format($jumlahDicairkan, 0, ',', '.');
    
    $description = "Pencairan dana $jumlahFormatted untuk kegiatan ID: $kegiatanId. Metode: $metodePencairan";
    if (!empty($catatan)) {
        $description .= ". Catatan: $catatan";
    }
    
    return writeLog(
        $userId,
        'PENCAIRAN_SUCCESS',
        $description,
        'kegiatan',
        $kegiatanId,
        null,
        [
            'jumlahDicairkan' => $jumlahDicairkan,
            'metodePencairan' => $metodePencairan
        ]
    );
}

/**
 * Log login attempt
 * 
 * @param int $userId User ID (0 jika login gagal)
 * @param string $email Email yang digunakan login
 * @param bool $success True jika login berhasil
 * @param string|null $reason Alasan jika login gagal
 * @return bool Success status
 */
function logLogin(int $userId, string $email, bool $success, ?string $reason = null): bool
{
    $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
    $description = $success 
        ? "User berhasil login dengan email: $email"
        : "Login gagal untuk email: $email" . ($reason ? ". Alasan: $reason" : "");
    
    return writeLog($userId, $action, $description, 'user', $userId > 0 ? $userId : null);
}

/**
 * Log logout
 * 
 * @param int $userId User ID
 * @return bool Success status
 */
function logLogout(int $userId): bool
{
    return writeLog($userId, 'LOGOUT', 'User melakukan logout', 'user', $userId);
}

/**
 * Log security violation (path traversal, SQL injection attempt, etc)
 * 
 * @param int $userId User ID (0 jika anonymous)
 * @param string $violationType Tipe pelanggaran
 * @param string $details Detail pelanggaran
 * @return bool Success status
 */
function logSecurityViolation(int $userId, string $violationType, string $details): bool
{
    return writeLog(
        $userId,
        'SECURITY_VIOLATION',
        "[$violationType] $details",
        null,
        null
    );
}

/**
 * Get activity logs dengan filter
 * 
 * @param array $filters Filter options (userId, action, dateFrom, dateTo, entityType)
 * @param int $limit Limit hasil
 * @param int $offset Offset untuk pagination
 * @return array Array of log entries
 */
function getActivityLogs(array $filters = [], int $limit = 100, int $offset = 0): array
{
    $conn = getLogDbConnection();
    if (!$conn) {
        return [];
    }
    
    $sql = "
        SELECT 
            l.*,
            u.nama as userName
        FROM tbl_activity_logs l
        LEFT JOIN tbl_users u ON l.userId = u.userId
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    if (!empty($filters['userId'])) {
        $sql .= " AND l.userId = ?";
        $params[] = $filters['userId'];
        $types .= 'i';
    }
    
    if (!empty($filters['action'])) {
        $sql .= " AND l.action = ?";
        $params[] = $filters['action'];
        $types .= 's';
    }
    
    if (!empty($filters['entityType'])) {
        $sql .= " AND l.entityType = ?";
        $params[] = $filters['entityType'];
        $types .= 's';
    }
    
    if (!empty($filters['dateFrom'])) {
        $sql .= " AND DATE(l.createdAt) >= ?";
        $params[] = $filters['dateFrom'];
        $types .= 's';
    }
    
    if (!empty($filters['dateTo'])) {
        $sql .= " AND DATE(l.createdAt) <= ?";
        $params[] = $filters['dateTo'];
        $types .= 's';
    }
    
    $sql .= " ORDER BY l.createdAt DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $logs = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Decode JSON fields
        if ($row['oldValue']) {
            $row['oldValue'] = json_decode($row['oldValue'], true);
        }
        if ($row['newValue']) {
            $row['newValue'] = json_decode($row['newValue'], true);
        }
        $logs[] = $row;
    }
    
    mysqli_stmt_close($stmt);
    
    return $logs;
}

/**
 * Helper internal: Dapatkan koneksi database untuk logging
 * Menggunakan koneksi yang sudah ada atau membuat baru
 * 
 * @return mysqli|null
 */
function getLogDbConnection()
{
    global $conn;
    
    // Gunakan koneksi global jika sudah ada
    if (isset($conn) && $conn instanceof mysqli && mysqli_ping($conn)) {
        return $conn;
    }
    
    // Coba load dari file conn.php
    $connFile = __DIR__ . '/../model/conn.php';
    if (file_exists($connFile)) {
        require_once $connFile;
        if (isset($conn) && $conn instanceof mysqli) {
            return $conn;
        }
    }
    
    return null;
}
