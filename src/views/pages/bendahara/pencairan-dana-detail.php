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
            <!-- Simpan total anggaran asli untuk validasi -->
            <input type="hidden" name="total_anggaran" id="total_anggaran" value="<?= $anggaran_disetujui ?? 0 ?>">
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

            <?php if ($status_lower === 'menunggu' || $status_lower === 'dana diberikan'): ?>
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Proses Pencairan Dana</h3>
                
                <div class="grid grid-cols-1 gap-y-5">
                    
                    <!-- Pilihan Metode -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 block">
                            Metode Pencairan <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-4">
                            <label class="flex-1 flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" name="metode_pencairan" value="penuh" class="peer mr-3" checked onchange="toggleMetode('penuh')">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700 block">Pencairan Penuh</span>
                                    <span class="text-xs text-gray-500">Cairkan 100% dana sekaligus</span>
                                </div>
                            </label>
                            <label class="flex-1 flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                <input type="radio" name="metode_pencairan" value="bertahap" class="peer mr-3" onchange="toggleMetode('bertahap')">
                                <div>
                                    <span class="text-sm font-semibold text-gray-700 block">Pencairan Bertahap</span>
                                    <span class="text-xs text-gray-500">Cairkan dana dalam beberapa termin</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Container Input Penuh -->
                    <div id="container-penuh" class="space-y-4 p-5 bg-gray-50 rounded-xl border border-gray-200">
                        <div>
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nominal Pencairan (Rp) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative mt-1">
                                <input type="text" id="jumlah_dicairkan" name="jumlah_dicairkan" 
                                       value="<?= number_format($grand_total_rab, 0, '', '') ?>"
                                       class="block w-full px-4 py-3 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 bg-white"
                                       oninput="formatRupiah(this)">
                            </div>
                        </div>
                    </div>

                    <!-- Container Input Bertahap -->
                    <div id="container-bertahap" class="hidden space-y-4 p-5 bg-gray-50 rounded-xl border border-gray-200">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rincian Tahapan</label>
                            <button type="button" onclick="addStage()" class="text-xs bg-blue-100 text-blue-700 px-3 py-1.5 rounded-md hover:bg-blue-200 transition-colors">
                                + Tambah Tahap
                            </button>
                        </div>
                        
                        <input type="hidden" name="jumlah_tahap" id="jumlah_tahap" value="0">
                        <div id="stages-wrapper" class="space-y-3">
                            <!-- Dynamic inputs will appear here -->
                        </div>
                        <div class="text-right text-xs text-gray-500 mt-2">
                            Total Persentase: <span id="total-persen" class="font-bold text-gray-800">0%</span>
                        </div>
                    </div>

                    <!-- Catatan -->
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Catatan Bendahara (Opsional)</label>
                        <textarea name="catatan" rows="2" class="mt-1 block w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                </div>
            </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?= htmlspecialchars($back_url) ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <?php if ($status_lower === 'menunggu'): ?>
                <div class="flex gap-4 w-full sm:w-auto">
                    <button type="button" onclick="submitForm()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all">
                        <i class="fas fa-check-circle text-xs"></i> Proses Pencairan
                    </button>
                </div>
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
    formatRupiah(document.getElementById('jumlah_dicairkan'));
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
    if(stageCount >= 5) return; // Max 5 tahap
    stageCount++;
    document.getElementById('jumlah_tahap').value = stageCount;
    
    const wrapper = document.getElementById('stages-wrapper');
    const div = document.createElement('div');
    div.className = 'flex gap-3 items-end animate-fade-in-up';
    div.innerHTML = `
        <div class="w-10 text-center py-2.5 text-sm font-bold text-gray-400">#${stageCount}</div>
        <div class="flex-1">
            <label class="text-[10px] uppercase text-gray-500 font-semibold">Tanggal Pencairan</label>
            <input type="date" name="tanggal_tahap_${stageCount}" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div class="w-24">
            <label class="text-[10px] uppercase text-gray-500 font-semibold">Persentase</label>
            <div class="relative">
                <input type="number" name="persentase_tahap_${stageCount}" class="w-full pl-3 pr-6 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-right" placeholder="0" min="1" max="100" oninput="updateTotalPersen()">
                <span class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-500 text-xs">%</span>
            </div>
        </div>
    `;
    wrapper.appendChild(div);
}

function updateTotalPersen() {
    let total = 0;
    for(let i=1; i<=stageCount; i++) {
        const val = parseFloat(document.querySelector(`input[name="persentase_tahap_${i}"]`).value) || 0;
        total += val;
    }
    const display = document.getElementById('total-persen');
    display.innerText = total + '%';
    display.className = total === 100 ? 'font-bold text-green-600' : 'font-bold text-red-500';
}

function formatRupiah(input) {
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
            Swal.fire('Error', 'Nominal pencairan tidak valid', 'error');
            return;
        }
        // Set value clean without dots
        document.getElementById('jumlah_dicairkan').value = jumlah;
    } else {
        let total = 0;
        for(let i=1; i<=stageCount; i++) {
            total += parseFloat(document.querySelector(`input[name="persentase_tahap_${i}"]`).value) || 0;
        }
        if (Math.abs(total - 100) > 0.1) {
            Swal.fire('Error', `Total persentase harus 100% (Saat ini: ${total}%)`, 'error');
            return;
        }
    }

    Swal.fire({
        title: 'Konfirmasi Pencairan',
        text: "Apakah data yang dimasukkan sudah benar? Tindakan ini tidak dapat dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#d1d5db',
        confirmButtonText: 'Ya, Cairkan Dana',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const hiddenAction = document.createElement('input');
            hiddenAction.type = 'hidden';
            hiddenAction.name = 'action';
            hiddenAction.value = 'cairkan';
            form.appendChild(hiddenAction);
            
            form.submit();
        }
    });
}
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
</style>