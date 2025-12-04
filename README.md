# DocuTrack 📄

Selamat datang di **DocuTrack**, sebuah solusi Sistem Informasi Manajemen yang dirancang untuk mengefisiensikan proses administrasi, pengajuan kegiatan, dan pengelolaan keuangan di lingkungan akademik.

## 🌟 Tentang Proyek

DocuTrack hadir sebagai jembatan digital yang menghubungkan berbagai pemangku kepentingan—mulai dari Pengusul, Verifikator, PPK, Bendahara, hingga Pimpinan—dalam satu ekosistem terpadu. Aplikasi ini bertujuan untuk meningkatkan transparansi, akuntabilitas, dan kecepatan dalam:

*   **Pengajuan Usulan Kegiatan:** Mempermudah proses submission dan tracking status usulan.
*   **Verifikasi & Persetujuan:** Alur kerja berjenjang (tiered approval) yang sistematis.
*   **Manajemen Keuangan:** Pengelolaan pencairan dana dan monitoring anggaran.
*   **Pelaporan Pertanggungjawaban (LPJ):** Digitalisasi arsip dan validasi dokumen laporan.

## 🚀 Fitur Utama

*   **Multi-Role Access:** Dashboard yang disesuaikan untuk Super Admin, Admin, Verifikator, PPK, Bendahara, Wadir, dan Pengusul.
*   **Real-time Monitoring:** Pantau progres pengajuan kegiatan dan pencairan dana secara langsung.
*   **Manajemen Dokumen Terpusat:** Penyimpanan aman untuk KAK, RAB, dan dokumen pendukung lainnya.
*   **Alur Kerja Otomatis:** Notifikasi dan status yang terupdate otomatis sesuai tahapan birokrasi.

## 🛠️ Teknologi yang Digunakan

Proyek ini dibangun dengan fondasi teknologi yang solid dan mudah dikembangkan:

*   **Backend:** PHP (MVC Architecture)
*   **Database:** MySQL
*   **Frontend:** HTML5, CSS3, JavaScript (Vanilla & Helper Libraries)
*   **Styling:** Tailwind CSS (via PostCSS)

## 📦 Instalasi & Penggunaan

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username/DocuTrack.git
    ```
2.  **Install Dependencies**
    ```bash
    composer install
    npm install
    ```
3.  **Konfigurasi Database**
    *   Copy file `.env.example` menjadi `.env`
    *   Sesuaikan kredensial database Anda.
    *   Import database schema dari folder `database/`.
4.  **Jalankan Aplikasi**
    Akses melalui web server lokal Anda (misal: XAMPP/Laragon) atau jalankan built-in server:
    ```bash
    php -S localhost:8000 -t public
    ```

## 🤝 Berkontribusi

Kami sangat menghargai setiap kontribusi untuk membuat DocuTrack menjadi lebih baik. Silakan ajukan *Pull Request* atau buat *Issue* jika Anda menemukan bug atau memiliki ide fitur baru.

---
*Dibuat dengan ❤️ untuk efisiensi administrasi yang lebih baik.*