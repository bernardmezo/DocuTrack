<?php
// File: src/views/pages/PPK/pengajuan_kegiatan.php

$list_usulan = $list_usulan ?? [];
$jurusan_list = $jurusan_list ?? [];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="stage-list" class="stage-content bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <!-- Header Section -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <!-- Title and Description -->
            <div class="mb-4 sm:mb-5">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-blue-600"></i>
                    <span>Antrian Verifikasi (Persetujuan PPK)</span>
                </h2>
            </div>

            <!-- Filter Controls - Stack on Mobile -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3 sm:mb-4">
                <!-- Jurusan Filter -->
                <div class="relative flex-1">
                    <select id="filter-jurusan" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?php echo htmlspecialchars($jurusan); ?>" style="color: #374151 !important; font-weight: 600;"><?php echo htmlspecialchars($jurusan); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-500 pointer-events-none z-10 text-xs sm:text-sm"></i>
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
                <i class="fas fa-search absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 z-10 text-xs sm:text-sm"></i>
                <input type="text" id="search-kegiatan-input" placeholder="Cari nama kegiatan, pengusul, atau NIM..."
                       class="w-full pl-9 sm:pl-10 pr-4 py-2 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                       aria-label="Cari Kegiatan">
            </div>
        </div>
        
        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 600px;">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Kegiatan & Pengusul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="kegiatan-table-body" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Visible on Mobile Only) -->
        <div class="md:hidden overflow-y-auto" style="max-height: 600px;">
            <div id="mobile-kegiatan-list" class="p-3 space-y-3">
                <!-- Mobile cards will be populated by JavaScript -->
            </div>
        </div>
        
        <!-- Pagination - Responsive -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-kegiatan" class="font-semibold text-gray-800">0</span> dari <span id="total-kegiatan" class="font-semibold text-gray-800">0</span> data
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
    
    .status-menunggu {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    
    .status-disetujui-verifikator {
        background: linear-gradient(135deg, #e9d5ff 0%, #d8b4fe 100%);
        color: #6b21a8;
        border: 1px solid #c084fc;
    }
    
    .status-default {
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        color: #374151;
        border: 1px solid #9ca3af;
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
    
    /* Pagination Button Styling */
    .pagination-btn {
        min-width: 2rem;
        height: 2rem;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        background-color: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    .pagination-btn:hover:not(:disabled) {
        background-color: #f3f4f6;
        border-color: #d1d5db;
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: transparent;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .pagination-btn:active:not(:disabled) {
        transform: scale(0.95);
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
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .pagination-btn {
            min-width: 1.75rem;
            height: 1.75rem;
            font-size: 0.75rem;
        }
    }
</style>

<script>
    window.allDataUsulan = <?php echo json_encode($list_usulan); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    const dataRaw = window.allDataUsulan || [];
    const ITEMS_PER_PAGE = 5; 
    
    class KegiatanTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('kegiatan-table-body');
            this.mobileList = document.getElementById('mobile-kegiatan-list');
            this.paginationContainer = document.getElementById('pagination-container');
            this.showingSpan = document.getElementById('showing-kegiatan');
            this.totalSpan = document.getElementById('total-kegiatan');
            
            this.searchInput = document.getElementById('search-kegiatan-input');
            this.filterJurusan = document.getElementById('filter-jurusan');
            this.resetBtn = document.getElementById('reset-filter');
            
            if (this.tbody) this.init();
        }
        
        init() {
            this.render();
            this.attachEvents();
        }
        
        attachEvents() {
            const update = () => { this.currentPage = 1; this.filter(); };
            if(this.searchInput) this.searchInput.addEventListener('input', update);
            if(this.filterJurusan) this.filterJurusan.addEventListener('change', update);
            
            if(this.resetBtn) {
                this.resetBtn.addEventListener('click', () => {
                    if(this.searchInput) this.searchInput.value = '';
                    if(this.filterJurusan) this.filterJurusan.value = '';
                    this.currentPage = 1;
                    this.filter();
                });
            }
        }
        
        filter() {
            const search = this.searchInput ? this.searchInput.value.toLowerCase() : '';
            const jurusan = this.filterJurusan ? this.filterJurusan.value.toLowerCase() : '';
            
            this.filteredData = this.allData.filter(item => {
                const matchSearch = !search || 
                    (item.nama && item.nama.toLowerCase().includes(search)) ||
                    (item.pengusul && item.pengusul.toLowerCase().includes(search)) ||
                    (item.nim && item.nim.toLowerCase().includes(search));
                const matchJurusan = !jurusan || (item.jurusan && item.jurusan.toLowerCase() === jurusan);
                
                return matchSearch && matchJurusan;
            });
            this.render();
        }
        
        render() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            // Update counter
            if(this.showingSpan) this.showingSpan.textContent = pageData.length;
            if(this.totalSpan) this.totalSpan.textContent = this.filteredData.length;
            
            if (pageData.length === 0) {
                this.renderEmpty();
                this.renderPagination(0);
                return;
            }
            
            // Render Desktop Table
            if(this.tbody) {
                this.tbody.innerHTML = pageData.map((item, i) => {
                    const no = start + i + 1;
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';

                    // LOGIKA WARNA BADGE
                    const statusLower = (item.status || '').toLowerCase();
                    let badgeClass = 'text-gray-700 bg-gray-100 border-gray-200'; // Default
                    let iconClass = 'fa-hourglass-half';

                    if (statusLower === 'disetujui verifikator') {
                        badgeClass = 'text-purple-700 bg-purple-100 border-purple-200';
                        iconClass = 'fa-check-double';
                    } else if (statusLower === 'menunggu') {
                        badgeClass = 'text-yellow-700 bg-yellow-100 border-yellow-200';
                        iconClass = 'fa-clock';
                    }

                    return `
                    <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                        <td class="px-6 py-5 text-sm">
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 mb-1">${this.escapeHtml(item.nama)}</span>
                                <span class="text-gray-600 text-xs">
                                    ${this.escapeHtml(item.pengusul)}
                                    <span class="text-gray-500">(${this.escapeHtml(item.nim || '-')})</span>
                                </span>
                                <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                    <i class="fas fa-graduation-cap mr-1"></i>${this.escapeHtml(item.prodi || '-')}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-calendar-alt text-gray-400 text-xs"></i> ${tgl}
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border ${badgeClass}">
                                <i class="fas ${iconClass}"></i> ${this.escapeHtml(item.status)}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                            <a href="/docutrack/public/ppk/telaah/show/${item.id}?ref=pengajuan-kegiatan" 
                               class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-blue-700 hover:shadow-md transition-all">
                                <i class="fas fa-search"></i> Periksa
                            </a>
                        </td>
                    </tr>`;
                }).join('');
            }
            
            // Render Mobile Cards
            if(this.mobileList) {
                this.mobileList.innerHTML = pageData.map((item, i) => {
                    const no = start + i + 1;
                    const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';

                    // Status badge untuk mobile
                    const statusLower = (item.status || '').toLowerCase();
                    let statusClass = 'status-default';
                    let statusIcon = 'fa-hourglass-half';

                    if (statusLower === 'disetujui verifikator') {
                        statusClass = 'status-disetujui-verifikator';
                        statusIcon = 'fa-check-double';
                    } else if (statusLower === 'menunggu') {
                        statusClass = 'status-menunggu';
                        statusIcon = 'fa-clock';
                    }

                    return `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <span class="mobile-card-number">#${no}</span>
                            <span class="status-badge ${statusClass}">
                                <i class="fas ${statusIcon}"></i>
                                ${this.escapeHtml(item.status || 'Menunggu')}
                            </span>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-clipboard-list"></i>
                                Nama Kegiatan
                            </div>
                            <div class="mobile-card-kegiatan">${this.escapeHtml(item.nama)}</div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-user"></i>
                                Pengusul
                            </div>
                            <div class="mobile-card-pengusul">
                                ${this.escapeHtml(item.pengusul)} (${this.escapeHtml(item.nim || '-')})
                            </div>
                            <div class="mobile-card-prodi">
                                <i class="fas fa-graduation-cap"></i>
                                ${this.escapeHtml(item.prodi || '-')}
                            </div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-calendar-alt"></i>
                                Tanggal Pengajuan
                            </div>
                            <div class="mobile-card-value">${tgl}</div>
                        </div>
                        
                        <div class="mobile-card-actions">
                            <a href="/docutrack/public/ppk/telaah/show/${item.id}?ref=pengajuan-kegiatan" class="mobile-card-btn mobile-card-btn-primary">
                                <i class="fas fa-search"></i>
                                Periksa
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
            
            this.renderPagination(Math.ceil(this.filteredData.length / this.itemsPerPage));
        }
        
        renderEmpty() {
            const emptyHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <div class="empty-state-text">Tidak ada antrian usulan saat ini.</div>
                    <div class="empty-state-subtext">Coba ubah filter atau kata kunci pencarian</div>
                </div>`;
            
            if(this.tbody) this.tbody.innerHTML = `<tr><td colspan="5">${emptyHTML}</td></tr>`;
            if(this.mobileList) this.mobileList.innerHTML = emptyHTML;
        }
        
        renderPagination(totalPages) {
            if (!this.paginationContainer) return;
            
            if (totalPages <= 1) {
                this.paginationContainer.innerHTML = '';
                return;
            }

            let btns = '';
            btns += `<button onclick="kegiatanTable.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''} class="pagination-btn"><i class="fas fa-chevron-left"></i></button>`;

            const maxVisible = 5;
            let startPage = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                btns += `<button onclick="kegiatanTable.goToPage(1)" class="pagination-btn">1</button>`;
                if (startPage > 2) btns += `<span class="px-2 text-sm text-gray-400">...</span>`;
            }

            for (let i = startPage; i <= endPage; i++) {
                const active = i === this.currentPage ? 'active' : '';
                btns += `<button onclick="kegiatanTable.goToPage(${i})" class="pagination-btn ${active}">${i}</button>`;
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) btns += `<span class="px-2 text-sm text-gray-400">...</span>`;
                btns += `<button onclick="kegiatanTable.goToPage(${totalPages})" class="pagination-btn">${totalPages}</button>`;
            }

            btns += `<button onclick="kegiatanTable.goToPage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''} class="pagination-btn"><i class="fas fa-chevron-right"></i></button>`;

            this.paginationContainer.innerHTML = btns;
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                this.currentPage = page;
                this.render();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
        
        escapeHtml(text) {
            if (!text) return '';
            return text.toString()
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    }
    
    window.kegiatanTable = new KegiatanTableManager(dataRaw);
});
</script>