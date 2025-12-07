<?php
// File: src/views/pages/admin/detail_kak.php
// PART 1 of 3 - PHP Setup & Functions (IKU dengan Modal)

$status = $status ?? 'Menunggu';
$user_role = $user_role ?? 'admin';

$is_revisi = (strtolower($status) === 'revisi');
$is_disetujui = (strtolower($status) === 'disetujui' || strtolower($status) === 'usulan disetujui');
$is_ditolak = (strtolower($status) === 'ditolak');

$komentar_revisi = $komentar_revisi ?? [];
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/admin/dashboard'; 

$surat_pengantar = $kegiatan_data['surat_pengantar'] ?? '';
$tanggal_mulai = $kegiatan_data['tanggal_mulai'] ?? '';
$tanggal_selesai = $kegiatan_data['tanggal_selesai'] ?? '';
$surat_pengantar_url = $surat_pengantar_url ?? '';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { 
        return "Rp " . number_format($angka ?? 0, 0, ',', '.'); 
    }
}

function showCommentIcon($field_name, $komentar_list, $is_revisi_mode) {
    if ($is_revisi_mode && isset($komentar_list[$field_name])) {
        $comment = htmlspecialchars($komentar_list[$field_name]);
        echo "<span class='comment-icon-wrapper relative inline-flex items-center ml-2 group'>";
        echo "<span class='comment-icon flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 border-2 border-yellow-400 cursor-pointer transition-all duration-300 hover:bg-yellow-200 hover:scale-110 hover:shadow-lg hover:shadow-yellow-200'>";
        echo "<i class='fas fa-comment-dots text-yellow-600 text-sm group-hover:animate-pulse'></i>";
        echo "</span>";
        echo "<span class='comment-tooltip invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-1/2 -translate-x-1/2 bottom-full mb-3 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50'>";
        echo "<span class='flex items-center gap-2 text-yellow-400 font-semibold mb-1'><i class='fas fa-exclamation-circle'></i> Catatan Revisi</span>";
        echo "<span class='block text-gray-200 leading-relaxed'>{$comment}</span>";
        echo "<span class='absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-gray-900'></span>";
        echo "</span>";
        echo "</span>";
    }
}

function isEditable($field_name, $is_revisi_mode, $komentar_list) {
    return $is_revisi_mode && isset($komentar_list[$field_name]);
}

function renderField($label, $value, $field_name, $is_revisi, $komentar_list, $input_name = null, $type = 'text', $classes = '') {
    $is_editable = isEditable($field_name, $is_revisi, $komentar_list);
    $input_name = $input_name ?? $field_name;
    $base_classes = $classes ?: 'p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm';
    
    echo "<div>";
    echo "<label class='block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2'>";
    echo htmlspecialchars($label);
    showCommentIcon($field_name, $komentar_list, $is_revisi);
    echo "</label>";
    
    if ($is_editable) {
        echo "<input type='{$type}' name='{$input_name}' value='" . htmlspecialchars($value) . "' ";
        echo "class='{$base_classes} ring-2 ring-yellow-400 focus:ring-yellow-500 focus:border-yellow-500'>";
    } else {
        echo "<div class='{$base_classes}'>" . htmlspecialchars($value) . "</div>";
    }
    echo "</div>";
}

