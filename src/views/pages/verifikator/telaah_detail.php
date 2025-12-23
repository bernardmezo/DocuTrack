<?php
// File: src/views/pages/verifikator/telaah_detail.php

$kegiatanId = $kegiatan_data['kegiatanId'] ?? $id ?? '';

$status = $status ?? 'Menunggu';
$user_role = $user_role ?? 'verifikator'; 

$is_disetujui = (strtolower($status) === 'disetujui');
$is_menunggu = (strtolower($status) === 'menunggu');
$is_telah_direvisi = (strtolower($status) === 'telah direvisi');
$is_ditolak = (strtolower($status) === 'ditolak');
$is_revisi = (strtolower($status) === 'revisi');

$komentar_revisi = $komentar_revisi ?? [];
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/verifikator/dashboard'; 

$surat_pengantar = $kegiatan_data['surat_pengantar'] ?? '';
$tanggal_mulai = $kegiatan_data['tanggal_mulai'] ?? '';
$tanggal_selesai = $kegiatan_data['tanggal_selesai'] ?? '';
$surat_pengantar_url = $surat_pengantar_url ?? '';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}

if (!function_exists('isValidDate')) {
    function isValidDate($date) {
        return !empty($date) && $date !== '0000-0000' && strtotime($date) !== false;
    }
}

if (!function_exists('formatTanggal')) {
    function formatTanggal($date, $format = 'd M Y') {
        if (!isValidDate($date)) return '-';
        return date($format, strtotime($date));
    }
}

