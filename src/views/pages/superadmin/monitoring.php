<?php
// src/views/pages/superadmin/monitoring.php

// Ensure data is available
$list_kegiatan = $list_kegiatan ?? [];
$list_lpj = $list_lpj ?? [];

// Helper function for badges
function getStatusBadge($status) {
    $status = strtolower($status);
    $classes = "px-2.5 py-0.5 rounded-full text-xs font-medium border ";
    
    switch ($status) {
        case 'disetujui':
        case 'approved':
            return $classes . "bg-green-50 text-green-700 border-green-200";
        case 'ditolak':
        case 'rejected':
            return $classes . "bg-red-50 text-red-700 border-red-200";
        case 'revisi':
            return $classes . "bg-yellow-50 text-yellow-700 border-yellow-200";
        case 'menunggu':
        case 'menunggu verifikasi':
        case 'in process':
            return $classes . "bg-blue-50 text-blue-700 border-blue-200";
        case 'draft':
            return $classes . "bg-gray-50 text-gray-600 border-gray-200";
        default:
            return $classes . "bg-gray-50 text-gray-600 border-gray-200";
    }
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full space-y-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">
                Monitoring Global
            </h2>
            <p class="text-sm text-gray-500">Pantau seluruh pengajuan kegiatan dan laporan pertanggungjawaban di satu tempat.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.location.reload()" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-50 hover:text-blue-600 transition-all shadow-sm">
                <i class="fas fa-sync-alt mr-2"></i>Refresh Data
            </button>
        </div>
    </div>

    <!-- Section 1: Monitoring Pengajuan Kegiatan -->
    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gradient-to-r from-blue-50/50 to-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <i class="fas fa-file-alt text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Daftar Pengajuan Kegiatan</h3>
                    <p class="text-xs text-gray-500">Semua usulan kegiatan masuk</p>
                </div>
            </div>
            
            <div class="relative">
                <input type="text" id="search-kegiatan" placeholder="Cari kegiatan..." 
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 w-full sm:w-64 transition-all">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 rounded-tl-lg">Tanggal</th>
                        <th class="px-6 py-4">Nama Kegiatan</th>
                        <th class="px-6 py-4">Pengusul</th>
                        <th class="px-6 py-4">Jurusan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Posisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="tbody-kegiatan">
                    <?php if (empty($list_kegiatan)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-400 italic">
                                Belum ada data pengajuan kegiatan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($list_kegiatan as $k): ?>
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($k['created_at'])) ?>
                                    <div class="text-xs text-gray-400"><?= date('H:i', strtotime($k['created_at'])) ?></div>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    <?= htmlspecialchars($k['nama']) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-gray-700 font-medium"><?= htmlspecialchars($k['pengusul']) ?></span>
                                        <span class="text-xs text-gray-400"><?= htmlspecialchars($k['nim']) ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?= htmlspecialchars($k['jurusan']) ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="<?= getStatusBadge($k['status']) ?>">
                                        <?= htmlspecialchars($k['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                        <?= htmlspecialchars($k['posisi_sekarang']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (count($list_kegiatan) > 0): ?>
        <div class="p-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
            Menampilkan <?= count($list_kegiatan) ?> data terbaru
        </div>
        <?php endif; ?>
    </section>

    <!-- Section 2: Monitoring LPJ -->
    <section class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-gradient-to-r from-purple-50/50 to-white">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                    <i class="fas fa-clipboard-check text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Daftar LPJ Masuk</h3>
                    <p class="text-xs text-gray-500">Laporan pertanggungjawaban kegiatan</p>
                </div>
            </div>

            <div class="relative">
                <input type="text" id="search-lpj" placeholder="Cari LPJ..." 
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-400 focus:border-purple-400 w-full sm:w-64 transition-all">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Tanggal Upload</th>
                        <th class="px-6 py-4">Kegiatan Terkait</th>
                        <th class="px-6 py-4">Pengusul</th>
                        <th class="px-6 py-4 text-right">Total Realisasi</th>
                        <th class="px-6 py-4 text-center">Status LPJ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="tbody-lpj">
                     <?php if (empty($list_lpj)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
                                Belum ada data LPJ masuk.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($list_lpj as $l): ?>
                            <tr class="hover:bg-purple-50/30 transition-colors">
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                    <?= date('d M Y', strtotime($l['tanggal_upload'])) ?>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    <?= htmlspecialchars($l['nama_kegiatan']) ?>
                                    <div class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($l['jurusan']) ?></div>
                                </td>
                                <td class="px-6 py-4 text-gray-700">
                                    <?= htmlspecialchars($l['pengusul']) ?>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-gray-700">
                                    Rp <?= number_format($l['total_realisasi'], 0, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="<?= getStatusBadge($l['status_lpj']) ?>">
                                        <?= htmlspecialchars($l['status_lpj']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (count($list_lpj) > 0): ?>
        <div class="p-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
            Menampilkan <?= count($list_lpj) ?> data terbaru
        </div>
        <?php endif; ?>
    </section>

</main>

<!-- Simple Client-Side Search Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Function for Kegiatan
    const searchKegiatan = document.getElementById('search-kegiatan');
    const tbodyKegiatan = document.getElementById('tbody-kegiatan');
    const rowsKegiatan = tbodyKegiatan.querySelectorAll('tr');

    searchKegiatan.addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        rowsKegiatan.forEach(row => {
            // Check if row is the "No Data" row
            if (row.cells.length === 1) return;
            
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });

    // Search Function for LPJ
    const searchLpj = document.getElementById('search-lpj');
    const tbodyLpj = document.getElementById('tbody-lpj');
    const rowsLpj = tbodyLpj.querySelectorAll('tr');

    searchLpj.addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        rowsLpj.forEach(row => {
             // Check if row is the "No Data" row
            if (row.cells.length === 1) return;

            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(term) ? '' : 'none';
        });
    });
});
</script>