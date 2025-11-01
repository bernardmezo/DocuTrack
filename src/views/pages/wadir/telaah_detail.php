<?php
// File: src/views/pages/wadir/telaah_detail.php (HANYA UNTUK WADIR)

// --- 1. Setup Variabel (Diterima dari Controller) ---
$status = $status ?? 'Menunggu';

// Status Boolean
$is_disetujui = (strtolower($status) === 'disetujui');
$is_menunggu = (strtolower($status) === 'menunggu');

// Data Payload
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$back_url = $back_url ?? '/docutrack/public/wadir/dashboard'; 

// Fallback helper
if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "RP " . number_format($angka ?? 0, 0, ',', '.'); }
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Persetujuan Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                    <?php else: ?> <span class="font-semibold text-gray-600">Menunggu Persetujuan Anda</span>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?php echo htmlspecialchars($back_url); ?>" 
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 w-full md:w-auto transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>
        
        <form id="form-wadir-approval" action="#" method="POST">
            
            <div class="mb-8 animate-reveal" style="animation-delay: 100ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">1. Kerangka Acuan Kegiatan (KAK)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <div class="md:col-span-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pengusul</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1"><?php echo htmlspecialchars($kegiatan_data['nama_pengusul'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Kegiatan</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1"><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Gambaran Umum</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[50px] leading-relaxed"><?php echo htmlspecialchars($kegiatan_data['gambaran_umum'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Penerima Manfaat</label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[50px] leading-relaxed"><?php echo htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 200ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU)
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Indikator yang Dipilih:</label>
                <div class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-gray-100 rounded-lg border border-gray-200">
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
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 300ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    3. Indikator Kinerja KAK
                </h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
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
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 400ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">4. Rincian Anggaran Biaya (RAB)</h3>
                <?php 
                    $grand_total_rab = 0;
                    if (!empty($rab_data)):
                        foreach ($rab_data as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal = 0;
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                        <?php echo htmlspecialchars($kategori); ?>
                    </h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full min-w-[700px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Uraian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Rincian</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Volume & Satuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Harga Satuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($items as $item): 
                                    $item_id = $item['id'] ?? uniqid();
                                    $total_item = ($item['volume'] ?? 0) * ($item['harga'] ?? 0);
                                    $subtotal += $total_item;
                                ?>
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['uraian'] ?? ''); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['rincian'] ?? ''); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['volume'] ?? 0); ?> <?php echo htmlspecialchars($item['satuan'] ?? ''); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?php echo formatRupiah($item['harga'] ?? 0); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-800 font-medium"><?php echo formatRupiah($total_item); ?></td>
                                </tr>
                                <?php endforeach; $grand_total_rab += $subtotal; ?>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="4" class="px-4 py-2 text-right text-sm text-gray-800">Subtotal</td>
                                    <td class="px-4 py-2 text-sm text-gray-900"><?php echo formatRupiah($subtotal); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
            
            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto">
                
                <?php if ($is_menunggu): ?>
                    <button type="button" id="btn-setujui-wadir" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5">
                         <i class="fas fa-check-double text-xs"></i> Setujui Usulan
                    </button>
                
                <?php elseif ($is_disetujui): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700">
                        <i class="fas fa-check-circle"></i> Telah Anda Setujui
                    </span>
                
                <?php endif; ?>
                 </div>
            </div>
        </form>
        
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        const namaKegiatan = <?php echo json_encode($kegiatan_data['nama_kegiatan'] ?? 'Kegiatan Ini'); ?>;
        const formWadir = document.getElementById('form-wadir-approval');
        
        // --- Event Listener Tombol Setujui (Wadir) ---
        const btnSetujuiWadir = document.getElementById('btn-setujui-wadir');
        btnSetujuiWadir?.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Setujui Usulan Ini?',
                html: `Usulan untuk <strong>${namaKegiatan}</strong> akan disetujui.`,
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
                    // TODO: Ganti action form untuk Wadir Setuju
                    // formWadir.action = '.../wadirSetujui';
                    formWadir.submit();
                }
            });
        });
        
    }); // Akhir DOMContentLoaded
</script>