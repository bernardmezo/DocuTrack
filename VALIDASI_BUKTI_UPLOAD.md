# ✅ Validasi Bukti Upload - Implementation Summary

## 🎯 Fitur yang Ditambahkan

### 1. **Auto-detect Bukti yang Sudah Ada**

- Query `getRABForLPJWithExisting()` sudah LEFT JOIN dengan `tbl_lpj_item`
- Jika `fileBukti` tidak kosong di database → item sudah punya bukti

### 2. **UI Update untuk Bukti yang Sudah Ada**

Jika bukti sudah ada:

- ✅ Tampilkan icon hijau **"Terupload"** (bukan button upload)
- ✅ Field realisasi jadi **readonly** (tidak bisa edit)
- ✅ Tampilkan nilai realisasi dari database

Jika bukti belum ada:

- ✅ Tampilkan button **"Upload"**
- ✅ Field realisasi bisa **diedit**

### 3. **Logic After Upload Success**

Setelah upload berhasil:

1. Button upload → hidden
2. Icon hijau → muncul
3. Field realisasi → jadi readonly dengan nilai saat ini
4. Hidden input `file_bukti` → terisi dengan filename
5. Re-check semua bukti untuk enable button submit

---

## 🧪 Testing Checklist

### **Scenario 1: Item Belum Punya Bukti**

1. ✅ Buka halaman LPJ
2. ✅ Lihat item yang belum ada buktinya
3. ✅ **Expected**:
   - Button "Upload" muncul
   - Field realisasi bisa diedit (ada input number)

### **Scenario 2: Upload Bukti Pertama Kali**

1. ✅ Klik button "Upload"
2. ✅ Pilih file gambar
3. ✅ Klik "Simpan Bukti"
4. ✅ **Expected**:
   - Alert "Bukti berhasil diupload dan tersimpan!"
   - Button upload → hilang
   - Icon hijau "Terupload" → muncul
   - Field realisasi → jadi readonly (biru, tidak bisa edit)

### **Scenario 3: Item Sudah Punya Bukti (dari Database)**

1. ✅ Refresh halaman LPJ (Ctrl + F5)
2. ✅ **Expected**:
   - Icon hijau "Terupload" langsung muncul (tanpa button upload)
   - Field realisasi sudah readonly dari awal
   - Nilai realisasi ditampilkan dari database

### **Scenario 4: Submit LPJ**

1. ✅ Upload semua bukti untuk semua item
2. ✅ **Expected**:
   - Button "Ajukan ke Bendahara" jadi **enabled** (warna hijau)
3. ✅ Klik button submit
4. ✅ **Expected**:
   - Validasi pass
   - Data terkirim ke server

---

## 🔍 Debug Log

### **Check di Browser Console**

```javascript
// Jalankan ini di console untuk cek status upload
document.querySelectorAll("tbody tr[data-rab-item-id]").forEach((row) => {
  const rabItemId = row.dataset.rabItemId;
  const fileBuktiInput = row.querySelector('input[name*="[file_bukti]"]');
  const fileBukti = fileBuktiInput ? fileBuktiInput.value : "EMPTY";
  const realisasiInput = row.querySelector(".realisasi-input");
  const isEditable = !!realisasiInput;

  console.log({
    rabItemId,
    fileBukti,
    isEditable,
    status: fileBukti ? "✅ Ada bukti" : "❌ Belum ada bukti",
  });
});
```

**Expected Output:**

```
{rabItemId: "123", fileBukti: "lpj_bukti_123_xxx.jpg", isEditable: false, status: "✅ Ada bukti"}
{rabItemId: "124", fileBukti: "EMPTY", isEditable: true, status: "❌ Belum ada bukti"}
```

---

### **Check di Database**

```sql
-- Cek data yang sudah diupload
SELECT
    li.lpjItemId,
    li.lpjId,
    li.kategoriId,
    li.uraian,
    li.realisasi,
    li.fileBukti,
    li.createdAt
FROM tbl_lpj_item li
WHERE li.lpjId = 123  -- Ganti dengan lpjId Anda
ORDER BY li.lpjItemId DESC;
```

**Expected Result:**

```
| lpjItemId | uraian    | realisasi | fileBukti                   | createdAt           |
|-----------|-----------|-----------|----------------------------|---------------------|
| 456       | Nasi Box  | 500000    | lpj_bukti_123_xxx.jpg      | 2025-12-16 10:30:00 |
| 457       | Sewa Mic  | 750000    | lpj_bukti_124_xxx.jpg      | 2025-12-16 10:31:00 |
```

---

## 📝 Key Changes Summary

| File               | Change                                     | Purpose                                 |
| ------------------ | ------------------------------------------ | --------------------------------------- |
| **detail_lpj.php** | Tambah `$bukti_sudah_ada = !empty($bukti)` | Deteksi bukti dari database             |
| **detail_lpj.php** | Update kondisi realisasi input             | Readonly jika bukti sudah ada           |
| **detail_lpj.php** | Update kondisi button upload               | Hide jika bukti sudah ada               |
| **detail_lpj.php** | Update `checkAllBuktiUploaded()`           | Cek dari hidden input, bukan dataset    |
| **detail_lpj.php** | Update upload success handler              | Ubah input jadi readonly setelah upload |

---

## ✅ Flow Diagram

```
┌─────────────────────────────────────────┐
│  User membuka halaman LPJ               │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│  Controller query getRABForLPJWithExisting│
│  LEFT JOIN tbl_lpj_item                 │
└──────────────┬──────────────────────────┘
               │
               ▼
        ┌──────────────┐
        │ fileBukti?   │
        └──┬───────┬───┘
           │       │
    ┌──────┘       └──────┐
    │ NULL               │ ADA
    ▼                    ▼
┌─────────────┐    ┌──────────────┐
│ Button      │    │ Icon Hijau   │
│ Upload      │    │ "Terupload"  │
│             │    │              │
│ Realisasi   │    │ Realisasi    │
│ EDITABLE    │    │ READONLY     │
└─────────────┘    └──────────────┘
```

---

## 🚨 Troubleshooting

### **Masalah: Icon hijau tidak muncul setelah upload**

**Solusi:**

1. Check console log: `console.log(result.filename)`
2. Pastikan `result.success === true`
3. Cek apakah `displayArea` ada di DOM:
   ```javascript
   const displayArea = document.getElementById("bukti-display-123");
   console.log("Display area:", displayArea);
   ```

### **Masalah: Field realisasi masih bisa diedit setelah upload**

**Solusi:**

1. Check apakah script update TD berjalan:
   ```javascript
   console.log("Updating realisasi to readonly...");
   ```
2. Pastikan `tdRealisasi` tidak null
3. Refresh halaman untuk verify dari database

### **Masalah: Button submit tetap disabled**

**Solusi:**

1. Jalankan debug script di console (lihat section Debug Log)
2. Check apakah semua `fileBukti` hidden input terisi
3. Pastikan `checkAllBuktiUploaded()` dipanggil setelah upload

---

## ✨ Next Steps

1. ✅ Test upload flow
2. ✅ Test readonly realisasi
3. ✅ Test submit dengan semua bukti
4. ✅ Test refresh halaman (data persist)
5. ✅ Test validasi submit

Semua fitur sudah terimplementasi! 🚀
