<?php
// File: src/views/pages/PPK/riwayat.php

if (!isset($list_riwayat)) {
    $list_riwayat = [];
}
if (!isset($jurusan_list)) {
    $jurusan_list = [];
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="riwayat-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Riwayat Persetujuan PPK</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar semua usulan yang telah Anda proses (Disetujui atau Ditolak).</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10"></i>
                <input type="text" id="search-riwayat-input" placeholder="Cari Nama Kegiatan..."
                       class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                       aria-label="Cari Kegiatan">
            </div>
            
            <div class="relative w-full lg:w-60">
                <i class="fas fa-filter absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                <select id="filter-status" 
                        style="color: #374151 !important;"
                        class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                    <option value="" style="color: #374151 !important; font-weight: 600;">Semua Status</option>
                    <option value="disetujui" style="color: #374151 !important; font-weight: 600;">Disetujui</option>
                    <option value="ditolak" style="color: #374151 !important; font-weight: 600;">Ditolak</option>
                </select>
                <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
            </div>
            
            <div class="relative w-full lg:w-80">
                <i class="fas fa-graduation-cap absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                <select id="filter-jurusan" 
                        style="color: #374151 !important;"
                        class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                    <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                    <?php
                    sort($jurusan_list);
                    foreach ($jurusan_list as $jurusan) :
                        ?>
                        <option value="<?php echo htmlspecialchars(strtolower($jurusan)); ?>" 
                                style="color: #374151 !important; font-weight: 600;">
                            <?php echo htmlspecialchars($jurusan); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
            </div>
        </div>
        
        <div class="overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tgl. Persetujuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayat-table-body" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 gap-4 mt-4">
            <div id="pagination-info" class="text-sm text-gray-600"></div>
            <div id="pagination-riwayat" class="flex gap-1"></div>
        </div>
        
    </section>
</main>

<script>
    window.riwayatData = <?php echo json_encode(array_values($list_riwayat)); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-riwayat-input');
    const filterStatus = document.getElementById('filter-status');
    const filterJurusan = document.getElementById('filter-jurusan');
    const tableBody = document.getElementById('riwayat-table-body');
    const paginationContainer = document.getElementById('pagination-riwayat');
    const paginationInfo = document.getElementById('pagination-info');
    
    const allData = window.riwayatData || [];
    const ITEMS_PER_PAGE = 5;
    let filteredData = [...allData];
    let currentPage = 1;

    function applyFilters() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const statusFilter = filterStatus ? filterStatus.value.toLowerCase() : '';
        const jurusanFilter = filterJurusan ? filterJurusan.value.toLowerCase() : '';
        
        filteredData = allData.filter(item => {
            const nama = (item.nama || '').toLowerCase();
            const pengusul = (item.pengusul || '').toLowerCase();
            const status = (item.status || '').toLowerCase();
            const jurusan = (item.jurusan || '').toLowerCase();
            
            const searchMatch = !searchText || nama.includes(searchText) || pengusul.includes(searchText);
            const statusMatch = !statusFilter || status === statusFilter;
            const jurusanMatch = !jurusanFilter || jurusan === jurusanFilter;
            
            return searchMatch && statusMatch && jurusanMatch;
        });
        
        currentPage = 1;
        render();
    }

    function render() {
        if (!tableBody) return;
        
        const totalItems = filteredData.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
        const start = (currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        
        if (pageData.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                        ${allData.length === 0 ? 'Tidak ada riwayat persetujuan.' : 'Data tidak ditemukan.'}
                    </td>
                </tr>`;
        } else {
            tableBody.innerHTML = pageData.map((item, index) => {
                const no = start + index + 1;
                const tgl = formatDate(item.tanggal_pengajuan);
                const statusLower = (item.status || '').toLowerCase();
                
                let statusClass = 'text-gray-600 bg-gray-100';
                let statusIcon = 'fas fa-question-circle';
                
                if (statusLower === 'disetujui') {
                    statusClass = 'text-green-700 bg-green-100';
                    statusIcon = 'fas fa-check-circle';
                } else if (statusLower === 'ditolak') {
                    statusClass = 'text-red-700 bg-red-100';
                    statusIcon = 'fas fa-times-circle';
                }

                const displayProdi = item.prodi ? item.prodi : (item.jurusan || '-');
                
                return `
                <tr class="bg-white hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                    <td class="px-6 py-5 text-sm">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama || '')}</span>
                            <span class="text-gray-600 text-xs">
                                ${escapeHtml(item.pengusul || '')}
                                <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                            </span>
                            <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                <i class="fas fa-graduation-cap mr-1"></i>${escapeHtml(displayProdi)}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-check text-blue-500 text-xs"></i>
                            ${tgl}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${statusClass}">
                            <i class="${statusIcon}"></i>
                            ${escapeHtml(item.status || 'N/A')}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="/docutrack/public/ppk/telaah/show/${item.id}?ref=riwayat-verifikasi" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                           <i class="fas fa-eye"></i>
                           Detail
                        </a>
                    </td>
                </tr>`;
            }).join('');
        }
        
        if (paginationInfo) {
            const showingFrom = totalItems > 0 ? start + 1 : 0;
            const showingTo = totalItems > 0 ? Math.min(end, totalItems) : 0;
            paginationInfo.innerHTML = `Menampilkan <span class="font-semibold">${showingFrom}</span> s.d. <span class="font-semibold">${showingTo}</span> dari <span class="font-semibold">${totalItems}</span> hasil`;
        }
        
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (!paginationContainer) return;
        
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }
        
        let html = '';
        
        const prevDisabled = currentPage === 1;
        html += `<button class="pagination-btn px-3 py-1.5 rounded-md text-sm font-medium transition-colors 
            ${prevDisabled ? 'text-gray-400 cursor-not-allowed bg-gray-100' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'}" 
            data-page="${currentPage - 1}" ${prevDisabled ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>`;
        
        for (let i = 1; i <= totalPages; i++) {
            const isActive = i === currentPage;
            html += `<button class="pagination-btn px-3 py-1.5 rounded-md text-sm font-medium transition-colors 
                ${isActive ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'}" 
                data-page="${i}">${i}</button>`;
        }
        
        const nextDisabled = currentPage === totalPages;
        html += `<button class="pagination-btn px-3 py-1.5 rounded-md text-sm font-medium transition-colors 
            ${nextDisabled ? 'text-gray-400 cursor-not-allowed bg-gray-100' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'}" 
            data-page="${currentPage + 1}" ${nextDisabled ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>`;
        
        paginationContainer.innerHTML = html;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        } catch (e) {
            return dateStr;
        }
    }

    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    let debounceTimer;
    searchInput?.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 300);
    });

    filterStatus?.addEventListener('change', applyFilters);
    filterJurusan?.addEventListener('change', applyFilters);
    
    paginationContainer?.addEventListener('click', function(e) {
        const btn = e.target.closest('.pagination-btn');
        if (btn && !btn.disabled) {
            const page = parseInt(btn.dataset.page);
            if (page >= 1 && page !== currentPage) {
                currentPage = page;
                render();
                tableBody.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
    
    render();
});
</script>