function showCommentIcon($field_name, $komentar_list, $is_revisi, $is_telah_direvisi) {
    if (($is_revisi || $is_telah_direvisi) && isset($komentar_list[$field_name])) {
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

function render_comment_box($field_name, $is_menunggu_status, $is_telah_direvisi_status) {
    if ($is_menunggu_status || $is_telah_direvisi_status) { 
        echo "<div id='comment-box-{$field_name}' class='comment-box hidden mt-2 animate-reveal'>";
        echo "  <label for='comment-{$field_name}' class='text-xs font-semibold text-yellow-800'>Catatan Revisi untuk bagian ini:</label>";
        echo "  <textarea id='comment-{$field_name}' name='komentar[{$field_name}]' rows='3' 
                 class='mt-1 block w-full text-sm text-gray-800 bg-yellow-50 rounded-lg border border-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 py-2.5 leading-relaxed resize-none' 
                 placeholder='Tulis catatan revisi di sini...'></textarea>";
        echo "</div>";
    }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-6 md:mb-8">
        
        <div class="flex flex-col justify-start mb-4 md:mb-6 pb-4 md:pb-5 border-b border-gray-200 gap-2">
            <h2 class="text-xl md:text-3xl font-bold text-gray-800">Telaah Usulan Kegiatan</h2>
            <p class="text-xs md:text-sm text-gray-500">Status:
                <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                <?php elseif ($is_revisi): ?> <span class="font-semibold text-yellow-600">Menunggu Perbaikan Admin</span>
                <?php elseif ($is_telah_direvisi): ?> <span class="font-semibold text-purple-600">Telah Direvisi</span>
                <?php elseif ($is_ditolak): ?> <span class="font-semibold text-red-600">Ditolak</span>
                <?php else: ?> <span class="font-semibold text-gray-600">Menunggu Verifikasi</span>
                <?php endif; ?>
            </p>
        </div>

        <?php if ($is_telah_direvisi && !empty($komentar_revisi)): ?>
        <div class="revision-alert-box bg-yellow-50 border-l-4 border-yellow-400 p-4 md:p-6 rounded-lg mb-6 md:mb-8 animate-reveal">
            <div class="flex items-start md:items-center gap-3">
                <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-500 text-xl md:text-2xl"></i></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base md:text-lg font-bold text-yellow-800">Catatan Revisi Sebelumnya</h3>
                    <p class="text-xs md:text-sm text-yellow-700 mt-1">Admin telah memperbaiki usulan berdasarkan catatan ini. Harap telaah kembali.</p>
                </div>
            </div>
            <ul class="list-disc list-inside mt-3 md:mt-4 pl-6 md:pl-10 space-y-1 text-xs md:text-sm text-yellow-700">
                <?php foreach ($komentar_revisi as $field => $komentar): ?>
                    <li><span class="font-semibold"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</span> <?php echo htmlspecialchars($komentar); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ($is_ditolak): ?>
        <div class="revision-alert-box bg-red-50 border-l-4 border-red-400 p-4 md:p-6 rounded-lg mb-6 md:mb-8 animate-reveal">
             <div class="flex items-start md:items-center gap-3">
                <div class="flex-shrink-0"><i class="fas fa-times-circle text-red-500 text-xl md:text-2xl"></i></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base md:text-lg font-bold text-red-800">Usulan Ditolak</h3>
                    <p class="text-xs md:text-sm text-red-700 mt-1">Alasan Penolakan: "<?php echo htmlspecialchars($komentar_penolakan); ?>"</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="form-verifikasi" action="#" method="POST">
            <!-- Hidden fields -->
            <input type="hidden" name="kegiatan_id" value="<?php echo htmlspecialchars($kegiatanId); ?>">
            <input type="hidden" id="grand_total_rab_input" name="grand_total_rab" value="0">
            
            <!-- 1. KAK Section -->
            <div class="mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200 flex items-center gap-2">
                    1. Kerangka Acuan Kegiatan (KAK)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-4 md:mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Nama Pengusul <?php showCommentIcon('nama_pengusul', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['nama_pengusul']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= htmlspecialchars($kegiatan_data['nama_pengusul'] ?? '-') ?>
                        </div>
                        <?php if (!$is_ditolak) render_comment_box('nama_pengusul', $is_menunggu, $is_telah_direvisi); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            NIM Pengusul <?php showCommentIcon('nim_pengusul', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['nim_pengusul']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= htmlspecialchars($kegiatan_data['nim_pengusul'] ?? '-') ?>
                        </div>
                        <?php if (!$is_ditolak) render_comment_box('nim_pengusul', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                </div>

                <div class="mb-4 md:mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Nama Kegiatan <?php showCommentIcon('nama_kegiatan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </label>
                    <div class="p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['nama_kegiatan']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
                    </div>
                    <?php if (!$is_ditolak) render_comment_box('nama_kegiatan', $is_menunggu, $is_telah_direvisi); ?>
                </div>

                <div class="mb-4 md:mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Gambaran Umum <?php showCommentIcon('gambaran_umum', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </label>
                    <div class="p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[80px] md:min-h-[100px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['gambaran_umum']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')) ?>
                    </div>
                    <?php if (!$is_ditolak) render_comment_box('gambaran_umum', $is_menunggu, $is_telah_direvisi); ?>
                </div>

                <div class="mb-6 md:mb-8">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Penerima Manfaat <?php showCommentIcon('penerima_manfaat', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </label>
                    <div class="p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[60px] md:min-h-[80px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['penerima_manfaat']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')) ?>
                    </div>
                     <?php if (!$is_ditolak) render_comment_box('penerima_manfaat', $is_menunggu, $is_telah_direvisi); ?>
                </div>

                <div class="border-t border-gray-200 pt-4 md:pt-6 mb-4 md:mb-6">
                    <h4 class="text-base md:text-lg font-bold text-gray-800 mb-4 md:mb-5 pb-2">Strategi Pencapaian Keluaran</h4>
                    
                    <div class="mb-4 md:mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Metode Pelaksanaan <?php showCommentIcon('metode_pelaksanaan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[60px] md:min-h-[80px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['metode_pelaksanaan']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')) ?>
                        </div>
                        <?php if (!$is_ditolak) render_comment_box('metode_pelaksanaan', $is_menunggu, $is_telah_direvisi); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Tahapan Kegiatan <?php showCommentIcon('tahapan_kegiatan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-3 md:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[80px] md:min-h-[100px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['tahapan_kegiatan']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')) ?>
                        </div>
                        <?php if (!$is_ditolak) render_comment_box('tahapan_kegiatan', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                </div>
            </div>

            <!-- 2. IKU Section -->
            <div class="mb-6 md:mb-8 animate-reveal" style="animation-delay: 200ms;">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU)
                    <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Indikator yang Dipilih:</label>
                <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200 <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['iku_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                    <?php if (!empty($iku_data)): ?>
                        <?php foreach ($iku_data as $iku_item): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 md:px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo htmlspecialchars($iku_item); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-sm text-gray-500 italic">Tidak ada IKU yang dipilih.</span>
                    <?php endif; ?>
                </div>
                <?php if (!$is_ditolak) render_comment_box('iku_data', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <!-- 3. Indikator Kinerja KAK -->
            <div class="mb-6 md:mb-8 animate-reveal" style="animation-delay: 300ms;">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200 flex items-center">
                    3. Indikator Kinerja KAK
                    <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                
                <!-- Mobile: Card View -->
                <div class="block md:hidden space-y-3">
                    <?php if (!empty($indikator_data)): ?>
                        <?php foreach ($indikator_data as $item): ?>
                        <div class="bg-white border <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['indikator_data']) ? 'border-yellow-400 ring-2 ring-yellow-400' : 'border-gray-200'; ?> rounded-lg p-3 shadow-sm">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-bold text-gray-500 uppercase"><?php echo strtoupper(htmlspecialchars($item['bulan'] ?? 'N/A')); ?></span>
                                <span class="text-sm font-semibold text-blue-600"><?php echo htmlspecialchars($item['target'] ?? '0'); ?>%</span>
                            </div>
                            <p class="text-sm text-gray-700"><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></p>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-4 text-center text-sm text-gray-500 italic bg-gray-50 rounded-lg border border-gray-200">
                            Tidak ada indikator.
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Desktop: Table View -->
                <div class="hidden md:block overflow-x-auto border <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['indikator_data']) ? 'border-yellow-400 ring-2 ring-yellow-400' : 'border-gray-200'; ?> rounded-lg">
                    <table class="w-full min-w-[500px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Bulan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Indikator Keberhasilan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Target</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (!empty($indikator_data)): ?>
                                <?php foreach ($indikator_data as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo strtoupper(htmlspecialchars($item['bulan'] ?? 'N/A')); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['target'] ?? '0'); ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="px-4 py-3 text-sm text-gray-500 italic text-center">Tidak ada indikator.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if (!$is_ditolak) render_comment_box('indikator_data', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <!-- 4. RAB Section -->
            <div class="mb-6 md:mb-8 animate-reveal" style="animation-delay: 400ms;">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                <?php 
                    $grand_total_rab = 0;
                    if (!empty($rab_data)):
                        foreach ($rab_data as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal = 0;
                            $rab_comment_key = 'rab_' . strtolower(str_replace(' ', '_', $kategori));
                ?>
                    <h4 class="text-sm md:text-md font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                        <?php echo htmlspecialchars($kategori); ?>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </h4>
                    
                    <!-- Mobile: Card View -->
                    <div class="block md:hidden space-y-3 mb-4">
                        <?php 
                        $item_number = 0;
                        foreach ($items as $item): 
                            $item_number++;
                            $vol1 = $item['vol1'] ?? 0;
                            $sat1 = $item['sat1'] ?? '';
                            $vol2 = $item['vol2'] ?? 1;
                            $sat2 = $item['sat2'] ?? '';
                            $harga = $item['harga'] ?? 0;
                            $total_item = $vol1 * $vol2 * $harga;
                            $subtotal += $total_item;
                        ?>
                        <div class="bg-white border <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi[$rab_comment_key]) ? 'border-yellow-400 ring-2 ring-yellow-400' : 'border-gray-200'; ?> rounded-lg shadow-sm overflow-hidden">
                            <!-- Header -->
                            <div class="bg-gray-100 px-3 py-2.5 border-b border-gray-200 flex items-start gap-2">
                                <span class="bg-gray-600 text-white text-xs font-bold px-2 py-0.5 rounded mt-0.5 flex-shrink-0">#<?= $item_number ?></span>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($item['uraian'] ?? '') ?></div>
                                    <?php if (!empty($item['rincian'])): ?>
                                    <div class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($item['rincian']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-3 space-y-3">
                                <!-- Volume -->
                                <div>
                                    <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block mb-1.5">Volume</label>
                                    <div class="p-2.5 bg-gray-100 rounded-lg border border-gray-200">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-bold text-gray-900"><?= $vol1 ?></span>
                                                <span class="text-xs font-medium text-gray-600"><?= htmlspecialchars($sat1) ?></span>
                                            </div>
                                            <span class="text-lg text-gray-400 font-bold">×</span>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-xl font-bold text-gray-900"><?= $vol2 ?></span>
                                                <span class="text-xs font-medium text-gray-600"><?= htmlspecialchars($sat2) ?></span>
                                            </div>
                                        </div>
                                        <div class="text-center text-xs text-gray-500 mt-1.5">
                                            = <span class="font-semibold text-gray-700"><?= $vol1 * $vol2 ?> unit</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Harga Satuan -->
                                <div>
                                    <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block mb-1.5">Harga Satuan</label>
                                    <div class="p-2.5 bg-gray-100 rounded-lg border border-gray-200 text-center">
                                        <div class="text-base font-bold text-gray-900">
                                            Rp <?= number_format($harga, 0, ',', '.') ?>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">per unit</div>
                                    </div>
                                </div>
                                
                                <!-- Perhitungan -->
                                <div>
                                    <label class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider block mb-1.5">Perhitungan</label>
                                    <div class="p-2.5 bg-gray-100 rounded-lg border border-gray-200">
                                        <div class="text-sm text-gray-700 text-center space-y-1">
                                            <div><?= $vol1 * $vol2 ?> unit × Rp <?= number_format($harga, 0, ',', '.') ?></div>
                                            <div class="border-t border-gray-300 pt-1.5 mt-1.5 font-bold text-gray-900">
                                                = <?= formatRupiah($total_item) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Footer Total -->
                            <div class="bg-blue-50 border-t border-blue-100 px-3 py-2.5">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-blue-700 uppercase">Total Item</span>
                                    <span class="text-base font-bold text-blue-600"><?= formatRupiah($total_item) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Subtotal Mobile -->
                        <div class="bg-gray-100 border-2 border-gray-300 rounded-lg p-3.5 shadow-sm">
                            <div class="flex justify-between items-center">
                                <div>
                                    <div class="text-xs text-gray-600 uppercase font-semibold">Subtotal</div>
                                    <div class="text-xs text-gray-500 mt-0.5"><?= htmlspecialchars($kategori) ?> (<?= $item_number ?> item)</div>
                                </div>
                                <span class="text-lg font-bold text-gray-900"><?= formatRupiah($subtotal) ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop: Table View -->
                    <div class="hidden md:block overflow-x-auto border <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi[$rab_comment_key]) ? 'border-yellow-400 ring-2 ring-yellow-400' : 'border-gray-200'; ?> rounded-lg mb-4">
                        <table class="w-full min-w-[900px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Uraian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Rincian</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Vol 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Sat 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Vol 2</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Sat 2</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Harga (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php 
                                // Reset subtotal for desktop calculation
                                $subtotal = 0;
                                foreach ($items as $item): 
                                    $vol1 = $item['vol1'] ?? 0;
                                    $sat1 = $item['sat1'] ?? '';
                                    $vol2 = $item['vol2'] ?? 1;
                                    $sat2 = $item['sat2'] ?? '';
                                    $harga = $item['harga'] ?? 0;
                                    $total_item = $vol1 * $vol2 * $harga;
                                    $subtotal += $total_item;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['uraian'] ?? ''); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['rincian'] ?? ''); ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-700 text-center"><?php echo $vol1; ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-600 text-center"><?php echo htmlspecialchars($sat1); ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-700 text-center"><?php echo $vol2; ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-600 text-center"><?php echo htmlspecialchars($sat2); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700 text-right"><?php echo number_format($harga, 0, ',', '.'); ?></td>
                                    <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right"><?php echo formatRupiah($total_item); ?></td>
                                </tr>
                                <?php endforeach; $grand_total_rab += $subtotal; ?>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="7" class="px-4 py-2 text-right text-sm text-gray-800">Subtotal</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 text-right"><?php echo formatRupiah($subtotal); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php if (!$is_ditolak) render_comment_box($rab_comment_key, $is_menunggu, $is_telah_direvisi); ?>
                <?php 
                        endforeach; 
                    else:
                ?>
                    <p class="text-sm text-gray-500 italic">Tidak ada data RAB.</p>
                <?php endif; ?>
                
                <!-- Grand Total -->
                <div class="flex justify-end mt-4 md:mt-6">
                    <div class="p-3 md:p-4 bg-blue-50 rounded-lg border border-blue-100 w-full md:w-auto">
                        <div class="flex justify-between md:block items-center">
                            <span class="text-xs md:text-sm font-medium text-gray-700">Grand Total RAB:</span>
                            <span class="text-lg md:text-xl font-bold text-blue-600"><?php echo formatRupiah($grand_total_rab); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            
            
            <!-- 6. Kode MAK -->
            <div id="mak-section" class="mt-6 md:mt-8 pt-4 md:pt-6 border-t border-gray-200 animate-reveal 
                <?php echo ($is_disetujui) ? 'block' : 'hidden'; ?>" 
                style="<?php echo (($is_menunggu || $is_telah_direvisi) && !$is_ditolak) ? 'opacity: 0; max-height: 0px; overflow: hidden; transform: translateY(-10px); transition: all 0.4s ease-out;' : 'animation-delay: 500ms;'; ?>"
                >
                <h3 class="text-lg md:text-xl font-bold text-gray-800 mb-3 md:mb-4 flex items-center">
                    6. Kode Mata Anggaran Kegiatan (MAK)
                    <?php showCommentIcon('kode_mak', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                
                <div class="relative max-w-md group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-key text-gray-400 group-focus-within:text-blue-600 transition-colors duration-200"></i>
                    </div>

                    <input type="text" id="kode_mak" name="kode_mak" 
                        class="block w-full px-4 py-3 md:py-3.5 pl-11 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-600 transition-all peer <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['kode_mak']) ? 'border-yellow-500 ring-2 ring-yellow-300' : ''; ?>" 
                        value="<?php echo htmlspecialchars($kode_mak); ?>" placeholder=" " 
                        <?php echo (($is_disetujui || $is_ditolak) && !empty($kode_mak)) ? 'readonly' : ''; ?>>

                    <label for="kode_mak" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-white px-2 start-9 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-3 peer-placeholder-shown:text-gray-400 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">
                        Masukkan Kode MAK
                    </label>
                </div>

                <?php if (($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['kode_mak'])): ?>
                    <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['kode_mak']); ?></p>
                <?php endif; ?>
                <?php if (!$is_ditolak) render_comment_box('kode_mak', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row justify-between items-stretch sm:items-center mt-6 md:mt-10 pt-4 md:pt-6 border-t border-gray-200 gap-3 md:gap-4">
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                    <i class="fas fa-arrow-left text-xs"></i> 
                    <span>Kembali</span>
                </a>
                 
                <div class="flex flex-col sm:flex-row-reverse gap-3 md:gap-4 w-full sm:w-auto">
                
                <?php if ($is_menunggu || $is_telah_direvisi): ?>
                    <div id="review-actions" class="flex flex-col sm:flex-row-reverse gap-3 md:gap-4 w-full sm:w-auto">
                        <button type="button" id="btn-lanjut-mak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                            <span>Lanjut</span>
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                        <button type="button" id="btn-show-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                            <i class="fas fa-edit text-xs"></i> 
                            <span>Revisi</span>
                        </button>
                        <button type="button" id="btn-tolak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                            <i class="fas fa-times text-xs"></i> 
                            <span>Tolak</span>
                        </button>
                    </div>
                    
                    <div id="approval-actions" class="flex flex-col sm:flex-row-reverse gap-3 md:gap-4 w-full sm:w-auto hidden">
                        <button type="submit" id="btn-setujui-usulan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                             <i class="fas fa-check-double text-xs"></i> 
                             <span>Setujui Usulan</span>
                        </button>
                        <button type="button" id="btn-kembali-review" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                             <span>Kembali</span>
                        </button>
                    </div>
                    
                    <div id="comment-actions" class="flex flex-col sm:flex-row-reverse gap-3 md:gap-4 w-full sm:w-auto hidden">
                        <button type="submit" id="btn-kirim-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                             <i class="fas fa-paper-plane text-xs"></i> 
                             <span>Kirim Komentar Revisi</span>
                        </button>
                        <button type="button" id="btn-batal-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5 text-sm">
                             <span>Batal</span>
                        </button>
                    </div>
                <?php endif; ?>
                 </div>
            </div>
        </form>
        
    </section>
</main>

<style>
    /* Improve mobile touch targets */
    @media (max-width: 768px) {
        button, a {
            min-height: 44px;
        }
    }
    
    /* Better animations for mobile */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-reveal {
        animation: fadeInUp 0.3s ease-out forwards;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        const isDisetujui = <?php echo json_encode($is_disetujui); ?>;
        const namaKegiatan = <?php echo json_encode($kegiatan_data['nama_kegiatan'] ?? 'Kegiatan Ini'); ?>;
        const kegiatanId = <?php echo json_encode($kegiatanId ?: $id); ?>;
        const grandTotalRabValue = <?php echo json_encode($grand_total_rab ?? 0); ?>;

        // Set nilai grand_total_rab ke hidden input
        const grandTotalRabInput = document.getElementById('grand_total_rab_input');
        if (grandTotalRabInput) {
            grandTotalRabInput.value = grandTotalRabValue;
        }

        if (typeof formatRupiah !== 'function') {
            window.formatRupiah = (angka) => `Rp ${new Intl.NumberFormat('id-ID').format(angka || 0)}`;
        }
        
        document.querySelectorAll('#form-verifikasi .peer').forEach(input => {
            const label = input.nextElementSibling;
            if (label && label.classList.contains('floating-label')) {
                const updateLabel = () => {
                    if (input.value) {
                        label.classList.add('scale-75', '-translate-y-4', 'text-blue-600');
                        label.classList.remove('peer-placeholder-shown:scale-100', 'peer-placeholder-shown:translate-y-0');
                    } else {
                        label.classList.remove('scale-75', '-translate-y-4', 'text-blue-600');
                        label.classList.add('peer-placeholder-shown:scale-100', 'peer-placeholder-shown:translate-y-0');
                    }
                };
                updateLabel();
                input.addEventListener('input', updateLabel);
                input.addEventListener('focus', () => {
                    label.classList.add('scale-75', '-translate-y-4', 'text-blue-600');
                    label.classList.remove('peer-placeholder-shown:scale-100', 'peer-placeholder-shown:translate-y-0');
                });
                input.addEventListener('blur', () => {
                    if (!input.value) {
                        label.classList.remove('scale-75', '-translate-y-4', 'text-blue-600');
                        label.classList.add('peer-placeholder-shown:scale-100', 'peer-placeholder-shown:translate-y-0');
                    }
                });
            }
        });

        const btnLanjutMak = document.getElementById('btn-lanjut-mak');
        const btnKembaliReview = document.getElementById('btn-kembali-review');
        const btnShowRevisi = document.getElementById('btn-show-revisi');
        const btnBatalRevisi = document.getElementById('btn-batal-revisi');
        const btnKirimRevisi = document.getElementById('btn-kirim-revisi');
        const btnTolak = document.getElementById('btn-tolak');
        const btnSetujui = document.getElementById('btn-setujui-usulan');
        
        const reviewActions = document.getElementById('review-actions'); 
        const approvalActions = document.getElementById('approval-actions'); 
        const commentActions = document.getElementById('comment-actions'); 
        const makSection = document.getElementById('mak-section');
        const commentBoxes = document.querySelectorAll('.comment-box'); 
        const formVerifikasi = document.getElementById('form-verifikasi');

        function toggleReviewMode(mode) {
            if (reviewActions) reviewActions.classList.toggle('hidden', mode !== 'review');
            if (approvalActions) approvalActions.classList.toggle('hidden', mode !== 'approval');
            if (commentActions) commentActions.classList.toggle('hidden', mode !== 'comment');

            if (makSection) {
                if (mode === 'approval') {
                    makSection.classList.remove('hidden');
                    setTimeout(() => { 
                        makSection.style.opacity = '1';
                        makSection.style.maxHeight = '500px';
                        makSection.style.transform = 'translateY(0)';
                    }, 10);
                    document.getElementById('kode_mak')?.focus();
                } else {
                    if (!isDisetujui) {
                        makSection.style.opacity = '0';
                        makSection.style.maxHeight = '0px';
                        makSection.style.transform = 'translateY(-10px)';
                    }
                }
            }
            
            commentBoxes.forEach(box => {
                box.classList.toggle('hidden', mode !== 'comment');
                if (mode === 'comment') {
                    box.style.opacity = '0';
                    box.style.maxHeight = '0px';
                    setTimeout(() => {
                         box.style.opacity = '1';
                         box.style.maxHeight = '200px';
                    }, 10);
                } else if (box) {
                    box.style.opacity = '0';
                    box.style.maxHeight = '0px';
                }
            });
        }

        btnLanjutMak?.addEventListener('click', () => {
            toggleReviewMode('approval');
        });

        btnKembaliReview?.addEventListener('click', () => {
            toggleReviewMode('review');
        });

        btnShowRevisi?.addEventListener('click', () => {
            toggleReviewMode('comment');
        });

        btnBatalRevisi?.addEventListener('click', () => {
            commentBoxes.forEach(box => {
                const textarea = box.querySelector('textarea');
                if (textarea) textarea.value = '';
            });
            toggleReviewMode('review');
        });
        
        btnKirimRevisi?.addEventListener('click', (e) => {
            e.preventDefault();
            
            let hasComment = false;
            commentBoxes.forEach(box => {
                const textarea = box.querySelector('textarea');
                if (textarea && textarea.value.trim() !== '') {
                    hasComment = true;
                }
            });

            if (!hasComment) {
                Swal.fire('Gagal', 'Anda harus mengisi setidaknya satu komentar revisi.', 'error');
                return;
            }

            Swal.fire({
                title: 'Kirim Komentar Revisi?',
                text: "Usulan akan dikembalikan ke pengusul dengan catatan revisi Anda.",
                icon: 'warning',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Kirim Revisi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mengirim...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                    formVerifikasi.action = "/docutrack/public/verifikator/telaah/revise/" + kegiatanId + "?ref=detail";

                    formVerifikasi.submit();
                }
            });
        });

        btnTolak?.addEventListener('click', () => {
            Swal.fire({
                title: 'Tolak Usulan Ini?',
                input: 'textarea',
                inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                inputAttributes: { 'aria-label': 'Tuliskan alasan penolakan' },
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Anda harus memberikan alasan penolakan!'
                    }
                },
                icon: 'error',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Tolak Usulan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Memproses...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    
                    const alasanInput = document.createElement('input');
                    alasanInput.type = 'hidden';
                    alasanInput.name = 'alasan_penolakan';
                    alasanInput.value = result.value;
                    formVerifikasi.appendChild(alasanInput);
                    
                    formVerifikasi.action = "/docutrack/public/verifikator/telaah/reject/" + kegiatanId + "?ref=detail";
                    formVerifikasi.submit();
                }
            });
        });

        btnSetujui?.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            const kodeMakInput = document.getElementById('kode_mak');
            const kodeMak = kodeMakInput ? kodeMakInput.value : '';

            if (!kodeMak || kodeMak.trim() === '') {
                Swal.fire('Error', 'Kode MAK wajib diisi sebelum menyetujui.', 'error');
                kodeMakInput?.focus();
                kodeMakInput?.classList.add('border-red-500', 'ring-2', 'ring-red-300');
                return;
            } else {
                 kodeMakInput?.classList.remove('border-red-500', 'ring-2', 'ring-red-300');
            }

            // Validasi grand_total_rab
            if (grandTotalRabValue <= 0) {
                Swal.fire('Error', 'Total dana RAB tidak valid. Pastikan RAB sudah diisi dengan benar.', 'error');
                return;
            }
            
            Swal.fire({
                title: 'Setujui Usulan Ini?',
                html: `Usulan akan disetujui dengan:<br>
                       <div class="swal-kegiatan-nama">${namaKegiatan}</div>
                       <div class="text-sm mt-2"><b>Kode MAK:</b> ${kodeMak}</div>
                       <div class="text-sm"><b>Total Dana:</b> ${formatRupiah(grandTotalRabValue)}</div>`,
                icon: 'success',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#16A34A',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menyetujui...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });                    
                    
                    formVerifikasi.action = "/docutrack/public/verifikator/telaah/approve/" + kegiatanId + "?ref=detail";

                    formVerifikasi.submit();
                }
            });
        });
        
    });
</script>