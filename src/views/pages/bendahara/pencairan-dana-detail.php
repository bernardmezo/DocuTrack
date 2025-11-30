<?php
// File: src/views/pages/bendahara/pencairan-dana-detail.php
$status_lower = strtolower($status);

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <?php if (isset($_SESSION['flash_message'])): ?>
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
                    <?php if ($status_lower === 'dana diberikan'): ?>
                        <span class="font-semibold text-green-600">Dana Diberikan</span>
                    <?php elseif ($status_lower === 'revisi'): ?>
                        <span class="font-semibold text-yellow-600">Revisi</span>
                    <?php else: ?>
                        <span class="font-semibold text-gray-600"><?= htmlspecialchars($status) ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?= htmlspecialchars($back_url) ?>" 
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 w-full md:w-auto">
                <i class="fas fa-arrow-left text-xs"></i> 
                <?= htmlspecialchars($back_text ?? 'Kembali') ?>
            </a>
        </div>

        <?php if ($status_lower === 'menunggu'): ?>
        <form method="POST" action="/docutrack/public/bendahara/pencairan-dana/proses" id="formPencairan">
            <input type="hidden" name="kak_id" value="<?= $kegiatan_data['id'] ?? '' ?>">
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
                    <?php foreach ($iku_data as $iku): ?>
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

            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                
                <?php 
                $grand_total_rab = 0;
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
            </div>

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
                                    <?= date('d M Y', strtotime($kegiatan_data['tanggal_mulai'])) ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Selesai</span>
                                <span class="text-sm text-gray-800 font-medium">
                                    <?= date('d M Y', strtotime($kegiatan_data['tanggal_selesai'])) ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($status_lower === 'menunggu' || $status_lower === 'dana diberikan'): ?>
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Proses Pencairan Dana</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nominal yang Disetujui & Dicairkan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" 
                                   id="jumlah_dicairkan"
                                   name="jumlah_dicairkan" 
                                   value="<?= $status_lower === 'dana diberikan' ? number_format($jumlah_dicairkan ?? 0, 0, '', '') : '' ?>"
                                   placeholder="0"
                                   class="block w-full pl-10 pr-4 py-3 text-sm <?= $status_lower === 'dana diberikan' ? 'bg-gray-100' : '' ?> border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   <?= $status_lower === 'dana diberikan' ? 'readonly' : 'required' ?>>
                        </div>
                        <?php if ($status_lower === 'dana diberikan' && $tanggal_pencairan): ?>
                        <p class="mt-1 text-xs text-green-600">
                            <i class="fas fa-check-circle"></i> Dicairkan pada: <?= date('d F Y, H:i', strtotime($tanggal_pencairan)) ?>
                        </p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1 block">
                            Metode Pencairan <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2">
                            <?php 
                            $metode_terpilih = 'uang_muka';
                            if ($status_lower === 'dana diberikan' && !empty($metode_pencairan)) {
                                $metode_terpilih = $metode_pencairan;
                            }
                            ?>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg <?= $status_lower === 'menunggu' ? 'hover:bg-gray-50 cursor-pointer' : 'bg-gray-50 cursor-not-allowed' ?>">
                                <input type="radio" 
                                       name="metode_pencairan" 
                                       value="uang_muka" 
                                       class="mr-3" 
                                       <?= $status_lower === 'dana diberikan' ? 'disabled' : '' ?>
                                       <?= $metode_terpilih === 'uang_muka' ? 'checked' : '' ?>>
                                <span class="text-sm text-gray-700">Uang Muka</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg <?= $status_lower === 'menunggu' ? 'hover:bg-gray-50 cursor-pointer' : 'bg-gray-50 cursor-not-allowed' ?>">
                                <input type="radio" 
                                       name="metode_pencairan" 
                                       value="dana_penuh" 
                                       class="mr-3" 
                                       <?= $status_lower === 'dana diberikan' ? 'disabled' : '' ?>
                                       <?= $metode_terpilih === 'dana_penuh' ? 'checked' : '' ?>>
                                <span class="text-sm text-gray-700">Dana Penuh</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- ✅ Input Tanggal Batas LPJ -->
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Batas Waktu Pengumpulan LPJ <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <input type="date" 
                                   id="tenggat_lpj"
                                   name="tenggat_lpj" 
                                   min="<?= date('Y-m-d') ?>"
                                   class="block w-full px-4 py-3 text-sm <?= $status_lower === 'dana diberikan' ? 'bg-gray-100' : '' ?> border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   <?= $status_lower === 'dana diberikan' ? 'readonly' : 'required' ?>>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            <i class="fas fa-info-circle"></i> Mahasiswa harus mengumpulkan LPJ sebelum tanggal ini
                        </p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?= htmlspecialchars($back_url) ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> 
                    <?= htmlspecialchars($back_text ?? 'Kembali') ?>
                </a>
                 
                <?php if ($status_lower === 'menunggu'): ?>
                <div class="flex gap-4 w-full sm:w-auto">

                    <button type="button" 
                            onclick="konfirmasiCairkan()"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all">
                        <i class="fas fa-check-circle text-xs"></i> Setujui & Cairkan
                    </button>
                </div>
                <?php endif; ?>
            </div>

        <?php if ($status_lower === 'menunggu'): ?>
        </form>
        <?php endif; ?>

    </section>
