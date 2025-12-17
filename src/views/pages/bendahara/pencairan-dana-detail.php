<?php
// File: src/views/pages/bendahara/pencairan-dana-detail.php
$status_lower = strtolower($status);

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-4 md:mb-6 p-3 md:p-4 rounded-lg <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-yellow-50 border border-yellow-200 text-yellow-800' ?>">
        <div class="flex items-start gap-2">
            <i class="fas fa-<?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'check-circle' : 'exclamation-triangle' ?> mt-0.5"></i>
            <span class="text-sm md:text-base font-medium"><?= htmlspecialchars($_SESSION['flash_message']) ?></span>
        </div>
    </div>
    <?php 
        unset($_SESSION['flash_message'], $_SESSION['flash_type']); 
    endif; 
    ?>

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-6 md:mb-8">
        
        <!-- Header -->
        <div class="flex flex-col justify-start mb-4 md:mb-6 pb-4 md:pb-5 border-b border-gray-200 gap-2">
            <h2 class="text-xl md:text-3xl font-bold text-gray-800">Detail Usulan Kegiatan</h2>
            <p class="text-xs md:text-sm text-gray-500">Status:
                <?php if ($status_lower === 'dana diberikan'): ?>
                    <span class="font-semibold text-green-600">Dana Diberikan</span>
                <?php elseif ($status_lower === 'revisi'): ?>
                    <span class="font-semibold text-yellow-600">Revisi</span>
                <?php else: ?>
                    <span class="font-semibold text-gray-600"><?= htmlspecialchars($status) ?></span>
                <?php endif; ?>
            </p>
        </div>

        <?php if ($status_lower === 'menunggu'): ?>
        <form method="POST" action="/docutrack/public/bendahara/pencairan-dana/proses" id="formPencairan">
            <input type="hidden" name="kak_id" value="<?= $kegiatan_data['id'] ?? '' ?>">
            <input type="hidden" name="total_anggaran" id="total_anggaran" value="<?= $anggaran_disetujui ?? 0 ?>">
        <?php endif; ?>

            <!-- 1. KAK Section -->
            <div class="mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">1. Kerangka Acuan Kegiatan (KAK)</h3>
                <div class="space-y-4">
                    
                    <!-- Data Pengusul -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama Pengusul</label>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                                <?= htmlspecialchars($kegiatan_data['nama_pengusul']) ?>
                            </p>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">NIM Pengusul</label>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                                <?= htmlspecialchars($kegiatan_data['nim_pengusul']) ?>
                            </p>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama Penanggung Jawab</label>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                                <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab']) ?>
                            </p>
                        </div>

                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">NIM/NIP Penanggung Jawab</label>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                                <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab']) ?>
                            </p>
                        </div>
                    </div>
                    
                    <!-- Nama Kegiatan -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Nama Kegiatan</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200">
                            <?= htmlspecialchars($kegiatan_data['nama_kegiatan']) ?>
                        </p>
                    </div>

                    <!-- Gambaran Umum -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Gambaran Umum</label>
                        <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[80px] md:min-h-[100px] leading-relaxed">
                            <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'])) ?>
                        </div>
                    </div>
                    
                    <!-- Penerima Manfaat -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Penerima Manfaat</label>
                        <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[60px] md:min-h-[80px] leading-relaxed">
                            <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Strategi Pencapaian -->
            <div class="mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">Strategi Pencapaian Keluaran</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Metode Pelaksanaan</label>
                        <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[80px] md:min-h-[100px] leading-relaxed">
                            <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'])) ?>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Tahapan Kegiatan</label>
                        <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 min-h-[100px] md:min-h-[120px] leading-relaxed">
                            <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. IKU -->
            <div class="mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">2. Indikator Kinerja Utama (IKU)</h3>
                <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                    <?php foreach ($iku_data as $iku): ?>
                        <span class="px-2.5 md:px-3 py-1 rounded-full text-xs md:text-sm font-medium bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($iku) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- 3. Indikator Kinerja KAK -->
            <div class="mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">3. Indikator Kinerja KAK</h3>
                
                <!-- Mobile: Card View -->
                <div class="block md:hidden space-y-3">
                    <?php foreach ($indikator_data as $indikator): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <span class="text-xs font-bold text-gray-500 uppercase"><?= strtoupper(htmlspecialchars($indikator['bulan'])) ?></span>
                            <span class="text-sm font-semibold text-blue-600"><?= htmlspecialchars($indikator['target']) ?>%</span>
                        </div>
                        <p class="text-sm text-gray-700"><?= htmlspecialchars($indikator['nama']) ?></p>
                    </div>
                    <?php endforeach; ?>
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
                            <?php foreach ($indikator_data as $indikator): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-700"><?= strtoupper(htmlspecialchars($indikator['bulan'])) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($indikator['nama']) ?></td>
                                <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($indikator['target']) ?>%</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. RAB -->
            <div class="mb-6 md:mb-8">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                
                <?php 
                $grand_total_rab = 0;
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
                endforeach; 
                ?>
                
                <!-- Grand Total -->
                <div class="flex justify-end mt-4 md:mt-6">
                    <div class="p-3 md:p-4 bg-blue-50 rounded-lg border border-blue-100 w-full md:w-auto">
                        <div class="flex justify-between md:block items-center">
                            <span class="text-xs md:text-sm font-medium text-gray-700">Grand Total RAB:</span>
                            <span class="text-lg md:text-xl font-bold text-blue-600"><?= formatRupiah($grand_total_rab) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proses Pencairan -->
            <?php if ($status_lower === 'menunggu' || $status_lower === 'dana diberikan'): ?>
            <div class="mb-6 md:mb-8 pt-4 md:pt-6 border-t border-gray-200">
                <h3 class="text-lg md:text-xl font-bold text-gray-700 pb-2 md:pb-3 mb-3 md:mb-4 border-b border-gray-200">Proses Pencairan Dana</h3>
                
                <div class="space-y-4 md:space-y-5">
                    
                    <!-- Pilihan Metode -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">
                            Metode Pencairan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col md:flex-row gap-3 md:gap-4">
                            <label class="flex-1 flex items-start md:items-center p-3 md:p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" name="metode_pencairan" value="penuh" class="peer mt-1 md:mt-0 mr-3 flex-shrink-0" checked onchange="toggleMetode('penuh')">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700 block">Pencairan Penuh</span>
                                    <span class="text-xs text-gray-500">Cairkan 100% dana sekaligus</span>
                                </div>
                            </label>
                            <label class="flex-1 flex items-start md:items-center p-3 md:p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" name="metode_pencairan" value="bertahap" class="peer mt-1 md:mt-0 mr-3 flex-shrink-0" onchange="toggleMetode('bertahap')">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700 block">Pencairan Bertahap</span>
                                    <span class="text-xs text-gray-500">Cairkan dana dalam beberapa termin</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Container Input Penuh -->
                    <div id="container-penuh" class="space-y-4 p-4 md:p-5 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">
                                Nominal Pencairan (Rp) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="jumlah_dicairkan" name="jumlah_dicairkan" 
                                   value="<?= number_format($grand_total_rab, 0, '', '') ?>"
                                   class="block w-full px-3 md:px-4 py-2.5 md:py-3 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white"
                                   oninput="formatRupiahInput(this)">
                        </div>
                    </div>

                    <!-- Container Input Bertahap -->
                    <div id="container-bertahap" class="hidden space-y-4 p-4 md:p-5 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rincian Tahapan</label>
                            <button type="button" onclick="addStage()" class="text-xs bg-blue-100 text-blue-700 px-2.5 md:px-3 py-1.5 rounded-md hover:bg-blue-200 transition-colors flex items-center gap-1">
                                <i class="fas fa-plus text-[10px]"></i>
                                <span>Tambah Tahap</span>
                            </button>
                        </div>
                        
                        <input type="hidden" name="jumlah_tahap" id="jumlah_tahap" value="0">
                        <div id="stages-wrapper" class="space-y-3">
                            <!-- Dynamic inputs will appear here -->
                        </div>
                        <div class="text-right text-xs text-gray-500">
                            Total Persentase: <span id="total-persen" class="font-bold text-gray-800">0%</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block mb-1">Catatan Bendahara (Opsional)</label>
                        <textarea name="catatan" rows="3" class="block w-full px-3 md:px-4 py-2.5 md:py-3 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                    </div>

                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row justify-between items-stretch sm:items-center mt-6 md:mt-10 pt-4 md:pt-6 border-t border-gray-200 gap-3">
                <a href="<?= htmlspecialchars($back_url) ?>" class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all text-sm">
                    <i class="fas fa-arrow-left text-xs"></i> 
                    <span>Kembali</span>
                </a>
                 
                <?php if ($status_lower === 'menunggu'): ?>
                <button type="button" onclick="submitForm()" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-4 md:px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all text-sm">
                    <i class="fas fa-check-circle text-xs"></i> 
                    <span>Proses Pencairan</span>
                </button>
                <?php endif; ?>
            </div>

        <?php if ($status_lower === 'menunggu'): ?>
        </form>
        <?php endif; ?>

    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Init state
