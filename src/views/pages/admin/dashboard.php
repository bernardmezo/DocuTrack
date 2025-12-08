<?php
// File: src/views/pages/admin/dashboard.php
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Notification Bell Icon (Placeholder - Integrate with your existing UI) -->
    <div class="relative mb-6">
        <button id="notification-bell" class="relative p-2 rounded-full bg-white shadow-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fas fa-bell"></i>
            <?php if (!empty($unread_notifications_count) && $unread_notifications_count > 0): ?>
                <span class="absolute top-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-red-500 text-xs text-white flex items-center justify-center"><?= $unread_notifications_count ?></span>
            <?php endif; ?>
        </button>

        <!-- Notifications Dropdown (Hidden by default, show with JS) -->
        <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 hidden max-h-80 overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800">Notifikasi Anda</h4>
            </div>
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification):
                    $tipeLog = strtoupper($notification['tipe_log'] ?? 'INFORMASI');
                    $badgeClass = '';
                    switch ($tipeLog) {
                        case 'APPROVAL':
                        case 'PENCAIRAN':
                            $badgeClass = 'bg-green-100 text-green-800';
                            break;
                        case 'REJECTION':
                            $badgeClass = 'bg-red-100 text-red-800';
                            break;
                        case 'REVISION':
                            $badgeClass = 'bg-yellow-100 text-yellow-800';
                            break;
                        default:
                            $badgeClass = 'bg-blue-100 text-blue-800';
                            break;
                    }
                ?>
                    <a href="<?= htmlspecialchars($notification['link'] ?? '#') ?>" class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 <?= (isset($notification['status']) && $notification['status'] === 'BELUM_DIBACA') ? 'bg-blue-50' : '' ?>">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($notification['judul'] ?? 'Notifikasi') ?></p>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>"><?= htmlspecialchars($tipeLog) ?></span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($notification['pesan'] ?? '') ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($notification['created_at'] ?? '') ?></p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-4 py-3 text-sm text-gray-500">Tidak ada notifikasi baru.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript for Notification Dropdown Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bell = document.getElementById('notification-bell');
            const dropdown = document.getElementById('notification-dropdown');

            bell.addEventListener('click', function (event) {
                event.stopPropagation(); // Prevent document click from closing it immediately
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (event) {
                if (!dropdown.classList.contains('hidden') && !bell.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>

    <!-- Statistics Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"> 
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['total'] ?? 0); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-layer-group fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?></h3><p class="text-sm font-medium opacity-80">Disetujui</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-check-circle fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></h3><p class="text-sm font-medium opacity-80">Ditolak</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-times-circle fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-yellow-900 bg-gradient-to-br from-yellow-300 to-yellow-400 hover:shadow-[0_0_20px_rgba(250,204,21,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['menunggu'] ?? 0); ?></h3><p class="text-sm font-medium opacity-80">Menunggu</p></div>
                <div class="p-3 rounded-full bg-black/10 opacity-80 group-hover:opacity-100 transition-opacity text-yellow-800"><i class="fas fa-hourglass-half fa-xl"></i></div>
            </div>
        </div>
    </section>

    <!-- Progress Workflow Sections -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
        <!-- Alur KAK -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100 min-h-0"> 
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-6 bg-blue-500 rounded-full"></span>
                    Alur KAK Saat Ini
                </h3>
                <span class="text-xs font-medium px-2 py-1 bg-blue-50 text-blue-600 rounded-md border border-blue-100">Live Status</span>
            </div>
            
            <div class="relative px-2 pt-2 pb-10"> 
                <?php
                    $posisi_sekarang_kak = array_search($tahap_sekarang_kak, $tahapan_kak);
                if ($posisi_sekarang_kak === false) {
                    $posisi_sekarang_kak = 0;
                }
                    $total_langkah_kak = count($tahapan_kak) - 1;
                    $lebar_progress_kak = $total_langkah_kak > 0 ? ($posisi_sekarang_kak / $total_langkah_kak) * 100 : 0;
                ?>
                
                <!-- Progress Bar Background -->
                <div class="absolute top-[24px] left-0 w-full h-1.5 bg-gray-200 rounded-full z-0"></div>
                
                <!-- Progress Bar Fill -->
                <div class="absolute top-[24px] left-0 h-1.5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full z-0 transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(59,130,246,0.5)]" 
                     style="width: <?php echo $lebar_progress_kak; ?>%;"></div> 

                <!-- Progress Steps -->
                <div class="relative z-10 flex justify-between w-full">
                    <?php foreach ($tahapan_kak as $index => $nama_tahap) :
                        $is_completed = $index < $posisi_sekarang_kak;
                        $is_active = $index == $posisi_sekarang_kak;

                        if ($is_active) {
                            $circle_class = 'bg-blue-500 border-blue-500 text-white shadow-lg ring-4 ring-blue-100 scale-110';
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
                        <div class="w-11 h-11 rounded-full flex items-center justify-center transition-all duration-300 <?php echo $circle_class; ?>"> 
                            <i class="fas <?php echo $icons_kak[$nama_tahap] ?? 'fa-circle'; ?> text-sm"></i> 
                        </div>
                        <span class="mt-4 text-[10px] md:text-xs text-center max-w-[80px] leading-tight <?php echo $text_class; ?>">
                            <?php echo htmlspecialchars($nama_tahap); ?>
                        </span> 
                    </div>
                    <?php endforeach; ?>
                </div>
            </div> 
        </section>

        <!-- Alur LPJ -->
        <section class="bg-white p-6 md:p-8 rounded-2xl shadow-lg border border-gray-100 min-h-[240px]">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-1 h-6 bg-green-500 rounded-full"></span>
                    Alur LPJ Saat Ini
                </h3>
                <span class="text-xs font-medium px-2 py-1 bg-green-50 text-green-600 rounded-md border border-green-100">Live Status</span>
            </div>

            <div class="relative px-2 pb-8"> 
                <?php
                    $posisi_sekarang_lpj = array_search($tahap_sekarang_lpj, $tahapan_lpj);
                if ($posisi_sekarang_lpj === false) {
                    $posisi_sekarang_lpj = 0;
                }
                    $total_langkah_lpj = count($tahapan_lpj) - 1;
                    $lebar_progress_lpj = $total_langkah_lpj > 0 ? ($posisi_sekarang_lpj / $total_langkah_lpj) * 100 : 0;
                ?>
                
                <!-- Progress Bar Background -->
                <div class="absolute top-[22px] left-0 w-full h-1.5 bg-gray-200 rounded-full z-0"></div>
                
                <!-- Progress Bar Fill -->
                <div class="absolute top-[22px] left-0 h-1.5 bg-gradient-to-r from-green-500 to-green-600 rounded-full z-0 transition-all duration-1000 ease-out shadow-[0_0_10px_rgba(34,197,94,0.5)]" 
                     style="width: <?php echo $lebar_progress_lpj; ?>%;"></div> 
 
                <!-- Progress Steps -->
                <div class="relative z-10 flex justify-between w-full">
                    <?php foreach ($tahapan_lpj as $index => $nama_tahap) :
                        $is_completed = $index < $posisi_sekarang_lpj;
                        $is_active = $index == $posisi_sekarang_lpj;

                        if ($is_active) {
                            $circle_class = 'bg-green-500 border-green-500 text-white shadow-lg ring-4 ring-green-100 scale-110';
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
                        <div class="w-11 h-11 rounded-full flex items-center justify-center transition-all duration-300 <?php echo $circle_class; ?>"> 
                            <i class="fas <?php echo $icons_lpj[$nama_tahap] ?? 'fa-circle'; ?> text-sm"></i> 
                        </div>
                        <span class="mt-4 text-[10px] md:text-xs text-center max-w-[80px] leading-tight <?php echo $text_class; ?>">
                            <?php echo htmlspecialchars($nama_tahap); ?>
                        </span> 
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>

    <!-- Enhanced Table KAK with Filters & Pagination -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-blue-600"></i>
                    List Pengajuan KAK
                </h3>
                
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <select id="filter-status-kak" style="color: #111827; background-color: #ffffff;" class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400">
                            <option value="" class="text-gray-500">Semua Status</option>
                            <option value="disetujui" class="text-gray-700">Disetujui</option>
                            <option value="ditolak" class="text-gray-700">Ditolak</option>
                            <option value="revisi" class="text-gray-700">Revisi</option>
                            <option value="menunggu" class="text-gray-700">Menunggu</option>
                        </select>
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <div class="relative">
                        <select id="filter-jurusan-kak" style="color: #111827; background-color: #ffffff;" class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400">
                            <option value="" class="text-gray-500">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer" class="text-gray-700">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan" class="text-gray-700">Teknik Grafika dan Penerbitan</option>
                            <option value="Teknik Elektro" class="text-gray-700">Teknik Elektro</option>
                            <option value="Administrasi Niaga" class="text-gray-700">Administrasi Niaga</option>
                            <option value="Akuntansi" class="text-gray-700">Akuntansi</option>
                            <option value="Teknik Mesin" class="text-gray-700">Teknik Mesin</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <button id="reset-filter-kak" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        Reset
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-4 relative">
                <input type="text" id="search-kak" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <div class="overflow-y-auto overflow-x-auto" style="max-height: 500px;">
            <table class="w-full min-w-[900px]" id="table-kak">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Pengajuan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-kak" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination KAK -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span id="showing-kak" class="font-semibold text-gray-800">0</span> dari <span id="total-kak" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-kak" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

    <!-- Enhanced Table LPJ with Filters & Pagination -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-invoice text-green-600"></i>
                    List Pengajuan LPJ
                </h3>
                
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <select id="filter-status-lpj" style="color: #111827; background-color: #ffffff;" class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400">
                            <option value="" class="text-gray-500">Semua Status</option>
                            <option value="menunggu_upload" class="text-gray-700">Perlu Upload</option>
                            <option value="menunggu" class="text-gray-700">Menunggu</option>
                            <option value="revisi" class="text-gray-700">Revisi</option>
                            <option value="setuju" class="text-gray-700">Setuju</option>
                        </select>
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <div class="relative">
                        <select id="filter-jurusan-lpj" style="color: #111827; background-color: #ffffff;" class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400">
                            <option value="" class="text-gray-500">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer" class="text-gray-700">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan" class="text-gray-700">Teknik Grafika dan Penerbitan</option>
                            <option value="Teknik Elektro" class="text-gray-700">Teknik Elektro</option>
                            <option value="Administrasi Niaga" class="text-gray-700">Administrasi Niaga</option>
                            <option value="Teknik Mesin" class="text-gray-700">Teknik Mesin</option>
                            <option value="Sistem Informasi" class="text-gray-700">Sistem Informasi</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <button id="reset-filter-lpj" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        Reset
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-4 relative">
                <input type="text" id="search-lpj" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <div class="overflow-y-auto overflow-x-auto" style="max-height: 500px;">
            <table class="w-full min-w-[1000px]" id="table-lpj">
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
                <tbody id="tbody-lpj" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination LPJ -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span id="showing-lpj" class="font-semibold text-gray-800">0</span> dari <span id="total-lpj" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-lpj" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>

<script>
    window.dataKAK = <?= json_encode($list_kak) ?>;
    window.dataLPJ = <?= json_encode($list_lpj) ?>;
</script>
<script src="/docutrack/public/assets/js/admin/dashboard.js"></script>
