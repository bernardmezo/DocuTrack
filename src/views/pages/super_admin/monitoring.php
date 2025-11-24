<?php
// File: src/views/pages/super_admin/monitoring.php

if (!isset($list_proposal)) { $list_proposal = []; }
if (!isset($list_jurusan)) { $list_jurusan = []; }
if (!isset($pagination)) { $pagination = ['current_page' => 1, 'total_pages' => 1, 'total_items' => 0]; }
if (!isset($filters)) { $filters = ['status' => 'Semua', 'jurusan' => 'semua', 'search' => '']; }

/**
 * Helper Function: render_proposal_progress
 */
function render_proposal_progress($tahap_sekarang, $status) {
    $tahapan_all = ['Pengajuan', 'Verifikasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ'];
    
    $status_lower = strtolower($status);
    $is_ditolak = ($status_lower === 'ditolak');
    $is_approved = ($status_lower === 'approved');
    $is_menunggu = ($status_lower === 'menunggu');

    $posisi_sekarang = array_search($tahap_sekarang, $tahapan_all);
    if ($posisi_sekarang === false) $posisi_sekarang = 0; 
    
    $total_langkah = count($tahapan_all) - 1;
    
    // --- Logika Garis ---
    $lebar_garis_biru_selesai = 0;
    $lebar_garis_aktif = 0;
    $left_garis_aktif = 0;
    $color_garis_aktif = 'bg-blue-500';

    if ($posisi_sekarang > 0) {
        $lebar_garis_biru_selesai = ( ($posisi_sekarang - 1) / $total_langkah ) * 100;
        $lebar_garis_aktif = (1 / $total_langkah) * 100;
        $left_garis_aktif = $lebar_garis_biru_selesai;
    }
    
    if ($is_ditolak) {
        $color_garis_aktif = 'bg-red-500';
    } elseif ($is_approved) {
        $lebar_garis_biru_selesai = ( ($total_langkah - 1) / $total_langkah ) * 100;
        $lebar_garis_aktif = (1 / $total_langkah) * 100;
        $left_garis_aktif = $lebar_garis_biru_selesai;
        $color_garis_aktif = 'bg-green-500';
    } elseif ($is_menunggu && $posisi_sekarang === 0) {
        $lebar_garis_biru_selesai = 0;
        $lebar_garis_aktif = 0;
    }

    echo '<div class="relative w-full h-10 flex items-center">';
    echo '<div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 z-0 transform -translate-y-1/2"></div>';
    echo "<div class='absolute top-1/2 left-0 h-1 bg-blue-500 z-10 transform -translate-y-1/2 transition-all duration-500 ease-out' style='width: {$lebar_garis_biru_selesai}%;'></div>";
    
    if ($lebar_garis_aktif > 0) {
        echo "<div class='absolute top-1/2 h-1 {$color_garis_aktif} z-20 transform -translate-y-1/2 transition-all duration-500 ease-out' style='left: {$left_garis_aktif}%; width: {$lebar_garis_aktif}%;'></div>";
    }

    foreach ($tahapan_all as $index => $nama_tahap) {
        $left_position = $total_langkah > 0 ? ($index / $total_langkah) * 100 : 0;
        
        $is_completed = $index < $posisi_sekarang;
        $is_active = $index === $posisi_sekarang;

        $node_class = 'bg-gray-300'; $text_class = 'text-gray-400';

        if ($is_completed) {
            $node_class = 'bg-blue-500'; $text_class = 'text-blue-600';
        } elseif ($is_active) {
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
    
    echo '</div>';
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="monitoring-list" class="bg-gradient-to-br from-white to-blue-50/30 border border-blue-100/50 transition-all duration-300 hover:shadow-xl p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        
        <div class="mb-6 pb-5 border-b border-blue-100/50">
            <h2 class="text-xl md:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                Progres Proposal
            </h2>
            <p class="text-sm text-gray-500 mt-1">Monitor semua progres pengajuan KAK dan LPJ secara real-time.</p>
        </div>

        <!-- Form Filter dengan Layout Baru -->
        <form method="GET" action="" id="filter-form">
            
            <!-- BARIS 1: Tab Status (Full Width) -->
            <div class="mb-4">
                <div class="inline-flex p-1 bg-white rounded-xl shadow-sm border border-gray-200 w-full md:w-auto overflow-x-auto">
                    <button type="button" class="riwayat-filter-tab <?= $filters['status'] === 'Semua' ? 'active-tab' : '' ?>" data-status="Semua">
                        <i class="fas fa-list-ul mr-1.5"></i>Semua
                    </button>
                    <button type="button" class="riwayat-filter-tab <?= $filters['status'] === 'In Process' ? 'active-tab' : '' ?>" data-status="In Process">
                        <i class="fas fa-spinner mr-1.5"></i>In Process
                    </button>
                    <button type="button" class="riwayat-filter-tab <?= $filters['status'] === 'Menunggu' ? 'active-tab' : '' ?>" data-status="Menunggu">
                        <i class="fas fa-clock mr-1.5"></i>Menunggu
                    </button>
                    <button type="button" class="riwayat-filter-tab <?= $filters['status'] === 'Approved' ? 'active-tab' : '' ?>" data-status="Approved">
                        <i class="fas fa-check-circle mr-1.5"></i>Approved
                    </button>
                    <button type="button" class="riwayat-filter-tab <?= $filters['status'] === 'Ditolak' ? 'active-tab' : '' ?>" data-status="Ditolak">
                        <i class="fas fa-times-circle mr-1.5"></i>Ditolak
                    </button>
                </div>
                <input type="hidden" name="status" id="status-input" value="<?= htmlspecialchars($filters['status']) ?>">
            </div>

            <!-- BARIS 2: Filter Jurusan & Search (Sejajar, dengan spacing balance) -->
            <div class="flex flex-col md:flex-row gap-3 mb-6">
                
                <!-- Dropdown Filter Jurusan -->
                <div class="relative flex-shrink-0 w-full md:w-64">
                    <i class="fas fa-building absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 pointer-events-none z-10"></i>
                    
                    <select name="jurusan" id="jurusan-filter" 
                            class="w-full pl-11 pr-10 py-2.5 text-sm font-medium text-gray-900 bg-white rounded-xl border border-gray-200 focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-400/50 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-300"
                            style="color: #111827; background-color: #ffffff; -webkit-appearance: none;">
                        
                        <option value="semua" class="text-gray-500 font-medium" style="color: #6b7280;" <?= $filters['jurusan'] === 'semua' ? 'selected' : '' ?>>
                            Semua Jurusan
                        </option>
                        
                        <?php foreach ($list_jurusan as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>" class="text-gray-900" style="color: #111827; background-color: #ffffff;" <?= $filters['jurusan'] === $jurusan ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jurusan) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    
                    <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-400 pointer-events-none text-xs z-10"></i>
                </div>

                <!-- Search Input (Flex-grow untuk mengisi space) -->
                <div class="relative flex-grow">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                    <input type="text" name="search" id="search-monitoring-input" 
                           placeholder="Cari nama kegiatan atau pengusul..."
                           value="<?= htmlspecialchars($filters['search']) ?>"
                           class="peer w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 bg-white rounded-xl border border-gray-200 focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-400/50 transition-all duration-200 shadow-sm hover:border-gray-300"
                           style="color: #111827;"
                           autocomplete="off">
                </div>
            </div>
        </form>
        
        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <div class="w-full min-w-[900px]">
                <div class="grid grid-cols-3 gap-4 px-4 py-3 rounded-t-lg bg-blue-50/50 border-b border-blue-100">
                    <div class="text-xs font-bold text-blue-800 uppercase tracking-wider">Proposal Details</div>
                    <div class="text-xs font-bold text-blue-800 uppercase tracking-wider">Progres</div>
                    <div class="text-xs font-bold text-blue-800 uppercase tracking-wider">Status</div>
                </div>
                
                <div id="monitoring-table-body" class="divide-y divide-gray-100 bg-white/50 relative min-h-[200px]">
                    <!-- Loading Spinner (akan muncul saat navigasi) -->
                    <div id="loading-spinner" class="absolute inset-0 hidden items-center justify-center bg-white/50 backdrop-blur-sm z-40">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-3xl"></i>
                    </div>

                    <?php if (empty($list_proposal)): ?>
                        <div class="text-center py-10 text-gray-400 italic animate-fade-in">
                            Tidak ada proposal yang cocok dengan filter Anda.
                        </div>
                    <?php else: ?>
                        <?php 
                        $delay = 0;
                        foreach ($list_proposal as $item): 
                            $status_lower = strtolower($item['status']);
                            $row_class = 'bg-white';
                            $row_style = '';
                            
                            if ($status_lower === 'approved' || $status_lower === 'ditolak') {
                                $row_style = 'opacity-70';
                            } elseif ($status_lower === 'in process' || $status_lower === 'menunggu') {
                                $row_class = 'bg-blue-50/40';
                            }
                            
                            $status_class = match($status_lower) {
                                'approved' => 'text-green-700 bg-green-50 border-green-200',
                                'ditolak' => 'text-red-700 bg-red-50 border-red-200',
                                'in process' => 'text-blue-700 bg-blue-50 border-blue-200',
                                default => 'text-gray-700 bg-gray-100 border-gray-200'
                            };
                            
                            $delay += 100;
                        ?>
                            <div class="monitoring-row grid grid-cols-3 gap-4 px-4 py-6 items-center transition-colors hover:bg-blue-50/40 animate-reveal group border-b border-gray-50 last:border-b-0 <?= $row_class ?>" 
                                 style="<?= $row_style ?>; animation-delay: <?= $delay ?>ms;">
                                <div>
                                    <p class="text-sm text-gray-800 font-semibold"><?= htmlspecialchars($item['nama']) ?></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <p class="text-xs text-gray-600"><?= htmlspecialchars($item['pengusul']) ?></p>
                                        <span class="text-gray-300">•</span>
                                        <p class="text-xs text-blue-600 font-medium"><?= htmlspecialchars($item['jurusan']) ?></p>
                                    </div>
                                </div>
                                <div class="px-4">
                                    <?php render_proposal_progress($item['tahap_sekarang'], $item['status']); ?>
                                </div>
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold border <?= $status_class ?>">
                                        <?= htmlspecialchars($item['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center mt-6 pt-5 border-t border-blue-100/50 bg-white/30">
            <p class="text-sm text-gray-600 mb-4 md:mb-0">
                Menampilkan <span class="font-bold text-blue-700"><?= $pagination['showing_from'] ?></span> 
                s.d. <span class="font-bold text-blue-700"><?= $pagination['showing_to'] ?></span> 
                dari <span class="font-bold text-gray-800"><?= $pagination['total_items'] ?></span> hasil
            </p>
            
            <?php if ($pagination['total_pages'] > 1): ?>
                <nav class="flex items-center gap-1">
                    <?php 
                    $btnBase = "px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105";
                    $activeBtn = "bg-gradient-to-r from-blue-500 to-blue-600 text-white border-transparent shadow-md";
                    $inactiveBtn = "bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600";
                    $disabledBtn = "bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed opacity-60";
                    ?>
                    
                    <!-- Tombol Sebelumnya -->
                    <?php if ($pagination['current_page'] > 1): ?>
                        <a href="?page=<?= $pagination['current_page'] - 1 ?>&status=<?= urlencode($filters['status']) ?>&jurusan=<?= urlencode($filters['jurusan']) ?>&search=<?= urlencode($filters['search']) ?>" 
                           class="<?= $btnBase ?> <?= $inactiveBtn ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php else: ?>
                        <span class="<?= $btnBase ?> <?= $disabledBtn ?>">
                            <i class="fas fa-chevron-left"></i>
                        </span>
                    <?php endif; ?>
                    
                    <!-- Tombol Halaman -->
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i === $pagination['current_page']): ?>
                            <span class="<?= $btnBase ?> <?= $activeBtn ?>"><?= $i ?></span>
                        <?php else: ?>
                            <a href="?page=<?= $i ?>&status=<?= urlencode($filters['status']) ?>&jurusan=<?= urlencode($filters['jurusan']) ?>&search=<?= urlencode($filters['search']) ?>" 
                               class="<?= $btnBase ?> <?= $inactiveBtn ?>">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <!-- Tombol Selanjutnya -->
                    <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                        <a href="?page=<?= $pagination['current_page'] + 1 ?>&status=<?= urlencode($filters['status']) ?>&jurusan=<?= urlencode($filters['jurusan']) ?>&search=<?= urlencode($filters['search']) ?>" 
                           class="<?= $btnBase ?> <?= $inactiveBtn ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php else: ?>
                        <span class="<?= $btnBase ?> <?= $disabledBtn ?>">
                            <i class="fas fa-chevron-right"></i>
                        </span>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
        
    </section>
</main>

<script src="/docutrack/public/assets/js/super_admin/monitoring.js"></script>
<link rel="stylesheet" href="/docutrack/public/assets/css/super_admin/monitoring.css">