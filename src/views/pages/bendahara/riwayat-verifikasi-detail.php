<?php
// File: src/views/pages/bendahara/riwayat-verifikasi-detail.php
$status_lower = strtolower($status ?? 'Dana Diberikan');

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-6 md:mb-8">
        
        <!-- Header -->
        <div class="flex flex-col justify-start mb-4 md:mb-6 pb-4 md:pb-5 border-b border-gray-200 gap-2 md:gap-4">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h2 class="text-xl md:text-3xl font-bold text-gray-800">Riwayat Verifikasi - Detail</h2>
                    <p class="text-xs md:text-sm text-gray-500 mt-1">Status:
                        <?php if ($status_lower === 'dana diberikan'): ?>
                            <span class="font-semibold text-green-600">Dana Diberikan</span>
                        <?php elseif ($status_lower === 'revisi' || $status_lower === 'ditolak'): ?>
                            <span class="font-semibold text-yellow-600"><?= htmlspecialchars($status) ?></span>
                        <?php else: ?>
                            <span class="font-semibold text-gray-600"><?= htmlspecialchars($status) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="<?= htmlspecialchars($back_url ?? '/docutrack/public/bendahara/riwayat-verifikasi') ?>" 
                   class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all text-sm">
                    <i class="fas fa-arrow-left text-xs"></i> 
                    <span><?= htmlspecialchars($back_text ?? 'Kembali') ?></span>
                </a>
            </div>
        </div>

        <!-- Section 1: KAK -->
        <div class="mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">1. Kerangka Acuan Kegiatan (KAK)</h3>
            <div class="space-y-4">
                
                <!-- Data Pengusul -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama Pengusul</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?= htmlspecialchars($kegiatan_data['nama_pengusul'] ?? '-') ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">NIM Pengusul</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?= htmlspecialchars($kegiatan_data['nim_pengusul'] ?? '-') ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama Penanggung Jawab</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab'] ?? '-') ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">NIM/NIP Penanggung Jawab</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab'] ?? '-') ?>
                        </p>
                    </div>
                </div>
                
                <!-- Nama Kegiatan -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama Kegiatan</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                        <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
                    </p>
                </div>

                <!-- Gambaran Umum -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Gambaran Umum</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[80px] md:min-h-[100px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')) ?>
                    </div>
                </div>
                
                <!-- Penerima Manfaat -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Penerima Manfaat</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[60px] md:min-h-[80px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Strategi Pencapaian Keluaran -->
        <div class="mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">Strategi Pencapaian Keluaran</h3>
            
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Metode Pelaksanaan</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[80px] md:min-h-[100px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')) ?>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tahapan Kegiatan</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[100px] md:min-h-[120px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: IKU -->
        <div class="mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">2. Indikator Kinerja Utama (IKU)</h3>
            <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                <?php if (!empty($iku_data)): ?>
                    <?php foreach ($iku_data as $iku): ?>
                        <span class="px-2.5 md:px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($iku) ?>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-sm text-gray-500 italic">Tidak ada data IKU</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 3: Indikator KAK -->
        <div class="mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">3. Indikator Kinerja KAK</h3>
            
            <!-- Mobile: Card View -->
            <div class="block md:hidden space-y-3">
                <?php if (!empty($indikator_data)): ?>
                    <?php foreach ($indikator_data as $indikator): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-gray-500 uppercase"><?= strtoupper(htmlspecialchars($indikator['bulan'] ?? '-')) ?></span>
                            <span class="text-sm font-semibold text-blue-600"><?= htmlspecialchars($indikator['target'] ?? 0) ?>%</span>
                        </div>
                        <p class="text-sm text-gray-700"><?= htmlspecialchars($indikator['nama'] ?? '-') ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-sm text-gray-500 italic p-4 bg-gray-100 rounded-lg text-center">Tidak ada data indikator</p>
                <?php endif; ?>
            </div>

            <!-- Desktop: Table View -->
            <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Bulan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Indikator Keberhasilan</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Target</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php if (!empty($indikator_data)): ?>
                            <?php foreach ($indikator_data as $indikator): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700"><?= strtoupper(htmlspecialchars($indikator['bulan'] ?? '-')) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($indikator['nama'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($indikator['target'] ?? 0) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-sm text-gray-500 italic">Tidak ada data indikator</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 4: RAB -->
        <div class="mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
            
            <?php 
            $grand_total_rab = 0;
            if (!empty($rab_data)):
                foreach ($rab_data as $kategori => $items): 
                    if (empty($items)) continue;
                    $subtotal = 0;
            ?>
                <h4 class="text-sm md:text-md font-semibold text-gray-700 mt-4 mb-2"><?= htmlspecialchars($kategori) ?></h4>
                
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
                    <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                        <!-- Header dengan nomor -->
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
                <div class="hidden md:block overflow-x-auto border border-gray-200 rounded-lg mb-4">
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
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <?php 
                            // Reset for desktop
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
                                <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['uraian'] ?? '') ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['rincian'] ?? '') ?></td>
                                <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol1 ?></td>
                                <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat1) ?></td>
                                <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol2 ?></td>
                                <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat2) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700 text-right"><?= number_format($harga, 0, ',', '.') ?></td>
                                <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right"><?= formatRupiah($total_item) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= formatRupiah($subtotal) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php 
                    $grand_total_rab += $subtotal;
                endforeach; ?>
                
                <!-- Grand Total -->
                <div class="flex justify-end mt-4 md:mt-6">
                    <div class="p-3 md:p-4 bg-blue-50 rounded-lg border border-blue-100 w-full md:w-auto">
                        <div class="flex justify-between md:block items-center">
                            <span class="text-xs md:text-sm font-medium text-gray-700">Grand Total RAB:</span>
                            <span class="text-lg md:text-xl font-bold text-blue-600"><?= formatRupiah($grand_total_rab) ?></span>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500 italic p-4 bg-gray-100 rounded-lg">Tidak ada data RAB</p>
            <?php endif; ?>
        </div>

        <!-- Rincian Rancangan Kegiatan -->
        <div class="mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">Rincian Rancangan Kegiatan</h3>
            
            <div class="space-y-4">
                <!-- Surat Pengantar -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Surat Pengantar</label>
                    <?php if (!empty($surat_pengantar_url)): ?>
                    <a href="<?= htmlspecialchars($surat_pengantar_url) ?>" target="_blank"
                       class="flex items-center justify-between px-3 md:px-4 py-3 md:py-3.5 bg-gray-100 rounded-lg border border-gray-200 hover:bg-gray-200 transition-colors">
                        <span class="text-xs md:text-sm text-gray-800 flex items-center gap-2 truncate">
                            <i class="fas fa-file-pdf text-red-600 flex-shrink-0"></i>
                            <span class="truncate"><?= basename($surat_pengantar_url) ?></span>
                        </span>
                        <i class="fas fa-external-link-alt text-blue-600 text-xs md:text-sm flex-shrink-0 ml-2"></i>
                    </a>
                    <?php else: ?>
                    <div class="flex items-center justify-between px-3 md:px-4 py-3 md:py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                        <span class="text-xs md:text-sm text-gray-500 italic">Tidak ada file</span>
                        <i class="fas fa-file-alt text-gray-400"></i>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Kurun Waktu -->
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Kurun Waktu Pelaksanaan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="flex items-center justify-between px-3 md:px-4 py-3 md:py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-[10px] md:text-xs text-gray-500 block">Tanggal Mulai</span>
                                <span class="text-xs md:text-sm text-gray-800 font-medium">
                                    <?= !empty($kegiatan_data['tanggal_mulai']) ? date('d M Y', strtotime($kegiatan_data['tanggal_mulai'])) : '-' ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                        </div>
                        <div class="flex items-center justify-between px-3 md:px-4 py-3 md:py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-[10px] md:text-xs text-gray-500 block">Tanggal Selesai</span>
                                <span class="text-xs md:text-sm text-gray-800 font-medium">
                                    <?= !empty($kegiatan_data['tanggal_selesai']) ? date('d M Y', strtotime($kegiatan_data['tanggal_selesai'])) : '-' ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400 text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Informasi Pencairan -->
        <?php if ($status_lower === 'dana diberikan' && !empty($tanggal_pencairan)): ?>
        <div class="mb-6 md:mb-8 pt-4 md:pt-6 border-t border-gray-200">
            <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>Informasi Pencairan Dana
            </h3>
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Jumlah Dicairkan</label>
                        <p class="text-base md:text-lg font-bold text-green-600 p-3 bg-green-50 rounded-lg border border-green-200">
                            <?= formatRupiah($jumlah_dicairkan ?? 0) ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tanggal Pencairan</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?= date('d F Y, H:i', strtotime($tanggal_pencairan)) ?> WIB
                        </p>
                    </div>
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Metode Pencairan</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?php 
                            $metode_label = match($metode_pencairan ?? '') {
                                'uang_muka' => 'Uang Muka',
                                'dana_penuh' => 'Dana Penuh',
                                'bertahap' => 'Bertahap',
                                default => ucfirst(str_replace('_', ' ', $metode_pencairan ?? '-'))
                            };
                            echo htmlspecialchars($metode_label);
                            ?>
                        </p>
                    </div>
                </div>
                
                <?php if (!empty($catatan_bendahara)): ?>
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Catatan Bendahara</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[60px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($catatan_bendahara)) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer Actions -->
        <div class="flex flex-col-reverse sm:flex-row justify-between items-stretch sm:items-center mt-6 md:mt-10 pt-4 md:pt-6 border-t border-gray-200 gap-3">
            <a href="<?= htmlspecialchars($back_url ?? '/docutrack/public/bendahara/riwayat-verifikasi') ?>" 
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all text-sm">
                <i class="fas fa-arrow-left text-xs"></i> 
                <span><?= htmlspecialchars($back_text ?? 'Kembali ke Riwayat') ?></span>
            </a>
             
            <a href="/docutrack/public/bendahara/dashboard" 
               class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all text-sm">
                <i class="fas fa-home text-xs"></i> 
                <span>Ke Dashboard</span>
            </a>
        </div>

    </section>
</main>