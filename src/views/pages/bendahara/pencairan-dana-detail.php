<?php
// File: src/views/pages/bendahara/pencairan-dana-detail.php
$status_lower = strtolower($status);

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return "Rp " . number_format($angka ?? 0, 0, ',', '.');
    }
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <?php if (isset($_SESSION['flash_message'])) : ?>
    <div class="mb-6 p-4 rounded-lg <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-yellow-50 border border-yellow-200 text-yellow-800' ?>">
        <div class="flex items-center gap-2">
            <i class="fas fa-<?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <span class="font-medium"><?= htmlspecialchars($_SESSION['flash_message']) ?></span>
        </div>
    </div>
        <?php
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
    endif;
    ?>

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($status_lower === 'dana diberikan') : ?>
                        <span class="font-semibold text-green-600">Dana Diberikan</span>
                    <?php elseif ($status_lower === 'revisi') : ?>
                        <span class="font-semibold text-yellow-600">Revisi</span>
                    <?php else : ?>
                        <span class="font-semibold text-gray-600"><?= htmlspecialchars($status) ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

    <?php if (!empty($riwayat_pencairan)) : ?>
        <div class="mb-8 pt-6 border-t border-gray-200">
            <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">
                <i class="fas fa-history text-blue-600"></i> Riwayat Pencairan Dana
            </h3>
            
            <div class="overflow-x-auto border border-gray-200 rounded-lg mb-4">
                <table class="w-full">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Termin</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Nominal</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <?php foreach ($riwayat_pencairan as $index => $pencairan) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm text-gray-700"><?= $index + 1 ?></td>
                            <td class="px-4 py-3 text-sm text-gray-700">
                                <?= date('d M Y', strtotime($pencairan['tanggal_pencairan'])) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 font-medium">
                                <?= htmlspecialchars($pencairan['termin']) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right">
                                <?= formatRupiah($pencairan['nominal']) ?>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600">
                                <?= htmlspecialchars($pencairan['catatan'] ?: '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Box -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs font-semibold text-gray-600 uppercase">Total Anggaran</p>
                    <p class="text-xl font-bold text-blue-600"><?= formatRupiah($anggaran_disetujui) ?></p>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-xs font-semibold text-gray-600 uppercase">Sudah Dicairkan</p>
                    <p class="text-xl font-bold text-green-600"><?= formatRupiah($total_dicairkan) ?></p>
                </div>
                
                <div class="p-4 <?= $sisa_dana > 0 ? 'bg-orange-50 border-orange-200' : 'bg-gray-50 border-gray-200' ?> rounded-lg border">
                    <p class="text-xs font-semibold text-gray-600 uppercase">Sisa Dana</p>
                    <p class="text-xl font-bold <?= $sisa_dana > 0 ? 'text-orange-600' : 'text-gray-600' ?>">
                        <?= formatRupiah($sisa_dana) ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if ($status_lower === 'menunggu' || $status_lower === 'dana belum diberikan semua') : ?>
    <form method="POST" action="/docutrack/public/bendahara/pencairan-dana/proses" id="formPencairan">
        <input type="hidden" name="kegiatanId" value="<?= $kegiatan_data['id'] ?? '' ?>">
        <input type="hidden" name="total_anggaran" id="total_anggaran" value="<?= $anggaran_disetujui ?? 0 ?>">
        <input type="hidden" name="sisa_dana" id="sisa_dana" value="<?= $sisa_dana ?? 0 ?>">
        <input type="hidden" name="metode_pencairan" value="bertahap">
    <?php endif; ?>

            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">1. Kerangka Acuan Kegiatan (KAK)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pengusul</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                            <?= htmlspecialchars($kegiatan_data['nama_pengusul']) ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM Pengusul</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                            <?= htmlspecialchars($kegiatan_data['nim_pengusul']) ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Penanggung Jawab</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                            <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab']) ?>
                        </p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">NIM/NIP Penanggung Jawab</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                            <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab']) ?>
                        </p>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kegiatan</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1">
                            <?= htmlspecialchars($kegiatan_data['nama_kegiatan']) ?>
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gambaran Umum</label>
                        <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[100px] leading-relaxed">
                            <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'])) ?>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima Manfaat</label>
                        <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[80px] leading-relaxed">
                            <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'])) ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Strategi Pencapaian Keluaran</h3>
                
                <div class="mb-5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Metode Pelaksanaan</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[100px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'])) ?>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahapan Kegiatan</label>
                    <div class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[120px] leading-relaxed">
                        <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'])) ?>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">2. Indikator Kinerja Utama (IKU)</h3>
                <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                    <?php foreach ($iku_data as $iku) : ?>
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            <?= htmlspecialchars($iku) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>

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
                            <?php foreach ($indikator_data as $indikator) : ?>
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

            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                
                <?php
                $grand_total_rab = 0;
                foreach ($rab_data as $kategori => $items) :
                    if (empty($items)) {
                        continue;
                    }
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
                                <?php foreach ($items as $item) :
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
                                <?php endforeach;
                                $grand_total_rab += $subtotal; ?>
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
            </div>

            <?php if ($boleh_cairkan_lagi) : ?>
                <div class="mb-8 pt-6 border-t border-gray-200">
                    <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">
                        <?= empty($riwayat_pencairan) ? 'Proses Pencairan Dana Bertahap' : 'Pencairan Dana Tambahan' ?>
                    </h3>
                    
                    <div class="grid grid-cols-1 gap-y-5">
                        
                        <!-- Info Total Anggaran -->
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                                <div>
                                    <p class="text-sm font-semibold text-blue-900">Total Anggaran yang Disetujui</p>
                                    <p class="text-2xl font-bold text-blue-600"><?= formatRupiah($sisa_dana) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Container Input Bertahap -->
                        <div class="space-y-4 p-5 bg-gray-50 rounded-xl border border-gray-200">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <label class="text-sm font-bold text-gray-700">Rincian Tahapan Pencairan</label>
                                    <p class="text-xs text-gray-500 mt-1">Masukkan detail setiap termin pencairan dana</p>
                                </div>
                                <button type="button" onclick="addStage()" class="text-sm bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <i class="fas fa-plus"></i> Tambah Tahap
                                </button>
                            </div>
                            
                            <input type="hidden" name="jumlah_tahap" id="jumlah_tahap" value="0">
                            
                            <div id="stages-wrapper" class="space-y-3">
                                <!-- Dynamic inputs will appear here -->
                            </div>
                            
                            <div class="mt-4 p-3 bg-white rounded-lg border border-gray-200">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-700">Total Nominal:</span>
                                    <span id="total-nominal" class="text-xl font-bold text-gray-800">Rp 0</span>
                                </div>
                                <div class="mt-2 text-xs text-gray-500">
                                    <i class="fas fa-exclamation-circle"></i> 
                                    Total nominal harus sama dengan total anggaran yang disetujui
                                </div>
                            </div>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan Bendahara (Opsional)</label>
                            <textarea name="catatan" rows="3" placeholder="Tambahkan catatan atau instruksi khusus untuk pencairan bertahap..." class="mt-1 block w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                    </div>
                </div>
                <?php else: ?>
                <!-- Tampilkan pesan jika sudah lunas -->
                <div class="mb-8 pt-6 border-t border-gray-200">
                    <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            <div>
                                <p class="text-sm font-semibold text-green-900">Dana Sudah Dicairkan Seluruhnya</p>
                                <p class="text-xs text-green-700 mt-1">
                                    Total dana sebesar <?= formatRupiah($anggaran_disetujui) ?> telah dicairkan. 
                                    Tidak ada pencairan tambahan yang diperlukan.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?= htmlspecialchars($back_url) ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <?php if ($status_lower === 'menunggu' || $status_lower === 'dana belum diberikan semua') : ?>
                <div class="flex gap-4 w-full sm:w-auto">
                    <button type="button" onclick="submitForm()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all">
                        <i class="fas fa-check-circle text-xs"></i> Proses Pencairan Bertahap
                    </button>
                </div>
                <?php endif; ?>
            </div>

    <?php if ($status_lower === 'menunggu' || $status_lower === 'dana belum diberikan semua') : ?>
    </form>
    <?php endif; ?>

    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// State management
let stageCount = 0;

// ✅ GANTI: Gunakan sisa_dana, bukan total_anggaran
let sisaDana = parseInt(document.getElementById("sisa_dana") ? document.getElementById("sisa_dana").value : 0) || 0;

// ✅ TAMBAHAN: Simpan juga total anggaran untuk display
let totalAnggaran = parseInt(document.getElementById("total_anggaran") ? document.getElementById("total_anggaran").value : 0) || 0;

document.addEventListener('DOMContentLoaded', function() {
    console.log('Total Anggaran:', totalAnggaran);
    console.log('Sisa Dana Belum dicairkan:', sisaDana);
    
    // Initialize 1 stage by default if allowed
    if (document.getElementById('stages-wrapper')) {
        addStage();
    }
});

/**
 * Tambah tahap pencairan baru
 */
function addStage() {
    if (stageCount >= 5) {
        Swal.fire('Perhatian', 'Maksimal 5 tahap pencairan', 'warning');
        return;
    }
    
    stageCount++;
    document.getElementById('jumlah_tahap').value = stageCount;
    
    const wrapper = document.getElementById('stages-wrapper');
    const div = document.createElement('div');
    div.className = 'flex gap-3 items-start p-4 bg-white rounded-lg border border-gray-200 animate-fade-in-up';
    div.id = `stage-${stageCount}`;
    div.innerHTML = `
        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-bold text-sm flex-shrink-0">
            ${stageCount}
        </div>
        <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Tanggal Pencairan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggalTahapan[]" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Termin <span class="text-red-500">*</span></label>
                <input type="text" name="terminTahapan[]" placeholder="Contoh: Termin ${stageCount}" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600 block mb-1">Nominal (Rp) <span class="text-red-500">*</span></label>
                <input type="text" name="nominalTahapan[]" placeholder="0" class="nominal-input w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-right" oninput="formatRupiahInput(this); updateTotalNominal()" required>
            </div>
        </div>
        <button type="button" onclick="removeStage(${stageCount})" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded transition-colors flex-shrink-0" title="Hapus tahap ini">
            <i class="fas fa-trash text-sm"></i>
        </button>
    `;
    wrapper.appendChild(div);
}

/**
 * Hapus tahap pencairan
 */
function removeStage(stageId) {
    const stageElement = document.getElementById(`stage-${stageId}`);
    if (stageElement) {
        stageElement.remove();
        updateTotalNominal();
        
        // Update stage count
        const remainingStages = document.querySelectorAll('[id^="stage-"]').length;
        // stageCount = remainingStages; // Don't reset counter to avoid duplicate IDs
        document.getElementById('jumlah_tahap').value = remainingStages;
    }
}

/**
 * Format input rupiah dengan titik pemisah ribuan
 */
function formatRupiahInput(input) {
    let value = input.value.replace(/\D/g, ''); // Hapus semua non-digit
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); // Tambah titik setiap 3 digit
    input.value = value;
}

