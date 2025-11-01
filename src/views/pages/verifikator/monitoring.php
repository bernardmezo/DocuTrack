<?php
// File: src/views/pages/verifikator/monitoring.php

if (!isset($list_proposal)) { $list_proposal = []; }
if (!isset($tahapan_all)) { $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ']; }
$total_langkah = count($tahapan_all) - 1;
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="monitoring-section" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Progres Proposal</h2>
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 ..."></i>
                <input type="text" id="search-monitoring-input" placeholder="Cari Proposal..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm ...">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px]">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/3">Proposal Details</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/2">Progres</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider w-1/6">Status</th>
                    </tr>
                </thead>
                <tbody id="monitoring-table-body" class="divide-y divide-gray-100">
                    <?php
                        if (!empty($list_proposal)):
                            foreach ($list_proposal as $item):
                                
                                // --- Logika untuk Stepper ---
                                $posisi_sekarang = array_search($item['tahap_sekarang'], $tahapan_all);
                                if ($posisi_sekarang === false) $posisi_sekarang = 0;
                                
                                $status_lower = strtolower($item['status']);
                                $is_ditolak = $status_lower === 'ditolak';
                                $lebar_progress = $posisi_sekarang > 0 ? ($posisi_sekarang / $total_langkah) * 100 : 0;
                                
                                // Tentukan warna garis progress
                                $progress_color_class = $is_ditolak ? 'bg-red-500' : 'bg-gradient-to-r from-blue-500 to-cyan-400';

                                // --- Logika untuk Badge Status ---
                                $status_class = match ($status_lower) {
                                    'approved' => 'text-green-700 bg-green-100',
                                    'ditolak' => 'text-red-700 bg-red-100',
                                    'in process' => 'text-blue-700 bg-blue-100',
                                    default => 'text-gray-700 bg-gray-100', // Menunggu
                                };
                    ?>
                                <tr class='monitoring-row hover:bg-gray-50 transition-colors' data-nama="<?php echo htmlspecialchars($item['nama']); ?>">
                                    <td class='px-6 py-5 align-top'>
                                        <div class="text-sm font-semibold text-gray-900"><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></div>
                                        <div class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars($item['pengusul'] ?? 'N/A'); ?></div>
                                    </td>
                                    
                                    <td class='px-6 py-5 align-middle'>
                                        <div class="relative w-full h-10 flex items-center">
                                            <div class="absolute top-1/2 -translate-y-1/2 left-0 w-full h-1 bg-gray-200 rounded-full z-0"></div> 
                                            <div class="absolute top-1/2 -translate-y-1/2 left-0 h-1 <?php echo $progress_color_class; ?> rounded-full z-0 transition-all duration-500 ease-out" 
                                                 style="width: <?php echo $lebar_progress; ?>%;"></div> 
                                            
                                            <?php foreach ($tahapan_all as $index => $nama_tahap): 
                                                // Tentukan style dot
                                                $is_completed = $index < $posisi_sekarang;
                                                $is_active = $index == $posisi_sekarang;
                                                
                                                $dot_style = 'bg-gray-300 border-gray-400'; // Belum
                                                
                                                if ($is_completed) {
                                                    $dot_style = 'bg-blue-500 border-blue-600'; // Selesai
                                                } elseif ($is_active) {
                                                    if ($is_ditolak) {
                                                        $dot_style = 'bg-red-500 border-red-600 ring-4 ring-red-200 scale-110'; // Gagal
                                                    } else {
                                                        $dot_style = 'bg-blue-500 border-blue-600 ring-4 ring-blue-200 scale-110'; // Aktif
                                                    }
                                                }
                                                
                                                $left_position = $total_langkah > 0 ? ($index / $total_langkah) * 100 : 0;
                                            ?>
                                            <div class="relative z-10" style="position: absolute; left: <?php echo $left_position; ?>%; transform: translateX(-50%);" title="<?php echo htmlspecialchars($nama_tahap); ?>">
                                                <div class="w-4 h-4 rounded-full border-2 <?php echo $dot_style; ?> transition-all duration-300"></div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    
                                    <td class='px-6 py-5 align-top'>
                                        <span class='inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold <?php echo $status_class; ?>'>
                                            <?php echo htmlspecialchars($item['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr id="empty-row-monitoring">
                            <td colspan="3" class="text-center py-10 text-gray-500 italic">Tidak ada proposal untuk dimonitor.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
            <span class="text-sm text-gray-600">Showing 1 to <?php echo count($list_proposal); ?> of <?php echo count($list_proposal); ?> results</span>
            <div class="flex items-center gap-1">
                <button class="px-3 py-1 rounded-md text-sm font-medium bg-gray-100 text-gray-400 cursor-not-allowed"><i class="fas fa-chevron-left"></i></button>
                <button class="px-3 py-1 rounded-md text-sm font-medium bg-blue-600 text-white shadow-sm">1</button>
                <button class="px-3 py-1 rounded-md text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700">2</button>
                <button class="px-3 py-1 rounded-md text-sm font-medium bg-gray-100 hover:bg-gray-200 text-gray-700"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>

    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-monitoring-input');
        const tableBody = document.getElementById('monitoring-table-body');
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr.monitoring-row')) : [];
        const emptyRow = document.getElementById('empty-row-monitoring');

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
                emptyRow.style.display = hasVisibleRows ? 'none' : '';
            }
        }
        searchInput?.addEventListener('input', filterTable);
    });
</script>