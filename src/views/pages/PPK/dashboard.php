<?php
// File: src/views/pages/PPK/dashboard.php

// Ambil data yang dikirim dari PPKDashboardController
$stats = $stats;
$list_usulan = $list_usulan ?? [];
$current_page = $current_page ?? 1;
$total_pages = $total_pages ?? 1;
$jurusan_list = $jurusan_list ?? [];
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white
                    bg-gradient-to-br from-blue-400 to-blue-500 
                    hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 
                    transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['total']); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-layer-group fa-xl"></i></div>
            </div>
        </div>

        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white
                    bg-gradient-to-br from-green-400 to-green-500 
                    hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 
                    transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['disetujui']); ?></h3><p class="text-sm font-medium opacity-80">Disetujui verifikator</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-check-circle fa-xl"></i></div>
            </div>
        </div>

        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-yellow-900 
                    bg-gradient-to-br from-yellow-300 to-yellow-400 
                    hover:shadow-[0_0_20px_rgba(250,204,21,0.5)] hover:-translate-y-1 
                    transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['menunggu']); ?></h3><p class="text-sm font-medium opacity-80">Menunggu Persetujuan</p></div>
                <div class="p-3 rounded-full bg-black/10 opacity-80 group-hover:opacity-100 transition-opacity text-yellow-800"><i class="fas fa-hourglass-half fa-xl"></i></div>
            </div>
        </div>
        
    </section>

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="flex flex-col p-6 border-b border-gray-200 flex-shrink-0 gap-4">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Usulan (Semua Status)</h3>
            
            <div class="flex flex-col lg:flex-row gap-3">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10"></i>
                    <input type="text" id="search-ppk-input" placeholder="Cari Nama Kegiatan..."
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
                
                <div class="relative w-full lg:w-52">
                    <i class="fas fa-filter absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                    <select id="filter-status" 
                            style="color: #374151 !important;"
                            class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Status</option>
                        <option value="menunggu" style="color: #374151 !important; font-weight: 600;">Menunggu</option>
                        <option value="disetujui verifikator" style="color: #374151 !important; font-weight: 600;">Disetujui Verifikator</option>
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
                <tbody id="ppk-table-body" class="divide-y divide-gray-100">
                    <?php if (empty($list_usulan)): ?>
                        <tr id="empty-row">
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                Tidak ada usulan untuk ditinjau.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $items_per_page = 5; // Sesuaikan dengan JS
                        $start_number = ($current_page - 1) * $items_per_page + 1;
                        $nomor = $start_number;
                        ?>
                        <?php foreach ($list_usulan as $item): 
                            $status_text = htmlspecialchars($item['status'] ?? 'N/A');
                            $status_lower = strtolower($status_text);

                            $status_class = match ($status_lower) {
                                'disetujui verifikator' => 'text-green-700 bg-green-100',
                                default => 'text-gray-600 bg-gray-100',
                            };
                            $icon_class = match ($status_lower) {
                                'disetujui verifikator' => 'fas fa-check-circle',
                                default => 'fas fa-hourglass-half',
                            };
                            
                            $row_class = ($status_lower === 'menunggu') ? 'bg-gray-50 font-medium' : 'bg-white';
                            
                            // Format tanggal
                            $tanggal = $item['tanggal_pengajuan'] ?? '-';
                            if ($tanggal !== '-') {
                                $date = new DateTime($tanggal);
                                $tanggal_formatted = $date->format('d M Y');
                            } else {
                                $tanggal_formatted = '-';
                            }
                        ?>
                            <tr class='ppk-row <?php echo $row_class; ?> hover:bg-gray-100 transition-colors' 
                                data-nama="<?php echo htmlspecialchars(strtolower($item['nama'])); ?>"
                                data-status="<?php echo htmlspecialchars($status_lower); ?>"
                                data-jurusan="<?php echo htmlspecialchars(strtolower($item['jurusan'] ?? '')); ?>">
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-700'><?php echo $nomor++; ?>.</td>
                                <td class='px-6 py-5 text-sm'>
                                    <div class="flex flex-col">
                                        <span class="font-semibold text-gray-900 mb-1"><?php echo htmlspecialchars($item['nama']); ?></span>
                                        <span class="text-gray-600 text-xs">
                                            <?php echo htmlspecialchars($item['pengusul']); ?>
                                            <span class="text-gray-500">(<?php echo htmlspecialchars($item['nim'] ?? '-'); ?>)</span>
                                        </span>
                                        <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                            <i class="fas fa-graduation-cap mr-1"></i><?php echo htmlspecialchars($item['prodi'] ?? '-'); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-600'>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                                        <?php echo $tanggal_formatted; ?>
                                    </div>
                                </td>
                                <td class='px-6 py-5 whitespace-nowrap text-xs font-semibold'>
                                    <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo $status_class; ?>'>
                                        <i class='<?php echo $icon_class; ?>'></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm font-medium'>
                                    <a href="/docutrack/public/ppk/telaah/show/<?php echo $item['id'] ?? ''; ?>?ref=dashboard" 
                                       class='bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2'>
                                        <i class="fas fa-eye"></i>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div id="pagination-ppk" class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 gap-4">
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
    
    class PPKTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('ppk-table-body');
            this.paginationContainer = document.getElementById('pagination-ppk');
            
            this.searchInput = document.getElementById('search-ppk-input');
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
                // FILTER TETAP MENGGUNAKAN 'JURUSAN' (Induk)
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
                const statusClass = (item.status || '').toLowerCase() === 'disetujui verifikator' ? 'text-purple-700 bg-purple-100 border border-purple-200' : 'text-gray-600 bg-gray-100';
                const iconClass = (item.status || '').toLowerCase() === 'disetujui verifikator' ? 'fa-check-circle' : 'fa-hourglass-half';
                const tgl = item.tanggal_pengajuan ? new Date(item.tanggal_pengajuan).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) : '-';

                // TAMPILAN KOLOM: MENGGUNAKAN PRODI (item.prodi), bukan item.jurusan
                return `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${no}.</td>
                    <td class="px-6 py-5 text-sm">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 mb-1">${item.nama || ''}</span>
                            <span class="text-gray-600 text-xs">${item.pengusul || ''} <span class="text-gray-400">(${item.nim || '-'})</span></span>
                            <span class="text-gray-500 text-xs mt-0.5 font-medium"><i class="fas fa-graduation-cap mr-1"></i>${item.prodi || '-'}</span>
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
                        <a href="/docutrack/public/ppk/telaah/show/${item.id}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
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

            let btns = '';
            // Prev Button
            btns += `<button onclick="ppkTable.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''} class="px-3 py-2 rounded-md text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"><i class="fas fa-chevron-left"></i></button>`;

            for (let i = 1; i <= totalPages; i++) {
                const active = i === this.currentPage ? 'bg-blue-600 text-white shadow-md border-blue-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
                btns += `<button onclick="ppkTable.goToPage(${i})" class="px-3 py-2 rounded-md text-sm font-medium border transition-colors ${active}">${i}</button>`;
            }

            // Next Button
            btns += `<button onclick="ppkTable.goToPage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''} class="px-3 py-2 rounded-md text-sm font-medium bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"><i class="fas fa-chevron-right"></i></button>`;

            this.paginationContainer.innerHTML = `
                <div class="text-sm text-gray-600">Halaman ${this.currentPage} dari ${totalPages}</div>
                <div class="flex gap-1">${btns}</div>
            `;
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                this.currentPage = page;
                this.render();
            }
        }
    }
    
    window.ppkTable = new PPKTableManager(dataUsulan);
});
</script>