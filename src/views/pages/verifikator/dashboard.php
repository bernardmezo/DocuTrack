<?php
// File: src/views/pages/verifikator/dashboard.php

// Ambil data dari controller
// $stats = $stats ?? ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'pending' => 0];
$list_usulan = $list_usulan ?? [];
$jurusan_list = $jurusan_list ?? [];
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['total']); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-layer-group fa-xl"></i></div>
            </div>
        </div>
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['disetujui']); ?></h3><p class="text-sm font-medium opacity-80">Disetujui</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-check-circle fa-xl"></i></div>
            </div>
        </div>
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['ditolak']); ?></h3><p class="text-sm font-medium opacity-80">Ditolak</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-times-circle fa-xl"></i></div>
            </div>
        </div>
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-yellow-900 bg-gradient-to-br from-yellow-300 to-yellow-400 hover:shadow-[0_0_20px_rgba(250,204,21,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['pending']); ?></h3><p class="text-sm font-medium opacity-80">Pending</p></div>
                <div class="p-3 rounded-full bg-black/10 opacity-80 group-hover:opacity-100 transition-opacity text-yellow-800"><i class="fas fa-hourglass-half fa-xl"></i></div>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="flex flex-col p-6 border-b border-gray-200 flex-shrink-0 gap-4">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Usulan Masuk (Semua Status)</h3>
            
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10"></i>
                    <input type="text" id="search-verifikator-input" placeholder="Cari Nama Kegiatan..."
                           class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                           aria-label="Cari Kegiatan">
                </div>
                
                <div class="relative w-full lg:w-80">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                    <select id="filter-jurusan" 
                            style="color: #374151 !important;"
                            class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?php echo htmlspecialchars(strtolower($jurusan)); ?>" style="color: #374151 !important; font-weight: 600;"><?php echo htmlspecialchars($jurusan); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>
                
                <div class="relative w-full lg:w-64">
                    <i class="fas fa-filter absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                    <select id="filter-status" 
                            style="color: #374151 !important;"
                            class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Status</option>
                        <option value="menunggu" style="color: #374151 !important; font-weight: 600;">Menunggu</option>
                        <option value="telah direvisi" style="color: #374151 !important; font-weight: 600;">Telah Direvisi</option>
                        <option value="disetujui" style="color: #374151 !important; font-weight: 600;">Disetujui</option>
                        <option value="ditolak" style="color: #374151 !important; font-weight: 600;">Ditolak</option>
                        <option value="revisi" style="color: #374151 !important; font-weight: 600;">Revisi (Menunggu Admin)</option>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full min-w-[900px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tanggal Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="verifikator-table-body" class="divide-y divide-gray-100">
                    </tbody>
            </table>
        </div>
        
        <div id="pagination-verifikator" class="flex flex-col md:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 gap-4 transition-all duration-300">
            </div>
    </section>
</main>

<script>
    // Mengirim data usulan dari PHP ke Variable Global JS
    window.dataUsulan = <?php echo json_encode($list_usulan ?? []); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const dataUsulan = window.dataUsulan || [];
    const ITEMS_PER_PAGE = 5;
    
    class VerifikatorTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('verifikator-table-body');
            this.paginationContainer = document.getElementById('pagination-verifikator');
            
            this.searchInput = document.getElementById('search-verifikator-input');
            this.filterStatus = document.getElementById('filter-status');
            this.filterJurusan = document.getElementById('filter-jurusan');
            
            if (this.tbody) this.init();
        }
        
        init() {
            this.render();
            this.attachEvents();
        }
        
        attachEvents() {
            const update = () => { this.currentPage = 1; this.filter(); };
            if(this.searchInput) this.searchInput.addEventListener('input', update);
            if(this.filterStatus) this.filterStatus.addEventListener('change', update);
            if(this.filterJurusan) this.filterJurusan.addEventListener('change', update);
        }
        
        filter() {
            const search = this.searchInput ? this.searchInput.value.toLowerCase() : '';
            const status = this.filterStatus ? this.filterStatus.value.toLowerCase() : '';
            const jurusan = this.filterJurusan ? this.filterJurusan.value.toLowerCase() : '';
            
            this.filteredData = this.allData.filter(item => {
                const matchSearch = !search || (item.nama && item.nama.toLowerCase().includes(search));
                const matchStatus = !status || (item.status && item.status.toLowerCase() === status);
                
                // PENTING: Filter tetap berdasarkan JURUSAN INDUK (item.jurusan)
                const matchJurusan = !jurusan || (item.jurusan && item.jurusan.toLowerCase() === jurusan);
                
                return matchSearch && matchStatus && matchJurusan;
            });
            this.render();
        }
        
        render() {
            if (!this.tbody) return;
            
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            if (pageData.length === 0) {
                this.tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Data tidak ditemukan.</td></tr>`;
                this.renderPagination(0);
                return;
            }
            
            this.tbody.innerHTML = pageData.map((item, i) => {
                const no = start + i + 1;
                
                // Helper untuk status
                const statusLower = (item.status || '').toLowerCase();
                let statusClass, iconClass, rowClass;
                
                switch (statusLower) {
                    case 'disetujui':
                        statusClass = 'text-green-700 bg-green-100'; iconClass = 'fa-check-circle'; rowClass = 'bg-white opacity-80'; break;
                    case 'ditolak':
                        statusClass = 'text-red-700 bg-red-100'; iconClass = 'fa-times-circle'; rowClass = 'bg-white opacity-80'; break;
                    case 'revisi':
                        statusClass = 'text-yellow-700 bg-yellow-100'; iconClass = 'fa-exclamation-triangle'; rowClass = 'bg-white'; break;
                    case 'telah direvisi':
                        statusClass = 'text-purple-700 bg-purple-100'; iconClass = 'fa-sync-alt'; rowClass = 'bg-purple-50 font-medium'; break;
                    default:
                        statusClass = 'text-gray-600 bg-gray-100'; iconClass = 'fa-hourglass-half'; rowClass = 'bg-gray-50 font-medium';
                }
                
                const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';

                // LOGIC TAMPILAN PRODI:
                // Kita mengambil data 'prodi' untuk ditampilkan di kolom tabel.
                // Jika 'prodi' kosong, fallback ke 'jurusan'.
                const displayProdi = item.prodi ? item.prodi : (item.jurusan || '-');

                return `
                <tr class="${rowClass} hover:bg-gray-100 transition-colors border-b border-gray-100 last:border-0">
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                    <td class="px-6 py-5 text-sm">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 mb-1">${item.nama || ''}</span>
                            <span class="text-gray-600 text-xs">${item.pengusul || ''} <span class="text-gray-500">(${item.nim || '-'})</span></span>
                            
                            <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                <i class="fas fa-graduation-cap mr-1"></i>${displayProdi}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                        <i class="fas fa-calendar-alt text-gray-400 text-xs mr-1"></i> ${tgl}
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${statusClass}">
                            <i class="fas ${iconClass}"></i> ${item.status || 'Menunggu'}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="/docutrack/public/verifikator/telaah/show/${item.id}?ref=dashboard" class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </td>
                </tr>`;
            }).join('');
            
            this.renderPagination(Math.ceil(this.filteredData.length / this.itemsPerPage));
        }
        
        renderPagination(totalPages) {
            if (!this.paginationContainer) return;
            this.paginationContainer.innerHTML = '';
            if (totalPages <= 1) return;

            // Bagian Kiri: Info
            let leftContent = `<div class="text-sm text-gray-500 font-medium">Halaman <span class="text-gray-900 font-bold">${this.currentPage}</span> dari <span class="text-gray-900 font-bold">${totalPages}</span></div>`;

            // Bagian Kanan: Tombol
            let rightContent = '<div class="flex items-center gap-2">';
            
            // Prev
            rightContent += `<button onclick="verifikatorTable.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''} class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg transition-all duration-200 flex items-center gap-1 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"><i class="fas fa-chevron-left text-xs"></i> Prev</button>`;

            // Numbers
            for (let i = 1; i <= totalPages; i++) {
                const active = i === this.currentPage ? 'bg-blue-600 text-white border-blue-600 shadow-sm' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50 hover:text-blue-600';
                rightContent += `<button onclick="verifikatorTable.goToPage(${i})" class="px-3 py-2 text-sm font-bold border rounded-lg transition-all duration-200 ${active}">${i}</button>`;
            }

            // Next
            rightContent += `<button onclick="verifikatorTable.goToPage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''} class="px-3 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg transition-all duration-200 flex items-center gap-1 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">Next <i class="fas fa-chevron-right text-xs"></i></button>`;
            
            rightContent += '</div>';

            this.paginationContainer.innerHTML = leftContent + rightContent;
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                this.currentPage = page;
                this.render();
            }
        }
    }
    
    window.verifikatorTable = new VerifikatorTableManager(dataUsulan);
});
</script>