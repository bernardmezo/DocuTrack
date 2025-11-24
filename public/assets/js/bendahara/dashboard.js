document.addEventListener('DOMContentLoaded', function() {
    
    // Data dari PHP
    const dataKAK = window.dataKAK;
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
            this.filterStatus = document.getElementById(config.filterStatusId);
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
            
            // Filter Status
            this.filterStatus.addEventListener('change', () => {
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
                this.filterStatus.value = '';
                this.filterJurusan.value = '';
                this.currentPage = 1;
                this.applyFilters();
            });
        }
        
        applyFilters() {
            const searchTerm = this.searchInput.value.toLowerCase();
            const statusFilter = this.filterStatus.value.toLowerCase();
            const jurusanFilter = this.filterJurusan.value;
            
            this.filteredData = this.allData.filter(item => {
                const matchSearch = !searchTerm || 
                    item.nama.toLowerCase().includes(searchTerm) ||
                    (item.pengusul && item.pengusul.toLowerCase().includes(searchTerm)) ||
                    (item.nama_mahasiswa && item.nama_mahasiswa.toLowerCase().includes(searchTerm)) ||
                    (item.prodi && item.prodi.toLowerCase().includes(searchTerm)) ||
                    item.nim.toLowerCase().includes(searchTerm);
                    
                const matchStatus = !statusFilter || 
                    item.status.toLowerCase() === statusFilter;
                    
                // Filter jurusan - akan menampilkan semua prodi dari jurusan tersebut
                const matchJurusan = !jurusanFilter || 
                    item.jurusan === jurusanFilter;
                
                return matchSearch && matchStatus && matchJurusan;
            });

            // Highlight filter Status
            if (statusFilter) {
                this.filterStatus.style.color = '#000';
            } else {
                this.filterStatus.style.backgroundColor = '';
                this.filterStatus.style.color = '';
            }

            // Highlight filter Jurusan
            if (jurusanFilter) {
                this.filterJurusan.style.color = '#000';
            } else {
                this.filterJurusan.style.backgroundColor = '';
                this.filterJurusan.style.color = '';
            }

            // Highlight search input
            if (searchTerm) {
                this.searchInput.style.borderColor = '#000';
            } else {
                this.searchInput.style.borderColor = '';
            }
            
            this.render();
        }
        
        getStatusBadge(status) {
            const statusLower = status.toLowerCase();
            const badges = {
                'disetujui': 'text-green-700 bg-green-100 border border-green-200',
                'dana diberikan': 'text-green-700 bg-green-300 border border-green-200',
                'ditolak': 'text-red-700 bg-red-100 border border-red-200',
                'revisi': 'text-yellow-700 bg-yellow-100 border border-yellow-200',
                'menunggu': 'text-blue-700 bg-blue-100 border border-blue-200',
                'telah direvisi': 'text-purple-700 bg-purple-100 border border-purple-200'
            };
            
            const icons = {
                'disetujui': 'fa-check-circle',
                'dana diberikan': 'fa-money-check-alt',
                'ditolak': 'fa-times-circle',
                'revisi': 'fa-pencil-alt',
                'menunggu': 'fa-hourglass-half',
                'telah direvisi': 'fa-edit'
            };
            
            return `<span class='px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 ${badges[statusLower] || badges['menunggu']}'>
                <i class='fas ${icons[statusLower] || 'fa-question-circle'}'></i>
                ${status}
            </span>`;
        }
        
        getTenggatDisplay(tenggatLpj, status) {
            if (!tenggatLpj || tenggatLpj === null || tenggatLpj === '') {
                return '<span class="text-gray-500 italic text-sm">Belum Ada Tenggat</span>';
            }
            
            // Parse tanggal dengan benar
            const tenggatDate = new Date(tenggatLpj.replace(' ', 'T'));
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            tenggatDate.setHours(0, 0, 0, 0);
            
            const diffTime = tenggatDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let badgeClass = '';
            let icon = '';
            let text = '';
            
            if (diffDays < 0) {
                badgeClass = 'bg-red-100 text-red-700';
                icon = 'fa-exclamation-circle';
                text = 'Terlambat ' + Math.abs(diffDays) + ' hari';
            } else if (diffDays === 0) {
                badgeClass = 'bg-orange-100 text-orange-700';
                icon = 'fa-clock';
                text = 'Hari ini';
            } else if (diffDays <= 3) {
                badgeClass = 'bg-orange-100 text-orange-700';
                icon = 'fa-clock';
                text = diffDays + ' hari lagi';
            } else if (diffDays <= 7) {
                badgeClass = 'bg-yellow-100 text-yellow-700';
                icon = 'fa-clock';
                text = diffDays + ' hari lagi';
            } else {
                badgeClass = 'bg-blue-100 text-blue-700';
                icon = 'fa-calendar-check';
                text = diffDays + ' hari lagi';
            }
            
            return `
                <span class="${badgeClass} px-2.5 py-1 rounded text-xs font-medium inline-flex items-center gap-1.5">
                    ${icon ? `<i class="fas ${icon}"></i>` : ''}
                    ${text}
                </span>
            `;
        }
        
        renderTable() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            // Tentukan colspan berdasarkan tipe tabel
            const colspan = this.config.type === 'lpj' ? '6' : '5';
            
            if (pageData.length === 0) {
                this.tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="px-6 py-12 text-center">
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
                
                // Nama mahasiswa - gunakan nama_mahasiswa untuk LPJ, pengusul untuk KAK
                const namaMahasiswa = item.nama_mahasiswa || item.pengusul || 'N/A';
                
                // Prodi - tampilkan prodi, bukan jurusan
                const prodi = item.prodi || item.jurusan || '-';
                
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
                
                // Untuk tabel LPJ - tambahkan kolom tenggat
                if (this.config.type === 'lpj') {
                    return `
                        <tr class='${rowClass} hover:bg-${this.config.color}-50/50 transition-colors duration-150'>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium'>${rowNumber}.</td>
                            <td class='px-6 py-4 text-sm text-gray-800 font-medium'>
                                <div class="flex flex-col">
                                    <span class="font-medium">${this.escapeHtml(item.nama)}</span>
                                    <span class="text-xs text-gray-500 mt-1">${this.escapeHtml(namaMahasiswa)} (${this.escapeHtml(item.nim)}), ${this.escapeHtml(prodi)}</span>
                                </div>
                            </td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tglPengajuanDisplay}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getTenggatDisplay(item.tenggat_lpj, item.status)}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getStatusBadge(item.status)}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                <div class='flex gap-2'>
                                    <a href="${this.config.viewUrl}${item.id}?ref=dashboard" 
                                       class='bg-${this.config.color}-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-${this.config.color}-700 transition-colors'>
                                        Review
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                } else {
                    // Untuk tabel KAK - tanpa kolom prodi
                    return `
                        <tr class='${rowClass} hover:bg-${this.config.color}-50/50 transition-colors duration-150'>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium'>${rowNumber}.</td>
                            <td class='px-6 py-4 text-sm text-gray-800 font-medium'>
                                <div class="flex flex-col">
                                    <span class="font-medium">${this.escapeHtml(item.nama)}</span>
                                    <span class="text-xs text-gray-500 mt-1">${this.escapeHtml(namaMahasiswa)} (${this.escapeHtml(item.nim)}), ${this.escapeHtml(prodi)}</span>
                                </div>
                            </td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tglPengajuanDisplay}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getStatusBadge(item.status)}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                <div class='flex gap-2'>
                                    <a href="${this.config.viewUrl}${item.id}?ref=dashboard" 
                                       class='bg-${this.config.color}-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-${this.config.color}-700 transition-colors'>
                                        Review
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                }
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
                <button onclick="tableManagers.${this.config.type}.goToPage(${this.currentPage - 1})" 
                        ${this.currentPage === 1 ? 'disabled' : ''} 
                        class='px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                               ${this.currentPage === 1 
                                 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                 : 'bg-white text-gray-700 hover:bg-' + this.config.color + '-50 border border-gray-300 hover:border-' + this.config.color + '-300'}'>
                    <i class='fas fa-chevron-left text-xs'></i>
                </button>
            `);
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                    buttons.push(`
                        <button onclick="tableManagers.${this.config.type}.goToPage(${i})" 
                                class='px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                       ${i === this.currentPage 
                                         ? 'bg-gradient-to-r from-' + this.config.color + '-500 to-' + this.config.color + '-600 text-white shadow-md' 
                                         : 'bg-white text-gray-700 hover:bg-' + this.config.color + '-50 border border-gray-300 hover:border-' + this.config.color + '-300'}'>
                            ${i}
                        </button>
                    `);
                } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                    buttons.push(`<span class='px-2 py-2 text-gray-400'>...</span>`);
                }
            }
            
            // Next button
            buttons.push(`
                <button onclick="tableManagers.${this.config.type}.goToPage(${this.currentPage + 1})" 
                        ${this.currentPage === totalPages ? 'disabled' : ''} 
                        class='px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                               ${this.currentPage === totalPages 
                                 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                 : 'bg-white text-gray-700 hover:bg-' + this.config.color + '-50 border border-gray-300 hover:border-' + this.config.color + '-300'}'>
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
    
    // Initialize tables
    window.tableManagers = {
        kak: new TableManager(dataKAK, 'table-kak', {
            type: 'kak',
            color: 'blue',
            tbodyId: 'tbody-kak',
            paginationId: 'pagination-kak',
            showingId: 'showing-kak',
            totalId: 'total-kak',
            searchId: 'search-kak',
            filterStatusId: 'filter-status-kak',
            filterJurusanId: 'filter-jurusan-kak',
            resetBtnId: 'reset-filter-kak',
            viewUrl: '/docutrack/public/bendahara/pencairan-dana/show/'
        }),
        lpj: new TableManager(dataLPJ, 'table-lpj', {
            type: 'lpj',
            color: 'green',
            tbodyId: 'tbody-lpj',
            paginationId: 'pagination-lpj',
            showingId: 'showing-lpj',
            totalId: 'total-lpj',
            searchId: 'search-lpj',
            filterStatusId: 'filter-status-lpj',
            filterJurusanId: 'filter-jurusan-lpj',
            resetBtnId: 'reset-filter-lpj',
            viewUrl: '/docutrack/public/bendahara/pengajuan-lpj/show/'
        })
    };
});