function renderTextarea($label, $value, $field_name, $is_revisi, $komentar_list, $input_name = null, $min_height = '100px') {
    $is_editable = isEditable($field_name, $is_revisi, $komentar_list);
    $input_name = $input_name ?? $field_name;
    $base_classes = "p-4 rounded-lg border text-sm leading-relaxed w-full";
    
    echo "<div class='mb-6'>";
    echo "<label class='block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2'>";
    echo htmlspecialchars($label);
    showCommentIcon($field_name, $komentar_list, $is_revisi);
    echo "</label>";
    
    if ($is_editable) {
        echo "<textarea name='{$input_name}' rows='5' style='min-height: {$min_height}' ";
        echo "class='{$base_classes} bg-yellow-50 border-yellow-400 ring-2 ring-yellow-400 text-gray-800 focus:ring-yellow-500 focus:border-yellow-500'>";
        echo htmlspecialchars($value);
        echo "</textarea>";
    } else {
        echo "<div class='{$base_classes} bg-gray-50 border-gray-200 text-gray-700' style='min-height: {$min_height}'>";
        echo nl2br(htmlspecialchars($value));
        echo "</div>";
    }
    echo "</div>";
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                    <?php elseif ($is_revisi): ?> <span class="font-semibold text-yellow-600">Perlu Revisi</span>
                    <?php elseif ($is_ditolak): ?> <span class="font-semibold text-red-600">Ditolak</span>
                    <?php else: ?> <span class="font-semibold text-gray-600"><?= htmlspecialchars($status); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Alert Revisi -->
        <?php if ($is_revisi && !empty($komentar_revisi)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-yellow-800">Perlu Revisi</h3>
                    <p class="text-sm text-yellow-700 mt-1">Harap perbaiki bagian yang ditandai dengan ikon komentar:</p>
                </div>
            </div>
            <ul class="list-disc list-inside mt-4 pl-10 space-y-1 text-sm text-yellow-700">
                <?php foreach ($komentar_revisi as $field => $komentar): ?>
                    <li><span class="font-semibold"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</span> <?= htmlspecialchars($komentar); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Alert Ditolak -->
        <?php if ($is_ditolak): ?>
        <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-red-800">Usulan Ditolak</h3>
                    <p class="text-sm text-red-700 mt-1">Alasan: "<?= htmlspecialchars($komentar_penolakan); ?>"</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="form-verifikasi" action="/docutrack/public/admin/simpan-revisi/<?= $kegiatan_data['id'] ?? '' ?>" method="POST" enctype="multipart/form-data">
            
            <!-- 1. Kerangka Acuan Kerja (KAK) -->
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 pb-3 border-b border-gray-100">
                    1. Kerangka Acuan Kerja (KAK)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <?php 
                    renderField('Nama Pengusul', $kegiatan_data['nama_pengusul'] ?? '-', 'nama_pengusul', $is_revisi, $komentar_revisi);
                    renderField('NIM Pengusul', $kegiatan_data['nim_pengusul'] ?? '-', 'nim_pengusul', $is_revisi, $komentar_revisi);
                    renderField('Nama Penanggung Jawab', $kegiatan_data['nama_penanggung_jawab'] ?? '-', 'nama_penanggung_jawab', $is_revisi, $komentar_revisi);
                    renderField('NIM/NIP Penanggung Jawab', $kegiatan_data['nip_penanggung_jawab'] ?? '-', 'nip_penanggung_jawab', $is_revisi, $komentar_revisi);
                    ?>
                </div>

                <div class="mb-6">
                    <?php 
                    $is_nama_kegiatan_editable = isEditable('nama_kegiatan', $is_revisi, $komentar_revisi);
                    ?>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Nama Kegiatan
                        <?php showCommentIcon('nama_kegiatan', $komentar_revisi, $is_revisi); ?>
                    </label>
                    <?php if ($is_nama_kegiatan_editable): ?>
                        <input type="text" name="nama_kegiatan" value="<?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>" 
                               class="w-full p-4 bg-yellow-50 rounded-lg border border-yellow-400 ring-2 ring-yellow-400 text-gray-800 font-medium focus:ring-yellow-500 focus:border-yellow-500">
                    <?php else: ?>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium">
                            <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php 
                renderTextarea('Gambaran Umum', $kegiatan_data['gambaran_umum'] ?? '-', 'gambaran_umum', $is_revisi, $komentar_revisi);
                renderTextarea('Penerima Manfaat', $kegiatan_data['penerima_manfaat'] ?? '-', 'penerima_manfaat', $is_revisi, $komentar_revisi);
                ?>

                <h4 class="text-lg font-bold text-gray-800 mb-5 pb-2 border-b border-gray-100">Strategi Pencapaian Keluaran</h4>
                
                <?php 
                renderTextarea('Metode Pelaksanaan', $kegiatan_data['metode_pelaksanaan'] ?? '-', 'metode_pelaksanaan', $is_revisi, $komentar_revisi, null, '80px');
                renderTextarea('Tahapan Kegiatan', $kegiatan_data['tahapan_kegiatan'] ?? '-', 'tahapan_kegiatan', $is_revisi, $komentar_revisi);
                ?>
            </div>

            <!-- 2. IKU (Modal sama dengan Pengajuan Usulan) -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU) <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                
                <?php $is_iku_editable = isEditable('iku_data', $is_revisi, $komentar_revisi); ?>
                
                <!-- Hidden Input untuk menyimpan IKU -->
                <input type="hidden" id="indikator_kinerja_hidden" name="indikator_kinerja" value="<?= htmlspecialchars(is_array($iku_data) ? implode(',', $iku_data) : '') ?>">
                
                <?php if ($is_iku_editable): ?>
                    <!-- MODE EDIT: Display Area + Button Modal -->
                    <div class="p-4 bg-yellow-50 rounded-lg border-2 border-yellow-400">
                        <label class="text-sm font-medium text-gray-700 mb-2 block">
                            <i class="fas fa-info-circle text-yellow-600"></i> 
                            IKU yang Dipilih:
                        </label>
                        <div id="indicator-display-area" class="flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-white rounded-lg border border-yellow-400 transition-colors">
                            <span id="indicator-tags-container" class="contents"></span>
                            <button type="button" id="open-indicator-modal-btn" class="ml-auto inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 flex-shrink-0">
                                <i class="fas fa-plus-circle"></i> Tambah atau Ubah
                            </button>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- MODE VIEW: Chips Display -->
                    <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                        <?php if (!empty($iku_data)): ?>
                            <?php foreach ($iku_data as $iku_item): ?>
                                <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"><?= htmlspecialchars($iku_item); ?></span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span class="text-sm text-gray-500 italic">Tidak ada IKU yang dipilih.</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- 3. Indikator Kinerja (TANPA TOMBOL TAMBAH) -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    3. Indikator Kinerja KAK <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                <?php $is_indikator_editable = isEditable('indikator_data', $is_revisi, $komentar_revisi); ?>
                <div class="overflow-x-auto border border-gray-200 rounded-lg <?= $is_indikator_editable ? 'ring-2 ring-yellow-400' : '' ?>">
                    <table class="w-full min-w-[500px]">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Bulan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Indikator Keberhasilan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Target (%)</th>
                                <?php if ($is_indikator_editable): ?>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Aksi</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 <?= $is_indikator_editable ? 'bg-yellow-50' : 'bg-white' ?>" id="indikator-tbody">
                            <?php if (!empty($indikator_data)): ?>
                                <?php foreach ($indikator_data as $idx => $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <?php if ($is_indikator_editable): ?>
                                        <td class="px-4 py-3">
                                            <input type="text" name="indikator[<?= $idx ?>][bulan]" value="<?= htmlspecialchars($item['bulan'] ?? '') ?>" 
                                                   class="w-full text-sm p-2 border border-gray-300 rounded-md" placeholder="Bulan">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" name="indikator[<?= $idx ?>][nama]" value="<?= htmlspecialchars($item['nama'] ?? '') ?>" 
                                                   class="w-full text-sm p-2 border border-gray-300 rounded-md" placeholder="Indikator">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="indikator[<?= $idx ?>][target]" value="<?= htmlspecialchars($item['target'] ?? 0) ?>" 
                                                   class="w-full text-sm p-2 border border-gray-300 rounded-md" min="0" max="100">
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    <?php else: ?>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['bulan'] ?? 'N/A'); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['target'] ?? '0'); ?>%</td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="<?= $is_indikator_editable ? 4 : 3 ?>" class="px-4 py-3 text-sm text-gray-500 italic text-center">Tidak ada indikator.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