let stageCount = 0;

document.addEventListener('DOMContentLoaded', function() {
    formatRupiahInput(document.getElementById('jumlah_dicairkan'));
    // Initialize 2 stages by default if in bertahap mode
    addStage();
    addStage();
});

function toggleMetode(metode) {
    const containerPenuh = document.getElementById('container-penuh');
    const containerBertahap = document.getElementById('container-bertahap');
    
    if (metode === 'penuh') {
        containerPenuh.classList.remove('hidden');
        containerBertahap.classList.add('hidden');
    } else {
        containerPenuh.classList.add('hidden');
        containerBertahap.classList.remove('hidden');
    }
}

function addStage() {
    if(stageCount >= 5) {
        Swal.fire({
            icon: 'warning',
            title: 'Maksimal 5 Tahap',
            text: 'Anda hanya dapat menambahkan maksimal 5 tahapan pencairan',
            confirmButtonColor: '#2563eb'
        });
        return;
    }
    stageCount++;
    document.getElementById('jumlah_tahap').value = stageCount;
    
    const wrapper = document.getElementById('stages-wrapper');
    const div = document.createElement('div');
    div.className = 'animate-fade-in-up';
    div.innerHTML = `
        <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold flex-shrink-0">
                    #${stageCount}
                </div>
                <h5 class="text-sm font-semibold text-gray-700">Tahap ${stageCount}</h5>
            </div>
            <div class="space-y-3">
                <div>
                    <label class="text-[10px] uppercase text-gray-500 font-semibold block mb-1">Tanggal Pencairan</label>
                    <input type="date" name="tanggal_tahap_${stageCount}" 
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" 
                           required>
                </div>
                <div>
                    <label class="text-[10px] uppercase text-gray-500 font-semibold block mb-1">Persentase Dana</label>
                    <div class="relative">
                        <input type="number" name="persentase_tahap_${stageCount}" 
                               class="w-full pl-3 pr-8 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-right" 
                               placeholder="0" min="1" max="100" oninput="updateTotalPersen()">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 text-xs pointer-events-none">%</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    wrapper.appendChild(div);
}

function updateTotalPersen() {
    let total = 0;
    for(let i=1; i<=stageCount; i++) {
        const input = document.querySelector(`input[name="persentase_tahap_${i}"]`);
        if (input) {
            const val = parseFloat(input.value) || 0;
            total += val;
        }
    }
    const display = document.getElementById('total-persen');
    display.innerText = total + '%';
    
    if (Math.abs(total - 100) < 0.1) {
        display.className = 'font-bold text-green-600';
    } else if (total > 100) {
        display.className = 'font-bold text-red-600';
    } else {
        display.className = 'font-bold text-orange-500';
    }
}

function formatRupiahInput(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = value;
}

function submitForm() {
    const form = document.getElementById('formPencairan');
    const metode = document.querySelector('input[name="metode_pencairan"]:checked').value;
    
    if (metode === 'penuh') {
        const jumlah = document.getElementById('jumlah_dicairkan').value.replace(/\./g, '');
        if (!jumlah || parseInt(jumlah) <= 0) {
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Valid',
                text: 'Nominal pencairan harus lebih dari 0',
                confirmButtonColor: '#2563eb'
            });
            return;
        }
        // Set value clean without dots
        document.getElementById('jumlah_dicairkan').value = jumlah;
    } else {
        // Validasi bertahap
        let total = 0;
        let hasEmptyDate = false;
        
        for(let i=1; i<=stageCount; i++) {
            const persenInput = document.querySelector(`input[name="persentase_tahap_${i}"]`);
            const tanggalInput = document.querySelector(`input[name="tanggal_tahap_${i}"]`);
            
            if (!persenInput || !tanggalInput) continue;
            
            const persen = parseFloat(persenInput.value) || 0;
            total += persen;
            
            if (!tanggalInput.value) {
                hasEmptyDate = true;
            }
        }
        
        if (hasEmptyDate) {
            Swal.fire({
                icon: 'error',
                title: 'Data Tidak Lengkap',
                text: 'Semua tanggal pencairan harus diisi',
                confirmButtonColor: '#2563eb'
            });
            return;
        }
        
        if (Math.abs(total - 100) > 0.1) {
            Swal.fire({
                icon: 'error',
                title: 'Persentase Tidak Valid',
                html: `Total persentase harus <b>100%</b><br><small>Saat ini: ${total}%</small>`,
                confirmButtonColor: '#2563eb'
            });
            return;
        }
    }

    Swal.fire({
        title: 'Konfirmasi Pencairan',
        html: "Apakah data yang dimasukkan sudah benar?<br><small class='text-gray-500'>Tindakan ini tidak dapat dibatalkan.</small>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Cairkan Dana',
        cancelButtonText: '<i class="fas fa-times mr-1"></i> Batal',
        customClass: {
            confirmButton: 'swal-btn-confirm',
            cancelButton: 'swal-btn-cancel'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const hiddenAction = document.createElement('input');
            hiddenAction.type = 'hidden';
            hiddenAction.name = 'action';
            hiddenAction.value = 'cairkan';
            form.appendChild(hiddenAction);
            
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            form.submit();
        }
    });
}
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { 
        animation: fadeInUp 0.3s ease-out forwards; 
    }
    
    /* Improve mobile touch targets */
    @media (max-width: 768px) {
        button, a, input[type="radio"], input[type="checkbox"] {
            min-height: 44px;
            min-width: 44px;
        }
        
        input[type="radio"], input[type="checkbox"] {
            width: 20px;
            height: 20px;
        }
    }
    
    /* Better SweetAlert mobile styling */
    .swal2-popup {
        font-size: 0.875rem !important;
    }
    
    @media (max-width: 640px) {
        .swal2-popup {
            width: 90% !important;
            padding: 1.5rem !important;
        }
        
        .swal2-title {
            font-size: 1.25rem !important;
        }
        
        .swal2-html-container {
            font-size: 0.875rem !important;
        }
    }
    
    .swal-btn-confirm, .swal-btn-cancel {
        font-size: 0.875rem !important;
        padding: 0.625rem 1.25rem !important;
    }
</style>