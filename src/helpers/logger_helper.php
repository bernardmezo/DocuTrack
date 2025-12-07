<?php

/**
 * Logger Helper Functions - DocuTrack
 * =====================================
 * File ini berisi fungsi-fungsi untuk audit logging ke database.
 * Semua aktivitas penting user akan dicatat untuk akuntabilitas.
 *
 * OPTIMIZED VERSION:
 * - tbl_log_actions merged into tbl_activity_logs
 * - Category now stored directly in logs table as ENUM
 * - Professional category names: authentication, workflow, document, financial, user_management, security
 *
 * Date: December 2, 2025
 */

/**
 * Log Categories (matches ENUM in tbl_activity_logs)
 */
define('LOG_CATEGORY_AUTHENTICATION', 'authentication');
define('LOG_CATEGORY_WORKFLOW', 'workflow');
define('LOG_CATEGORY_DOCUMENT', 'document');
define('LOG_CATEGORY_FINANCIAL', 'financial');
define('LOG_CATEGORY_USER_MANAGEMENT', 'user_management');
define('LOG_CATEGORY_SECURITY', 'security');

/**
 * Predefined action codes with categories
 * Category values must match ENUM in database
 */
define('LOG_ACTIONS', [
    // AUTHENTICATION - Login, logout, session management
    'LOGIN_SUCCESS' => ['name' => 'Login Berhasil', 'category' => LOG_CATEGORY_AUTHENTICATION],
    'LOGIN_FAILED' => ['name' => 'Login Gagal', 'category' => LOG_CATEGORY_AUTHENTICATION],
    'LOGOUT' => ['name' => 'Logout', 'category' => LOG_CATEGORY_AUTHENTICATION],
    'SESSION_EXPIRED' => ['name' => 'Sesi Kadaluarsa', 'category' => LOG_CATEGORY_AUTHENTICATION],
    'PASSWORD_CHANGE' => ['name' => 'Ubah Password', 'category' => LOG_CATEGORY_AUTHENTICATION],

    // WORKFLOW - Approval chain actions
    'VERIFIKATOR_APPROVE' => ['name' => 'Verifikator Menyetujui', 'category' => LOG_CATEGORY_WORKFLOW],
    'VERIFIKATOR_REJECT' => ['name' => 'Verifikator Menolak', 'category' => LOG_CATEGORY_WORKFLOW],
    'VERIFIKATOR_REVISE' => ['name' => 'Verifikator Minta Revisi', 'category' => LOG_CATEGORY_WORKFLOW],
    'PPK_APPROVE' => ['name' => 'PPK Menyetujui', 'category' => LOG_CATEGORY_WORKFLOW],
    'PPK_REJECT' => ['name' => 'PPK Menolak', 'category' => LOG_CATEGORY_WORKFLOW],
    'PPK_REVISE' => ['name' => 'PPK Minta Revisi', 'category' => LOG_CATEGORY_WORKFLOW],
    'WADIR_APPROVE' => ['name' => 'Wadir Menyetujui', 'category' => LOG_CATEGORY_WORKFLOW],
    'WADIR_REJECT' => ['name' => 'Wadir Menolak', 'category' => LOG_CATEGORY_WORKFLOW],
    'WADIR_REVISE' => ['name' => 'Wadir Minta Revisi', 'category' => LOG_CATEGORY_WORKFLOW],
    'STATUS_CHANGE' => ['name' => 'Perubahan Status', 'category' => LOG_CATEGORY_WORKFLOW],
    'WORKFLOW_FORWARD' => ['name' => 'Lanjut ke Tahap Berikutnya', 'category' => LOG_CATEGORY_WORKFLOW],
    'WORKFLOW_RETURN' => ['name' => 'Dikembalikan ke Tahap Sebelumnya', 'category' => LOG_CATEGORY_WORKFLOW],

    // DOCUMENT - CRUD kegiatan, KAK, RAB, files
    'CREATE_KEGIATAN' => ['name' => 'Membuat Pengajuan Kegiatan', 'category' => LOG_CATEGORY_DOCUMENT],
    'UPDATE_KEGIATAN' => ['name' => 'Mengupdate Pengajuan Kegiatan', 'category' => LOG_CATEGORY_DOCUMENT],
    'DELETE_KEGIATAN' => ['name' => 'Menghapus Pengajuan Kegiatan', 'category' => LOG_CATEGORY_DOCUMENT],
    'CREATE_KAK' => ['name' => 'Membuat KAK', 'category' => LOG_CATEGORY_DOCUMENT],
    'UPDATE_KAK' => ['name' => 'Mengupdate KAK', 'category' => LOG_CATEGORY_DOCUMENT],
    'CREATE_RAB' => ['name' => 'Membuat RAB', 'category' => LOG_CATEGORY_DOCUMENT],
    'UPDATE_RAB' => ['name' => 'Mengupdate RAB', 'category' => LOG_CATEGORY_DOCUMENT],
    'UPLOAD_DOCUMENT' => ['name' => 'Upload Dokumen', 'category' => LOG_CATEGORY_DOCUMENT],
    'DELETE_DOCUMENT' => ['name' => 'Hapus Dokumen', 'category' => LOG_CATEGORY_DOCUMENT],
    'SUBMIT_RINCIAN' => ['name' => 'Submit Rincian Kegiatan', 'category' => LOG_CATEGORY_DOCUMENT],

    // FINANCIAL - Pencairan, LPJ, transaksi keuangan
    'PENCAIRAN_PROCESS' => ['name' => 'Proses Pencairan Dana', 'category' => LOG_CATEGORY_FINANCIAL],
    'PENCAIRAN_SUCCESS' => ['name' => 'Pencairan Dana Berhasil', 'category' => LOG_CATEGORY_FINANCIAL],
    'PENCAIRAN_REJECT' => ['name' => 'Pencairan Dana Ditolak', 'category' => LOG_CATEGORY_FINANCIAL],
    'LPJ_SUBMIT' => ['name' => 'Submit LPJ', 'category' => LOG_CATEGORY_FINANCIAL],
    'LPJ_APPROVE' => ['name' => 'LPJ Disetujui', 'category' => LOG_CATEGORY_FINANCIAL],
    'LPJ_REJECT' => ['name' => 'LPJ Ditolak', 'category' => LOG_CATEGORY_FINANCIAL],
    'LPJ_REVISE' => ['name' => 'LPJ Perlu Revisi', 'category' => LOG_CATEGORY_FINANCIAL],
    'BUDGET_UPDATE' => ['name' => 'Update Anggaran', 'category' => LOG_CATEGORY_FINANCIAL],
    'REALISASI_UPDATE' => ['name' => 'Update Realisasi', 'category' => LOG_CATEGORY_FINANCIAL],

    // USER_MANAGEMENT - CRUD user, role changes
    'USER_CREATE' => ['name' => 'Membuat User Baru', 'category' => LOG_CATEGORY_USER_MANAGEMENT],
    'USER_UPDATE' => ['name' => 'Mengupdate User', 'category' => LOG_CATEGORY_USER_MANAGEMENT],
    'USER_DELETE' => ['name' => 'Menghapus User', 'category' => LOG_CATEGORY_USER_MANAGEMENT],
    'USER_RESET_PASSWORD' => ['name' => 'Reset Password User', 'category' => LOG_CATEGORY_USER_MANAGEMENT],
    'USER_ROLE_CHANGE' => ['name' => 'Ubah Role User', 'category' => LOG_CATEGORY_USER_MANAGEMENT],
    'USER_ACTIVATE' => ['name' => 'Aktivasi User', 'category' => LOG_CATEGORY_USER_MANAGEMENT],
    'USER_DEACTIVATE' => ['name' => 'Nonaktifkan User', 'category' => LOG_CATEGORY_USER_MANAGEMENT],

    // SECURITY - Violations, unauthorized access
    'SECURITY_VIOLATION' => ['name' => 'Pelanggaran Keamanan Terdeteksi', 'category' => LOG_CATEGORY_SECURITY],
    'PATH_TRAVERSAL_ATTEMPT' => ['name' => 'Percobaan Path Traversal', 'category' => LOG_CATEGORY_SECURITY],
    'SQL_INJECTION_ATTEMPT' => ['name' => 'Percobaan SQL Injection', 'category' => LOG_CATEGORY_SECURITY],
    'UNAUTHORIZED_ACCESS' => ['name' => 'Akses Tidak Sah', 'category' => LOG_CATEGORY_SECURITY],
    'INVALID_TOKEN' => ['name' => 'Token Tidak Valid', 'category' => LOG_CATEGORY_SECURITY],
    'BRUTE_FORCE_DETECTED' => ['name' => 'Brute Force Terdeteksi', 'category' => LOG_CATEGORY_SECURITY],
]);