<!-- AKHIR PART 1 - Lanjut ke PART 2 -->

<!-- MODAL IKU -->
<?php if ($is_iku_editable): ?>
<div id="indicator-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1010] hidden opacity-0 transition-opacity duration-300"></div>
<div id="indicator-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-xl z-[1020] w-[90%] max-w-md hidden opacity-0 scale-95 transition-all duration-300">
    <div class="flex justify-between items-center p-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">Pilih Indikator Kinerja</h3>
        <button id="close-indicator-modal-btn" class="text-gray-400 hover:text-gray-600 transition-colors">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    <div class="p-4">
        <input type="search" id="indicator-search-input" placeholder="Cari indikator..." 
               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
        <div id="indicator-list-container" class="mt-4 max-h-60 overflow-y-auto modal-list pr-2"></div>
    </div>
    <div class="flex justify-end p-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
        <button id="done-indicator-modal-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all">
            Selesai
        </button>
    </div>
</div>

<script>
// IKU Modal Logic
document.addEventListener('DOMContentLoaded', function() {
    const allIndicators = ["Mendapat Pekerjaan", "Melanjutkan studi", "Menjadi Wiraswasta", "Kegiatan luar prodi", "Prestasi", "Pengabdian Masyarakat"];
    let selectedIndicators = new Set();
    
    const openBtn = document.getElementById('open-indicator-modal-btn');
    const closeBtn = document.getElementById('close-indicator-modal-btn');
    const doneBtn = document.getElementById('done-indicator-modal-btn');
    const modalBackdrop = document.getElementById('indicator-modal-backdrop');
    const modalContent = document.getElementById('indicator-modal-content');
    const searchInput = document.getElementById('indicator-search-input');
    const listContainer = document.getElementById('indicator-list-container');
    const tagsContainer = document.getElementById('indicator-tags-container');
    const hiddenInput = document.getElementById('indikator_kinerja_hidden');

    // Load existing IKU
    const existingIKU = hiddenInput.value.split(',').filter(v => v.trim());
    existingIKU.forEach(iku => selectedIndicators.add(iku.trim()));

    function renderModalList(filter = '') {
        if (!listContainer) return;
        listContainer.innerHTML = '';
        const lowerFilter = filter.toLowerCase();
        allIndicators.forEach(indicator => {
            if (indicator.toLowerCase().includes(lowerFilter)) {
                const isChecked = selectedIndicators.has(indicator);
                listContainer.innerHTML += `<label class="flex items-center w-full p-3 hover:bg-gray-100 rounded-lg cursor-pointer transition-colors duration-150">
                    <input type="checkbox" value="${indicator}" class="h-5 w-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 focus:ring-offset-0 mr-3" ${isChecked ? 'checked' : ''}>
                    <span class="ml-3 text-sm font-medium text-gray-700">${indicator}</span>
                </label>`;
            }
        });
    }

    function renderTags() {
        if (!tagsContainer || !hiddenInput) return;
        tagsContainer.innerHTML = '';
        if (selectedIndicators.size === 0) {
            tagsContainer.innerHTML = '<span class="text-sm text-gray-500 italic">Belum ada indikator dipilih.</span>';
        } else {
            selectedIndicators.forEach(indicator => {
                tagsContainer.innerHTML += `<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    ${indicator}
                    <button type="button" class="remove-tag-btn text-blue-500 hover:text-blue-900 leading-none text-lg -mr-1" data-value="${indicator}">&times;</button>
                </span>`;
            });
        }
        hiddenInput.value = Array.from(selectedIndicators).join(',');
    }

    function openModal() {
        if (!modalBackdrop || !modalContent) return;
        renderModalList(searchInput?.value || '');
        modalBackdrop.classList.remove('hidden');
        modalContent.classList.remove('hidden');
        setTimeout(() => { 
            modalBackdrop.classList.add('opacity-100'); 
            modalContent.classList.remove('opacity-0', 'scale-95'); 
            modalContent.classList.add('opacity-100', 'scale-100'); 
        }, 10);
    }

    function closeModal() {
        if (!modalBackdrop || !modalContent) return;
        modalBackdrop.classList.remove('opacity-100');
        modalContent.classList.remove('opacity-100', 'scale-100'); 
        modalContent.classList.add('opacity-0', 'scale-95');
        setTimeout(() => { 
            modalBackdrop.classList.add('hidden'); 
            modalContent.classList.add('hidden'); 
        }, 300);
    }

    openBtn?.addEventListener('click', openModal);
    closeBtn?.addEventListener('click', closeModal);
    doneBtn?.addEventListener('click', closeModal);
    modalBackdrop?.addEventListener('click', closeModal);
    searchInput?.addEventListener('input', () => renderModalList(searchInput.value));
    
    listContainer?.addEventListener('change', (e) => { 
        if (e.target.type === 'checkbox') { 
            const value = e.target.value; 
            if (e.target.checked) { 
                selectedIndicators.add(value); 
            } else { 
                selectedIndicators.delete(value); 
            } 
            renderTags(); 
        } 
    });
    
    tagsContainer?.addEventListener('click', (e) => { 
        const removeButton = e.target.closest('.remove-tag-btn'); 
        if (removeButton) { 
            selectedIndicators.delete(removeButton.dataset.value); 
            renderTags(); 
            renderModalList(searchInput?.value || ''); 
        } 
    });

    renderTags();
});
</script>
<?php endif; ?>

