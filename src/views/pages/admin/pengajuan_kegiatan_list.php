<?php
// File: src/views/pages/admin/pengajuan_kegiatan_list.php

// Variabel $list_kegiatan diasumsikan dikirim dari AdminPengajuanKegiatanController
if (!isset($list_kegiatan)) {
    $list_kegiatan = [];
}

// Extract unique jurusan for filter
$jurusan_list = array_unique(array_filter(array_column($list_kegiatan, 'jurusan')));
sort($jurusan_list);
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-6 md:mb-8 flex flex-col">
        
        <!-- Header Section -->
        <div class="flex flex-col justify-start mb-4 md:mb-6 pb-4 md:pb-5 border-b border-gray-200 gap-3">
            <div>
                <h2 class="text-lg md:text-2xl font-bold text-gray-800">List Pengajuan Kegiatan</h2>
            </div>
            
            <!-- Filter Controls - Stack on Mobile -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                <!-- Jurusan Filter -->
                <div class="relative flex-1">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs sm:text-sm"></i>
                    <select id="filter-jurusan" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>" style="color: #374151 !important; font-weight: 600;"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>

                <!-- Reset Button -->
                <button id="reset-filter" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative">
                <i class="fas fa-search absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-400 z-10 text-xs sm:text-sm"></i>
                <input type="text" id="search-kegiatan-input" placeholder="Cari Kegiatan atau Mahasiswa..."
                       class="w-full pl-9 sm:pl-11 pr-4 py-2 sm:py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                       aria-label="Cari Kegiatan">
            </div>
        </div>

        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="w-full min-w-[800px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-body-desktop" class="divide-y divide-gray-100 bg-white">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Visible on Mobile Only) -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-kegiatan-list" class="space-y-3">
                <!-- Mobile cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination - Responsive -->
        <div class="p-3 sm:p-4 mt-4 border-t border-gray-200 bg-gray-50 rounded-lg flex-shrink-0">
            <div class="flex flex-col gap-3">
                <div id="pagination-buttons" class="flex gap-1 flex-wrap justify-center"></div>
                <div class="text-xs sm:text-sm text-gray-600 text-center">
                    Menampilkan <span id="showing-start" class="font-semibold text-gray-800">0</span> s.d. 
                    <span id="showing-end" class="font-semibold text-gray-800">0</span> dari 
                    <span id="total-records" class="font-semibold text-gray-800">0</span> data
                </div>
            </div>
        </div>

    </section>

    <!-- Debug Info Section (if available) -->
    <?php if (empty($list_kegiatan) && isset($debug_info)): ?>
    <section class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4 md:p-6 mb-6 md:mb-8">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl md:text-2xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h3 class="text-base md:text-lg font-bold text-yellow-800 mb-2">Debug Information</h3>
                <div class="bg-white rounded-lg p-3 md:p-4 text-xs md:text-sm font-mono text-yellow-900 space-y-1">
                    <p><span class="font-semibold">Role:</span> <?= htmlspecialchars($debug_info['role'] ?? '-') ?></p>
                    <p><span class="font-semibold">Jurusan:</span> <?= htmlspecialchars($debug_info['jurusan'] ?? '-') ?></p>
                    <p><span class="font-semibold">Total Data (DB):</span> <?= $debug_info['total_raw'] ?? 0 ?></p>
                    <p><span class="font-semibold">Lolos Filter:</span> <?= $debug_info['total_filtered'] ?? 0 ?></p>
                </div>
                <p class="text-xs md:text-sm text-yellow-700 mt-3 italic">
                    *Pastikan jurusan akun Anda sama dengan jurusan di usulan.
                </p>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<style>
    /* Mobile Card Styling */
    .mobile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-left: 4px solid #3b82f6;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .mobile-card:active {
        transform: scale(0.98);
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
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .mobile-card-date {
        font-size: 0.8rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        color: white;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    .mobile-card-btn:active {
        opacity: 0.9;
        transform: scale(0.98);
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
    
    .status-siap {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        border: 1px solid #93c5fd;
    }
    
    .status-proses {
        background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
        color: #6b21a8;
        border: 1px solid #c084fc;
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
    
    /* Pagination Buttons */
    .pagination-btn {
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        transition: all 0.2s;
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: transparent;
    }
    
    .pagination-btn:not(:disabled):hover:not(.active) {
        background: #f3f4f6;
    }
    
    .pagination-btn:not(:disabled):active {
        transform: scale(0.95);
    }
</style>

<script>
// Data dari PHP
window.kegiatanData = <?php echo json_encode($list_kegiatan); ?>;

document.addEventListener('DOMContentLoaded', () => {
    const allData = window.kegiatanData || [];
    const ROWS_PER_PAGE = 5;
    
    let filteredData = [...allData];
    let currentPage = 1;
    
    const searchInput = document.getElementById('search-kegiatan-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const resetButton = document.getElementById('reset-filter');
    
    const tableBodyDesktop = document.getElementById('table-body-desktop');
    const mobileList = document.getElementById('mobile-kegiatan-list');
    const paginationButtons = document.getElementById('pagination-buttons');
    
    const showingStart = document.getElementById('showing-start');
    const showingEnd = document.getElementById('showing-end');
    const totalRecords = document.getElementById('total-records');

    // Filter function
    function applyFilters() {
        const searchText = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const jurusanFilter = filterJurusan ? filterJurusan.value : '';
        
        filteredData = allData.filter(item => {
            const namaKegiatan = (item.nama || '').toLowerCase();
            const namaMahasiswa = (item.nama_mahasiswa || item.pengusul || '').toLowerCase();
            const nim = (item.nim || '').toLowerCase();
            const jurusan = (item.jurusan || '');
            
            const searchMatch = !searchText || 
                namaKegiatan.includes(searchText) || 
                namaMahasiswa.includes(searchText) ||
                nim.includes(searchText);
            
            const jurusanMatch = !jurusanFilter || jurusan === jurusanFilter;
            
            return searchMatch && jurusanMatch;
        });
        
        currentPage = 1;
        render();
    }

    // Reset filters
    function resetFilters() {
        if (searchInput) searchInput.value = '';
        if (filterJurusan) filterJurusan.value = '';
        currentPage = 1;
        applyFilters();
    }

    // Render function
    function render() {
        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
        
        // Render Desktop Table
        if (tableBodyDesktop) {
            if (pageData.length === 0) {
                tableBodyDesktop.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-10">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <div class="empty-state-text">${allData.length === 0 ? 'Belum ada kegiatan yang perlu dilengkapi.' : 'Data tidak ditemukan.'}</div>
                                <div class="empty-state-subtext">${allData.length === 0 ? 'Kegiatan yang disetujui Verifikator akan muncul di sini.' : 'Coba ubah filter atau kata kunci pencarian Anda'}</div>
                            </div>
                        </td>
                    </tr>`;
            } else {
                tableBodyDesktop.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    const tglPengajuan = item.tanggal_pengajuan ? 
                        new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';
                    
                    const statusId = parseInt(item.statusUtamaId || 0);
                    const posisiId = parseInt(item.posisi || 0);
                    const isReady = (posisiId === 1 && statusId === 3);
                    
                    const namaMahasiswa = item.nama_mahasiswa || item.pengusul || 'N/A';
                    
                    return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama || 'Tanpa Judul')}</span>
                                <span class="text-gray-600 text-xs">
                                    ${escapeHtml(namaMahasiswa)}
                                    <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                                </span>
                                <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                    <i class="fas fa-graduation-cap mr-1"></i>${escapeHtml(item.prodi || item.jurusan || '-')}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-blue-500 text-xs"></i>
                                ${tglPengajuan}
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                            ${isReady ? 
                                `<span class="px-3 py-1.5 rounded-full text-blue-700 bg-blue-100 border border-blue-200 inline-flex items-center gap-1.5">
                                    <i class="fas fa-edit"></i>
                                    Siap Dilengkapi
                                </span>` :
                                `<span class="px-3 py-1.5 rounded-full text-purple-700 bg-purple-100 border border-purple-200 inline-flex items-center gap-1.5">
                                    <i class="fas fa-info-circle"></i>
                                    ${escapeHtml(item.status || 'Proses')}
                                </span>`
                            }
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                            <a href="/docutrack/public/admin/pengajuan-kegiatan/show/${item.id || 0}?mode=rincian"
                               class="bg-blue-600 text-white px-4 py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors shadow-sm inline-flex items-center gap-2">
                                <i class="fas fa-pen"></i> Lengkapi
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
                        <div class="empty-state-text">${allData.length === 0 ? 'Belum ada kegiatan yang perlu dilengkapi.' : 'Data tidak ditemukan.'}</div>
                        <div class="empty-state-subtext">${allData.length === 0 ? 'Kegiatan yang disetujui Verifikator akan muncul di sini.' : 'Coba ubah filter atau kata kunci pencarian Anda'}</div>
                    </div>`;
            } else {
                mobileList.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    const tglPengajuan = item.tanggal_pengajuan ? 
                        new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';
                    
                    const statusId = parseInt(item.statusUtamaId || 0);
                    const posisiId = parseInt(item.posisi || 0);
                    const isReady = (posisiId === 1 && statusId === 3);
                    
                    const namaMahasiswa = item.nama_mahasiswa || item.pengusul || 'N/A';
                    
                    let statusClass = 'status-badge status-proses';
                    let statusIcon = 'fas fa-info-circle';
                    let statusText = item.status || 'Proses';
                    
                    if (isReady) {
                        statusClass = 'status-badge status-siap';
                        statusIcon = 'fas fa-edit';
                        statusText = 'Siap Dilengkapi';
                    }
                    
                    return `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="mobile-card-number">#${no}</div>
                            <span class="${statusClass}">
                                <i class="${statusIcon}"></i>
                                ${escapeHtml(statusText)}
                            </span>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-clipboard-list"></i>
                                Nama Kegiatan
                            </div>
                            <div class="mobile-card-kegiatan">${escapeHtml(item.nama || 'Tanpa Judul')}</div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-user"></i>
                                Pengusul
                            </div>
                            <div class="mobile-card-mahasiswa">
                                ${escapeHtml(namaMahasiswa)}
                                <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                            </div>
                            <div class="mobile-card-prodi">
                                <i class="fas fa-graduation-cap"></i>
                                ${escapeHtml(item.prodi || item.jurusan || '-')}
                            </div>
                        </div>
                        
                        <div class="mobile-card-footer">
                            <div class="mobile-card-date">
                                <i class="fas fa-calendar-alt text-blue-500"></i>
                                ${tglPengajuan}
                            </div>
                            <a href="/docutrack/public/admin/pengajuan-kegiatan/show/${item.id || 0}?mode=rincian" class="mobile-card-btn">
                                <i class="fas fa-pen"></i>
                                Lengkapi Data
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
        }
        
        // Update pagination info
        const totalItems = filteredData.length;
        const showStart = totalItems === 0 ? 0 : start + 1;
        const showEnd = Math.min(end, totalItems);
        
        if (showingStart) showingStart.textContent = showStart;
        if (showingEnd) showingEnd.textContent = showEnd;
        if (totalRecords) totalRecords.textContent = totalItems;
        
        // Render pagination
        renderPagination(totalPages);
    }

    // Render pagination
    function renderPagination(totalPages) {
        if (!paginationButtons) return;
        
        if (totalPages <= 1) {
            paginationButtons.innerHTML = '';
            return;
        }
        
        let html = '';
        
        // Previous button
        const prevDisabled = currentPage === 1;
        html += `<button class="pagination-btn ${prevDisabled ? 'disabled' : ''}" 
                        onclick="goToPage(${currentPage - 1})" 
                        ${prevDisabled ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>`;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                const isActive = i === currentPage;
                html += `<button class="pagination-btn ${isActive ? 'active' : ''}" 
                                onclick="goToPage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="px-2 text-gray-400 self-center">...</span>`;
            }
        }
        
        // Next button
        const nextDisabled = currentPage === totalPages;
        html += `<button class="pagination-btn ${nextDisabled ? 'disabled' : ''}" 
                        onclick="goToPage(${currentPage + 1})" 
                        ${nextDisabled ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>`;
        
        paginationButtons.innerHTML = html;
    }

    // Go to page function
    window.goToPage = function(page) {
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            render();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    // HTML escape function
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    // Event listeners
    if (searchInput) {
        let debounceTimer;
        searchInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(applyFilters, 300);
        });
    }
    
    if (filterJurusan) {
        filterJurusan.addEventListener('change', applyFilters);
    }
    
    if (resetButton) {
        resetButton.addEventListener('click', resetFilters);
    }

    // Initial render
    render();
});
</script>