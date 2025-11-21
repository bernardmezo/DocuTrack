document.addEventListener('DOMContentLoaded', function() {
    
    // Data dari PHP
    const dataLPJ = window.dataLPJ;
    
    // Configuration
    const ITEMS_PER_PAGE = 5;
    
    // Table Manager Class
    class TableManager {
        constructor(data, tableId, config) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            this.config = config;
            
            this.tbody = document.getElementById(config.tbodyId);
            this.paginationContainer = document.getElementById(config.paginationId);
            this.showingSpan = document.getElementById(config.showingId);
            this.totalSpan = document.getElementById(config.totalId);
            this.searchInput = document.getElementById(config.searchId);
            this.filterJurusan = document.getElementById(config.filterJurusanId);
            this.resetBtn = document.getElementById(config.resetBtnId);
            
            this.init();
        }
        
        init() {
            this.render();
            this.attachEventListeners();
        }
        
        attachEventListeners() {
            // Search
            this.searchInput.addEventListener('input', () => {
                this.currentPage = 1;
                this.applyFilters();
            });
            
            // Filter Jurusan
            this.filterJurusan.addEventListener('change', () => {
                this.currentPage = 1;
                this.applyFilters();
            });
            
            // Reset
            this.resetBtn.addEventListener('click', () => {
                this.searchInput.value = '';
                this.filterJurusan.value = '';
                this.currentPage = 1;
                this.applyFilters();
            });
        }
        
        applyFilters() {
    const searchTerm = this.searchInput.value.toLowerCase();
    const jurusanFilter = this.filterJurusan.value;

    this.filteredData = this.allData.filter(item => {
        const matchSearch = !searchTerm || 
            item.nama.toLowerCase().includes(searchTerm) ||
            (item.pengusul && item.pengusul.toLowerCase().includes(searchTerm)) ||
            (item.nama_mahasiswa && item.nama_mahasiswa.toLowerCase().includes(searchTerm)) ||
            item.nim.toLowerCase().includes(searchTerm);
        
        const matchJurusan = !jurusanFilter || 
            item.jurusan === jurusanFilter;
        
        return matchSearch && matchJurusan;
    });

    // Highlight jurusan
    if (jurusanFilter) {
        this.filterJurusan.style.borderColor = '#10b981';
    } else {
        this.filterJurusan.style.borderColor = '';
    }

    // Highlight search
    if (searchTerm) {
        this.searchInput.style.borderColor = '#10b981';
    } else {
        this.searchInput.style.borderColor = '';
    }
    
    this.render();
}

        
        getStatusBadge(status) {
            const statusLower = status.toLowerCase();
            const badges = {
                'setuju': 'text-green-700 bg-green-100 border border-green-200',
                'revisi': 'text-orange-700 bg-orange-100 border border-orange-200',
                'menunggu': 'text-gray-700 bg-gray-100 border border-gray-200'
            };
            
            const icons = {
                'setuju': 'fa-check-circle',
                'revisi': 'fa-exclamation-triangle',
                'menunggu': 'fa-clock'
            };
            
            return `<span class='px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 ${badges[statusLower] || badges['menunggu']}'>
                <i class='fas ${icons[statusLower] || 'fa-question-circle'}'></i>
                ${status}
            </span>`;
        }
        
        renderTable() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            if (pageData.length === 0) {
                this.tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-inbox text-4xl text-gray-300"></i>
                                <p class="text-gray-500 font-medium">Tidak ada data yang ditemukan</p>
                                <p class="text-sm text-gray-400">Coba ubah filter atau kata kunci pencarian</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            this.tbody.innerHTML = pageData.map((item, index) => {
                const rowNumber = start + index + 1;
                const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-gray-50/50';
                
                // Nama mahasiswa
                const namaMahasiswa = item.nama_mahasiswa || item.pengusul || 'N/A';
                
                // Format tanggal pengajuan
                let tglPengajuanDisplay = '-';
                if (item.tanggal_pengajuan) {
                    const tglPengajuan = new Date(item.tanggal_pengajuan);
                    tglPengajuanDisplay = tglPengajuan.toLocaleDateString('id-ID', { 
                        day: '2-digit', 
                        month: 'short', 
                        year: 'numeric' 
                    });
                }
                
                // Format tenggat LPJ
                let tenggatDisplay = '-';
                if (item.tenggat_lpj) {
                    const tenggat = new Date(item.tenggat_lpj);
                    tenggatDisplay = tenggat.toLocaleDateString('id-ID', { 
                        day: '2-digit', 
                        month: 'short', 
                        year: 'numeric' 
                    });
                    
                    // Check if overdue
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    tenggat.setHours(0, 0, 0, 0);
                    
                    if (tenggat < today && item.status.toLowerCase() === 'menunggu') {
                        tenggatDisplay = `<span class="text-red-600 font-semibold">${tenggatDisplay} <i class="fas fa-exclamation-circle"></i></span>`;
                    }
                }
                
                return `
                    <tr class='${rowClass} hover:bg-green-50/50 transition-colors duration-150'>
                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium'>${rowNumber}.</td>
                        <td class='px-6 py-4 text-sm text-gray-800 font-medium'>
                            <div class="flex flex-col">
                                <span class="font-medium">${this.escapeHtml(item.nama)}</span>
                                <span class="text-xs text-gray-500 mt-1">${this.escapeHtml(namaMahasiswa)} (${this.escapeHtml(item.nim)}) - ${this.escapeHtml(item.jurusan)}</span>
                            </div>
                        </td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tglPengajuanDisplay}</td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tenggatDisplay}</td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getStatusBadge(item.status)}</td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                            <div class='flex gap-2'>
                                <a href="${this.config.viewUrl}${item.id}?ref=dashboard" 
                                   class='bg-green-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-green-700 transition-colors'>
                                    Review
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        renderPagination() {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            
            if (totalPages <= 1) {
                this.paginationContainer.innerHTML = '';
                return;
            }
            
            let buttons = [];
            
            // Previous button
            buttons.push(`
                <button onclick="window.lpjTable.goToPage(${this.currentPage - 1})" 
                        ${this.currentPage === 1 ? 'disabled' : ''} 
                        class='px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                               ${this.currentPage === 1 
                                 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                 : 'bg-white text-gray-700 hover:bg-green-50 border border-gray-300 hover:border-green-300'}'>
                    <i class='fas fa-chevron-left text-xs'></i>
                </button>
            `);
            
            // Page numbers with ellipsis logic
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                    buttons.push(`
                        <button onclick="window.lpjTable.goToPage(${i})" 
                                class='px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                       ${i === this.currentPage 
                                         ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md' 
                                         : 'bg-white text-gray-700 hover:bg-green-50 border border-gray-300 hover:border-green-300'}'>
                            ${i}
                        </button>
                    `);
                } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                    buttons.push(`<span class='px-2 py-2 text-gray-400'>...</span>`);
                }
            }
            
            // Next button
            buttons.push(`
                <button onclick="window.lpjTable.goToPage(${this.currentPage + 1})" 
                        ${this.currentPage === totalPages ? 'disabled' : ''} 
                        class='px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                               ${this.currentPage === totalPages 
                                 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                 : 'bg-white text-gray-700 hover:bg-green-50 border border-gray-300 hover:border-green-300'}'>
                    <i class='fas fa-chevron-right text-xs'></i>
                </button>
            `);
            
            this.paginationContainer.innerHTML = buttons.join('');
        }
        
        updateInfo() {
            const start = (this.currentPage - 1) * this.itemsPerPage + 1;
            const end = Math.min(start + this.itemsPerPage - 1, this.filteredData.length);
            
            this.showingSpan.textContent = this.filteredData.length > 0 ? `${start}-${end}` : '0';
            this.totalSpan.textContent = this.filteredData.length;
        }
        
        render() {
            this.renderTable();
            this.renderPagination();
            this.updateInfo();
        }
        
        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                this.currentPage = page;
                this.render();
                
                // Smooth scroll to table
                const section = this.tbody.closest('section');
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        }
    }
    
    // Initialize LPJ table and expose it globally
    window.lpjTable = new TableManager(dataLPJ, 'table-lpj', {
        type: 'lpj',
        color: 'green',
        tbodyId: 'tbody-lpj',
        paginationId: 'pagination-lpj',
        showingId: 'showing-lpj',
        totalId: 'total-lpj',
        searchId: 'search-lpj',
        filterJurusanId: 'filter-jurusan-lpj',
        resetBtnId: 'reset-filter-lpj',
        viewUrl: '/docutrack/public/admin/pengajuan-lpj/show/'
    });
});