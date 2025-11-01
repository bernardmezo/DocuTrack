<?php
// File: src/views/pages/admin/detail_lpj.php

// --- PERBAIKAN STATUS DI SINI ---
$status = $status ?? 'Menunggu';
$is_revisi = (strtolower($status) === 'revisi'); // Diubah dari 'revisi lpj'
$is_selesai = (strtolower($status) === 'setuju'); // Diubah dari 'selesai'
$is_menunggu = (strtolower($status) === 'menunggu');
// --- AKHIR PERBAIKAN ---

$kegiatan_data = $kegiatan_data ?? [];
$rab_items = $rab_items ?? [];
$back_url = $back_url ?? '/docutrack/public/admin/pengajuan-lpj';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "RP " . number_format($angka ?? 0, 0, ',', '.'); }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail RAB untuk LPJ</h2>
                <p class="text-sm text-gray-500 mt-1">Kegiatan: <strong><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></strong></p>
            </div>
             <div>
                <?php if ($is_selesai): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-check-circle"></i> Setuju
                    </span>
                <?php elseif ($is_revisi): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                        <i class="fas fa-exclamation-triangle"></i> Perlu Revisi
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">
                        <i class="fas fa-hourglass-half"></i> Menunggu
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <form id="form-lpj-submit" action="#" method="POST" enctype="multipart/form-data">
            
            <div class="mb-8 animate-reveal" style="animation-delay: 100ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Perbandingan Rencana Anggaran vs Realisasi</h3>
                
                <?php 
                    $grand_total_plan = 0;
                    $grand_total_realisasi = 0;
                    if (!empty($rab_items)):
                        foreach ($rab_items as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal_plan = 0;
                            $subtotal_realisasi = 0;
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2"><?php echo htmlspecialchars($kategori); ?></h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full min-w-[900px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-[25%]">Uraian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-[15%]">RAB Disetujui (Plan)</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-[15%]">Realisasi (Actual)</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-[15%]">Bukti (Proof)</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-[10%]">Selisih</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-[20%]">Komentar Verifikator</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($items as $item): 
                                    $item_id = $item['id'] ?? uniqid();
                                    $plan = $item['harga_plan'] ?? 0;
                                    $realisasi = $item['harga_realisasi'] ?? 0;
                                    $selisih = $plan - $realisasi;
                                    $komentar = $item['komentar'] ?? null;
                                    $has_comment = $is_revisi && !empty($komentar); // Cek jika revisi dan ada komentar

                                    $subtotal_plan += $plan;
                                    $subtotal_realisasi += $realisasi;
                                ?>
                                <tr class="<?php echo $has_comment ? 'bg-yellow-50' : ''; ?>">
                                    <td class="px-4 py-3 text-sm text-gray-800 font-medium"><?php echo htmlspecialchars($item['uraian'] ?? ''); ?></td>
                                    
                                    <td class="px-4 py-3 text-sm text-gray-600"><?php echo formatRupiah($plan); ?></td>
                                    
                                    <td class="px-4 py-3">
                                        <input type="number" name="realisasi[<?php echo $item_id; ?>]" 
                                               class="realisasi-input w-32 text-sm p-2 border-gray-300 rounded-md <?php echo $has_comment ? 'border-yellow-500 ring-2 ring-yellow-300' : ''; ?>" 
                                               value="<?php echo htmlspecialchars($realisasi); ?>" 
                                               data-plan="<?php echo $plan; ?>"
                                               <?php echo !$is_revisi ? 'readonly' : ''; // Hanya bisa diedit jika revisi ?>>
                                    </td>

                                    <td class='px-4 py-3 text-sm text-center'>
                                        <?php if (!empty($item['bukti_file']) && !$has_comment): // Jika ada file DAN tidak revisi ?>
                                            <div class="flex items-center justify-center gap-2 text-green-600">
                                                <i class="fas fa-check-circle"></i>
                                                <span class="text-xs font-medium">Terupload</span>
                                            </div>
                                        <?php else: // Jika REVISI atau BELUM UPLOAD ?>
                                            <button type="button" class="btn-upload-bukti bg-blue-100 text-blue-700 px-3 py-1 rounded-md text-xs font-medium hover:bg-blue-200 transition-colors <?php echo $has_comment ? 'ring-2 ring-yellow-400' : ''; ?>" 
                                                    data-item-id="<?php echo $item_id; ?>" 
                                                    data-item-name="<?php echo htmlspecialchars($item['uraian'] ?? 'Item'); ?>"
                                                    <?php echo $is_selesai ? 'disabled' : ''; // Nonaktifkan jika sudah Selesai ?>>
                                                <i class='fas fa-upload mr-1'></i> <?php echo empty($item['bukti_file']) ? 'Upload' : 'Ganti'; ?>
                                            </button>
                                            <div id="bukti-display-<?php echo $item_id; ?>" class="hidden items-center justify-center gap-2 text-green-600">
                                                <i class="fas fa-check-circle"></i>
                                                <span class="text-xs font-medium">Terupload</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="selisih-cell px-4 py-3 text-sm font-medium whitespace-nowrap">
                                        <?php echo formatRupiah($selisih); ?>
                                    </td>

                                    <td class="px-4 py-3 text-xs text-yellow-800 italic">
                                        <?php if ($has_comment) echo htmlspecialchars($komentar); ?>
                                    </td>
                                </tr>
                                <?php endforeach; $grand_total_plan += $subtotal_plan; $grand_total_realisasi += $subtotal_realisasi; ?>
                                
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="4" class="px-4 py-2 text-right text-sm text-gray-800">Subtotal</td>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?php echo formatRupiah($subtotal_plan); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?php echo formatRupiah($subtotal_realisasi); ?></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php 
                        endforeach; 
                    else:
                ?>
                    <p class="text-sm text-gray-500 italic">Tidak ada data RAB untuk ditampilkan.</p>
                <?php endif; ?>
                
                <div class="flex justify-end mt-4">
                    <div class="grid grid-cols-3 gap-x-6 p-4 bg-blue-50 rounded-lg w-full md:w-auto">
                        <span class="text-sm font-medium text-gray-700">Total RAB (Plan):</span>
                        <span class="text-xl font-bold text-gray-900 col-span-2" id="grand-total-plan"><?php echo formatRupiah($grand_total_plan); ?></span>
                        
                        <span class="text-sm font-medium text-gray-700">Total Realisasi:</span>
                        <span class="text-xl font-bold text-blue-600 col-span-2" id="grand-total-realisasi"><?php echo formatRupiah($grand_total_realisasi); ?></span>
                        
                        <span class="text-sm font-medium text-gray-700">Total Selisih:</span>
                        <span class="text-xl font-bold text-gray-900 col-span-2" id="grand-total-selisih"><?php echo formatRupiah($grand_total_plan - $grand_total_realisasi); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row-reverse justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                 
                 <?php if ($is_revisi): ?>
                    <button type="submit" id="submit-lpj-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-save text-xs"></i> Submit Revisi LPJ
                    </button>
                 <?php elseif ($is_menunggu): ?>
                    <button type="submit" id="submit-lpj-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5">
                         <i class="fas fa-check-circle text-xs"></i> Submit LPJ
                    </button>
                 <?php else: // Status 'Setuju' ?>
                    <button type="button" id="submit-lpj-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-300 opacity-70 cursor-not-allowed" disabled>
                         <i class="fas fa-check-double text-xs"></i> LPJ Telah Disetujui
                    </button>
                 <?php endif; ?>
                 
                 <a href="<?php echo htmlspecialchars($back_url); ?>" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                     <i class="fas fa-arrow-left text-xs"></i> Kembali
                 </a>
            </div>
        </form>
        
    </section>
</main>

<div id="upload-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1010] hidden opacity-0 transition-opacity duration-300"></div>
<div id="upload-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-xl z-[1020] w-[90%] max-w-lg hidden opacity-0 scale-95 transition-all duration-300">
    <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Upload Bukti Pertanggungjawaban</h3>
        <button id="close-upload-modal-btn" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times text-xl"></i></button>
    </div>
    <form id="upload-file-form">
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Upload bukti (nota, kuitansi, foto) untuk item: <strong id="modal-item-name" class="text-gray-900">...</strong></p>
            
            <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-lg p-10 text-center cursor-pointer hover:border-blue-500 transition-colors">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400"></i>
                <p class="mt-4 text-sm text-gray-600">Seret & lepas file di sini, atau <span class="font-semibold text-blue-600">klik untuk memilih file</span></p>
                <p class="text-xs text-gray-500 mt-2">Format: PNG, JPG, PDF (Maks. 5MB)</p>
                <input type="file" id="file-upload-input" class="hidden" accept="image/png, image/jpeg, .pdf">
            </div>

            <div id="file-preview-area" class="hidden mt-4">
                <div class="flex items-center justify-between bg-gray-100 rounded-lg p-3">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-alt text-2xl text-gray-500"></i>
                        <span id="file-preview-name" class="text-sm font-medium text-gray-800">namafile.pdf</span>
                    </div>
                    <button type="button" id="remove-file-btn" class="text-red-500 hover:text-red-700 text-sm"><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <input type="hidden" id="modal-item-id-input" name="item_id" value="">
        </div>
        <div class="flex justify-end p-4 bg-gray-50 border-t border-gray-200 rounded-b-lg gap-3">
            <button type="button" id="cancel-upload-btn" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-all">Batal</button>
            <button type="submit" id="confirm-upload-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all">Simpan Bukti</button>
        </div>
    </form>
</div>
<script url="/docutrack/public/assets/js/page-scripts/detail-lpj.js"></script>
    <script>
        
         
    
</script>