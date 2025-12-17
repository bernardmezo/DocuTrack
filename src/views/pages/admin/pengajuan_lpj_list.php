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

// Fungsi utilitas untuk formatting tanggal
function format_tanggal($date_string) {
    if (!$date_string) return '-';
    $timestamp = strtotime($date_string);
    // Ubah ke format 'd M Y' (contoh: 15 Des 2025)
    return date('d M Y', $timestamp);
}

// Fungsi utilitas untuk mendapatkan info status
function get_status_info($status_raw) {
    $status_raw = strtolower($status_raw);
    return match ($status_raw) {
        'setuju' => ['text' => 'Disetujui', 'class' => 'status-ok', 'icon' => 'fa-check-circle'],
        'revisi' => ['text' => 'Revisi', 'class' => 'status-rev', 'icon' => 'fa-exclamation-triangle'],
        'menunggu_upload' => ['text' => 'Perlu Upload', 'class' => 'status-upload', 'icon' => 'fa-upload'],
        'siap_submit' => ['text' => 'Siap Submit', 'class' => 'text-blue-700 bg-blue-100 border border-blue-200', 'icon' => 'fa-file-upload'],
        'menunggu' => ['text' => 'Menunggu', 'class' => 'status-wait', 'icon' => 'fa-hourglass-half'],
        default => ['text' => 'Tidak Diketahui', 'class' => 'text-red-800 bg-red-100 border border-red-200', 'icon' => 'fa-question-circle'],
    };
}

