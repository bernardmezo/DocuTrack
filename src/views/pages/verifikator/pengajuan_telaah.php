<?php
// File: src/views/pages/verifikator/pengajuan_telaah.php

// Pastikan variabel terdefinisi (Data dikirim dari Controller)
if (!isset($list_usulan)) { $list_usulan = []; }
$jurusan_list = $jurusan_list ?? [];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Enhanced Table with Filters & Pagination - RESPONSIVE -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <!-- Header Section -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <!-- Title and Filters Container -->
            <div class="flex flex-col gap-4">
                <!-- Title -->
                <div>
                    <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-clipboard-list text-blue-600"></i>
                        <span>Antrian Pengajuan Telaah</span>
                    </h3>
                </div>
                
                <!-- Filter Controls - Stack on Mobile -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <!-- Jurusan Filter -->
                    <div class="relative flex-1">
                        <select id="filter-jurusan"
                                style="color: #374151 !important; font-size: 14px !important;"
                                class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                            <?php 
                            sort($jurusan_list);
                            foreach ($jurusan_list as $jurusan): 
                            ?>
                                <option value="<?php echo htmlspecialchars(strtolower($jurusan)); ?>" style="color: #374151 !important;">
                                    <?php echo htmlspecialchars($jurusan); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <!-- Reset Button -->
                    <button id="reset-filter" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-kegiatan-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="min-w-full" id="table-usulan">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Kegiatan & Pengusul</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Tgl. Pengajuan</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kegiatan-table-body" class="divide-y divide-gray-100 bg-white">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Visible on Mobile Only) -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-usulan-list" class="p-3 space-y-3">
                <!-- Mobile cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination - Responsive -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-usulan" class="font-semibold text-gray-800">0</span> dari <span id="total-usulan" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-container" class="flex gap-1 flex-wrap justify-center"></div>
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
    }
    
    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
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
    
    .mobile-card-pengusul {
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
    
    .mobile-card-actions {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        gap: 0.5rem;
    }
    
    .mobile-card-btn {
        flex: 1;
        padding: 0.625rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
    
    .mobile-card-btn-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
    }
    
    .mobile-card-btn-primary:active {
        transform: scale(0.98);
    }
    
    .mobile-card-btn i {
        font-size: 0.875rem;
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
    
    .status-telah-direvisi {
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #6b21a8;
        border: 1px solid #d8b4fe;
    }
    
    .status-menunggu {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        border: 1px solid #93c5fd;
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
    window.allDataUsulan = <?php echo json_encode($list_usulan) ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Ambil Data & Konfigurasi
    const dataRaw = window.allDataUsulan || [];
    const ITEMS_PER_PAGE = 5;
    
    // 2. State Management
    let state = {
        data: dataRaw,
        filteredData: dataRaw,
        currentPage: 1,
        filters: {
            search: '',
            jurusan: ''
        }
    };

    // 3. DOM Elements
    const els = {
        search: document.getElementById('search-kegiatan-input'),
        jurusan: document.getElementById('filter-jurusan'),
        reset: document.getElementById('reset-filter'),
        tbody: document.getElementById('kegiatan-table-body'),
        mobileList: document.getElementById('mobile-usulan-list'),
        pagination: document.getElementById('pagination-container'),
        showing: document.getElementById('showing-usulan'),
        total: document.getElementById('total-usulan')
    };

    // 4. Helper Functions
    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        const d = new Date(dateString);
        if (isNaN(d.getTime())) return '-';
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
    }

    function getStatusInfo(status) {
        const statusLower = (status || '').toLowerCase();
        if (statusLower === 'telah direvisi') {
            return {
                class: 'status-telah-direvisi',
                icon: 'fas fa-sync-alt',
                text: 'Telah Direvisi'
            };
        }
        return {
            class: 'status-menunggu',
            icon: 'fas fa-hourglass-half',
            text: 'Menunggu'
        };
    }

    // 5. Filter Function
    function applyFilters() {
        const search = state.filters.search.toLowerCase();
        const jurusan = state.filters.jurusan.toLowerCase();

        state.filteredData = state.data.filter(item => {
            const namaMatch = (item.nama || '').toLowerCase().includes(search);
            const pengusulMatch = (item.pengusul || '').toLowerCase().includes(search);
            const nimMatch = (item.nim || '').toLowerCase().includes(search);
            
            const jurusanItem = (item.jurusan || '').toLowerCase();
            const jurusanMatch = jurusan === '' || jurusanItem === jurusan;

            return (namaMatch || pengusulMatch || nimMatch) && jurusanMatch;
        });

        state.currentPage = 1;
        render();
    }

    // 6. Render Functions
    function render() {
        renderDesktop();
        renderMobile();
        renderPagination();
        updateInfo();
    }

    function renderDesktop() {
        if (!els.tbody) return;

        const start = (state.currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = state.filteredData.slice(start, end);

        if (pageData.length === 0) {
            els.tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-10">
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <div class="empty-state-text">Tidak ada data yang ditemukan</div>
                            <div class="empty-state-subtext">Coba ubah filter atau kata kunci pencarian</div>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        els.tbody.innerHTML = pageData.map((item, index) => {
            const nomor = start + index + 1;
            const statusInfo = getStatusInfo(item.status);
            const tglFormatted = formatDate(item.tanggal_pengajuan);
            const displayProdi = item.prodi ? item.prodi : (item.jurusan || '-');

            return `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${nomor}.</td>
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
                            <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                            ${tglFormatted}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                        <span class="status-badge ${statusInfo.class}">
                            <i class="${statusInfo.icon}"></i> ${statusInfo.text}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="/docutrack/public/verifikator/telaah/show/${item.id}?ref=pengajuan-telaah" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-search"></i> Telaah
                        </a>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function renderMobile() {
        if (!els.mobileList) return;

        const start = (state.currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = state.filteredData.slice(start, end);

        if (pageData.length === 0) {
            els.mobileList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <div class="empty-state-text">Tidak ada data yang ditemukan</div>
                    <div class="empty-state-subtext">Coba ubah filter atau kata kunci pencarian</div>
                </div>
            `;
            return;
        }

        els.mobileList.innerHTML = pageData.map((item, index) => {
            const nomor = start + index + 1;
            const statusInfo = getStatusInfo(item.status);
            const tglFormatted = formatDate(item.tanggal_pengajuan);
            const displayProdi = item.prodi ? item.prodi : (item.jurusan || '-');

            return `
                <div class="mobile-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-number">#${nomor}</div>
                        <span class="status-badge ${statusInfo.class}">
                            <i class="${statusInfo.icon}"></i> ${statusInfo.text}
                        </span>
                    </div>
                    
                    <div class="mobile-card-row">
                        <div class="mobile-card-label">
                            <i class="fas fa-file-alt"></i> Nama Kegiatan
                        </div>
                        <div class="mobile-card-kegiatan">${escapeHtml(item.nama || '-')}</div>
                    </div>
                    
                    <div class="mobile-card-row">
                        <div class="mobile-card-label">
                            <i class="fas fa-user"></i> Pengusul
                        </div>
                        <div class="mobile-card-pengusul">
                            ${escapeHtml(item.pengusul || '-')} (${escapeHtml(item.nim || '-')})
                        </div>
                        <div class="mobile-card-prodi">
                            <i class="fas fa-graduation-cap"></i> ${escapeHtml(displayProdi)}
                        </div>
                    </div>
                    
                    <div class="mobile-card-row">
                        <div class="mobile-card-label">
                            <i class="fas fa-calendar-alt"></i> Tanggal Pengajuan
                        </div>
                        <div class="mobile-card-value">${tglFormatted}</div>
                    </div>
                    
                    <div class="mobile-card-actions">
                        <a href="/docutrack/public/verifikator/telaah/show/${item.id}?ref=pengajuan-telaah" 
                           class="mobile-card-btn mobile-card-btn-primary">
                            <i class="fas fa-search"></i> Telaah Usulan
                        </a>
                    </div>
                </div>
            `;
        }).join('');
    }

    function renderPagination() {
        if (!els.pagination) return;

        const totalPages = Math.ceil(state.filteredData.length / ITEMS_PER_PAGE);
        
        if (totalPages <= 1) {
            els.pagination.innerHTML = '';
            return;
        }

        let html = '';
        
        // Prev button
        html += `<button onclick="changePage(${state.currentPage - 1})" ${state.currentPage === 1 ? 'disabled' : ''} 
                 class="px-3 py-2 text-sm font-medium rounded-md transition-colors border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-left text-xs"></i>
                 </button>`;

        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            const isActive = i === state.currentPage;
            html += `<button onclick="changePage(${i})" 
                     class="px-3 py-2 text-sm font-medium rounded-md transition-colors border ${
                         isActive 
                         ? 'bg-blue-600 text-white border-blue-600 shadow-sm' 
                         : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                     }">${i}</button>`;
        }

        // Next button
        html += `<button onclick="changePage(${state.currentPage + 1})" ${state.currentPage === totalPages ? 'disabled' : ''} 
                 class="px-3 py-2 text-sm font-medium rounded-md transition-colors border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-chevron-right text-xs"></i>
                 </button>`;

        els.pagination.innerHTML = html;
    }

    function updateInfo() {
        const start = (state.currentPage - 1) * ITEMS_PER_PAGE;
        const end = Math.min(start + ITEMS_PER_PAGE, state.filteredData.length);
        const showing = state.filteredData.length > 0 ? end - start : 0;

        if (els.showing) els.showing.textContent = showing;
        if (els.total) els.total.textContent = state.filteredData.length;
    }

    // Make changePage global for onclick
    window.changePage = function(newPage) {
        const totalPages = Math.ceil(state.filteredData.length / ITEMS_PER_PAGE);
        if (newPage >= 1 && newPage <= totalPages) {
            state.currentPage = newPage;
            render();
            // Scroll to top on mobile
            if (window.innerWidth < 768) {
                document.querySelector('.main-content').scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    };

    // 7. Event Listeners
    if (els.search) {
        let debounceTimer;
        els.search.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                state.filters.search = e.target.value;
                applyFilters();
            }, 300);
        });
    }

    if (els.jurusan) {
        els.jurusan.addEventListener('change', (e) => {
            state.filters.jurusan = e.target.value;
            applyFilters();
        });
    }

    if (els.reset) {
        els.reset.addEventListener('click', () => {
            state.filters = { search: '', jurusan: '' };
            if (els.search) els.search.value = '';
            if (els.jurusan) els.jurusan.value = '';
            applyFilters();
        });
    }

    // 8. Init
    applyFilters();
});
</script>