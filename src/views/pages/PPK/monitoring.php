<?php
// File: src/views/pages/PPK/monitoring.php

if (!isset($list_proposal)) { $list_proposal = []; }
if (!isset($list_jurusan)) { $list_jurusan = []; }
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section id="monitoring-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Progres Proposal</h2>
            <p class="text-sm text-gray-500 mt-1">Monitor semua progres pengajuan KAK dan LPJ secara real-time.</p>
        </div>

        <!-- Filter UI -->
        <div class="flex flex-col gap-4 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex-shrink-0 w-full md:w-auto p-1 bg-gray-100 rounded-full flex items-center space-x-1 overflow-x-auto">
                    <button type="button" class="riwayat-filter-tab active-tab" data-status="Semua">Semua</button>
                    <button type="button" class="riwayat-filter-tab" data-status="In Process">In Process</button>
                    <button type="button" class="riwayat-filter-tab" data-status="Menunggu">Menunggu</button>
                    <button type="button" class="riwayat-filter-tab" data-status="Approved">Approved</button>
                    <button type="button" class="riwayat-filter-tab" data-status="Ditolak">Ditolak</button>
                </div>

                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="search-monitoring-input" placeholder="Cari Nama Kegiatan / Pengusul..."
                           class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm">
                </div>
            </div>

            <div class="flex items-center gap-3">
                <label for="filter-jurusan" class="text-sm font-medium text-gray-700 whitespace-nowrap">
                    <i class="fas fa-filter mr-1 text-gray-400"></i> Filter Jurusan:
                </label>
                <div class="relative w-full md:w-72">
                    <select id="filter-jurusan" 
                            class="w-full px-4 py-2.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm appearance-none cursor-pointer">
                        <option value="semua" class="text-gray-800 bg-white">Semua Jurusan</option>
                        <?php foreach ($list_jurusan as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>" class="text-gray-800 bg-white"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 pointer-events-none"></i>
                </div>
            </div>
        </div>
        
        <!-- Table -->
        <div class="overflow-x-auto">
            <div class="w-full min-w-[900px]">
                <div class="grid grid-cols-3 gap-4 px-4 py-3 bg-gray-50 rounded-t-lg">
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wider">Proposal Details</div>
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wider">Progres</div>
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wider">Status</div>
                </div>
                
                <div id="monitoring-table-body" class="divide-y divide-gray-100 relative min-h-[200px]">
                    <div id="loading-spinner" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-40 hidden">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center mt-6 pt-5 border-t border-gray-100">
            <p id="pagination-info" class="text-sm text-gray-600 mb-4 md:mb-0">Menampilkan 0 dari 0 hasil</p>
            <nav id="pagination-nav" class="flex items-center gap-1"></nav>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-monitoring-input');
    const filterTabs = document.querySelectorAll('.riwayat-filter-tab');
    const filterJurusan = document.getElementById('filter-jurusan');
    const tableBody = document.getElementById('monitoring-table-body');
    const paginationNav = document.getElementById('pagination-nav');
    const paginationInfo = document.getElementById('pagination-info');
    const loadingSpinner = document.getElementById('loading-spinner');

    let currentPage = 1;
    let currentStatus = 'semua';
    let currentJurusan = 'semua';
    let currentSearch = '';
    let debounceTimer;

    // Progress Bar Visualizer
    function renderProposalProgressJS(tahapSekarang, status) {
        // Step yang didefinisikan di Frontend
        const tahapanAll = ['Pengajuan', 'Verifikasi', 'ACC PPK', 'Dana Cair', 'LPJ'];
        
        const statusLower = (status || '').toLowerCase();
        const isDitolak = (statusLower === 'ditolak');
        
        // Cari index tahap sekarang di array
        let posisiSekarang = tahapanAll.indexOf(tahapSekarang);
        
        // Fallback jika string dari DB berbeda sedikit
        if (posisiSekarang === -1) {
            if (tahapSekarang === 'Review LPJ') posisiSekarang = 4; // Anggap LPJ
            else if (tahapSekarang === 'ACC WD') posisiSekarang = 2; 
            else posisiSekarang = 0; // Default Pengajuan
        }
        
        const totalLangkah = tahapanAll.length - 1;
        let lebarBiru = 0;

        if (posisiSekarang > 0) {
            lebarBiru = ( (posisiSekarang) / totalLangkah ) * 100;
        }

        // Warna Bar
        let barColor = 'bg-blue-500';
        if (isDitolak) {
            barColor = 'bg-red-500';
        } else if (tahapSekarang === 'LPJ' || tahapSekarang === 'Selesai') {
            barColor = 'bg-green-500';
            lebarBiru = 100;
        }

        // letakkan garis sedikit lebih rendah (58%) dan nodes sedikit lebih atas (38%),
        // serta tingkatkan tinggi container ke h-16 sehingga ada ruang untuk label
        let linesHTML = `
            <div class="absolute left-0 w-full h-1 bg-gray-200 z-0 transform -translate-y-1/4 rounded-full" style="top:58%;"></div>
            <div class="absolute left-0 h-1 ${barColor} z-10 transform -translate-y-1/2 transition-all duration-500 ease-out rounded-full" style="top:58%; width: ${lebarBiru}%;"></div>
        `;

        let nodesHTML = '';
        tahapanAll.forEach((namaTahap, index) => {
            const leftPosition = totalLangkah > 0 ? (index / totalLangkah) * 100 : 0;
            let isCompleted = index <= posisiSekarang;
            let isActive = index === posisiSekarang;
            
            let nodeClass = 'bg-gray-300 border-gray-300';
            let textClass = 'text-gray-400';

            if (isCompleted) {
                if (isDitolak && isActive) {
                    nodeClass = 'bg-red-500 border-red-500 ring-2 ring-red-200';
                    textClass = 'text-red-600 font-bold';
                } else if ((tahapSekarang === 'LPJ') && isActive) {
                    nodeClass = 'bg-green-500 border-green-500 ring-2 ring-green-200';
                    textClass = 'text-green-600 font-bold';
                } else {
                    nodeClass = 'bg-blue-500 border-blue-500';
                    textClass = 'text-blue-600';
                }
            }

            nodesHTML += `
                <div class='absolute z-20 flex flex-col items-center' style='left: ${leftPosition}%; transform: translateX(-50%); top:38%;'>
                    <div class='w-3 h-3 rounded-full border ${nodeClass} transition-all duration-300 bg-white'></div>
                    <span class='absolute -bottom-10 text-[10px] w-24 text-center ${textClass} leading-tight hidden md:block'>${namaTahap}</span>
                </div>
            `;
        });
        
        return `<div class="relative w-full h-16 flex items-center mt-2 mb-2 px-2">${linesHTML}${nodesHTML}</div>`;
    }
    
    // RENDER TABLE
    function renderTable(proposals) {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        
        if (!proposals || proposals.length === 0) {
            tableBody.innerHTML = `<div class="text-center py-10 text-gray-500 italic">Tidak ada proposal yang cocok dengan filter Anda.</div>`;
            return;
        }

        let delay = 0;
        proposals.forEach(item => {
            const statusLower = (item.status || '').toLowerCase();
            
            const statusClass = {
                'approved': 'text-green-700 bg-green-100 border-green-200',
                'disetujui': 'text-green-700 bg-green-100 border-green-200',
                'ditolak': 'text-red-700 bg-red-100 border-red-200',
                'in process': 'text-blue-700 bg-blue-100 border-blue-200',
                'menunggu': 'text-yellow-700 bg-yellow-100 border-yellow-200'
            }[statusLower] || 'text-gray-700 bg-gray-100 border-gray-200';

            const displayJurusan = item.prodi ? item.prodi : (item.jurusan || '-');
            const displayNama = item.nama || 'Tanpa Judul';
            const displayPengusul = item.nama_lengkap || item.pengusul || 'N/A';
            const displayNIM = item.nim || '-';
            
            // PENTING: Ambil tahap_sekarang dari JSON response
            const displayTahap = item.tahap_sekarang || 'Pengajuan';
            const displayStatus = item.status || 'Unknown';

            delay += 50; 
            
            tableBody.insertAdjacentHTML('beforeend', `
                <div class='grid grid-cols-3 gap-4 px-4 py-5 items-center transition-colors border-b border-gray-100 hover:bg-gray-50 animate-reveal' style="animation-delay: ${delay}ms;">
                    <div>
                        <p class="text-sm text-gray-900 font-bold line-clamp-2" title="${displayNama}">${displayNama}</p>
                        <p class="text-xs text-gray-600 mt-1">${displayPengusul} <span class="text-gray-400">(${displayNIM})</span></p>
                        <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-graduation-cap mr-1"></i>${displayJurusan}</p>
                    </div>
                    <div class="px-2">
                        ${renderProposalProgressJS(displayTahap, displayStatus)}
                    </div>
                    <div>
                        <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border ${statusClass}'>
                            ${displayStatus}
                        </span>
                    </div>
                </div>
            `);
        });
    }
    
    function renderPagination(pagination) {
        if (!paginationNav || !paginationInfo) return;
        paginationNav.innerHTML = '';
        
        paginationInfo.innerHTML = `Menampilkan <span class="font-semibold text-gray-900">${pagination.showingFrom}</span> - <span class="font-semibold text-gray-900">${pagination.showingTo}</span> dari <span class="font-semibold text-gray-900">${pagination.totalItems}</span> data`;
        
        if (pagination.totalPages <= 1) return;

        // Prev Button
        paginationNav.innerHTML += `
            <button class="pagination-btn w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" 
                data-page="${pagination.currentPage - 1}" ${pagination.currentPage === 1 ? 'disabled' : ''}>
                <i class="fas fa-chevron-left text-xs"></i>
            </button>`;
        
        // Page Numbers Logic
        let startPage = Math.max(1, pagination.currentPage - 2);
        let endPage = Math.min(pagination.totalPages, pagination.currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.currentPage) {
                paginationNav.innerHTML += `<button class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-600 text-white text-sm font-medium shadow-sm cursor-default" disabled>${i}</button>`;
            } else {
                paginationNav.innerHTML += `<button class="pagination-btn w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 text-sm font-medium transition-colors" data-page="${i}">${i}</button>`;
            }
        }
        
        // Next Button
        paginationNav.innerHTML += `
            <button class="pagination-btn w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors" 
                data-page="${pagination.currentPage + 1}" ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}>
                <i class="fas fa-chevron-right text-xs"></i>
            </button>`;
    }

    async function fetchData() {
        if (loadingSpinner) loadingSpinner.classList.remove('hidden');
        
        const url = `/docutrack/public/ppk/monitoring/data?page=${currentPage}&status=${currentStatus}&jurusan=${encodeURIComponent(currentJurusan)}&search=${encodeURIComponent(currentSearch)}`;
        
        try {
            const response = await fetch(url);
            
            // Cek jika response bukan OK
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.error || `Server Error: ${response.status}`);
            }
            
            const data = await response.json();
            renderTable(data.proposals);
            renderPagination(data.pagination);
        } catch (error) {
            console.error('Fetch error:', error);
            if(tableBody) tableBody.innerHTML = `<div class="text-center py-10 text-red-500 italic flex flex-col items-center"><i class="fas fa-exclamation-triangle text-2xl mb-2"></i>Gagal memuat data: ${error.message}</div>`;
        } finally {
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
        }
    }

    // Event Listeners
    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => { 
            currentPage = 1; 
            currentSearch = searchInput.value; 
            fetchData(); 
        }, 500);
    });

    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active-tab', 'bg-blue-600', 'text-white', 'shadow-md'));
            filterTabs.forEach(t => t.classList.add('text-gray-600'));
            
            tab.classList.remove('text-gray-600');
            tab.classList.add('active-tab', 'bg-blue-600', 'text-white', 'shadow-md');
            
            currentPage = 1;
            currentStatus = tab.dataset.status.toLowerCase();
            fetchData();
        });
    });

    filterJurusan?.addEventListener('change', () => {
        currentPage = 1;
        currentJurusan = filterJurusan.value;
        fetchData();
    });
    
    paginationNav?.addEventListener('click', (e) => {
        const button = e.target.closest('.pagination-btn');
        if (button && !button.disabled) { 
            currentPage = parseInt(button.dataset.page); 
            fetchData(); 
        }
    });
    
    // Initial Load
    fetchData();
});
</script>

<style>
.riwayat-filter-tab {
    @apply px-4 py-2 text-sm font-medium text-gray-600 rounded-full transition-all duration-200 whitespace-nowrap border border-transparent;
}
.riwayat-filter-tab:hover {
    @apply bg-gray-200;
}
@keyframes reveal {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-reveal {
    animation: reveal 0.4s ease-out forwards;
    opacity: 0;
}
</style>