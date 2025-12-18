<?php
// File: src/views/pages/admin/detail_kegiatan.php

$status = $status ?? 'Menunggu';
$user_role = $user_role ?? 'admin';

$is_revisi = (strtolower($status) === 'revisi');
$is_disetujui = (strtolower($status) === 'disetujui' || strtolower($status) === 'usulan disetujui');
$is_ditolak = (strtolower($status) === 'ditolak');

// Get kegiatan ID for edit link
$kegiatanId = $kegiatan_data['kegiatanId'] ?? ($id ?? 0);

$komentar_revisi = $komentar_revisi ?? [];
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/admin/dashboard'; 

$surat_pengantar = $kegiatan_data['surat_pengantar'] ?? '';
$surat_pengantar_url = $surat_pengantar_url ?? '';
$tanggal_mulai = $kegiatan_data['tanggal_mulai'] ?? '';
$tanggal_selesai = $kegiatan_data['tanggal_selesai'] ?? '';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}
if (!function_exists('isValidDate')) {
    function isValidDate($date) { return !empty($date) && $date !== '0000-0000' && strtotime($date) !== false; }
}
if (!function_exists('formatTanggal')) {
    function formatTanggal($date, $format = 'd M Y') {
        return isValidDate($date) ? date($format, strtotime($date)) : '-';
    }
}
function displayValue($value, $placeholder = 'Belum diisi') {
    $text = !empty($value) ? htmlspecialchars($value) : '<span class="text-gray-400 italic">' . $placeholder . '</span>';
    return is_string($text) ? $text : '';
}
function showCommentIcon($field_name, $komentar_list, $is_revisi_mode) {
    if ($is_revisi_mode && isset($komentar_list[$field_name])) {
        $comment = htmlspecialchars($komentar_list[$field_name]);
        $html = "<span class='comment-icon-wrapper relative inline-flex items-center ml-2 group'>";
        $html .= "<span class='comment-icon flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 border-2 border-yellow-400 cursor-pointer transition-all duration-300 hover:bg-yellow-200 hover:scale-110 hover:shadow-lg hover:shadow-yellow-200'>";
        $html .= "<i class='fas fa-comment-dots text-yellow-600 text-sm group-hover:animate-pulse'></i>";
        $html .= "</span>";
        $html .= "<span class='comment-tooltip invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-1/2 -translate-x-1/2 bottom-full mb-3 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50'>";
        $html .= "<span class='flex items-center gap-2 text-yellow-400 font-semibold mb-1'><i class='fas fa-exclamation-circle'></i> Catatan Revisi</span>";
        $html .= "<span class='block text-gray-200 leading-relaxed'>{$comment}</span>";
        $html .= "<span class='absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-gray-900'></span>";
        $html .= "</span>";
        $html .= "</span>";
        echo $html;
    }
}
function isEditable($field_name, $is_revisi_mode, $komentar_list) {
    return $is_revisi_mode && isset($komentar_list[$field_name]);
}
?>

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-2xl shadow-lg overflow-hidden mb-6 sm:mb-8">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4 sm:mb-6 pb-4 sm:pb-5 border-b border-gray-200 gap-3 sm:gap-4">
            <div class="w-full lg:w-auto">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800">Detail Usulan Kegiatan</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                    <span>Status:</span>
                    <?php 
                    $status_class = $is_disetujui ? 'bg-green-100 text-green-700' : ($is_revisi ? 'bg-yellow-100 text-yellow-700' : ($is_ditolak ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700'));
                    $status_icon = $is_disetujui ? 'fas fa-check-circle' : ($is_revisi ? 'fas fa-exclamation-triangle' : ($is_ditolak ? 'fas fa-times-circle' : 'fas fa-hourglass-half'));
                    ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-xs font-semibold <?= $status_class; ?>">
                        <i class="<?= $status_icon; ?>"></i> <?= htmlspecialchars($status); ?>
                    </span>
                </p>
            </div>
        </div>

        <?php if ($is_revisi && !empty($komentar_revisi)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 sm:p-6 rounded-lg mb-6 sm:mb-8">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl sm:text-2xl mt-0.5 flex-shrink-0"></i>
                <div class="ml-3 sm:ml-4 flex-1">
                    <h3 class="text-base sm:text-lg font-bold text-yellow-800">Perlu Revisi</h3>
                    <p class="text-xs sm:text-sm text-yellow-700 mt-1">Harap perbaiki bagian yang ditandai:</p>
                    <ul class="list-disc list-inside mt-3 sm:mt-4 pl-0 space-y-1 text-xs sm:text-sm text-yellow-700">
                        <?php foreach ($komentar_revisi as $field => $komentar): ?>
                            <li class="break-words"><span class="font-semibold"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</span> <?= htmlspecialchars($komentar); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php elseif ($is_ditolak): ?>
        <div class="bg-red-50 border-l-4 border-red-400 p-4 sm:p-6 rounded-lg mb-6 sm:mb-8">
            <div class="flex items-start">
                <i class="fas fa-times-circle text-red-500 text-xl sm:text-2xl mt-0.5 flex-shrink-0"></i>
                <div class="ml-3 sm:ml-4 flex-1">
                    <h3 class="text-base sm:text-lg font-bold text-red-800">Usulan Ditolak</h3>
                    <?php if (!empty($komentar_penolakan)): ?>
                        <p class="text-xs sm:text-sm text-red-700 mt-2 mb-1 font-semibold">Alasan Penolakan:</p>
                        <div class="bg-white p-3 rounded border border-red-200 text-sm text-gray-700 mt-1">
                            <?= nl2br(htmlspecialchars($komentar_penolakan)); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-xs sm:text-sm text-red-700 mt-1 italic">Tidak ada alasan penolakan yang tercatat.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="form-verifikasi" action="#" method="POST" enctype="multipart/form-data">
            
            <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-sm border border-gray-100 mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 pb-3 border-b border-gray-100">
                    <span class="hidden sm:inline">1.</span> Kerangka Acuan Kerja (KAK)
                </h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
                    <?php 
                    $kak_details = [
                        'Nama Pengusul' => $kegiatan_data['nama_pengusul'] ?? '-',
                        'NIM Pengusul' => $kegiatan_data['nim_pengusul'] ?? '-',
                        'Penanggung Jawab' => $kegiatan_data['nama_penanggung_jawab'] ?? '-',
                        'NIM/NIP PJ' => $kegiatan_data['nip_penanggung_jawab'] ?? '-',
                    ];
                    foreach ($kak_details as $label => $value): ?>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2"><?= $label; ?></label>
                            <div class="p-2.5 sm:p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-xs sm:text-sm break-words">
                                <?= htmlspecialchars($value); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php 
                $kak_long_texts = [
                    'Nama Kegiatan' => ['key' => 'nama_kegiatan', 'min_h' => 'min-h-[40px]', 'value' => $kegiatan_data['nama_kegiatan'] ?? '-'],
                    'Gambaran Umum' => ['key' => 'gambaran_umum', 'min_h' => 'min-h-[80px]', 'value' => nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-'))],
                    'Penerima Manfaat' => ['key' => 'penerima_manfaat', 'min_h' => 'min-h-[60px]', 'value' => nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-'))],
                    'Metode Pelaksanaan' => ['key' => 'metode_pelaksanaan', 'min_h' => 'min-h-[60px]', 'value' => nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-'))],
                    'Tahapan Kegiatan' => ['key' => 'tahapan_kegiatan', 'min_h' => 'min-h-[80px]', 'value' => nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-'))],
                ];
                foreach ($kak_long_texts as $label => $data): ?>
                    <div class="mb-4 sm:mb-6 <?= in_array($label, ['Metode Pelaksanaan', 'Tahapan Kegiatan']) ? 'mt-6 sm:mt-8 border-t border-gray-100 pt-4 sm:pt-6' : ''; ?>">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2"><?= $label; ?></label>
                        <div class="p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-xs sm:text-sm leading-relaxed <?= $data['min_h']; ?> break-words">
                            <?= $data['value']; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200 flex items-center">
                    <span class="hidden sm:inline">2.</span> Indikator Kinerja Utama (IKU) <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Indikator yang Dipilih:</label>
                <div class="flex flex-wrap items-center gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                    <?php if (!empty($iku_data)): ?>
                        <?php foreach ($iku_data as $iku_item): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800 break-all">
                                <i class="fas fa-check-circle text-xs flex-shrink-0"></i>
                                <span class="break-words"><?= htmlspecialchars($iku_item); ?></span>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-xs sm:text-sm text-gray-400 italic">Tidak ada IKU yang dipilih.</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200 flex items-center">
                    <span class="hidden sm:inline">3.</span> Indikator Kinerja KAK <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                
                <?php if (!empty($indikator_data)): ?>
                    <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full min-w-[500px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-1/6">Bulan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-4/6">Indikator Keberhasilan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-1/6">Target (%)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <?php foreach ($indikator_data as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= displayValue($item['bulan'] ?? '', '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= displayValue($item['nama'] ?? '', '-'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <?php $target = $item['target'] ?? 0; ?>
                                        <?= $target > 0 ? 
                                            '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">' . htmlspecialchars($target) . '%</span>' : 
                                            '<span class="text-gray-400 italic">-</span>'; 
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="md:hidden space-y-3">
                        <?php foreach ($indikator_data as $item): ?>
                        <div class="p-3 bg-white border border-gray-200 rounded-lg">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Bulan</span>
                                    <p class="text-sm text-gray-800 font-medium mt-0.5"><?= displayValue($item['bulan'] ?? '', '-'); ?></p>
                                </div>
                                <?php $target = $item['target'] ?? 0; ?>
                                <?= $target > 0 ? 
                                    '<span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700"><i class="fas fa-bullseye"></i> ' . htmlspecialchars($target) . '%</span>' : ''; 
                                ?>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Indikator</span>
                                <p class="text-sm text-gray-700 leading-snug break-words"><?= displayValue($item['nama'] ?? '', '-'); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-6 text-center text-gray-400 italic text-sm bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                        Tidak ada data indikator kinerja
                    </div>
                <?php endif; ?>
            </div>

            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">4.</span> Rincian Anggaran Biaya (RAB)
                </h3>
                <?php 
                    $grand_total_rab = 0;
                    if (!empty($rab_data)):
                        foreach ($rab_data as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal = 0;
                            $rab_comment_key = 'rab_' . strtolower(str_replace(' ', '_', $kategori));
                            $has_rab_comment = isEditable($rab_comment_key, $is_revisi, $komentar_revisi);
                ?>
                    <h4 class="text-sm sm:text-base font-semibold text-gray-700 mt-4 mb-2 sm:mb-3 flex items-center gap-2">
                        <i class="fas fa-folder-open text-blue-600"></i>
                        <span class="break-words"><?= htmlspecialchars($kategori); ?></span>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi); ?>
                    </h4>
                    
                    <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg <?= $has_rab_comment ? 'ring-2 ring-yellow-400' : ''; ?> mb-4">
                        <table class="w-full min-w-[1000px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Uraian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase w-1/5">Rincian</th>
                                    <th class="px-1 py-3 text-center text-xs font-bold text-gray-600 uppercase w-1/12">Vol 1</th>
                                    <th class="px-1 py-3 text-center text-xs font-bold text-gray-600 uppercase w-1/12">Sat 1</th>
                                    <th class="px-1 py-3 text-center text-xs font-bold text-gray-600 uppercase w-1/12">Vol 2</th>
                                    <th class="px-1 py-3 text-center text-xs font-bold text-gray-600 uppercase w-1/12">Sat 2</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-1/12">Harga (Rp)</th>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase w-1/12">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <?php 
                                foreach ($items as $item): 
                                    $vol1 = $item['vol1'] ?? 0; $sat1 = $item['sat1'] ?? '-';
                                    $vol2 = $item['vol2'] ?? 1; $sat2 = $item['sat2'] ?? '-';
                                    $harga = $item['harga'] ?? 0;
                                    $total_item = ($vol1 * $vol2 * $harga);
                                    $subtotal += $total_item;
                                ?>
                                <tr class="<?= $has_rab_comment ? 'bg-yellow-50' : 'hover:bg-gray-50'; ?>">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <?= $has_rab_comment ? '<input type="text" name="rab['.$kategori.'][uraian][]" class="w-full text-sm p-2 border border-gray-300 rounded-md" value="'.htmlspecialchars($item['uraian'] ?? '').'">' : displayValue($item['uraian']); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <?= $has_rab_comment ? '<input type="text" name="rab['.$kategori.'][rincian][]" class="w-full text-sm p-2 border border-gray-300 rounded-md" value="'.htmlspecialchars($item['rincian'] ?? '').'">' : displayValue($item['rincian']); ?>
                                    </td>
                                    <td class="px-1 py-3 text-sm text-gray-700 text-center">
                                        <?= $has_rab_comment ? '<input type="number" name="rab['.$kategori.'][vol1][]" class="w-full text-sm p-1 text-center border border-gray-300 rounded-md" value="'.$vol1.'">' : ($vol1 > 0 ? htmlspecialchars($vol1) : '-'); ?>
                                    </td>
                                    <td class="px-1 py-3 text-sm text-gray-700 text-center">
                                        <?= $has_rab_comment ? '<input type="text" name="rab['.$kategori.'][sat1][]" class="w-full text-sm p-1 text-center border border-gray-300 rounded-md" value="'.htmlspecialchars($sat1).'">' : htmlspecialchars($sat1); ?>
                                    </td>
                                    <td class="px-1 py-3 text-sm text-gray-700 text-center">
                                        <?= $has_rab_comment ? '<input type="number" name="rab['.$kategori.'][vol2][]" class="w-full text-sm p-1 text-center border border-gray-300 rounded-md" value="'.$vol2.'">' : ($vol2 > 0 ? htmlspecialchars($vol2) : '-'); ?>
                                    </td>
                                    <td class="px-1 py-3 text-sm text-gray-700 text-center">
                                        <?= $has_rab_comment ? '<input type="text" name="rab['.$kategori.'][sat2][]" class="w-full text-sm p-1 text-center border border-gray-300 rounded-md" value="'.htmlspecialchars($sat2).'">' : htmlspecialchars($sat2); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 text-right">
                                        <?= $has_rab_comment ? '<input type="number" name="rab['.$kategori.'][harga][]" class="w-full text-sm p-2 text-right border border-gray-300 rounded-md" value="'.$harga.'">' : ($harga > 0 ? number_format($harga, 0, ',', '.') : '<span class="text-gray-400">-</span>'); ?>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right"><?= formatRupiah($total_item); ?></td>
                                </tr>
                                <?php endforeach; $grand_total_rab += $subtotal; ?>
                                <tr class="bg-blue-50 font-semibold">
                                    <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal <?= htmlspecialchars($kategori); ?></td>
                                    <td class="px-4 py-3 text-sm text-blue-700 text-right"><?= formatRupiah($subtotal); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="lg:hidden space-y-3 mb-4">
                        <?php 
                        foreach ($items as $item): 
                            $vol1 = $item['vol1'] ?? 0; $sat1 = $item['sat1'] ?? '-';
                            $vol2 = $item['vol2'] ?? 1; $sat2 = $item['sat2'] ?? '-';
                            $harga = $item['harga'] ?? 0;
                            $total_item = ($vol1 * $vol2 * $harga);
                        ?>
                        <div class="p-3 bg-white border border-gray-200 rounded-lg <?= $has_rab_comment ? 'ring-2 ring-yellow-400 bg-yellow-50' : ''; ?>">
                            <div class="space-y-2 text-xs">
                                <p><span class="text-gray-500 font-semibold block mb-0.5">Uraian:</span>
                                    <?= $has_rab_comment ? '<input type="text" name="rab['.$kategori.'][uraian][]" class="w-full text-sm p-2 border border-gray-300 rounded-md bg-yellow-50" value="'.htmlspecialchars($item['uraian'] ?? '').'">' : '<span class="text-gray-800 break-words">'.displayValue($item['uraian'], '-').'</span>'; ?></p>
                                <p><span class="text-gray-500 font-semibold block mb-0.5">Rincian:</span>
                                    <?= $has_rab_comment ? '<input type="text" name="rab['.$kategori.'][rincian][]" class="w-full text-sm p-2 border border-gray-300 rounded-md bg-yellow-50" value="'.htmlspecialchars($item['rincian'] ?? '').'">' : '<span class="text-gray-800 break-words">'.displayValue($item['rincian'], '-').'</span>'; ?></p>
                                
                                <div class="grid grid-cols-2 gap-2">
                                    <p><span class="text-gray-500 font-semibold block mb-0.5">Vol 1 / Sat 1:</span>
                                        <?= $has_rab_comment ? 
                                            '<div class="flex gap-1"><input type="number" name="rab['.$kategori.'][vol1][]" class="w-1/2 text-sm p-1 text-center border border-gray-300 rounded-md bg-yellow-50" value="'.$vol1.'"><input type="text" name="rab['.$kategori.'][sat1][]" class="w-1/2 text-sm p-1 text-center border border-gray-300 rounded-md bg-yellow-50" value="'.htmlspecialchars($sat1).'"></div>' : 
                                            '<span class="text-gray-800">'.($vol1 > 0 ? htmlspecialchars($vol1) . ' ' . htmlspecialchars($sat1) : '-').'</span>'; ?></p>
                                    
                                    <p><span class="text-gray-500 font-semibold block mb-0.5">Vol 2 / Sat 2:</span>
                                        <?= $has_rab_comment ? 
                                            '<div class="flex gap-1"><input type="number" name="rab['.$kategori.'][vol2][]" class="w-1/2 text-sm p-1 text-center border border-gray-300 rounded-md bg-yellow-50" value="'.$vol2.'"><input type="text" name="rab['.$kategori.'][sat2][]" class="w-1/2 text-sm p-1 text-center border border-gray-300 rounded-md bg-yellow-50" value="'.htmlspecialchars($sat2).'"></div>' : 
                                            '<span class="text-gray-800">'.($vol2 > 0 ? htmlspecialchars($vol2) . ' ' . htmlspecialchars($sat2) : '-').'</span>'; ?></p>
                                </div>
                                
                                <div class="pt-2 border-t border-gray-100 mt-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 font-semibold">Harga Satuan</span>
                                        <span class="text-gray-800 font-medium">
                                            <?= $has_rab_comment ? 
                                                '<input type="number" name="rab['.$kategori.'][harga][]" class="w-20 text-sm p-1 text-right border border-gray-300 rounded-md bg-yellow-50" value="'.$harga.'">' : 
                                                'Rp '.number_format($harga, 0, ',', '.'); ?>
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-gray-700 font-bold">Total</span>
                                        <span class="text-blue-600 font-bold text-sm"><?= formatRupiah($total_item); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="p-3 bg-blue-50 border-2 border-blue-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-800">Subtotal <?= htmlspecialchars($kategori); ?></span>
                                <span class="text-sm font-bold text-blue-700"><?= formatRupiah($subtotal); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($has_rab_comment): ?>
                        <p class="text-xs text-yellow-600 mt-1 italic mb-4">
                            <i class="fas fa-comment-dots"></i> <?= htmlspecialchars($komentar_revisi[$rab_comment_key]); ?>
                        </p>
                    <?php endif; ?>
                <?php endforeach; else: ?>
                    <div class="p-6 sm:p-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-center">
                        <i class="fas fa-inbox text-3xl sm:text-4xl text-gray-300 mb-3"></i>
                        <p class="text-xs sm:text-sm text-gray-400 italic">Tidak ada data RAB.</p>
                    </div>
                <?php endif; ?>
                
                <div class="flex justify-end mt-4 sm:mt-6">
                    <div class="w-full sm:w-auto p-4 sm:p-5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg border-2 border-blue-200 shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calculator text-xl sm:text-2xl text-blue-600 flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider block">Grand Total RAB</span>
                                <span class="text-lg sm:text-2xl font-bold text-blue-600 break-all"><?= formatRupiah($grand_total_rab); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($is_disetujui): ?>
            <div class="mb-6 sm:mb-8 pt-4 sm:pt-6 border-t border-gray-200">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">5.</span> Rincian Rancangan Kegiatan
                </h3>
                
                <div class="mb-4 sm:mb-6">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Surat Pengantar</label>
                    <div class="relative">
                        <?php if (!empty($surat_pengantar)): ?>
                            <div class="flex items-center justify-between gap-3 px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-3 min-w-0 flex-1">
                                    <i class="fas fa-file-pdf text-red-500 text-xl flex-shrink-0"></i>
                                    <span class="text-sm text-gray-800 font-medium truncate" title="<?= htmlspecialchars($surat_pengantar); ?>">
                                        <?= htmlspecialchars($surat_pengantar); ?>
                                    </span>
                                </div>
                                <a href="<?= htmlspecialchars($surat_pengantar_url); ?>" target="_blank" class="text-blue-600 hover:text-blue-700 transition-colors flex-shrink-0 p-1">
                                    <i class="fas fa-download text-base"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="px-4 py-3.5 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-sm text-gray-400 italic flex items-center gap-3">
                                <i class="fas fa-file-pdf text-gray-300 text-xl"></i> Belum ada file yang diunggah
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Kurun Waktu Pelaksanaan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php 
                        $dates = [
                            'Tanggal Mulai' => ['date' => $tanggal_mulai, 'icon' => 'fas fa-calendar-alt', 'color' => 'text-blue-500'],
                            'Tanggal Selesai' => ['date' => $tanggal_selesai, 'icon' => 'fas fa-calendar-check', 'color' => 'text-green-500'],
                        ];
                        foreach ($dates as $label => $data): 
                            $is_valid = isValidDate($data['date']);
                            $icon_color = $is_valid ? $data['color'] : 'text-gray-300';
                            $date_display = $is_valid ? formatTanggal($data['date']) : '<span class="text-gray-400 italic font-normal">Belum ditentukan</span>';
                        ?>
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs text-gray-500 block mb-1"><?= $label; ?></span>
                                <span class="text-sm text-gray-800 font-semibold break-words"><?= $date_display; ?></span>
                            </div>
                            <i class="<?= $data['icon']; ?> <?= $icon_color; ?> text-lg flex-shrink-0 ml-2"></i>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-6 sm:mb-8 pt-4 sm:pt-6 border-t border-gray-200">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">6.</span> Kode Mata Anggaran Kegiatan (MAK)
                </h3>
                <div class="relative">
                    <?php if (!empty($kode_mak)): ?>
                        <div class="flex items-center gap-3 px-4 py-3.5 bg-green-50 rounded-lg border-2 border-green-200">
                            <i class="fas fa-key text-green-600 text-lg flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-green-600 font-semibold block mb-1">Kode MAK</span>
                                <span class="text-sm text-gray-800 font-mono font-bold break-all"><?= htmlspecialchars($kode_mak); ?></span>
                            </div>
                            <i class="fas fa-check-circle text-green-600 text-xl flex-shrink-0"></i>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-3 px-4 py-3.5 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <i class="fas fa-key text-gray-300 text-lg flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-gray-500 block mb-1">Kode MAK</span>
                                <span class="text-sm text-gray-400 italic">Belum tersedia</span>
                            </div>
                            <i class="fas fa-minus-circle text-gray-300 flex-shrink-0"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-start gap-1.5"><i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i><span class="italic">Kode MAK akan diberikan setelah usulan disetujui</span></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mt-6 sm:mt-10 pt-4 sm:pt-6 border-t border-gray-200 gap-3 sm:gap-4 no-print">
                <a href="<?= htmlspecialchars($back_url); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 text-sm sm:text-base order-2 sm:order-1">
                    <i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span>
                </a>
                <div class="flex flex-col sm:flex-row-reverse gap-3 sm:gap-4 w-full sm:w-auto order-1 sm:order-2">
                    <?php if ($is_revisi): ?>
                        <a href="/docutrack/public/admin/detail-kak/<?= htmlspecialchars($kegiatan_data['kegiatanId'] ?? $id ?? 0) ?>/edit-usulan" 
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-6 py-2.5 sm:py-3 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg text-sm sm:text-base">
                            <i class="fas fa-edit"></i> <span>Edit Usulan</span>
                        </a>
                    <?php elseif ($is_disetujui): ?>
                        <button type="button" id="print-pdf-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-6 py-2.5 sm:py-3 rounded-lg shadow-md hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg text-sm sm:text-base">
                            <i class="fas fa-print"></i> <span>Print PDF</span>
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
    
    // Fungsi untuk menghitung ulang total RAB di mode revisi (Hanya visual/perhitungan front-end)
    const updateRABTotal = () => {
        const formatRupiahJS = (angka) => 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(angka) || 0);

        document.querySelectorAll('table[data-kategori]').forEach(table => {
            let subtotalDesktop = 0;
            const kategori = table.getAttribute('data-kategori');
            
            // 1. Hitung ulang RAB Desktop
            table.querySelectorAll('tbody tr').forEach(row => {
                const uraianInput = row.querySelector('input[name*="[uraian]"]');
                if (uraianInput) {
                    const vol1 = parseFloat(row.querySelector(`input[name="rab[${kategori}][vol1][]"]`)?.value) || 0;
                    const vol2 = parseFloat(row.querySelector(`input[name="rab[${kategori}][vol2][]"]`)?.value) || 1;
                    const harga = parseFloat(row.querySelector(`input[name="rab[${kategori}][harga][]"]`)?.value) || 0;
                    const totalItem = vol1 * vol2 * harga;
                    subtotalDesktop += totalItem;

                    // Update total item di baris tabel
                    const totalCell = row.querySelector('td:last-child');
                    if (totalCell) totalCell.textContent = formatRupiahJS(totalItem);
                }
            });

            // Update subtotal desktop
            const subtotalCell = table.querySelector('.bg-blue-50 td:last-child');
            if (subtotalCell) subtotalCell.textContent = formatRupiahJS(subtotalDesktop);

            // 2. Sinkronisasi & Hitung ulang RAB Mobile
            const mobileContainer = document.querySelector(`.lg:hidden[data-kategori="${kategori}"]`);
            let subtotalMobile = 0;

            if (mobileContainer) {
                mobileContainer.querySelectorAll('.p-3.bg-white').forEach(card => {
                    const vol1Input = card.querySelector('input[name*="[vol1]"]');
                    if (vol1Input) {
                         const vol1 = parseFloat(card.querySelector('input[name*="[vol1]"]')?.value) || 0;
                         const vol2 = parseFloat(card.querySelector('input[name*="[vol2]"]')?.value) || 1;
                         const harga = parseFloat(card.querySelector('input[name*="[harga]"]')?.value) || 0;
                         const totalItem = vol1 * vol2 * harga;
                         subtotalMobile += totalItem;

                         // Update total item di card mobile
                         const totalDisplay = card.querySelector('.pt-2.border-t .text-blue-600');
                         if (totalDisplay) totalDisplay.textContent = formatRupiahJS(totalItem);
                    }
                });

                // Update subtotal mobile
                const subtotalMobileCell = mobileContainer.querySelector('.bg-blue-50 .text-blue-700');
                if (subtotalMobileCell) subtotalMobileCell.textContent = formatRupiahJS(subtotalMobile);
            }
            
            // Catatan: Grand Total harus dihitung ulang di server setelah submit,
            // tetapi untuk tampilan visual, kita bisa mengambil grand total dari PHP ($grand_total_rab) 
            // atau menghitungnya di JS (yang kompleks karena loop PHP). Karena ini hanya mode revisi,
            // kita fokus memastikan data input sudah benar.
        });
    };

    // Panggil updateRABTotal setiap kali ada perubahan di input RAB (HANYA JIKA ADA INPUT DI MODE REVISI)
    if ('<?php echo $is_revisi; ?>') {
        document.querySelectorAll('input[name*="rab["]').forEach(input => {
            input.addEventListener('input', updateRABTotal);
        });
        // Panggil sekali saat load untuk inisialisasi total jika ada data awal
        updateRABTotal(); 
    }

    // Print PDF - Redirect to PDF download endpoint
    document.getElementById('print-pdf-btn')?.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Get kegiatan ID from current URL or data attribute
        const currentPath = window.location.pathname;
        const kegiatanId = currentPath.split('/').pop(); // Assumes URL like /admin/detail-kak/{id}
        
        // Redirect to PDF download endpoint
        window.location.href = `/docutrack/public/admin/detail-kak/${kegiatanId}/pdf`;
    });

    // Simpan Revisi
    document.getElementById('btn-simpan-revisi')?.addEventListener('click', (e) => {
        e.preventDefault();
        
        // Cek apakah SweetAlert (Swal) tersedia
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Perubahan akan disimpan dan dikirim untuk verifikasi.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="fas fa-check mr-2"></i> Ya, Simpan!',
                cancelButtonText: '<i class="fas fa-times mr-2"></i> Batal',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg px-5 py-2.5',
                    cancelButton: 'rounded-lg px-5 py-2.5'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ 
                        title: 'Menyimpan...', 
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading() 
                    });
                    formVerifikasi.submit(); // Kirim form
                }
            });
        } else {
            if (confirm('Simpan perubahan?')) {
                formVerifikasi.submit(); // Kirim form
            }
        }
    });
});
</script>

<style>
/* Improve mobile touch targets */
@media (max-width: 768px) {
    button, a {
        min-height: 44px;
    }
    
    input[type="radio"], input[type="checkbox"] {
        width: 20px;
        height: 20px;
    }
}

/* Print styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white;
    }
    
    .shadow-lg, .shadow-md, .shadow-sm {
        box-shadow: none !important;
    }
}

/* Smooth transitions */
button, a {
    transition: all 0.3s ease;
}

/* Comment tooltip animation */
.comment-icon-wrapper .comment-tooltip {
    pointer-events: none;
}

.comment-icon-wrapper:hover .comment-tooltip {
    pointer-events: auto;
}
</style>