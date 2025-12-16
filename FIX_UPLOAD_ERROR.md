# 🔧 Fix untuk Error "Unknown column 'rabItemId' in 'where clause'"

## ✅ Masalah Sudah Diperbaiki

### Penyebab Error:
Tabel `tbl_lpj_item` **TIDAK PUNYA kolom `rabItemId`**, tapi query di method `upsertLpjItemBukti()` mencoba menggunakan kolom tersebut di WHERE clause.

### Solusi yang Diterapkan:

#### 1. **Ubah Strategi Matching di `upsertLpjItemBukti()`**
   - ❌ **SEBELUM**: `WHERE lpjId = ? AND rabItemId = ?`
   - ✅ **SESUDAH**: `WHERE lpjId = ? AND kategoriId = ? AND uraian = ?`

#### 2. **Perbaiki Query INSERT**
   - Kolom `harga` → `hargaSatuan`
   - Tambahkan error handling

#### 3. **Update `getRABItemById()`**
   - Tambahkan alias untuk backward compatibility
   - Tambahkan debug log

---

## 🧪 Cara Testing

### 1. **Clear Cache Browser**
```
Ctrl + Shift + R (Hard Reload)
```

### 2. **Test Upload**
1. Buka halaman LPJ
2. Klik button upload pada item RAB
3. Pilih file gambar (JPG/PNG < 5MB)
4. Klik "Simpan Bukti"

### 3. **Expected Result**
```json
{
  "success": true,
  "message": "Bukti berhasil diupload dan tersimpan",
  "filename": "lpj_bukti_123_1234567890.jpg"
}
```

### 4. **Check Database**
```sql
-- Lihat data yang baru masuk
SELECT lpjItemId, lpjId, kategoriId, uraian, fileBukti, createdAt
FROM tbl_lpj_item 
WHERE lpjId = 123  -- Ganti dengan lpjId Anda
ORDER BY lpjItemId DESC 
LIMIT 5;
```

### 5. **Check Error Log**
```bash
# Windows XAMPP
tail -f C:\xampp\apache\logs\error.log

# Cari log ini:
# ✅ Found RAB item: rabItemId=123, uraian=Nasi Box
# ✅ Inserted new lpjItemId=456 with bukti: lpj_bukti_123_xxx.jpg
```

---

## 📋 Struktur Tabel `tbl_lpj_item` yang Dibutuhkan

```sql
CREATE TABLE IF NOT EXISTS tbl_lpj_item (
    lpjItemId INT AUTO_INCREMENT PRIMARY KEY,
    lpjId INT NOT NULL,
    kategoriId INT NOT NULL,
    uraian VARCHAR(255),
    rincian TEXT,
    sat1 VARCHAR(50),
    sat2 VARCHAR(50),
    vol1 DECIMAL(10,2),
    vol2 DECIMAL(10,2),
    hargaSatuan DECIMAL(15,2),
    totalHarga DECIMAL(15,2),
    realisasi DECIMAL(15,2),
    fileBukti VARCHAR(255),
    komentar TEXT,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updatedAt TIMESTAMP NULL,
    FOREIGN KEY (lpjId) REFERENCES tbl_lpj(lpjId),
    FOREIGN KEY (kategoriId) REFERENCES tbl_kategori_rab(kategoriRabId),
    INDEX idx_lpj_kategori (lpjId, kategoriId),
    INDEX idx_uraian (uraian)
);
```

**CATATAN**: Kolom `rabItemId` **OPSIONAL**. Jika ingin menambahkan nanti:
```sql
ALTER TABLE tbl_lpj_item ADD COLUMN rabItemId INT NULL AFTER lpjId;
ALTER TABLE tbl_lpj_item ADD INDEX idx_rab_item (rabItemId);
```

---

## 🚨 Troubleshooting

### Jika Masih Error "Unknown column"

**1. Cek Nama Kolom Tabel**
```sql
SHOW COLUMNS FROM tbl_lpj_item;
```

**2. Pastikan Kolom Ini Ada:**
- lpjItemId ✅
- lpjId ✅
- kategoriId ✅
- uraian ✅
- hargaSatuan ✅ (BUKAN `harga`)
- totalHarga ✅
- fileBukti ✅

**3. Jika Ada Kolom yang Berbeda Nama:**
Update query di `LpjModel.php` sesuai nama kolom yang benar.

### Jika Error "Column count doesn't match"

Berarti jumlah kolom di INSERT tidak sesuai dengan jumlah VALUES.
Cek query INSERT di baris ~260:
```php
$insertQuery = "INSERT INTO tbl_lpj_item 
               (lpjId, kategoriId, uraian, rincian, 
                sat1, sat2, vol1, vol2, hargaSatuan, totalHarga, 
                realisasi, fileBukti, createdAt)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
```

Hitung:
- Kolom: 12 (+ createdAt = 13)
- VALUES: 12 placeholder `?` (+ NOW() = 13)
- Bind params: `"iissssddddds"` = 12 parameter ✅

---

## 📝 Summary of Changes

| File | Line | Change | Why |
|------|------|--------|-----|
| `LpjModel.php` | 238 | `WHERE lpjId = ? AND kategoriId = ? AND uraian = ?` | Kolom `rabItemId` tidak ada di tabel |
| `LpjModel.php` | 260 | `harga` → `hargaSatuan` | Match dengan nama kolom di database |
| `LpjModel.php` | 318 | Tambah aliases di SELECT | Backward compatibility |
| `LpjModel.php` | 278 | Tambah error handling | Better debugging |

---

## ✅ Next Steps

1. ✅ **Test Upload** → Harus berhasil tanpa error
2. ✅ **Check Database** → Data masuk ke `tbl_lpj_item`
3. ✅ **Check UI** → Button upload berubah jadi icon hijau "Ada"
4. ✅ **Test Submit LPJ** → Tombol "Ajukan ke Bendahara" aktif

Jika masih ada error, kirimkan:
- Screenshot error di browser console
- Log dari `C:\xampp\apache\logs\error.log`
- Hasil `SHOW COLUMNS FROM tbl_lpj_item;`
