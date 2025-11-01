<?php
// File: src/views/pages/verifikator/riwayat_verifikasi.php

if (!isset($list_riwayat)) { $list_riwayat = []; } // Pastikan variabel ada
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="riwayat-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Riwayat Verifikasi</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar semua usulan yang telah Anda proses (Disetujui, Ditolak, atau Revisi).</p>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            
            <div class="flex-shrink-0 w-full md:w-auto p-1 bg-gray-100 rounded-full flex items-center space-x-1">
                <button type="button" class="riwayat-filter-tab active-tab" data-status="Semua">
                    Semua
                </button>
                <button type="button" class="riwayat-filter-tab" data-status="Disetujui">
                    Disetujui
                </button>
                <button type="button" class="riwayat-filter-tab" data-status="Revisi">
                    Direvisi
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
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl. Verifikasi</th>
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
                                $status_class = match (strtolower($status_text)) {
                                     'disetujui' => 'text-green-700 bg-green-100',
                                     'revisi' => 'text-yellow-700 bg-yellow-100',
                                     'ditolak' => 'text-red-700 bg-red-100',
                                     default => 'text-gray-600 bg-gray-100',
                                };
                    ?>
                                <tr class='riwayat-row hover:bg-gray-50 transition-colors' data-status="<?php echo $status_text; ?>">
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700'><?php echo $nomor++; ?>.</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-900 font-medium'><?php echo htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['tgl_verifikasi'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold'>
                                        <span class='px-3 py-1 rounded-full <?php echo $status_class; ?>'><?php echo $status_text; ?></span>
                                    </td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium'>
                                        <div class='flex gap-2 items-center'>
                                            <a href="/docutrack/public/verifikator/telaah/show/<?php echo $item['id'] ?? ''; ?>?ref=riwayat" 
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
                            <td colspan="6" class="text-center py-10 text-gray-500 italic">Tidak ada riwayat verifikasi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
    </section>
</main>

<style>
    .riwayat-filter-tab {
        @apply px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent -mb-px
               hover:text-blue-600 hover:border-blue-600 transition-colors duration-200;
    }
    .riwayat-filter-tab.active-tab {
        @apply text-blue-600 border-blue-600 font-semibold;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const searchInput = document.getElementById('search-riwayat-input');
        const filterTabs = document.querySelectorAll('.riwayat-filter-tab');
        const tableBody = document.getElementById('riwayat-table-body');
        const allRows = tableBody ? Array.from(tableBody.querySelectorAll('tr.riwayat-row')) : [];
        const emptyRow = document.getElementById('empty-row');
        let currentStatusFilter = 'Semua'; // Default filter

        function runFilter() {
            const searchText = searchInput.value.toLowerCase().trim();
            const filterStatus = currentStatusFilter.toLowerCase(); // <-- Ubah filter jadi lowercase
            let hasVisibleRows = false;

            allRows.forEach(row => {
                const status = row.dataset.status.toLowerCase();
                const namaKegiatan = row.cells[1].textContent.toLowerCase();

                // --- PERBAIKAN LOGIKA DI SINI ---
                // Cek filter status (sekarang keduanya lowercase)
                const statusMatch = (filterStatus === 'semua') || (status === filterStatus);
                // --- AKHIR PERBAIKAN ---

                // 2. Cek Filter Search
                const searchMatch = (searchText === '') || namaKegiatan.includes(searchText);

                // 3. Tampilkan jika keduanya cocok
                if (statusMatch && searchMatch) {
                    row.style.display = '';
                    hasVisibleRows = true;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Tampilkan pesan "kosong" jika tidak ada hasil
            if (emptyRow) {
                emptyRow.style.display = hasVisibleRows ? 'none' : '';
            }
        }

        // Event Listener untuk Search Bar
        searchInput?.addEventListener('input', runFilter);

        // Event Listener untuk Filter Tabs
        filterTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Hapus 'active' dari tab lain
                filterTabs.forEach(t => t.classList.remove('active-tab'));
                // Tambah 'active' ke tab ini
                tab.classList.add('active-tab');
                // Set filter status
                currentStatusFilter = tab.dataset.status; // Ambil 'Semua' (tetap uppercase)
                // Jalankan filter
                runFilter();
            });
        });
        
        // Jalankan filter saat halaman dimuat (untuk menyembunyikan baris 'empty' jika ada data)
        runFilter(); 
    });
</script>