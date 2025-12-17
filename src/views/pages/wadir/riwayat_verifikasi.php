<?php
// File: src/views/pages/Wadir/riwayat_verifikasi.php

if (!isset($list_riwayat)) { $list_riwayat = []; }
if (!isset($jurusan_list)) { $jurusan_list = []; }
?>

<main class="main-content font-poppins p-4 sm:p-6 lg:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="riwayat-list" class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <!-- Header Section -->
        <div class="flex flex-col p-4 sm:p-6 border-b border-gray-200 flex-shrink-0 gap-3 sm:gap-4">
            <div>
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800">Riwayat Persetujuan</h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Daftar semua usulan yang telah Anda setujui.</p>
            </div>
            
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-400 z-10 text-sm"></i>
                    <input type="text" id="search-riwayat-input" placeholder="Cari Nama Kegiatan..."
                           class="w-full pl-9 sm:pl-11 pr-3 sm:pr-4 py-2 sm:py-2.5 text-xs sm:text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                           aria-label="Cari Kegiatan">
                </div>
                
                <div class="relative w-full sm:w-64 lg:w-80">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10 text-sm"></i>
                    <select id="filter-jurusan" 
                            style="color: #374151 !important;"
                            class="w-full pl-9 sm:pl-11 pr-8 sm:pr-10 py-2 sm:py-2.5 text-xs sm:text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($jurusan)); ?>" style="color: #374151 !important; font-weight: 600;"><?php echo htmlspecialchars($jurusan); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 sm:right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>
            </div>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Kegiatan & Pengusul</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Tgl. Disetujui</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="riwayat-table-body" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div id="riwayat-mobile-cards" class="md:hidden divide-y divide-gray-200">
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-6 py-3 sm:py-4 border-t border-gray-200 gap-3 sm:gap-4">
            <div id="pagination-info" class="text-xs sm:text-sm text-gray-600 text-center sm:text-left"></div>
            <div id="pagination-riwayat" class="flex gap-1 flex-wrap justify-center"></div>
        </div>
        
    </section>
</main>

