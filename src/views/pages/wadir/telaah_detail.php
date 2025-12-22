<?php
// File: src/views/pages/wadir/telaah_detail.php (HANYA UNTUK Wadir)

$status = $status ?? 'Menunggu';

$is_disetujui = (strtolower($status) === 'disetujui');
$is_menunggu = (strtolower($status) === 'menunggu');

$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/wadir/dashboard';

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

function displayValue($value, $placeholder = 'Belum diisi') {
    return !empty($value) ? htmlspecialchars($value) : '<span class="text-gray-400 italic">' . $placeholder . '</span>';
}
?>

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 sm:p-6 lg:p-10 rounded-xl lg:rounded-2xl shadow-lg overflow-hidden mb-6 sm:mb-8">
        
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-4 sm:mb-6 pb-4 sm:pb-5 border-b border-gray-200 gap-3 sm:gap-4">
            <div class="w-full lg:w-auto">
                <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800">Persetujuan Usulan Kegiatan</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1 flex flex-wrap items-center gap-2">
                    <span>Status:</span>
                    <?php if ($is_disetujui): ?> 
                        <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <i class="fas fa-check-circle"></i> Disetujui
                        </span>
                    <?php else: ?> 
                        <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                            <i class="fas fa-hourglass-half"></i> Menunggu Persetujuan Anda
                        </span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
        
        <form id="form-Wadir-approval" action="#" method="POST">
            
            <!-- 1. KAK -->
            <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg lg:rounded-xl shadow-sm border border-gray-100 mb-4 sm:mb-6">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-4 sm:mb-6 flex items-center gap-2 pb-3 border-b border-gray-100">
                    <span class="hidden sm:inline">1.</span> Kerangka Acuan Kerja (KAK)
                </h3>
                
                <!-- Grid Info Pengusul & Penanggung Jawab -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-4 sm:mb-6">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Pengusul</label>
                        <div class="p-2.5 sm:p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-xs sm:text-sm break-words">
                            <?= htmlspecialchars($kegiatan_data['nama_pengusul'] ?? '-') ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NIM Pengusul</label>
                        <div class="p-2.5 sm:p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-xs sm:text-sm">
                            <?= htmlspecialchars($kegiatan_data['nim_pengusul'] ?? '-') ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Penanggung Jawab</label>
                        <div class="p-2.5 sm:p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-xs sm:text-sm break-words">
                            <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab'] ?? '-') ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NIM/NIP PJ</label>
                        <div class="p-2.5 sm:p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-xs sm:text-sm">
                            <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab'] ?? '-') ?>
                        </div>
                    </div>
                </div>

                <!-- Nama Kegiatan -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Kegiatan</label>
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm sm:text-base break-words">
                        <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
                    </div>
                </div>

                <!-- Gambaran Umum -->
                <div class="mb-4 sm:mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Gambaran Umum</label>
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-xs sm:text-sm leading-relaxed min-h-[80px] sm:min-h-[100px] break-words">
                        <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')) ?>
                    </div>
                </div>

                <!-- Penerima Manfaat -->
                <div class="mb-6 sm:mb-8">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Penerima Manfaat</label>
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-xs sm:text-sm leading-relaxed break-words">
                        <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')) ?>
                    </div>
                </div>

                <!-- Strategi Pencapaian -->
                <h4 class="text-base sm:text-lg font-bold text-gray-800 mb-3 sm:mb-5 pb-2 border-b border-gray-100">Strategi Pencapaian Keluaran</h4>
                
                <div class="mb-4 sm:mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pelaksanaan</label>
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-xs sm:text-sm leading-relaxed min-h-[60px] sm:min-h-[80px] break-words">
                        <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')) ?>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tahapan Kegiatan</label>
                    <div class="p-3 sm:p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-xs sm:text-sm leading-relaxed min-h-[80px] sm:min-h-[100px] break-words">
                        <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')) ?>
                    </div>
                </div>
            </div>

            <!-- 2. IKU -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">2.</span> Indikator Kinerja Utama (IKU)
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-2">Indikator yang Dipilih:</label>
                <div class="flex flex-wrap items-center gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                    <?php if (!empty($iku_data) && is_array($iku_data)): ?>
                        <?php foreach ($iku_data as $iku_item): ?>
                            <span class="inline-flex items-center gap-1.5 px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800 break-all">
                                <i class="fas fa-check-circle text-xs flex-shrink-0"></i>
                                <span class="break-words"><?php echo htmlspecialchars($iku_item); ?></span>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-xs sm:text-sm text-gray-400 italic">Tidak ada IKU yang dipilih</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. Indikator Kinerja -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">3.</span> Indikator Kinerja KAK
                </h3>
                
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full min-w-[500px]">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Bulan</th>
                                <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Indikator Keberhasilan</th>
                                <th class="px-3 lg:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Target (%)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php if (!empty($indikator_data) && is_array($indikator_data)): ?>
                                <?php foreach ($indikator_data as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 lg:px-4 py-3 text-sm text-gray-700">
                                        <?php echo displayValue($item['bulan'] ?? '', '-'); ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-3 text-sm text-gray-700">
                                        <?php echo displayValue($item['nama'] ?? '', '-'); ?>
                                    </td>
                                    <td class="px-3 lg:px-4 py-3 text-sm text-gray-700">
                                        <?php 
                                        $target = $item['target'] ?? 0;
                                        if ($target > 0): 
                                        ?>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <?php echo htmlspecialchars($target); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="px-4 py-8 text-sm text-gray-400 italic text-center">Tidak ada data indikator kinerja</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden space-y-3">
                    <?php if (!empty($indikator_data) && is_array($indikator_data)): ?>
                        <?php foreach ($indikator_data as $item): ?>
                        <div class="p-3 bg-white border border-gray-200 rounded-lg">
                            <div class="flex items-start justify-between mb-2">
                                <div class="flex-1">
                                    <span class="text-xs font-semibold text-gray-500 uppercase">Bulan</span>
                                    <p class="text-sm text-gray-800 font-medium mt-0.5"><?php echo displayValue($item['bulan'] ?? '', '-'); ?></p>
                                </div>
                                <?php 
                                $target = $item['target'] ?? 0;
                                if ($target > 0): 
                                ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <i class="fas fa-bullseye"></i>
                                        <?php echo htmlspecialchars($target); ?>%
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase block mb-1">Indikator</span>
                                <p class="text-sm text-gray-700 leading-snug break-words"><?php echo displayValue($item['nama'] ?? '', '-'); ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="p-6 text-center text-gray-400 italic text-sm bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                            Tidak ada data indikator kinerja
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 4. RAB -->
            <div class="mb-6 sm:mb-8">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">4.</span> Rincian Anggaran Biaya (RAB)
                </h3>
                <?php 
                    $grand_total_rab = 0;
                    if (!empty($rab_data) && is_array($rab_data)):
                        foreach ($rab_data as $kategori => $items): 
                            if (empty($items) || !is_array($items)) continue;
                            $subtotal = 0;
                ?>
                    <h4 class="text-sm sm:text-base font-semibold text-gray-700 mt-4 mb-2 sm:mb-3 flex items-center gap-2">
                        <i class="fas fa-folder-open text-blue-600"></i>
                        <span class="break-words"><?php echo htmlspecialchars($kategori); ?></span>
                    </h4>
                    
                    <!-- Desktop Table -->
                    <div class="hidden lg:block overflow-x-auto border border-gray-200 rounded-lg mb-4">
                        <table class="w-full min-w-[900px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 xl:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Uraian</th>
                                    <th class="px-3 xl:px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Rincian</th>
                                    <th class="px-2 xl:px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Vol 1</th>
                                    <th class="px-2 xl:px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Sat 1</th>
                                    <th class="px-2 xl:px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Vol 2</th>
                                    <th class="px-2 xl:px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase">Sat 2</th>
                                    <th class="px-3 xl:px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Harga (Rp)</th>
                                    <th class="px-3 xl:px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <?php foreach ($items as $item): 
                                    $vol1 = $item['vol1'] ?? 0;
                                    $sat1 = $item['sat1'] ?? '-';
                                    $vol2 = $item['vol2'] ?? 1;
                                    $sat2 = $item['sat2'] ?? '-';
                                    $harga = $item['harga'] ?? 0;
                                    $total_item = $vol1 * $vol2 * $harga;
                                    $subtotal += $total_item;
                                ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 xl:px-4 py-3 text-sm text-gray-700">
                                        <?php echo !empty($item['uraian']) ? htmlspecialchars($item['uraian']) : '<span class="text-gray-400 italic">-</span>'; ?>
                                    </td>
                                    <td class="px-3 xl:px-4 py-3 text-sm text-gray-700">
                                        <?php echo !empty($item['rincian']) ? htmlspecialchars($item['rincian']) : '<span class="text-gray-400 italic">-</span>'; ?>
                                    </td>
                                    <td class="px-2 xl:px-3 py-3 text-sm text-gray-700 text-center font-medium">
                                        <?php echo $vol1 > 0 ? $vol1 : '<span class="text-gray-400">-</span>'; ?>
                                    </td>
                                    <td class="px-2 xl:px-3 py-3 text-sm text-gray-600 text-center">
                                        <?php echo htmlspecialchars($sat1); ?>
                                    </td>
                                    <td class="px-2 xl:px-3 py-3 text-sm text-gray-700 text-center font-medium">
                                        <?php echo $vol2 > 0 ? $vol2 : '<span class="text-gray-400">-</span>'; ?>
                                    </td>
                                    <td class="px-2 xl:px-3 py-3 text-sm text-gray-600 text-center">
                                        <?php echo htmlspecialchars($sat2); ?>
                                    </td>
                                    <td class="px-3 xl:px-4 py-3 text-sm text-gray-700 text-right">
                                        <?php echo $harga > 0 ? number_format($harga, 0, ',', '.') : '<span class="text-gray-400">-</span>'; ?>
                                    </td>
                                    <td class="px-3 xl:px-4 py-3 text-sm text-blue-600 font-semibold text-right">
                                        <?php echo $total_item > 0 ? formatRupiah($total_item) : '<span class="text-gray-400">Rp 0</span>'; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $grand_total_rab += $subtotal; ?>
                                <tr class="bg-blue-50 font-semibold">
                                    <td colspan="7" class="px-3 xl:px-4 py-3 text-right text-sm text-gray-800">Subtotal <?php echo htmlspecialchars($kategori); ?></td>
                                    <td class="px-3 xl:px-4 py-3 text-sm text-blue-700 text-right"><?php echo formatRupiah($subtotal); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card View for RAB -->
                    <div class="lg:hidden space-y-3 mb-4">
                        <?php foreach ($items as $idx => $item): 
                            $vol1 = $item['vol1'] ?? 0;
                            $sat1 = $item['sat1'] ?? '-';
                            $vol2 = $item['vol2'] ?? 1;
                            $sat2 = $item['sat2'] ?? '-';
                            $harga = $item['harga'] ?? 0;
                            $total_item = $vol1 * $vol2 * $harga;
                        ?>
                        <div class="p-3 bg-white border border-gray-200 rounded-lg">
                            <div class="mb-2 pb-2 border-b border-gray-100">
                                <span class="text-xs font-bold text-gray-500">Item #<?php echo $idx + 1; ?></span>
                            </div>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <span class="text-gray-500 font-semibold block mb-0.5">Uraian</span>
                                    <span class="text-gray-800 break-words"><?php echo !empty($item['uraian']) ? htmlspecialchars($item['uraian']) : '-'; ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500 font-semibold block mb-0.5">Rincian</span>
                                    <span class="text-gray-800 break-words"><?php echo !empty($item['rincian']) ? htmlspecialchars($item['rincian']) : '-'; ?></span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <span class="text-gray-500 font-semibold block mb-0.5">Volume 1</span>
                                        <span class="text-gray-800"><?php echo $vol1 > 0 ? $vol1 . ' ' . htmlspecialchars($sat1) : '-'; ?></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 font-semibold block mb-0.5">Volume 2</span>
                                        <span class="text-gray-800"><?php echo $vol2 > 0 ? $vol2 . ' ' . htmlspecialchars($sat2) : '-'; ?></span>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-gray-100">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 font-semibold">Harga Satuan</span>
                                        <span class="text-gray-800 font-medium">Rp <?php echo number_format($harga, 0, ',', '.'); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center mt-1">
                                        <span class="text-gray-700 font-bold">Total</span>
                                        <span class="text-blue-600 font-bold"><?php echo formatRupiah($total_item); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Subtotal Mobile -->
                        <div class="p-3 bg-blue-50 border-2 border-blue-200 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-800">Subtotal <?php echo htmlspecialchars($kategori); ?></span>
                                <span class="text-sm font-bold text-blue-700"><?php echo formatRupiah($subtotal); ?></span>
                            </div>
                        </div>
                    </div>
                <?php 
                        endforeach; 
                    else:
                ?>
                    <div class="p-6 sm:p-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300 text-center">
                        <i class="fas fa-inbox text-3xl sm:text-4xl text-gray-300 mb-3"></i>
                        <p class="text-xs sm:text-sm text-gray-400 italic">Tidak ada data RAB yang tersedia</p>
                    </div>
                <?php endif; ?>
                
                <!-- Grand Total -->
                <div class="flex justify-end mt-4 sm:mt-6">
                    <div class="w-full sm:w-auto p-4 sm:p-5 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg sm:rounded-xl border-2 border-blue-200 shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-calculator text-xl sm:text-2xl text-blue-600 flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs font-semibold text-gray-600 uppercase tracking-wider block">Grand Total RAB</span>
                                <span class="text-lg sm:text-2xl font-bold text-blue-600 break-all"><?php echo formatRupiah($grand_total_rab); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Rincian Rancangan Kegiatan -->
            <div class="mb-6 sm:mb-8 pt-4 sm:pt-6 border-t border-gray-200">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">5.</span> Rincian Rancangan Kegiatan
                </h3>
                
                <!-- Surat Pengantar -->
                <div class="mb-4 sm:mb-6">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Surat Pengantar</label>
                    <div class="relative">
                        <?php if (!empty($surat_pengantar)): ?>
                            <div class="flex items-center justify-between gap-2 sm:gap-3 px-3 sm:px-4 py-2.5 sm:py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                                <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-1">
                                    <i class="fas fa-file-pdf text-red-500 text-base sm:text-xl flex-shrink-0"></i>
                                    <span class="text-xs sm:text-sm text-gray-800 font-medium truncate" title="<?php echo htmlspecialchars($surat_pengantar); ?>">
                                        <?php echo htmlspecialchars($surat_pengantar); ?>
                                    </span>
                                </div>
                                <a href="<?php echo htmlspecialchars($surat_pengantar_url); ?>" target="_blank" 
                                   class="text-blue-600 hover:text-blue-700 transition-colors flex-shrink-0 p-1">
                                    <i class="fas fa-download text-sm sm:text-base"></i>
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3.5 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <i class="fas fa-file-pdf text-gray-300 text-base sm:text-xl"></i>
                                    <span class="text-xs sm:text-sm text-gray-400 italic">Belum ada file yang diunggah</span>
                                </div>
                                <i class="fas fa-times-circle text-gray-300"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kurun Waktu Pelaksanaan -->
                <div class="mb-4">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Kurun Waktu Pelaksanaan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs text-gray-500 block mb-1">Tanggal Mulai</span>
                                <span class="text-xs sm:text-sm text-gray-800 font-semibold break-words">
                                    <?php 
                                    if (isValidDate($tanggal_mulai)) {
                                        echo formatTanggal($tanggal_mulai);
                                    } else {
                                        echo '<span class="text-gray-400 italic font-normal">Belum ditentukan</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt <?php echo isValidDate($tanggal_mulai) ? 'text-blue-500' : 'text-gray-300'; ?> text-base sm:text-lg flex-shrink-0 ml-2"></i>
                        </div>
                        <div class="flex items-center justify-between px-3 sm:px-4 py-2.5 sm:py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="min-w-0 flex-1">
                                <span class="text-xs text-gray-500 block mb-1">Tanggal Selesai</span>
                                <span class="text-xs sm:text-sm text-gray-800 font-semibold break-words">
                                    <?php 
                                    if (isValidDate($tanggal_selesai)) {
                                        echo formatTanggal($tanggal_selesai);
                                    } else {
                                        echo '<span class="text-gray-400 italic font-normal">Belum ditentukan</span>';
                                    }
                                    ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-check <?php echo isValidDate($tanggal_selesai) ? 'text-green-500' : 'text-gray-300'; ?> text-base sm:text-lg flex-shrink-0 ml-2"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- 6. Kode MAK -->
            <div class="mb-6 sm:mb-8 pt-4 sm:pt-6 border-t border-gray-200">
                <h3 class="text-lg sm:text-xl font-bold text-gray-700 pb-3 mb-3 sm:mb-4 border-b border-gray-200">
                    <span class="hidden sm:inline">6.</span> Kode Mata Anggaran Kegiatan (MAK)
                </h3>
                <div class="relative">
                    <?php if (!empty($kode_mak)): ?>
                        <div class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2.5 sm:py-3.5 bg-green-50 rounded-lg border-2 border-green-200">
                            <i class="fas fa-key text-green-600 text-base sm:text-lg flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-green-600 font-semibold block mb-1">Kode MAK</span>
                                <span class="text-xs sm:text-sm text-gray-800 font-mono font-bold break-all">
                                    <?php echo htmlspecialchars($kode_mak); ?>
                                </span>
                            </div>
                            <i class="fas fa-check-circle text-green-600 text-lg sm:text-xl flex-shrink-0"></i>
                        </div>
                    <?php else: ?>
                        <div class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-2.5 sm:py-3.5 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <i class="fas fa-key text-gray-300 text-base sm:text-lg flex-shrink-0"></i>
                            <div class="flex-1 min-w-0">
                                <span class="text-xs text-gray-500 block mb-1">Kode MAK</span>
                                <span class="text-xs sm:text-sm text-gray-400 italic">
                                    Belum tersedia
                                </span>
                            </div>
                            <i class="fas fa-minus-circle text-gray-300 flex-shrink-0"></i>
                        </div>
                        <p class="text-xs text-gray-500 mt-2 flex items-start gap-1.5">
                            <i class="fas fa-info-circle mt-0.5 flex-shrink-0"></i>
                            <span class="italic">Kode MAK akan diberikan setelah usulan disetujui</span>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mt-6 sm:mt-10 pt-4 sm:pt-6 border-t border-gray-200 gap-3 sm:gap-4">
                
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-4 sm:px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 text-sm sm:text-base order-2 sm:order-1">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex flex-col sm:flex-row-reverse gap-3 sm:gap-4 w-full sm:w-auto order-1 sm:order-2">
                
                <?php if ($is_menunggu): ?>
                    <!-- <button type="button" id="btn-tolak-wadir"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg shadow-md hover:bg-red-700 transition-all duration-300">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                    <button type="button" id="btn-revisi-wadir"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300">
                        <i class="fas fa-pencil-alt"></i> Revisi
                    </button> -->
                    <button type="button" id="btn-setujui-Wadir" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 sm:px-6 py-2.5 sm:py-3 rounded-lg shadow-md hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg text-sm sm:text-base">
                         <i class="fas fa-check-double"></i> Setujui Usulan
                    </button>
                
                <?php elseif ($is_disetujui): ?>
                    <div class="flex items-center gap-2 sm:gap-3 px-4 sm:px-5 py-2.5 sm:py-3 rounded-lg bg-green-50 border-2 border-green-200 w-full sm:w-auto justify-center sm:justify-start">
                        <i class="fas fa-check-circle text-green-600 text-lg sm:text-xl flex-shrink-0"></i>
                        <div>
                            <span class="text-xs text-green-600 font-semibold uppercase block">Status</span>
                            <span class="text-xs sm:text-sm font-bold text-green-700">Telah Disetujui</span>
                        </div>
                    </div>
                
                <?php endif; ?>
                 </div>
            </div>
        </form>
        
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        const namaKegiatan = <?php echo json_encode($kegiatan_data['nama_kegiatan'] ?? 'Kegiatan Ini'); ?>;
        const formWadir = document.getElementById('form-Wadir-approval');

        const kegiatanId = "<?php echo $id ?? ''; ?>"; 
        
        const btnSetujuiWadir = document.getElementById('btn-setujui-Wadir');
        const btnTolakWadir = document.getElementById('btn-tolak-wadir');
        const btnRevisiWadir = document.getElementById('btn-revisi-wadir');

        btnSetujuiWadir?.addEventListener('click', (e) => {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Setujui Usulan Ini?',
                    html: `Usulan untuk <strong>${namaKegiatan}</strong> akan disetujui dan dapat dilanjutkan ke tahap berikutnya.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16A34A',
                    cancelButtonColor: '#6B7280',
                    confirmButtonText: '<i class="fas fa-check mr-2"></i> Ya, Setujui!',
                    cancelButtonText: '<i class="fas fa-times mr-2"></i> Batal',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'rounded-lg px-5 py-2.5',
                        cancelButton: 'rounded-lg px-5 py-2.5'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({ 
                            title: 'Menyetujui Usulan...', 
                            html: 'Mohon tunggu sebentar',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => Swal.showLoading() 
                        });
                        
                        setTimeout(() => {
                            formWadir.action = `/docutrack/public/wadir/telaah/approve/${kegiatanId}`;
                            formWadir.submit();
                        }, 1500);
                    }
                });
            } else {
                if (confirm(`Apakah Anda yakin ingin menyetujui usulan "${namaKegiatan}"?`)) {
                    formWadir.action = `/docutrack/public/wadir/telaah/approve/${kegiatanId}`;
                    formWadir.submit();
                }
            }
        });

        btnTolakWadir?.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Tolak Usulan?',
                input: 'textarea',
                inputPlaceholder: 'Masukkan alasan penolakan...',
                inputAttributes: {
                    'aria-label': 'Masukkan alasan penolakan'
                },
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                confirmButtonColor: '#DC2626',
                cancelButtonText: 'Batal',
                preConfirm: (text) => {
                    if (!text) {
                        Swal.showValidationMessage('Alasan penolakan tidak boleh kosong');
                    }
                    return text;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'alasan_penolakan';
                    input.value = result.value;
                    formWadir.appendChild(input);
                    formWadir.action = `/docutrack/public/wadir/telaah/reject/${kegiatanId}`;
                    formWadir.submit();
                }
            });
        });

        btnRevisiWadir?.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Kirim untuk Revisi?',
                input: 'textarea',
                inputPlaceholder: 'Masukkan komentar atau catatan untuk revisi...',
                inputAttributes: {
                    'aria-label': 'Masukkan komentar atau catatan untuk revisi'
                },
                showCancelButton: true,
                confirmButtonText: 'Kirim Revisi',
                confirmButtonColor: '#F59E0B',
                cancelButtonText: 'Batal',
                preConfirm: (text) => {
                    if (!text) {
                        Swal.showValidationMessage('Komentar revisi tidak boleh kosong');
                    }
                    return text;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'komentar_revisi';
                    input.value = result.value;
                    formWadir.appendChild(input);
                    formWadir.action = `/docutrack/public/wadir/telaah/revise/${kegiatanId}`;
                    formWadir.submit();
                }
            });
        });
        
    });
</script>