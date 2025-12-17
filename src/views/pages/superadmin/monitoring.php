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

<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full space-y-4 sm:space-y-6 md:space-y-8">

    <!-- Header -->
    <div class="mb-4 sm:mb-6 md:mb-8 p-4 sm:p-5 md:p-6 rounded-xl sm:rounded-2xl bg-white/70 backdrop-blur-xl border border-white shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-[0.03] pointer-events-none">
            <i class="fas fa-microchip text-6xl sm:text-7xl md:text-9xl"></i>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 sm:gap-3 mb-1">
                    <span class="flex h-2 w-2 sm:h-2.5 sm:w-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <h1 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-700 to-indigo-600 bg-clip-text text-transparent">
                        Monitoring Global
                    </h1>
                </div>
                <div class="inline-flex items-center gap-2 px-2.5 sm:px-3 py-1 bg-indigo-50/50 rounded-full border border-indigo-100/50">
                    <i class="fas fa-sparkles text-indigo-500 text-[10px]"></i>
                    <p class="text-xs sm:text-sm text-gray-500">Pantau seluruh pengajuan kegiatan dan laporan pertanggungjawaban di satu tempat.</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.location.reload()" class="group flex items-center gap-2 px-4 sm:px-5 py-2 sm:py-2.5 bg-white text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-800 hover:text-white transition-all duration-300 shadow-sm border border-slate-100 w-full sm:w-auto justify-center">
                    <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                    <span>System Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Section 1: Monitoring Pengajuan Kegiatan -->
    <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-100 flex flex-col gap-3 sm:gap-4 bg-gradient-to-r from-blue-50/50 to-white">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="p-1.5 sm:p-2 bg-blue-100 text-blue-600 rounded-lg flex-shrink-0">
                    <i class="fas fa-file-alt text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-gray-800 text-base sm:text-lg truncate">Pengajuan Kegiatan</h3>
                    <p class="text-xs text-gray-500 truncate">Semua usulan kegiatan masuk</p>
                </div>
            </div>
            
            <div class="relative">
                <input type="text" id="search-kegiatan" placeholder="Cari kegiatan..." 
                       class="pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-400 focus:border-blue-400 w-full transition-all">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
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
                <tbody class="divide-y divide-gray-50" id="tbody-kegiatan-desktop">
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

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-100" id="cards-kegiatan">
            <?php if (empty($list_kegiatan)): ?>
                <div class="p-6 text-center text-gray-400 italic text-sm">
                    Belum ada data pengajuan kegiatan.
                </div>
            <?php else: ?>
                <?php foreach($list_kegiatan as $k): ?>
                    <div class="p-4 hover:bg-blue-50/30 transition-colors space-y-3">
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-2">
                            <h4 class="font-semibold text-gray-800 text-sm leading-tight flex-1">
                                <?= htmlspecialchars($k['nama']) ?>
                            </h4>
                            <span class="<?= getStatusBadge($k['status']) ?> flex-shrink-0">
                                <?= htmlspecialchars($k['status']) ?>
                            </span>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <div class="text-gray-400 mb-1">Pengusul</div>
                                <div class="font-medium text-gray-700"><?= htmlspecialchars($k['pengusul']) ?></div>
                                <div class="text-gray-400 text-[10px] mt-0.5"><?= htmlspecialchars($k['nim']) ?></div>
                            </div>
                            <div>
                                <div class="text-gray-400 mb-1">Jurusan</div>
                                <div class="font-medium text-gray-700"><?= htmlspecialchars($k['jurusan']) ?></div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <i class="far fa-calendar text-[10px]"></i>
                                <span><?= date('d M Y', strtotime($k['created_at'])) ?></span>
                                <span class="text-gray-300">•</span>
                                <span><?= date('H:i', strtotime($k['created_at'])) ?></span>
                            </div>
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">
                                <?= htmlspecialchars($k['posisi_sekarang']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (count($list_kegiatan) > 0): ?>
        <div class="p-3 sm:p-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
            Menampilkan <?= count($list_kegiatan) ?> data terbaru
        </div>
        <?php endif; ?>
    </section>

    <!-- Section 2: Monitoring LPJ -->
    <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-100 flex flex-col gap-3 sm:gap-4 bg-gradient-to-r from-purple-50/50 to-white">
            <div class="flex items-center gap-2 sm:gap-3">
                <div class="p-1.5 sm:p-2 bg-purple-100 text-purple-600 rounded-lg flex-shrink-0">
                    <i class="fas fa-clipboard-check text-base sm:text-lg"></i>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-bold text-gray-800 text-base sm:text-lg truncate">LPJ Masuk</h3>
                    <p class="text-xs text-gray-500 truncate">Laporan pertanggungjawaban kegiatan</p>
                </div>
            </div>

            <div class="relative">
                <input type="text" id="search-lpj" placeholder="Cari LPJ..." 
                       class="pl-9 pr-4 py-2 sm:py-2.5 text-xs sm:text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-purple-400 focus:border-purple-400 w-full transition-all">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
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
                <tbody class="divide-y divide-gray-50" id="tbody-lpj-desktop">
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

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-100" id="cards-lpj">
            <?php if (empty($list_lpj)): ?>
                <div class="p-6 text-center text-gray-400 italic text-sm">
                    Belum ada data LPJ masuk.
                </div>
            <?php else: ?>
                <?php foreach($list_lpj as $l): ?>
                    <div class="p-4 hover:bg-purple-50/30 transition-colors space-y-3">
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-800 text-sm leading-tight">
                                    <?= htmlspecialchars($l['nama_kegiatan']) ?>
                                </h4>
                                <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($l['jurusan']) ?></p>
                            </div>
                            <span class="<?= getStatusBadge($l['status_lpj']) ?> flex-shrink-0">
                                <?= htmlspecialchars($l['status_lpj']) ?>
                            </span>
                        </div>

                        <!-- Info -->
                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Pengusul</span>
                                <span class="font-medium text-gray-700"><?= htmlspecialchars($l['pengusul']) ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Total Realisasi</span>
                                <span class="font-mono font-semibold text-gray-800">
                                    Rp <?= number_format($l['total_realisasi'], 0, ',', '.') ?>
                                </span>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center gap-1.5 pt-2 border-t border-gray-100 text-xs text-gray-500">
                            <i class="far fa-calendar text-[10px]"></i>
                            <span>Upload: <?= date('d M Y', strtotime($l['tanggal_upload'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (count($list_lpj) > 0): ?>
        <div class="p-3 sm:p-4 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
            Menampilkan <?= count($list_lpj) ?> data terbaru
        </div>
        <?php endif; ?>
    </section>

</main>

<!-- Enhanced Client-Side Search Script with Mobile Support -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search Function for Kegiatan (Desktop Table)
    const searchKegiatan = document.getElementById('search-kegiatan');
    const tbodyKegiatanDesktop = document.getElementById('tbody-kegiatan-desktop');
    const cardsKegiatan = document.getElementById('cards-kegiatan');
    
    if (tbodyKegiatanDesktop) {
        const rowsKegiatanDesktop = tbodyKegiatanDesktop.querySelectorAll('tr');
        
        searchKegiatan.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            
            // Filter desktop table
            rowsKegiatanDesktop.forEach(row => {
                if (row.cells.length === 1) return; // Skip "No Data" row
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
    
    // Search Function for Kegiatan (Mobile Cards)
    if (cardsKegiatan) {
        const cardElements = cardsKegiatan.children;
        
        searchKegiatan.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            
            // Filter mobile cards
            Array.from(cardElements).forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }

    // Search Function for LPJ (Desktop Table)
    const searchLpj = document.getElementById('search-lpj');
    const tbodyLpjDesktop = document.getElementById('tbody-lpj-desktop');
    const cardsLpj = document.getElementById('cards-lpj');
    
    if (tbodyLpjDesktop) {
        const rowsLpjDesktop = tbodyLpjDesktop.querySelectorAll('tr');
        
        searchLpj.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            
            // Filter desktop table
            rowsLpjDesktop.forEach(row => {
                if (row.cells.length === 1) return; // Skip "No Data" row
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
    
    // Search Function for LPJ (Mobile Cards)
    if (cardsLpj) {
        const cardElements = cardsLpj.children;
        
        searchLpj.addEventListener('keyup', function(e) {
            const term = e.target.value.toLowerCase();
            
            // Filter mobile cards
            Array.from(cardElements).forEach(card => {
                const text = card.innerText.toLowerCase();
                card.style.display = text.includes(term) ? '' : 'none';
            });
        });
    }
});
</script>