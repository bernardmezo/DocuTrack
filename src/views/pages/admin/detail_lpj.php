<?php
error_log("=== VIEW detail_lpj.php START ===");

// Status sudah lowercase dari controller
$status = trim($status ?? 'draft');
$total_items = $total_items ?? 0;
$uploaded_items = $uploaded_items ?? 0;

error_log("View Variables:");
error_log("  - status: '{$status}'");
error_log("  - total_items: {$total_items}");
error_log("  - uploaded_items: {$uploaded_items}");

// Define status flags
$is_setuju = ($status === 'setuju');
$is_menunggu = ($status === 'menunggu');
$is_siap_submit = ($status === 'siap_submit');
$is_menunggu_upload = ($status === 'menunggu_upload');
$is_belum_ada_item = ($status === 'belum_ada_item');
$is_draft = ($status === 'draft');
$is_revisi = ($status === 'revisi');

// Gabungkan flag untuk kondisi "bisa upload"
$can_upload = ($is_draft || $is_menunggu_upload || $is_siap_submit || $is_revisi || $is_belum_ada_item);

// Tentukan apakah perlu tampilkan info banner
$show_info_banner = (
    ($is_menunggu_upload || $is_belum_ada_item || ($is_draft && $total_items > 0))
    && !$all_bukti_uploaded
);

error_log("View Flags:");
error_log("  - is_setuju: " . ($is_setuju ? 'Y' : 'N'));
error_log("  - is_menunggu: " . ($is_menunggu ? 'Y' : 'N'));
error_log("  - is_siap_submit: " . ($is_siap_submit ? 'Y' : 'N'));
error_log("  - is_menunggu_upload: " . ($is_menunggu_upload ? 'Y' : 'N'));
error_log("  - is_belum_ada_item: " . ($is_belum_ada_item ? 'Y' : 'N'));
error_log("  - can_upload: " . ($can_upload ? 'Y' : 'N'));
error_log("  - show_info_banner: " . ($show_info_banner ? 'Y' : 'N'));