/**
 * Get category for an action code
 *
 * @param string $action Action code
 * @return string Category (defaults to 'workflow' if not found)
 */
function getActionCategory(string $action): string
{
    return LOG_ACTIONS[$action]['category'] ?? LOG_CATEGORY_WORKFLOW;
}

/**
 * Validate action code against predefined list
 *
 * @param string $action Action code to validate
 * @return bool True if valid
 */
function isValidActionCode(string $action): bool
{
    return array_key_exists($action, LOG_ACTIONS);
}

/**
 * Get action info from predefined list
 *
 * @param string $action Action code
 * @return array|null Action info or null if not found
 */
function getActionInfo(string $action): ?array
{
    return LOG_ACTIONS[$action] ?? null;
}

/**
 * Write log ke tabel tbl_activity_logs
 * OPTIMIZED: Now includes category column
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
    // Get category from action code
    $category = getActionCategory($action);

    // Validate action code
    if (!isValidActionCode($action)) {
        error_log("[AUDIT WARNING] Unknown action code: $action - logging with default category");
    }

    // Dapatkan koneksi database
    $conn = getLogDbConnection();

    if (!$conn) {
        // Fallback ke error_log jika DB tidak tersedia
        error_log("[AUDIT] [$category][$action] User:$userId - $description");
        return false;
    }

    try {
        // OPTIMIZED: Now includes category column
        $stmt = mysqli_prepare($conn, "
            INSERT INTO tbl_activity_logs 
            (userId, action, category, entityType, entityId, description, oldValue, newValue, ipAddress, userAgent, createdAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        if (!$stmt) {
            error_log("[AUDIT ERROR] Failed to prepare statement: " . mysqli_error($conn));
            return false;
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 500) : 'Unknown';
        $oldValueJson = $oldValue ? json_encode($oldValue) : null;
        $newValueJson = $newValue ? json_encode($newValue) : null;

        mysqli_stmt_bind_param(
            $stmt,
            'isssssssss',
            $userId,
            $action,
            $category,
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
 * OPTIMIZED: Category now stored directly in tbl_activity_logs
 *
 * @param array $filters Filter options (userId, action, category, dateFrom, dateTo, entityType)
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

    // OPTIMIZED: Category now in tbl_activity_logs directly
    $sql = "
        SELECT 
            l.*,
            u.nama as userName
        FROM tbl_activity_logs l
        LEFT JOIN tbl_user u ON l.userId = u.userId
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

    // Filter by category (now directly in logs table)
    if (!empty($filters['category'])) {
        $sql .= " AND l.category = ?";
        $params[] = $filters['category'];
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
        // Add action name from constants
        $actionInfo = getActionInfo($row['action']);
        $row['actionName'] = $actionInfo['name'] ?? $row['action'];
        $logs[] = $row;
    }

    mysqli_stmt_close($stmt);

    return $logs;
}

/**
 * Get log statistics by category
 * OPTIMIZED: Category now stored directly in tbl_activity_logs
 *
 * @param string|null $dateFrom Start date (YYYY-MM-DD)
 * @param string|null $dateTo End date (YYYY-MM-DD)
 * @return array Statistics grouped by category
 */
