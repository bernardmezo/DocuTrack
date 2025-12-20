<?php
// UI Pengajuan LPJ - Sesuai Screenshot
$status = trim($status ?? 'draft');
$kegiatan_data = $kegiatan_data ?? [];
$rab_items = $rab_items ?? [];
$back_url = $back_url ?? '/docutrack/public/admin/pengajuan-lpj';
$lpj_id = $lpj_id ?? 0;
$kak_id = $kak_id ?? 0;

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) {
        return "Rp " . number_format($angka ?? 0, 0, ',', '.');
    }
}

// Status flags
$is_draft = ($status === 'draft' || $status === 'menunggu_upload' || $status === 'siap_submit');
$is_menunggu = ($status === 'menunggu' || $status === 'diajukan');
$is_setuju = ($status === 'setuju' || $status === 'disetujui');
$is_revisi = ($status === 'revisi');
$can_edit = ($is_draft || $is_revisi);
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail RAB untuk LPJ</h2>
                <p class="text-sm text-gray-500 mt-1">Kegiatan: <strong><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></strong></p>
            </div>
            
            <!-- Back Button -->
            <a href="<?php echo $back_url; ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <!-- Info Banner -->
        <?php if ($can_edit) : ?>
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
            <div class="flex items-start gap-3">
                <i class="fas fa-info-circle text-blue-600 text-xl mt-0.5"></i>
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Informasi Pengajuan LPJ</h4>
                    <p class="text-sm text-blue-700">
                        Silakan upload semua bukti pertanggungjawaban untuk setiap item RAB. Isi <strong>Realisasi (RP)</strong> sesuai bukti yang diupload, lalu klik tombol <strong>"Ajukan ke Bendahara"</strong> untuk meminta verifikasi.
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Section 1: Rencana Anggaran Biaya (RAB) - Terverifikasi -->
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-blue-500">
                <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                <h3 class="text-xl font-bold text-gray-800">1. Rencana Anggaran Biaya (RAB) - Terverifikasi</h3>
            </div>

            <?php
            $grand_total_anggaran = 0;
            
            if (!empty($rab_items)) :
                foreach ($rab_items as $kategori => $items) :
                    if (empty($items)) continue;
                    
                    $subtotal_kategori = 0;
            ?>
            
            <!-- Kategori Header -->
            <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3 flex items-center gap-2">
                <span class="w-1 h-5 bg-blue-500 rounded"></span>
                <?php echo htmlspecialchars($kategori); ?>
            </h4>
            
            <!-- Table RAB -->
            <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Uraian / Rincian</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase w-32">Vol & Sat</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-40">Harga Satuan (RP)</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-48">Total Anggaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($items as $item) :
                            $total_anggaran = $item['harga_plan'] ?? $item['totalRencana'] ?? 0;
                            $subtotal_kategori += $total_anggaran;
                            
                            $vol_sat = '';
                            if (!empty($item['vol1']) && !empty($item['sat1'])) {
                                $vol_sat = $item['vol1'] . ' ' . $item['sat1'];
                                if (!empty($item['vol2']) && !empty($item['sat2'])) {
                                    $vol_sat .= ' x ' . $item['vol2'] . ' ' . $item['sat2'];
                                }
                            }
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <div class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($item['uraian'] ?? '-'); ?></div>
                                <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($item['rincian'] ?? '-'); ?></div>
                            </td>
                            <td class="px-4 py-3 text-center text-sm text-gray-600">
                                <?php echo htmlspecialchars($vol_sat ?: '-'); ?>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700 font-medium">
                                <?php echo number_format($item['harga_satuan'] ?? 0, 0, ',', '.'); ?>
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-800 font-bold">
                                <?php echo formatRupiah($total_anggaran); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Subtotal -->
                        <tr class="bg-blue-50 font-semibold">
                            <td colspan="3" class="px-4 py-3 text-right text-sm text-gray-800">
                                Subtotal <?php echo htmlspecialchars($kategori); ?>
                            </td>
                            <td class="px-4 py-3 text-right text-base text-blue-700 font-bold">
                                <?php echo formatRupiah($subtotal_kategori); ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <?php
                    $grand_total_anggaran += $subtotal_kategori;
                endforeach;
            else :
            ?>
            
            <div class="p-6 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                    <div>
                        <h4 class="text-lg font-semibold text-yellow-800 mb-2">Data RAB Tidak Ditemukan</h4>
                        <p class="text-sm text-yellow-700">
                            Tidak ada data Rencana Anggaran Biaya (RAB) untuk kegiatan ini. 
                            Pastikan KAK sudah dibuat dengan lengkap sebelum melakukan pengajuan LPJ.
                        </p>
                    </div>
                </div>
            </div>
            
            <?php endif; ?>
        </div>

        <!-- Section 2: Realisasi Penggunaan Dana (LPJ) -->
        <form id="form-lpj-submit" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="lpj_id" value="<?php echo $lpj_id; ?>">
            <input type="hidden" name="kak_id" value="<?php echo $kak_id; ?>">
            <input type="hidden" name="kegiatan_id" value="<?php echo $kegiatan_data['kegiatanId']; ?>">

            <div class="mb-8">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b-2 border-green-500">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    <h3 class="text-xl font-bold text-gray-800">2. Realisasi Penggunaan Dana (LPJ)</h3>
                </div>

                <!-- Warning Message -->
                <div class="mb-6 p-4 bg-orange-50 border-l-4 border-orange-500 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-orange-600 text-xl mt-0.5"></i>
                        <p class="text-sm text-orange-800">
                            <strong>Penting:</strong> Nilai Realisasi tidak boleh melebihi Total Anggaran yang telah disetujui di atas.
                        </p>
                    </div>
                </div>

                <?php
                $grand_total_realisasi = 0;
                
                foreach ($rab_items as $kategori => $items) :
                    if (empty($items)) continue;
                    
                    $subtotal_realisasi = 0;
                ?>
                
                <!-- Kategori Header -->
                <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3 flex items-center gap-2">
                    <span class="w-1 h-5 bg-green-500 rounded"></span>
                    <?php echo htmlspecialchars($kategori); ?>
                </h4>
                
                <!-- Table Realisasi -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                    <table class="w-full lpj-table" data-kategori="<?php echo htmlspecialchars($kategori); ?>">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Item Kegiatan</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-40">Anggaran (RP)</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-green-600 uppercase w-48">Realisasi (RP)</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase w-32">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($items as $item) :
                                $item_id = $item['rabItemId'] ?? $item['id'];
                                if (empty($item_id)) continue;
                                
                                $anggaran = $item['harga_plan'] ?? $item['totalRencana'] ?? 0;
                                $realisasi = $item['realisasi'] ?? 0;
                                $bukti = $item['fileBukti'] ?? '';
                                $bukti_uploaded = !empty($bukti);
                                
                                $subtotal_realisasi += $realisasi;
                            ?>
                            <tr class="hover:bg-gray-50" data-rab-item-id="<?php echo $item_id; ?>">
                                <!-- Item Kegiatan -->
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-gray-800"><?php echo htmlspecialchars($item['uraian'] ?? '-'); ?></div>
                                    <?php if (!empty($item['rincian'])) : ?>
                                    <div class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($item['rincian']); ?></div>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Anggaran -->
                                <td class="px-4 py-3 text-right">
                                    <span class="text-xs text-gray-500">Rp</span>
                                    <span class="text-sm font-medium text-gray-700"><?php echo number_format($anggaran, 2, ',', '.'); ?></span>
                                </td>
                                
                                <!-- Realisasi -->
                                <td class="px-4 py-3">
                                    <?php if ($can_edit && !$bukti_uploaded) : ?>
                                    <!-- Editable -->
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                        <input type="number" 
                                               name="items[<?php echo $item_id; ?>][realisasi]"
                                               class="realisasi-input w-full pl-8 pr-3 py-2 text-sm text-right border border-green-300 rounded focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                               value="<?php echo number_format($realisasi, 2, '.', ''); ?>" 
                                               max="<?php echo $anggaran; ?>"
                                               step="0.01"
                                               data-max="<?php echo $anggaran; ?>"
                                               data-rab-item-id="<?php echo $item_id; ?>">
                                    </div>
                                    <?php else : ?>
                                    <!-- Read-only -->
                                    <div class="text-right">
                                        <span class="text-xs text-gray-500">Rp</span>
                                        <span class="text-sm font-bold text-green-600"><?php echo number_format($realisasi, 2, ',', '.'); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Bukti Upload -->
                                <td class="px-4 py-3 text-center">
                                    <?php if ($all_bukti_uploaded) : ?>
                                    <!-- Sudah Upload -->
                                    <button type="button" 
                                            class="btn-view-bukti inline-flex items-center gap-2 px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition text-sm"
                                            data-file="<?php echo htmlspecialchars($bukti); ?>"
                                            data-uraian="<?php echo htmlspecialchars($item['uraian']); ?>">
                                        <i class="fas fa-eye"></i> Lihat
                                    </button>
                                    <?php else : ?>
                                    <!-- Upload Button -->
                                    <button type="button" 
                                            class="btn-upload-bukti inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium"
                                            data-rab-item-id="<?php echo $item_id; ?>"
                                            data-uraian="<?php echo htmlspecialchars($item['uraian']); ?>">
                                        <i class="fas fa-upload"></i> Upload
                                    </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php
                    $grand_total_realisasi += $subtotal_realisasi;
                endforeach;
                ?>
            </div>

            <!-- Total Summary -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 mb-6">
                <!-- Total Anggaran -->
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-600 uppercase">Total Anggaran (Rencana)</span>
                        <span class="text-2xl font-bold text-gray-800"><?php echo formatRupiah($grand_total_anggaran); ?></span>
                    </div>
                </div>
                
                <!-- Total Realisasi -->
                <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-green-700 uppercase">Total Realisasi (LPJ)</span>
                        <span id="grand-total-realisasi" class="text-2xl font-bold text-green-600"><?php echo formatRupiah($grand_total_realisasi); ?></span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <?php if ($can_edit) : ?>
            <div class="flex flex-col md:flex-row gap-4 justify-between mt-8 pt-6 border-t border-gray-200">
                <a href="<?php echo $back_url; ?>" 
                   class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                
                <div class="flex gap-3">
                    <button type="button" 
                            id="btn-save-draft"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition font-medium">
                        <i class="fas fa-save"></i> Simpan Draft
                    </button>
                    
                    <button type="submit" 
                            id="btn-submit-lpj"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-semibold shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-paper-plane"></i> Submit LPJ ke Bendahara
                    </button>
                </div>
            </div>
            
            <!-- Info Submit -->
            <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-green-600 text-lg mt-0.5"></i>
                    <div class="text-sm text-green-800">
                        <strong>Catatan:</strong> Pastikan semua bukti sudah diupload dan nilai realisasi sudah benar sebelum menekan tombol <strong>"Submit LPJ ke Bendahara"</strong>. 
                        Setelah di-submit, LPJ akan dikirim ke Bendahara untuk diverifikasi.
                    </div>
                </div>
            </div>
            <?php elseif ($is_menunggu) : ?>
            <div class="mt-8 p-6 bg-yellow-50 border-2 border-yellow-300 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hourglass-half text-yellow-600 text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-yellow-800 text-lg">LPJ Sedang Diverifikasi</h4>
                        <p class="text-sm text-yellow-700 mt-1">LPJ Anda telah disubmit dan sedang menunggu verifikasi dari Bendahara.</p>
                    </div>
                </div>
            </div>
            <?php elseif ($is_setuju) : ?>
            <div class="mt-8 p-6 bg-green-50 border-2 border-green-300 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    <div>
                        <h4 class="font-bold text-green-800 text-lg">LPJ Telah Disetujui</h4>
                        <p class="text-sm text-green-700 mt-1">LPJ Anda telah diverifikasi dan disetujui oleh Bendahara.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </form>

    </section>

