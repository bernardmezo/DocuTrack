<?php
// File: src/views/pages/verifikator/riwayat_verifikasi.php

if (!isset($list_riwayat)) { $list_riwayat = []; }
if (!isset($jurusan_list)) { $jurusan_list = []; }
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="riwayat-list" class="stage-content bg-white p-0 rounded-2xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <!-- Header Section -->
        <div class="p-4 sm:p-5 md:p-7 border-b border-gray-200 flex-shrink-0">
            <!-- Title -->
            <div class="mb-4 sm:mb-5">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i>
                    <span>Riwayat Verifikasi</span>
                </h2>
            </div>

            <!-- Filter Controls - Stack on Mobile -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3 sm:mb-4">
                <!-- Status Filter -->
                <div class="relative flex-1 sm:flex-initial">
                    <i class="fas fa-filter absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 z-10 text-xs sm:text-sm pointer-events-none"></i>
                    <select id="filter-status" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50 sm:min-w-[180px]">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Status</option>
                        <option value="disetujui" style="color: #374151 !important; font-weight: 600;">Disetujui</option>
                        <option value="revisi" style="color: #374151 !important; font-weight: 600;">Revisi</option>
                        <option value="ditolak" style="color: #374151 !important; font-weight: 600;">Ditolak</option>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                </div>
                
                <!-- Jurusan Filter -->
                <div class="relative flex-1">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs sm:text-sm"></i>
                    <select id="filter-jurusan" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        <?php 
                        sort($jurusan_list);
                        foreach ($jurusan_list as $jurusan): 
                        ?>
                            <option value="<?php echo htmlspecialchars(strtolower($jurusan)); ?>" 
                                    style="color: #374151 !important; font-weight: 600;">
                                <?php echo htmlspecialchars($jurusan); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                </div>
                
                <!-- Reset Button -->
                <button id="reset-filter-riwayat" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative">
                <i class="fas fa-search absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-400 z-10 text-xs sm:text-sm"></i>
                <input type="text" id="search-riwayat-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..."
                       class="w-full pl-9 sm:pl-11 pr-4 py-2 sm:py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                       aria-label="Cari Kegiatan">
            </div>
        </div>
        
        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Kegiatan & Pengusul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Verifikasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="riwayat-table-body" class="divide-y divide-gray-100 bg-white">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Visible on Mobile Only) -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-riwayat-list" class="p-3 space-y-3">
                <!-- Mobile cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination - Responsive -->
        <div class="p-3 sm:p-4 md:px-6 md:py-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 sm:gap-4">
                <div id="pagination-info" class="text-xs sm:text-sm text-gray-600 text-center sm:text-left"></div>
                <div id="pagination-riwayat" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
        
    </section>
</main>