function getLogStatsByCategory(?string $dateFrom = null, ?string $dateTo = null): array
{
    $conn = getLogDbConnection();
    if (!$conn) {
        return [];
    }

    $sql = "
        SELECT 
            category,
            COUNT(*) as total
        FROM tbl_activity_logs
        WHERE 1=1
    ";

    $params = [];
    $types = '';

    if ($dateFrom) {
        $sql .= " AND DATE(createdAt) >= ?";
        $params[] = $dateFrom;
        $types .= 's';
    }

    if ($dateTo) {
        $sql .= " AND DATE(createdAt) <= ?";
        $params[] = $dateTo;
        $types .= 's';
    }

    $sql .= " GROUP BY category ORDER BY total DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $stats = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $stats[$row['category']] = (int)$row['total'];
    }

    mysqli_stmt_close($stmt);

    return $stats;
}

/**
 * Get available action codes with their names
 * OPTIMIZED: Returns from predefined constants (no separate table)
 *
 * @param string|null $category Filter by category
 * @return array List of action codes
 */
function getAvailableActionCodes(?string $category = null): array
{
    $actions = [];
    foreach (LOG_ACTIONS as $code => $info) {
        if ($category === null || $info['category'] === $category) {
            $actions[] = [
                'actionCode' => $code,
                'actionName' => $info['name'],
                'category' => $info['category']
            ];
        }
    }
    return $actions;
}

/**
 * Helper internal: Dapatkan koneksi database untuk logging
 * Menggunakan koneksi standar aplikasi dari bootstrap.
 *
 * @return mysqli|null
 */
function getLogDbConnection()
{
    // Gunakan fungsi db() standar dari bootstrap untuk konsistensi
    // Pastikan bootstrap.php sudah di-include di awal script yang memanggil logger.
    if (function_exists('db')) {
        return db();
    }

    // Fallback jika dipanggil dari konteks yang tidak biasa
    error_log("[AUDIT WARNING] Cannot find db() connection function.");
    return null;
}