<script>
    window.riwayatData = <?php echo json_encode(array_values($list_riwayat)); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-riwayat-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const tableBody = document.getElementById('riwayat-table-body');
    const mobileCards = document.getElementById('riwayat-mobile-cards');
    const paginationContainer = document.getElementById('pagination-riwayat');
    const paginationInfo = document.getElementById('pagination-info');
    
    const allData = window.riwayatData || [];
    const ITEMS_PER_PAGE = 5;
    let filteredData = [...allData];
    let currentPage = 1;

    function applyFilters() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const jurusanFilter = filterJurusan ? filterJurusan.value.toLowerCase() : '';
        
        filteredData = allData.filter(item => {
            const nama = (item.nama || '').toLowerCase();
            const pengusul = (item.pengusul || '').toLowerCase();
            const jurusan = (item.jurusan || '').toLowerCase();
            
            const searchMatch = !searchText || nama.includes(searchText) || pengusul.includes(searchText);
            const jurusanMatch = !jurusanFilter || jurusan === jurusanFilter;
            
            return searchMatch && jurusanMatch;
        });
        
        currentPage = 1;
        render();
    }

    function render() {
        const totalItems = filteredData.length;
        const totalPages = Math.max(1, Math.ceil(totalItems / ITEMS_PER_PAGE));
        const start = (currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        
        // Render Desktop Table
        if (tableBody) {
            if (pageData.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 lg:px-6 py-10 text-center text-gray-500 italic text-sm">
                            ${allData.length === 0 ? 'Tidak ada riwayat persetujuan.' : 'Data tidak ditemukan.'}
                        </td>
                    </tr>`;
            } else {
                tableBody.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    const displayProdi = item.prodi || item.jurusan;

                    return `
                    <tr class="bg-white hover:bg-gray-50 transition-colors">
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama || '')}</span>
                                <span class="text-gray-600 text-xs">
                                    ${escapeHtml(item.pengusul || '')}
                                    <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                                </span>
                                <span class="text-gray-500 text-xs mt-0.5">
                                    <i class="fas fa-graduation-cap mr-1"></i>${escapeHtml(displayProdi || '-')}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-check text-green-500 text-xs"></i>
                                ${item.tgl}
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-green-700 bg-green-100">
                                <i class="fas fa-check-circle"></i>
                                ${escapeHtml(item.status || 'Disetujui')}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-4 lg:py-5 whitespace-nowrap text-sm font-medium">
                            <a href="/docutrack/public/wadir/telaah/show/${item.id}?ref=riwayat-verifikasi" 
                               class="bg-blue-600 text-white px-3 lg:px-4 py-1.5 lg:py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                               <i class="fas fa-eye"></i>
                               Detail
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
        }

        // Render Mobile Cards
        if (mobileCards) {
            if (pageData.length === 0) {
                mobileCards.innerHTML = `
                    <div class="p-8 text-center text-gray-500 italic text-sm">
                        ${allData.length === 0 ? 'Tidak ada riwayat persetujuan.' : 'Data tidak ditemukan.'}
                    </div>`;
            } else {
                mobileCards.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    const displayProdi = item.prodi || item.jurusan;

                    return `
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-bold text-gray-500">#${no}</span>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold text-green-700 bg-green-100">
                                        <i class="fas fa-check-circle text-[10px]"></i>
                                        ${escapeHtml(item.status || 'Disetujui')}
                                    </span>
                                </div>
                                <h3 class="font-semibold text-gray-900 text-sm mb-2 leading-snug">${escapeHtml(item.nama || '')}</h3>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-3 text-xs">
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-user w-4 text-gray-400"></i>
                                <span>${escapeHtml(item.pengusul || '')}</span>
                                <span class="text-gray-400">(${escapeHtml(item.nim || '-')})</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-graduation-cap w-4 text-gray-400"></i>
                                <span>${escapeHtml(displayProdi || '-')}</span>
                            </div>
                            <div class="flex items-center gap-2 text-gray-600">
                                <i class="fas fa-calendar-check w-4 text-green-500"></i>
                                <span>${item.tgl}</span>
                            </div>
                        </div>
                        
                        <a href="/docutrack/public/wadir/telaah/show/${item.id}?ref=riwayat-verifikasi" 
                           class="w-full bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center justify-center gap-2">
                           <i class="fas fa-eye"></i>
                           Lihat Detail
                        </a>
                    </div>`;
                }).join('');
            }
        }
        
        // Pagination Info
        if (paginationInfo) {
            const showingFrom = totalItems > 0 ? start + 1 : 0;
            const showingTo = totalItems > 0 ? Math.min(end, totalItems) : 0;
            paginationInfo.innerHTML = `<span class="hidden sm:inline">Menampilkan </span><span class="font-semibold">${showingFrom}</span> - <span class="font-semibold">${showingTo}</span> dari <span class="font-semibold">${totalItems}</span>`;
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
        html += `<button class="pagination-btn px-2 sm:px-3 py-1.5 rounded-md text-xs sm:text-sm font-medium transition-colors 
            ${prevDisabled ? 'text-gray-400 cursor-not-allowed bg-gray-100' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'}" 
            data-page="${currentPage - 1}" ${prevDisabled ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>`;
        
        // Show fewer page numbers on mobile
        const maxVisiblePages = window.innerWidth < 640 ? 3 : 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
        
        if (endPage - startPage < maxVisiblePages - 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }
        
        if (startPage > 1) {
            html += `<button class="pagination-btn px-2 sm:px-3 py-1.5 rounded-md text-xs sm:text-sm font-medium transition-colors text-gray-700 bg-white border border-gray-300 hover:bg-gray-50" data-page="1">1</button>`;
            if (startPage > 2) {
                html += `<span class="px-1 sm:px-2 py-1.5 text-gray-400 text-xs sm:text-sm">...</span>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            const isActive = i === currentPage;
            html += `<button class="pagination-btn px-2 sm:px-3 py-1.5 rounded-md text-xs sm:text-sm font-medium transition-colors 
                ${isActive ? 'bg-blue-600 text-white' : 'text-gray-700 bg-white border border-gray-300 hover:bg-gray-50'}" 
                data-page="${i}">${i}</button>`;
        }
        
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                html += `<span class="px-1 sm:px-2 py-1.5 text-gray-400 text-xs sm:text-sm">...</span>`;
            }
            html += `<button class="pagination-btn px-2 sm:px-3 py-1.5 rounded-md text-xs sm:text-sm font-medium transition-colors text-gray-700 bg-white border border-gray-300 hover:bg-gray-50" data-page="${totalPages}">${totalPages}</button>`;
        }
        
        const nextDisabled = currentPage === totalPages;
        html += `<button class="pagination-btn px-2 sm:px-3 py-1.5 rounded-md text-xs sm:text-sm font-medium transition-colors 
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

    filterJurusan?.addEventListener('change', applyFilters);
    
    paginationContainer?.addEventListener('click', function(e) {
        const btn = e.target.closest('.pagination-btn');
        if (btn && !btn.disabled) {
            const page = parseInt(btn.dataset.page);
            if (page >= 1 && page !== currentPage) {
                currentPage = page;
                render();
                const scrollTarget = window.innerWidth < 768 ? mobileCards : tableBody;
                scrollTarget?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
    
    // Re-render on window resize to adjust pagination
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            renderPagination(Math.max(1, Math.ceil(filteredData.length / ITEMS_PER_PAGE)));
        }, 250);
    });
    
    render();
});
</script>