</main>

<script>
// Format input rupiah dengan pemisah ribuan
document.addEventListener('DOMContentLoaded', function() {
    const inputJumlah = document.getElementById('jumlah_dicairkan');
    
    if (inputJumlah && !inputJumlah.hasAttribute('readonly')) {
        formatRupiah(inputJumlah);
        
        inputJumlah.addEventListener('input', function(e) {
            formatRupiah(e.target);
        });
        
        inputJumlah.addEventListener('keypress', function(e) {
            if (e.which < 48 || e.which > 57) {
                e.preventDefault();
            }
        });
    } else if (inputJumlah && inputJumlah.hasAttribute('readonly')) {
        formatRupiah(inputJumlah);
    }
});

function formatRupiah(input) {
    let value = input.value.replace(/\D/g, '');
    value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = value;
}

function konfirmasiCairkan() {
    const form = document.getElementById('formPencairan');
    const jumlah = document.getElementById('jumlah_dicairkan').value.replace(/\./g, '');
    const tenggatLpj = document.getElementById('tenggat_lpj').value;
    
    // Validasi jumlah
    if (!jumlah || parseInt(jumlah) <= 0) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Jumlah Tidak Valid',
                text: 'Jumlah dana harus diisi dengan nilai yang valid!',
                confirmButtonColor: '#3B82F6'
            });
        } else {
            alert('Jumlah dana harus diisi dengan nilai yang valid!');
        }
        return;
    }
    
    // ✅ Validasi tenggat LPJ
    if (!tenggatLpj) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Batas LPJ Belum Diisi',
                text: 'Tanggal batas pengumpulan LPJ wajib diisi!',
                confirmButtonColor: '#3B82F6'
            });
        } else {
            alert('Tanggal batas pengumpulan LPJ wajib diisi!');
        }
        return;
    }
    
    const formatted = parseInt(jumlah).toLocaleString('id-ID');
    const formattedTanggal = new Date(tenggatLpj).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Cairkan Dana?',
            html: `Apakah Anda yakin akan menyetujui dan mencairkan dana sebesar<br><strong class="text-blue-600">Rp ${formatted}</strong>?<br><br><small class="text-gray-600">Batas pengumpulan LPJ: <strong>${formattedTanggal}</strong></small><br><br><small class="text-red-600">Tindakan ini tidak dapat dibatalkan.</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Cairkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'Memproses...', 
                    text: 'Sedang mencairkan dana...',
                    allowOutsideClick: false, 
                    didOpen: () => Swal.showLoading() 
                });
                
                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'cairkan';
                form.appendChild(actionInput);
                
                // Pastikan mengirim angka murni tanpa titik
                document.getElementById('jumlah_dicairkan').value = jumlah;
                form.submit();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin akan mencairkan dana sebesar Rp ' + formatted + '?\nBatas LPJ: ' + formattedTanggal + '\n\nTindakan ini tidak dapat dibatalkan.')) {
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'cairkan';
            form.appendChild(actionInput);
            
            document.getElementById('jumlah_dicairkan').value = jumlah;
            form.submit();
        }
    }
}
</script>