<link rel="stylesheet" href="/docutrack/public/assets/css/admin/pengajuan_lpj_list.css">
<?php
// File: src/views/pages/admin/pengajuan_lpj_list.php

// 1. MOCK DATA (Simulasi Database)
if (!isset($list_lpj)) {
    $list_lpj = [
        [
            'id' => 1,
            'nama' => 'Seminar Nasional Teknologi AI',
            'nama_mahasiswa' => 'Budi Santoso',
            'nim' => '190101001',
            'jurusan' => 'Teknik Informatika',
            'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-16 days')), 
            'status' => 'Setuju'
        ],
        [
            'id' => 2,
            'nama' => 'Workshop UI/UX Design 2024',
            'nama_mahasiswa' => 'Siti Aminah',
            'nim' => '190101002',
            'jurusan' => 'Desain Grafis',
            'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'status' => 'Revisi'
        ],
        [
            'id' => 3,
            'nama' => 'Lomba Coding Tingkat Kampus',
            'nama_mahasiswa' => 'Andi Pratama',
            'nim' => '190101003',
            'jurusan' => 'Sistem Informasi',
            'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-5 days')), 
            'status' => 'Setuju'
        ],
        [
            'id' => 4,
            'nama' => 'Pentas Seni Mahasiswa',
            'nama_mahasiswa' => 'Dewi Lestari',
            'nim' => '190101004',
            'jurusan' => 'Seni Musik',
            'tanggal_pengajuan' => date('Y-m-d H:i:s'), 
            'status' => 'Menunggu'
        ],
    ];
}

