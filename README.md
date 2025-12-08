# DocuTrack: Sistem Informasi Manajemen Pengajuan

![PHP](https://img.shields.io/badge/PHP-8.2-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-8.0-orange.svg)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-3.x-green.svg)
![PHPUnit](https://img.shields.io/badge/PHPUnit-11.x-purple.svg)

**DocuTrack** adalah sebuah solusi Sistem Informasi Manajemen yang dirancang untuk mendigitalisasi dan meningkatkan efisiensi alur kerja administrasi, mulai dari pengajuan kegiatan, verifikasi berjenjang, hingga pengelolaan keuangan dan pelaporan di lingkungan akademik atau organisasi.

---

## 🌟 Fitur Utama

- **Arsitektur Multi-Role:** Dashboard dan hak akses yang dirancang khusus untuk setiap peran: Super Admin, Admin (Pengusul), Verifikator, PPK, Bendahara, dan Pimpinan (Wadir/Direktur).
- **Alur Kerja Persetujuan (Approval Workflow):** Proses verifikasi dan persetujuan pengajuan yang sistematis dan transparan, dari satu level ke level berikutnya.
- **Manajemen Dokumen Terpusat:** Unggah, simpan, dan kelola dokumen penting seperti KAK, RAB, dan Surat Pengantar dalam satu repositori.
- **Monitoring Real-time:** Pantau status dan progres setiap pengajuan kegiatan dan pencairan dana secara langsung melalui dashboard.
- **Manajemen Keuangan:** Fasilitasi proses pencairan dana dan pelaporan pertanggungjawaban (LPJ) yang terstruktur.
- **Logging & Audit Trail:** Pencatatan setiap aktivitas penting pengguna untuk akuntabilitas dan penelusuran.

## 🛠️ Teknologi

- **Backend:** PHP 8.2+ dengan arsitektur MVC + Service Layer
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Styling:** Tailwind CSS (diproses melalui PostCSS)
- **Manajemen Dependensi:** Composer (PHP) & NPM (JS)
- **Testing:** PHPUnit

## 🚀 Getting Started

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan proyek DocuTrack di lingkungan lokal Anda.

### Prasyarat

- Web Server (XAMPP, Laragon, atau sejenisnya)
- PHP 8.2 atau lebih tinggi
- MySQL atau MariaDB
- Composer
- Node.js & NPM

### 1. Clone Repository

```bash
git clone https://github.com/bernardmezo/DocuTrack.git
cd DocuTrack
```

### 2. Instal Dependensi

Instal semua dependensi yang diperlukan untuk PHP dan JavaScript.

```bash
# Instal dependensi PHP
composer install

# Instal dependensi Node.js (untuk Tailwind CSS)
npm install
```

### 3. Konfigurasi Environment

Salin file environment dan sesuaikan dengan konfigurasi lokal Anda.

```bash
cp .env.example .env
```
Buka file `.env` yang baru dibuat dan atur variabel berikut sesuai dengan setup database Anda:
```ini
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=docutrack
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Setup Database

Buat database baru di MySQL/MariaDB Anda dengan nama yang telah Anda tentukan di file `.env` (contoh: `docutrack`). Kemudian, impor skema dan data awal.

```bash
# Contoh menggunakan mysql client
mysql -u root -p docutrack < database/schema_with_seed.sql
```

### 5. Jalankan Aplikasi

Arahkan *web root* dari server lokal Anda (misal: Apache di XAMPP) ke direktori `public/` di dalam folder proyek.

Buka browser dan akses `http://localhost/DocuTrack/public/`.

## 🧪 Menjalankan Tes

Proyek ini menggunakan PHPUnit untuk memastikan kualitas dan stabilitas kode. Untuk menjalankan semua test suite, gunakan perintah berikut dari direktori root proyek:

```bash
# Menjalankan PHPUnit
./vendor/bin/phpunit
```

Pastikan semua tes berjalan dengan sukses untuk memverifikasi bahwa semua komponen inti aplikasi berfungsi dengan benar.

## 🤝 Berkontribusi

Kontribusi Anda sangat kami hargai. Silakan buat *Issue* untuk melaporkan bug atau mengusulkan fitur, dan ajukan *Pull Request* untuk berpartisipasi dalam pengembangan.