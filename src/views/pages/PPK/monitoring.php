<?php
// File: src/views/pages/PPK/monitoring.php

if (!isset($list_proposal)) { $list_proposal = []; }
if (!isset($list_jurusan)) { $list_jurusan = []; }
?>

<main class="main-content font-poppins p-3 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    <section id="monitoring-list" class="stage-content bg-white p-3 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-4 md:mb-6 pb-4 md:pb-5 border-b border-gray-200">
            <h2 class="text-lg md:text-2xl font-bold text-gray-800">Progres Proposal</h2>
        </div>

        <div class="flex flex-col gap-3 md:gap-4 mb-4 md:mb-6">
            <!-- Status Filter Tabs -->
            <div class="w-full overflow-x-auto scrollbar-hide">
                <div class="inline-flex p-1 bg-gray-100 rounded-full items-center gap-1 min-w-min">
                    <button type="button" class="riwayat-filter-tab active-tab" data-status="Semua">Semua</button>
                    <button type="button" class="riwayat-filter-tab" data-status="In Process">In Process</button>
                    <button type="button" class="riwayat-filter-tab" data-status="Menunggu">Menunggu</button>
                    <button type="button" class="riwayat-filter-tab" data-status="Approved">Approved</button>
                    <button type="button" class="riwayat-filter-tab" data-status="Ditolak">Ditolak</button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="relative w-full">
                <i class="fas fa-search absolute top-1/2 left-3 md:left-4 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="search-monitoring-input" placeholder="Cari kegiatan / pengusul..."
                       class="w-full pl-9 md:pl-10 pr-4 py-2 md:py-2.5 text-xs md:text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm">
            </div>

            <!-- Department Filter -->
            <div class="flex items-center gap-2 md:gap-3">
                <label for="filter-jurusan" class="text-xs md:text-sm font-medium text-gray-700 whitespace-nowrap">
                    <i class="fas fa-filter mr-1 text-gray-400"></i> Jurusan:
                </label>
                <div class="relative flex-1 md:flex-initial md:w-72">
                    <select id="filter-jurusan" 
                            class="w-full px-3 md:px-4 py-2 md:py-2.5 text-xs md:text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm appearance-none cursor-pointer">
                        <option value="semua">Semua Jurusan</option>
                        <?php foreach ($list_jurusan as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 md:right-4 -translate-y-1/2 text-gray-500 pointer-events-none text-xs"></i>
                </div>
            </div>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <div class="w-full min-w-[900px]">
                <div class="grid grid-cols-3 gap-4 px-4 py-3 bg-gray-50 rounded-t-lg">
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wider">Proposal Details</div>
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wider">Progres</div>
                    <div class="text-xs font-bold text-gray-600 uppercase tracking-wider">Status</div>
                </div>
                
                <div id="monitoring-table-body" class="divide-y divide-gray-100 relative min-h-[200px]">
                    <div id="loading-spinner" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-40">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden">
            <div id="monitoring-mobile-body" class="space-y-3 relative min-h-[200px]">
                <div id="loading-spinner-mobile" class="absolute inset-0 flex items-center justify-center bg-white/50 backdrop-blur-sm z-40">
                    <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center mt-4 md:mt-6 pt-4 md:pt-5 border-t border-gray-100 gap-3">
            <p id="pagination-info" class="text-xs md:text-sm text-gray-600 text-center md:text-left">Menampilkan 0 dari 0 hasil</p>
            <nav id="pagination-nav" class="flex items-center gap-1 flex-wrap justify-center"></nav>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-monitoring-input');
    const filterTabs = document.querySelectorAll('.riwayat-filter-tab');
    const filterJurusan = document.getElementById('filter-jurusan');
    const tableBody = document.getElementById('monitoring-table-body');
    const mobileBody = document.getElementById('monitoring-mobile-body');
    const paginationNav = document.getElementById('pagination-nav');
    const paginationInfo = document.getElementById('pagination-info');
    const loadingSpinner = document.getElementById('loading-spinner');
    const loadingSpinnerMobile = document.getElementById('loading-spinner-mobile');

    let currentPage = 1;
    let currentStatus = 'semua';
    let currentJurusan = 'semua';
    let currentSearch = '';
    let debounceTimer;
    const isMobile = window.innerWidth < 768;

    // Render Progress Bar
    function renderProposalProgressJS(tahapSekarang, status, forMobile = false) {
        const tahapanAll = ['Pengajuan', 'Verifikasi', 'ACC PPK', 'ACC WD', 'Dana Cair', 'LPJ'];
        
        const statusLower = status.toLowerCase();
        const isDitolak = (statusLower === 'ditolak');
        const isApproved = (statusLower === 'approved');
        const isMenunggu = (statusLower === 'menunggu');

        let posisiSekarang = tahapanAll.indexOf(tahapSekarang);
        if (posisiSekarang === -1) posisiSekarang = 0;
        
        const totalLangkah = tahapanAll.length - 1;
        
        let lebarBiru = 0, lebarMerah = 0, lebarHijau = 0, leftMerah = 0, leftHijau = 0, lebarAktifBiru = 0, leftAktifBiru = 0;

        if (posisiSekarang > 0) {
            lebarBiru = ( (posisiSekarang - 1) / totalLangkah ) * 100;
        }

        if (isDitolak) {
            lebarMerah = (1 / totalLangkah) * 100;
            leftMerah = lebarBiru;
        } else if (isApproved) {
            lebarBiru = ( (totalLangkah - 1) / totalLangkah ) * 100;
            lebarHijau = (1 / totalLangkah) * 100;
            leftHijau = lebarBiru;
        } else if (!isMenunggu || posisiSekarang > 0) {
            lebarAktifBiru = (1 / totalLangkah) * 100;
            leftAktifBiru = lebarBiru;
        }

        let linesHTML = `
            <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 z-0 transform -translate-y-1/2"></div>
            <div class="absolute top-1/2 left-0 h-0.5 bg-blue-500 z-10 transform -translate-y-1/2 transition-all duration-500 ease-out" style="width: ${lebarBiru}%;"></div>
        `;
        if (lebarMerah > 0) linesHTML += `<div class="absolute top-1/2 h-0.5 bg-red-500 z-20 transform -translate-y-1/2" style="left: ${leftMerah}%; width: ${lebarMerah}%;"></div>`;
        if (lebarHijau > 0) linesHTML += `<div class="absolute top-1/2 h-0.5 bg-green-500 z-20 transform -translate-y-1/2" style="left: ${leftHijau}%; width: ${lebarHijau}%;"></div>`;
        if (lebarAktifBiru > 0) linesHTML += `<div class="absolute top-1/2 h-0.5 bg-blue-500 z-20 transform -translate-y-1/2" style="left: ${leftAktifBiru}%; width: ${lebarAktifBiru}%;"></div>`;

        let nodesHTML = '';
        tahapanAll.forEach((namaTahap, index) => {
            const leftPosition = totalLangkah > 0 ? (index / totalLangkah) * 100 : 0;
            let isCompleted = index < posisiSekarang;
            let isActive = index === posisiSekarang;
            let nodeClass = 'bg-gray-300', textClass = 'text-gray-400';
            const nodeSize = forMobile ? 'w-3 h-3' : 'w-4 h-4';

            if (isCompleted) { nodeClass = 'bg-blue-500'; textClass = 'text-blue-600'; }
            else if (isActive) {
                if (isDitolak) { nodeClass = 'bg-red-500 ring-2 md:ring-4 ring-red-200 scale-110'; textClass = 'text-red-600 font-bold'; }
                else { nodeClass = 'bg-blue-500 ring-2 md:ring-4 ring-blue-200 scale-110'; textClass = 'text-blue-600 font-bold'; }
            }
            
            if (isApproved) {
                nodeClass = 'bg-blue-500'; textClass = 'text-blue-600';
                if (namaTahap === 'LPJ') { nodeClass = 'bg-green-500 ring-2 md:ring-4 ring-green-200 scale-110'; textClass = 'text-green-600 font-bold'; }
            }

            // Shortened labels for mobile
            const mobileName = {
                'Pengajuan': 'Ajuan',
                'Verifikasi': 'Verif',
                'ACC PPK': 'PPK',
                'ACC WD': 'WD',
                'Dana Cair': 'Dana',
                'LPJ': 'LPJ'
            }[namaTahap] || namaTahap;

            const displayName = forMobile ? mobileName : namaTahap;
            const textSize = forMobile ? 'text-[9px]' : 'text-xs';

            nodesHTML += `
                <div class='absolute z-30 flex flex-col items-center' style='left: ${leftPosition}%; transform: translateX(-50%);' title='${namaTahap}'>
                    <div class='${nodeSize} rounded-full border-2 border-white ${nodeClass} transition-all duration-300'></div>
                    <span class='absolute -bottom-4 md:-bottom-5 ${textSize} w-12 md:w-20 text-center ${textClass}'>${displayName}</span>
                </div>
            `;
        });
        
        const heightClass = forMobile ? 'h-8' : 'h-10';
        return `<div class="relative w-full ${heightClass} flex items-center">${linesHTML}${nodesHTML}</div>`;
    }
    
    // RENDER DESKTOP TABLE
    function renderTable(proposals) {
        if (!tableBody) return;
        tableBody.innerHTML = '';
        
        if (proposals.length === 0) {
            tableBody.innerHTML = `<div class="text-center py-10 text-gray-500 italic text-sm">Tidak ada proposal yang cocok dengan filter Anda.</div>`;
            return;
        }

        let delay = 0;
        proposals.forEach(item => {
            const statusLower = item.status.toLowerCase();
            let rowClass = 'bg-white', rowStyle = '';
            
            if (statusLower === 'approved' || statusLower === 'ditolak') rowStyle = 'opacity-70';
            else if (statusLower === 'in process' || statusLower === 'menunggu') rowClass = 'bg-blue-50';
            
            const statusClass = {
                'approved': 'text-green-700 bg-green-100',
                'ditolak': 'text-red-700 bg-red-100',
                'in process': 'text-blue-700 bg-blue-100',
            }[statusLower] || 'text-yellow-700 bg-yellow-100';

            const displayJurusan = item.prodi ? item.prodi : item.jurusan;
            delay += 80;
            
            tableBody.insertAdjacentHTML('beforeend', `
                <div class='monitoring-row grid grid-cols-3 gap-4 px-4 py-5 items-center transition-colors animate-reveal ${rowClass} ${rowStyle}' style="animation-delay: ${delay}ms;">
                    <div>
                        <p class="text-sm text-gray-900 font-bold">${item.nama}</p>
                        <p class="text-xs text-gray-600 mt-1">${item.pengusul} <span class="text-gray-400">(${item.nim})</span></p>
                        <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-graduation-cap mr-1"></i>${displayJurusan}</p>
                    </div>
                    <div class="px-2">${renderProposalProgressJS(item.tahap_sekarang, item.status, false)}</div>
                    <div>
                        <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${statusClass}'>${item.status}</span>
                    </div>
                </div>
            `);
        });
    }

    // RENDER MOBILE CARDS
    function renderMobileCards(proposals) {
        if (!mobileBody) return;
        mobileBody.innerHTML = '';
        
        if (proposals.length === 0) {
            mobileBody.innerHTML = `<div class="text-center py-10 text-gray-500 italic text-sm">Tidak ada proposal yang cocok dengan filter Anda.</div>`;
            return;
        }

        let delay = 0;
        proposals.forEach(item => {
            const statusLower = item.status.toLowerCase();
            let cardClass = 'bg-white';
            
            if (statusLower === 'in process' || statusLower === 'menunggu') cardClass = 'bg-blue-50';
            
            const statusClass = {
                'approved': 'text-green-700 bg-green-100',
                'ditolak': 'text-red-700 bg-red-100',
                'in process': 'text-blue-700 bg-blue-100',
            }[statusLower] || 'text-yellow-700 bg-yellow-100';

            const displayJurusan = item.prodi ? item.prodi : item.jurusan;
            delay += 80;
            
            mobileBody.insertAdjacentHTML('beforeend', `
                <div class='${cardClass} rounded-xl shadow-sm border border-gray-200 p-3 animate-reveal' style="animation-delay: ${delay}ms;">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1 min-w-0 pr-2">
                            <p class="text-sm text-gray-900 font-bold truncate">${item.nama}</p>
                            <p class="text-xs text-gray-600 mt-0.5 truncate">${item.pengusul}</p>
                            <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-graduation-cap mr-1"></i>${displayJurusan}</p>
                        </div>
                        <span class='inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold whitespace-nowrap ${statusClass}'>${item.status}</span>
                    </div>
                    <div class="mt-3 pt-3 border-t border-gray-200">
                        <p class="text-xs text-gray-600 mb-2 font-medium">Progres:</p>
                        ${renderProposalProgressJS(item.tahap_sekarang, item.status, true)}
                    </div>
                </div>
            `);
        });
    }
    
    function renderPagination(pagination) {
        if (!paginationNav || !paginationInfo) return;
        paginationNav.innerHTML = '';
        
        paginationInfo.innerHTML = `Menampilkan <span class="font-semibold">${pagination.showingFrom}</span> s.d. <span class="font-semibold">${pagination.showingTo}</span> dari <span class="font-semibold">${pagination.totalItems}</span> hasil`;
        
        if (pagination.totalPages <= 1) return;

        const btnClass = 'px-2.5 md:px-3 py-1 rounded-md text-xs md:text-sm';
        paginationNav.innerHTML += `<button class="pagination-btn ${btnClass} ${pagination.currentPage === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" data-page="${pagination.currentPage - 1}" ${pagination.currentPage === 1 ? 'disabled' : ''}><i class="fas fa-chevron-left"></i></button>`;
        
        // Show fewer page numbers on mobile
        const maxPages = isMobile ? 3 : 5;
        let startPage = Math.max(1, pagination.currentPage - Math.floor(maxPages / 2));
        let endPage = Math.min(pagination.totalPages, startPage + maxPages - 1);
        
        if (endPage - startPage < maxPages - 1) {
            startPage = Math.max(1, endPage - maxPages + 1);
        }

        if (startPage > 1) {
            paginationNav.innerHTML += `<button class="pagination-btn ${btnClass} text-gray-700 hover:bg-gray-100" data-page="1">1</button>`;
            if (startPage > 2) paginationNav.innerHTML += `<span class="px-2 text-gray-400">...</span>`;
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.currentPage) {
                paginationNav.innerHTML += `<button class="pagination-btn ${btnClass} font-medium bg-blue-600 text-white" data-page="${i}" disabled>${i}</button>`;
            } else {
                paginationNav.innerHTML += `<button class="pagination-btn ${btnClass} font-medium text-gray-700 hover:bg-gray-100" data-page="${i}">${i}</button>`;
            }
        }

        if (endPage < pagination.totalPages) {
            if (endPage < pagination.totalPages - 1) paginationNav.innerHTML += `<span class="px-2 text-gray-400">...</span>`;
            paginationNav.innerHTML += `<button class="pagination-btn ${btnClass} text-gray-700 hover:bg-gray-100" data-page="${pagination.totalPages}">${pagination.totalPages}</button>`;
        }
        
        paginationNav.innerHTML += `<button class="pagination-btn ${btnClass} ${pagination.currentPage === pagination.totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" data-page="${pagination.currentPage + 1}" ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}><i class="fas fa-chevron-right"></i></button>`;
    }

    async function fetchData() {
        if (loadingSpinner) loadingSpinner.classList.remove('hidden');
        if (loadingSpinnerMobile) loadingSpinnerMobile.classList.remove('hidden');
        
        const url = `/docutrack/public/ppk/monitoring/data?page=${currentPage}&status=${currentStatus}&jurusan=${encodeURIComponent(currentJurusan)}&search=${encodeURIComponent(currentSearch)}`;
        
        try {
            const response = await fetch(url);
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            
            renderTable(data.proposals);
            renderMobileCards(data.proposals);
            renderPagination(data.pagination);
        } catch (error) {
            console.error('Fetch error:', error);
            const errorMsg = `<div class="text-center py-10 text-red-500 italic text-sm">Gagal memuat data.</div>`;
            if(tableBody) tableBody.innerHTML = errorMsg;
            if(mobileBody) mobileBody.innerHTML = errorMsg;
        } finally {
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
            if (loadingSpinnerMobile) loadingSpinnerMobile.classList.add('hidden');
        }
    }

    searchInput?.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => { currentPage = 1; currentSearch = searchInput.value; fetchData(); }, 500);
    });

    filterTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            filterTabs.forEach(t => t.classList.remove('active-tab'));
            tab.classList.add('active-tab');
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
        if (button && !button.disabled) { currentPage = parseInt(button.dataset.page); fetchData(); }
    });
    
    fetchData();
});
</script>

<style>
.riwayat-filter-tab {
    @apply px-3 md:px-4 py-1.5 md:py-2 text-xs md:text-sm font-medium text-gray-600 rounded-full transition-all duration-200 whitespace-nowrap flex-shrink-0;
}
.riwayat-filter-tab:hover {
    @apply bg-gray-200;
}
.riwayat-filter-tab.active-tab {
    @apply bg-blue-600 text-white shadow-md;
}
@keyframes reveal {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-reveal {
    animation: reveal 0.4s ease-out forwards;
    opacity: 0;
}
#filter-jurusan {
    color: #1f2937 !important;
    background-color: #ffffff !important;
}
#filter-jurusan option {
    color: #1f2937 !important;
    background-color: #ffffff !important;
    padding: 8px 12px;
}
#filter-jurusan:focus {
    color: #1f2937 !important;
}
/* Hide scrollbar for horizontal scroll but keep functionality */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>