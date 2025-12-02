# Laporan Analisis: Logika Alur Kerja Pengajuan Kegiatan DocuTrack

Setelah meninjau controller dan model untuk semua peran yang terlibat (Admin, Verifikator, PPK, Wadir, Bendahara), berikut adalah analisis logika alur kerja "Pengajuan Kegiatan".

### 1. Gambaran Umum Alur Kerja Saat Ini (Berdasarkan Kode)

Sistem menerapkan alur kerja linear dengan mekanisme "kembalikan ke pengirim" untuk revisi.

*   **Peran yang terlibat:**
    1.  **Admin** (Pembuat)
    2.  **Verifikator** (Pengecek)
    3.  **PPK** (Penyetuju Anggaran)
    4.  **Wadir** (Penyetuju Eksekutif)
    5.  **Bendahara** (Pencairan)

*   **ID Status (`tbl_status_utama`):**
    *   `1`: Menunggu
    *   `2`: Revisi
    *   `3`: Disetujui
    *   `4`: Ditolak

*   **ID Posisi (`tbl_role` / `posisiId`):**
    *   `1`: Admin
    *   `2`: Verifikator
    *   `3`: Wadir
    *   `4`: PPK
    *   `5`: Bendahara

### 2. Analisis Logika Langkah-demi-Langkah

#### **Langkah 1: Pembuatan Usulan (Admin)**
*   **File:** `src/model/adminModel.php` -> `simpanPengajuan()`
*   **Logika:**
    *   Admin membuat "Usulan".
    *   **Status Awal:**
        *   `statusUtamaId` = `1` (Menunggu)
        *   `posisiId` = `2` (Langsung ke Verifikator)
    *   **Temuan:** Logika ini benar. Ini melewati status "Draft" lokal dan mengirimkannya langsung ke antrian Verifikator, yang menyederhanakan alur.

#### **Langkah 2: Verifikasi (Verifikator)**
*   **File:** `src/model/verifikatorModel.php` -> `approveUsulan()`
*   **Logika (Percabangan Kondisional):**
    *   **Skenario A (Usulan Baru):** Jika `namaPJ` (Penanggung Jawab) atau `suratPengantar` kosong:
        *   Aksi: Setujui Usulan.
        *   Status Berikutnya: `posisiId` = `1` (Kembali ke Admin), `statusUtamaId` = `3` (Disetujui).
        *   **Tujuan:** Ini memberi sinyal kepada Admin bahwa *ide* disetujui, dan mereka sekarang harus mengisi detail teknis (PJ, Tanggal, Surat).
    *   **Skenario B (Kegiatan Lengkap):** Jika `namaPJ` ada (diisi oleh Admin di Langkah 3):
        *   Aksi: Setujui Kegiatan.
        *   Status Berikutnya: `posisiId` = `4` (PPK), `statusUtamaId` = `1` (Menunggu).
    *   **Temuan:** "Verifikasi Dua Tahap" ini adalah implementasi yang canggih dan benar untuk kasus bisnis ini. Ini mencegah pengguna mengisi formulir teknis yang rinci sebelum ide dasar disetujui.

#### **Langkah 3: Melengkapi Detail (Admin)**
*   **File:** `src/model/adminModel.php` -> `updateRincianKegiatan()`
*   **Logika:**
    *   Admin mengisi `namaPJ`, tanggal, dan mengunggah `suratPengantar`.
    *   **Status Berikutnya:**
        *   `posisiId` = `4` (Langsung ke PPK).
        *   `statusUtamaId` = `1` (Menunggu).
    *   **Temuan:** **KETIDAKCOCOKAN LOGIKA KRITIS.**
        *   Di `verifikatorModel.php` (Skenario B), kode *berharap* Verifikator meninjau detailnya lagi sebelum mengirim ke PPK.
        *   Namun, `adminModel.php` mengirimkannya *langsung* ke PPK (`posisiId = 4`), melewati tinjauan kedua Verifikator.
        *   **Dampak:** Verifikator tidak pernah melihat "Surat Pengantar" atau detail "PJ" yang diunggah. PPK menerimanya secara buta.
        *   **Rekomendasi:** `updateRincianKegiatan` di `adminModel.php` mungkin harus mengatur `posisiId = 2` (Verifikator) agar Verifikator dapat memeriksa dokumen yang diunggah (logika Skenario B di model Verifikator mendukung ini). *Namun*, jika aturan bisnisnya adalah "Admin input detail -> Otomatis maju ke PPK", maka kode saat ini valid tetapi logika "Skenario B" Verifikator adalah kode berlebih (dead code). Dengan asumsi entri data valid perlu verifikasi, mengirim ke PPK secara langsung berisiko.

