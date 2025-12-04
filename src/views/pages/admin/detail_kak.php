<?php
// File: src/views/pages/admin/detail_kegiatan.php

$status = $status ?? 'Menunggu';
$user_role = $user_role ?? 'admin';

$is_revisi = (strtolower($status) === 'revisi');
$is_disetujui = (strtolower($status) === 'disetujui' || strtolower($status) === 'usulan disetujui');
$is_ditolak = (strtolower($status) === 'ditolak');

$komentar_revisi = $komentar_revisi ?? [];
$komentar_penolakan = $komentar_penolakan ?? '';
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$kode_mak = $kode_mak ?? '';
$back_url = $back_url ?? '/docutrack/public/admin/dashboard'; 

$surat_pengantar = $kegiatan_data['surat_pengantar'] ?? '';
$tanggal_mulai = $kegiatan_data['tanggal_mulai'] ?? '';
$tanggal_selesai = $kegiatan_data['tanggal_selesai'] ?? '';
$surat_pengantar_url = $surat_pengantar_url ?? '';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}

function showCommentIcon($field_name, $komentar_list, $is_revisi_mode) {
    if ($is_revisi_mode && isset($komentar_list[$field_name])) {
        $comment = htmlspecialchars($komentar_list[$field_name]);
        echo "<span class='comment-icon-wrapper relative inline-flex items-center ml-2 group'>";
        echo "<span class='comment-icon flex items-center justify-center w-7 h-7 rounded-full bg-yellow-100 border-2 border-yellow-400 cursor-pointer transition-all duration-300 hover:bg-yellow-200 hover:scale-110 hover:shadow-lg hover:shadow-yellow-200'>";
        echo "<i class='fas fa-comment-dots text-yellow-600 text-sm group-hover:animate-pulse'></i>";
        echo "</span>";
        echo "<span class='comment-tooltip invisible group-hover:visible opacity-0 group-hover:opacity-100 absolute left-1/2 -translate-x-1/2 bottom-full mb-3 w-64 p-3 bg-gray-900 text-white text-xs rounded-lg shadow-xl transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50'>";
        echo "<span class='flex items-center gap-2 text-yellow-400 font-semibold mb-1'><i class='fas fa-exclamation-circle'></i> Catatan Revisi</span>";
        echo "<span class='block text-gray-200 leading-relaxed'>{$comment}</span>";
        echo "<span class='absolute left-1/2 -translate-x-1/2 top-full w-0 h-0 border-l-8 border-r-8 border-t-8 border-l-transparent border-r-transparent border-t-gray-900'></span>";
        echo "</span>";
        echo "</span>";
    }
}