// Fungsi utilitas untuk mendapatkan info tombol aksi
function get_action_button_info($status_raw) {
    $status_raw = strtolower($status_raw);
    return match ($status_raw) {
        'menunggu_upload' => ['text' => 'Upload Bukti', 'color' => 'bg-orange-600 hover:bg-orange-700', 'icon' => 'fa-upload'],
        'siap_submit' => ['text' => 'Submit LPJ', 'color' => 'bg-blue-600 hover:bg-blue-700', 'icon' => 'fa-file-upload'],
        'menunggu' => ['text' => 'Lihat Status', 'color' => 'bg-gray-600 hover:bg-gray-700', 'icon' => 'fa-eye'],
        'setuju' => ['text' => 'Lihat Detail', 'color' => 'bg-green-600 hover:bg-green-700', 'icon' => 'fa-eye'],
        'revisi' => ['text' => 'Lihat Revisi', 'color' => 'bg-yellow-600 hover:bg-yellow-700', 'icon' => 'fa-eye'],
        default => ['text' => 'Review', 'color' => 'bg-indigo-600 hover:bg-indigo-700', 'icon' => 'fa-eye'],
    };
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <?php if($success_msg): ?>
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm" role="alert">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium"><?= htmlspecialchars($success_msg) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if($error_msg): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm" role="alert">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium"><?= htmlspecialchars($error_msg) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <section class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-6 md:mb-8 flex flex-col">
        
        <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-4 md:mb-6 pb-4 md:pb-5 border-b border-gray-200 gap-3">
            
            <div class="flex-shrink-0">
                <h2 class="text-xl md:text-2xl font-bold text-gray-800">Antrian LPJ</h2>
                <p class="text-sm text-gray-500 mt-1 hidden md:block">Daftar Laporan Pertanggungjawaban yang perlu diverifikasi.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 flex-wrap justify-end flex-shrink-0">
                
                <div class="relative flex-1 sm:flex-none w-full sm:w-auto min-w-[280px]">
                    <i class="fas fa-graduation-cap absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs sm:text-sm"></i>
                    <select id="filter-jurusan" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="select-filter w-full pl-9 pr-9 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                        <?php foreach ($jurusan_list as $jurusan): ?>
                            <option value="<?= htmlspecialchars(strtolower($jurusan)) ?>"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>

                <div class="relative flex-1 sm:flex-none w-full sm:w-auto min-w-[150px]">
                    <i class="fas fa-filter absolute top-1/2 left-3 -translate-y-1/2 text-gray-400 pointer-events-none z-10 text-xs sm:text-sm"></i>
                    <select id="filter-status" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="select-filter w-full pl-9 pr-9 py-2 sm:py-2.5 border border-gray-300 rounded-lg text-xs sm:text-sm font-semibold text-gray-700 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                        <option value="" style="color: #374151 !important; font-weight: 600;">Semua Status</option>
                        <option value="menunggu_upload">Perlu Upload</option>
                        <option value="siap_submit">Siap Submit</option>
                        <option value="menunggu">Menunggu</option>
                        <option value="revisi">Revisi</option>
                        <option value="setuju">Setuju</option>
                    </select>
                    <i class="fas fa-chevron-down absolute top-1/2 right-3 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
                </div>
                
                <button id="reset-filter" class="w-full sm:w-auto px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2 md:hidden">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Reset Filter</span>
                </button>
            </div>
            
        </div>
        
        <div class="relative mb-4">
            <i class="fas fa-search absolute top-1/2 left-3 sm:left-4 -translate-y-1/2 text-gray-400 z-10 text-xs sm:text-sm"></i>
            <input type="text" id="search-lpj-input" placeholder="Cari Kegiatan atau Mahasiswa..."
                   class="w-full pl-9 sm:pl-11 pr-4 py-2 sm:py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                   aria-label="Cari LPJ">
        </div>

        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="w-full min-w-[900px]">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-10">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[100px]">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[150px]">Tenggat LPJ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[120px]">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="table-body-desktop">
                        <?php if (!empty($list_lpj)): 
                            $nomor = 1;
                            foreach ($list_lpj as $item): 
                                $status_raw = strtolower($item['status'] ?? 'menunggu');
                                $status_info = get_status_info($status_raw);
                                $btn_info = get_action_button_info($status_raw);
                                $tgl_pengajuan = format_tanggal($item['tanggal_pengajuan'] ?? null);
                                $tenggat_lpj_date = $item['tenggatLpj'] ?? null;
                                
                                // LOGIKA TENGGAT WAKTU UNTUK DESKTOP
                                $deadline_html = '<span class="text-gray-600 font-medium">-</span>';
                                
                                if ($status_raw === 'menunggu_upload' && $tenggat_lpj_date) {
                                    $tenggat_ts = strtotime($tenggat_lpj_date);
                                    $hari_ini_ts = time();
                                    $diff_seconds = $tenggat_ts - $hari_ini_ts;
                                    $sisa_hari = ceil($diff_seconds / (60 * 60 * 24)); 

                                    $tenggat_display = format_tanggal($tenggat_lpj_date);

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
                                    
                                    $deadline_html = '<div class="flex flex-col items-start gap-1">
                                                        <span class="text-gray-900 font-medium">'.$tenggat_display.'</span>
                                                        <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border '.$badge_class.'">
                                                            <i class="fas '.$icon.'"></i><span>'.$text_status.'</span>
                                                        </div>
                                                    </div>';
                                } else if ($tenggat_lpj_date) {
                                    $tenggat_display = format_tanggal($tenggat_lpj_date);
                                    $deadline_html = '<span class="text-gray-900 font-medium">'.$tenggat_display.'</span>';
                                } else if ($status_raw === 'menunggu_upload') {
                                    $deadline_html = '<span class="px-2 py-0.5 rounded-full text-orange-700 bg-orange-100 border border-orange-200 text-xs font-medium">Belum Ditetapkan</span>';
                                }
                                
                        ?>
                        <tr class="data-row hover:bg-gray-50 transition-colors"
                            data-jurusan="<?php echo strtolower($item['jurusan'] ?? ''); ?>"
                            data-status="<?php echo $status_raw; ?>"
                            data-search="<?php echo strtolower(($item['nama'] ?? '') . ' ' . ($item['nama_mahasiswa'] ?? '') . ' ' . ($item['prodi'] ?? '')); ?>">
                            
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 font-medium">
                                <?php echo $nomor++; ?>.
                            </td>

                            <td class="px-6 py-5 text-sm">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 mb-1 leading-snug"><?php echo htmlspecialchars($item['nama'] ?? 'Tanpa Judul'); ?></span>
                                    <span class="text-gray-600 text-xs mt-1">
                                        <?php echo htmlspecialchars($item['nama_mahasiswa'] ?? 'N/A'); ?> 
                                        <span class="text-gray-500 font-medium">(<?php echo htmlspecialchars($item['nim'] ?? '-'); ?>)</span>
                                    </span>
                                    <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                        <i class="fas fa-graduation-cap mr-1 text-blue-500"></i><?php echo htmlspecialchars($item['prodi'] ?? $item['jurusan'] ?? '-'); ?>
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-500 text-xs"></i>
                                    <?php echo $tgl_pengajuan; ?>
                                </div>
                            </td>

                            <td class="px-6 py-5 text-sm">
                                <?php echo $deadline_html; ?>
                            </td>

                            <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                                <span class="px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 <?php echo $status_info['class']; ?>">
                                    <i class="fas <?php echo $status_info['icon']; ?>"></i>
                                    <?php echo $status_info['text']; ?>
                                </span>
                            </td>

                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                                <a href="/docutrack/public/admin/pengajuan-lpj/show/<?php echo $item['id'] ?? 0; ?>" 
                                   class="<?php echo $btn_info['color']; ?> text-white px-4 py-1.5 rounded-lg text-xs font-semibold shadow-md transition-all duration-300 transform hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                                    <i class="fas <?php echo $btn_info['icon']; ?>"></i> <?php echo $btn_info['text']; ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                        <tr id="empty-row-desktop">
                            <td colspan="6" class="px-6 py-10">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <div class="empty-state-text">Belum ada data pengajuan LPJ.</div>
                                    <div class="empty-state-subtext">Semua LPJ yang diajukan akan muncul di sini.</div>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr id="no-results-row-desktop" class="hidden">
                            <td colspan="6" class="px-6 py-10">
                                <div class="empty-state">
                                    <i class="fas fa-search-minus"></i>
                                    <div class="empty-state-text">Data tidak ditemukan.</div>
                                    <div class="empty-state-subtext">Coba ganti filter atau kata kunci pencarian.</div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="md:hidden overflow-y-auto" style="min-height: 400px;">
            <div id="mobile-lpj-list" class="space-y-3">
                <div id="empty-row-mobile" class="empty-state p-10 <?php echo !empty($list_lpj) ? 'hidden' : ''; ?>">
                    <i class="fas fa-inbox"></i>
                    <div class="empty-state-text">Belum ada data pengajuan LPJ.</div>
                    <div class="empty-state-subtext">Semua LPJ yang diajukan akan muncul di sini.</div>
                </div>
                <div id="no-results-row-mobile" class="empty-state p-10 hidden">
                    <i class="fas fa-search-minus"></i>
                    <div class="empty-state-text">Data tidak ditemukan.</div>
                    <div class="empty-state-subtext">Coba ganti filter atau kata kunci pencarian.</div>
                </div>
            </div>
        </div>

        <div class="p-3 sm:p-4 mt-4 border-t border-gray-200 bg-gray-50 rounded-lg flex-shrink-0">
            <div class="flex flex-col gap-3">
                <div id="pagination-buttons" class="flex gap-1 flex-wrap justify-center"></div>
                <div class="text-xs sm:text-sm text-gray-600 text-center">
                    Menampilkan <span id="showing-start" class="font-semibold text-gray-800">0</span> s.d. 
                    <span id="showing-end" class="font-semibold text-gray-800">0</span> dari 
                    <span id="total-records" class="font-semibold text-gray-800">0</span> data
                </div>
            </div>
        </div>

    </section>

</main>

<style>
    /* Mobile Card Styling */
    .mobile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-left: 4px solid #3b82f6; /* Warna biru untuk LPJ */
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    
    .mobile-card:active {
        transform: scale(0.98);
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .mobile-card-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .mobile-card-row {
        margin-bottom: 0.875rem;
    }
    
    .mobile-card-row:last-of-type {
        margin-bottom: 0;
    }
    
    .mobile-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-label i {
        color: #3b82f6;
        font-size: 0.75rem;
    }
    
    .mobile-card-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
    }
    
    .mobile-card-mahasiswa {
        font-size: 0.85rem;
        color: #4b5563;
        margin-top: 0.25rem;
    }
    
    .mobile-card-prodi {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .mobile-card-footer {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .mobile-card-date {
        font-size: 0.8rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-btn {
        width: 100%;
        padding: 0.75rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        text-decoration: none;
        color: white;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    }
    
    .mobile-card-btn:active {
        opacity: 0.9;
        transform: scale(0.98);
    }
    
    /* Status Badge Styling */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        border: 1px solid;
    }
    
    .status-badge i {
        font-size: 0.625rem;
    }
    
    .status-upload {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border-color: #fcd34d;
    }
    
    .status-wait {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #4b5563;
        border-color: #d1d5db;
    }

    .status-rev {
        background: linear-gradient(135deg, #fef9c3 0%, #fcd34d 100%);
        color: #78350f;
        border-color: #f59e0b;
    }

    .status-ok {
        background: linear-gradient(135deg, #d1fae5 0%, #6ee7b7 100%);
        color: #065f46;
        border-color: #34d399;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .empty-state-subtext {
        font-size: 0.85rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }
    
    /* Animations */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .mobile-card {
        animation: slideIn 0.3s ease forwards;
    }
    
    /* Pagination Buttons */
    .pagination-btn {
        min-width: 2.25rem;
        height: 2.25rem;
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        transition: all 0.2s;
    }
    
    .pagination-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-btn.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: transparent;
    }
    
    .pagination-btn:not(:disabled):hover:not(.active) {
        background: #f3f4f6;
    }
    
    .pagination-btn:not(:disabled):active {
        transform: scale(0.95);
    }
</style>

<script>
// Data PHP di-encode ke JS agar bisa dirender ulang
window.lpjData = <?php echo json_encode($list_lpj ?? []); ?>;

document.addEventListener('DOMContentLoaded', () => {
    const allData = window.lpjData || [];
    const ROWS_PER_PAGE = 5;
    
    let filteredData = [...allData];
    let currentPage = 1;
    
    const searchInput = document.getElementById('search-lpj-input');
    const filterJurusan = document.getElementById('filter-jurusan');
    const filterStatus = document.getElementById('filter-status');
    const resetButton = document.getElementById('reset-filter'); 
    
    const tableBodyDesktop = document.getElementById('table-body-desktop');
    const mobileList = document.getElementById('mobile-lpj-list');
    const paginationButtons = document.getElementById('pagination-buttons');
    
    const emptyRowDesktop = document.getElementById('empty-row-desktop');
    const noResultsRowDesktop = document.getElementById('no-results-row-desktop');
    const emptyRowMobile = document.getElementById('empty-row-mobile');
    const noResultsRowMobile = document.getElementById('no-results-row-mobile');

    const showingStart = document.getElementById('showing-start');
    const showingEnd = document.getElementById('showing-end');
    const totalRecords = document.getElementById('total-records');

    // --- FUNGSI UTILITY ---
    
    // HTML escape function
    function escapeHtml(str) {
        if (!str) return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
    
    // Status and Button Utility (Replikasi PHP di JS untuk render Mobile)
    function getStatusInfo(status_raw) {
        const statusMap = {
            'setuju': { text: 'Disetujui', class: 'status-ok', icon: 'fa-check-circle' },
            'revisi': { text: 'Revisi', class: 'status-rev', icon: 'fa-exclamation-triangle' },
            'menunggu_upload': { text: 'Perlu Upload', class: 'status-upload', icon: 'fa-upload' },
            // Kelas Tailwind eksplisit di JS
            'siap_submit': { text: 'Siap Submit', class: 'text-blue-700 bg-blue-100 border border-blue-200', icon: 'fa-file-upload' }, 
            'menunggu': { text: 'Menunggu', class: 'status-wait', icon: 'fa-hourglass-half' },
        };
        return statusMap[status_raw] || { text: 'Tidak Diketahui', class: 'text-red-800 bg-red-100 border border-red-200', icon: 'fa-question-circle' };
    }
    
    function getActionButtonInfo(status_raw) {
        const buttonAction = {
            'menunggu_upload': { text: 'Upload Bukti', color: 'bg-orange-600 hover:bg-orange-700', icon: 'fa-upload' },
            'siap_submit': { text: 'Submit LPJ', color: 'bg-blue-600 hover:bg-blue-700', icon: 'fa-file-upload' },
            'menunggu': { text: 'Lihat Status', color: 'bg-gray-600 hover:bg-gray-700', icon: 'fa-eye' },
            'setuju': { text: 'Lihat Detail', color: 'bg-green-600 hover:bg-green-700', icon: 'fa-eye' },
            'revisi': { text: 'Lihat Revisi', color: 'bg-yellow-600 hover:bg-yellow-700', icon: 'fa-eye' },
            'default': { text: 'Review', color: 'bg-indigo-600 hover:bg-indigo-700', icon: 'fa-eye' },
        };
        return buttonAction[status_raw] || buttonAction['default'];
    }
    
    function formatDate(date_string) {
        if (!date_string) return '-';
        return new Date(date_string).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'});
    }
    
    // Logic highlight filter
    function highlightFilter(selectElement) {
        const value = selectElement.value.toLowerCase();
        if (value) {
            selectElement.classList.add('font-bold', 'border-blue-500', 'bg-blue-50');
            selectElement.classList.remove('font-semibold', 'border-gray-300', 'bg-white');
        } else {
            selectElement.classList.add('font-semibold', 'border-gray-300', 'bg-white');
            selectElement.classList.remove('font-bold', 'border-blue-500', 'bg-blue-50');
        }
    }

    // --- FUNGSI FILTER DAN RENDER UTAMA ---

    function applyFilters() {
        const searchText = searchInput.value.toLowerCase().trim();
        const jurusanFilter = filterJurusan.value.toLowerCase().trim();
        const statusFilter = filterStatus.value.toLowerCase().trim();

        filteredData = allData.filter(item => {
            const searchData = (item.nama || '') + ' ' + (item.nama_mahasiswa || '') + ' ' + (item.prodi || '');
            const jurusan = (item.jurusan || '').toLowerCase();
            const status = (item.status || '').toLowerCase();
            
            return (!searchText || searchData.toLowerCase().includes(searchText)) &&
                   (!jurusanFilter || jurusan === jurusanFilter) &&
                   (!statusFilter || status === statusFilter);
        });

        highlightFilter(filterJurusan);
        highlightFilter(filterStatus);
        
        // Highlight search input (dibuat sederhana)
        if (searchText) {
            searchInput.classList.add('border-blue-500');
        } else {
            searchInput.classList.remove('border-blue-500');
        }

        currentPage = 1; 
        render();
    }
    
    function resetFilters() {
        searchInput.value = '';
        filterJurusan.value = '';
        filterStatus.value = '';
        applyFilters();
    }

    function render() {
        const start = (currentPage - 1) * ROWS_PER_PAGE;
        const end = start + ROWS_PER_PAGE;
        const pageData = filteredData.slice(start, end);
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
        
        // 1. Render Desktop Table
        if (tableBodyDesktop) {
            if (pageData.length === 0) {
                tableBodyDesktop.innerHTML = ''; 
                if (allData.length === 0) {
                    if(emptyRowDesktop) emptyRowDesktop.classList.remove('hidden');
                    if(noResultsRowDesktop) noResultsRowDesktop.classList.add('hidden');
                } else {
                    if(emptyRowDesktop) emptyRowDesktop.classList.add('hidden');
                    if(noResultsRowDesktop) noResultsRowDesktop.classList.remove('hidden');
                }
            } else {
                if(emptyRowDesktop) emptyRowDesktop.classList.add('hidden');
                if(noResultsRowDesktop) noResultsRowDesktop.classList.add('hidden');
                
                let desktopHtml = '';
                pageData.forEach((item, index) => {
                    const no = start + index + 1;
                    const status_raw = (item.status || 'menunggu').toLowerCase();
                    const status_info = getStatusInfo(status_raw);
                    const btn_info = getActionButtonInfo(status_raw);
                    const tgl_pengajuan = formatDate(item.tanggal_pengajuan);
                    const tenggat_lpj_date = item.tenggatLpj;
                    
                    // Logic Tenggat Waktu (Desktop Replikasi)
                    let deadlineHtml = '';
                    if (status_raw === 'menunggu_upload' && tenggat_lpj_date) {
                        const tenggat_ts = new Date(tenggat_lpj_date).getTime();
                        const hari_ini_ts = new Date().getTime();
                        const diff_seconds = (tenggat_ts - hari_ini_ts) / 1000;
                        const sisa_hari = Math.ceil(diff_seconds / (60 * 60 * 24));
                        
                        let badge_class;
                        let icon;
                        let text_status;
                        
                        if (sisa_hari < 0) {
                            badge_class = 'bg-red-50 border-red-200 text-red-700';
                            icon = 'fa-exclamation-triangle';
                            text_status = 'Terlewat ' + Math.abs(sisa_hari) + ' hari';
                        } else if (sisa_hari == 0) {
                            badge_class = 'bg-red-50 border-red-200 text-red-700';
                            icon = 'fa-exclamation-circle';
                            text_status = 'Hari Ini!';
                        } else if (sisa_hari <= 3) {
                            badge_class = 'bg-orange-50 border-orange-200 text-orange-700';
                            icon = 'fa-hourglass-end';
                            text_status = 'Sisa ' + sisa_hari + ' hari';
                        } else {
                            badge_class = 'bg-blue-50 border-blue-200 text-blue-700';
                            icon = 'fa-calendar-day';
                            text_status = 'Sisa ' + sisa_hari + ' hari';
                        }
                        
                        deadlineHtml = `
                            <div class="flex flex-col items-start gap-1">
                                <span class="text-gray-900 font-medium">${formatDate(tenggat_lpj_date)}</span>
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium border ${badge_class}">
                                    <i class="fas ${icon}"></i><span>${text_status}</span>
                                </div>
                            </div>`;
                    } else if (tenggat_lpj_date) {
                        deadlineHtml = `<span class="text-gray-900 font-medium">${formatDate(tenggat_lpj_date)}</span>`;
                    } else if (status_raw === 'menunggu_upload') {
                        deadlineHtml = `<span class="px-2 py-0.5 rounded-full text-orange-700 bg-orange-100 border border-orange-200 text-xs font-medium">Belum Ditetapkan</span>`;
                    } else {
                        deadlineHtml = `<span class="text-gray-600 font-medium">-</span>`;
                    }

                    
                    desktopHtml += `
                        <tr class="data-row hover:bg-gray-50 transition-colors"
                            data-jurusan="${escapeHtml((item.jurusan || '').toLowerCase())}"
                            data-status="${status_raw}"
                            data-search="${escapeHtml(((item.nama || '') + ' ' + (item.nama_mahasiswa || '') + ' ' + (item.prodi || '')).toLowerCase())}">
                            
                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-700 font-medium">${no}.</td>

                            <td class="px-6 py-5 text-sm">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-gray-900 mb-1 leading-snug">${escapeHtml(item.nama || 'Tanpa Judul')}</span>
                                    <span class="text-gray-600 text-xs mt-1">
                                        ${escapeHtml(item.nama_mahasiswa || 'N/A')} 
                                        <span class="text-gray-500 font-medium">(${escapeHtml(item.nim || '-')})</span>
                                    </span>
                                    <span class="text-gray-500 text-xs mt-0.5 font-medium">
                                        <i class="fas fa-graduation-cap mr-1 text-blue-500"></i>${escapeHtml(item.prodi || item.jurusan || '-')}
                                    </span>
                                </div>
                            </td>

                            <td class="px-6 py-5 whitespace-nowrap text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-500 text-xs"></i>
                                    ${tgl_pengajuan}
                                </div>
                            </td>

                            <td class="px-6 py-5 text-sm">${deadlineHtml}</td>

                            <td class="px-6 py-5 whitespace-nowrap text-xs font-semibold">
                                <span class="px-3 py-1.5 rounded-full inline-flex items-center gap-1.5 ${status_info.class}">
                                    <i class="fas ${status_info.icon}"></i>
                                    ${status_info.text}
                                </span>
                            </td>

                            <td class="px-6 py-5 whitespace-nowrap text-sm font-medium">
                                <a href="/docutrack/public/admin/pengajuan-lpj/show/${item.id || 0}" 
                                   class="<?php echo $btn_info['color']; ?> text-white px-4 py-1.5 rounded-lg text-xs font-semibold shadow-md transition-all duration-300 transform hover:scale-105 active:scale-95 inline-flex items-center gap-2">
                                    <i class="fas ${btn_info.icon}"></i> ${btn_info.text}
                                </a>
                            </td>
                        </tr>`;
                });
                tableBodyDesktop.innerHTML = desktopHtml;
            }
        }
        
        // 2. Render Mobile Cards
        if (mobileList) {
            if(emptyRowMobile) emptyRowMobile.classList.add('hidden');
            if(noResultsRowMobile) noResultsRowMobile.classList.add('hidden');
            
            if (pageData.length === 0) {
                 if (allData.length === 0) {
                    if(emptyRowMobile) emptyRowMobile.classList.remove('hidden');
                } else {
                    if(noResultsRowMobile) noResultsRowMobile.classList.remove('hidden');
                }
                mobileList.innerHTML = mobileList.querySelector('.empty-state') ? mobileList.innerHTML : '';
            } else {
                mobileList.innerHTML = pageData.map((item, index) => {
                    const no = start + index + 1;
                    const status_raw = (item.status || 'menunggu').toLowerCase();
                    const status_info = getStatusInfo(status_raw);
                    const btn_info = getActionButtonInfo(status_raw);
                    const tgl_pengajuan = formatDate(item.tanggal_pengajuan);
                    const tenggat_lpj_date = item.tenggatLpj;
                    
                    // Logic Tenggat Waktu (Mobile Card)
                    let deadlineHtml = '';
                    if (status_raw === 'menunggu_upload' && tenggat_lpj_date) {
                        const tenggat_ts = new Date(tenggat_lpj_date).getTime();
                        const hari_ini_ts = new Date().getTime();
                        const diff_seconds = (tenggat_ts - hari_ini_ts) / 1000;
                        const sisa_hari = Math.ceil(diff_seconds / (60 * 60 * 24));
                        
                        let deadline_badge_class = '';
                        let text_status = formatDate(tenggat_lpj_date);

                        if (sisa_hari < 0) {
                            deadline_badge_class = 'text-red-700 font-bold';
                            text_status += ' (Terlewat)';
                        } else if (sisa_hari == 0) {
                            deadline_badge_class = 'text-red-700 font-bold';
                            text_status += ' (Hari Ini!)';
                        } else if (sisa_hari <= 3) {
                            deadline_badge_class = 'text-orange-700 font-bold';
                            text_status += ` (Sisa ${sisa_hari} hari)`;
                        } else {
                            deadline_badge_class = 'text-blue-700';
                            text_status += ` (Sisa ${sisa_hari} hari)`;
                        }

                        deadlineHtml = `
                            <div class="mobile-card-row">
                                <div class="mobile-card-label">
                                    <i class="fas fa-hourglass-end"></i> Tenggat LPJ
                                </div>
                                <div class="mobile-card-value ${deadline_badge_class}">${escapeHtml(text_status)}</div>
                            </div>`;
                    } else if (tenggat_lpj_date) {
                         deadlineHtml = `
                            <div class="mobile-card-row">
                                <div class="mobile-card-label">
                                    <i class="fas fa-calendar-check"></i> Tenggat LPJ
                                </div>
                                <div class="mobile-card-value">${formatDate(tenggat_lpj_date)}</div>
                            </div>`;
                    }
                    
                    const namaMahasiswa = item.nama_mahasiswa || 'N/A';
                    
                    return `
                    <div class="mobile-card">
                        <div class="mobile-card-header">
                            <div class="mobile-card-number">#${no}</div>
                            <span class="status-badge ${status_info.class}">
                                <i class="fas ${status_info.icon}"></i>
                                ${escapeHtml(status_info.text)}
                            </span>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-clipboard-list"></i>
                                Nama Kegiatan
                            </div>
                            <div class="mobile-card-kegiatan">${escapeHtml(item.nama || 'Tanpa Judul')}</div>
                        </div>
                        
                        <div class="mobile-card-row">
                            <div class="mobile-card-label">
                                <i class="fas fa-user"></i>
                                Pengusul
                            </div>
                            <div class="mobile-card-mahasiswa">
                                ${escapeHtml(namaMahasiswa)}
                                <span class="text-gray-500">(${escapeHtml(item.nim || '-')})</span>
                            </div>
                            <div class="mobile-card-prodi">
                                <i class="fas fa-graduation-cap"></i>
                                ${escapeHtml(item.prodi || item.jurusan || '-')}
                            </div>
                        </div>
                        
                        ${deadlineHtml}
                        
                        <div class="mobile-card-footer">
                            <div class="mobile-card-date">
                                <i class="fas fa-calendar-alt text-gray-500"></i>
                                Tgl. Pengajuan: ${tgl_pengajuan}
                            </div>
                            <a href="/docutrack/public/admin/pengajuan-lpj/show/${item.id || 0}" class="mobile-card-btn ${btn_info.color}">
                                <i class="fas ${btn_info.icon}"></i>
                                ${btn_info.text}
                            </a>
                        </div>
                    </div>`;
                }).join('');
            }
        }
        
        // 3. Update pagination info
        const totalItems = filteredData.length;
        const showStart = totalItems === 0 ? 0 : start + 1;
        const showEnd = Math.min(end, totalItems);
        
        if (showingStart) showingStart.textContent = showStart;
        if (showingEnd) showingEnd.textContent = showEnd;
        if (totalRecords) totalRecords.textContent = totalItems;
        
        // 4. Render pagination
        renderPagination(totalPages);
    }

    // Render pagination (Diadaptasi dari kode dashboard)
    function renderPagination(totalPages) {
        if (!paginationButtons) return;
        
        if (totalPages <= 1) {
            paginationButtons.innerHTML = '';
            return;
        }
        
        let html = '';
        
        // Previous button
        const prevDisabled = currentPage === 1;
        html += `<button class="pagination-btn ${prevDisabled ? 'disabled' : ''}" 
                        onclick="goToPage(${currentPage - 1})" 
                        ${prevDisabled ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>`;
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                const isActive = i === currentPage;
                html += `<button class="pagination-btn ${isActive ? 'active' : ''}" 
                                onclick="goToPage(${i})">${i}</button>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<span class="px-2 text-gray-400 self-center">...</span>`;
            }
        }
        
        // Next button
        const nextDisabled = currentPage === totalPages;
        html += `<button class="pagination-btn ${nextDisabled ? 'disabled' : ''}" 
                        onclick="goToPage(${currentPage + 1})" 
                        ${nextDisabled ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>`;
        
        paginationButtons.innerHTML = html;
    }

    // Go to page function
    window.goToPage = function(page) {
        const totalPages = Math.ceil(filteredData.length / ROWS_PER_PAGE);
        if (page >= 1 && page <= totalPages && page !== currentPage) {
            currentPage = page;
            render();
            // Scroll ke atas section agar user melihat data baru
            document.querySelector('.overflow-y-auto').scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    // --- EVENT LISTENERS ---

    // Gunakan debounce untuk search input agar performa lebih baik
    let debounceTimer;
    searchInput?.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(applyFilters, 300);
    });
    
    filterJurusan?.addEventListener('change', applyFilters);
    filterStatus?.addEventListener('change', applyFilters);

    // Event listener untuk tombol reset filter
    resetButton?.addEventListener('click', resetFilters);
    
    // Initial render
    applyFilters();
});
</script>