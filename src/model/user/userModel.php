<?php
// Model untuk tbl_users

// ==== ROLES FUNCTIONS ====

/**
 * Mengambil semua data role dari tbl_roles.
 * Berguna untuk mengisi <select> dropdown saat membuat/mengedit user.
**/
if (function_exists('getAllRoles')) {
    function getAllRoles() {
        global $conn;
        
        $query = "SELECT * FROM tbl_roles ORDER BY role_name ASC";
        $result = mysqli_query($conn, $query);

        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $roles = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $roles[] = $row;
        }

        mysqli_free_result($result);
        return $roles;
    }
}


// ==== USER FUNCTIONS ====

// Memverifikasi login use
if (!function_exists('verifyUserLogin')) {
    function verifyUserLogin($email, $password) {
        global $conn;

        // Ambil user berdasarkan email dan join dengan role
        $query = "SELECT u.*, r.role_name 
                  FROM tbl_users u 
                  JOIN tbl_roles r ON u.role_id = r.role_id
                  WHERE u.email = ? AND u.is_active = 1";
                  
        $stmt = mysqli_prepare($conn, $query);
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
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
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}


// tambah user baru (buat di insert user, ada di super admin)
if (!function_exists('insertUser')) {
    function insertUser($userName, $roleId, $prodiId, $email, $password, $confirmPassword) {
        global $conn;
        
        try {
            // Validasi password dan konfirmasi password
            if ($password !== $confirmPassword) {
                throw new Exception('Password dan konfirmasi password tidak sesuai.');
            }
        } catch (Exception $e) {
            error_log('Error inserting user: ' . $e->getMessage());
            return false;
        }

        // hash password
        $passwordHased = password_hash($password, PASSWORD_BCRYPT);

        // Set is_active ke 1 (aktif) secara default
        $is_active = 1;

        // Siapkan pernyataan SQL (menambahkan is_active)
        $stmt = mysqli_prepare($conn, "INSERT INTO tbl_users (username, role_id, prodi_id, email, password_hash, is_active, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter (tambahkan 'i' untuk is_active)
        mysqli_stmt_bind_param($stmt, 'siissi', $userName, $roleId, $prodiId, $email, $passwordHased, $is_active);

        // Eksekusi pernyataan
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

// Mengupdate data user (non-password) oleh admin.
if (!function_exists('updateUser')) {
    function updateUser($user_id, $userName, $roleId, $prodiId, $email, $is_active) {
        global $conn;

        $query = "UPDATE tbl_users SET username = ?, role_id = ?, prodi_id = ?, email = ?, is_active = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        // Bind parameter: s, i, i, s, i, i
        mysqli_stmt_bind_param($stmt, 'siisii', $userName, $roleId, $prodiId, $email, $is_active, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

// Mengubah password user.
if (!function_exists('updateUserPassword')) {
    function updateUserPassword($user_id, $newPassword, $confirmPassword) {
        global $conn;

        if ($newPassword !== $confirmPassword) {
            error_log('Password change failed: Passwords do not match.');
            return false;
        }

        $passwordHased = password_hash($newPassword, PASSWORD_BCRYPT);
        
        $query = "UPDATE tbl_users SET password_hash = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'si', $passwordHased, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}

//  Mengambil semua data user dengan nama role-nya (Fungsi ini diambil dari contohmu dan disesuaikan dengan skema)
if (!function_exists('getAllUsers')) {
    function getAllUsers() {
        global $conn;

        // Menggunakan user_id dan role_name sesuai skema
        $query = "SELECT u.user_id, u.username, u.email, u.prodi_id, u.is_active, r.role_name, u.created_at 
                  FROM tbl_users u
                  JOIN tbl_roles r ON u.role_id = r.role_id
                  ORDER BY u.created_at DESC";

        $result = mysqli_query($conn, $query);
        if ($result === false) {
            error_log('Query failed: ' . mysqli_error($conn));
            return [];
        }

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }

        mysqli_free_result($result);
        return $users;
    }
}

// Mengambil satu data user spesifik berdasarkan ID.
if (!function_exists('getUserById')) {
    function getUserById($user_id) {
        global $conn;

        $query = "SELECT u.*, r.role_name 
                  FROM tbl_users u
                  JOIN tbl_roles r ON u.role_id = r.role_id
                  WHERE u.user_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $user_id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            return $user;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }
    }
}

// Menghapus data user berdasarkan ID.
if (!function_exists('deleteUser')) {
    function deleteUser($user_id) {
        global $conn;

        $query = "DELETE FROM tbl_users WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);

        if ($stmt === false) {
            error_log('Prepare failed: ' . mysqli_error($conn));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'i', $user_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            error_log('Execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
    }
}
?>