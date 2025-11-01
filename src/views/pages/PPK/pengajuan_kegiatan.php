<?php
// File: src/views/pages/PPK/pengajuan_kegiatan.php

if (!isset($list_usulan)) { $list_usulan = []; } // Pastikan variabel ada
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="stage-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian Verifikasi (Persetujuan PPK)</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar usulan yang menunggu persetujuan akhir Anda.</p>
        </div>

        <div class="mb-6 flex justify-end"> 
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-kegiatan-input" placeholder="Cari Nama Kegiatan..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                       aria-label="Cari Kegiatan">
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
                <tbody id="kegiatan-table-body" class="divide-y divide-gray-100">
                    <?php
                        if (!empty($list_usulan)):
                            $nomor = 1;
                            foreach ($list_usulan as $item):
                                // Logika Status (Hanya 'Menunggu' yang akan muncul)
                                $status_class = 'text-gray-700 bg-gray-100';
                                $icon_class = 'fas fa-hourglass-half';
                                $row_class = 'bg-gray-50 font-medium'; // Highlight
                    ?>
                                <tr class='<?php echo $row_class; ?> hover:bg-gray-100 transition-colors'>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700'><?php echo $nomor++; ?>.</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-900'><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold'>
                                        <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo $status_class; ?>'>
                                            <i class='<?php echo $icon_class; ?>'></i>
                                            <?php echo htmlspecialchars($item['status'] ?? 'N/A'); ?>
                                        </span>
                                    </td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium'>
                                        <div class='flex gap-2 items-center'>
                                            <a href="/docutrack/public/ppk/telaah/show/<?php echo $item['id'] ?? ''; ?>?ref=kegiatan" 
                                               class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>
                                               Setujui
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr id="empty-row">
                            <td colspan="5" class="text-center py-10 text-gray-500 italic">Tidak ada usulan yang menunggu persetujuan Anda.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-kegiatan-input');
        const tableBody = document.getElementById('kegiatan-table-body');
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr')) : [];
        const emptyRow = document.getElementById('empty-row');

        function filterTable() {
            if (!searchInput) return;
            const filterText = searchInput.value.toLowerCase().trim();
            let hasVisibleRows = false;

            allRows.forEach(row => {
                if (row.id === 'empty-row') return; 
                
                const nameCell = row.cells[1]; 
                if (nameCell) {
                    const name = nameCell.textContent.toLowerCase();
                    const isMatch = name.includes(filterText);
                    row.style.display = isMatch ? '' : 'none';
                    if (isMatch) hasVisibleRows = true;
                }
            });
            if (emptyRow) {
                emptyRow.style.display = hasVisibleRows ? 'none' : (allRows.length > 1 ? 'none' : '');
            }
        }
        searchInput?.addEventListener('input', filterTable);
    });
</script>