<?php
// File: src/models/LoginModel.php

class LoginModel {
    // Properti untuk menyimpan koneksi
    private $db;

    public function __construct() {
        // 1. Panggil file koneksi
        // Gunakan __DIR__ agar path-nya pasti benar (di folder yang sama dengan model ini)
        require_once __DIR__ . '/conn.php';

        // 2. Pindahkan variabel $conn global ke dalam properti class ($this->db)
        // Kita gunakan 'global' keyword atau cek isset agar variabel $conn dari luar bisa masuk
        if (isset($conn)) {
            $this->db = $conn;
        } else {
            die("Error: Variabel \$conn tidak ditemukan di conn.php");
        }
    }

    /**
     * Mengambil data user berdasarkan email
     */
    public function getUserByEmail($email) {
        // Catatan: Parameter $password dihapus dari sini. 
        // Model tugasnya hanya mengambil data. Pengecekan password dilakukan di Controller (AuthController).

        // 3. Perbaikan Query: Gunakan '?' bukan ':email' untuk MySQLi
        $query = "SELECT 
            u.userId,
            u.nama,
            u.email,
            u.password,
            u.roleId,
            r.namaRole,    /* <--- Ini mengambil nama role dari tabel tbl_role */
            j.namaJurusan
          FROM tbl_user u
          LEFT JOIN tbl_role r ON u.roleId = r.roleId
          LEFT JOIN tbl_jurusan j ON u.namaJurusan = j.namaJurusan
          WHERE u.email = ?";

        // 4. Prepare Statement menggunakan $this->db
        $stmt = mysqli_prepare($this->db, $query);

        if ($stmt) {
            // 5. Binding Parameter (s = string) menggantikan tanda tanya (?) dengan isi variabel $email
            mysqli_stmt_bind_param($stmt, "s", $email);

            // 6. Eksekusi
            mysqli_stmt_execute($stmt);

            // 7. Ambil Hasilnya
            $result = mysqli_stmt_get_result($stmt);
            
            // 8. Kembalikan data dalam bentuk Array Asosiatif (agar enak dipakai $user['nama'])
            if ($row = mysqli_fetch_assoc($result)) {
                return $row;
            } else {
                return false; // Email tidak ditemukan
            }

            // Tutup statement
            mysqli_stmt_close($stmt);
        } else {
            // Jika query error (misal salah nama tabel)
            die("Query Error: " . mysqli_error($this->db));
        }
    }
}