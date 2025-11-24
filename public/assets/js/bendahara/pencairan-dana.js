document.addEventListener('DOMContentLoaded', function() {
    
    // Data dari PHP - Filter hanya status Menunggu
    const dataKAK = window.dataKAK.filter(item => item.status === 'Menunggu');

    // --- MAPPING DATA JURUSAN & PRODI PNJ ---
    const mapJurusanProdiPNJ = {
        "Teknik Informatika dan Komputer": [
            "Teknik Informatika", "Teknik Multimedia Digital", "Teknik Multimedia Jaringan", "Teknologi Industri Cetak Kemasan", "TIK"
        ],
        "Akuntansi": [
            "Akuntansi", "Keuangan dan Perbankan", "Manajemen Keuangan", "Akuntansi Keuangan"
        ],
        "Administrasi Niaga": [
            "Administrasi Bisnis", "MICE", "Bahasa Inggris untuk Komunikasi Bisnis", "Manajemen Pemasaran"
        ],
        "Teknik Sipil": [
            "Teknik Sipil", "Konstruksi Gedung", "Konstruksi Sipil", "Jalan dan Jembatan"
        ],
        "Teknik Mesin": [
            "Teknik Mesin", "Teknik Konversi Energi", "Alat Berat", "Manufaktur"
        ],
        "Teknik Elektro": [
            "Teknik Telekomunikasi", "Teknik Otomasi Listrik Industri", "Broadband Multimedia", "Instrumentasi dan Kontrol Industri"
        ],
        "Teknik Grafika dan Penerbitan": [
            "Teknik Grafika", "Penerbitan", "Desain Grafis", "Teknologi Industri Cetak Kemasan"
        ]
    };

    // Helper: Cek apakah sebuah prodi termasuk dalam jurusan yang dipilih
    function isProdiInJurusan(namaProdi, selectedJurusan) {
        if (!namaProdi || !selectedJurusan) return false;
        
        // 1. Cek langsung jika data backend sudah mengirim field 'jurusan' yang sesuai
        if (namaProdi === selectedJurusan) return true;

        // 2. Cek via mapping array
        const daftarProdi = mapJurusanProdiPNJ[selectedJurusan];
        if (daftarProdi) {
            return daftarProdi.some(p => namaProdi.toLowerCase().includes(p.toLowerCase()));
        }
        return false;
    }
    
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
            const selectedJurusan = this.filterJurusan.value; 

            this.filteredData = this.allData.filter(item => {
                // 1. Filter Pencarian Text
                const matchSearch = !searchTerm || 
                    item.nama.toLowerCase().includes(searchTerm) ||
                    (item.pengusul && item.pengusul.toLowerCase().includes(searchTerm)) ||
                    (item.nama_mahasiswa && item.nama_mahasiswa.toLowerCase().includes(searchTerm)) ||
                    item.nim.toLowerCase().includes(searchTerm);
                
                // 2. Filter Jurusan PNJ
                let matchJurusan = true;
                if (selectedJurusan) {
                    if (item.jurusan === selectedJurusan) {
                        matchJurusan = true;
                    } 
                    else if (item.prodi) {
                        matchJurusan = isProdiInJurusan(item.prodi, selectedJurusan);
                    }
                    else {
                        matchJurusan = false;
                    }
                }
                
                return matchSearch && matchJurusan;
            });

            this.filterJurusan.style.borderColor = selectedJurusan ? '#10b981' : '';
            this.searchInput.style.borderColor = searchTerm ? '#10b981' : '';
            
            this.render();
        }
        
        getStatusBadge(status) {
            const statusLower = status.toLowerCase();
            const badges = {
                'dana diberikan': 'text-green-700 bg-green-100 border border-green-200',
                'ditolak': 'text-red-700 bg-red-100 border border-red-200',
                'menunggu': 'text-blue-700 bg-blue-100 border border-blue-200'
            };
            
            const icons = {
                'dana diberikan': 'fa-check-circle',
                'ditolak': 'fa-times-circle',
                'menunggu': 'fa-hourglass-half'
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
                        <td colspan="5" class="px-6 py-12 text-center">
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
                
                // Ambil Nama Prodi (Prioritas field 'prodi', fallback ke 'jurusan')
                const displayProdi = item.prodi || item.jurusan || '-';

                let tglPengajuanDisplay = '-';
                if (item.tanggal_pengajuan) {
                    const tglPengajuan = new Date(item.tanggal_pengajuan);
                    tglPengajuanDisplay = tglPengajuan.toLocaleDateString('id-ID', { 
                        day: '2-digit', 
                        month: 'short', 
                        year: 'numeric' 
                    });
                }
                
                // PERUBAHAN TAMPILAN DISINI:
                // Format: Nama (NIM) - Prodi
                return `
                    <tr class='${rowClass} hover:bg-blue-50/50 transition-colors duration-150'>
                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium'>${rowNumber}.</td>
                        <td class='px-6 py-4 text-sm text-gray-800 font-medium'>
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900">${this.escapeHtml(item.nama)}</span>
                                <span class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-user-graduate mr-1 text-gray-400"></i>
                                    ${this.escapeHtml(namaMahasiswa)} (${this.escapeHtml(item.nim)}) - ${this.escapeHtml(displayProdi)}
                                </span>
                            </div>
                        </td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm text-gray-600'>${tglPengajuanDisplay}</td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm'>${this.getStatusBadge(item.status)}</td>
                        <td class='px-6 py-4 whitespace-nowrap text-sm font-medium'>
                            <div class='flex gap-2'>
                                <a href="${this.config.viewUrl}${item.id}?ref=pencairan-dana" 
                                   class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-1.5 shadow-sm'>
                                    <i class='fas fa-eye text-xs'></i>
                                    <span class='hidden sm:inline'>Review</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }
        
        escapeHtml(text) {
            if (!text) return '';
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
                <button onclick="window.kakTable.goToPage(${this.currentPage - 1})" 
                        ${this.currentPage === 1 ? 'disabled' : ''} 
                        class='px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                               ${this.currentPage === 1 
                                 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                 : 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-300 hover:border-blue-300'}'>
                    <i class='fas fa-chevron-left text-xs'></i>
                </button>
            `);
            
            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                    buttons.push(`
                        <button onclick="window.kakTable.goToPage(${i})" 
                                class='px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                                       ${i === this.currentPage 
                                         ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' 
                                         : 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-300 hover:border-blue-300'}'>
                            ${i}
                        </button>
                    `);
                } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                    buttons.push(`<span class='px-2 py-2 text-gray-400'>...</span>`);
                }
            }
            
            // Next button
            buttons.push(`
                <button onclick="window.kakTable.goToPage(${this.currentPage + 1})" 
                        ${this.currentPage === totalPages ? 'disabled' : ''} 
                        class='px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 
                               ${this.currentPage === totalPages 
                                 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                                 : 'bg-white text-gray-700 hover:bg-blue-50 border border-gray-300 hover:border-blue-300'}'>
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
    
    window.kakTable = new TableManager(dataKAK, 'table-kak', {
        type: 'kak',
        color: 'blue',
        tbodyId: 'tbody-kak',
        paginationId: 'pagination-kak',
        showingId: 'showing-kak',
        totalId: 'total-kak',
        searchId: 'search-kak',
        filterJurusanId: 'filter-jurusan-kak',
        resetBtnId: 'reset-filter-kak',
        viewUrl: '/docutrack/public/bendahara/pencairan-dana/show/'
    });
});