$kegiatan_data = $kegiatan_data ?? [];
$rab_items = $rab_items ?? [];
$back_url = $back_url ?? '/docutrack/public/admin/pengajuan-lpj';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return "Rp " . number_format($angka ?? 0, 0, ',', '.');
    }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail RAB untuk LPJ</h2>
                <p class="text-sm text-gray-500 mt-1">Kegiatan: <strong><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></strong></p>
            </div>
            
            <!-- Status Badge -->
            <div class="flex flex-col items-end gap-2">
                <?php if ($is_setuju) : ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-check-circle"></i> Disetujui Bendahara
                    </span>
                    
                <?php elseif ($is_menunggu) : ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">
                        <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi Bendahara
                    </span>
                    
                <?php elseif ($is_revisi) : ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-700">
                        <i class="fas fa-exclamation-triangle"></i> Perlu Revisi
                    </span>
                    
                <?php elseif ($is_siap_submit) : ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700">
                        <i class="fas fa-check-double"></i> Siap Diajukan
                    </span>
                    
                <?php elseif ($is_menunggu_upload) : ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-700">
                        <i class="fas fa-upload"></i> Menunggu Upload Bukti
                    </span>
                    <?php if (!$all_bukti_uploaded) : ?>
                        <span class="text-xs text-orange-600 font-medium flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> Mohon upload semua bukti terlebih dahulu
                        </span>
                    <?php endif; ?>
                    
                <?php else : // draft or belum_ada_item ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-gray-100 text-gray-700">
                        <i class="fas fa-file"></i> Draft
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if (($is_menunggu_upload || $is_draft) && !$all_bukti_uploaded) : ?>
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
            <!-- HIDDEN INPUT UNTUK JS -->
            <input type="hidden" id="kegiatan_id" value="<?php echo $kegiatan_data['kegiatanId'] ?? $kegiatan_data['id'] ?? 0; ?>">

            <div class="mb-8 animate-reveal" style="animation-delay: 100ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Rencana Anggaran Biaya (RAB)</h3>
                
                <?php
                    $grand_total_plan = 0;
                if (!empty($rab_items)) :
                    foreach ($rab_items as $kategori => $items) :
                        if (empty($items)) {
                            continue;
                        }
                        $subtotal_plan = 0;
                        ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3"><?php echo htmlspecialchars($kategori); ?></h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full min-w-[1200px]" data-kategori="<?php echo htmlspecialchars($kategori); ?>">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase" style="width: 200px;">Uraian</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase" style="width: 180px;">Rincian</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 80px;">Vol 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 90px;">Sat 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 80px;">Vol 2</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 90px;">Sat 2</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 uppercase" style="width: 130px;">Harga Satuan (Rp)</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 uppercase" style="width: 150px;">Total Rencana</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-blue-600 uppercase" style="width: 150px;">Realisasi (Rp)</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 100px;">Bukti</th>
                                <?php if ($is_revisi || $is_selesai) : ?>
                                        <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase" style="width: 250px;">Komentar Verifikator</th>
                                <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                            <?php foreach ($items as $item) :
                                    $item_id = $item['id'] ?? uniqid();
                                    $plan = $item['harga_plan'] ?? 0;
                                    $komentar = $item['komentar'] ?? null;
                                    $has_comment = $is_revisi && !empty($komentar);
                                    $bukti_uploaded = !empty($item['bukti_file']);

                                    $rincian = $item['rincian'] ?? '-';
                                    $vol1 = $item['vol1'] ?? '-';
                                    $sat1 = $item['sat1'] ?? '-';
                                    $vol2 = $item['vol2'] ?? '-';
                                    $sat2 = $item['sat2'] ?? '-';
                                    $harga_satuan = $item['harga_satuan'] ?? 0;

                                    $subtotal_plan += $plan;
                                ?>
                                <tr class="<?php echo $has_comment ? 'bg-yellow-50' : ''; ?>" 
                                    data-lpj-item-id="<?php echo $item_id; ?>"
                                    data-uraian="<?php echo htmlspecialchars($item['uraian'] ?? ''); ?>"
                                    data-uploaded-file="<?php echo htmlspecialchars($item['bukti_file'] ?? ''); ?>">

                                    <td class="px-3 py-3 text-sm text-gray-800 font-medium" style="width: 200px;">
                                        <?php echo htmlspecialchars($item['uraian'] ?? ''); ?>
                                        <?php if ($has_comment) : ?>
                                            <span class="block text-xs text-yellow-600 mt-1">
                                                <i class="fas fa-exclamation-circle"></i> Perlu revisi
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600" style="width: 180px;">
                                        <?php echo htmlspecialchars($rincian); ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600 text-center" style="width: 80px;">
                                        <?php echo htmlspecialchars($vol1); ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600 text-center" style="width: 90px;">
                                        <?php echo htmlspecialchars($sat1); ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600 text-center" style="width: 80px;">
                                        <?php echo htmlspecialchars($vol2); ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600 text-center" style="width: 90px;">
                                        <?php echo htmlspecialchars($sat2); ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600 text-right" style="width: 130px;">
                                        <?php echo number_format($harga_satuan, 0, ',', '.'); ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-600 text-right font-medium" style="width: 150px;">
                                        <?php echo formatRupiah($plan); ?>
                                    </td>

                                    <!-- Kolom Realisasi -->
                                    <td class="px-3 py-3" style="width: 150px;">
                                        <?php if ($is_draft || $is_menunggu_upload || $is_siap_submit) : ?>
                                            <div class="relative">
                                                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs">Rp</span>
                                                <input type="number" 
                                                    class="realisasi-input w-full pl-6 pr-2 py-1 text-sm text-right border border-blue-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                    value="<?php echo $plan; ?>" 
                                                    min="0" 
                                                    step="1"
                                                    data-item-id="<?php echo $item_id; ?>">
                                            </div>
                                        <?php else : ?>
                                            <div class="text-right text-sm font-bold text-blue-600">
                                                <?php echo formatRupiah($item['realisasi'] ?? $plan); ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <!-- Kolom Bukti -->
                                    <td class='px-3 py-3 text-center' style="width: 100px;">
                                        <?php if ($bukti_uploaded && !$has_comment) : ?>
                                            <div class="flex items-center justify-center gap-2 text-green-600">
                                                <i class="fas fa-check-circle"></i>
                                                <span class="text-xs font-medium">Ada</span>
                                            </div>
                                        <?php else : ?>
                                            <button type="button" 
                                                    class="btn-upload-bukti bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors <?php echo $has_comment ? 'ring-2 ring-yellow-400' : ''; ?> <?php echo !$bukti_uploaded ? 'animate-pulse' : ''; ?>" 
                                                    data-lpj-item-id="<?php echo $item_id; ?>"
                                                    data-item-name="<?php echo htmlspecialchars($item['uraian'] ?? 'Item'); ?>"
                                                    <?php echo ($is_setuju || $is_menunggu) ; ?>> <!-- ? 'disabled' : ''; = penting, disable jika statusnya is_setuju-->
                                                <i class='fas fa-upload'></i>
                                            </button>
                                            <div id="bukti-display-<?php echo $item_id; ?>" 
                                                class="<?php echo $bukti_uploaded ? 'flex' : 'hidden'; ?> items-center justify-center gap-2 text-green-600">
                                                <i class="fas fa-check-circle"></i>
                                                <span class="text-xs font-medium">Ada</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <?php if ($is_revisi || $is_selesai) : ?>
                                        <td class="px-3 py-3 text-xs italic <?php echo $has_comment ? 'text-yellow-800 font-medium' : 'text-gray-500'; ?>" style="width: 250px;">
                                            <?php echo $has_comment ? htmlspecialchars($komentar) : '-'; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach;
                            $grand_total_plan += $subtotal_plan; ?>
                                
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal <?php echo htmlspecialchars($kategori); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-600 text-right"><?php echo formatRupiah($subtotal_plan); ?></td>
                                    <td class="px-4 py-3 text-sm text-blue-700 text-right subtotal-realisasi" data-subtotal-realisasi="<?php echo $subtotal_plan; ?>"><?php echo formatRupiah($subtotal_plan); ?></td>
                                    <td colspan="<?php echo ($is_revisi || $is_selesai) ? '2' : '1'; ?>"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                        <?php
                    endforeach;
                else :
                    ?>
                    <p class="text-sm text-gray-500 italic">Tidak ada data RAB untuk ditampilkan.</p>
                <?php endif; ?>
                
                <div class="flex justify-end mt-6 gap-4">
                    <!-- Total Rencana -->
                    <div class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 p-5 bg-gray-50 rounded-xl border border-gray-200 w-full md:w-auto min-w-[300px]">
                        <span class="text-lg font-semibold text-gray-600">Total Rencana:</span>
                        <span class="text-2xl font-bold text-gray-700 text-right"><?php echo formatRupiah($grand_total_plan); ?></span>
                    </div>
                    
                    <!-- Total Realisasi -->
                    <div class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 w-full md:w-auto min-w-[350px]">
                        <span class="text-lg font-semibold text-gray-800">Total Realisasi:</span>
                        <span class="text-2xl font-bold text-blue-600 text-right" id="grand-total-realisasi"><?php echo formatRupiah($grand_total_plan); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row-reverse justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                 
                 <?php if ($is_revisi) : ?>
                    <button type="button" id="submit-lpj-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-paper-plane"></i> Submit Revisi LPJ
                    </button>
                 <?php elseif ($is_menunggu) : ?>
                    <button type="button" id="submit-lpj-btn" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5 <?php echo !$all_bukti_uploaded ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                            <?php echo !$all_bukti_uploaded ? 'disabled' : ''; ?>>
                         <i class="fas fa-check-circle"></i> 
                         <?php echo $all_bukti_uploaded ? 'Ajukan ke Bendahara' : 'Upload Bukti Terlebih Dahulu'; ?>
                    </button>
                 <?php else : // Status 'Setuju' ?>
                    <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md opacity-70 cursor-not-allowed">
                         <i class="fas fa-check-double"></i> LPJ Telah Disetujui
                    </div>
                 <?php endif; ?>
                 
                 <a href="<?php echo htmlspecialchars($back_url); ?>" 
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                     <i class="fas fa-arrow-left"></i> Kembali
                 </a>
            </div>
        </form>
        
    </section>
