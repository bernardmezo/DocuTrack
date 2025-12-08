<?php
// File: src/views/pages/PPK/pengajuan_kegiatan.php

$list_usulan = $list_usulan ?? [];
$jurusan_list = $jurusan_list ?? [];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="stage-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian Verifikasi (Persetujuan PPK)</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar usulan yang telah disetujui verifikator dan menunggu persetujuan Anda.</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10"></i>
                <input type="text" id="search-kegiatan-input" placeholder="Cari Nama Kegiatan..."
                       class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                       aria-label="Cari Kegiatan">
            </div>
            
            <div class="relative w-full lg:w-80">
                <i class="fas fa-graduation-cap absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                <select id="filter-jurusan" 
                        style="color: #374151 !important;"
                        class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                    <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                    <?php foreach ($jurusan_list as $jurusan) : ?>
                        <option value="<?php echo htmlspecialchars($jurusan); ?>" style="color: #374151 !important; font-weight: 600;"><?php echo htmlspecialchars($jurusan); ?></option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
            </div>
        </div>
        
        <div class="overflow-y-auto overflow-x-auto max-h-[600px]">
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
                <tbody id="kegiatan-table-body" class="divide-y divide-gray-100"></tbody>
            </table>
        </div>
        
        <div id="pagination-container" class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 gap-4 mt-4"></div>
        
    </section>
</main>

<script>
    window.allDataUsulan = <?php echo json_encode($list_usulan); ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    const dataRaw = window.allDataUsulan || [];
    const ITEMS_PER_PAGE = 5; 
    
    let state = {
        data: dataRaw,
        filteredData: dataRaw,
        currentPage: 1,
        filters: { search: '', jurusan: '' }
    };

    const els = {
        search: document.getElementById('search-kegiatan-input'),
        jurusan: document.getElementById('filter-jurusan'),
        tbody: document.getElementById('kegiatan-table-body'),
        pagination: document.getElementById('pagination-container')
    };

    function applyFilters() {
        const search = state.filters.search.toLowerCase();
        const jurusan = state.filters.jurusan.toLowerCase();

        state.filteredData = state.data.filter(item => {
            const nama = (item.nama || '').toLowerCase();
            const pengusul = (item.pengusul || '').toLowerCase();
            const searchMatch = nama.includes(search) || pengusul.includes(search);
            const jurusanMatch = jurusan === '' || (item.jurusan && item.jurusan.toLowerCase() === jurusan);
            return searchMatch && jurusanMatch;
        });

        state.currentPage = 1;
        render();
    }

    function render() {
        renderTable();
        renderPagination();
    }

    function renderTable() {
        els.tbody.innerHTML = '';

        if (state.filteredData.length === 0) {
            els.tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Tidak ada antrian usulan saat ini.</td></tr>`;
            return;
        }

        const start = (state.currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = state.filteredData.slice(start, end);

        pageData.forEach((item, index) => {
            const nomor = start + index + 1;
            
            let tglFormatted = '-';
            if (item.tanggal_pengajuan) {
                const d = new Date(item.tanggal_pengajuan);
                tglFormatted = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            }

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

            const row = `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${nomor}.</td>
                    <td class="px-6 py-5 text-sm">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama)}</span>
                            <span class="text-gray-600 text-xs">
                                ${escapeHtml(item.pengusul)}
                                <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                            </span>
                            <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                <i class="fas fa-graduation-cap mr-1"></i>${escapeHtml(item.prodi || '-')}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-gray-400 text-xs"></i> ${tglFormatted}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border ${badgeClass}">
                            <i class="fas ${iconClass}"></i> ${escapeHtml(item.status)}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="/docutrack/public/ppk/telaah/show/${item.id}?ref=pengajuan-kegiatan" 
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-medium hover:bg-blue-700 hover:shadow-md transition-all">
                            <i class="fas fa-search"></i> Periksa
                        </a>
                    </td>
                </tr>
            `;
            els.tbody.innerHTML += row;
        });
    }

    function renderPagination() {
        els.pagination.innerHTML = '';
        const totalPages = Math.ceil(state.filteredData.length / ITEMS_PER_PAGE);

        if (totalPages <= 1) return;

        const infoDiv = document.createElement('div');
        infoDiv.className = 'text-sm text-gray-600';
        infoDiv.textContent = `Halaman ${state.currentPage} dari ${totalPages}`;
        
        const btnContainer = document.createElement('div');
        btnContainer.className = 'flex gap-2';

        const prevBtn = createPageBtn('<i class="fas fa-chevron-left"></i>', state.currentPage > 1, () => changePage(state.currentPage - 1));
        btnContainer.appendChild(prevBtn);

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= state.currentPage - 1 && i <= state.currentPage + 1)) {
                btnContainer.appendChild(createPageBtn(i, true, () => changePage(i), state.currentPage === i));
            } else if (i === state.currentPage - 2 || i === state.currentPage + 2) {
                const dots = document.createElement('span');
                dots.className = 'px-2 py-2 text-gray-400';
                dots.innerText = '...';
                btnContainer.appendChild(dots);
            }
        }

        const nextBtn = createPageBtn('<i class="fas fa-chevron-right"></i>', state.currentPage < totalPages, () => changePage(state.currentPage + 1));
        btnContainer.appendChild(nextBtn);

        els.pagination.appendChild(infoDiv);
        els.pagination.appendChild(btnContainer);
    }

    function createPageBtn(html, isEnabled, onClick, isActive = false) {
        const btn = document.createElement('button');
        btn.innerHTML = html;
        btn.className = `px-3 py-2 text-sm font-medium rounded-lg transition-all border ${
            isActive 
            ? 'bg-blue-600 text-white border-blue-600 shadow-md' 
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 hover:text-blue-600'
        }`;
        
        if (!isEnabled) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            btn.addEventListener('click', onClick);
        }
        return btn;
    }

    function changePage(newPage) {
        state.currentPage = newPage;
        render();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    if (els.search) els.search.addEventListener('input', (e) => { state.filters.search = e.target.value; applyFilters(); });
    if (els.jurusan) els.jurusan.addEventListener('change', (e) => { state.filters.jurusan = e.target.value; applyFilters(); });

    applyFilters();
});
</script>
