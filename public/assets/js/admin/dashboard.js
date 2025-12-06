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
                    
                // Filter berdasarkan JURUSAN (bukan prodi)
                const matchJurusan = !jurusanFilter || 
                    item.jurusan === jurusanFilter;
                
                return matchSearch && matchStatus && matchJurusan;
            });

            // Highlight filter Status
            if (statusFilter) {
                this.filterStatus.style.fontWeight = '500';
                this.filterStatus.style.borderColor = '#3b82f6';
            } else {
                this.filterStatus.style.fontWeight = 'normal';
                this.filterStatus.style.borderColor = '';
            }

            // Highlight filter Jurusan
            if (jurusanFilter) {
                this.filterJurusan.style.fontWeight = '500';
                this.filterJurusan.style.borderColor = '#3b82f6';
            } else {
                this.filterJurusan.style.fontWeight = 'normal';
                this.filterJurusan.style.borderColor = '';
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

                // TAMBAHAN BARU: Status Disetujui Verifikator (UNGU)
                // 'disetujui verifikator': 'text-purple-700 bg-purple-100 border border-purple-200',

                'setuju': 'text-green-700 bg-green-100 border border-green-200',
                'ditolak': 'text-red-700 bg-red-100 border border-red-200',
                'revisi': 'text-yellow-700 bg-yellow-100 border border-yellow-200',
                'menunggu': 'text-gray-700 bg-gray-100 border border-gray-200',
                'menunggu_upload': 'text-orange-700 bg-orange-100 border border-orange-200',
                'dana diberikan': 'text-blue-700 bg-blue-100 border border-blue-200'
            };
            
            const icons = {
                'disetujui': 'fa-check-circle',
                'disetujui verifikator': 'fa-check-double',
                'setuju': 'fa-check-circle',
                'ditolak': 'fa-times-circle',
                'revisi': 'fa-edit',
                'menunggu': 'fa-clock',
                'menunggu_upload': 'fa-upload',
                'dana diberikan': 'fa-hand-holding-usd'
            };
            
            const displayText = {
                'disetujui': 'Disetujui',
                'disetujui verifikator': 'Disetujui Verifikator', // Teks yang akan tampil
                'setuju': 'Disetujui',
                'ditolak': 'Ditolak',
                'revisi': 'Revisi',
                'menunggu': 'Menunggu',
                'menunggu_upload': 'Perlu Upload',
                'dana diberikan': 'Dana Diberikan'
            };
            
            return `<span class='px-3 py-1.5 rounded-full text-xs font-semibold inline-flex items-center gap-1.5 ${badges[statusLower] || badges['menunggu']}'>
                <i class='fas ${icons[statusLower] || 'fa-question-circle'}'></i>
                ${displayText[statusLower] || status}
            </span>`;
        }
        
        calculateDeadline(item) {
            if (this.config.type !== 'lpj') return '';
            
            const status = item.status.toLowerCase();
            
            // Untuk status menunggu_upload
            if (status === 'menunggu_upload') {
                return '<span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-orange-50 border border-orange-200 text-orange-700"><i class="fas fa-upload"></i><span>Perlu Upload Bukti</span></span>';
            }
            
            // Hanya hitung deadline jika status 'setuju' atau 'disetujui'
            if (status !== 'setuju' && status !== 'disetujui') {
                return '<span class="text-gray-400 text-xs italic">Menunggu Persetujuan</span>';
            }
            
            const tglPengajuan = new Date(item.tanggal_pengajuan);
            const deadline = new Date(tglPengajuan);
            deadline.setDate(deadline.getDate() + 14);
            
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            deadline.setHours(0, 0, 0, 0);
            
            const diffTime = deadline - today;
            const sisaHari = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            let badgeClass, icon, textStatus;
            
            if (sisaHari < 0) {
                badgeClass = 'bg-red-100 text-red-700';
                icon = 'fa-exclamation-circle';
                textStatus = `Terlewat ${Math.abs(sisaHari)} hari`;
            } else if (sisaHari === 0) {
                badgeClass = 'bg-red-100 text-red-700';
                icon = 'fa-bell';
                textStatus = 'Hari Ini!';
            } else if (sisaHari <= 3) {
                badgeClass = 'bg-orange-100 text-orange-700';
                icon = 'fa-hourglass-end';
                textStatus = `Sisa ${sisaHari} hari`;
            } else if (sisaHari <= 7) {
                badgeClass = 'bg-blue-100 text-blue-700';
                icon = 'fa-hourglass-half';
                textStatus = `Sisa ${sisaHari} hari`;
            } else {
                badgeClass = 'bg-green-100 text-green-700';
                icon = 'fa-calendar-check';
                textStatus = `Sisa ${sisaHari} hari`;
            }
            
            return `
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ${badgeClass} w-fit">
                    <i class="fas ${icon}"></i> ${textStatus}
                </span>
            `;
        }
        
        renderTable() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            // KAK = 5 kolom, LPJ = 6 kolom
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
                
                const namaMahasiswa = item.nama_mahasiswa || item.pengusul || 'N/A';
                
                // PERUBAHAN UTAMA: Tampilkan PRODI di kolom
                const prodi = item.prodi || item.jurusan || 'N/A';
                
                let tglPengajuanDisplay = '-';
                if (item.tanggal_pengajuan) {
                    const tglPengajuan = new Date(item.tanggal_pengajuan);
                    tglPengajuanDisplay = tglPengajuan.toLocaleDateString('id-ID', { 
                        day: '2-digit', 
                        month: 'short', 
                        year: 'numeric' 
                    });
                }
                
                // Tentukan label tombol berdasarkan status
                let buttonLabel = 'Lihat';
                const status = item.status.toLowerCase();
                if (this.config.type === 'lpj' && status === 'menunggu_upload') {
                    buttonLabel = 'Upload Bukti';
                }
                
                if (this.config.type === 'lpj') {
                    // TABEL LPJ - 6 KOLOM (No, Nama Kegiatan, Tgl Pengajuan, Tenggat LPJ, Status, Aksi)
                    return `
                        <tr class='${rowClass} hover:bg-${this.config.color}-50/50 transition-colors duration-150'>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium'>${rowNumber}.</td>
                            <td class='px-6 py-5 text-sm'>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 mb-1">${this.escapeHtml(item.nama)}</span>
                                    <span class="text-gray-600 text-xs">
                                        ${this.escapeHtml(namaMahasiswa)} 
                                        <span class="text-gray-500">(${this.escapeHtml(item.nim)})</span>
                                    </span>
                                    <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                        <i class="fas fa-graduation-cap mr-1"></i>${this.escapeHtml(prodi)}
                                    </span>
                                </div>
                            </td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tglPengajuanDisplay}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.calculateDeadline(item)}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getStatusBadge(item.status)}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                            <div class='flex gap-2'>
                                <a href="${this.config.viewUrl}${item.id}?ref=dashboard" 
                                class='inline-flex items-center gap-2 bg-${this.config.color}-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-${this.config.color}-700 focus:outline-none focus:ring-2 focus:ring-${this.config.color}-500 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow-md'>
                                    <i class="fas fa-eye"></i>
                                    <span>${buttonLabel}</span>
                                </a>
                            </div>
                        </td>
                        </tr>
                    `;
                } else {
                    // TABEL KAK - 5 KOLOM (No, Nama Kegiatan, Tgl Pengajuan, Status, Aksi)
                    return `
                        <tr class='${rowClass} hover:bg-${this.config.color}-50/50 transition-colors duration-150'>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium'>${rowNumber}.</td>
                            <td class='px-6 py-5 text-sm'>
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 mb-1">${this.escapeHtml(item.nama)}</span>
                                    <span class="text-gray-600 text-xs">
                                        ${this.escapeHtml(namaMahasiswa)} 
                                        <span class="text-gray-500">(${this.escapeHtml(item.nim)})</span>
                                    </span>
                                    <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                        <i class="fas fa-graduation-cap mr-1"></i>${this.escapeHtml(prodi)}
                                    </span>
                                </div>
                            </td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tglPengajuanDisplay}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getStatusBadge(item.status)}</td>
                            <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                                <div class='flex gap-2'>
                                    <a href="${this.config.viewUrl}${item.id}?ref=dashboard" 
                                    class='inline-flex items-center gap-2 bg-${this.config.color}-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-${this.config.color}-700 focus:outline-none focus:ring-2 focus:ring-${this.config.color}-500 focus:ring-offset-1 transition-all duration-200 shadow-sm hover:shadow-md'>
                                        <i class="fas fa-eye"></i>
                                        <span>Lihat</span>
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
            viewUrl: '/docutrack/public/admin/detail-kak/show/'
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
            viewUrl: '/docutrack/public/admin/pengajuan-lpj/show/'
        })
    };
    
    window.deleteItem = function(id, type) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-lg',
                    cancelButton: 'rounded-lg'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: 'Data berhasil dihapus.',
                        icon: 'success',
                        confirmButtonColor: '#3b82f6',
                        customClass: {
                            popup: 'rounded-xl',
                            confirmButton: 'rounded-lg'
                        }
                    });
                }
            });
        } else {
            if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
                alert('Data berhasil dihapus!');
            }
        }
    };
});