</main>

<!-- Upload Modal -->
<div id="upload-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1010] hidden opacity-0 transition-opacity duration-300"></div>
<div id="upload-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-xl shadow-2xl z-[1020] w-[90%] max-w-lg hidden opacity-0 scale-95 transition-all duration-300">
    <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-xl">
        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
            <i class="fas fa-cloud-upload-alt"></i> Upload Bukti Pertanggungjawaban
        </h3>
        <button id="close-upload-modal-btn" class="text-white hover:text-gray-200 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
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
                        <div>
                            <span id="file-preview-name" class="text-sm font-medium text-gray-800 block">namafile.pdf</span>
                            <span class="text-xs text-gray-500">Siap diupload</span>
                        </div>
                    </div>
                    <button type="button" id="remove-file-btn" class="text-red-500 hover:text-red-700 text-lg transition-colors">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
            </div>
            <input type="hidden" id="modal-item-id-input" name="item_id" value="">
        </div>
        <div class="flex justify-end p-5 bg-gray-50 border-t border-gray-200 rounded-b-xl gap-3">
            <button type="button" id="cancel-upload-btn" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-all">
                Batal
            </button>
            <button type="submit" id="confirm-upload-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fas fa-check"></i> Simpan Bukti
            </button>
        </div>
    </form>
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
// Di detail_lpj.php bagian <script>, ganti bagian upload dengan:

document.addEventListener('DOMContentLoaded', () => {
    // --- MODAL VARIABLES ---
    const backdrop = document.getElementById('upload-modal-backdrop');
    const modal = document.getElementById('upload-modal-content');
    const closeBtn = document.getElementById('close-upload-modal-btn');
    const cancelBtn = document.getElementById('cancel-upload-btn');
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('file-upload-input');
    const filePreview = document.getElementById('file-preview-area');
    const filePreviewName = document.getElementById('file-preview-name');
    const removeFileBtn = document.getElementById('remove-file-btn');
    const uploadForm = document.getElementById('upload-file-form');
    const modalItemName = document.getElementById('modal-item-name');
    const modalItemIdInput = document.getElementById('modal-item-id-input');
    const submitLpjBtn = document.getElementById('submit-lpj-btn');
    
    let selectedFile = null;
    let currentItemId = null;

    // --- MODAL FUNCTIONS ---
    function openModal(itemId, itemName) {
        console.log('Opening modal for item:', itemId, itemName); // Debug
        currentItemId = itemId;
        modalItemName.textContent = itemName;
        modalItemIdInput.value = itemId;
        backdrop.classList.remove('hidden');
        modal.classList.remove('hidden');
        setTimeout(() => {
            backdrop.classList.add('opacity-100');
            modal.classList.add('opacity-100', 'scale-100');
            modal.classList.remove('scale-95');
        }, 10);
    }

    function closeModal() {
        backdrop.classList.remove('opacity-100');
        modal.classList.remove('opacity-100', 'scale-100');
        modal.classList.add('scale-95');
        setTimeout(() => {
            backdrop.classList.add('hidden');
            modal.classList.add('hidden');
            resetForm();
        }, 300);
    }

    function resetForm() {
        selectedFile = null;
        fileInput.value = '';
        filePreview.classList.add('hidden');
    }

    function handleFile(file) {
        if (file && (file.type === 'image/png' || file.type === 'image/jpeg' || file.type === 'image/jpg')) {
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file maksimal 5MB');
                return;
            }
            selectedFile = file;
            filePreviewName.textContent = file.name;
            filePreview.classList.remove('hidden');
        } else {
            alert('Format file tidak didukung. Gunakan PNG atau JPG.');
        }
    }

    // ✅ PERBAIKAN: Event listener untuk tombol upload
    document.querySelectorAll('.btn-upload-bukti').forEach(btn => {
        btn.addEventListener('click', () => {
            // Ambil dari data-lpj-item-id, bukan data-item-id
            const itemId = btn.dataset.lpjItemId || btn.dataset.itemId;
            const itemName = btn.dataset.itemName;
            
            console.log('Button clicked - itemId:', itemId, 'itemName:', itemName); // Debug
            
            if (!itemId) {
                alert('Error: Item ID tidak ditemukan');
                return;
            }
            
            openModal(itemId, itemName);
        });
    });

    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    backdrop.addEventListener('click', closeModal);

    // --- DRAG & DROP ---
    dropzone.addEventListener('click', () => fileInput.click());
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-blue-500', 'bg-blue-50');
    });
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
    });
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        const file = e.dataTransfer.files[0];
        handleFile(file);
    });

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        handleFile(file);
    });

    removeFileBtn.addEventListener('click', resetForm);

    // ✅ PERBAIKAN: AJAX UPLOAD dengan FormData yang benar
    uploadForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        if (!selectedFile) {
            alert('Pilih file terlebih dahulu');
            return;
        }
        
        if (!currentItemId) {
            alert('Error: Item ID tidak valid');
            return;
        }

        const formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('item_id', currentItemId); // ✅ Kirim item_id

        const submitBtn = document.getElementById('confirm-upload-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengupload...';
        submitBtn.disabled = true;

        try {
            console.log('Uploading file for item:', currentItemId); // Debug
            
            const response = await fetch('/docutrack/public/admin/pengajuan-lpj/upload-bukti', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            console.log('Upload response:', result); // Debug

            if (result.success) {
                // Update UI Row
                const row = document.querySelector(`tr[data-lpj-item-id="${currentItemId}"]`);
                if (row) {
                    row.dataset.uploadedFile = result.filename;
                    
                    // Update visual buttons
                    const uploadBtn = row.querySelector('.btn-upload-bukti');
                    const displayArea = document.getElementById(`bukti-display-${currentItemId}`);
                    
                    if (uploadBtn && displayArea) {
                        uploadBtn.classList.add('hidden');
                        displayArea.classList.remove('hidden');
                        displayArea.classList.add('flex');
                    }
                }

                alert('Bukti berhasil diupload!');
                closeModal();
                checkAllBuktiUploaded();

            } else {
                alert('Upload Gagal: ' + result.message);
            }

        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat upload file.');
        } finally {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });

    // --- CHECK ALL BUKTI ---
    function checkAllBuktiUploaded() {
        const rows = document.querySelectorAll('tbody tr[data-lpj-item-id]');
        let allUploaded = true;

        rows.forEach(row => {
            if (!row.dataset.uploadedFile || row.dataset.uploadedFile === '') {
                allUploaded = false;
            }
        });

        if (submitLpjBtn) {
            submitLpjBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                
                if (submitLpjBtn.disabled) return;

                // Validasi semua bukti sudah terupload
                const rows = document.querySelectorAll('tbody tr[data-lpj-item-id]');
                let allUploaded = true;
                let missingCount = 0;
                
                rows.forEach(row => {
                    if (!row.dataset.uploadedFile || row.dataset.uploadedFile === '') {
                        allUploaded = false;
                        missingCount++;
                    }
                });

                if (!allUploaded) {
                    alert(`Masih ada ${missingCount} bukti yang belum diupload. Mohon upload semua bukti terlebih dahulu.`);
                    return;
                }

                // Validasi Realisasi
                const realisasiInputs = document.querySelectorAll('.realisasi-input');
                let validRealisasi = true;
                
                realisasiInputs.forEach(input => {
                    const val = parseFloat(input.value);
                    if (isNaN(val) || val < 0) {
                        validRealisasi = false;
                    }
                });

                if (!validRealisasi) {
                    alert('Mohon isi semua kolom Realisasi dengan nilai yang valid (>= 0).');
                    return;
                }

                if (!confirm('Apakah Anda yakin semua bukti dan data realisasi sudah benar? Data akan dikirim ke Bendahara untuk verifikasi.')) {
                    return;
                }

                // Disable button dan update text
                submitLpjBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                submitLpjBtn.disabled = true;

                // ✅ PERBAIKAN: Kumpulkan data dengan format yang benar
                const items = [];
                
                rows.forEach(row => {
                    const lpjItemId = row.dataset.lpjItemId;
                    const uraian = row.dataset.uraian;
                    const uploadedFile = row.dataset.uploadedFile;
                    
                    // Ambil realisasi dari input
                    const realisasiInput = row.querySelector('.realisasi-input');
                    let realisasi = 0;
                    
                    if (realisasiInput) {
                        realisasi = parseFloat(realisasiInput.value) || 0;
                    } else {
                        // Jika tidak ada input (status selain draft/revisi), ambil dari text
                        const realisasiText = row.querySelector('td:nth-child(9)'); // Kolom realisasi
                        if (realisasiText) {
                            const textContent = realisasiText.textContent.trim();
                            realisasi = parseFloat(textContent.replace(/[^0-9]/g, '')) || 0;
                        }
                    }
                    
                    console.log('Item data:', {
                        lpjItemId,
                        uraian,
                        realisasi,
                        uploadedFile
                    });

                    items.push({
                        lpj_item_id: lpjItemId,
                        uraian: uraian,
                        realisasi: realisasi,
                        file_bukti: uploadedFile
                    });
                });

                const kegiatanId = document.getElementById('kegiatan_id').value;

                console.log('Submitting data:', {
                    kegiatan_id: kegiatanId,
                    total_items: items.length,
                    items: items
                });

                // ✅ Kirim data ke server
                const payload = new FormData();
                payload.append('kegiatan_id', kegiatanId);
                payload.append('items', JSON.stringify(items));

                try {
                    const response = await fetch('/docutrack/public/admin/pengajuan-lpj/submit', {
                        method: 'POST',
                        body: payload
                    });

                    const result = await response.json();
                    console.log('Server response:', result);

                    if (result.success) {
                        alert(result.message || 'LPJ berhasil diajukan ke Bendahara!');
                        window.location.href = '/docutrack/public/admin/pengajuan-lpj';
                    } else {
                        alert('Gagal Submit: ' + (result.message || 'Terjadi kesalahan'));
                        submitLpjBtn.disabled = false;
                        submitLpjBtn.innerHTML = '<i class="fas fa-check-circle"></i> Ajukan ke Bendahara';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan koneksi: ' + error.message);
                    submitLpjBtn.disabled = false;
                    submitLpjBtn.innerHTML = '<i class="fas fa-check-circle"></i> Ajukan ke Bendahara';
                }
            });
        }
    }

    // --- REALISASI CALCULATION (tetap sama) ---
    const realisasiInputs = document.querySelectorAll('.realisasi-input');
    const grandTotalRealisasiEl = document.getElementById('grand-total-realisasi');

    function formatRupiahJS(angka) {
        return 'Rp ' + new Intl.NumberFormat('id-ID').format(angka);
    }

    function updateGrandTotalRealisasi() {
        let grandTotal = 0;
        
        realisasiInputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            grandTotal += val;
        });
        
        document.querySelectorAll('table[data-kategori]').forEach(table => {
            let subtotalKategori = 0;
            table.querySelectorAll('.realisasi-input').forEach(inp => {
                subtotalKategori += parseFloat(inp.value) || 0;
            });
            const subtotalEl = table.querySelector('.subtotal-realisasi');
            if(subtotalEl) subtotalEl.textContent = formatRupiahJS(subtotalKategori);
        });

        if (grandTotalRealisasiEl) grandTotalRealisasiEl.textContent = formatRupiahJS(grandTotal);
    }

    realisasiInputs.forEach(input => {
        input.addEventListener('input', updateGrandTotalRealisasi);
    });

    // Initial check on load
    checkAllBuktiUploaded();
});
</script>