<!-- 4. RAB (TANPA TOMBOL TAMBAH BARIS, Support vol1, sat1, vol2, sat2) -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                <?php 
                    $grand_total_rab = 0;
                    if (!empty($rab_data)):
                        foreach ($rab_data as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal = 0;
                            $rab_comment_key = 'rab_' . strtolower(str_replace(' ', '_', $kategori));
                            $has_rab_comment = isEditable($rab_comment_key, $is_revisi, $komentar_revisi);
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                        <?= htmlspecialchars($kategori); ?>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi); ?>
                    </h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg <?= $has_rab_comment ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <table class="w-full min-w-[900px]">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Uraian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Rincian</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Vol 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Sat 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Vol 2</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Sat 2</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Harga (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                                    <?php if ($has_rab_comment): ?>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 <?= $has_rab_comment ? 'bg-yellow-50' : 'bg-white'; ?>">
                                <?php foreach ($items as $idx => $item): 
                                    $vol1 = $item['vol1'] ?? 0;
                                    $sat1 = $item['sat1'] ?? '';
                                    $vol2 = $item['vol2'] ?? 1;
                                    $sat2 = $item['sat2'] ?? '';
                                    $harga = $item['harga'] ?? 0;
                                    $total_item = $vol1 * $vol2 * $harga;
                                    $subtotal += $total_item;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <?php if ($has_rab_comment): ?>
                                        <!-- MODE EDIT -->
                                        <td class="px-4 py-3">
                                            <input type="text" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][uraian]" 
                                                   class="w-full text-sm p-2 border border-gray-300 rounded-md" 
                                                   value="<?= htmlspecialchars($item['uraian'] ?? ''); ?>">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="text" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][rincian]" 
                                                   class="w-full text-sm p-2 border border-gray-300 rounded-md" 
                                                   value="<?= htmlspecialchars($item['rincian'] ?? ''); ?>">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][vol1]" 
                                                   class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md rab-calc" 
                                                   value="<?= $vol1; ?>" 
                                                   data-kategori="<?= htmlspecialchars($kategori) ?>" 
                                                   data-idx="<?= $idx ?>">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][sat1]" 
                                                   class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md" 
                                                   value="<?= htmlspecialchars($sat1); ?>">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][vol2]" 
                                                   class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md rab-calc" 
                                                   value="<?= $vol2; ?>" 
                                                   data-kategori="<?= htmlspecialchars($kategori) ?>" 
                                                   data-idx="<?= $idx ?>">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][sat2]" 
                                                   class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md" 
                                                   value="<?= htmlspecialchars($sat2); ?>">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" 
                                                   name="rab[<?= htmlspecialchars($kategori); ?>][<?= $idx ?>][harga]" 
                                                   class="w-28 text-sm p-2 text-right border border-gray-300 rounded-md rab-calc" 
                                                   value="<?= $harga; ?>" 
                                                   data-kategori="<?= htmlspecialchars($kategori) ?>" 
                                                   data-idx="<?= $idx ?>">
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-sm text-blue-600 font-semibold rab-total" 
                                                  data-kategori="<?= htmlspecialchars($kategori) ?>" 
                                                  data-idx="<?= $idx ?>">
                                                <?= formatRupiah($total_item); ?>
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <button type="button" 
                                                    onclick="removeRABRow(this)" 
                                                    class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    <?php else: ?>
                                        <!-- MODE VIEW -->
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['uraian'] ?? ''); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['rincian'] ?? ''); ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol1; ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat1); ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol2; ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat2); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700 text-right"><?= number_format($harga, 0, ',', '.'); ?></td>
                                        <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right"><?= formatRupiah($total_item); ?></td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; $grand_total_rab += $subtotal; ?>
                                
                                <!-- SUBTOTAL ROW -->
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= formatRupiah($subtotal); ?></td>
                                    <?php if ($has_rab_comment): ?>
                                    <td></td>
                                    <?php endif; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($has_rab_comment): ?>
                        <p class="text-xs text-yellow-700 mt-2 italic flex items-center gap-1">
                            <i class="fas fa-info-circle"></i> 
                            <?= htmlspecialchars($komentar_revisi[$rab_comment_key]); ?>
                        </p>
                        <!-- HAPUS TOMBOL TAMBAH BARIS RAB -->
                    <?php endif; ?>
                <?php endforeach; else: ?>
                    <p class="text-sm text-gray-500 italic">Tidak ada data RAB.</p>
                <?php endif; ?>
                
                <!-- GRAND TOTAL -->
                <div class="flex justify-end mt-6">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <span class="text-sm font-medium text-gray-700">Grand Total RAB: </span>
                        <span class="text-xl font-bold text-blue-600" id="grand-total"><?= formatRupiah($grand_total_rab); ?></span>
                    </div>
                </div>
            </div>

            <!-- 5. Rincian Rancangan Kegiatan (Hanya Disetujui) -->
            <?php if ($is_disetujui): ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 pb-3 mb-6 border-b border-gray-200">5. Rincian Rancangan Kegiatan</h3>
                
                <div class="mb-6">
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">Surat Pengantar</label>
                    <div class="relative max-w-sm">
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <span class="text-sm text-gray-800">
                                <?= !empty($surat_pengantar) ? htmlspecialchars($surat_pengantar) : '-'; ?>
                            </span>
                            <?php if (!empty($surat_pengantar)): ?>
                                <a href="<?= htmlspecialchars($surat_pengantar_url); ?>" target="_blank" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-download"></i>
                                </a>
                            <?php else: ?>
                                <i class="fas fa-file-alt text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 mb-3 block">Kurun Waktu Pelaksanaan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Mulai</span>
                                <span class="text-sm text-gray-800 font-medium">
                                    <?= !empty($tanggal_mulai) ? date('d M Y', strtotime($tanggal_mulai)) : '-'; ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Selesai</span>
                                <span class="text-sm text-gray-800 font-medium">
                                    <?= !empty($tanggal_selesai) ? date('d M Y', strtotime($tanggal_selesai)) : '-'; ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 6. Kode MAK (Hanya Disetujui) -->
            <?php if ($is_disetujui): ?>
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">6. Kode Mata Anggaran Kegiatan (MAK)</h3>
                <div class="relative max-w-md">
                    <i class="fas fa-key absolute top-3.5 left-3 text-gray-400"></i>
                    <input type="text" id="kode_mak" name="kode_mak" 
                           class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-gray-100 rounded-lg border border-gray-200" 
                           value="<?= htmlspecialchars($kode_mak); ?>" readonly>
                </div>
            </div>
            <?php endif; ?>

            <!-- Footer Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?= htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex gap-4 w-full sm:w-auto">
                    <?php if ($is_revisi): ?>
                        <button type="submit" id="btn-simpan-revisi" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all">
                            <i class="fas fa-save text-xs"></i> Simpan Revisi
                        </button>
                    <?php elseif ($is_disetujui): ?>
                        <button type="button" id="print-pdf-btn" 
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-red-700 transition-all">
                            <i class="fas fa-print text-xs"></i> Print PDF
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const formVerifikasi = document.getElementById('form-verifikasi');
    
    // ============================================
    // TAMPILKAN NOTIFIKASI SUKSES (Jika Ada)
    // ============================================
    <?php if (isset($_SESSION['success_message'])): ?>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?= addslashes($_SESSION['success_message']) ?>',
            confirmButtonColor: '#10B981',
            confirmButtonText: 'OK'
        });
    }
    <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: '<?= addslashes($_SESSION['error_message']) ?>',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK'
        });
    }
    <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    // ============================================
    // PRINT PDF BUTTON
    // ============================================
    document.getElementById('print-pdf-btn')?.addEventListener('click', (e) => {
        e.preventDefault(); 
        window.print(); 
    });

    // ============================================
    // SIMPAN REVISI BUTTON (Dengan Konfirmasi)
    // ============================================
    document.getElementById('btn-simpan-revisi')?.addEventListener('click', (e) => {
        e.preventDefault();
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Perubahan akan disimpan dan dikirim untuk verifikasi ulang.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading
                    Swal.fire({ 
                        title: 'Menyimpan...', 
                        html: 'Harap tunggu, data sedang diproses.',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading() 
                    });
                    
                    // Submit form
                    formVerifikasi.submit();
                }
            });
        } else {
            if (confirm('Simpan perubahan revisi?')) {
                formVerifikasi.submit();
            }
        }
    });

    // ============================================
    // AUTO-CALCULATE RAB TOTALS (Event Listener)
    // ============================================
    document.querySelectorAll('.rab-calc').forEach(input => {
        input.addEventListener('input', function() {
            const kategori = this.dataset.kategori;
            const idx = this.dataset.idx;
            calculateRABTotal(kategori, idx);
        });
    });
});

