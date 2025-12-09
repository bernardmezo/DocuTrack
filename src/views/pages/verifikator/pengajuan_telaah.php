<?php
// File: src/views/pages/verifikator/pengajuan_telaah.php

// Pastikan variabel terdefinisi (Data dikirim dari Controller)
if (!isset($list_usulan)) {
    $list_usulan = [];
}
$jurusan_list = $jurusan_list ?? [];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">

    <section id="stage-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian Pengajuan Telaah</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar usulan yang memerlukan verifikasi Anda ("Telah Direvisi" adalah prioritas).</p>
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
                    <?php
                    // Sort agar rapi
                    sort($jurusan_list);
                    foreach ($jurusan_list as $jurusan) :
                        ?>
                        <option value="<?php echo htmlspecialchars(strtolower($jurusan)); ?>" 
                                style="color: #374151 !important; font-weight: 600;">
                            <?php echo htmlspecialchars($jurusan); ?>
                        </option>
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
        
        <div id="pagination-container" class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 gap-4 mt-4">
            </div>
        
    </section>
</main>

<script>
    window.allDataUsulan = <?php echo json_encode($list_usulan) ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Ambil Data & Konfigurasi
    const dataRaw = window.allDataUsulan || [];
    const ITEMS_PER_PAGE = 5; // Jumlah item per halaman
    
    // 2. State Management
    let state = {
        data: dataRaw,           // Data asli (tidak berubah)
        filteredData: dataRaw,   // Data hasil filter (berubah-ubah)
        currentPage: 1,          // Halaman aktif saat ini
        filters: {
            search: '',
            jurusan: ''
        }
    };

    // 3. DOM Elements
    const els = {
        search: document.getElementById('search-kegiatan-input'),
        jurusan: document.getElementById('filter-jurusan'),
        tbody: document.getElementById('kegiatan-table-body'),
        pagination: document.getElementById('pagination-container')
    };

    // 4. Fungsi Utama
    
    // Apply Filter (Dijalankan saat input search / dropdown berubah)
    function applyFilters() {
        const search = state.filters.search.toLowerCase();
        const jurusan = state.filters.jurusan.toLowerCase();

        state.filteredData = state.data.filter(item => {
            const namaMatch = (item.nama || '').toLowerCase().includes(search);
            const pengusulMatch = (item.pengusul || '').toLowerCase().includes(search);
            
            // Filter berdasarkan Jurusan (Induk)
            const jurusanItem = (item.jurusan || '').toLowerCase();
            // Logika: jika filter kosong (semua jurusan) ATAU jurusan item sama dengan filter
            const jurusanMatch = jurusan === '' || jurusanItem === jurusan;

            return (namaMatch || pengusulMatch) && jurusanMatch;
        });

        // Reset ke halaman 1 setiap kali filter berubah
        state.currentPage = 1;
        render();
    }

    // Render UI
    function render() {
        renderTable();
        renderPagination();
    }

    // Render Tabel
    function renderTable() {
        els.tbody.innerHTML = '';

        if (state.filteredData.length === 0) {
            els.tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                        Tidak ada data yang sesuai.
                    </td>
                </tr>
            `;
            return;
        }

        // Logika Pagination: Ambil sebagian data dari filteredData
        const start = (state.currentPage - 1) * ITEMS_PER_PAGE;
        const end = start + ITEMS_PER_PAGE;
        const pageData = state.filteredData.slice(start, end);

        // Generate HTML Rows
        let rowsHTML = '';
        pageData.forEach((item, index) => {
            const nomor = start + index + 1;
            
            // Format Tanggal
            let tglFormatted = '-';
            if (item.tanggal_pengajuan) {
                const d = new Date(item.tanggal_pengajuan);
                // Cek valid date
                if (!isNaN(d.getTime())) {
                    tglFormatted = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                }
            }

            // Tentukan Class Status & Icon
            let statusClass = 'bg-gray-100 text-gray-600';
            let iconClass = 'fas fa-hourglass-half';
            let statusText = item.status || 'Menunggu';

            if (statusText.toLowerCase() === 'telah direvisi') {
                statusClass = 'bg-purple-100 text-purple-700';
                iconClass = 'fas fa-sync-alt';
            }

            // TAMPILAN PRODI (ANAK), BUKAN JURUSAN (INDUK)
            // Fallback ke jurusan jika prodi kosong
            const displayProdi = item.prodi ? item.prodi : (item.jurusan || '-');

            rowsHTML += `
                <tr class="hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-0">
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700">${nomor}.</td>
                    <td class="px-6 py-5 text-sm">
                        <div class="flex flex-col">
                            <span class="font-semibold text-gray-900 mb-1">${escapeHtml(item.nama || '')}</span>
                            <span class="text-gray-600 text-xs">
                                ${escapeHtml(item.pengusul || '')}
                                <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                            </span>
                            <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                <i class="fas fa-graduation-cap mr-1"></i>${escapeHtml(displayProdi)}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-alt text-gray-400 text-xs"></i>
                            ${tglFormatted}
                        </div>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full ${statusClass}">
                            <i class="${iconClass}"></i> ${escapeHtml(statusText)}
                        </span>
                    </td>
                    <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                        <a href="/docutrack/public/verifikator/telaah/show/${item.id}?ref=pengajuan-telaah" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                            <i class="fas fa-search"></i> Telaah
                        </a>
                    </td>
                </tr>
            `;
        });
        els.tbody.innerHTML = rowsHTML;
    }

    // Render Pagination Controls
    function renderPagination() {
        els.pagination.innerHTML = '';
        const totalPages = Math.ceil(state.filteredData.length / ITEMS_PER_PAGE);

        // Info Halaman
        const infoDiv = document.createElement('div');
        infoDiv.className = 'text-sm text-gray-600 mb-4 sm:mb-0';
        infoDiv.innerHTML = `Menampilkan halaman <span class="font-semibold">${state.currentPage}</span> dari <span class="font-semibold">${Math.max(1, totalPages)}</span>`;
        
        // Jika tidak ada data atau cuma 1 halaman, tampilkan info saja
        if (totalPages <= 1) {
            els.pagination.appendChild(infoDiv);
            return;
        }

        // Container Tombol
        const btnContainer = document.createElement('div');
        btnContainer.className = 'flex gap-2';

        // Tombol Prev
        const prevBtn = createPageBtn('Sebelumnya', state.currentPage > 1, () => changePage(state.currentPage - 1));
        btnContainer.appendChild(prevBtn);

        // Angka Halaman (Logic simple: Tampilkan max 5 halaman di sekitar current)
        let startPage = Math.max(1, state.currentPage - 2);
        let endPage = Math.min(totalPages, state.currentPage + 2);

        // Selalu tampilkan halaman 1
        if (startPage > 1) {
            btnContainer.appendChild(createPageBtn('1', true, () => changePage(1), state.currentPage === 1));
            if (startPage > 2) btnContainer.appendChild(createDots());
        }

        for (let i = startPage; i <= endPage; i++) {
            btnContainer.appendChild(createPageBtn(i, true, () => changePage(i), state.currentPage === i));
        }

        // Selalu tampilkan halaman terakhir
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) btnContainer.appendChild(createDots());
            btnContainer.appendChild(createPageBtn(totalPages, true, () => changePage(totalPages), state.currentPage === totalPages));
        }

        // Tombol Next
        const nextBtn = createPageBtn('Selanjutnya', state.currentPage < totalPages, () => changePage(state.currentPage + 1));
        btnContainer.appendChild(nextBtn);

        els.pagination.appendChild(infoDiv);
        els.pagination.appendChild(btnContainer);
    }

    // Helper: Buat Tombol Pagination
    function createPageBtn(text, isEnabled, onClick, isActive = false) {
        const btn = document.createElement('button');
        btn.innerHTML = text; 
        btn.className = `px-3 py-2 text-sm font-medium rounded-md transition-colors border ${
            isActive 
            ? 'bg-blue-600 text-white border-blue-600 shadow-sm' 
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
        }`;
        
        if (!isEnabled) {
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            btn.addEventListener('click', onClick);
        }
        return btn;
    }

    function createDots() {
        const span = document.createElement('span');
        span.className = 'px-2 py-2 text-sm text-gray-500';
        span.textContent = '...';
        return span;
    }

    function changePage(newPage) {
        state.currentPage = newPage;
        render(); // Re-render tabel dengan halaman baru (tanpa reload page)
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // 5. Event Listeners
    if (els.search) {
        let debounceTimer;
        els.search.addEventListener('input', (e) => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                state.filters.search = e.target.value;
                applyFilters();
            }, 300); // Debounce biar ga berat saat ngetik
        });
    }

    if (els.jurusan) {
        els.jurusan.addEventListener('change', (e) => {
            state.filters.jurusan = e.target.value;
            applyFilters();
        });
    }

    // 6. Init (Jalankan pertama kali)
    applyFilters(); 
});
</script>