// Extract unique jurusan for filter
$jurusan_list = array_unique(array_filter(array_column($list_lpj, 'jurusan')));
sort($jurusan_list);
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian LPJ</h2>
            </div>
            
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <!-- Filter Jurusan -->
                <div class="relative w-full md:w-48">
                    <select id="filter-jurusan" class="w-full pl-4 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-full focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm cursor-pointer appearance-none">
                        <option value="">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?php echo htmlspecialchars($jurusan); ?>"><?php echo htmlspecialchars($jurusan); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>

                <!-- Filter Status -->
                <div class="relative w-full md:w-40">
                    <select id="filter-status" class="w-full pl-4 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-full focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm cursor-pointer appearance-none">
                        <option value="">Semua Status</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="revisi">Revisi</option>
                        <option value="setuju">Setuju</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>

                <!-- Search Input -->
                <div class="relative w-full md:w-80">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                    <input type="text" id="search-lpj-input" placeholder="Cari Kegiatan atau Mahasiswa..."
                           class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                           aria-label="Cari LPJ">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto max-h-96 border border-gray-100 rounded-lg">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Tenggat LPJ</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="table-body">
                    <?php if (!empty($list_lpj)): 
                        $nomor = 1;
                        foreach ($list_lpj as $item): 
                            $status_raw = strtolower($item['status'] ?? 'menunggu');
                            $tgl_pengajuan_ts = strtotime($item['tanggal_pengajuan'] ?? 'now');
                            
                            // --- LOGIKA TENGGAT WAKTU ---
                            $deadline_html = '<span class="text-gray-400 text-xs italic">Menunggu Persetujuan</span>';

                            // HANYA HITUNG JIKA STATUS 'SETUJU'
                            if ($status_raw === 'setuju') {
                                $tgl_deadline_ts = strtotime('+14 days', $tgl_pengajuan_ts);
                                $tgl_deadline_display = date('d M Y', $tgl_deadline_ts);
                                
                                $hari_ini_ts = time();
                                $diff_seconds = $tgl_deadline_ts - $hari_ini_ts;
                                $sisa_hari = ceil($diff_seconds / (60 * 60 * 24));

                                if ($sisa_hari < 0) {
                                    $badge_class = 'bg-red-100 text-red-700';
                                    $icon = 'fa-exclamation-circle';
                                    $text_status = 'Terlewat ' . abs($sisa_hari) . ' hari';
                                } elseif ($sisa_hari == 0) {
                                    $badge_class = 'bg-red-100 text-red-700';
                                    $icon = 'fa-bell';
                                    $text_status = 'Hari Ini!';
                                } elseif ($sisa_hari <= 3) {
                                    $badge_class = 'bg-orange-100 text-orange-700';
                                    $icon = 'fa-hourglass-end';
                                    $text_status = 'Sisa ' . $sisa_hari . ' hari';
                                } elseif ($sisa_hari <= 7) {
                                    $badge_class = 'bg-blue-100 text-blue-700';
                                    $icon = 'fa-hourglass-half';
                                    $text_status = 'Sisa ' . $sisa_hari . ' hari';
                                } else {
                                    $badge_class = 'bg-green-100 text-green-700';
                                    $icon = 'fa-calendar-check';
                                    $text_status = 'Sisa ' . $sisa_hari . ' hari';
                                }

                                $deadline_html = '
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-medium text-gray-700">'.$tgl_deadline_display.'</span>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold '.$badge_class.' w-fit">
                                            <i class="fas '.$icon.'"></i> '.$text_status.'
                                        </span>
                                    </div>';
                            }
                            // --- END LOGIKA TENGGAT ---

                            // Status Badge
                            $status_badge = match ($status_raw) {
                                'setuju' => 'text-green-600 bg-green-100',
                                'revisi' => 'text-yellow-600 bg-yellow-100',
                                default => 'text-gray-600 bg-gray-100',
                            };
                    ?>
                    <tr class="data-row hover:bg-gray-50 transition-colors"
                        data-jurusan="<?php echo strtolower($item['jurusan'] ?? ''); ?>"
                        data-status="<?php echo $status_raw; ?>"
                        data-search="<?php echo strtolower(($item['nama'] ?? '') . ' ' . ($item['nama_mahasiswa'] ?? '')); ?>">
                        
                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <?php echo $nomor++; ?>.
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 text-sm text-gray-800">
                            <div class="flex flex-col">
                                <span class="font-medium"><?php echo htmlspecialchars($item['nama'] ?? 'Tanpa Judul'); ?></span>
                                <span class="text-xs text-gray-500 mt-1"><?php echo htmlspecialchars($item['nama_mahasiswa'] ?? 'N/A'); ?> (<?php echo htmlspecialchars($item['nim'] ?? '-'); ?>), <?php echo htmlspecialchars($item['jurusan'] ?? '-'); ?></span>
                            </div>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600">
                            <?php echo date('d M Y', $tgl_pengajuan_ts); ?>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm">
                            <?php echo $deadline_html; ?>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold">
                            <span class="px-3 py-1 rounded-full <?php echo $status_badge; ?>">
                                <?php echo htmlspecialchars($item['status'] ?? 'Menunggu'); ?>
                            </span>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2 items-center">
                                <a href="/docutrack/public/admin/pengajuan-lpj/show/<?php echo $item['id'] ?? 0; ?>" 
                                   class="bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors">
                                    Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; else: ?>
                    <tr id="empty-row">
                        <td colspan="6" class="text-center py-10 text-gray-500 italic">
                            Belum ada data pengajuan LPJ.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr id="no-results-row" class="hidden">
                        <td colspan="6" class="text-center py-10 text-gray-500 italic">
                            Data tidak ditemukan.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
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
    const searchInput = document.getElementById('search-lpj-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const filterStatus = document.getElementById('filter-status');
    const tableBody = document.getElementById('table-body');
    const allRows = Array.from(tableBody.querySelectorAll('.data-row'));
    const emptyRow = document.getElementById('empty-row');
    const noResultsRow = document.getElementById('no-results-row');
    
    let currentPage = 1;
    const rowsPerPage = 10;
    let filteredRows = [...allRows];

    function filterTable() {
        const searchText = searchInput.value.toLowerCase().trim();
        const jurusanFilter = filterJurusan.value.toLowerCase();
        const statusFilter = filterStatus.value.toLowerCase();

        filteredRows = allRows.filter(row => {
            const searchData = row.dataset.search || '';
            const jurusan = row.dataset.jurusan || '';
            const status = row.dataset.status || '';
            return (!searchText || searchData.includes(searchText)) &&
                   (!jurusanFilter || jurusan === jurusanFilter) &&
                   (!statusFilter || status === statusFilter);
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
        const start = total === 0 ? 0 : (currentPage - 1) * rowsPerPage + 1;
        const end = Math.min(currentPage * rowsPerPage, total);
        
        document.getElementById('showing-start').textContent = start;
        document.getElementById('showing-end').textContent = end;
        document.getElementById('total-records').textContent = total;

        const totalPages = Math.ceil(total / rowsPerPage);
        const pageContainer = document.getElementById('page-numbers');
        pageContainer.innerHTML = '';

        if (totalPages > 1) {
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    const btn = document.createElement('button');
                    btn.textContent = i;
                    btn.className = `px-3 py-2 text-sm font-medium rounded-lg transition-all ${
                        currentPage === i 
                        ? 'bg-blue-600 text-white' 
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                    }`;
                    btn.onclick = () => { currentPage = i; renderTable(); };
                    pageContainer.appendChild(btn);
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    const span = document.createElement('span');
                    span.textContent = '...';
                    span.className = 'px-2 text-gray-400 self-center';
                    pageContainer.appendChild(span);
                }
            }
        }

        document.getElementById('prev-page').disabled = currentPage === 1;
        document.getElementById('next-page').disabled = currentPage === totalPages || totalPages === 0;
    }

    searchInput?.addEventListener('input', filterTable);
    filterJurusan?.addEventListener('change', filterTable);
    filterStatus?.addEventListener('change', filterTable);
    
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