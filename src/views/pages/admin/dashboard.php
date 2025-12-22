<?php
// File: src/views/pages/admin/dashboard.php
?>

<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Statistics Cards -->
    <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 md:gap-6 mb-6 md:mb-8"> 
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5 sm:mb-1"><?php echo htmlspecialchars($stats['total'] ?? 0); ?></h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Total Usulan</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-2.5 md:p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-layer-group text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5 sm:mb-1"><?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?></h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Disetujui</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-2.5 md:p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-check-circle text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5 sm:mb-1"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Ditolak</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-2.5 md:p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-times-circle text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-yellow-900 bg-gradient-to-br from-yellow-300 to-yellow-400 hover:shadow-[0_0_20px_rgba(250,204,21,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <div class="order-2 sm:order-1">
                    <h3 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-0.5 sm:mb-1"><?php echo htmlspecialchars($stats['menunggu'] ?? 0); ?></h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Menunggu</p>
                </div>
                <div class="order-1 sm:order-2 p-2 sm:p-2.5 md:p-3 rounded-full bg-black/10 opacity-80 group-hover:opacity-100 transition-opacity text-yellow-800">
                    <i class="fas fa-hourglass-half text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Progress Workflow Sections -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 md:gap-8 mb-6 md:mb-8">
        <!-- Alur KAK -->
        <section class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100"> 
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 md:mb-8 gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 sm:h-6 bg-blue-500 rounded-full"></span>
                    Alur KAK Saat Ini
                </h3>
                <span class="text-[10px] sm:text-xs font-medium px-2 py-1 bg-blue-50 text-blue-600 rounded-md border border-blue-100">Live Status</span>
            </div>
            
            <div class="relative px-1 sm:px-2 pt-2 pb-8 sm:pb-10"> 
                <?php
                    $posisi_sekarang_kak = array_search($tahap_sekarang_kak, $tahapan_kak);
                if ($posisi_sekarang_kak === false) {
                    $posisi_sekarang_kak = 0;
                }
                    $total_langkah_kak = count($tahapan_kak) - 1;
                    $lebar_progress_kak = $total_langkah_kak > 0 ? ($posisi_sekarang_kak / $total_langkah_kak) * 100 : 0;
                ?>
                
                <!-- Progress Bar Container -->
                <div class="absolute top-[16px] sm:top-[20px] md:top-[22px] left-0 right-0 h-1 sm:h-1.5 z-0">
                    <!-- Background Bar -->
                    <div class="absolute inset-0 bg-gray-200 rounded-full"></div>
                    <!-- Progress Fill -->
                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(59,130,246,0.4)]" 
                         style="width: <?php echo $lebar_progress_kak; ?>%;"></div>
                </div> 

                <!-- Progress Steps -->
                <div class="relative z-10 flex justify-between w-full">
                    <?php foreach ($tahapan_kak as $index => $nama_tahap) :
                        $is_completed = $index < $posisi_sekarang_kak;
                        $is_active = $index == $posisi_sekarang_kak;

                        if ($is_active) {
                            $circle_class = 'bg-blue-500 border-blue-500 text-white shadow-lg ring-2 sm:ring-4 ring-blue-100 scale-110';
                            $text_class = 'text-blue-700 font-bold';
                        } elseif ($is_completed) {
                            $circle_class = 'bg-blue-500 border-blue-500 text-white shadow-md';
                            $text_class = 'text-blue-600 font-medium';
                        } else {
                            $circle_class = 'bg-white border-2 border-gray-300 text-gray-400';
                            $text_class = 'text-gray-400';
                        }
                        ?>
                    <div class="flex flex-col items-center group transition-transform hover:-translate-y-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center transition-all duration-300 <?php echo $circle_class; ?>"> 
                            <i class="fas <?php echo $icons_kak[$nama_tahap] ?? 'fa-circle'; ?> text-xs sm:text-sm"></i> 
                        </div>
                        <span class="mt-2 sm:mt-3 md:mt-4 text-[8px] sm:text-[10px] md:text-xs text-center max-w-[60px] sm:max-w-[70px] md:max-w-[80px] leading-tight <?php echo $text_class; ?>">
                            <?php echo htmlspecialchars($nama_tahap); ?>
                        </span> 
                    </div>
                    <?php endforeach; ?>
                </div>
            </div> 
        </section>

        <!-- Alur LPJ -->
        <section class="bg-white p-4 sm:p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 md:mb-8 gap-2">
                <h3 class="text-base sm:text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-5 sm:h-6 bg-green-500 rounded-full"></span>
                    Alur LPJ Saat Ini
                </h3>
                <span class="text-[10px] sm:text-xs font-medium px-2 py-1 bg-green-50 text-green-600 rounded-md border border-green-100">Live Status</span>
            </div>

            <div class="relative px-1 sm:px-2 pb-6 sm:pb-8"> 
                <?php
                    $posisi_sekarang_lpj = array_search($tahap_sekarang_lpj, $tahapan_lpj);
                if ($posisi_sekarang_lpj === false) {
                    $posisi_sekarang_lpj = 0;
                }
                    $total_langkah_lpj = count($tahapan_lpj) - 1;
                    $lebar_progress_lpj = $total_langkah_lpj > 0 ? ($posisi_sekarang_lpj / $total_langkah_lpj) * 100 : 0;
                ?>
                
                <!-- Progress Bar Container -->
                <div class="absolute top-[16px] sm:top-[20px] md:top-[22px] left-0 right-0 h-1 sm:h-1.5 z-0">
                    <!-- Background Bar -->
                    <div class="absolute inset-0 bg-gray-200 rounded-full"></div>
                    <!-- Progress Fill -->
                    <div class="absolute inset-y-0 left-0 bg-gradient-to-r from-green-500 to-green-600 rounded-full transition-all duration-1000 ease-out shadow-[0_0_8px_rgba(34,197,94,0.4)]" 
                         style="width: <?php echo $lebar_progress_lpj; ?>%;"></div>
                </div> 
 
                <!-- Progress Steps -->
                <div class="relative z-10 flex justify-between w-full">
                    <?php foreach ($tahapan_lpj as $index => $nama_tahap) :
                        $is_completed = $index < $posisi_sekarang_lpj;
                        $is_active = $index == $posisi_sekarang_lpj;

                        if ($is_active) {
                            $circle_class = 'bg-green-500 border-green-500 text-white shadow-lg ring-2 sm:ring-4 ring-green-100 scale-110';
                            $text_class = 'text-green-700 font-bold';
                        } elseif ($is_completed) {
                            $circle_class = 'bg-green-500 border-green-500 text-white shadow-md';
                            $text_class = 'text-green-600 font-medium';
                        } else {
                            $circle_class = 'bg-white border-2 border-gray-300 text-gray-400';
                            $text_class = 'text-gray-400';
                        }
                        ?>
                    <div class="flex flex-col items-center group transition-transform hover:-translate-y-1">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 md:w-11 md:h-11 rounded-full flex items-center justify-center transition-all duration-300 <?php echo $circle_class; ?>"> 
                            <i class="fas <?php echo $icons_lpj[$nama_tahap] ?? 'fa-circle'; ?> text-xs sm:text-sm"></i> 
                        </div>
                        <span class="mt-2 sm:mt-3 md:mt-4 text-[8px] sm:text-[10px] md:text-xs text-center max-w-[60px] sm:max-w-[70px] md:max-w-[80px] leading-tight <?php echo $text_class; ?>">
                            <?php echo htmlspecialchars($nama_tahap); ?>
                        </span> 
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Enhanced Table KAK with Filters & Pagination -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 md:mb-8 flex flex-col">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600 text-base sm:text-lg"></i>
                    List Pengajuan KAK
                </h3>
                
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <div class="relative flex-1 sm:flex-none">
                        <select id="filter-status-kak" style="color: #111827; background-color: #ffffff;" class="w-full sm:w-auto pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400">
                            <option value="" class="text-gray-500">Semua Status</option>
                            <option value="disetujui" class="text-gray-700">Disetujui</option>
                            <option value="ditolak" class="text-gray-700">Ditolak</option>
                            <option value="revisi" class="text-gray-700">Revisi</option>
                            <option value="menunggu" class="text-gray-700">Menunggu</option>
                        </select>
                        <i class="fas fa-filter absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <div class="relative flex-1 sm:flex-none">
                        <select id="filter-jurusan-kak" style="color: #111827; background-color: #ffffff;" class="w-full sm:w-auto pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400">
                            <option value="" class="text-gray-500">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer" class="text-gray-700">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan" class="text-gray-700">Teknik Grafika dan Penerbitan</option>
                            <option value="Teknik Elektro" class="text-gray-700">Teknik Elektro</option>
                            <option value="Administrasi Niaga" class="text-gray-700">Administrasi Niaga</option>
                            <option value="Akuntansi" class="text-gray-700">Akuntansi</option>
                            <option value="Teknik Mesin" class="text-gray-700">Teknik Mesin</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <button id="reset-filter-kak" class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-kak" placeholder="Cari nama kegiatan" class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Table View - Hidden on Mobile -->
        <div class="hidden md:block overflow-y-auto overflow-x-auto" style="max-height: 500px;">
            <table class="w-full min-w-[900px]" id="table-kak">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-kak" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Card View - Visible on Mobile -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="cards-kak" class="space-y-3 p-4">
                <!-- Cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination KAK -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-kak" class="font-semibold text-gray-800">0</span> dari <span id="total-kak" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-kak" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

    <!-- Enhanced Table LPJ with Filters & Pagination -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-6 md:mb-8 flex flex-col">
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 sm:gap-4">
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-green-600 text-base sm:text-lg"></i>
                    List Pengajuan LPJ
                </h3>
                
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <div class="relative flex-1 sm:flex-none">
                        <select id="filter-status-lpj" style="color: #111827; background-color: #ffffff;" class="w-full sm:w-auto pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400">
                            <option value="" class="text-gray-500">Semua Status</option>
                            <option value="menunggu_upload" class="text-gray-700">Perlu Upload</option>
                            <option value="menunggu" class="text-gray-700">Menunggu</option>
                            <option value="revisi" class="text-gray-700">Revisi</option>
                            <option value="setuju" class="text-gray-700">Setuju</option>
                        </select>
                        <i class="fas fa-filter absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <div class="relative flex-1 sm:flex-none">
                        <select id="filter-jurusan-lpj" style="color: #111827; background-color: #ffffff;" class="w-full sm:w-auto pl-9 sm:pl-10 pr-9 sm:pr-10 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400">
                            <option value="" class="text-gray-500">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer" class="text-gray-700">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan" class="text-gray-700">Teknik Grafika dan Penerbitan</option>
                            <option value="Teknik Elektro" class="text-gray-700">Teknik Elektro</option>
                            <option value="Administrasi Niaga" class="text-gray-700">Administrasi Niaga</option>
                            <option value="Teknik Mesin" class="text-gray-700">Teknik Mesin</option>
                            <option value="Sistem Informasi" class="text-gray-700">Sistem Informasi</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-2.5 sm:right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <button id="reset-filter-lpj" class="w-full sm:w-auto px-3 sm:px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-xs sm:text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        <span class="hidden sm:inline">Reset</span>
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-lpj" placeholder="Cari nama kegiatan" class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-xs sm:text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <i class="fas fa-search absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Table View - Hidden on Mobile -->
        <div class="hidden md:block overflow-y-auto overflow-x-auto" style="max-height: 500px;">
            <table class="w-full min-w-[1000px]" id="table-lpj">
                <thead class="bg-gradient-to-r from-green-50 to-emerald-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tenggat LPJ</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-lpj" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Card View - Visible on Mobile -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="cards-lpj" class="space-y-3 p-4">
                <!-- Cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination LPJ -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-lpj" class="font-semibold text-gray-800">0</span> dari <span id="total-lpj" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-lpj" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>

<script>
    window.dataKAK = <?= json_encode($list_kak, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
    window.dataLPJ = <?= json_encode($list_lpj, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="/docutrack/public/assets/js/admin/dashboard.js"></script>
