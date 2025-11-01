<?php
// File: src/views/pages/wadir/dashboard.php

// Ambil data yang dikirim dari WadirDashboardController
$stats = $stats ?? ['total' => 0, 'disetujui' => 0, 'menunggu' => 0];
$list_usulan = $list_usulan ?? [];
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white
                    bg-gradient-to-br from-blue-400 to-blue-500 
                    hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 
                    transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['total']); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-layer-group fa-xl"></i></div>
            </div>
        </div>

        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white
                    bg-gradient-to-br from-green-400 to-green-500 
                    hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 
                    transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['disetujui']); ?></h3><p class="text-sm font-medium opacity-80">Disetujui</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-check-circle fa-xl"></i></div>
            </div>
        </div>

        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-yellow-900 
                    bg-gradient-to-br from-yellow-300 to-yellow-400 
                    hover:shadow-[0_0_20px_rgba(250,204,21,0.5)] hover:-translate-y-1 
                    transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['menunggu']); ?></h3><p class="text-sm font-medium opacity-80">Menunggu Persetujuan</p></div>
                <div class="p-3 rounded-full bg-black/10 opacity-80 group-hover:opacity-100 transition-opacity text-yellow-800"><i class="fas fa-hourglass-half fa-xl"></i></div>
            </div>
        </div>
        
    </section>

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="flex flex-col md:flex-row justify-between items-center p-6 border-b border-gray-200 flex-shrink-0 gap-4">
            <h3 class="text-xl font-semibold text-gray-800 w-full md:w-auto">Daftar Usulan (Semua Status)</h3>
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-wadir-input" placeholder="Cari Nama Kegiatan..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                       aria-label="Cari Kegiatan">
            </div>
        </div>
        
        <div class="overflow-y-auto overflow-x-auto max-h-96">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Nama Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="wadir-table-body" class="divide-y divide-gray-100">
                    <?php if (empty($list_usulan)): ?>
                        <tr id="empty-row">
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                Tidak ada usulan untuk ditinjau.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $nomor = 1; ?>
                        <?php foreach ($list_usulan as $item): 
                            $status_text = htmlspecialchars($item['status'] ?? 'N/A');
                            $status_lower = strtolower($status_text);

                            // --- Logika Status (Hanya 2) ---
                            $status_class = match ($status_lower) {
                                'disetujui' => 'text-green-700 bg-green-100',
                                default => 'text-gray-600 bg-gray-100', // 'Menunggu'
                            };
                            $icon_class = match ($status_lower) {
                                'disetujui' => 'fas fa-check-circle',
                                default => 'fas fa-hourglass-half',
                            };
                            
                            $row_class = ($status_lower === 'menunggu') ? 'bg-gray-50 font-medium' : 'bg-white';
                        ?>
                            <tr class='wadir-row <?php echo $row_class; ?> hover:bg-gray-100 transition-colors' data-nama="<?php echo htmlspecialchars(strtolower($item['nama'])); ?>">
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-700'><?php echo $nomor++; ?>.</td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-900'><?php echo htmlspecialchars($item['nama']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-xs font-semibold'>
                                    <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo $status_class; ?>'>
                                        <i class='<?php echo $icon_class; ?>'></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm font-medium'>
                                    <div class='flex gap-2'>
                                        <a href="/docutrack/public/wadir/telaah/show/<?php echo $item['id'] ?? ''; ?>?ref=dashboard" 
                                           class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>
                                           Lihat
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-wadir-input');
        const tableBody = document.getElementById('wadir-table-body');
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr.wadir-row')) : [];
        const emptyRow = document.getElementById('empty-row');

        function filterTable() {
            if (!searchInput) return;
            const filterText = searchInput.value.toLowerCase().trim();
            let hasVisibleRows = false;

            allRows.forEach(row => {
                const namaKegiatan = row.dataset.nama.toLowerCase();
                const isMatch = namaKegiatan.includes(filterText);
                
                row.style.display = isMatch ? '' : 'none';
                if (isMatch) hasVisibleRows = true;
            });
            
            if (emptyRow) {
                emptyRow.style.display = hasVisibleRows ? 'none' : (allRows.length > 0 ? '' : 'table-row');
            }
        }
        searchInput?.addEventListener('input', filterTable);
    });
</script>