// ============================================
// FUNCTION: Calculate RAB Total per Item
// UPDATED untuk vol1 x vol2 x harga
// ============================================
function calculateRABTotal(kategori, idx) {
    const vol1Input = document.querySelector(`input[name="rab[${kategori}][${idx}][vol1]"]`);
    const vol2Input = document.querySelector(`input[name="rab[${kategori}][${idx}][vol2]"]`);
    const hargaInput = document.querySelector(`input[name="rab[${kategori}][${idx}][harga]"]`);
    const totalSpan = document.querySelector(`.rab-total[data-kategori="${kategori}"][data-idx="${idx}"]`);
    
    if (vol1Input && vol2Input && hargaInput && totalSpan) {
        const vol1 = parseFloat(vol1Input.value) || 0;
        const vol2 = parseFloat(vol2Input.value) || 1; // Default 1 agar tidak zero-out
        const harga = parseFloat(hargaInput.value) || 0;
        const total = vol1 * vol2 * harga;
        
        totalSpan.textContent = formatRupiah(total);
        
        // Recalculate Grand Total setelah update
        updateGrandTotal();
    }
}

// ============================================
// FUNCTION: Update Grand Total
// ============================================
function updateGrandTotal() {
    let grandTotal = 0;
    
    // Sum all .rab-total spans
    document.querySelectorAll('.rab-total').forEach(span => {
        const text = span.textContent.replace(/[^0-9]/g, '');
        grandTotal += parseInt(text) || 0;
    });
    
    const grandTotalDisplay = document.getElementById('grand-total');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = formatRupiah(grandTotal);
    }
}

