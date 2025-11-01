<?php
// File: src/views/pages/admin/pengajuan_lpj_list.php

if (!isset($list_lpj)) { $list_lpj = []; }
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="stage-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian Pengajuan LPJ</h2>
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-lpj-input" placeholder="Cari Nama Kegiatan..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                       aria-label="Cari Kegiatan LPJ">
            </div>
        </div>
        
        <div class="overflow-x-auto max-h-96 border border-gray-100 rounded-lg">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pengusul</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                        if (!empty($list_lpj)):
                            $nomor = 1;
                            foreach ($list_lpj as $item):
                                // --- PERBAIKAN DI SINI ---
                                $status_class = match (strtolower($item['status'] ?? '')) {
                                    'setuju' => 'text-green-600 bg-green-100',
                                    'revisi' => 'text-yellow-700 bg-yellow-100',
                                    'menunggu' => 'text-gray-600 bg-gray-100',
                                    default => 'text-gray-600 bg-gray-100', // Fallback
                                };
                                // --- AKHIR PERBAIKAN ---
                    ?>
                                <tr class='hover:bg-gray-50 transition-colors'>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700 font-medium'><?php echo $nomor++; ?>.</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-800 font-medium'><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold'><span class='px-3 py-1 rounded-full <?php echo $status_class; ?>'><?php echo htmlspecialchars($item['status'] ?? 'N/A'); ?></span></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium'>
                                        <div class='flex gap-2 items-center'>
                                            <a href="/docutrack/public/admin/pengajuan-lpj/show/<?php echo $item['id'] ?? ''; ?>?ref=lpj" class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>
                                                Lihat RAB
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500 italic">Belum ada kegiatan yang siap untuk diajukan LPJ.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </section>

</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-lpj-input');
        const tableBody = document.querySelector('#stage-list tbody');
        const tableRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
        let emptyRow = tableBody ? tableBody.querySelector('td[colspan="5"]') : null;

        function filterTable() {
            if (!searchInput) return;
            const filterText = searchInput.value.toLowerCase().trim();
            let hasVisibleRows = false;

            tableRows.forEach(row => {
                if (row.cells.length === 1 && row.cells[0].getAttribute('colspan') === '5') {
                    return; 
                }
                const nameCell = row.cells[1]; // Kolom Nama Kegiatan
                if (nameCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const isMatch = name.includes(filterText);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) hasVisibleRows = true;
                }
            });
            if (emptyRow) {
                emptyRow.parentElement.style.display = hasVisibleRows ? 'none' : '';
            }
        }
        searchInput?.addEventListener('input', filterTable);
    });
</script>