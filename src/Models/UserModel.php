<?php

namespace App\Models;

// No base Model class, directly use mysqli

class UserModel
{
    private $db;
    protected $table = 'tbl_user'; // Define the table name

    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Get user by ID.
     *
     * @param int $userId
     * @return array|null
     */
    public function getById(int $userId): ?array
    {
        $query = "SELECT userId, nama, email, roleId FROM {$this->table} WHERE userId = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user;
    }

    /**
     * Get users by role name.
     *
     * @param string $roleName
     * @return array
     */
    public function getUsersByRole(string $roleName): array
    {
        $query = "SELECT u.userId, u.nama, u.email, u.roleId
                  FROM {$this->table} u
                  JOIN tbl_roles r ON u.roleId = r.roleId
                  WHERE r.roleName = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "s", $roleName);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $users = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $users[] = $row;
        }
        mysqli_stmt_close($stmt);
        return $users;
    }

    /**
     * Get user ID by email address.
     *
     * @param string $email
     * @return int|null
     */
    public function getUserIdByEmail(string $email): ?int
    {
        $query = "SELECT userId FROM {$this->table} WHERE email = ?";
        $stmt = mysqli_prepare($this->db, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $user['userId'] ?? null;
    }
}
