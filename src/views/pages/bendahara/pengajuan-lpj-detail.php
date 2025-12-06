<?php
// File: src/views/pages/bendahara/pengajuan-lpj-detail.php

$status = $status ?? 'Menunggu';
$status_lower = strtolower($status);

// Cek status untuk menentukan tampilan
$is_menunggu = ($status_lower === 'menunggu');
$is_telah_direvisi = ($status_lower === 'telah direvisi');
$is_revisi = ($status_lower === 'revisi');
$is_disetujui = ($status_lower === 'disetujui');

$kegiatan_data = $kegiatan_data ?? [];
$rab_items = $rab_items ?? [];
$back_url = $back_url ?? '/docutrack/public/bendahara/pengajuan-lpj';

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka) { return "Rp " . number_format($angka ?? 0, 0, ',', '.'); }
}

/**
 * Helper render comment box RAB
 */
if (!function_exists('render_comment_box_rab_lpj')) {
    function render_comment_box_rab_lpj($field_name, $is_menunggu_status, $is_telah_direvisi_status) {
        if ($is_menunggu_status || $is_telah_direvisi_status) {
            ?>
            <div id="comment-box-<?= htmlspecialchars($field_name) ?>"
                 class="comment-box mt-2 animate-reveal">
                <label for="comment-<?= htmlspecialchars($field_name) ?>"
                       class="text-xs font-semibold text-yellow-800">
                    Catatan Revisi untuk bagian ini:
                </label>
                <textarea id="comment-<?= htmlspecialchars($field_name) ?>"
                          name="komentar[<?= htmlspecialchars($field_name) ?>]"
                          rows="3"
                          class="mt-1 block w-full text-sm text-gray-800 bg-yellow-50 rounded-lg border border-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-500 py-2.5 leading-relaxed resize-none"
                          placeholder="Tulis catatan revisi di sini..."><?= htmlspecialchars($_POST['komentar'][$field_name] ?? '') ?></textarea>
            </div>
            <?php
        }
    }
}

/**
 * Helper Show Comment Icon (Pastikan fungsi ini ada atau didefinisikan)
 * Jika di file asli tidak ada function ini di scope global, kita buat dummy-nya agar tidak error
 */
