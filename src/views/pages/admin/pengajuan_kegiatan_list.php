<?php
// File: src/views/pages/admin/pengajuan_kegiatan.php

// Variabel $list_kegiatan diasumsikan dikirim dari AdminPengajuanKegiatanController
if (!isset($list_kegiatan)) {
    $list_kegiatan = [];
}

// Extract unique jurusan for filter
$jurusan_list = array_unique(array_filter(array_column($list_kegiatan, 'jurusan')));
sort($jurusan_list);
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">List Kegiatan Disetujui</h2>
                <p class="text-sm text-gray-500 mt-1">Kegiatan yang telah disetujui verifikator</p>
            </div>
            
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <div class="relative w-full md:w-auto">
                    <select id="filter-jurusan" style="color: #1f2937;" class="w-full md:min-w-[220px] pl-4 pr-10 py-2.5 text-sm font-medium bg-gray-50 border border-gray-300 rounded-full focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm cursor-pointer appearance-none hover:border-gray-400">
                        <option value="" style="color: #6b7280;">Semua Jurusan</option>
                        <option value="Teknik Informatika dan Komputer" style="color: #1f2937;">Teknik Informatika dan Komputer</option>
                        <option value="Teknik Grafika dan Penerbitan" style="color: #1f2937;">Teknik Grafika dan Penerbitan</option>
                        <option value="Teknik Elektro" style="color: #1f2937;">Teknik Elektro</option>
                        <option value="Administrasi Niaga" style="color: #1f2937;">Administrasi Niaga</option>
                        <option value="Akuntansi" style="color: #1f2937;">Akuntansi</option>
                        <option value="Teknik Mesin" style="color: #1f2937;">Teknik Mesin</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>

                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                    <input type="text" id="search-kegiatan-input" placeholder="Cari Kegiatan atau Mahasiswa..."
                           class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                           aria-label="Cari Kegiatan">
                </div>
            </div>
        </div>

        <div class="overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full min-w-[800px]">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="table-body">
                    <?php if (!empty($list_kegiatan)): 
                        $nomor = 1;
                        foreach ($list_kegiatan as $item): 
                            $tgl_pengajuan_ts = strtotime($item['tanggal_pengajuan'] ?? 'now');
                            $tgl_pengajuan_display = date('d M Y', $tgl_pengajuan_ts);
                            $nama_mahasiswa = $item['nama_mahasiswa'] ?? $item['pengusul'] ?? 'N/A';
                    ?>
                    <tr class="data-row hover:bg-gray-50 transition-colors"
                        data-jurusan="<?php echo strtolower($item['jurusan'] ?? ''); ?>"
                        data-search="<?php echo strtolower(($item['nama'] ?? '') . ' ' . $nama_mahasiswa); ?>">
                        
                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <?php echo $nomor++; ?>.
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 text-sm text-gray-800">
                            <div class="flex flex-col">
                                <span class="font-medium"><?php echo htmlspecialchars($item['nama'] ?? 'Tanpa Judul'); ?></span>
                                <span class="text-xs text-gray-500 mt-1">
                                    <?php echo htmlspecialchars($nama_mahasiswa); ?> 
                                    (<?php echo htmlspecialchars($item['nim'] ?? '-'); ?>), 
                                    <?php echo htmlspecialchars($item['prodi'] ?? $item['jurusan'] ?? '-'); ?>
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600">
                            <?php echo $tgl_pengajuan_display; ?>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold">
                            <span class="px-3 py-1.5 rounded-full text-green-700 bg-green-100 border border-green-200 inline-flex items-center gap-1.5">
                                <i class="fas fa-check-circle"></i>
                                Disetujui
                            </span>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2 items-center">
                                <a href="/docutrack/public/admin/pengajuan-kegiatan/show/<?php echo $item['id'] ?? 0; ?>?mode=rincian" 
                                   class="bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors">
                                    Rincian
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr id="empty-row">
                        <td colspan="5" class="text-center py-10 text-gray-500 italic">
                            Belum ada kegiatan yang disetujui.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr id="no-results-row" class="hidden">
                        <td colspan="5" class="text-center py-10 text-gray-500 italic">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center mt-6 pt-5 border-t border-gray-100 gap-4">
            <div class="text-sm text-gray-600">
                Menampilkan <span class="font-semibold text-gray-800" id="showing-start">0</span> - 
                <span class="font-semibold text-gray-800" id="showing-end">0</span> dari 
                <span class="font-semibold text-gray-800" id="total-records">0</span> data
            </div>

            <div class="flex items-center gap-2">
                <button id="prev-page" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                
                <div id="page-numbers" class="flex gap-1"></div>
                
                <button id="next-page" class="px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
        </div>

    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search-kegiatan-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const tableBody = document.getElementById('table-body');
    const allRows = Array.from(tableBody.querySelectorAll('.data-row'));
    const emptyRow = document.getElementById('empty-row');
    const noResultsRow = document.getElementById('no-results-row');
    
    let currentPage = 1;
    // REVISI: Mengubah jumlah baris per halaman menjadi 5
    const rowsPerPage = 5;
    let filteredRows = [...allRows];

    // Auto-adjust dropdown width based on content
    function adjustDropdownWidth(selectElement) {
        const tempSpan = document.createElement('span');
        tempSpan.style.visibility = 'hidden';
        tempSpan.style.position = 'absolute';
        tempSpan.style.whiteSpace = 'nowrap';
        tempSpan.style.fontSize = getComputedStyle(selectElement).fontSize;
        tempSpan.style.fontFamily = getComputedStyle(selectElement).fontFamily;
        tempSpan.style.fontWeight = getComputedStyle(selectElement).fontWeight;
        tempSpan.style.padding = '0 40px';
        document.body.appendChild(tempSpan);

        let maxWidth = 0;
        
        Array.from(selectElement.options).forEach(option => {
            tempSpan.textContent = option.text;
            const width = tempSpan.offsetWidth;
            if (width > maxWidth) {
                maxWidth = width;
            }
        });

        document.body.removeChild(tempSpan);

        const minWidth = 220;
        const maxWidthLimit = 400;
        const finalWidth = Math.max(minWidth, Math.min(maxWidth + 20, maxWidthLimit));
        
        selectElement.style.width = finalWidth + 'px';
    }

    if (filterJurusan) {
        adjustDropdownWidth(filterJurusan);
        
        // Highlight saat ada filter aktif
        filterJurusan.addEventListener('change', function() {
            if (this.value) {
                this.style.fontWeight = '600';
                this.style.borderColor = '#2563eb';
                this.style.backgroundColor = '#eff6ff';
            } else {
                this.style.fontWeight = '500';
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            }
        });
        
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                adjustDropdownWidth(filterJurusan);
            } else {
                filterJurusan.style.width = '100%';
            }
        });
    }

    function filterTable() {
        const searchText = searchInput.value.toLowerCase().trim();
        const jurusanFilter = filterJurusan.value;

        filteredRows = allRows.filter(row => {
            const searchData = row.dataset.search || '';
            const jurusan = row.dataset.jurusan || ''; // Filter by jurusan

            return (!searchText || searchData.includes(searchText)) &&
                (!jurusanFilter || jurusan === jurusanFilter.toLowerCase());
        });

        currentPage = 1;
        renderTable();
    }

    function renderTable() {
        allRows.forEach(row => row.style.display = 'none');
        
        if (filteredRows.length > 0) {
            const start = (currentPage - 1) * rowsPerPage;
            const paginatedRows = filteredRows.slice(start, start + rowsPerPage);
            
            paginatedRows.forEach((row, index) => {
                row.style.display = '';
                const numCell = row.querySelector('td:first-child');
                if(numCell) numCell.textContent = (start + index + 1) + '.';
            });
            
            if(emptyRow) emptyRow.style.display = 'none';
            if(noResultsRow) noResultsRow.style.display = 'none';
        } else {
            if(allRows.length > 0) {
                if(noResultsRow) noResultsRow.style.display = '';
                if(emptyRow) emptyRow.style.display = 'none';
            } else {
                if(emptyRow) emptyRow.style.display = '';
                if(noResultsRow) noResultsRow.style.display = 'none';
            }
        }
        updatePaginationUI();
    }

    function updatePaginationUI() {
        const total = filteredRows.length;
        const totalPages = Math.ceil(total / rowsPerPage) || 1; // Minimal 1 halaman
        
        // Validasi currentPage agar tidak out of bounds
        if (currentPage > totalPages) {
            currentPage = totalPages;
        }
        if (currentPage < 1) {
            currentPage = 1;
        }
        
        const start = total === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, total);
        
        document.getElementById('showing-start').textContent = start;
        document.getElementById('showing-end').textContent = end;
        document.getElementById('total-records').textContent = total;

        const pageContainer = document.getElementById('page-numbers');
        pageContainer.innerHTML = '';

        if (totalPages > 1) {
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    btn.className = `px-3 py-2 text-sm font-medium rounded-lg transition-all ${
                        currentPage === i 
                        ? 'bg-blue-600 text-white shadow-md' 
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                    }`;
                    btn.onclick = () => { 
                        currentPage = i; 
                        renderTable();
                        // Scroll ke atas tabel saat ganti halaman
                        tableBody.closest('section').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    };
                    btn.setAttribute('aria-label', `Halaman ${i}`);
                    btn.setAttribute('aria-current', currentPage === i ? 'page' : 'false');
                    pageContainer.appendChild(btn);
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    const span = document.createElement('span');
                    span.textContent = '...';
                    span.className = 'px-2 text-gray-400 self-center';
                    span.setAttribute('aria-hidden', 'true');
                    pageContainer.appendChild(span);
                }
            }
        }

        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages;
        
        // Update aria-label untuk accessibility
        prevBtn.setAttribute('aria-label', 'Halaman sebelumnya');
        nextBtn.setAttribute('aria-label', 'Halaman selanjutnya');
    }

    searchInput?.addEventListener('input', filterTable);
    filterJurusan?.addEventListener('change', filterTable);
    
    document.getElementById('prev-page')?.addEventListener('click', () => {
        if (currentPage > 1) { currentPage--; renderTable(); }
    });
    
    document.getElementById('next-page')?.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (currentPage < totalPages) { currentPage++; renderTable(); }
    });

    renderTable();
});
</script>