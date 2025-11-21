<?php
// File: src/views/pages/verifikator/telaah_detail.php (HANYA UNTUK VERIFIKATOR)

// --- 1. Setup Variabel (HANYA UNTUK VERIFIKATOR) ---
$status = $status ?? 'Menunggu';
$user_role = $user_role ?? 'verifikator'; 

$is_disetujui = (strtolower($status) === 'disetujui');
$is_menunggu = (strtolower($status) === 'menunggu');
$is_telah_direvisi = (strtolower($status) === 'telah direvisi');
$is_ditolak = (strtolower($status) === 'ditolak');
$is_revisi = (strtolower($status) === 'revisi'); // Status saat menunggu admin

$komentar_revisi = $komentar_revisi ?? [];
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/verifikator/dashboard'; 

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "RP " . number_format($angka ?? 0, 0, ',', '.'); }
}

// --- 2. FUNGSI HELPER (KHUSUS VERIFIKATOR) ---
function showCommentIcon($field_name, $komentar_list, $is_revisi, $is_telah_direvisi) {
    if (($is_revisi || $is_telah_direvisi) && isset($komentar_list[$field_name])) {
        $comment = htmlspecialchars($komentar_list[$field_name]);
        echo "<span class='komentar-tooltip-trigger relative ml-2' title='Komentar: {$comment}'>";
        echo " <i class='fas fa-comment-dots text-yellow-500 text-base cursor-help'></i>";
        echo "</span>";
    }
}
function render_comment_box($field_name, $is_menunggu_status, $is_telah_direvisi_status) {
    if ($is_menunggu_status || $is_telah_direvisi_status) { // Hanya tampil jika status menunggu/telah direvisi
        echo "<div id='comment-box-{$field_name}' class='comment-box hidden mt-2 animate-reveal'>";
        echo "  <label for='comment-{$field_name}' class='text-xs font-semibold text-yellow-800'>Catatan Revisi untuk bagian ini:</label>";
        echo "  <textarea id='comment-{$field_name}' name='komentar[{$field_name}]' rows='3' 
                 class='mt-1 block w-full text-sm text-gray-800 bg-yellow-50 rounded-lg border border-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 py-2.5 leading-relaxed resize-none' 
                 placeholder='Tulis catatan revisi di sini...'></textarea>";
        echo "</div>";
    }
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Telaah Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                    <?php elseif ($is_revisi): ?> <span class="font-semibold text-yellow-600">Menunggu Perbaikan Admin</span>
                    <?php elseif ($is_telah_direvisi): ?> <span class="font-semibold text-purple-600">Telah Direvisi</span>
                    <?php elseif ($is_ditolak): ?> <span class="font-semibold text-red-600">Ditolak</span>
                    <?php else: ?> <span class="font-semibold text-gray-600">Menunggu Verifikasi</span>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?php echo htmlspecialchars($back_url); ?>" 
               class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 w-full md:w-auto transform hover:-translate-y-0.5">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <?php if ($is_telah_direvisi && !empty($komentar_revisi)): ?>
        <div class="revision-alert-box bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8 animate-reveal">
            <div class="flex items-center">
                <div class="flex-shrink-0"><i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i></div>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-yellow-800">Catatan Revisi Sebelumnya</h3>
                    <p class="text-sm text-yellow-700 mt-1">Admin telah memperbaiki usulan berdasarkan catatan ini. Harap telaah kembali.</p>
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
                            <?php showCommentIcon('nama_pengusul', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1"><?php echo htmlspecialchars($kegiatan_data['nama_pengusul'] ?? 'N/A'); ?></p>
                        <?php if (!$is_ditolak) render_comment_box('nama_pengusul', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                    <div class="md:col-span-1">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Nama Kegiatan
                            <?php showCommentIcon('nama_kegiatan', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1"><?php echo htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A'); ?></p>
                        <?php if (!$is_ditolak) render_comment_box('nama_kegiatan', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Gambaran Umum
                            <?php showCommentIcon('gambaran_umum', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[50px] leading-relaxed <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['gambaran_umum']) ? 'ring-2 ring-yellow-400' : ''; ?>"><?php echo htmlspecialchars($kegiatan_data['gambaran_umum'] ?? 'N/A'); ?></p>
                        <?php if (!$is_ditolak) render_comment_box('gambaran_umum', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                    <div class="md:col-span-2">
                        <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center">
                            Penerima Manfaat
                            <?php showCommentIcon('penerima_manfaat', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                        </label>
                        <p class="text-sm text-gray-900 p-3 bg-gray-100 rounded-lg border border-gray-200 mt-1 min-h-[50px] leading-relaxed <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['penerima_manfaat']) ? 'ring-2 ring-yellow-400' : ''; ?>"><?php echo htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? 'N/A'); ?></p>
                        <?php if (!$is_ditolak) render_comment_box('penerima_manfaat', $is_menunggu, $is_telah_direvisi); ?>
                    </div>
                </div>
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 200ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU)
                    <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Indikator yang Dipilih:</label>
                <div class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-gray-100 rounded-lg border border-gray-200 <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['iku_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                <?php if (!$is_ditolak) render_comment_box('iku_data', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <div class="mb-8 animate-reveal" style="animation-delay: 300ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    3. Indikator Kinerja KAK
                    <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <div class="overflow-x-auto border border-gray-200 rounded-lg <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['indikator_data']) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                <?php if (!$is_ditolak) render_comment_box('indikator_data', $is_menunggu, $is_telah_direvisi); ?>
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
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-4 mb-2 flex items-center">
                        <?php echo htmlspecialchars($kategori); ?>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                    </h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi[$rab_comment_key]) ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                    <?php if (!$is_ditolak) render_comment_box($rab_comment_key, $is_menunggu, $is_telah_direvisi); ?>
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
                style="<?php echo (($is_menunggu || $is_telah_direvisi) && !$is_ditolak) ? 'opacity: 0; max-height: 0px; overflow: hidden; transform: translateY(-10px); transition: all 0.4s ease-out;' : 'animation-delay: 500ms;'; ?>"
                >
                
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    Kode Mata Anggaran Kegiatan (MAK)
                    <?php showCommentIcon('kode_mak', $komentar_revisi, $is_revisi, $is_telah_direvisi); ?>
                </h3>
                <div class="relative max-w-md">
                    <i class="fas fa-key absolute top-3.5 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                    <input type="text" id="kode_mak" name="kode_mak" 
                           class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer <?php echo ($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['kode_mak']) ? 'border-yellow-500 ring-2 ring-yellow-300' : ''; ?>" 
                           value="<?php echo htmlspecialchars($kode_mak); ?>" placeholder=" " 
                           <?php echo (($is_disetujui || $is_ditolak) && !empty($kode_mak)) ? 'readonly' : ''; ?>>
                    <label for="kode_mak" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Masukkan Kode MAK</label>
                </div>
                 <?php if (($is_revisi || $is_telah_direvisi) && isset($komentar_revisi['kode_mak'])): ?>
                    <p class="text-xs text-yellow-600 mt-1 italic"><?php echo htmlspecialchars($komentar_revisi['kode_mak']); ?></p>
                <?php endif; ?>
                <?php if (!$is_ditolak) render_comment_box('kode_mak', $is_menunggu, $is_telah_direvisi); ?>
            </div>

            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                
                <a href="<?php echo htmlspecialchars($back_url); ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto">
                
                <?php if ($is_menunggu || $is_telah_direvisi): ?>
                    <div id="review-actions" class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto">
                        <button type="button" id="btn-lanjut-mak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-0.5">
                            Lanjut <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                        <button type="button" id="btn-show-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-edit text-xs"></i> Revisi
                        </button>
                        <button type="button" id="btn-tolak" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-red-700 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-times text-xs"></i> Tolak
                        </button>
                    </div>
                    
                    <div id="approval-actions" class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto hidden">
                        <button type="submit" id="btn-setujui-usulan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5">
                             <i class="fas fa-check-double text-xs"></i> Setujui Usulan
                        </button>
                        <button type="button" id="btn-kembali-review" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5">
                             Kembali
                        </button>
                    </div>
                    
                    <div id="comment-actions" class="flex flex-col sm:flex-row-reverse gap-4 w-full sm:w-auto hidden">
                        <button type="submit" id="btn-kirim-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all duration-300 transform hover:-translate-y-0.5">
                             <i class="fas fa-paper-plane text-xs"></i> Kirim Komentar Revisi
                        </button>
                        <button type="button" id="btn-batal-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5">
                             Batal
                        </button>
                    </div>

                <?php else: // (Status 'Disetujui', 'Ditolak', 'Revisi') ?>
                    <button type="button" id="btn-lihat-riwayat" 
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm border border-gray-300 hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-0.5">
                        <i class="fas fa-history text-xs"></i> Lihat Riwayat
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
        const isDisetujui = <?php echo json_encode($is_disetujui); ?>;
        const namaKegiatan = <?php echo json_encode($kegiatan_data['nama_kegiatan'] ?? 'Kegiatan Ini'); ?>;

        // --- 2. Fallback Fungsi formatRupiah ---
        if (typeof formatRupiah !== 'function') {
            window.formatRupiah = (angka) => `RP ${new Intl.NumberFormat('id-ID').format(angka || 0)}`;
        }
        
        // --- 3. Logika Floating Label ---
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
                updateLabel();
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
        // 💡 LOGIKA UI DINAMIS VERIFIKATOR (LENGKAP) 💡
        // ===================================
        
        const btnLanjutMak = document.getElementById('btn-lanjut-mak');
        const btnKembaliReview = document.getElementById('btn-kembali-review');
        const btnShowRevisi = document.getElementById('btn-show-revisi');
        const btnBatalRevisi = document.getElementById('btn-batal-revisi');
        const btnKirimRevisi = document.getElementById('btn-kirim-revisi');
        const btnTolak = document.getElementById('btn-tolak');
        const btnSetujui = document.getElementById('btn-setujui-usulan');
        const btnLihatRiwayat = document.getElementById('btn-lihat-riwayat');
        
        const reviewActions = document.getElementById('review-actions'); 
        const approvalActions = document.getElementById('approval-actions'); 
        const commentActions = document.getElementById('comment-actions'); 
        const makSection = document.getElementById('mak-section');
        const commentBoxes = document.querySelectorAll('.comment-box'); 
        const formVerifikasi = document.getElementById('form-verifikasi');

        function toggleReviewMode(mode) { // 'review', 'approval', 'comment'
            if (reviewActions) reviewActions.classList.toggle('hidden', mode !== 'review');
            if (approvalActions) approvalActions.classList.toggle('hidden', mode !== 'approval');
            if (commentActions) commentActions.classList.toggle('hidden', mode !== 'comment');

            if (makSection) {
                if (mode === 'approval') {
                    makSection.classList.remove('hidden');
                    setTimeout(() => { 
                        makSection.style.opacity = '1';
                        makSection.style.maxHeight = '500px';
                        makSection.style.transform = 'translateY(0)';
                    }, 10);
                    document.getElementById('kode_mak')?.focus();
                } else {
                    if (!isDisetujui) {
                        makSection.style.opacity = '0';
                        makSection.style.maxHeight = '0px';
                        makSection.style.transform = 'translateY(-10px)';
                    }
                }
            }
            
            commentBoxes.forEach(box => {
                box.classList.toggle('hidden', mode !== 'comment');
                if (mode === 'comment') {
                    box.style.opacity = '0';
                    box.style.maxHeight = '0px';
                    setTimeout(() => {
                         box.style.opacity = '1';
                         box.style.maxHeight = '200px';
                    }, 10);
                } else if (box) {
                    box.style.opacity = '0';
                    box.style.maxHeight = '0px';
                }
            });
        }

        // --- Event Listener untuk Lanjut (ke MAK) ---
        btnLanjutMak?.addEventListener('click', () => {
            toggleReviewMode('approval');
        });

        // --- Event Listener untuk Kembali (dari MAK) ---
        btnKembaliReview?.addEventListener('click', () => {
            toggleReviewMode('review');
        });

        // --- Event Listener untuk Revisi (Masuk mode komentar) ---
        btnShowRevisi?.addEventListener('click', () => {
            toggleReviewMode('comment');
        });

        // --- Event Listener untuk Batal Revisi ---
        btnBatalRevisi?.addEventListener('click', () => {
            commentBoxes.forEach(box => {
                const textarea = box.querySelector('textarea');
                if (textarea) textarea.value = '';
            });
            toggleReviewMode('review');
        });
        
        // --- Event Listener untuk Kirim Komentar Revisi (Submit) ---
        btnKirimRevisi?.addEventListener('click', (e) => {
            e.preventDefault();
            
            let hasComment = false;
            commentBoxes.forEach(box => {
                const textarea = box.querySelector('textarea');
                if (textarea && textarea.value.trim() !== '') {
                    hasComment = true;
                }
            });

            if (!hasComment) {
                Swal.fire('Gagal', 'Anda harus mengisi setidaknya satu komentar revisi.', 'error');
                return;
            }

            Swal.fire({
                title: 'Kirim Komentar Revisi?',
                text: "Usulan akan dikembalikan ke pengusul dengan catatan revisi Anda.",
                icon: 'warning',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#F59E0B', // Kuning
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Kirim Revisi',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Mengirim...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    formVerifikasi.submit();
                }
            });
        });

        // --- Event Listener untuk Tombol Tolak ---
        btnTolak?.addEventListener('click', () => {
            Swal.fire({
                title: 'Tolak Usulan Ini?',
                input: 'textarea',
                inputPlaceholder: 'Tuliskan alasan penolakan di sini...',
                inputAttributes: { 'aria-label': 'Tuliskan alasan penolakan' },
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Anda harus memberikan alasan penolakan!'
                    }
                },
                icon: 'error',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#EF4444', // Merah
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Tolak Usulan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Ditolak!', text: 'Usulan telah ditolak.', icon: 'success', customClass: { popup: 'swal-loading' } });
                    // formVerifikasi.submit();
                }
            });
        });

        // --- Event Listener untuk Tombol Setujui Usulan (setelah isi MAK) ---
        btnSetujui?.addEventListener('click', (e) => {
            e.preventDefault(); 
            
            const kodeMakInput = document.getElementById('kode_mak');
            const kodeMak = kodeMakInput ? kodeMakInput.value : '';

            if (!kodeMak || kodeMak.trim() === '') {
                Swal.fire('Error', 'Kode MAK wajib diisi sebelum menyetujui.', 'error');
                kodeMakInput?.focus();
                kodeMakInput?.classList.add('border-red-500', 'ring-2', 'ring-red-300');
                return;
            } else {
                 kodeMakInput?.classList.remove('border-red-500', 'ring-2', 'ring-red-300');
            }
            
            Swal.fire({
                title: 'Setujui Usulan Ini?',
                html: `Usulan akan disetujui dengan Kode MAK:<br><div class="swal-kegiatan-nama">${namaKegiatan}</div>`,
                icon: 'success',
                customClass: { popup: 'swal-konfirmasi' },
                showCancelButton: true,
                confirmButtonColor: '#16A34A', // Hijau
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Setujui!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ title: 'Menyetujui...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                    formVerifikasi.submit();
                }
            });
        });

        // --- Event Listener untuk Tombol Lihat Riwayat ---
         btnLihatRiwayat?.addEventListener('click', () => {
            const riwayatHtml = `
                <ul class="list-disc list-inside text-left text-gray-600 space-y-2">
                    <li><span class="font-semibold text-green-600">[26 Okt 2025]</span> Disetujui oleh <strong>Verifikator</strong>.</li>
                    <li><span class="font-semibold text-yellow-600">[25 Okt 2025]</span> Dikembalikan untuk revisi.</li>
                    <li><span class="font-semibold text-gray-600">[24 Okt 2025]</span> Diajukan oleh <strong>Admin (Putra Yopan)</strong>.</li>
                </ul>
            `;
            Swal.fire({
                title: 'Riwayat Verifikasi',
                html: riwayatHtml,
                icon: 'info',
                customClass: { popup: 'swal-konfirmasi' },
                confirmButtonColor: '#3B82F6',
                confirmButtonText: 'Tutup'
            });
        });
        
    }); // Akhir DOMContentLoaded
</script>