if (!function_exists('showCommentIcon')) {
    function showCommentIcon($itemId, $komentar, $isRevisi, $isTelahDirevisi) {
        if (!empty($komentar)) {
            echo '<span class="ml-2 text-yellow-600 cursor-help" title="'.htmlspecialchars($komentar).'"><i class="fas fa-comment-dots"></i></span>';
        }
    }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <?php if (isset($_SESSION['flash_message'])): ?>
    <div class="mb-6 p-4 rounded-lg <?= ($_SESSION['flash_type'] ?? 'success') === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' ?>">
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
                <h2 class="text-2xl md:text-3xl font-bold text-gray-800">Verifikasi LPJ</h2>
                <p class="text-sm text-gray-500 mt-1">Kegiatan: <strong><?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? 'N/A') ?></strong></p>
                <p class="text-xs text-gray-500 mt-0.5">Pengusul: <?= htmlspecialchars($kegiatan_data['nama_mahasiswa'] ?? 'N/A') ?> (<?= htmlspecialchars($kegiatan_data['nim'] ?? 'N/A') ?>)</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <?php if ($is_disetujui): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-700 border border-green-300">
                        <i class="fas fa-check-double"></i> Telah Disetujui
                    </span>
                <?php elseif ($is_revisi): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800 border border-yellow-300">
                        <i class="fas fa-clock"></i> Menunggu Revisi dari Admin
                    </span>
                <?php elseif ($is_telah_direvisi): ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-blue-100 text-blue-700 border border-blue-300">
                        <i class="fas fa-edit"></i> Telah Direvisi - Perlu Dicek Ulang
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-semibold bg-orange-100 text-orange-700 border border-orange-300">
                        <i class="fas fa-hourglass-half"></i> Menunggu Verifikasi
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($is_revisi): ?>
            <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-500 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-yellow-600 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-yellow-800 mb-1">Status: Menunggu Revisi</h4>
                        <p class="text-sm text-yellow-700">Admin sedang memperbaiki item yang Anda minta untuk direvisi. Halaman ini dalam mode <strong>view only</strong>.</p>
                    </div>
                </div>
            </div>
        <?php elseif ($is_telah_direvisi): ?>
            <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-circle text-blue-600 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-800 mb-1">Status: Telah Direvisi</h4>
                        <p class="text-sm text-blue-700">Admin telah melakukan perbaikan. Silakan cek ulang dan putuskan untuk <strong>menyetujui</strong> atau <strong>meminta revisi kembali</strong>.</p>
                    </div>
                </div>
            </div>
        <?php elseif ($is_disetujui): ?>
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-check-double text-green-600 text-xl mt-0.5"></i>
                    <div>
                        <h4 class="text-sm font-semibold text-green-800 mb-1">LPJ Telah Disetujui</h4>
                        <p class="text-sm text-green-700">LPJ ini telah Anda setujui pada: <strong><?= date('d F Y, H:i', strtotime($tanggal_persetujuan ?? 'now')) ?> WIB</strong></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form id="form-lpj-verifikasi" method="POST" action="/docutrack/public/bendahara/pengajuan-lpj/proses">
            <input type="hidden" name="lpj_id" value="<?= $kegiatan_data['id'] ?? '' ?>">
            <input type="hidden" name="action" id="form-action" value="">

            <div class="mb-8 animate-reveal" style="animation-delay: 100ms;">
                <h3 class="text-xl font-bold text-gray-700 pb-3 mb-4 border-b border-gray-200">Realisasi Anggaran Biaya (LPJ Items)</h3>
                
                <?php 
                    $grand_total_realisasi = 0;
                    if (!empty($rab_items)):
                        foreach ($rab_items as $kategori => $items): 
                            if (empty($items)) continue;
                            $subtotal_kategori = 0;
                            // key komentar per-kategori
                            $rab_comment_key = 'rab_' . strtolower(str_replace(' ', '_', $kategori));
                ?>
                    <h4 class="text-md font-semibold text-gray-700 mt-6 mb-3"><?= htmlspecialchars($kategori) ?></h4>
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full min-w-[1200px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase" style="width: 200px;">Uraian</th>
                                    <th class="px-3 py-3 text-left text-xs font-bold text-gray-600 uppercase" style="width: 180px;">Rincian</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 80px;">Vol 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 90px;">Sat 1</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 80px;">Vol 2</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 90px;">Sat 2</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 uppercase" style="width: 130px;">Harga (Rp)</th>
                                    <th class="px-3 py-3 text-right text-xs font-bold text-gray-600 uppercase" style="width: 150px;">Total</th>
                                    <th class="px-3 py-3 text-center text-xs font-bold text-gray-600 uppercase" style="width: 100px;">Bukti</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php foreach ($items as $item): 
                                    $item_id = $item['id'] ?? uniqid();
                                    $uraian = $item['uraian'] ?? '-';
                                    $rincian = $item['rincian'] ?? '-';
                                    $vol1 = $item['vol1'] ?? 0;
                                    $sat1 = $item['sat1'] ?? '-';
                                    $vol2 = $item['vol2'] ?? 1;
                                    $sat2 = $item['sat2'] ?? '-';
                                    
                                    // PERBAIKAN: Ambil variabel yang benar dari array $item
                                    $harga_satuan = $item['harga_satuan'] ?? 0; 
                                    $total_sub = $item['subtotal'] ?? 0; // Menggunakan key 'subtotal'
                                    
                                    $bukti_file = $item['bukti_file'] ?? null;
                                    $komentar_existing = $item['komentar'] ?? null;
                                    
                                    $has_existing_comment = !empty($komentar_existing);
                                    $row_class = $has_existing_comment ? 'bg-yellow-50' : '';

                                    $subtotal_kategori += $total_sub;
                                ?>
                                <tr class="<?= $row_class ?>">
                                    <td class="px-3 py-3 text-sm text-gray-800 font-medium">
                                        <div class="flex items-center gap-1">
                                            <span><?= htmlspecialchars($uraian) ?></span>
                                            <?php showCommentIcon($item_id, $komentar_existing, $is_revisi, $is_telah_direvisi); ?>
                                        </div>
                                    </td>
                                    
                                    <td class="px-4 py-3 text-sm text-gray-700"><?= htmlspecialchars($rincian) ?></td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol1 ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat1) ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= $vol2 ?></td>
                                    <td class="px-3 py-3 text-sm text-gray-700 text-center"><?= htmlspecialchars($sat2) ?></td>
                                    
                                    <td class="px-3 py-3 text-sm text-gray-700 text-right">
                                        <?= number_format($harga_satuan, 0, ',', '.') ?>
                                    </td>
                                    
                                    <td class="px-4 py-3 text-sm text-blue-600 font-semibold text-right">
                                        <?= formatRupiah($total_sub) ?>
                                    </td>
                                    
                                    <td class="px-3 py-3 text-center">
                                        <?php if ($bukti_file): ?>
                                            <a href="/docutrack/public/uploads/lpj_bukti/<?= htmlspecialchars($bukti_file) ?>" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-blue-600 hover:text-blue-800 text-xs font-medium hover:underline">
                                                <i class="fas fa-file-pdf"></i>
                                                <span>Lihat</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-gray-400 italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; $grand_total_realisasi += $subtotal_kategori; ?>
                                
                                <tr class="bg-gray-100 font-semibold">
                                    <td colspan="7" class="px-4 py-3 text-right text-sm text-gray-800">Subtotal <?= htmlspecialchars($kategori) ?></td>
                                    <td class="px-4 py-3 text-sm text-gray-900 text-right"><?= formatRupiah($subtotal_kategori) ?></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <?php
                        // Comment box per kategori
                        if (!$is_revisi && !$is_disetujui) {
                            render_comment_box_rab_lpj($rab_comment_key, $is_menunggu, $is_telah_direvisi);
                        }
                    ?>

                <?php 
                        endforeach; 
                    else:
                ?>
                    <p class="text-sm text-gray-500 italic p-4 text-center bg-gray-50 rounded-lg">Tidak ada data item LPJ untuk ditampilkan.</p>
                <?php endif; ?>
                
                <div class="flex justify-end mt-6">
                    <div class="grid grid-cols-[auto_1fr] gap-x-6 gap-y-2 p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl border border-blue-200 w-full md:w-auto min-w-[350px]">
                        <span class="text-lg font-semibold text-gray-800">Grand Total Realisasi:</span>
                        <span class="text-2xl font-bold text-blue-600 text-right"><?= formatRupiah($grand_total_realisasi) ?></span>
                    </div>
                </div>
            </div>

            <?php if (!$is_revisi && !$is_disetujui): ?>
            <div class="mb-8 pt-6 border-t border-gray-200">
                <label class="text-sm font-semibold text-gray-700 mb-2 block">Catatan Umum (Opsional)</label>
                <textarea name="catatan_umum" 
                          rows="3" 
                          placeholder="Tambahkan catatan umum untuk LPJ ini jika diperlukan..."
                          class="w-full text-sm p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
            </div>
            <?php endif; ?>

            <div class="flex flex-col sm:flex-row-reverse justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                
                <?php if ($is_disetujui): ?>
                    <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-6 py-3 rounded-lg shadow-md opacity-70 cursor-not-allowed">
                        <i class="fas fa-check-double"></i> LPJ Telah Disetujui
                    </div>
                    
                <?php elseif ($is_revisi): ?>
                    <div class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-6 py-3 rounded-lg shadow-md opacity-70 cursor-not-allowed">
                        <i class="fas fa-clock"></i> Menunggu Revisi dari Admin
                    </div>
                    
                <?php else: ?>
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button type="button" 
                                onclick="konfirmasiRevisi()"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-yellow-500 text-white font-semibold px-5 py-3 rounded-lg shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-300 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-edit"></i> Minta Revisi
                        </button>

                        <button type="button" 
                                onclick="konfirmasiSetuju()"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-green-600 text-white font-semibold px-5 py-3 rounded-lg shadow-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition-all duration-300 transform hover:-translate-y-0.5">
                            <i class="fas fa-check-circle"></i> Setujui LPJ
                        </button>
                    </div>
                <?php endif; ?>
                
                <a href="<?= htmlspecialchars($back_url) ?>" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

        </form>
        
    </section>
