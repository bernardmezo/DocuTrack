<?php
// File: src/views/pages/admin/edit_usulan.php
$kegiatan_data = $kegiatan_data ?? [];
$iku_data = $iku_data ?? [];
$indikator_data = $indikator_data ?? [];
$rab_data = $rab_data ?? [];
$komentar_revisi = $komentar_revisi ?? [];
$tahapan_array = $tahapan_array ?? [];
$back_url = $back_url ?? '/docutrack/public/admin/dashboard';
$kegiatanId = $kegiatan_data['kegiatanId'] ?? 0;

$success_msg = $_SESSION['flash_message'] ?? null;
$error_msg = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_error']);

function hasComment($field_name, $komentar_list) {
    return isset($komentar_list[$field_name]);
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <?php if ($success_msg): ?>
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium"><?= htmlspecialchars($success_msg) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($error_msg): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium"><?= htmlspecialchars($error_msg) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <section class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden">
        
        <div class="mb-6 pb-4 border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Edit Usulan Kegiatan</h2>
            <p class="text-sm text-gray-600">Perbaiki bagian yang diminta untuk direvisi</p>
        </div>

        <?php if (!empty($komentar_revisi)): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg mb-8">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mt-0.5 flex-shrink-0"></i>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-bold text-yellow-800 mb-2">Catatan Revisi dari Verifikator</h3>
                    <ul class="list-disc list-inside space-y-2 text-sm text-yellow-700">
                        <?php foreach ($komentar_revisi as $field => $komentar): ?>
                            <li><span class="font-semibold"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</span> <?= htmlspecialchars($komentar); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <form id="form-edit-usulan" action="/docutrack/public/admin/detail-kak/<?= $kegiatanId ?>/resubmit-usulan" method="POST">
            
            <!-- Data Kegiatan -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Data Kegiatan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Kegiatan -->
                    <div class="md:col-span-2 <?= hasComment('nama_kegiatan', $komentar_revisi) ? 'ring-2 ring-yellow-300 rounded-lg p-4 bg-yellow-50' : '' ?>">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Kegiatan
                            <?php if (hasComment('nama_kegiatan', $komentar_revisi)): ?>
                                <span class="text-yellow-600 text-xs ml-2"><i class="fas fa-star-of-life"></i> Perlu Direvisi</span>
                            <?php endif; ?>
                        </label>
                        <input type="text" name="nama_kegiatan" value="<?= htmlspecialchars($kegiatan_data['nama_kegiatan'] ?? '') ?>" 
                            class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>

                    <!-- Gambaran Umum -->
                    <div class="md:col-span-2 <?= hasComment('gambaran_umum', $komentar_revisi) ? 'ring-2 ring-yellow-300 rounded-lg p-4 bg-yellow-50' : '' ?>">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Gambaran Umum
                            <?php if (hasComment('gambaran_umum', $komentar_revisi)): ?>
                                <span class="text-yellow-600 text-xs ml-2"><i class="fas fa-star-of-life"></i> Perlu Direvisi</span>
                            <?php endif; ?>
                        </label>
                        <textarea name="gambaran_umum" rows="4" 
                            class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required><?= htmlspecialchars($kegiatan_data['gambaran_umum'] ?? '') ?></textarea>
                    </div>

                    <!-- Penerima Manfaat -->
                    <div class="md:col-span-2 <?= hasComment('penerima_manfaat', $komentar_revisi) ? 'ring-2 ring-yellow-300 rounded-lg p-4 bg-yellow-50' : '' ?>">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Penerima Manfaat
                            <?php if (hasComment('penerima_manfaat', $komentar_revisi)): ?>
                                <span class="text-yellow-600 text-xs ml-2"><i class="fas fa-star-of-life"></i> Perlu Direvisi</span>
                            <?php endif; ?>
                        </label>
                        <textarea name="penerima_manfaat" rows="3" 
                            class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required><?= htmlspecialchars($kegiatan_data['penerima_manfaat'] ?? '') ?></textarea>
                    </div>

                    <!-- Metode Pelaksanaan -->
                    <div class="md:col-span-2 <?= hasComment('metode_pelaksanaan', $komentar_revisi) ? 'ring-2 ring-yellow-300 rounded-lg p-4 bg-yellow-50' : '' ?>">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Metode Pelaksanaan
                            <?php if (hasComment('metode_pelaksanaan', $komentar_revisi)): ?>
                                <span class="text-yellow-600 text-xs ml-2"><i class="fas fa-star-of-life"></i> Perlu Direvisi</span>
                            <?php endif; ?>
                        </label>
                        <textarea name="metode_pelaksanaan" rows="3" 
                            class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required><?= htmlspecialchars($kegiatan_data['metode_pelaksanaan'] ?? '') ?></textarea>
                    </div>

                    <!-- Tahapan Kegiatan -->
                    <div class="md:col-span-2 <?= hasComment('tahapan_kegiatan', $komentar_revisi) ? 'ring-2 ring-yellow-300 rounded-lg p-4 bg-yellow-50' : '' ?>">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Tahapan Kegiatan
                            <?php if (hasComment('tahapan_kegiatan', $komentar_revisi)): ?>
                                <span class="text-yellow-600 text-xs ml-2"><i class="fas fa-star-of-life"></i> Perlu Direvisi</span>
                            <?php endif; ?>
                        </label>
                        <textarea name="tahapan_kegiatan" rows="4" 
                            class="w-full px-4 py-3 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Masukkan tahapan kegiatan, satu per baris" required><?= htmlspecialchars($kegiatan_data['tahapan_kegiatan'] ?? '') ?></textarea>
                        <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle"></i> Pisahkan setiap tahapan dengan baris baru</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Tambahan -->
            <div class="mb-8">
                <h3 class="text-lg font-bold text-gray-700 mb-4 pb-2 border-b border-gray-200">
                    <i class="fas fa-clipboard-list text-blue-600 mr-2"></i>Informasi Tambahan
                </h3>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 mb-3"><strong>Catatan:</strong></p>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-600">
                        <li>Data IKU, Indikator Kinerja, dan RAB tidak dapat diedit melalui form ini</li>
                        <li>Jika perlu mengubah data tersebut, silakan hubungi administrator</li>
                        <li>Pastikan semua field yang ditandai diisi dengan benar</li>
                        <li>Setelah submit, usulan akan dikirim kembali ke Verifikator untuk ditinjau ulang</li>
                    </ul>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                <a href="<?= htmlspecialchars($back_url) ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-6 py-3 rounded-lg shadow-sm hover:bg-gray-200 transition-all duration-300">
                    <i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span>
                </a>
                <button type="submit" id="btn-resubmit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold px-6 py-3 rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-paper-plane"></i> <span>Kirim Ulang Usulan</span>
                </button>
            </div>
        </form>
    </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-edit-usulan');
    const btnResubmit = document.getElementById('btn-resubmit');

    form.addEventListener('submit', (e) => {
        e.preventDefault();

        Swal.fire({
            title: 'Kirim Ulang Usulan?',
            html: 'Usulan yang telah direvisi akan dikirim kembali ke Verifikator untuk ditinjau ulang.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563EB',
            cancelButtonColor: '#6B7280',
            confirmButtonText: '<i class="fas fa-check mr-2"></i> Ya, Kirim!',
            cancelButtonText: '<i class="fas fa-times mr-2"></i> Batal',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'rounded-lg px-5 py-2.5',
                cancelButton: 'rounded-lg px-5 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ 
                    title: 'Mengirim...', 
                    html: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => Swal.showLoading() 
                });
                form.submit();
            }
        });
    });
});
</script>

<style>
/* Smooth animations */
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

.animate-reveal {
    animation: fadeInUp 0.3s ease-out forwards;
}

/* Better touch targets on mobile */
@media (max-width: 768px) {
    button, a {
        min-height: 44px;
    }
}
</style>