function isEditable($field_name, $is_revisi_mode, $komentar_list) {
    return $is_revisi_mode && isset($komentar_list[$field_name]);
}
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section class="bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Detail Usulan Kegiatan</h2>
                <p class="text-sm text-gray-500 mt-1">Status:
                    <?php if ($is_disetujui): ?> <span class="font-semibold text-green-600">Disetujui</span>
                    <?php elseif ($is_revisi): ?> <span class="font-semibold text-yellow-600">Perlu Revisi</span>
                    <?php elseif ($is_ditolak): ?> <span class="font-semibold text-red-600">Ditolak</span>
                    <?php else: ?> <span class="font-semibold text-gray-600"><?= htmlspecialchars($status); ?></span>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <!-- Alert Revisi -->
        <?php if ($is_revisi && !empty($komentar_revisi)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-yellow-800">Perlu Revisi</h3>
                    <p class="text-sm text-yellow-700 mt-1">Harap perbaiki bagian yang ditandai:</p>
                </div>
            </div>
            <ul class="list-disc list-inside mt-4 pl-10 space-y-1 text-sm text-yellow-700">
                <?php foreach ($komentar_revisi as $field => $komentar): ?>
                    <li><span class="font-semibold"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</span> <?= htmlspecialchars($komentar); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <!-- Alert Ditolak -->
        <?php if ($is_ditolak): ?>
        <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg mb-8">
            <div class="flex items-center">
                <i class="fas fa-times-circle text-red-500 text-2xl"></i>
                <div class="ml-4">
                    <h3 class="text-lg font-bold text-red-800">Usulan Ditolak</h3>
                    <p class="text-sm text-red-700 mt-1">Alasan: "<?= htmlspecialchars($komentar_penolakan); ?>"</p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form id="form-verifikasi" action="#" method="POST" enctype="multipart/form-data">
            
            <div class="bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100 mb-6">
        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 pb-3 border-b border-gray-100">
            Kerangka Acuan Kerja (KAK)
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Pengusul</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm">
                    <?= htmlspecialchars($kegiatan_data['nama_pengusul'] ?? '-') ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NIM Pengusul</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm">
                    <?= htmlspecialchars($kegiatan_data['nim_pengusul'] ?? '-') ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Penanggung Jawab</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm">
                    <?= htmlspecialchars($kegiatan_data['nama_penanggung_jawab'] ?? '-') ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">NIM/NIP Penanggung Jawab</label>
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium text-sm">
                    <?= htmlspecialchars($kegiatan_data['nip_penanggung_jawab'] ?? '-') ?>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Nama Kegiatan</label>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-800 font-medium">
                <?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '-') ?>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Gambaran Umum</label>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[100px]">
                <?= nl2br(htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '-')) ?>
            </div>
        </div>

        <div class="mb-8">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Penerima Manfaat</label>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed">
                <?= nl2br(htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '-')) ?>
            </div>
        </div>

        <h4 class="text-lg font-bold text-gray-800 mb-5 pb-2 border-b border-gray-100">Strategi Pencapaian Keluaran</h4>
        
        <div class="mb-6">
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Pelaksanaan</label>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[80px]">
                <?= nl2br(htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '-')) ?>
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tahapan Kegiatan</label>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200 text-gray-700 text-sm leading-relaxed min-h-[100px]">
                <?= nl2br(htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '-')) ?>
            </div>
        </div>
    </div>

            <!-- 2. IKU -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    2. Indikator Kinerja Utama (IKU) <?php showCommentIcon('iku_data', $komentar_revisi, $is_revisi); ?>
                </h3>
                <div class="flex flex-wrap gap-2 p-3 min-h-[60px] bg-gray-100 rounded-lg border border-gray-200">
                    <?php if (!empty($iku_data)): ?>
                        <?php foreach ($iku_data as $iku_item): ?>
                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"><?= htmlspecialchars($iku_item); ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-sm text-gray-500 italic">Tidak ada IKU yang dipilih.</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 3. Indikator Kinerja -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200 flex items-center">
                    3. Indikator Kinerja KAK <?php showCommentIcon('indikator_data', $komentar_revisi, $is_revisi); ?>
                </h3>
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
                                <?php foreach ($indikator_data as $item): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['bulan'] ?? 'N/A'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['target'] ?? '0'); ?>%</td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="px-4 py-3 text-sm text-gray-500 italic text-center">Tidak ada indikator.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. RAB -->
            <div class="mb-8">
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
                        <?= htmlspecialchars($kategori); ?>
                        <?php showCommentIcon($rab_comment_key, $komentar_revisi, $is_revisi); ?>
                    </h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg <?= $has_rab_comment ? 'ring-2 ring-yellow-400' : ''; ?>">
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
                                <tr class="<?= $has_rab_comment ? 'bg-yellow-50' : 'hover:bg-gray-50'; ?>">
                                    <?php if ($has_rab_comment): ?>
                                        <td class="px-4 py-3"><input type="text" name="rab[<?= $kategori; ?>][uraian][]" class="w-full text-sm p-2 border border-gray-300 rounded-md" value="<?= htmlspecialchars($item['uraian'] ?? ''); ?>"></td>
                                        <td class="px-4 py-3"><input type="text" name="rab[<?= $kategori; ?>][rincian][]" class="w-full text-sm p-2 border border-gray-300 rounded-md" value="<?= htmlspecialchars($item['rincian'] ?? ''); ?>"></td>
                                        <td class="px-3 py-3"><input type="number" name="rab[<?= $kategori; ?>][vol1][]" class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md" value="<?= $vol1; ?>"></td>
                                        <td class="px-3 py-3"><input type="text" name="rab[<?= $kategori; ?>][sat1][]" class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md" value="<?= htmlspecialchars($sat1); ?>"></td>
                                        <td class="px-3 py-3"><input type="number" name="rab[<?= $kategori; ?>][vol2][]" class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md" value="<?= $vol2; ?>"></td>
                                        <td class="px-3 py-3"><input type="text" name="rab[<?= $kategori; ?>][sat2][]" class="w-16 text-sm p-2 text-center border border-gray-300 rounded-md" value="<?= htmlspecialchars($sat2); ?>"></td>
                                        <td class="px-4 py-3"><input type="number" name="rab[<?= $kategori; ?>][harga][]" class="w-28 text-sm p-2 text-right border border-gray-300 rounded-md" value="<?= $harga; ?>"></td>
                                    <?php else: ?>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['uraian'] ?? ''); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($item['rincian'] ?? ''); ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol1; ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat1); ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol2; ?></td>
                                        <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat2); ?></td>
                                        <td class="px-4 py-3 text-sm text-gray-700 text-right"><?= number_format($harga, 0, ',', '.'); ?></td>
                                    <?php endif; ?>
                                    <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right"><?= formatRupiah($total_item); ?></td>
                                </tr>
                                <?php endforeach; $grand_total_rab += $subtotal; ?>
                                <tr class="bg-gray-50 font-semibold">
                                    <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal</td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= formatRupiah($subtotal); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($has_rab_comment): ?>
                        <p class="text-xs text-yellow-600 mt-1 italic"><?= htmlspecialchars($komentar_revisi[$rab_comment_key]); ?></p>
                    <?php endif; ?>
                <?php endforeach; else: ?>
                    <p class="text-sm text-gray-500 italic">Tidak ada data RAB.</p>
                <?php endif; ?>
                
                <div class="flex justify-end mt-6">
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                        <span class="text-sm font-medium text-gray-700">Grand Total RAB: </span>
                        <span class="text-xl font-bold text-blue-600"><?= formatRupiah($grand_total_rab); ?></span>
                    </div>
                </div>
            </div>

            <!-- 5. Rincian Rancangan Kegiatan (Hanya Disetujui - Readonly) -->
            <?php if ($is_disetujui): ?>
            <div class="mb-8">
                <h3 class="text-xl font-bold text-gray-800 pb-3 mb-6 border-b border-gray-200">Rincian Rancangan Kegiatan</h3>
                
                <!-- Surat Pengantar -->
                <div class="mb-6">
                    <label class="text-sm font-semibold text-gray-700 mb-2 block">Surat Pengantar</label>
                    <div class="relative max-w-sm">
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <span class="text-sm text-gray-800">
                                <?= !empty($surat_pengantar) ? htmlspecialchars($surat_pengantar) : '-'; ?>
                            </span>
                            <?php if (!empty($surat_pengantar)): ?>
                                <a href="<?= htmlspecialchars($surat_pengantar_url); ?>" target="_blank" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-download"></i>
                                </a>
                            <?php else: ?>
                                <i class="fas fa-file-alt text-gray-400"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Kurun Waktu Pelaksanaan -->
                <div class="mb-4">
                    <label class="text-sm font-semibold text-gray-700 mb-3 block">Kurun Waktu Pelaksanaan</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg">
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Mulai</span>
                                <span class="text-sm text-gray-800 font-medium">
                                    <?= !empty($tanggal_mulai) ? date('d M Y', strtotime($tanggal_mulai)) : '-'; ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3.5 bg-gray-100 rounded-lg border border-gray-200">
                            <div>
                                <span class="text-xs text-gray-500 block">Tanggal Selesai</span>
                                <span class="text-sm text-gray-800 font-medium">
                                    <?= !empty($tanggal_selesai) ? date('d M Y', strtotime($tanggal_selesai)) : '-'; ?>
                                </span>
                            </div>
                            <i class="fas fa-calendar-alt text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- 6. Kode MAK (Hanya Disetujui) -->
            <?php if ($is_disetujui): ?>
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Kode Mata Anggaran Kegiatan (MAK)</h3>
                <div class="relative max-w-md">
                    <i class="fas fa-key absolute top-3.5 left-3 text-gray-400"></i>
                    <input type="text" id="kode_mak" name="kode_mak" 
                           class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-gray-100 rounded-lg border border-gray-200" 
                           value="<?= htmlspecialchars($kode_mak); ?>" readonly>
                </div>
            </div>
            <?php endif; ?>

            <!-- Footer Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?= htmlspecialchars($back_url); ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-sm hover:bg-gray-200 transition-all">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                </a>
                 
                <div class="flex gap-4 w-full sm:w-auto">
                    <?php if ($is_revisi): ?>
                        <button type="submit" id="btn-simpan-revisi" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-yellow-600 transition-all">
                            <i class="fas fa-save text-xs"></i> Simpan Revisi
                        </button>
                    <?php elseif ($is_disetujui): ?>
                        <button type="button" id="print-pdf-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-red-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-red-700 transition-all">
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
    const formVerifikasi = document.getElementById('form-verifikasi');
    
    // Print PDF
    document.getElementById('print-pdf-btn')?.addEventListener('click', (e) => {
        e.preventDefault(); 
        window.print(); 
    });

    // Simpan Revisi
    document.getElementById('btn-simpan-revisi')?.addEventListener('click', (e) => {
        e.preventDefault();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Simpan Perubahan?',
                text: "Perubahan akan disimpan dan dikirim untuk verifikasi.",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#F59E0B',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({ 
                        title: 'Menyimpan...', 
                        allowOutsideClick: false, 
                        didOpen: () => Swal.showLoading() 
                    });
                    formVerifikasi.submit();
                }
            });
        } else {
            if (confirm('Simpan perubahan?')) {
                formVerifikasi.submit();
            }
        }
    });
});
</script>