// ============================================
// FUNCTION: Format Rupiah
// ============================================
function formatRupiah(angka) {
    return 'Rp ' + angka.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// ============================================
// FUNCTION: Remove Row (Indikator)
// ============================================
function removeRow(button) {
    const row = button.closest('tr');
    if (row) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Baris?',
                text: "Baris ini akan dihapus dari tabel.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: 'Baris berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        } else {
            if (confirm('Hapus baris ini?')) {
                row.remove();
            }
        }
    }
}

// ============================================
// FUNCTION: Remove RAB Row
// ============================================
function removeRABRow(button) {
    const row = button.closest('tr');
    if (row) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Hapus Baris RAB?',
                text: "Baris ini akan dihapus dari RAB.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove();
                    updateGrandTotal(); // Update grand total setelah hapus baris
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Dihapus!',
                        text: 'Baris RAB berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        } else {
            if (confirm('Hapus baris RAB ini?')) {
                row.remove();
                updateGrandTotal();
            }
        }
    }
}

// ============================================
// HELPER: Show Success Notification
// (Bisa dipanggil dari controller setelah simpan)
// ============================================
function showSuccessNotification(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: message || 'Data berhasil disimpan.',
            confirmButtonColor: '#10B981',
            confirmButtonText: 'OK'
        });
    } else {
        alert(message || 'Data berhasil disimpan.');
    }
}

// ============================================
// HELPER: Show Error Notification
// ============================================
function showErrorNotification(message) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: message || 'Terjadi kesalahan saat menyimpan data.',
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'OK'
        });
    } else {
        alert(message || 'Terjadi kesalahan.');
    }
}
</script>