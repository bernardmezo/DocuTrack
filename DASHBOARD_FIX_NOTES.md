# Fix Dashboard Admin - List KAK Tidak Muncul

## Masalah
Dashboard admin menampilkan statistik KAK dengan benar (menghitung total), tetapi list KAK menampilkan "Tidak ada data yang ditemukan".

## Penyebab Masalah
1. **Filter Jurusan untuk Admin**: Admin menggunakan filter `$_SESSION['user_jurusan']` yang membatasi hasil query hanya untuk jurusan tertentu, padahal admin seharusnya melihat SEMUA data dari semua jurusan.
2. **Query Status Mapping**: Status "Draft" tidak konsisten dengan yang diharapkan oleh dashboard.
3. **JavaScript Filter**: Filter data di JavaScript tidak menangani field yang mungkin undefined/null dengan baik.

## Solusi yang Diterapkan

### 1. DashboardController.php
**File**: `src/controllers/Admin/DashboardController.php`

**Perubahan**:
```php
// SEBELUM:
$jurusan = $_SESSION['user_jurusan'] ?? null;
$list_kak = $this->kegiatanService->getDashboardKAK($jurusan);

// SESUDAH:
// Admin tidak perlu filter jurusan - harus melihat semua data
$list_kak = $this->kegiatanService->getDashboardKAK(null);
```

**Alasan**: Admin harus bisa melihat semua KAK dari semua jurusan untuk keperluan monitoring dan approval.

---

### 2. KegiatanModel.php
**File**: `src/Models/Kegiatan/KegiatanModel.php`

**Perubahan pada method `getDashboardKAK()`**:
1. Menambahkan JOIN dengan `tbl_kak` untuk memastikan relasi data
2. Memperbaiki CASE statement untuk status:

```php
CASE 
    WHEN k.statusUtamaId = 4 THEN 'Ditolak'
    WHEN k.statusUtamaId = 2 THEN 'Revisi'
    WHEN k.statusUtamaId = 5 THEN 'Disetujui'
    WHEN k.posisiId = 1 AND k.statusUtamaId = 3 THEN 'Usulan Disetujui'
    WHEN k.posisiId = 1 AND k.statusUtamaId = 1 THEN 'Menunggu'  // CHANGED from 'Draft'
    WHEN k.posisiId = 2 THEN 'Di Verifikator'
    WHEN k.posisiId = 4 THEN 'Di PPK'
    WHEN k.posisiId = 3 THEN 'Di Wadir'
    WHEN k.posisiId = 5 AND k.tanggalPencairan IS NULL THEN 'Di Bendahara'
    WHEN k.posisiId = 5 AND k.tanggalPencairan IS NOT NULL THEN 'Dana Diberikan'
    ELSE 'Menunggu'  // CHANGED: Fallback ke 'Menunggu' instead of namaStatusUsulan
END as status
```

---

### 3. dashboard.js
**File**: `public/assets/js/admin/dashboard.js`

**Perubahan**:

#### a. Tambah Debug Logging
```javascript
// Di awal DOMContentLoaded
console.log('Data KAK:', dataKAK);
console.log('Data LPJ:', dataLPJ);
console.log('Total KAK:', dataKAK ? dataKAK.length : 0);
console.log('Total LPJ:', dataLPJ ? dataLPJ.length : 0);
```

#### b. Validasi Data di Constructor
```javascript
constructor(data, tableId, config) {
    // Validasi data - pastikan data tidak undefined atau null
    this.allData = data || [];
    this.filteredData = data || [];
    
    console.log(`[${config.type.toUpperCase()}] Initializing with ${this.allData.length} items`);
    // ...
}
```

#### c. Perbaikan Filter Logic
```javascript
this.filteredData = this.allData.filter(item => {
    // Pastikan semua field ada sebelum digunakan
    const nama = item.nama || '';
    const pengusul = item.pengusul || '';
    const nama_mahasiswa = item.nama_mahasiswa || '';
    const prodi = item.prodi || '';
    const nim = item.nim || '';
    const status = item.status || '';
    const jurusan = item.jurusan || '';
    
    // Filter logic dengan safe access
    // ...
});
```

#### d. Logging di Render Methods
Menambahkan console.log di `renderCards()` dan `renderTable()` untuk tracking data flow.

---

## File Testing
**File baru**: `test_dashboard_data.php`

Script untuk memverifikasi:
1. Koneksi database
2. KegiatanModel::getDashboardKAK() mengambil data dengan benar
3. Direct query ke database
4. KegiatanService::getDashboardKAK() berfungsi
5. JSON encoding bekerja dengan benar
6. Dashboard stats

**Cara menggunakan**:
Akses `http://localhost/docutrack/test_dashboard_data.php` di browser untuk melihat hasil test.

---

## Langkah Troubleshooting

Jika list KAK masih tidak muncul setelah perubahan ini:

1. **Buka Browser Console** (F12 > Console tab)
   - Lihat output dari console.log untuk memeriksa data yang diterima
   - Check apakah ada JavaScript errors

2. **Jalankan Test Script**
   - Akses `http://localhost/docutrack/test_dashboard_data.php`
   - Verifikasi bahwa data bisa diambil dari database

3. **Check Database**
   ```sql
   SELECT * FROM tbl_kegiatan LIMIT 10;
   SELECT * FROM tbl_kak LIMIT 10;
   ```

4. **Check Session Admin**
   ```php
   echo "<pre>";
   print_r($_SESSION);
   echo "</pre>";
   ```
   - Pastikan admin tidak memiliki filter jurusan yang membatasi

5. **Check Network Tab** (F12 > Network)
   - Lihat response dari controller
   - Verifikasi bahwa data dikirim dari server

---

## Expected Behavior After Fix

1. ✅ Dashboard admin menampilkan statistik KAK dengan benar
2. ✅ List KAK menampilkan SEMUA data dari semua jurusan
3. ✅ Filter status, jurusan, dan search berfungsi dengan baik
4. ✅ Pagination bekerja dengan benar
5. ✅ Tidak ada JavaScript errors di console

---

## Files Modified

1. `src/controllers/Admin/DashboardController.php` - Removed jurusan filter for admin
2. `src/Models/Kegiatan/KegiatanModel.php` - Fixed query and status mapping
3. `public/assets/js/admin/dashboard.js` - Added validation and debug logging

## Files Created

1. `test_dashboard_data.php` - Testing script for data retrieval
2. `DASHBOARD_FIX_NOTES.md` - This documentation

---

## Tanggal Fix
18 Desember 2025
