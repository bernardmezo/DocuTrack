<?php
// File: src/views/pages/wadir/riwayat_verifikasi.php

if (!isset($list_riwayat)) { $list_riwayat = []; } // Pastikan variabel ada
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="riwayat-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Riwayat Persetujuan</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar semua usulan yang telah Anda setujui atau tolak.</p>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            
            <div class="flex-shrink-0 w-full md:w-auto p-1 bg-gray-100 rounded-full flex items-center space-x-1">
                <button type="button" class="riwayat-filter-tab active-tab" data-status="Semua">
                    Semua
                </button>
                <button type="button" class="riwayat-filter-tab" data-status="Disetujui">
                    Disetujui
                </button>
                <button type="button" class="riwayat-filter-tab" data-status="Ditolak">
                    Ditolak
                </button>
            </div>

            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-riwayat-input" placeholder="Cari Nama Kegiatan..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                       aria-label="Cari Riwayat">
            </div>
        </div>
        
        <div class="overflow-x-auto max-h-96 border border-gray-100 rounded-lg">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pengusul</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl. Diputuskan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayat-table-body" class="divide-y divide-gray-100">
                    <?php
                        if (!empty($list_riwayat)):
                            $nomor = 1;
                            foreach ($list_riwayat as $item):
                                $status_text = htmlspecialchars($item['status'] ?? 'N/A');
                                $status_lower = strtolower($status_text);
                                
                                $status_class = match ($status_lower) {
                                     'disetujui' => 'text-green-700 bg-green-100',
                                     'ditolak' => 'text-red-700 bg-red-100',
                                     default => 'text-gray-600 bg-gray-100',
                                };
                    ?>
                                <tr class='riwayat-row bg-white opacity-80 hover:opacity-100 hover:bg-gray-50 transition-all' data-status="<?php echo $status_text; ?>">
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700'><?php echo $nomor++; ?>.</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-900 font-medium'><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['tgl_verifikasi'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold'>
                                        <span class='px-3 py-1 rounded-full <?php echo $status_class; ?>'><?php echo $status_text; ?></span>
                                    </td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium'>
                                        <div class='flex gap-2 items-center'>
                                            <a href="/docutrack/public/wadir/telaah/show/<?php echo $item['id'] ?? ''; ?>?ref=riwayat" 
                                               class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>
                                               Detail
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr id="empty-row">
                            <td colspan="6" class="text-center py-10 text-gray-500 italic">Tidak ada riwayat persetujuan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </section>
</main>

<style>
    .riwayat-filter-tab {
        @apply w-full md:w-auto px-4 py-2 rounded-full text-sm font-semibold 
               text-gray-600 transition-all duration-300 ease-in-out;
    }
    .riwayat-filter-tab:hover {
        @apply bg-gray-200 text-gray-800;
    }
    .riwayat-filter-tab.active-tab {
        @apply bg-blue-600 text-white shadow-md;
    }
    .riwayat-filter-tab.active-tab:hover {
        @apply bg-blue-700 text-white;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-riwayat-input');
        const filterTabs = document.querySelectorAll('.riwayat-filter-tab');
        const tableBody = document.getElementById('riwayat-table-body');
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr.riwayat-row')) : [];
        const emptyRow = document.getElementById('empty-row');
        let currentStatusFilter = 'Semua';

        function runFilter() {
            if (!tableBody) return;
            const searchText = searchInput.value.toLowerCase().trim();
            const filterStatus = currentStatusFilter.toLowerCase();
            let hasVisibleRows = false;

            allRows.forEach(row => {
                const status = row.dataset.status.toLowerCase();
                const namaKegiatan = row.cells[1].textContent.toLowerCase();

                const statusMatch = (filterStatus === 'semua') || (status === filterStatus);
                const searchMatch = (searchText === '') || namaKegiatan.includes(searchText);

                if (statusMatch && searchMatch) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            if (emptyRow) {
                emptyRow.style.display = hasVisibleRows ? 'none' : '';
            }
        }

        searchInput?.addEventListener('input', runFilter);

        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                filterTabs.forEach(t => t.classList.remove('active-tab'));
                tab.classList.add('active-tab');
                currentStatusFilter = tab.dataset.status;
                runFilter();
            });
        });
        
        runFilter(); // Jalankan sekali saat load
    });
</script>