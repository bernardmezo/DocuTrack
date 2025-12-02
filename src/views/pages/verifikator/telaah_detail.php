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

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Telaah Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                    <?php elseif ($is_revisi): ?> <span class="font-semibold text-yellow-600">Menunggu Perbaikan Admin</span>
                    <?php elseif ($is_telah_direvisi): ?> <span class="font-semibold text-purple-600">Telah Direvisi</span>
                    <?php elseif ($is_ditolak): ?> <span class="font-semibold text-red-600">Ditolak</span>
                    <?php else: ?> <span class="font-semibold text-gray-600">Menunggu Verifikasi</span>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?php echo htmlspecialchars($back_url); ?>" 
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 w-full md:w-auto transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <?php if ($is_telah_direvisi && !empty($komentar_revisi)): ?>
        <div class="revision-alert-box bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8 animate-reveal">
            <div class="flex items-center">
                <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i></div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-yellow-800">Catatan Revisi Sebelumnya</h3>
                    <p class="text-sm text-yellow-700 mt-1">Admin telah memperbaiki usulan berdasarkan catatan ini. Harap telaah kembali.</p>
                </div>
            </div>
            <ul class="list-disc list-inside mt-4 pl-10 space-y-1 text-sm text-yellow-700">
                <?php foreach ($komentar_revisi as $field => $komentar): ?>
                    <li><span class="font-semibold"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</span> <?php echo htmlspecialchars($komentar); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if ($is_ditolak): ?>
        <div class="revision-alert-box bg-red-50 border-l-4 border-red-400 p-6 rounded-lg mb-8 animate-reveal">
             <div class="flex items-center">
                <div class="flex-shrink-0"><i class="fas fa-times-circle text-red-500 text-2xl"></i></div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-red-800">Usulan Ditolak</h3>
                    <p class="text-sm text-red-700 mt-1">Alasan Penolakan: "<?php echo htmlspecialchars($komentar_penolakan); ?>"</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="form-verifikasi" action="#" method="POST">
            
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center gap-2">
                    1. Kerangka Acuan Kegiatan (KAK)
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
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

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Nama Penanggung Jawab <?php showCommentIcon('nama_penanggung_jawab', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['nama_penanggung_jawab']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab'] ?? '-') ?>
                        </div>
                         <?php if (!$is_ditolak) render_comment_box('nama_penanggung_jawab', $is_menunggu, $is_telah_direvisi); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            NIM/NIP Penanggung Jawab <?php showCommentIcon('nip_penanggung_jawab', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['nip_penanggung_jawab']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab'] ?? '-') ?>
                        </div>
                         <?php if (!$is_ditolak) render_comment_box('nip_penanggung_jawab', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Nama Kegiatan <?php showCommentIcon('nama_kegiatan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </label>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['nama_kegiatan']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
                    </div>
                    <?php if (!$is_ditolak) render_comment_box('nama_kegiatan', $is_menunggu, $is_telah_direvisi); ?>
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Gambaran Umum <?php showCommentIcon('gambaran_umum', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </label>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[100px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['gambaran_umum']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')) ?>
                    </div>
                    <?php if (!$is_ditolak) render_comment_box('gambaran_umum', $is_menunggu, $is_telah_direvisi); ?>
                </div>

                <div class="mb-8">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                        Penerima Manfaat <?php showCommentIcon('penerima_manfaat', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </label>
                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['penerima_manfaat']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                        <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')) ?>
                    </div>
                     <?php if (!$is_ditolak) render_comment_box('penerima_manfaat', $is_menunggu, $is_telah_direvisi); ?>
                </div>

                <div class="border-t border-gray-200 pt-6 mb-6">
                    <h4 class="text-lg font-bold text-gray-800 mb-5 pb-2">Strategi Pencapaian Keluaran</h4>
                    
                    <div class="mb-6">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Metode Pelaksanaan <?php showCommentIcon('metode_pelaksanaan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[80px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['metode_pelaksanaan']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')) ?>
                        </div>
                        <?php if (!$is_ditolak) render_comment_box('metode_pelaksanaan', $is_menunggu, $is_telah_direvisi); ?>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                            Tahapan Kegiatan <?php showCommentIcon('tahapan_kegiatan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[100px] <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['tahapan_kegiatan']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                            <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')) ?>
                        </div>
                        <?php if (!$is_ditolak) render_comment_box('tahapan_kegiatan', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                </div>
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 200ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU)
                    <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Indikator yang Dipilih:</label>
                <div class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-gray-100 rounded-lg border border-gray-200 <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['iku_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
                    <?php if (!empty($iku_data)): ?>
                        <?php foreach ($iku_data as $iku_item): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?php echo htmlspecialchars($iku_item); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-sm text-gray-500 italic">Tidak ada IKU yang dipilih.</span>
                    <?php endif; ?>
                </div>
                <?php if (!$is_ditolak) render_comment_box('iku_data', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 300ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    3. Indikator Kinerja KAK
                    <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['indikator_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['bulan'] ?? 'N/A'); ?></td>
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

            <div class="mb-8 animate-reveal" style="animation-delay: 400ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                <?php 
                    $grand_total_rab = 0;
                    if (!empty($rab_data)):
                        foreach ($rab_data as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal = 0;
                            $rab_comment_key = 'rab_' . strtolower(str_replace(' ', '_', $kategori));
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                        <?php echo htmlspecialchars($kategori); ?>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi[$rab_comment_key]) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                                <?php foreach ($items as $item): 
                                    $vol1 = $item['vol1'] ?? 0;
                                    $sat1 = $item['sat1'] ?? '';
                                    $vol2 = $item['vol2'] ?? 1;
                                    $sat2 = $item['sat2'] ?? '';
                                    $harga = $item['harga'] ?? 0;
                                    $total_item = $vol1 * $vol2 * $harga;
                                    $subtotal += $total_item;
                                ?>
                                <tr>
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
                
                <div class="flex justify-end mt-4">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <span class="text-sm font-medium text-gray-700">Grand Total RAB: </span>
                        <span class="text-xl font-bold text-blue-600"><?php echo formatRupiah($grand_total_rab); ?></span>
                    </div>
                </div>
            </div>

            <div class="mb-8 pt-6 border-t border-gray-200 animate-reveal" style="animation-delay: 500ms;">
                <h3 class="text-xl font-bold text-gray-800 pb-3 mb-4 border-b border-gray-200">5. Rincian Rancangan Kegiatan</h3>
                
                <div class="mb-6">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Surat Pengantar</label>
                    <div class="relative max-w-2xl">
                        <?php if (!empty($surat_pengantar)): ?>
                            <div class="flex items-center justify-between gap-3 px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <i class="fas fa-file-pdf text-red-500 text-xl flex-shrink-0"></i>
                                    <span class="text-sm text-gray-800 font-medium truncate" title="<?php echo htmlspecialchars($surat_pengantar); ?>">
                                        <?php echo htmlspecialchars($surat_pengantar); ?>
                                    </span>
                                </div>
                                <a href="<?php echo htmlspecialchars($surat_pengantar_url); ?>" target="_blank" 
                                   class="text-blue-600 hover:text-blue-700 transition-colors flex-shrink-0">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center justify-between px-4 py-3.5 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-file-pdf text-gray-300 text-xl"></i>
                                    <span class="text-sm text-gray-400 italic">Belum ada file yang diunggah</span>
                                </div>
                                <i class="fas fa-times-circle text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Kurun Waktu Pelaksanaan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block mb-1">Tanggal Mulai</span>
                                <span class="text-sm text-gray-800 font-semibold">
                                    <?php 
                                    if (isValidDate($tanggal_mulai)) {
                                        echo formatTanggal($tanggal_mulai);
                                    } else {
                                        echo '<span class="text-gray-400 italic font-normal">Belum ditentukan</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt <?php echo isValidDate($tanggal_mulai) ? 'text-blue-500' : 'text-gray-300'; ?> text-lg"></i>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block mb-1">Tanggal Selesai</span>
                                <span class="text-sm text-gray-800 font-semibold">
                                    <?php 
                                    if (isValidDate($tanggal_selesai)) {
                                        echo formatTanggal($tanggal_selesai);
                                    } else {
                                        echo '<span class="text-gray-400 italic font-normal">Belum ditentukan</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-check <?php echo isValidDate($tanggal_selesai) ? 'text-green-500' : 'text-gray-300'; ?> text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="mak-section" class="mt-8 pt-6 border-t border-gray-200 animate-reveal 
                <?php echo ($is_disetujui) ? 'block' : 'hidden'; ?>" 
                style="<?php echo (($is_menunggu || $is_telah_direvisi) && !$is_ditolak) ? 'opacity: 0; max-height: 0px; overflow: hidden; transform: translateY(-10px); transition: all 0.4s ease-out;' : 'animation-delay: 500ms;'; ?>"
                >
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    6. Kode Mata Anggaran Kegiatan (MAK)
                    <?php showCommentIcon('kode_mak', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <div class="relative max-w-md">
                    <i class="fas fa-key absolute top-3.5 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                    <input type="text" id="kode_mak" name="kode_mak" 
                           class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['kode_mak']) ? 'border-yellow-500 ring-2 ring-yellow-300' : ''; ?>" 
                           value="<?php echo htmlspecialchars($kode_mak); ?>" placeholder=" " 
                           <?php echo (($is_disetujui || $is_ditolak) && !empty($kode_mak)) ? 'readonly' : ''; ?>>
                    <label for="kode_mak" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Masukkan Kode MAK</label>
                </div>
                 <?php if (($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['kode_mak'])): ?>
                    <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['kode_mak']); ?></p>
                <?php endif; ?>
                <?php if (!$is_ditolak) render_comment_box('kode_mak', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto">
                
                <?php if ($is_menunggu || $is_telah_direvisi): ?>
                    <div id="review-actions" class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto">
                        <button type="button" id="btn-lanjut-mak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-0.5">
                            Lanjut <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                        <button type="button" id="btn-show-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-edit text-xs"></i> Revisi
                        </button>
                        <button type="button" id="btn-tolak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-times text-xs"></i> Tolak
                        </button>
                    </div>
                    
                    <div id="approval-actions" class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto hidden">
                        <button type="submit" id="btn-setujui-usulan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5">
                             <i class="fas fa-check-double text-xs"></i> Setujui Usulan
                        </button>
                        <button type="button" id="btn-kembali-review" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5">
                             Kembali
                        </button>
                    </div>
                    
                    <div id="comment-actions" class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto hidden">
                        <button type="submit" id="btn-kirim-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5">
                             <i class="fas fa-paper-plane text-xs"></i> Kirim Komentar Revisi
                        </button>
                        <button type="button" id="btn-batal-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5">
                             Batal
                        </button>
                    </div>
                <?php endif; ?>
                 </div>
            </div>
        </form>
        
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        const isDisetujui = <?php echo json_encode($is_disetujui); ?>;
        const namaKegiatan = <?php echo json_encode($kegiatan_data['nama_kegiatan'] ?? 'Kegiatan Ini'); ?>;
        const kegiatanId = <?php echo json_encode($kegiatanId ?: $id); ?>;

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
            
            Swal.fire({
                title: 'Setujui Usulan Ini?',
                html: `Usulan akan disetujui dengan Kode MAK:<br><div class="swal-kegiatan-nama">${namaKegiatan}</div>`,
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