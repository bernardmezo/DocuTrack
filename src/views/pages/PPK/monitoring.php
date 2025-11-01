<?php
// File: src/views/pages/PPK/monitoring.php

if (!isset($list_proposal)) { $list_proposal = []; } // Pastikan variabel ada

/**
 * ---------------------------------------------------
 * "Brilliant" Helper Function: render_proposal_progress
 * (VERSI DIPERBAIKI - ERROR $is_menunggu HILANG)
 * ---------------------------------------------------
 */
function render_proposal_progress($tahap_sekarang, $status) {
    $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
    
    // --- PERBAIKAN DI SINI ---
    $status_lower = strtolower($status);
    $is_ditolak = ($status_lower === 'ditolak');
    $is_approved = ($status_lower === 'approved');
    $is_menunggu = ($status_lower === 'menunggu'); // <-- Variabel yang hilang sekarang didefinisikan
    // --- AKHIR PERBAIKAN ---

    $posisi_sekarang = array_search($tahap_sekarang, $tahapan_all);
    if ($posisi_sekarang === false) $posisi_sekarang = 0; 
    
    $total_langkah = count($tahapan_all) - 1; // 5
    
    // --- Logika Garis Baru ---
    $lebar_garis_biru_selesai = 0; // Garis biru untuk progres yang sudah lewat
    $lebar_garis_aktif = 0;       // Garis untuk segmen yang aktif (bisa merah, hijau, biru)
    $left_garis_aktif = 0;        // Posisi 'left' untuk garis aktif
    $color_garis_aktif = 'bg-blue-500'; // Default

    if ($posisi_sekarang > 0) {
        $lebar_garis_biru_selesai = ( ($posisi_sekarang - 1) / $total_langkah ) * 100;
        $lebar_garis_aktif = (1 / $total_langkah) * 100;
        $left_garis_aktif = $lebar_garis_biru_selesai;
    }
    
    if ($is_ditolak) {
        $color_garis_aktif = 'bg-red-500'; // Segmen aktif (gagal) menjadi merah
    } elseif ($is_approved) {
        $lebar_garis_biru_selesai = ( ($total_langkah - 1) / $total_langkah ) * 100; // 80%
        $lebar_garis_aktif = (1 / $total_langkah) * 100; // 20%
        $left_garis_aktif = $lebar_garis_biru_selesai;
        $color_garis_aktif = 'bg-green-500'; // Segmen terakhir (LPJ) menjadi hijau
    } elseif ($is_menunggu && $posisi_sekarang === 0) { // <-- PERBAIKAN LOGIKA IF
        // Jika masih menunggu di 'Pengajuan' (posisi 0), tidak ada garis
        $lebar_garis_biru_selesai = 0;
        $lebar_garis_aktif = 0;
    }
    // --- Akhir Logika Garis ---

    // Mulai render HTML
    echo '<div class="relative w-full h-10 flex items-center">';
    // Garis Latar Abu-abu
    echo '<div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 z-0 transform -translate-y-1/2"></div>';
    
    // Garis Progress Biru (Selesai)
    echo "<div class='absolute top-1/2 left-0 h-1 bg-blue-500 z-10 transform -translate-y-1/2 transition-all duration-500 ease-out' style='width: {$lebar_garis_biru_selesai}%;'></div>";
    
    // Garis Progress Aktif (Merah/Hijau/Biru)
    if ($lebar_garis_aktif > 0) {
        echo "<div class='absolute top-1/2 h-1 {$color_garis_aktif} z-20 transform -translate-y-1/2 transition-all duration-500 ease-out' style='left: {$left_garis_aktif}%; width: {$lebar_garis_aktif}%;'></div>";
    }

    // Render setiap titik (node)
    foreach ($tahapan_all as $index => $nama_tahap) {
        $left_position = $total_langkah > 0 ? ($index / $total_langkah) * 100 : 0;
        
        $is_completed = $index < $posisi_sekarang;
        $is_active = $index === $posisi_sekarang;

        $node_class = 'bg-gray-300'; $text_class = 'text-gray-400';

        if ($is_completed) { // Sudah selesai (biru)
            $node_class = 'bg-blue-500'; $text_class = 'text-blue-600';
        } elseif ($is_active) { // Sedang aktif
            if ($is_ditolak) {
                $node_class = 'bg-red-500 ring-4 ring-red-200 scale-110'; $text_class = 'text-red-600 font-bold';
            } else {
                $node_class = 'bg-blue-500 ring-4 ring-blue-200 scale-110'; $text_class = 'text-blue-600 font-bold';
            }
        }
        
        if ($is_approved) {
            $node_class = 'bg-blue-500'; $text_class = 'text-blue-600';
            if ($nama_tahap === 'LPJ') {
                 $node_class = 'bg-green-500 ring-4 ring-green-200 scale-110'; $text_class = 'text-green-600 font-bold';
            }
        }

        echo "<div class='absolute z-30 flex flex-col items-center group' style='left: {$left_position}%; transform: translateX(-50%);' title='{$nama_tahap}'>";
        echo "  <div class='w-4 h-4 rounded-full border-2 border-white {$node_class} transition-all duration-300'></div>";
        echo "  <span class='absolute -bottom-5 text-xs w-20 text-center {$text_class} hidden md:block'>{$nama_tahap}</span>";
        echo "</div>";
    }
    
    echo '</div>'; // Penutup div relative
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="monitoring-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Progres Proposal</h2>
            <p class="text-sm text-gray-500 mt-1">Monitor semua progres pengajuan KAK dan LPJ secara real-time.</p>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
            
            <div class="flex-shrink-0 w-full md:w-auto p-1 bg-gray-100 rounded-full flex items-center space-x-1">
                <button type="button" class="riwayat-filter-tab active-tab" data-status="Semua">Semua</button>
                <button type="button" class="riwayat-filter-tab" data-status="In Process">In Process</button>
                <button type="button" class="riwayat-filter-tab" data-status="Menunggu">Menunggu</button>
                <button type="button" class="riwayat-filter-tab" data-status="Approved">Approved</button>
                <button type="button" class="riwayat-filter-tab" data-status="Ditolak">Ditolak</button>
            </div>

            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-monitoring-input" placeholder="Cari Nama Kegiatan..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                       aria-label="Cari Riwayat">
            </div>
        </div>
        
        <div class="overflow-x-auto">
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

        <div class="flex flex-col md:flex-row justify-between items-center mt-6 pt-5 border-t border-gray-100">
            <p id="pagination-info" class="text-sm text-gray-600 mb-4 md:mb-0">
                Menampilkan 0 dari 0 hasil
            </p>
            <nav id="pagination-nav" class="flex items-center gap-1"></nav>
        </div>
        
    </section>
</main>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- 1. ELEMEN DOM ---
        const searchInput = document.getElementById('search-monitoring-input');
        const filterTabs = document.querySelectorAll('.riwayat-filter-tab');
        const tableBody = document.getElementById('monitoring-table-body');
        const paginationNav = document.getElementById('pagination-nav');
        const paginationInfo = document.getElementById('pagination-info');
        const loadingSpinner = document.getElementById('loading-spinner');

        // --- 2. STATE FILTER ---
        let currentPage = 1;
        let currentStatus = 'semua';
        let currentSearch = '';
        let debounceTimer;

        // ===================================
        // 💡 FUNGSI RENDER PROGRES (VERSI JAVASCRIPT DIPERBAIKI) 💡
        // ===================================
        function renderProposalProgressJS(tahapSekarang, status) {
            const tahapanAll = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
            
            // --- PERBAIKAN DI SINI ---
            const statusLower = status.toLowerCase();
            const isDitolak = (statusLower === 'ditolak');
            const isApproved = (statusLower === 'approved');
            const isMenunggu = (statusLower === 'menunggu');
            // --- AKHIR PERBAIKAN ---

            let posisiSekarang = tahapanAll.indexOf(tahapSekarang);
            if (posisiSekarang === -1) posisiSekarang = 0;
            
            const totalLangkah = tahapanAll.length - 1; // 5
            
            // --- Logika Garis Baru ---
            let lebarBiru = 0;
            let lebarMerah = 0;
            let lebarHijau = 0;
            let leftMerah = 0;
            let leftHijau = 0;
            let lebarAktifBiru = 0;
            let leftAktifBiru = 0;

            if (posisiSekarang > 0) {
                lebarBiru = ( (posisiSekarang - 1) / totalLangkah ) * 100;
            }

            if (isDitolak) {
                lebarMerah = (1 / totalLangkah) * 100;
                leftMerah = lebarBiru;
            } else if (isApproved) {
                lebarBiru = ( (totalLangkah - 1) / totalLangkah ) * 100; // 80%
                lebarHijau = (1 / totalLangkah) * 100; // 20%
                leftHijau = lebarBiru;
            } else if (!isMenunggu || posisiSekarang > 0) { // 'In Process' atau 'Menunggu' tapi bukan di awal
                lebarAktifBiru = (1 / totalLangkah) * 100;
                leftAktifBiru = lebarBiru;
            }
            // Jika 'Menunggu' dan posisi 0, semua lebar = 0
            // --- Akhir Logika Garis ---

            // --- Render HTML Garis ---
            let linesHTML = `
                <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 z-0 transform -translate-y-1/2"></div>
                <div class="absolute top-1/2 left-0 h-1 bg-blue-500 z-10 transform -translate-y-1/2 transition-all duration-500 ease-out" style="width: ${lebarBiru}%;"></div>
            `;
            if (lebarMerah > 0) {
                linesHTML += `<div class="absolute top-1/2 h-1 bg-red-500 z-20 transform -translate-y-1/2 transition-all duration-500 ease-out" style="left: ${leftMerah}%; width: ${lebarMerah}%;"></div>`;
            }
            if (lebarHijau > 0) {
                linesHTML += `<div class="absolute top-1/2 h-1 bg-green-500 z-20 transform -translate-y-1/2 transition-all duration-500 ease-out" style="left: ${leftHijau}%; width: ${lebarHijau}%;"></div>`;
            }
            if (lebarAktifBiru > 0) {
                linesHTML += `<div class="absolute top-1/2 h-1 bg-blue-500 z-20 transform -translate-y-1/2 transition-all duration-500 ease-out" style="left: ${leftAktifBiru}%; width: ${lebarAktifBiru}%;"></div>`;
            }

            // --- Render HTML Node (Titik) ---
            let nodesHTML = '';
            tahapanAll.forEach((namaTahap, index) => {
                const leftPosition = totalLangkah > 0 ? (index / totalLangkah) * 100 : 0;
                
                let isCompleted = index < posisiSekarang;
                let isActive = index === posisiSekarang;

                let nodeClass = 'bg-gray-300';
                let textClass = 'text-gray-400';

                if (isCompleted) { // Selesai
                    nodeClass = 'bg-blue-500';
                    textClass = 'text-blue-600';
                } else if (isActive) { // Aktif
                    if (isDitolak) {
                        nodeClass = 'bg-red-500 ring-4 ring-red-200 scale-110';
                        textClass = 'text-red-600 font-bold';
                    } else {
                        nodeClass = 'bg-blue-500 ring-4 ring-blue-200 scale-110';
                        textClass = 'text-blue-600 font-bold';
                    }
                }
                
                if (isApproved) {
                    nodeClass = 'bg-blue-500'; textClass = 'text-blue-600';
                    if (namaTahap === 'LPJ') {
                         nodeClass = 'bg-green-500 ring-4 ring-green-200 scale-110';
                         textClass = 'text-green-600 font-bold';
                    }
                }

                nodesHTML += `
                    <div class='absolute z-30 flex flex-col items-center group' style='left: ${leftPosition}%; transform: translateX(-50%);' title='${namaTahap}'>
                        <div class='w-4 h-4 rounded-full border-2 border-white ${nodeClass} transition-all duration-300'></div>
                        <span class='absolute -bottom-5 text-xs w-20 text-center ${textClass} hidden md:block'>${namaTahap}</span>
                    </div>
                `;
            });
            
            return `
                <div class="relative w-full h-10 flex items-center">
                    ${linesHTML}
                    ${nodesHTML}
                </div>
            `;
        }
        
        // --- 4. FUNGSI RENDER DATA TABEL ---
        function renderTable(proposals) {
            if (!tableBody) return;
            tableBody.innerHTML = ''; // Kosongkan tabel
            
            if (proposals.length === 0) {
                tableBody.innerHTML = `
                    <div id="empty-row" class="text-center py-10 text-gray-500 italic">
                        Tidak ada proposal yang cocok dengan filter Anda.
                    </div>
                `;
                return;
            }

            let animationDelay = 0;
            proposals.forEach(item => {
                const statusLower = item.status.toLowerCase();
                let rowClass = 'bg-white';
                let rowStyle = '';
                
                if (statusLower === 'approved' || statusLower === 'ditolak') {
                    rowStyle = 'opacity-70'; // Redupkan jika sudah selesai
                } else if (statusLower === 'in process' || statusLower === 'menunggu') {
                    rowClass = 'bg-blue-50'; // Highlight jika sedang diproses
                }
                
                const statusClass = {
                    'approved': 'text-green-700 bg-green-100',
                    'ditolak': 'text-red-700 bg-red-100',
                    'in process': 'text-blue-700 bg-blue-100',
                }[statusLower] || 'text-gray-700 bg-gray-100'; // 'Menunggu'

                animationDelay += 100;
                
                const rowHTML = `
                    <div class='monitoring-row grid grid-cols-3 gap-4 px-4 py-6 items-center transition-colors animate-reveal ${rowClass} ${rowStyle}' 
                         data-nama="${item.nama.toLowerCase()}"
                         data-status="${statusLower}"
                         style="animation-delay: ${animationDelay}ms;">
                        
                        <div>
                            <p class="text-sm text-gray-900 font-bold">${item.nama}</p>
                            <p class="text-xs text-gray-600 mt-1">${item.pengusul}</p>
                        </div>
                        <div class="px-4">
                            ${renderProposalProgressJS(item.tahap_sekarang, item.status)}
                        </div>
                        <div>
                            <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold ${statusClass}'>
                                ${item.status}
                            </span>
                        </div>
                    </div>
                `;
                tableBody.insertAdjacentHTML('beforeend', rowHTML);
            });
        }
        
        // --- 5. FUNGSI RENDER PAGINATION ---
        function renderPagination(pagination) {
            if (!paginationNav || !paginationInfo) return;
            paginationNav.innerHTML = ''; // Kosongkan nav
            
            paginationInfo.innerHTML = `
                Menampilkan <span class="font-semibold">${pagination.showingFrom}</span> 
                s.d. <span class="font-semibold">${pagination.showingTo}</span> 
                dari <span class="font-semibold">${pagination.totalItems}</span> hasil
            `;
            
            if (pagination.totalPages <= 1) return;

            // Tombol "Sebelumnya"
            paginationNav.innerHTML += `
                <button class="pagination-btn px-3 py-1 rounded-md text-sm ${pagination.currentPage === 1 ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" 
                        data-page="${pagination.currentPage - 1}" ${pagination.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
            
            // Tombol Halaman
            for (let i = 1; i <= pagination.totalPages; i++) {
                if (i === pagination.currentPage) {
                    paginationNav.innerHTML += `<button class="pagination-btn px-3 py-1 rounded-md text-sm font-medium bg-blue-600 text-white" data-page="${i}" disabled>${i}</button>`;
                } else {
                    paginationNav.innerHTML += `<button class="pagination-btn px-3 py-1 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-100" data-page="${i}">${i}</button>`;
                }
            }
            
            // Tombol "Selanjutnya"
            paginationNav.innerHTML += `
                <button class="pagination-btn px-3 py-1 rounded-md text-sm ${pagination.currentPage === pagination.totalPages ? 'text-gray-400 cursor-not-allowed' : 'text-gray-700 hover:bg-gray-100'}" 
                        data-page="${pagination.currentPage + 1}" ${pagination.currentPage === pagination.totalPages ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
        }

        // --- 6. FUNGSI FETCH DATA (UTAMA) ---
        async function fetchData() {
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');
            
            const url = `/docutrack/public/ppk/monitoring/data?page=${currentPage}&status=${currentStatus}&search=${currentSearch}`;
            
            try {
                const response = await fetch(url);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                
                renderTable(data.proposals);
                renderPagination(data.pagination);
                
            } catch (error) {
                console.error('Fetch error:', error);
                if(tableBody) tableBody.innerHTML = `<div id="empty-row" class="text-center py-10 text-red-500 italic">Gagal memuat data. Silakan coba lagi.</div>`;
            } finally {
                if (loadingSpinner) loadingSpinner.classList.add('hidden');
            }
        }

        // --- 7. EVENT LISTENERS ---
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
                filterTabs.forEach(t => t.classList.remove('active-tab'));
                tab.classList.add('active-tab');
                currentPage = 1;
                currentStatus = tab.dataset.status.toLowerCase();
                fetchData();
            });
        });
        
        paginationNav?.addEventListener('click', (e) => {
            const button = e.target.closest('.pagination-btn');
            if (button && !button.disabled) {
                currentPage = parseInt(button.dataset.page);
                fetchData();
            }
        });
        
        // --- 8. INISIALISASI ---
        fetchData(); 
        
    });
</script>