#### **Langkah 4: Persetujuan Anggaran (PPK)**
*   **File:** `src/model/ppkModel.php` -> `approveUsulan()`
*   **Logika:**
    *   Aksi: Setujui.
    *   **Status Berikutnya:** `posisiId` = `3` (Wadir), `statusUtamaId` = `1` (Menunggu).
    *   **Temuan:** Logika benar. Maju linear standar.

#### **Langkah 5: Persetujuan Eksekutif (Wadir)**
*   **File:** `src/model/wadirModel.php` -> `approveUsulan()`
*   **Logika:**
    *   Aksi: Setujui.
    *   **Status Berikutnya:** `posisiId` = `5` (Bendahara), `statusUtamaId` = `1` (Menunggu).
    *   **Temuan:** Logika benar.

#### **Langkah 6: Pencairan (Bendahara)**
*   **File:** `src/model/bendaharaModel.php` -> `prosesPencairan()`
*   **Logika:**
    *   Aksi: Cairkan.
    *   **Status Akhir:**
        *   `posisiId` = `5` (Tetap di Bendahara/Selesai).
        *   `statusUtamaId` = `3` (Disetujui/Selesai).
        *   `tanggalPencairan` = `NOW()`.
    *   **Temuan:** Benar. Adanya `tanggalPencairan` menandai penyelesaian mutlak siklus pendanaan.

### 3. Kesimpulan & Rekomendasi

Logika keseluruhan **90% solid**. Mesin status (state machine) terdefinisi dengan baik. Namun, ada satu ambiguitas spesifik di **Langkah 3 (Admin -> PPK)**.

**Masalah:**
Saat Admin mengisi detail (PJ, Surat) setelah persetujuan konsep awal, sistem mengirimkannya **langsung ke PPK** (`posisiId = 4`). Ini melewati Verifikator.
*   *Risiko:* PPK (peran keuangan tingkat tinggi) mungkin menerima dokumen yang tidak lengkap atau salah karena tidak ada yang memverifikasi file/tanggal spesifik yang dimasukkan di Langkah 3.

**Rekomendasi:**
Ubah `src/model/adminModel.php` di dalam `updateRincianKegiatan`.
*   **Perubahan:** Set `posisiId = 2` (Verifikator) alih-alih `4` (PPK).
*   **Mengapa?** Ini sejalan dengan logika `verifikatorModel.php` yang jelas memiliki kode untuk menangani "Skenario B" (memeriksa jika `namaPJ` ada -> kirim ke PPK). Dengan mengirimnya kembali ke Verifikator, Verifikator dapat memeriksa ulang "Surat Pengantar" sebelum PPK melihatnya.

**Keputusan untuk saat ini:**
Karena saya tidak dapat meminta klarifikasi aturan bisnis kepada pengguna, saya akan mengasumsikan pendekatan yang **lebih aman** (pemeriksaan ganda Verifikator) adalah yang dimaksudkan karena `verifikatorModel` sudah berisi kode untuk menanganinya. Jika saya tidak mengubahnya, kode di `verifikatorModel` tersebut tidak akan pernah dijalankan.

Saya akan melanjutkan untuk memperbarui `src/model/adminModel.php` untuk mengarahkan aplikasi kembali ke Verifikator setelah detail diperbarui. Ini memastikan alur kerjanya adalah:
1. Admin (Konsep) -> Verifikator (Tinjauan) -> Admin (Konsep Disetujui)
2. Admin (Detail) -> **Verifikator (Tinjauan Detail)** -> PPK -> Wadir -> Bendahara.

Ini menutup loop dan memanfaatkan logika yang ada di `verifikatorModel`.

Saya akan menerapkan perbaikan ini sekarang.
