<?php
// File: src/views/pages/bendahara/dashboard.php
if (!isset($list_kak)) { $list_kak = []; }
if (!isset($list_lpj)) { $list_lpj = []; }
if (!isset($stats)) { 
    $stats = ['total' => 0, 'danaDiberikan' => 0, 'ditolak' => 0, 'menunggu' => 0];
}
?>

<main class="main-content font-poppins px-3 py-4 sm:p-6 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <?php if(isset($success_message) && $success_message): ?>
    <div class="mb-4 bg-green-50 border-l-4 border-green-500 p-3 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-2 text-base"></i>
            <p class="text-green-700 font-medium text-sm"><?= htmlspecialchars($success_message) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($error_message) && $error_message): ?>
    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-3 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-2 text-base"></i>
            <p class="text-red-700 font-medium text-sm"><?= htmlspecialchars($error_message) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5"> 
        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-lg transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold"><?php echo htmlspecialchars($stats['total'] ?? count($list_kak)); ?></h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Total Usulan</p>
                <div class="mt-2 text-right opacity-70">
                    <i class="fas fa-layer-group text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-lg transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold"><?php echo htmlspecialchars($stats['danaDiberikan'] ?? 0); ?></h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Dana Diberikan</p>
                <div class="mt-2 text-right opacity-70">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-lg transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Ditolak</p>
                <div class="mt-2 text-right opacity-70">
                    <i class="fas fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative group p-4 rounded-xl shadow-md overflow-hidden text-yellow-900 bg-gradient-to-br from-yellow-300 to-yellow-400 hover:shadow-lg transition-all duration-300">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col gap-1">
                <h3 class="text-3xl md:text-5xl font-bold"><?php echo htmlspecialchars($stats['menunggu'] ?? 0); ?></h3>
                <p class="text-xs md:text-sm font-medium opacity-90">Menunggu</p>
                <div class="mt-2 text-right opacity-70 text-yellow-800">
                    <i class="fas fa-hourglass-half text-2xl"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Table KAK -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-5 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-3">
                <i class="fas fa-file-alt text-blue-600"></i>
                <span>List Pencairan Dana</span>
            </h3>
            
            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3">
                <div class="relative flex-1">
                    <select id="filter-status-kak" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected style="color: #374151 !important;">Semua Status</option>
                        <option value="dana diberikan" style="color: #374151 !important;">Dana Diberikan</option>
                        <option value="ditolak" style="color: #374151 !important;">Ditolak</option>
                        <option value="menunggu" style="color: #374151 !important;">Menunggu</option>
                    </select>
                    <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                
                <div class="relative flex-1">
                    <select id="filter-jurusan-kak"
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                        <option value="Teknik Informatika dan Komputer" style="color: #374151 !important;">Teknik Informatika dan Komputer</option>
                        <option value="Teknik Grafika dan Penerbitan" style="color: #374151 !important;">Teknik Grafika dan Penerbitan</option>
                        <option value="Teknik Elektro" style="color: #374151 !important;">Teknik Elektro</option>
                        <option value="Teknik Mesin" style="color: #374151 !important;">Teknik Mesin</option>
                        <option value="Teknik Sipil" style="color: #374151 !important;">Teknik Sipil</option>
                        <option value="Administrasi Niaga" style="color: #374151 !important;">Administrasi Niaga</option>
                        <option value="Akuntansi" style="color: #374151 !important;">Akuntansi</option>
                    </select>
                    <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                
                <button id="reset-filter-kak" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-kak" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="min-w-full" id="table-kak">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-kak" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-kak-list" class="p-3 space-y-3"></div>
        </div>

        <!-- Pagination -->
        <div class="p-3 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col gap-3">
                <div id="pagination-kak" class="flex gap-1 flex-wrap justify-center"></div>
                <div class="text-xs text-gray-600 text-center">
                    Menampilkan <span id="showing-kak" class="font-semibold text-gray-800">0</span> dari <span id="total-kak" class="font-semibold text-gray-800">0</span> data
                </div>
            </div>
        </div>
    </section>

    <!-- Table LPJ -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-5 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-3">
                <i class="fas fa-file-invoice text-green-600"></i>
                <span>List LPJ</span>
            </h3>
            
            <!-- Filter Controls -->
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-3">
                <div class="relative flex-1">
                    <select id="filter-status-lpj" 
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="" selected style="color: #374151 !important;">Semua Status</option>
                        <option value="menunggu" style="color: #374151 !important;">Menunggu</option>
                        <option value="telah direvisi" style="color: #374151 !important;">Telah Direvisi</option>
                        <option value="revisi" style="color: #374151 !important;">Revisi</option>
                        <option value="disetujui" style="color: #374151 !important;">Disetujui</option>
                    </select>
                    <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                
                <div class="relative flex-1">
                    <select id="filter-jurusan-lpj"
                            style="color: #374151 !important; font-size: 14px !important;"
                            class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                        <option value="Teknik Informatika dan Komputer" style="color: #374151 !important;">Teknik Informatika dan Komputer</option>
                        <option value="Teknik Grafika dan Penerbitan" style="color: #374151 !important;">Teknik Grafika dan Penerbitan</option>
                        <option value="Teknik Elektro" style="color: #374151 !important;">Teknik Elektro</option>
                        <option value="Teknik Mesin" style="color: #374151 !important;">Teknik Mesin</option>
                        <option value="Teknik Sipil" style="color: #374151 !important;">Teknik Sipil</option>
                        <option value="Administrasi Niaga" style="color: #374151 !important;">Administrasi Niaga</option>
                        <option value="Akuntansi" style="color: #374151 !important;">Akuntansi</option>
                    </select>
                    <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                </div>
                
                <button id="reset-filter-lpj" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-redo text-xs"></i>
                    <span>Reset</span>
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-lpj" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="min-w-full" id="table-lpj">
                    <thead class="bg-gradient-to-r from-green-50 to-emerald-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tenggat LPJ</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-lpj" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-lpj-list" class="p-3 space-y-3"></div>
        </div>

        <!-- Pagination -->
        <div class="p-3 border-t border-gray-200 bg-gray-50">
            <div class="flex flex-col gap-3">
                <div id="pagination-lpj" class="flex gap-1 flex-wrap justify-center"></div>
                <div class="text-xs text-gray-600 text-center">
                    Menampilkan <span id="showing-lpj" class="font-semibold text-gray-800">0</span> dari <span id="total-lpj" class="font-semibold text-gray-800">0</span> data
                </div>
            </div>
        </div>
    </section>

    <!-- Table Riwayat Verifikasi -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-5 flex flex-col">
        <div class="p-4 border-b border-gray-200">
            <h3 class="text-base font-semibold text-gray-800 flex items-center gap-2 mb-3">
                <i class="fas fa-history text-purple-600"></i>
                <span>Riwayat Verifikasi</span>
            </h3>
            
            <!-- Filter Controls -->
            <!-- Simplified for now, can be expanded later -->
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="min-w-full" id="table-riwayat">
                    <thead class="bg-gradient-to-r from-purple-50 to-pink-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">NIM</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Prodi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tanggal Verifikasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Verifikator</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-riwayat" class="divide-y divide-gray-100 bg-white">
                        <?php if (empty($riwayat_verifikasi)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada riwayat verifikasi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($riwayat_verifikasi as $index => $item): ?>
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium"><?= $index + 1 ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($item['nama'] ?? '-') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($item['nim'] ?? '-') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"><?= htmlspecialchars($item['prodi'] ?? '-') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($item['tanggal_verifikasi'] ?? 'now'))) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= htmlspecialchars($item['status'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($item['verifikator'] ?? '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-riwayat-list" class="p-3 space-y-3">
                 <!-- Mobile logic will be handled by JS or simple PHP loop here -->
                 <?php foreach ($riwayat_verifikasi as $item): ?>
                    <div class="bg-white border rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-semibold text-sm text-gray-900"><?= htmlspecialchars($item['nama'] ?? '-') ?></h4>
                                <p class="text-xs text-gray-600 mt-0.5"><?= htmlspecialchars($item['nim'] ?? '-') ?> - <?= htmlspecialchars($item['prodi'] ?? '-') ?></p>
                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($item['verifikator'] ?? '-') ?></p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                <?= htmlspecialchars($item['status'] ?? '-') ?>
                            </span>
                        </div>
                        <div class="mt-3 text-xs text-gray-400">
                            <?= htmlspecialchars(date('d/m/Y H:i', strtotime($item['tanggal_verifikasi'] ?? 'now'))) ?>
                        </div>
                    </div>
                 <?php endforeach; ?>
            </div>
        </div>
    </section>

</main>

<style>
    /* ... (styles retained) ... */
    /* Mobile Card Styling - Optimized for Phone */
    .mobile-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    .mobile-card.blue-theme {
        border-left: 4px solid #3b82f6;
    }
    
    .mobile-card.green-theme {
        border-left: 4px solid #10b981;
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .mobile-card-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
    }
    
    .mobile-card-number.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .mobile-card-row {
        margin-bottom: 0.875rem;
    }
    
    .mobile-card-row:last-child {
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
        font-size: 0.75rem;
        color: #9ca3af;
    }
    
    .mobile-card-value {
        font-size: 0.875rem;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
        margin-bottom: 0.25rem;
    }
    
    .mobile-card-pengusul {
        font-size: 0.8rem;
        color: #6b7280;
    }
    
    .mobile-card-prodi {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .mobile-card-actions {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #f3f4f6;
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
    }
    
    .mobile-card-btn.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
    
    .mobile-card-btn.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .mobile-card-btn:active {
        opacity: 0.9;
        transform: scale(0.98);
    }
    
    /* Status Badge */
    .status-badge {
        padding: 0.375rem 0.625rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-badge i {
        font-size: 0.625rem;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #9ca3af;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
    }
    
    /* Pagination Buttons */
    .pagination-buttons button {
        min-width: 2.25rem;
        height: 2.25rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
    }
    
    .pagination-buttons button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    .pagination-buttons button.active {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-color: transparent;
    }
    
    .pagination-buttons button:not(:disabled):active {
        transform: scale(0.95);
    }
</style>

<script>
    window.dataKAK = <?= json_encode($list_kak) ?>;
    window.dataLPJ = <?= json_encode($list_lpj) ?>;
    window.dataRiwayat = <?= json_encode($riwayat_verifikasi ?? []) ?>;
</script>
<script src="/docutrack/public/assets/js/bendahara/dashboard.js"></script>