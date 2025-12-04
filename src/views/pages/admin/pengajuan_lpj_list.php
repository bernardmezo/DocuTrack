<?php
// File: src/views/pages/admin/pengajuan_lpj_list.php

// Flash messages
$success_msg = $_SESSION['flash_message'] ?? null;
$error_msg = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_message'], $_SESSION['flash_error']);

// 1. Ensure data available
if (!isset($list_lpj)) {
    $list_lpj = [];
}

// Extract unique jurusan for filter
$jurusan_list = array_unique(array_filter(array_column($list_lpj, 'jurusan')));
sort($jurusan_list);
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <?php if($success_msg): ?>
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium"><?= htmlspecialchars($success_msg) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($error_msg): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium"><?= htmlspecialchars($error_msg) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg mb-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <div>
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian LPJ</h2>
            </div>
            
            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <!-- Filter Jurusan -->
                <div class="relative w-full md:w-auto">
                    <select id="filter-jurusan" style="color: #1f2937;" class="w-full md:min-w-[220px] pl-4 pr-10 py-2.5 text-sm font-medium bg-gray-50 border border-gray-300 rounded-full focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm cursor-pointer appearance-none hover:border-gray-400">
                        <option value="" style="color: #6b7280;">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>" style="color: #1f2937;"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>

                <!-- Filter Status -->
                <div class="relative w-full md:w-40">
                    <select id="filter-status" class="w-full pl-4 pr-10 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-full focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm cursor-pointer appearance-none">
                        <option value="">Semua Status</option>
                        <option value="menunggu_upload">Perlu Upload</option>
                        <option value="siap_submit">Siap Submit</option>
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

        <!-- Table Container - Fixed Height -->
        <div class="border border-gray-100 rounded-lg" style="min-height: 400px;">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50">
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
                            $tenggat_lpj = $item['tenggatLpj'] ?? null;
                            
                            // --- LOGIKA TENGGAT WAKTU ---
                            $deadline_html = '<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 border border-gray-200 text-gray-600"><i class="fas fa-clock"></i><span>Menunggu</span></div>';

                            // KHUSUS STATUS MENUNGGU_UPLOAD
                            if ($status_raw === 'menunggu_upload') {
                                if ($tenggat_lpj) {
                                    $tenggat_ts = strtotime($tenggat_lpj);
                                    $hari_ini_ts = time();
                                    $diff_seconds = $tenggat_ts - $hari_ini_ts;
                                    $sisa_hari = ceil($diff_seconds / (60 * 60 * 24));

                                    if ($sisa_hari < 0) {
                                        $badge_class = 'bg-red-50 border-red-200 text-red-700';
                                        $icon = 'fa-exclamation-triangle';
                                        $text_status = 'Terlewat ' . abs($sisa_hari) . ' hari';
                                    } elseif ($sisa_hari == 0) {
                                        $badge_class = 'bg-red-50 border-red-200 text-red-700';
                                        $icon = 'fa-exclamation-circle';
                                        $text_status = 'Hari Ini!';
                                    } elseif ($sisa_hari <= 3) {
                                        $badge_class = 'bg-orange-50 border-orange-200 text-orange-700';
                                        $icon = 'fa-hourglass-end';
                                        $text_status = 'Sisa ' . $sisa_hari . ' hari';
                                    } else {
                                        $badge_class = 'bg-blue-50 border-blue-200 text-blue-700';
                                        $icon = 'fa-calendar-day';
                                        $text_status = 'Sisa ' . $sisa_hari . ' hari';
                                    }
                                    $deadline_html = '<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium border '.$badge_class.'"><i class="fas '.$icon.'"></i><span>'.$text_status.'</span></div>';
                                } else {
                                    $deadline_html = '<div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-medium bg-orange-50 border border-orange-200 text-orange-700"><i class="fas fa-upload"></i><span>Perlu Upload Bukti</span></div>';
                                }
                            }
                            // --- END LOGIKA TENGGAT ---

                            // Status Badge
                            $status_badge = match ($status_raw) {
                                'setuju' => 'text-green-600 bg-green-100',
                                'revisi' => 'text-yellow-600 bg-yellow-100',
                                'menunggu_upload' => 'text-orange-600 bg-orange-100',
                                'siap_submit' => 'text-blue-600 bg-blue-100',
                                default => 'text-gray-600 bg-gray-100',
                            };
                            
                            // Status Display Text
                            $status_display = match ($status_raw) {
                                'setuju' => 'Disetujui',
                                'revisi' => 'Revisi',
                                'menunggu_upload' => 'Perlu Upload',
                                'siap_submit' => 'Siap Submit',
                                default => 'Menunggu',
                            };
                    ?>
                    <tr class="data-row hover:bg-gray-50 transition-colors"
                        data-jurusan="<?php echo strtolower($item['jurusan'] ?? ''); ?>"
                        data-status="<?php echo $status_raw; ?>"
                        data-search="<?php echo strtolower(($item['nama'] ?? '') . ' ' . ($item['nama_mahasiswa'] ?? '') . ' ' . ($item['prodi'] ?? '')); ?>">
                        
                        <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                            <?php echo $nomor++; ?>.
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-4 text-sm text-gray-800">
                            <div class="flex flex-col">
                                <span class="font-medium"><?php echo htmlspecialchars($item['nama'] ?? 'Tanpa Judul'); ?></span>
                                <span class="text-xs text-gray-500 mt-1">
                                    <?php echo htmlspecialchars($item['nama_mahasiswa'] ?? 'N/A'); ?> 
                                    (<?php echo htmlspecialchars($item['nim'] ?? '-'); ?>), 
                                    <?php echo htmlspecialchars($item['prodi'] ?? $item['jurusan'] ?? '-'); ?>
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap text-sm text-gray-600">
                            <?php echo date('d M Y', $tgl_pengajuan_ts); ?>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-4 text-sm">
                            <?php echo $deadline_html; ?>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap text-xs font-semibold">
                            <span class="px-3 py-1 rounded-full <?php echo $status_badge; ?>">
                                <?php echo $status_display; ?>
                            </span>
                        </td>

                        <td class="px-4 py-3 md:px-6 md:py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2 items-center">
                                <?php 
                                $button_text = match ($status_raw) {
                                    'menunggu_upload' => 'Upload Bukti',
                                    'siap_submit' => 'Submit LPJ',
                                    'menunggu' => 'Lihat Status',
                                    'setuju' => 'Lihat Detail',
                                    'revisi' => 'Lihat Revisi',
                                    default => 'Review',
                                };
                                $button_color = match ($status_raw) {
                                    'menunggu_upload' => 'bg-orange-600 hover:bg-orange-700',
                                    'siap_submit' => 'bg-blue-600 hover:bg-blue-700',
                                    'setuju' => 'bg-green-600 hover:bg-green-700',
                                    default => 'bg-gray-600 hover:bg-gray-700',
                                };
                                ?>
                                <a href="/docutrack/public/admin/pengajuan-lpj/show/<?php echo $item['id'] ?? 0; ?>" 
                                   class="<?php echo $button_color; ?> text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium transition-colors">
                                    <?php echo $button_text; ?>
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
    const rowsPerPage = 5; // Maksimal 5 baris per halaman
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

    // Adjust jurusan dropdown on load
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

    // Highlight filter Status
    if (filterStatus) {
        filterStatus.addEventListener('change', function() {
            if (this.value) {
                this.style.fontWeight = '600';
                this.style.borderColor = '#2563eb';
                this.style.backgroundColor = '#eff6ff';
            } else {
                this.style.fontWeight = 'normal';
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            }
        });
    }

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

        // Highlight search input
        if (searchText) {
            searchInput.style.borderColor = '#000';
        } else {
            searchInput.style.borderColor = '';
        }

        currentPage = 1; // Reset ke halaman 1 saat filter
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
        const totalPages = Math.ceil(total / rowsPerPage) || 1;
        
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
                    btn.className = `px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 ${
                        currentPage === i 
                        ? 'bg-gradient-to-r from-green-500 to-green-600 text-white shadow-md' 
                        : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'
                    }`;
                    btn.onclick = () => { 
                        currentPage = i; 
                        renderTable();
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
        
        prevBtn.setAttribute('aria-label', 'Halaman sebelumnya');
        nextBtn.setAttribute('aria-label', 'Halaman selanjutnya');
    }

    searchInput?.addEventListener('input', filterTable);
    filterJurusan?.addEventListener('change', filterTable);
    filterStatus?.addEventListener('change', filterTable);
    
    document.getElementById('prev-page')?.addEventListener('click', () => {
        if (currentPage > 1) { 
            currentPage--; 
            renderTable();
        }
    });
    
    document.getElementById('next-page')?.addEventListener('click', () => {
        const totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        if (currentPage < totalPages) { 
            currentPage++; 
            renderTable();
        }
    });

    renderTable();
});
</script>