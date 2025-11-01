<?php
// File: src/views/pages/admin/detail_kegiatan.php (HANYA UNTUK ADMIN)

// --- 1. Setup Variabel (Diterima dari Controller Admin) ---
$status = $status ?? 'Menunggu';
$user_role = $user_role ?? 'admin'; // Role di sini selalu admin

$is_revisi = (strtolower($status) === 'revisi');
$is_disetujui = (strtolower($status) === 'disetujui');
$is_ditolak = (strtolower($status) === 'ditolak');

$komentar_revisi = $komentar_revisi ?? [];
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/admin/dashboard'; 

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "RP " . number_format($angka ?? 0, 0, ',', '.'); }
}

// --- 2. FUNGSI HELPER (KHUSUS ADMIN) ---

function showCommentIcon($field_name, $komentar_list, $is_revisi_mode) {
    if ($is_revisi_mode && isset($komentar_list[$field_name])) {
        $comment = htmlspecialchars($komentar_list[$field_name]);
        echo "<span class='komentar-tooltip-trigger relative ml-2' title='Komentar: {$comment}'>";
        echo " <i class='fas fa-comment-dots text-yellow-500 text-base cursor-help'></i>";
        echo "</span>";
    }
}

function isEditable($field_name, $is_revisi_mode, $komentar_list) {
    // Admin bisa edit jika status revisi DAN ada komentar di field itu
    return $is_revisi_mode && isset($komentar_list[$field_name]);
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                    <?php elseif ($is_revisi): ?> <span class="font-semibold text-yellow-600">Perlu Revisi</span>
                    <?php elseif ($is_ditolak): ?> <span class="font-semibold text-red-600">Ditolak</span>
                    <?php else: ?> <span class="font-semibold text-gray-600"><?php echo htmlspecialchars($status); ?></span>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?php echo htmlspecialchars($back_url); ?>" 
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 w-full md:w-auto transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <?php if ($is_revisi && !empty($komentar_revisi)): ?>
        <div class="revision-alert-box bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8 animate-reveal">
            <div class="flex items-center">
                <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i></div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-yellow-800">Perlu Revisi</h3>
                    <p class="text-sm text-yellow-700 mt-1">Usulan ini dikembalikan dengan catatan. Harap perbaiki bagian yang ditandai:</p>
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
            
            <div class="mb-8 animate-reveal" style="animation-delay: 100ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">1. Kerangka Acuan Kegiatan (KAK)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    
                    <div class="md:col-span-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Nama Pengusul
                            <?php showCommentIcon('nama_pengusul', $komentar_revisi, $is_revisi); ?>
                        </label>
                        <?php if (isEditable('nama_pengusul', $is_revisi, $komentar_revisi)): ?>
                            <input type="text" name="nama_pengusul" class="block w-full px-4 py-3.5 mt-1 text-sm text-gray-800 bg-white rounded-lg border-yellow-400 ring-2 ring-yellow-200 focus:outline-none focus:ring-yellow-500" value="<?php echo htmlspecialchars($kegiatan_data['nama_pengusul'] ?? ''); ?>">
                            <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['nama_pengusul']); ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1"><?php echo htmlspecialchars($kegiatan_data['nama_pengusul'] ?? 'N/A'); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="md:col-span-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Nama Kegiatan
                            <?php showCommentIcon('nama_kegiatan', $komentar_revisi, $is_revisi); ?>
                        </label>
                        <?php if (isEditable('nama_kegiatan', $is_revisi, $komentar_revisi)): ?>
                            <input type="text" name="nama_kegiatan" class="block w-full px-4 py-3.5 mt-1 text-sm text-gray-800 bg-white rounded-lg border-yellow-400 ring-2 ring-yellow-200 focus:outline-none focus:ring-yellow-500" value="<?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? ''); ?>">
                            <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['nama_kegiatan']); ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1"><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Gambaran Umum
                            <?php showCommentIcon('gambaran_umum', $komentar_revisi, $is_revisi); ?>
                        </label>
                        <?php if (isEditable('gambaran_umum', $is_revisi, $komentar_revisi)): ?>
                            <textarea name="gambaran_umum" rows="5" class="block w-full px-4 py-3.5 mt-1 text-sm text-gray-800 bg-white rounded-lg border-yellow-400 ring-2 ring-yellow-200 focus:outline-none focus:ring-yellow-500"><?php echo htmlspecialchars($kegiatan_data['gambaran_umum'] ?? ''); ?></textarea>
                            <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['gambaran_umum']); ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[50px] leading-relaxed <?php echo ($is_revisi) && isset($komentar_revisi['gambaran_umum']) ? 'ring-2 ring-yellow-400' : ''; ?>"><?php echo htmlspecialchars($kegiatan_data['gambaran_umum'] ?? 'N/A'); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Penerima Manfaat
                            <?php showCommentIcon('penerima_manfaat', $komentar_revisi, $is_revisi); ?>
                        </label>
                        <?php if (isEditable('penerima_manfaat', $is_revisi, $komentar_revisi)): ?>
                            <textarea name="penerima_manfaat" rows="3" class="block w-full px-4 py-3.5 mt-1 text-sm text-gray-800 bg-white rounded-lg border-yellow-400 ring-2 ring-yellow-200 focus:outline-none focus:ring-yellow-500"><?php echo htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? ''); ?></textarea>
                            <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['penerima_manfaat']); ?></p>
                        <?php else: ?>
                            <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[50px] leading-relaxed <?php echo ($is_revisi) && isset($komentar_revisi['penerima_manfaat']) ? 'ring-2 ring-yellow-400' : ''; ?>"><?php echo htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? 'N/A'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 200ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU)
                    <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Indikator yang Dipilih:</label>
                <div class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-gray-100 rounded-lg border border-gray-200 <?php echo ($is_revisi) && isset($komentar_revisi['iku_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                    <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg <?php echo ($is_revisi) && isset($komentar_revisi['indikator_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                            $rab_comment_key = 'rab_' . strtolower(str_replace(' ', '_', $kategori));
                            $has_rab_comment = isEditable($rab_comment_key, $is_revisi, $komentar_revisi);
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                        <?php echo htmlspecialchars($kategori); ?>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi); ?>
                    </h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg <?php echo $has_rab_comment ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                                <tr class="<?php echo $has_rab_comment ? 'bg-yellow-50' : ''; ?>">
                                    <?php if ($has_rab_comment): ?>
                                        <td><input type="text" name="rab[<?php echo htmlspecialchars($kategori); ?>][<?php echo htmlspecialchars($item_id); ?>][uraian]" class="w-full text-sm p-2 border-gray-300 rounded" value="<?php echo htmlspecialchars($item['uraian'] ?? ''); ?>"></td>
                                        <td><input type="text" name="rab[<?php echo htmlspecialchars($kategori); ?>][<?php echo htmlspecialchars($item_id); ?>][rincian]" class="w-full text-sm p-2 border-gray-300 rounded" value="<?php echo htmlspecialchars($item['rincian'] ?? ''); ?>"></td>
                                        <td class="flex items-center gap-2 p-2">
                                            <input type="number" name="rab[<?php echo htmlspecialchars($kategori); ?>][<?php echo htmlspecialchars($item_id); ?>][volume]" class="w-20 text-sm p-2 border-gray-300 rounded" value="<?php echo htmlspecialchars($item['volume'] ?? 0); ?>">
                                            <input type="text" class="w-20 text-sm p-2 border-gray-300 rounded" name="rab[<?php echo htmlspecialchars($kategori); ?>][<?php echo htmlspecialchars($item_id); ?>][satuan]" value="<?php echo htmlspecialchars($item['satuan'] ?? ''); ?>">
                                        </td>
                                        <td><input type="number" name="rab[<?php echo htmlspecialchars($kategori); ?>][<?php echo htmlspecialchars($item_id); ?>][harga]" class="w-32 text-sm p-2 border-gray-300 rounded" value="<?php echo htmlspecialchars($item['harga'] ?? 0); ?>"></td>
                                    <?php else: ?>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['uraian'] ?? ''); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['rincian'] ?? ''); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo htmlspecialchars($item['volume'] ?? 0); ?> <?php echo htmlspecialchars($item['satuan'] ?? ''); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?php echo formatRupiah($item['harga'] ?? 0); ?></td>
                                    <?php endif; ?>
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
                    <?php if ($has_rab_comment): ?>
                        <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi[$rab_comment_key]); ?></p>
                    <?php endif; ?>
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
            
            <div id="mak-section" class="mt-8 pt-6 border-t border-gray-200 animate-reveal 
                <?php echo ($is_disetujui) ? 'block' : 'hidden'; ?>" 
                style="animation-delay: 500ms;">
                
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    Kode Mata Anggaran Kegiatan (MAK)
                    <?php showCommentIcon('kode_mak', $komentar_revisi, $is_revisi); ?>
                </h3>
                <div class="relative max-w-md">
                    <i class="fas fa-key absolute top-3.5 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                    <input type="text" id="kode_mak" name="kode_mak" 
                           class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer <?php echo $is_revisi && isset($komentar_revisi['kode_mak']) ? 'border-yellow-500 ring-2 ring-yellow-300' : ''; ?>" 
                           value="<?php echo htmlspecialchars($kode_mak); ?>" placeholder=" " 
                           <?php echo (($is_disetujui || $is_ditolak) && !empty($kode_mak)) ? 'readonly' : ''; ?>>
                    <label for="kode_mak" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Masukkan Kode MAK</label>
                </div>
                 <?php if ($is_revisi && isset($komentar_revisi['kode_mak'])): ?>
                    <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['kode_mak']); ?></p>
                <?php endif; ?>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto">
                
                <?php if ($is_revisi): ?>
                    <button type="submit" id="btn-simpan-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5">
                         <i class="fas fa-save text-xs"></i> Simpan Revisi
                    </button>
                
                <?php elseif ($is_disetujui): ?>
                    <button type="button" id="print-pdf-btn" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-print text-xs"></i> Print PDF
                    </button>
                <?php endif; ?>
                </div>
            </div>
        </form>
        
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        
        // --- 1. Ambil Status & Nama dari PHP ---
        const isRevisi = <?php echo json_encode($is_revisi); ?>;
        const isDisetujui = <?php echo json_encode($is_disetujui); ?>;
        const namaKegiatan = <?php echo json_encode($kegiatan_data['nama_kegiatan'] ?? 'Kegiatan Ini'); ?>;
        const formVerifikasi = document.getElementById('form-verifikasi');

        // --- 2. Fallback Fungsi formatRupiah ---
        if (typeof formatRupiah !== 'function') {
            window.formatRupiah = (angka) => `RP ${new Intl.NumberFormat('id-ID').format(angka || 0)}`;
        }
        
        // --- 3. Logika Tombol Print ---
        const printPdfBtn = document.getElementById('print-pdf-btn');
        printPdfBtn?.addEventListener('click', (e) => {
            e.preventDefault(); 
            window.print(); 
        });

        // --- 4. Logika Floating Label ---
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
                updateLabel(); // Panggil saat load
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

        // ===================================
        // 💡 LOGIKA UI ADMIN (HANYA SIMPAN REVISI) 💡
        // ===================================
        
        const btnSimpanRevisiAdmin = document.getElementById('btn-simpan-revisi');

        btnSimpanRevisiAdmin?.addEventListener('click', (e) => {
            e.preventDefault();
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Perubahan yang Anda buat akan disimpan dan usulan akan dikirim kembali untuk verifikasi.",
                icon: 'info',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#F59E0B', // Kuning
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    formVerifikasi.submit();
                }
            });
        });
        
    }); // Akhir DOMContentLoaded
</script>