/**
 * Update total nominal dari semua tahapan
 */
function updateTotalNominal() {
    let total = 0;
    const inputs = document.querySelectorAll('.nominal-input');
    
    inputs.forEach(input => {
        const cleanValue = input.value.replace(/\D/g, ''); // Hapus titik dan char lain
        total += parseInt(cleanValue) || 0;
    });
    
    const display = document.getElementById('total-nominal');
    if (display) {
        display.innerText = 'Rp ' + total.toLocaleString('id-ID');
        
        // Visual feedback berdasarkan sisa dana
        if (total === sisaDana) {
            display.className = 'text-xl font-bold text-green-600';
        } else if (total > sisaDana) {
            display.className = 'text-xl font-bold text-red-600';
        } else {
            display.className = 'text-xl font-bold text-gray-800';
        }
    }
}

/**
 * Validasi dan submit form pencairan
 */
function submitForm() {
    try {
        const form = document.getElementById('formPencairan');
        if (!form) {
            Swal.fire('Error', 'Form tidak ditemukan di halaman', 'error');
            return;
        }
        
        const tanggalInputs = document.querySelectorAll('input[name="tanggalTahapan[]"]');
        const terminInputs = document.querySelectorAll('input[name="terminTahapan[]"]');
        const nominalInputs = document.querySelectorAll('input[name="nominalTahapan[]"]');
        
        if (tanggalInputs.length === 0) {
            Swal.fire('Error', 'Minimal 1 tahap pencairan diperlukan', 'error');
            return;
        }

        // Validasi semua field harus terisi
        let hasEmptyField = false;
        let totalNominal = 0;
        
        for (let i = 0; i < tanggalInputs.length; i++) {
            const tanggal = tanggalInputs[i].value;
            const termin = terminInputs[i].value;
            const nominalRaw = nominalInputs[i].value;
            const nominal = parseFloat(nominalRaw.replace(/\D/g, '')) || 0;
            
            if (!tanggal || !termin || nominal <= 0) {
                hasEmptyField = true;
                break;
            }
            
            totalNominal += nominal;
        }
        
        if (hasEmptyField) {
            Swal.fire('Error', 'Semua field tahapan harus diisi dengan benar', 'error');
            return;
        }

        const sisaDanaInt = parseInt(sisaDana);
        const totalNominalInt = parseInt(totalNominal);
        
        // Validasi total nominal tidak boleh MELEBIHI sisa dana
        if (totalNominalInt > sisaDanaInt || totalNominalInt < 0) {
            Swal.fire({
                title: 'Total Nominal Tidak Sesuai',
                html: `
                    <div class="text-left">
                        <p class="mb-2">Total nominal tahapan melebihi sisa dana yang tersedia:</p>
                        <div class="bg-gray-50 p-3 rounded">
                            <p><strong>Total Anggaran:</strong> Rp ${totalAnggaran.toLocaleString('id-ID')}</p>
                            <p><strong>Sudah Dicairkan:</strong> Rp ${(totalAnggaran - sisaDanaInt).toLocaleString('id-ID')}</p>
                            <p><strong>Sisa Dana:</strong> Rp ${sisaDanaInt.toLocaleString('id-ID')}</p>
                            <p><strong>Total Input:</strong> Rp ${totalNominalInt.toLocaleString('id-ID')}</p>
                            <p class="mt-2 text-red-600">
                                <strong>Kelebihan:</strong> Rp ${Math.abs(sisaDanaInt - totalNominalInt).toLocaleString('id-ID')}
                            </p>
                        </div>
                    </div>
                `,
                icon: 'error'
            });
            return;
        }

        // Konfirmasi jika mencairkan kurang dari sisa dana atau sama
        const sisaSetelahPencairan = sisaDanaInt - totalNominalInt;
        let confirmTitle = 'Konfirmasi Pencairan';
        let confirmHtml = `
            <div class="text-left">
                <p class="mb-3">Anda akan mencairkan dana sebanyak <strong>${tanggalInputs.length} tahap</strong>:</p>
                <div class="bg-blue-50 p-3 rounded border border-blue-200 mb-3">
                    <p class="text-2xl font-bold text-blue-600">Rp ${totalNominalInt.toLocaleString('id-ID')}</p>
                </div>
        `;

        if (sisaSetelahPencairan > 0) {
            confirmTitle = 'Konfirmasi Pencairan Sebagian';
            confirmHtml += `
                <p class="text-sm text-gray-600">
                    <i class="fas fa-exclamation-circle text-orange-500"></i> 
                    Masih ada sisa dana sebesar <strong>Rp ${sisaSetelahPencairan.toLocaleString('id-ID')}</strong> yang belum dicairkan.
                </p>
            `;
        }
        
        confirmHtml += '</div>';

        Swal.fire({
            title: confirmTitle,
            html: confirmHtml,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#d1d5db',
            confirmButtonText: 'Ya, Cairkan Dana',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const existingAction = form.querySelector('input[name="action"]');
                if (existingAction) existingAction.remove();
                
                const hiddenAction = document.createElement('input');
                hiddenAction.type = 'hidden';
                hiddenAction.name = 'action';
                hiddenAction.value = 'cairkan';
                form.appendChild(hiddenAction);
                
                form.submit();
            }
        });
        
    } catch (error) {
        console.error('submitForm error:', error);
        Swal.fire('Error', 'Terjadi kesalahan: ' + error.message, 'error');
    }
}
</script>

<style>
@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(10px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}
.animate-fade-in-up { 
    animation: fadeInUp 0.3s ease-out forwards; 
}
</style>