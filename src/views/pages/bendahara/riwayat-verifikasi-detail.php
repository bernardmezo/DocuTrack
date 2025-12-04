<?php
// File: src/views/pages/bendahara/riwayat-verifikasi-detail.php
$status_lower = strtolower($status ?? 'Dana Diberikan');

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Riwayat Verifikasi - Detail</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
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
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 w-full md:w-auto">
                <i class="fas fa-arrow-left text-xs"></i> 
                <?= htmlspecialchars($back_text ?? 'Kembali') ?>
            </a>
        </div>

        <!-- Section 1: KAK -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">1. Kerangka Acuan Kegiatan (KAK)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pengusul</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                        <?= htmlspecialchars($kegiatan_data['nama_pengusul'] ?? '-') ?>
                    </p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM Pengusul</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                        <?= htmlspecialchars($kegiatan_data['nim_pengusul'] ?? '-') ?>
                    </p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Penanggung Jawab</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                        <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab'] ?? '-') ?>
                    </p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM/NIP Penanggung Jawab</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                        <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab'] ?? '-') ?>
                    </p>
                </div>
                
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kegiatan</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                        <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
                    </p>
                </div>

                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gambaran Umum</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[100px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')) ?>
                    </div>
                </div>
                
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima Manfaat</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[80px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Strategi Pencapaian Keluaran -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Strategi Pencapaian Keluaran</h3>
            
            <div class="mb-5">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode Pelaksanaan</label>
                <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[100px] leading-relaxed">
                    <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')) ?>
                </div>
            </div>

            <div>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahapan Kegiatan</label>
                <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[120px] leading-relaxed">
                    <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')) ?>
                </div>
            </div>
        </div>

        <!-- Section 2: IKU -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">2. Indikator Kinerja Utama (IKU)</h3>
            <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                <?php if (!empty($iku_data)): ?>
                    <?php foreach ($iku_data as $iku): ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($iku) ?>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-sm text-gray-500 italic">Tidak ada data IKU</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Section 3: Indikator KAK -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">3. Indikator Kinerja KAK</h3>
            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full min-w-[500px]">
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
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
            
            <?php 
            $grand_total_rab = 0;
            if (!empty($rab_data)):
                foreach ($rab_data as $kategori => $items): 
                    if (empty($items)) continue;
                    $subtotal = 0;
            ?>
                <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2"><?= htmlspecialchars($kategori) ?></h4>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
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
                            <?php foreach ($items as $item): 
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
                            <?php endforeach; $grand_total_rab += $subtotal; ?>
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal</td>
                                <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= formatRupiah($subtotal) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <?php endforeach; ?>
                
                <div class="flex justify-end mt-6">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <span class="text-sm font-medium text-gray-700">Grand Total RAB: </span>
                        <span class="text-xl font-bold text-blue-600"><?= formatRupiah($grand_total_rab) ?></span>
                    </div>
                </div>
            <?php else: ?>
                <p class="text-sm text-gray-500 italic p-4 bg-gray-100 rounded-lg">Tidak ada data RAB</p>
            <?php endif; ?>
        </div>

        <!-- Rincian Rancangan Kegiatan -->
        <div class="mb-8">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Rincian Rancangan Kegiatan</h3>
            
            <div class="mb-6">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">Surat Pengantar</label>
                <div class="relative max-w-sm">
                    <?php if (!empty($surat_pengantar_url)): ?>
                    <a href="<?= htmlspecialchars($surat_pengantar_url) ?>" target="_blank"
                       class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200 hover:bg-gray-200 transition-colors">
                        <span class="text-sm text-gray-800 flex items-center gap-2">
                            <i class="fas fa-file-pdf text-red-600"></i>
                            <?= basename($surat_pengantar_url) ?>
                        </span>
                        <i class="fas fa-external-link-alt text-blue-600"></i>
                    </a>
                    <?php else: ?>
                    <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                        <span class="text-sm text-gray-500 italic">Tidak ada file</span>
                        <i class="fas fa-file-alt text-gray-400"></i>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-4">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 block">Kurun Waktu Pelaksanaan</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                    <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                        <div>
                            <span class="text-xs text-gray-500 block">Tanggal Mulai</span>
                            <span class="text-sm text-gray-800 font-medium">
                                <?= !empty($kegiatan_data['tanggal_mulai']) ? date('d M Y', strtotime($kegiatan_data['tanggal_mulai'])) : '-' ?>
                            </span>
                        </div>
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                        <div>
                            <span class="text-xs text-gray-500 block">Tanggal Selesai</span>
                            <span class="text-sm text-gray-800 font-medium">
                                <?= !empty($kegiatan_data['tanggal_selesai']) ? date('d M Y', strtotime($kegiatan_data['tanggal_selesai'])) : '-' ?>
                            </span>
                        </div>
                        <i class="fas fa-calendar-alt text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section: Informasi Pencairan -->
        <?php if ($status_lower === 'dana diberikan' && !empty($tanggal_pencairan)): ?>
        <div class="mb-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>Informasi Pencairan Dana
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah Dicairkan</label>
                    <p class="text-lg font-bold text-green-600 p-3 bg-green-50 rounded-lg border border-green-200 mt-1">
                        <?= formatRupiah($jumlah_dicairkan ?? 0) ?>
                    </p>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pencairan</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                        <?= date('d F Y, H:i', strtotime($tanggal_pencairan)) ?> WIB
                    </p>
                </div>
                
                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode Pencairan</label>
                    <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
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
                
                <?php if (!empty($catatan_bendahara)): ?>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan Bendahara</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[60px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($catatan_bendahara)) ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
            <a href="<?= htmlspecialchars($back_url ?? '/docutrack/public/bendahara/riwayat-verifikasi') ?>" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-left text-xs"></i> 
                <?= htmlspecialchars($back_text ?? 'Kembali ke Riwayat') ?>
            </a>
             
            <a href="/docutrack/public/bendahara/dashboard" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all">
                <i class="fas fa-home text-xs"></i> Ke Dashboard
            </a>
        </div>

    </section>
</main>
