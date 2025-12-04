<?php
/**
 * UserModel - OOP Implementation with Dependency Injection
 * 
 * Model untuk mengelola tbl_users dan tbl_roles dengan pola OOP dan DI.
 * Menghilangkan global $conn dan menggunakan constructor injection.
 * 
 * @category Model
 * @package  DocuTrack
 * @author   DocuTrack Team
 * @license  MIT License
 * @version  2.0.0
 */

class UserModel
{
    /**
     * @var mysqli Database connection instance
     */
    private $db;

    /**
     * Constructor - Dependency Injection untuk database connection
     *
     * @param mysqli $db Database connection dari Database::getInstance()->getConnection()
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    // ==== ROLES METHODS ====

    /**
     * Mengambil semua data role dari tbl_roles.
     * 
     * Berguna untuk mengisi <select> dropdown saat membuat/mengedit user.
     *
     * @return array Array of role records
     */
    public function getAllRoles(): array
    {
        $query = "SELECT roleId, namaRole FROM tbl_role ORDER BY namaRole ASC";
        $result = mysqli_query($this->db, $query);

        if ($result === false) {
            error_log('UserModel::getAllRoles - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }

        mysqli_free_result($result);
        return $roles;
    }


    // ==== USER METHODS ====

    /**
     * Memverifikasi login user berdasarkan email dan password.
     *
     * @param string $email User email address
     * @param string $password Plain text password to verify
     * @return array|false User data array if successful, false otherwise
     */
    public function verifyUserLogin(string $email, string $password)
    {
        // Ambil user berdasarkan email dan join dengan role
        $query = "SELECT u.*, r.role_name 
                  FROM tbl_users u 
                  JOIN tbl_roles r ON u.role_id = r.role_id
                  WHERE u.email = ? AND u.is_active = 1";
                  
        $stmt = mysqli_prepare($this->db, $query);
        if ($stmt === false) {
            error_log('UserModel::verifyUserLogin - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 's', $email);
        
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            // Jika user ditemukan dan password cocok
            if ($user && password_verify($password, $user['password_hash'])) {
                return $user; // Login berhasil
            } else {
                return false; // Email tidak ditemukan atau password salah
            }
        } else {
            error_log('UserModel::verifyUserLogin - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }


    /**
     * Menambah user baru ke tbl_users.
     * 
     * User akan di-set aktif (is_active = 1) secara default.
     *
     * @param string $userName Username for the new user
     * @param int $roleId Role ID from tbl_roles
     * @param int|null $prodiId Prodi ID (nullable)
     * @param string $email User email address
     * @param string $password Plain text password (will be hashed)
     * @param string $confirmPassword Password confirmation
     * @return bool True if successful, false otherwise
     */
    public function insertUser(
        string $userName,
        int $roleId,
        ?int $prodiId,
        string $email,
        string $password,
        string $confirmPassword
    ): bool {
        try {
            // Validasi password dan konfirmasi password
            if ($password !== $confirmPassword) {
                throw new Exception('Password dan konfirmasi password tidak sesuai.');
            }
        } catch (Exception $e) {
            error_log('UserModel::insertUser - Validation error: ' . $e->getMessage());
            return false;
        }

        // hash password
        $passwordHased = password_hash($password, PASSWORD_BCRYPT);

        // Set is_active ke 1 (aktif) secara default
        $is_active = 1;

        // Siapkan pernyataan SQL - fixed to match tbl_user schema
        $stmt = mysqli_prepare(
            $this->db,
            "INSERT INTO tbl_user (nama, roleId, namaJurusan, email, password) 
             VALUES (?, ?, NULL, ?, ?)"
        );
        if ($stmt === false) {
            error_log('UserModel::insertUser - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        // Bind parameter - fixed column names
        mysqli_stmt_bind_param($stmt, 'siss', $userName, $roleId, $email, $passwordHased);

        // Eksekusi pernyataan
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::insertUser - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengupdate data user (non-password) oleh admin.
     *
     * @param int $user_id User ID to update
     * @param string $userName New username
     * @param int $roleId New role ID
     * @param int|null $prodiId New prodi ID (nullable)
     * @param string $email New email address
     * @param int $is_active Active status (0 or 1)
     * @return bool True if successful, false otherwise
     */
    public function updateUser(
        int $user_id,
        string $userName,
        int $roleId,
        ?int $prodiId,
        string $email,
        int $is_active
    ): bool {
        $query = "UPDATE tbl_user SET nama = ?, roleId = ?, email = ? WHERE userId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::updateUser - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        // Bind parameter - fixed to match schema
        mysqli_stmt_bind_param($stmt, 'sisi', $userName, $roleId, $email, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::updateUser - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengubah password user.
     *
     * @param int $user_id User ID whose password to change
     * @param string $newPassword New plain text password
     * @param string $confirmPassword Password confirmation
     * @return bool True if successful, false otherwise
     */
    public function updateUserPassword(
        int $user_id,
        string $newPassword,
        string $confirmPassword
    ): bool {
        if ($newPassword !== $confirmPassword) {
            error_log('UserModel::updateUserPassword - Passwords do not match.');
            return false;
        }

        $passwordHased = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $query = "UPDATE tbl_user SET password = ? WHERE userId = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::updateUserPassword - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'si', $passwordHased, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::updateUserPassword - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    /**
     * Mengambil semua data user dengan nama role-nya.
     *
     * @return array Array of user records with role information
     */
    public function getAllUsers(): array
    {
        // Menggunakan user_id dan role_name sesuai skema
        $query = "SELECT u.user_id, u.username, u.email, u.prodi_id, u.is_active, r.role_name, u.created_at 
                  FROM tbl_users u
                  JOIN tbl_roles r ON u.role_id = r.role_id
                  ORDER BY u.created_at DESC";

        $result = mysqli_query($this->db, $query);
        if ($result === false) {
            error_log('UserModel::getAllUsers - Query failed: ' . mysqli_error($this->db));
            return [];
        }

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        mysqli_free_result($result);
        return $users;
    }

    /**
     * Mengambil satu data user spesifik berdasarkan ID.
     *
     * @param int $user_id User ID to retrieve
     * @return array|null User data array if found, null otherwise
     */
    public function getUserById(int $user_id): ?array
    {
        $query = "SELECT u.*, r.role_name 
                  FROM tbl_users u
                  JOIN tbl_roles r ON u.role_id = r.role_id
                  WHERE u.user_id = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::getUserById - Prepare failed: ' . mysqli_error($this->db));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user ?: null;
        } else {
            error_log('UserModel::getUserById - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
    }

    /**
     * Menghapus data user berdasarkan ID.
     *
     * @param int $user_id User ID to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteUser(int $user_id): bool
    {
        $query = "DELETE FROM tbl_users WHERE user_id = ?";
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt === false) {
            error_log('UserModel::deleteUser - Prepare failed: ' . mysqli_error($this->db));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $user_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('UserModel::deleteUser - Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}