</main>

<!-- Modal Upload Bukti -->
<div id="modal-upload-bukti" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 rounded-t-2xl">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-upload"></i> Upload Bukti LPJ
            </h3>
        </div>
        
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Item: <strong id="modal-item-name" class="text-gray-800"></strong>
            </p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File (JPG, PNG, PDF - Max 5MB)
                </label>
                <input type="file" 
                       id="input-file-bukti" 
                       accept="image/jpeg,image/jpg,image/png,application/pdf"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <p class="text-xs text-gray-500 mt-2">Format: JPG, PNG, atau PDF (maksimal 5MB)</p>
            </div>
            
            <div class="flex gap-3">
                <button type="button" 
                        id="btn-cancel-upload"
                        class="flex-1 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition font-medium">
                    Batal
                </button>
                <button type="button" 
                        id="btn-confirm-upload"
                        class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-semibold">
                    Upload
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View Bukti -->
<div id="modal-view-bukti" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-alt"></i> Pratinjau Bukti LPJ
            </h3>
            <button type="button" id="btn-close-view" class="text-white hover:text-gray-200 transition">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        
        <div class="p-6 overflow-auto max-h-[calc(90vh-120px)]">
            <div id="view-bukti-content" class="flex items-center justify-center min-h-[400px]">
                <img id="view-image" class="max-w-full h-auto rounded-lg shadow-lg" style="display: none;">
                <iframe id="view-pdf" class="w-full h-[600px] rounded-lg shadow-lg" style="display: none;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables
const lpjId = <?php echo $lpj_id; ?>;
const baseBuktiUrl = '/docutrack/public/uploads/lpj/';
let currentRabItemId = null;

// Update total realisasi
function updateTotalRealisasi() {
    let total = 0;
    document.querySelectorAll('.realisasi-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    document.getElementById('grand-total-realisasi').textContent = 
        'Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

// Validasi realisasi tidak melebihi anggaran
document.querySelectorAll('.realisasi-input').forEach(input => {
    input.addEventListener('input', function() {
        const max = parseFloat(this.dataset.max);
        const value = parseFloat(this.value);
        
        if (value > max) {
            this.value = max;
            alert('Realisasi tidak boleh melebihi anggaran yang telah disetujui!');
        }
        
        updateTotalRealisasi();
    });
});

// Upload Bukti Handler
document.querySelectorAll('.btn-upload-bukti').forEach(btn => {
    btn.addEventListener('click', function() {
        currentRabItemId = this.dataset.rabItemId;
        const uraian = this.dataset.uraian;
        
        document.getElementById('modal-item-name').textContent = uraian;
        document.getElementById('input-file-bukti').value = '';
        document.getElementById('modal-upload-bukti').classList.remove('hidden');
    });
});

// Cancel Upload
document.getElementById('btn-cancel-upload').addEventListener('click', function() {
    document.getElementById('modal-upload-bukti').classList.add('hidden');
    currentRabItemId = null;
});

// Confirm Upload
document.getElementById('btn-confirm-upload').addEventListener('click', async function() {
    const fileInput = document.getElementById('input-file-bukti');
    const file = fileInput.files[0];
    
    if (!file) {
        alert('Pilih file terlebih dahulu!');
        return;
    }
    
    // Validasi ukuran
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran file maksimal 5MB!');
        return;
    }
    
    // Validasi tipe
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!allowedTypes.includes(file.type)) {
        alert('Format file harus JPG, PNG, atau PDF!');
        return;
    }
    
    // Upload via AJAX
    const formData = new FormData();
    formData.append('file', file);
    formData.append('lpj_id', lpjId);
    formData.append('rab_item_id', currentRabItemId);
    
    try {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';
        
        const response = await fetch('/docutrack/public/admin/pengajuan-lpj/upload-bukti', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Reload dulu, alert setelah reload
            location.reload();
        } else {
            alert('Gagal upload: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat upload!');
    } finally {
        this.disabled = false;
        this.innerHTML = 'Upload';
        document.getElementById('modal-upload-bukti').classList.add('hidden');
    }
});

// View Bukti Handler
document.querySelectorAll('.btn-view-bukti').forEach(btn => {
    btn.addEventListener('click', function() {
        const fileName = this.dataset.file;
        const filePath = baseBuktiUrl + fileName;
        const extension = fileName.split('.').pop().toLowerCase();
        
        const imgElement = document.getElementById('view-image');
        const pdfElement = document.getElementById('view-pdf');
        
        if (extension === 'pdf') {
            imgElement.style.display = 'none';
            pdfElement.style.display = 'block';
            pdfElement.src = filePath;
        } else {
            pdfElement.style.display = 'none';
            imgElement.style.display = 'block';
            imgElement.src = filePath;
        }
        
        document.getElementById('modal-view-bukti').classList.remove('hidden');
    });
});

// Close View Modal
document.getElementById('btn-close-view').addEventListener('click', function() {
    document.getElementById('modal-view-bukti').classList.add('hidden');
});

// Save Draft Handler
document.getElementById('btn-save-draft')?.addEventListener('click', async function() {
    if (!confirm('Simpan perubahan sebagai draft?')) {
        return;
    }
    
    // Collect data items
    const items = [];
    document.querySelectorAll('[data-rab-item-id]').forEach(row => {
        const rabItemId = row.dataset.rabItemId;
        const realisasiInput = row.querySelector('.realisasi-input');
        const realisasi = realisasiInput ? parseFloat(realisasiInput.value) : 0;
        
        items.push({
            id: rabItemId,
            total: realisasi
        });
    });
    
    const formData = new FormData();
    formData.append('kegiatan_id', document.querySelector('input[name="kegiatan_id"]').value);
    formData.append('lpj_id', lpjId);
    formData.append('items', JSON.stringify(items));
    formData.append('save_draft', '1');
    
    try {
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
        
        const response = await fetch('/docutrack/public/admin/pengajuan-lpj/save-draft', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Draft berhasil disimpan!');
            location.reload();
        } else {
            alert('Gagal menyimpan draft: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan draft!');
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="fas fa-save"></i> Simpan Draft';
    }
});

// Submit Form
document.getElementById('form-lpj-submit').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Validasi semua bukti sudah diupload dengan cara yang lebih akurat
    const allRows = document.querySelectorAll('[data-rab-item-id]');
    const uploadButtons = document.querySelectorAll('.btn-upload-bukti');
    
    console.log('Total items:', allRows.length);
    console.log('Items belum upload:', uploadButtons.length);
    
    // Jika masih ada tombol Upload, berarti belum semua diupload
    if (uploadButtons.length > 0) {
        alert('❌ GAGAL SUBMIT!\n\nMohon upload semua bukti terlebih dahulu!\n\nTotal item: ' + allRows.length + '\nBelum upload: ' + uploadButtons.length + ' bukti');
        return;
    }
    
    // Validasi semua realisasi sudah diisi
    let hasEmptyRealisasi = false;
    allRows.forEach(row => {
        const realisasiInput = row.querySelector('.realisasi-input');
        if (realisasiInput) {
            const value = parseFloat(realisasiInput.value);
            if (!value || value <= 0) {
                hasEmptyRealisasi = true;
            }
        }
    });
    
    if (hasEmptyRealisasi) {
        alert('❌ GAGAL SUBMIT!\n\nMohon isi semua nilai realisasi dengan benar!');
        return;
    }
    
    // Konfirmasi
    if (!confirm('🚀 SUBMIT LPJ ke Bendahara?\n\nSetelah di-submit, LPJ akan dikirim ke Bendahara untuk verifikasi.\n\nApakah Anda yakin semua data sudah benar?')) {
        return;
    }
    
    // Collect data items untuk submit
    const items = [];
    allRows.forEach(row => {
        const rabItemId = row.dataset.rabItemId;
        const realisasiInput = row.querySelector('.realisasi-input');
        const realisasi = realisasiInput ? parseFloat(realisasiInput.value) : 0;
        
        items.push({
            id: rabItemId,
            total: realisasi
        });
    });
    
    console.log('Submitting items:', items);
    
    // Create FormData with proper structure
    const formData = new FormData();
    formData.append('kegiatan_id', document.querySelector('input[name="kegiatan_id"]').value);
    formData.append('items', JSON.stringify(items));
    
    try {
        const btnSubmit = document.getElementById('btn-submit-lpj');
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim ke Bendahara...';
        
        const response = await fetch('/docutrack/public/admin/pengajuan-lpj/submit', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ LPJ BERHASIL DISUBMIT!\n\n' + result.message + '\n\nAnda akan diarahkan ke halaman list LPJ.');
            window.location.href = '<?php echo $back_url; ?>';
        } else {
            alert('❌ Gagal submit LPJ:\n\n' + result.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="fas fa-paper-plane"></i> Submit LPJ ke Bendahara';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Terjadi kesalahan saat submit LPJ!');
        document.getElementById('btn-submit-lpj').disabled = false;
        document.getElementById('btn-submit-lpj').innerHTML = '<i class="fas fa-paper-plane"></i> Submit LPJ ke Bendahara';
    }
});

// Initialize
updateTotalRealisasi();
</script>

<style>
.animate-reveal {
    animation: reveal 0.5s ease-out;
}

@keyframes reveal {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    opacity: 1;
}
</style>
