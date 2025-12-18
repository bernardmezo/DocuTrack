<?php
// File: src/views/pages/admin/detail_lpj.php

// --- LOGIKA PHP AWAL ---
$status = $status ?? 'Menunggu';
$is_revisi = (strtolower($status) === 'revisi');
$is_selesai = (strtolower($status) === 'setuju');
$is_menunggu = (strtolower($status) === 'menunggu');

$all_bukti_uploaded = true;
if ($is_menunggu && !empty($rab_items)) {
    foreach ($rab_items as $items) {
        foreach ($items as $item) {
            if (empty($item['bukti_file'])) {
                $all_bukti_uploaded = false;
                break 2;
            }
        }
    }
}

$kegiatan_data = $kegiatan_data ?? [];
$rab_items = $rab_items ?? [];
$back_url = $back_url ?? '/docutrack/public/admin/pengajuan-lpj';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}
$grand_total_plan = 0;
$base_bukti_url = '/docutrack/public/uploads/lpj/';
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail RAB untuk LPJ</h2>
                <p class="text-sm text-gray-500 mt-1">Kegiatan: <strong><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></strong></p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <?php if ($is_selesai): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700"><i class="fas fa-check-circle"></i> Disetujui Bendahara</span>
                <?php elseif ($is_revisi): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800"><i class="fas fa-exclamation-triangle"></i> Perlu Revisi</span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700"><i class="fas fa-hourglass-half"></i> Menunggu Verifikasi</span>
                    <?php if (!$all_bukti_uploaded): ?>
                        <span class="text-xs text-orange-600 font-medium flex items-center gap-1"><i class="fas fa-info-circle"></i> Mohon upload semua bukti terlebih dahulu</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($is_revisi): ?>
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg shadow-sm animate-reveal">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mt-0.5"></i>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-yellow-800 mb-1 uppercase tracking-wider">Catatan Revisi dari Bendahara</h4>
                        <div class="text-sm text-yellow-700 leading-relaxed bg-white/50 p-3 rounded-lg border border-yellow-200 mt-2">
                            <?php echo !empty($kegiatan_data['komentarRevisi']) ? nl2br(htmlspecialchars($kegiatan_data['komentarRevisi'])) : 'Silakan periksa catatan pada tiap item RAB di bawah.'; ?>
                        </div>
                        <p class="text-xs text-yellow-600 mt-3 font-medium italic"><i class="fas fa-info-circle mr-1"></i> Mohon perbaiki data realisasi atau upload ulang bukti sesuai catatan di atas atau pada masing-masing item.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($is_menunggu && !$all_bukti_uploaded): ?>
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-600 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800 mb-1">Informasi Pengajuan LPJ</h4>
                        <p class="text-sm text-blue-700">Silakan upload semua bukti pertanggungjawaban untuk setiap item RAB. Setelah semua bukti terupload, klik tombol <strong>"Ajukan ke Bendahara"</strong> untuk meminta verifikasi.</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form id="form-lpj-submit" action="#" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="kegiatan_id" value="<?php echo $kegiatan_data['kegiatanId'] ?? $kegiatan_data['id'] ?? 0; ?>">
            <input type="hidden" id="lpj_id" value="<?php echo $lpj_id ?? 0; ?>">

            <!-- BAGIAN 1: TABEL RAB (STATIS) -->
            <div class="mb-10 animate-reveal" style="animation-delay: 100ms;">
                <div class="flex items-center gap-3 mb-4 pb-2 border-b-2 border-gray-100">
                    <div class="bg-blue-100 p-2 rounded-lg text-blue-600">
                        <i class="fas fa-file-invoice-dollar text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">1. Rencana Anggaran Biaya (RAB) - Terverifikasi</h3>
                </div>
                
                <?php 
                    if (!empty($rab_items)):
                        foreach ($rab_items as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal_plan = 0;
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3 px-2 border-l-4 border-blue-500"><?php echo htmlspecialchars($kategori); ?></h4>
                    
                    <div class="overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-4/12">Uraian / Rincian</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase w-2/12">Vol & Sat</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-3/12">Harga Satuan (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-3/12">Total Anggaran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($items as $item): 
                                    $plan = $item['harga_plan'] ?? 0;
                                    $subtotal_plan += $plan;
                                    $satuan_komplit = (($item['vol1'] ?? '-') . ' ' . ($item['sat1'] ?? '-')) . ((($item['vol2'] ?? '-') != '-' && ($item['sat2'] ?? '-') != '-') ? ' x ' . ($item['vol2'] ?? '-') . ' ' . ($item['sat2'] ?? '-') : '');
                                ?>
                                <tr class="bg-white">
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($item['uraian'] ?? ''); ?></div>
                                        <div class="text-gray-500 text-xs mt-0.5"><?php echo htmlspecialchars($item['rincian'] ?? '-'); ?></div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-center"><?php echo htmlspecialchars($satuan_komplit); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right"><?php echo number_format($item['harga_satuan'] ?? 0, 0, ',', '.'); ?></td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-800 text-right"><?php echo formatRupiah($plan); ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <tr class="bg-blue-50/50 font-bold border-t border-blue-100">
                                    <td colspan="3" class="px-4 py-3 text-right text-sm text-blue-800">Subtotal <?php echo htmlspecialchars($kategori); ?></td>
                                    <td class="px-4 py-3 text-sm text-blue-900 text-right"><?php echo formatRupiah($subtotal_plan); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; endif; ?>
            </div>

            <!-- BAGIAN 2: TABEL INPUT LPJ -->
            <div class="mb-8 animate-reveal" style="animation-delay: 200ms;">
                <div class="flex items-center gap-3 mb-4 pb-2 border-b-2 border-gray-100">
                    <div class="bg-green-100 p-2 rounded-lg text-green-600">
                        <i class="fas fa-edit text-xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">2. Realisasi Penggunaan Dana (LPJ)</h3>
                </div>

                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-info-circle text-amber-600"></i>
                        <p class="text-sm text-amber-800 font-medium italic">Penting: Nilai Realisasi tidak boleh melebihi Total Anggaran yang telah disetujui di atas.</p>
                    </div>
                </div>
                
                <?php 
                    if (!empty($rab_items)):
                        foreach ($rab_items as $kategori => $items): 
                            if (empty($items)) continue;
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3 px-2 border-l-4 border-green-500"><?php echo htmlspecialchars($kategori); ?></h4>
                    
                    <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-xl shadow-sm">
                        <table class="w-full lpj-input-table" data-kategori="<?php echo htmlspecialchars($kategori); ?>">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase w-4/12">Item Kegiatan</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 uppercase w-2/12">Anggaran (Rp)</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-green-600 uppercase w-2/12">Realisasi (Rp)</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase w-1/12">Bukti</th>
                                    <?php if ($is_revisi || $is_selesai): ?>
                                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase w-3/12">Catatan Bendahara</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($items as $item): 
                                    $item_id = $item['id'] ?? uniqid();
                                    $plan = $item['harga_plan'] ?? 0;
                                    $komentar = $item['komentar'] ?? null;
                                    $has_comment = $is_revisi && !empty($komentar);
                                    $bukti_uploaded = !empty($item['bukti_file']);
                                ?>
                                <tr class="<?php echo $has_comment ? 'bg-yellow-50' : 'bg-white'; ?>" 
                                    data-row-id="<?php echo $item_id; ?>"
                                    data-uraian="<?php echo htmlspecialchars($item['uraian'] ?? ''); ?>"
                                    data-total-plan="<?php echo $plan; ?>"
                                    data-uploaded-file="<?php echo htmlspecialchars($item['bukti_file'] ?? ''); ?>">

                                    <td class="px-3 py-4 text-sm font-medium text-gray-800">
                                        <?php echo htmlspecialchars($item['uraian'] ?? ''); ?>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500 text-right font-medium">
                                        <?php echo number_format($plan, 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-3 py-4 align-middle">
                                        <?php if ($is_menunggu || $is_revisi): ?>
                                            <div class="relative group">
                                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                                <input type="number" 
                                                        class="realisasi-input w-full pl-7 pr-2 py-1.5 text-sm text-right border-2 border-green-100 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" 
                                                        value="<?php echo $plan; ?>" 
                                                        max="<?php echo $plan; ?>"
                                                        min="0" step="1" 
                                                        data-item-id="<?php echo $item_id; ?>">
                                                <div class="error-msg hidden text-[10px] text-red-500 mt-1 absolute right-0">Melebihi anggaran!</div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-right text-sm font-bold text-blue-600"><?php echo formatRupiah($plan); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td class='px-3 py-4 text-center bukti-column'>
                                        <?php if ($bukti_uploaded && !$has_comment): ?>
                                            <button type="button" class="btn-bukti btn-view-bukti bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 shadow-sm transition-all" data-file="<?php echo htmlspecialchars($item['bukti_file']); ?>" data-item-name="<?php echo htmlspecialchars($item['uraian'] ?? 'Item'); ?>"><i class="fas fa-eye"></i> Lihat</button>
                                        <?php else: ?>
                                            <button type="button" class="btn-bukti btn-upload-bukti bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 shadow-sm transition-all <?php echo $has_comment ? 'ring-2 ring-yellow-400' : ''; ?>" data-item-id="<?php echo $item_id; ?>" data-item-name="<?php echo htmlspecialchars($item['uraian'] ?? 'Item'); ?>" <?php echo $is_selesai ? 'disabled' : ''; ?>><i class='fas fa-upload'></i> <?php echo $bukti_uploaded ? 'Ubah' : 'Upload'; ?></button>
                                            <div id="bukti-display-<?php echo $item_id; ?>" class="mt-1 <?php echo $bukti_uploaded ? 'flex' : 'hidden'; ?> items-center justify-center gap-1 text-[10px] font-bold text-green-600"><i class="fas fa-check-circle"></i> FILE OK</div>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($is_revisi || $is_selesai): ?>
                                        <td class="px-3 py-4 text-xs italic <?php echo $has_comment ? 'text-yellow-800 font-medium bg-yellow-100/50 p-2 rounded-lg' : 'text-gray-500'; ?> align-top"><?php echo $has_comment ? htmlspecialchars($komentar) : '-'; ?></td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile View: Input LPJ -->
                    <div class="lg:hidden mt-4 space-y-4">
                        <?php foreach ($items as $item): 
                            $item_id = $item['id'] ?? uniqid();
                            $plan = $item['harga_plan'] ?? 0;
                            $komentar = $item['komentar'] ?? null;
                            $has_comment = $is_revisi && !empty($komentar);
                            $bukti_uploaded = !empty($item['bukti_file']);
                        ?>
                            <div class="p-4 border-2 <?php echo $has_comment ? 'border-yellow-300 bg-yellow-50' : 'border-gray-100 bg-white'; ?> rounded-xl shadow-sm" data-mobile-card-id="<?php echo $item_id; ?>">
                                <div class="font-bold text-gray-800 mb-3"><?php echo htmlspecialchars($item['uraian'] ?? ''); ?></div>
                                
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="bg-gray-50 p-2 rounded-lg">
                                        <span class="text-[10px] text-gray-500 uppercase font-bold block">Anggaran</span>
                                        <span class="text-sm font-bold text-gray-700"><?php echo formatRupiah($plan); ?></span>
                                    </div>
                                    <div class="bg-green-50 p-2 rounded-lg relative">
                                        <span class="text-[10px] text-green-600 uppercase font-bold block">Realisasi</span>
                                        <?php if ($is_menunggu || $is_revisi): ?>
                                            <div class="relative mt-1">
                                                <span class="absolute left-1 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                                <input type="number" 
                                                    class="realisasi-input w-full pl-6 pr-1 py-1 text-sm text-right border-green-200 rounded focus:ring-green-500" 
                                                    value="<?php echo $plan; ?>" max="<?php echo $plan; ?>" min="0" data-item-id="<?php echo $item_id; ?>">
                                            </div>
                                        <?php else: ?>
                                            <span class="text-sm font-bold text-green-700"><?php echo formatRupiah($plan); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center gap-2 bukti-container-mobile">
                                        <?php if ($bukti_uploaded && !$has_comment): ?>
                                            <button type="button" class="btn-view-bukti text-green-600 text-sm font-bold flex items-center gap-1" data-file="<?php echo htmlspecialchars($item['bukti_file']); ?>" data-item-name="<?php echo htmlspecialchars($item['uraian'] ?? 'Item'); ?>"><i class="fas fa-eye"></i> Cek Bukti</button>
                                        <?php else: ?>
                                            <button type="button" class="btn-upload-bukti bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold" data-item-id="<?php echo $item_id; ?>" data-item-name="<?php echo htmlspecialchars($item['uraian'] ?? 'Item'); ?>"><i class="fas fa-upload"></i> <?php echo $bukti_uploaded ? 'Ubah Bukti' : 'Upload Bukti'; ?></button>
                                            <div id="bukti-display-mobile-<?php echo $item_id; ?>" class="<?php echo $bukti_uploaded ? 'block' : 'hidden'; ?> text-green-600 font-bold text-[10px]"><i class="fas fa-check"></i> OK</div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if ($has_comment): ?>
                                        <div class="text-right">
                                            <span class="text-[10px] text-yellow-600 font-bold block">Revisi Bendahara:</span>
                                            <p class="text-[11px] text-yellow-800 italic"><?php echo htmlspecialchars($komentar); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php 
                        endforeach; 
                    else:
                ?>
                    <p class="text-sm text-gray-500 italic">Tidak ada data untuk ditampilkan.</p>
                <?php endif; ?>
                
                <div class="flex flex-wrap justify-end mt-10 gap-4">
                    <div class="p-5 bg-gray-50 rounded-2xl border-2 border-gray-200 w-full xs:w-auto min-w-[280px]">
                        <span class="text-sm font-bold text-gray-500 uppercase tracking-widest block mb-1">Total Anggaran (Rencana)</span>
                        <span class="text-2xl font-black text-gray-800"><?php echo formatRupiah($grand_total_plan); ?></span>
                    </div>
                    
                    <div class="p-5 bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl shadow-xl w-full xs:w-auto min-w-[280px] transform hover:scale-105 transition-transform">
                        <span class="text-sm font-bold text-green-100 uppercase tracking-widest block mb-1">Total Realisasi (LPJ)</span>
                        <span class="text-3xl font-black text-white" id="grand-total-realisasi"><?php echo formatRupiah($grand_total_plan); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row-reverse justify-between items-center mt-12 pt-8 border-t-2 border-gray-100 gap-4">
                <?php if ($is_revisi): ?>
                    <button type="button" id="submit-lpj-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-yellow-600 transition-all transform hover:-translate-y-1 active:scale-95"><i class="fas fa-paper-plane"></i> KIRIM ULANG REVISI LPJ</button>
                <?php elseif ($is_menunggu): ?>
                    <button type="button" id="submit-lpj-btn" 
                             class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-bold px-8 py-4 rounded-xl shadow-lg hover:bg-green-700 transition-all transform hover:-translate-y-1 active:scale-95 <?php echo !$all_bukti_uploaded ? 'opacity-50 cursor-not-allowed grayscale' : ''; ?>"
                             <?php echo !$all_bukti_uploaded ? 'disabled' : ''; ?>><i class="fas fa-check-circle"></i> <?php echo $all_bukti_uploaded ? 'AJUKAN LPJ KE BENDAHARA' : 'UPLOAD SEMUA BUKTI TERLEBIH DAHULU'; ?></button>
                <?php else: ?>
                    <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-100 text-green-700 font-bold px-8 py-4 rounded-xl border-2 border-green-200"><i class="fas fa-check-double"></i> LPJ TELAH DISETUJUI</div>
                <?php endif; ?>
                     
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                     class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-bold px-8 py-4 rounded-xl hover:bg-gray-200 transition-all"><i class="fas fa-arrow-left"></i> KEMBALI</a>
            </div>
        </form>
        
    </section>
</main>

<div id="upload-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1010] hidden opacity-0 transition-opacity duration-300"></div>
<div id="upload-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl z-[1020] w-[90%] max-w-lg hidden opacity-0 scale-95 transition-all duration-300">
    <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-xl">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2"><i class="fas fa-cloud-upload-alt"></i> Upload Bukti Pertanggungjawaban</h3>
        <button id="close-upload-modal-btn" class="text-white hover:text-gray-200 transition-colors"><i class="fas fa-times text-xl"></i></button>
    </div>
    <form id="upload-file-form">
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">Upload bukti (nota, kuitansi, foto) untuk item:</p>
            <p class="text-base font-semibold text-gray-900 mb-6 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-600" id="modal-item-name">...</p>
            <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-lg p-10 text-center cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition-all">
                <i class="fas fa-cloud-upload-alt text-5xl text-gray-400"></i>
                <p class="mt-4 text-sm text-gray-600">Seret & lepas file di sini, atau <span class="font-semibold text-blue-600">klik untuk memilih file</span></p>
                <p class="text-xs text-gray-500 mt-2">Format: PNG, JPG (Maks. 5MB)</p>
                <input type="file" id="file-upload-input" class="hidden" accept="image/png, image/jpeg, image/jpg">
            </div>
            <div id="file-preview-area" class="hidden mt-4">
                <div class="flex items-center justify-between bg-gradient-to-r from-green-50 to-green-100 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-alt text-3xl text-green-600"></i>
                        <div><span id="file-preview-name" class="text-sm font-medium text-gray-800 block">namafile.pdf</span><span class="text-xs text-gray-500">Siap diupload</span></div>
                    </div>
                    <button type="button" id="remove-file-btn" class="text-red-500 hover:text-red-700 text-lg transition-colors"><i class="fas fa-trash-alt"></i></button>
                </div>
            </div>
            <input type="hidden" id="modal-item-id-input" name="item_id" value="">
        </div>
        <div class="flex justify-end p-5 bg-gray-50 border-t border-gray-200 rounded-b-xl gap-3">
            <button type="button" id="cancel-upload-btn" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-all">Batal</button>
            <button type="submit" id="confirm-upload-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2"><i class="fas fa-check"></i> Simpan Bukti</button>
        </div>
    </form>
</div>

<div id="view-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1030] hidden opacity-0 transition-opacity duration-300"></div>
<div id="view-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl z-[1040] w-[95%] max-w-4xl hidden opacity-0 scale-95 transition-all duration-300">
    <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gradient-to-r from-gray-600 to-gray-700 rounded-t-xl">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2"><i class="fas fa-file-alt"></i> Pratinjau Bukti</h3>
        <button id="close-view-modal-btn" class="text-white hover:text-gray-200 transition-colors"><i class="fas fa-times text-xl"></i></button>
    </div>
    <div class="p-6">
        <p class="text-base font-semibold text-gray-900 mb-4 p-3 bg-gray-50 rounded-lg border-l-4 border-gray-600" id="view-modal-item-name">...</p>
        <div class="flex justify-center items-center bg-gray-100 rounded-lg overflow-hidden h-[70vh]">
            <!-- Untuk Gambar -->
            <img id="view-modal-image" src="" alt="Bukti LPJ" class="hidden object-contain max-h-full max-w-full">
            <!-- Untuk PDF -->
            <iframe id="view-modal-pdf" src="" class="hidden w-full h-full" frameborder="0"></iframe>
            
            <div id="view-modal-error" class="hidden flex-col items-center gap-3">
                <i class="fas fa-exclamation-circle text-4xl text-red-500"></i>
                <p class="text-red-600 font-medium">Gagal memuat dokumen atau format tidak didukung.</p>
            </div>
        </div>
        <div class="flex justify-between items-center mt-4">
            <a id="view-modal-download-link" href="#" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-4 py-2 rounded-lg transition-all"><i class="fas fa-download"></i> Unduh Dokumen Asli</a>
            <span class="text-xs text-gray-500 italic">Pastikan file sudah sesuai dengan ketentuan pelaporan.</span>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .7; }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- VARIABLES ---
    const backdrop = document.getElementById('upload-modal-backdrop');
    const modal = document.getElementById('upload-modal-content');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-upload-input');
    const filePreview = document.getElementById('file-preview-area');
    const submitLpjBtn = document.getElementById('submit-lpj-btn');
    const viewModal = document.getElementById('view-modal-content');
    const viewBackdrop = document.getElementById('view-modal-backdrop');
    const uploadForm = document.getElementById('upload-file-form');
    
    let currentItemId = null;
    let selectedFile = null;
    const baseBuktiUrl = '<?php echo $base_bukti_url; ?>';
    const isRevisi = '<?php echo $is_revisi ? 'true' : 'false'; ?>' === 'true';

    // --- UTILITY FUNCTIONS ---
    const formatRupiahJS = (angka) => 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(angka) || 0);

    // --- MODAL UPLOAD/VIEW LOGIC ---
    const openModal = (itemId, itemName) => {
        currentItemId = itemId;
        document.getElementById('modal-item-name').textContent = itemName;
        document.getElementById('modal-item-id-input').value = itemId;
        selectedFile = null;
        fileInput.value = '';
        filePreview.classList.add('hidden');
        dropzone.classList.remove('hidden');

        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.add('opacity-100');
            modal.classList.add('opacity-100', 'scale-100');
            modal.classList.remove('scale-95');
        }, 10);
    };

    const closeModal = () => {
        backdrop.classList.remove('opacity-100');
        modal.classList.remove('opacity-100', 'scale-100');
        modal.classList.add('scale-95');
        setTimeout(() => { backdrop.classList.add('hidden'); modal.classList.add('hidden'); }, 300);
    };
    
    const openViewModal = (fileName, itemName) => {
        const filePath = baseBuktiUrl + fileName;
        const isPdf = fileName.toLowerCase().endsWith('.pdf');
        
        document.getElementById('view-modal-item-name').textContent = itemName;
        document.getElementById('view-modal-download-link').href = filePath;
        
        const img = document.getElementById('view-modal-image');
        const iframe = document.getElementById('view-modal-pdf');
        const errorMsg = document.getElementById('view-modal-error');
        
        // Reset state
        img.classList.add('hidden');
        iframe.classList.add('hidden');
        errorMsg.classList.add('hidden');
        
        if (isPdf) {
            iframe.src = filePath;
            iframe.classList.remove('hidden');
        } else {
            img.src = filePath;
            img.classList.remove('hidden');
            img.onerror = () => {
                img.classList.add('hidden');
                errorMsg.classList.remove('hidden');
                errorMsg.classList.add('flex');
            };
        }

        viewBackdrop.classList.remove('hidden');
        viewModal.classList.remove('hidden');
        setTimeout(() => { 
            viewBackdrop.classList.add('opacity-100'); 
            viewModal.classList.add('opacity-100', 'scale-100'); 
            viewModal.classList.remove('scale-95'); 
        }, 10);
    };

    const closeViewModal = () => {
        const iframe = document.getElementById('view-modal-pdf');
        iframe.src = ''; // Stop PDF loading when closed
        
        viewBackdrop.classList.remove('opacity-100');
        viewModal.classList.remove('opacity-100', 'scale-100');
        viewModal.classList.add('scale-95');
        setTimeout(() => { viewBackdrop.classList.add('hidden'); viewModal.classList.add('hidden'); }, 300);
    };

    // --- CALCULATIONS & SYNCHRONIZATION (PERBAIKAN TOTAL REALISASI) ---
    
    function updateGrandTotalRealisasi() {
        let grandTotal = 0;
        
        // 1. Hitung Total Global: Ambil nilai dari input yang berada di elemen TABLE (desktop) sebagai sumber perhitungan utama
        document.querySelectorAll('.lpj-input-table[data-kategori]').forEach(table => {
            let subtotalKategori = 0;
            
            // Hitung subtotal kategori dari input desktop (sumber utama)
            table.querySelectorAll('.realisasi-input').forEach(inp => { 
                subtotalKategori += parseFloat(inp.value) || 0; 
            });
            
            grandTotal += subtotalKategori;

            // Update Subtotal Desktop (jika ada elemen subtotal-realisasi)
            const subtotalEl = table.querySelector('.subtotal-realisasi');
            if(subtotalEl) subtotalEl.textContent = formatRupiahJS(subtotalKategori);
        });
        
        document.getElementById('grand-total-realisasi').textContent = formatRupiahJS(grandTotal);
    }

    // Fungsi sinkronisasi universal untuk input Realisasi dengan Validasi
    const syncInput = (e) => {
        const itemId = e.target.dataset.itemId;
        const value = parseFloat(e.target.value) || 0;
        
        // Cari baris desktop sebagai referensi data-total-plan
        const desktopRow = document.querySelector(`tr[data-row-id="${itemId}"]`);
        const maxPlan = desktopRow ? parseFloat(desktopRow.dataset.totalPlan) : 0;
        
        let isInvalid = false;
        if (value > maxPlan) {
            isInvalid = true;
        }

        // Cari semua input terkait (desktop & mobile)
        document.querySelectorAll(`.realisasi-input[data-item-id="${itemId}"]`).forEach(input => {
            if (input !== e.target) {
                input.value = value;
            }
            
            // Beri feedback visual
            const errorMsg = input.nextElementSibling;
            if (isInvalid) {
                input.classList.add('border-red-500', 'bg-red-50', 'text-red-700');
                input.classList.remove('border-green-100');
                if (errorMsg && errorMsg.classList.contains('error-msg')) errorMsg.classList.remove('hidden');
            } else {
                input.classList.remove('border-red-500', 'bg-red-50', 'text-red-700');
                input.classList.add('border-green-100');
                if (errorMsg && errorMsg.classList.contains('error-msg')) errorMsg.classList.add('hidden');
            }
        });

        updateGrandTotalRealisasi();
        checkAllBuktiUploaded(); // Re-validate submit button state
    };

    // --- BUKTI UPLOAD / VIEW RENDERER ---
    function updateButtonAndDataset(itemId, filename, uraian) {
        // Update data-uploaded-file di row desktop (sebagai sumber data)
        const rowDesktop = document.querySelector(`tr[data-row-id="${itemId}"]`);
        if (rowDesktop) rowDesktop.dataset.uploadedFile = filename;

        const targets = [
            { selector: `tr[data-row-id="${itemId}"] .bukti-column`, isDesktop: true },
            { selector: `div[data-mobile-card-id="${itemId}"] .bukti-container-mobile`, isDesktop: false }
        ];

        targets.forEach(target => {
            const btnContainer = document.querySelector(target.selector);
            if (!btnContainer) return;

            btnContainer.innerHTML = ''; 
            let btnHtml = '';

            if (target.isDesktop) {
                // Style Desktop
                btnHtml += `<button type="button" class="btn-bukti btn-view-bukti bg-green-500 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-green-600 shadow-sm transition-all" data-file="${filename}" data-item-name="${uraian}"><i class="fas fa-eye"></i> Lihat</button>`;
                if (!'<?php echo $is_selesai; ?>') {
                    btnHtml += `<button type="button" class="btn-bukti btn-upload-bukti bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-blue-700 shadow-sm transition-all mt-1" data-item-id="${itemId}" data-item-name="${uraian}"><i class='fas fa-upload'></i> Ubah</button>`;
                }
            } else {
                // Style Mobile
                btnHtml += `<button type="button" class="btn-view-bukti text-green-600 text-sm font-bold flex items-center gap-1" data-file="${filename}" data-item-name="${uraian}"><i class="fas fa-eye"></i> Cek Bukti</button>`;
                if (!'<?php echo $is_selesai; ?>') {
                    btnHtml += `<button type="button" class="btn-upload-bukti bg-blue-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold" data-item-id="${itemId}" data-item-name="${uraian}"><i class="fas fa-upload"></i> Ubah Bukti</button>`;
                }
                btnHtml += `<div id="bukti-display-mobile-${itemId}" class="block text-green-600 font-bold text-[10px]"><i class="fas fa-check"></i> OK</div>`;
            }

            btnContainer.innerHTML = btnHtml;
        });

        checkAllBuktiUploaded();
    }
            }
        });

        checkAllBuktiUploaded();
    }

    // --- CHECK ALL BUKTI STATUS & VALIDITY ---
    function checkAllBuktiUploaded() {
        const rows = document.querySelectorAll('.lpj-input-table tbody tr[data-row-id]');
        let allUploaded = true;
        let allValidRealisasi = true;
        const isMenunggu = '<?php echo $is_menunggu ? 'true' : 'false'; ?>' === 'true';
        const statusType = '<?php echo strtolower($status); ?>';

        rows.forEach(row => { 
            // 1. Cek Upload
            if (isMenunggu) {
                if (!row.dataset.uploadedFile || row.dataset.uploadedFile === '') allUploaded = false; 
            }

            // 2. Cek Realisasi vs Anggaran
            const realisasiInput = row.querySelector('.realisasi-input');
            if (realisasiInput) {
                const val = parseFloat(realisasiInput.value) || 0;
                const max = parseFloat(row.dataset.totalPlan) || 0;
                if (val > max) allValidRealisasi = false;
            }
        });
       
        if (submitLpjBtn) {
            if (statusType === 'setuju') return;

            if (allUploaded && allValidRealisasi) {
                submitLpjBtn.disabled = false;
                submitLpjBtn.classList.remove('opacity-50', 'cursor-not-allowed', 'grayscale');
                submitLpjBtn.innerHTML = `<i class="fas fa-check-circle"></i> ${statusType === 'revisi' ? 'KIRIM ULANG REVISI LPJ' : 'AJUKAN LPJ KE BENDAHARA'}`;
            } else {
                submitLpjBtn.disabled = true;
                submitLpjBtn.classList.add('opacity-50', 'cursor-not-allowed', 'grayscale');
                
                if (!allValidRealisasi) {
                    submitLpjBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ADA INPUT MELEBIHI ANGGARAN';
                } else if (!allUploaded && isMenunggu) {
                    submitLpjBtn.innerHTML = '<i class="fas fa-check-circle"></i> UPLOAD SEMUA BUKTI TERLEBIH DAHULU';
                }
            }
        }
    }


    // --- INITIAL LISTENERS & CALLS ---

    // Gunakan Event Delegation untuk tombol bukti (Upload & View)
    // Ini menangani tombol yang ada saat load awal MAUPUN yang dibuat dinamis setelah upload
    document.addEventListener('click', (e) => {
        const uploadBtn = e.target.closest('.btn-upload-bukti');
        const viewBtn = e.target.closest('.btn-view-bukti');

        if (uploadBtn) {
            e.preventDefault();
            openModal(uploadBtn.dataset.itemId, uploadBtn.dataset.itemName);
        } else if (viewBtn) {
            e.preventDefault();
            openViewModal(viewBtn.dataset.file, viewBtn.dataset.itemName);
        }
    });

    // Realisasi Listeners
    document.querySelectorAll('.realisasi-input').forEach(input => input.addEventListener('input', syncInput));

    // Listeners Modal Close
    document.getElementById('close-upload-modal-btn').addEventListener('click', closeModal);
    document.getElementById('cancel-upload-btn').addEventListener('click', closeModal);
    document.getElementById('upload-modal-backdrop').addEventListener('click', closeModal);

    document.getElementById('close-view-modal-btn').addEventListener('click', closeViewModal);
    document.getElementById('view-modal-backdrop').addEventListener('click', closeViewModal);


    // Accordion Listeners
    document.querySelectorAll('.toggle-detail-btn').forEach(button => {
        button.addEventListener('click', () => {
            const card = button.closest('[data-mobile-card-id]');
            const detailContent = card.querySelector('.detail-content');
            const icon = button.querySelector('i');
            
            detailContent.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        });
    });

    // --- DRAG & DROP & FILE INPUT ---
    const handleFile = (file) => {
        if (file && (file.type === 'image/png' || file.type === 'image/jpeg' || file.type === 'image/jpg')) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                return;
            }
            selectedFile = file;
            document.getElementById('file-preview-name').textContent = file.name;
            filePreview.classList.remove('hidden');
            filePreview.classList.add('flex');
            dropzone.classList.add('hidden');
        } else {
            alert('Format file tidak didukung. Gunakan PNG atau JPG.');
        }
    };
    
    document.getElementById('remove-file-btn').addEventListener('click', () => {
        selectedFile = null;
        fileInput.value = '';
        filePreview.classList.add('hidden');
        dropzone.classList.remove('hidden');
    });

    dropzone.addEventListener('click', () => fileInput.click());
    ['dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, (e) => {
            e.preventDefault();
            e.stopPropagation();
            if (eventName === 'dragover') { dropzone.classList.add('border-blue-500', 'bg-blue-50'); } 
            else { dropzone.classList.remove('border-blue-500', 'bg-blue-50'); }
            if (eventName === 'drop') { handleFile(e.dataTransfer.files[0]); }
        });
    });
    fileInput.addEventListener('change', (e) => handleFile(e.target.files[0]));


    // --- AJAX UPLOAD BUKTI ---
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!selectedFile) { alert('Pilih file terlebih dahulu'); return; }

        const lpjIdInput = document.getElementById('lpj_id');
        if (!lpjIdInput || !lpjIdInput.value) {
            alert('Kesalahan Sistem: LPJ ID tidak ditemukan. Mohon refresh halaman.');
            return;
        }

        const formData = new FormData();
        formData.append('lpj_id', lpjIdInput.value);
        formData.append('item_id', currentItemId);
        formData.append('file', selectedFile);

        const submitBtn = document.getElementById('confirm-upload-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
        submitBtn.disabled = true;

        try {
            const response = await fetch('/docutrack/public/admin/pengajuan-lpj/upload-bukti', { 
                method: 'POST', 
                body: formData 
            });
            
            const responseText = await response.text();
            let result;
            
            try {
                result = JSON.parse(responseText);
            } catch (e) {
                console.error('Invalid JSON Response:', responseText);
                throw new Error('Server mengirim format data yang tidak valid.');
            }

            if (result.success) {
                updateButtonAndDataset(currentItemId, result.filename, document.getElementById('modal-item-name').textContent);
                alert('Bukti berhasil diupload!');
                closeModal();
            } else {
                alert('Upload Gagal: ' + (result.message || 'Terjadi kesalahan tidak diketahui.'));
            }
        } catch (error) {
            console.error('Fetch Error:', error);
            alert('Gagal mengupload file: ' + error.message);
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    // --- AJAX SUBMIT LPJ ---
    if (submitLpjBtn) {
        submitLpjBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (submitLpjBtn.disabled) {
                if (submitLpjBtn.textContent.includes('Upload Bukti')) alert('Anda harus mengupload semua bukti pertanggungjawaban.');
                return;
            }

            let validRealisasi = true;
            document.querySelectorAll('input.realisasi-input').forEach(input => {
                // Hanya cek input yang ada di tampilan (desktop/card)
                 if (input.closest('table') || input.closest('[data-mobile-card-id]')) {
                    if (!input.value || parseFloat(input.value) < 0 || isNaN(parseFloat(input.value))) validRealisasi = false;
                 }
            });
            if (!validRealisasi) { alert('Mohon isi semua kolom Realisasi dengan nilai yang valid (>= 0).'); return; }

            const confirmationMessage = isRevisi ? 'Apakah Anda yakin semua revisi dan data realisasi sudah benar? Data akan dikirim ulang ke Bendahara.' : 'Apakah Anda yakin semua bukti dan data realisasi sudah benar? Data akan dikirim ke Bendahara.';
            if (!confirm(confirmationMessage)) return;

            const originalButtonText = submitLpjBtn.innerHTML;
            submitLpjBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
            submitLpjBtn.disabled = true;

            const items = [];
            // Ambil data dari baris table input LPJ
            document.querySelectorAll('.lpj-input-table tbody tr[data-row-id]').forEach(row => {
                const realisasiInput = row.querySelector('.realisasi-input'); 
                const realisasiVal = realisasiInput ? parseFloat(realisasiInput.value) : parseFloat(row.dataset.totalPlan);
                const fileBukti = row.dataset.uploadedFile || '';
                
                // Cari kategori dari tabel terluar
                const table = row.closest('table[data-kategori]');
                const kategori = table ? table.dataset.kategori : 'Unknown';

                items.push({
                    id: row.dataset.rowId,
                    kategori: kategori,
                    uraian: row.dataset.uraian,
                    // Rincian lain bisa ditambahkan di sini jika dibutuhkan backend
                    total: realisasiVal,
                    file_bukti: fileBukti
                });
            });

            const payload = new FormData();
            payload.append('kegiatan_id', document.getElementById('kegiatan_id').value);
            payload.append('items', JSON.stringify(items));

            try {
                const response = await fetch('/docutrack/public/admin/pengajuan-lpj/submit', { method: 'POST', body: payload });
                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    window.location.href = '<?php echo htmlspecialchars($back_url); ?>'; 
                } else {
                    alert('Gagal Submit: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan koneksi saat submit LPJ.');
            } finally {
                submitLpjBtn.innerHTML = originalButtonText;
                submitLpjBtn.disabled = false;
            }
        });
    }

    updateGrandTotalRealisasi();
    checkAllBuktiUploaded();
});
</script>