<style>
    /* Mobile Card Styling */
    .mobile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        cursor: pointer;
    }
    
    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
    }
    
    .mobile-card:active {
        transform: translateY(0);
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .mobile-card-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .mobile-card-row {
        margin-bottom: 0.875rem;
    }
    
    .mobile-card-row:last-of-type {
        margin-bottom: 0;
    }
    
    .mobile-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-label i {
        color: #3b82f6;
        font-size: 0.75rem;
    }
    
    .mobile-card-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
    }
    
    .mobile-card-kegiatan:hover {
        color: #3b82f6;
    }
    
    .mobile-card-mahasiswa {
        font-size: 0.85rem;
        color: #4b5563;
        margin-top: 0.25rem;
    }
    
    .mobile-card-prodi {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .mobile-card-footer {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .mobile-card-date {
        font-size: 0.8rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Status Badge Styling */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-badge i {
        font-size: 0.625rem;
    }
    
    .status-disetujui {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 1px solid #86efac;
    }
    
    .status-revisi {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    
    .status-ditolak {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .empty-state-subtext {
        font-size: 0.85rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }
    
    /* Smooth animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .mobile-card {
        animation: slideIn 0.3s ease forwards;
    }
</style>

<script>
    // Data dikirim dari PHP ke JS
    window.riwayatData = <?php echo json_encode(array_values($list_riwayat)); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-riwayat-input');
    const filterStatus = document.getElementById('filter-status');
    const filterJurusan = document.getElementById('filter-jurusan');
    const resetButton = document.getElementById('reset-filter-riwayat');
    const tableBody = document.getElementById('riwayat-table-body');
    const mobileList = document.getElementById('mobile-riwayat-list');
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
            const nim = (item.nim || '').toLowerCase();
            const status = (item.status || '').toLowerCase();
            const jurusan = (item.jurusan || '').toLowerCase();
            
            const searchMatch = !searchText || nama.includes(searchText) || pengusul.includes(searchText) || nim.includes(searchText);
            const statusMatch = !statusFilter || status === statusFilter;
            const jurusanMatch = !jurusanFilter || jurusan === jurusanFilter;
            
            return searchMatch && statusMatch && jurusanMatch;
        });
        
        currentPage = 1;
        render();
    }

    function resetFilters() {
        if (searchInput) searchInput.value = '';
        if (filterStatus) filterStatus.value = '';
        if (filterJurusan) filterJurusan.value = '';
        applyFilters();
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
                        <td colspan="5" class="px-6 py-10">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <div class="empty-state-text">${allData.length === 0 ? 'Tidak ada riwayat verifikasi.' : 'Data tidak ditemukan.'}</div>
                                <div class="empty-state-subtext">Coba ubah filter atau kata kunci pencarian Anda</div>
                            </div>
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
                    } else if (statusLower === 'revisi') {
                        statusClass = 'text-yellow-700 bg-yellow-100';
                        statusIcon = 'fas fa-sync-alt';
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
                            <a href="/docutrack/public/verifikator/telaah/show/${item.id}?ref=riwayat-verifikasi" 
                               class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                               <i class="fas fa-eye"></i>
                               Detail
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
        }
        
        // Render Mobile Cards
        if (mobileList) {
            if (pageData.length === 0) {
                mobileList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <div class="empty-state-text">${allData.length === 0 ? 'Tidak ada riwayat verifikasi.' : 'Data tidak ditemukan.'}</div>
                        <div class="empty-state-subtext">Coba ubah filter atau kata kunci pencarian Anda</div>
                    </div>`;
            } else {
                mobileList.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    const tgl = formatDate(item.tanggal_pengajuan);
                    const statusLower = (item.status || '').toLowerCase();
                    const displayProdi = item.prodi ? item.prodi : (item.jurusan || '-');
                    
                    let statusClass = 'status-badge';
                    let statusIcon = 'fas fa-question-circle';
                    
                    if (statusLower === 'disetujui') {
                        statusClass += ' status-disetujui';
                        statusIcon = 'fas fa-check-circle';
                    } else if (statusLower === 'revisi') {
                        statusClass += ' status-revisi';
                        statusIcon = 'fas fa-sync-alt';
                    } else if (statusLower === 'ditolak') {
                        statusClass += ' status-ditolak';
                        statusIcon = 'fas fa-times-circle';
                    }
                    
                    return `
                    <div class="mobile-card" onclick="window.location.href='/docutrack/public/verifikator/telaah/show/${item.id}?ref=riwayat-verifikasi'">
                        <div class="mobile-card-header">
                            <div class="mobile-card-number">#${no}</div>
                            <span class="${statusClass}">
                                <i class="${statusIcon}"></i>
                                ${escapeHtml(item.status || 'N/A')}
                            </span>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-clipboard-list"></i>
                                Nama Kegiatan
                            </div>
                            <div class="mobile-card-kegiatan">${escapeHtml(item.nama || '')}</div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-user"></i>
                                Pengusul
                            </div>
                            <div class="mobile-card-mahasiswa">
                                ${escapeHtml(item.pengusul || '')}
                                <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                            </div>
                            <div class="mobile-card-prodi">
                                <i class="fas fa-graduation-cap"></i>
                                ${escapeHtml(displayProdi)}
                            </div>
                        </div>
                        
                        <div class="mobile-card-footer">
                            <div class="mobile-card-date">
                                <i class="fas fa-calendar-check text-blue-500"></i>
                                ${tgl}
                            </div>
                            <a href="/docutrack/public/verifikator/telaah/show/${item.id}?ref=riwayat-verifikasi" 
                               class="bg-blue-600 text-white px-3 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2"
                               onclick="event.stopPropagation()">
                               <i class="fas fa-eye"></i>
                               Detail
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
        }
        
        // Update Pagination Info
        if (paginationInfo) {
            const showingFrom = totalItems > 0 ? start + 1 : 0;
            const showingTo = totalItems > 0 ? Math.min(end, totalItems) : 0;
            paginationInfo.innerHTML = `Menampilkan <span class="font-semibold">${showingFrom}</span> s.d. <span class="font-semibold">${showingTo}</span> dari <span class="font-semibold">${totalItems}</span> data`;
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
    resetButton?.addEventListener('click', resetFilters);
    
    paginationContainer?.addEventListener('click', function(e) {
        const btn = e.target.closest('.pagination-btn');
        if (btn && !btn.disabled) {
            const page = parseInt(btn.dataset.page);
            if (page >= 1 && page !== currentPage) {
                currentPage = page;
                render();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });
    
    render();
});
</script>