</main>

<script>
function konfirmasiSetuju() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Setujui LPJ?',
            html: '<span class="text-green-600 font-bold">✓ KONFIRMASI PERSETUJUAN</span><br><br>Apakah Anda yakin akan <strong>menyetujui</strong> LPJ ini?<br><small class="text-gray-600">Tindakan ini tidak dapat dibatalkan.</small>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16A34A',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'Memproses...', 
                    allowOutsideClick: false, 
                    didOpen: () => Swal.showLoading() 
                });
                
                document.getElementById('form-action').value = 'setuju';
                document.getElementById('form-lpj-verifikasi').submit();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin akan menyetujui LPJ ini?\n\nTindakan ini tidak dapat dibatalkan.')) {
            document.getElementById('form-action').value = 'setuju';
            document.getElementById('form-lpj-verifikasi').submit();
        }
    }
}

function konfirmasiRevisi() {
    const form = document.getElementById('form-lpj-verifikasi');
    // Ambil semua textarea komentar
    const komentarInputs = form.querySelectorAll('.comment-box textarea[name^="komentar"]');
    
    let hasComment = false;
    komentarInputs.forEach(input => {
        if (input.value.trim()) {
            hasComment = true;
        }
    });
    
    // Juga cek jika ada catatan umum
    const catatanUmum = form.querySelector('textarea[name="catatan_umum"]');
    if (catatanUmum && catatanUmum.value.trim()) {
        hasComment = true;
    }
    
    if (!hasComment) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Komentar Diperlukan',
                text: 'Mohon isi komentar pada bagian item atau catatan umum untuk meminta revisi!',
                confirmButtonColor: '#3B82F6'
            });
        } else {
            alert('Mohon isi komentar pada bagian item atau catatan umum untuk meminta revisi!');
        }
        return;
    }
    
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Minta Revisi?',
            html: '<span class="text-yellow-600 font-bold">⚠️ PERMINTAAN REVISI</span><br><br>Apakah Anda yakin akan meminta <strong>revisi</strong> untuk LPJ ini?<br><small class="text-gray-600">Admin akan menerima notifikasi untuk memperbaiki.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EAB308',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, Minta Revisi!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'Memproses...', 
                    allowOutsideClick: false, 
                    didOpen: () => Swal.showLoading() 
                });
                
                document.getElementById('form-action').value = 'revisi';
                form.submit();
            }
        });
    } else {
        if (confirm('Apakah Anda yakin akan meminta revisi untuk LPJ ini?')) {
            document.getElementById('form-action').value = 'revisi';
            form.submit();
        }
    }
}
</script>

<style>
    /* CSS UNTUK TOOLTIP KOMENTAR */
    .comment-icon-wrapper { display: inline-flex; }
    .comment-icon { cursor: pointer; }
    
    /* Animasi Reveal */
    .animate-reveal {
        animation: reveal 0.5s ease-out forwards;
        opacity: 0;
        transform: translateY(10px);
    }
    @keyframes reveal {
        to { opacity: 1; transform: translateY(0); }
    }
</style>