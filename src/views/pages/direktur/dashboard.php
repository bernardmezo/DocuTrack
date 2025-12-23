<?php
// File: src/views/pages/direktur/dashboard.php
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Statistics Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"> 
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['total'] ?? count($list_kak)); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan Masuk</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-layer-group fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?></h3><p class="text-sm font-medium opacity-80">Total Usuan Disetujui</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-check-circle fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan Ditolak</p></div>
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

    <!-- Charts Section -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Bar Chart - Usulan Per Prodi -->
        <div class="lg:col-span-2 bg-gradient-to-br from-white to-cyan-50/40 rounded-2xl shadow-lg p-7 hover:shadow-2xl transition-all duration-300 border border-cyan-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-cyan-600 to-blue-700 bg-clip-text text-transparent mb-1">
                        <i class="fas fa-chart-bar mr-2"></i>Usulan Per Jurusan
                    </h2>
                    <p class="text-sm text-gray-500">Distribusi pengajuan berdasarkan jurusan</p>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0 flex-wrap">
                    <button class="filter-btn-prodi active px-3 md:px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-xs md:text-sm font-medium hover:from-cyan-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 whitespace-nowrap" data-filter="today">
                        <i class="fas fa-calendar-day mr-1"></i> Hari Ini
                    </button>
                    <button class="filter-btn-prodi px-3 md:px-4 py-2 rounded-lg bg-white border-2 border-gray-200 text-gray-700 text-xs md:text-sm font-medium hover:border-cyan-400 hover:text-cyan-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap" data-filter="week">
                        <i class="fas fa-calendar-week mr-1"></i> Minggu
                    </button>
                    <button class="filter-btn-prodi px-3 md:px-4 py-2 rounded-lg bg-white border-2 border-gray-200 text-gray-700 text-xs md:text-sm font-medium hover:border-cyan-400 hover:text-cyan-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap" data-filter="month">
                        <i class="fas fa-calendar-alt mr-1"></i> Bulan
                    </button>
                    <button class="filter-btn-prodi px-3 md:px-4 py-2 rounded-lg bg-white border-2 border-gray-200 text-gray-700 text-xs md:text-sm font-medium hover:border-cyan-400 hover:text-cyan-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap" data-filter="year">
                        <i class="fas fa-calendar mr-1"></i> Tahun
                    </button>
                </div>
            </div>
            
            <!-- Summary Info -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-gradient-to-br from-cyan-50 to-cyan-100/50 rounded-xl p-3 border border-cyan-200/50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-cyan-500 shadow-sm"></div>
                        <span class="text-xs text-gray-600 font-medium">Total Prodi</span>
                    </div>
                    <p class="text-2xl font-bold text-cyan-600 mt-1" id="totalProdi">-</p>
                </div>
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 rounded-xl p-3 border border-emerald-200/50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500 shadow-sm"></div>
                        <span class="text-xs text-gray-600 font-medium">Tertinggi</span>
                    </div>
                    <p class="text-2xl font-bold text-emerald-600 mt-1" id="maxUsulan">-</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 rounded-xl p-3 border border-amber-200/50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-amber-500 shadow-sm"></div>
                        <span class="text-xs text-gray-600 font-medium">Rata-rata</span>
                    </div>
                    <p class="text-2xl font-bold text-amber-600 mt-1" id="avgUsulan">-</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 rounded-xl p-3 border border-blue-200/50">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-blue-500 shadow-sm"></div>
                        <span class="text-xs text-gray-600 font-medium">Total Usulan</span>
                    </div>
                    <p class="text-2xl font-bold text-blue-600 mt-1" id="totalUsulanProdi">-</p>
                </div>
            </div>

            <div class="relative h-96 bg-white/50 rounded-xl p-4 backdrop-blur-sm">
                <canvas id="prodiChart"></canvas>
            </div>
        </div>

        <!-- Donut Chart -->
        <div class="bg-gradient-to-br from-white to-rose-50/30 rounded-2xl shadow-lg p-7 hover:shadow-2xl transition-all duration-300 border border-rose-100/50">
            <div class="mb-6">
                <h2 class="text-2xl font-bold bg-gradient-to-r from-rose-600 to-pink-600 bg-clip-text text-transparent mb-1">
                    <i class="fas fa-chart-pie mr-2"></i>Distribusi Status
                </h2>
                <p class="text-sm text-gray-500">Status pengajuan saat ini</p>
            </div>
            <div class="flex items-center justify-center mb-6">
                <div class="relative w-64 h-64">
                    <canvas id="donutChart"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-5xl font-bold bg-gradient-to-br from-cyan-600 to-blue-600 bg-clip-text text-transparent" id="totalCount"><?php echo htmlspecialchars($stats['total'] ?? 0); ?></span>
                        <span class="text-sm text-gray-500 font-semibold mt-1">Total Usulan</span>
                    </div>
                </div>
            </div>
            <div class="space-y-3 bg-white/60 rounded-xl p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-green-50 transition-colors duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-green-400 to-green-600 shadow-lg group-hover:scale-110 transition-transform duration-200"></div>
                        <span class="text-sm text-gray-700 font-semibold">Disetujui</span>
                    </div>
                    <span class="text-lg font-bold text-green-600" id="countDisetujui"><?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?></span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 shadow-lg group-hover:scale-110 transition-transform duration-200"></div>
                        <span class="text-sm text-gray-700 font-semibold">Menunggu</span>
                    </div>
                    <span class="text-lg font-bold text-blue-600" id="countMenunggu"><?php echo htmlspecialchars($stats['menunggu'] ?? 0); ?></span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-orange-50 transition-colors duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg group-hover:scale-110 transition-transform duration-200"></div>
                        <span class="text-sm text-gray-700 font-semibold">Revisi</span>
                    </div>
                    <span class="text-lg font-bold text-orange-600" id="countRevisi"><?php echo htmlspecialchars($stats['revisi'] ?? 0); ?></span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors duration-200 group">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gradient-to-br from-red-400 to-red-600 shadow-lg group-hover:scale-110 transition-transform duration-200"></div>
                        <span class="text-sm text-gray-700 font-semibold">Ditolak</span>
                    </div>
                    <span class="text-lg font-bold text-red-600" id="countDitolak"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></span>
                </div>
            </div>
        </div>
    </section>
    
    <!-- New Chart Section: Dana Keluar Per Jurusan -->
    <section class="grid grid-cols-1 gap-6 mb-8">
        <div class="bg-gradient-to-br from-white to-emerald-50/40 rounded-2xl shadow-lg p-7 hover:shadow-2xl transition-all duration-300 border border-emerald-100/50">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-green-700 bg-clip-text text-transparent mb-1">
                        <i class="fas fa-chart-line mr-2"></i>Total Dana Keluar Per Jurusan
                    </h2>
                    <p class="text-sm text-gray-500">Distribusi total dana yang dicairkan berdasarkan jurusan</p>
                </div>
            </div>
            
            <div class="relative h-96 bg-white/50 rounded-xl p-4 backdrop-blur-sm">
                <canvas id="danaPerJurusanChart"></canvas>
            </div>
        </div>
    </section>

    <!-- Daftar Pengajuan Table -->
    <section class="bg-white rounded-xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] overflow-hidden border border-gray-100">
    <div class="p-5 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
        <div class="flex items-center gap-4">
            <div class="hidden sm:flex h-11 w-11 items-center justify-center bg-indigo-50 text-indigo-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-800 tracking-tight">Daftar Pengajuan</h3>
                <p class="text-sm text-slate-500">Kelola dan pantau seluruh usulan anggaran</p>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
            <div class="relative w-full sm:w-72 group">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs transition-colors group-focus-within:text-indigo-500"></i>
                <input type="text" id="searchPengajuan" placeholder="Cari nama kegiatan..." 
                       class="w-full pl-9 pr-4 py-2 text-sm bg-slate-50 border-transparent rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all outline-none text-slate-600">
            </div>
            <div class="relative w-full sm:w-56">
                <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                <select id="filterJurusan" class="w-full pl-9 pr-8 py-2 text-sm bg-slate-50 border-transparent rounded-lg focus:bg-white focus:ring-2 focus:ring-indigo-100 focus:border-indigo-400 transition-all outline-none appearance-none text-slate-600 cursor-pointer">
                    <option value="">Semua Jurusan</option>
                    <?php foreach ($list_jurusan as $jurusan): ?>
                        <option value="<?= htmlspecialchars($jurusan) ?>">
                            <?= htmlspecialchars($jurusan) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-[10px] pointer-events-none"></i>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-y border-slate-100">
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Detail Kegiatan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Unit Kerja / Jurusan</th>
                    <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Estimasi Dana</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100" id="pengajuanTableBody">
                <tr class="hover:bg-indigo-50/30 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="block font-medium text-slate-700 group-hover:text-indigo-600 transition-colors">Seminar Nasional Teknologi 2024</span>
                        <span class="text-[10px] text-slate-400 uppercase font-medium">ID: REQ-9921</span>
                    </td>
                    <td class="px-6 py-4 text-slate-500">Teknik Informatika</td>
                    <td class="px-6 py-4 text-right">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full font-bold text-sm">Rp 15.000.000</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-slate-100 bg-white">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm">
            <p class="text-slate-500 italic sm:not-italic">
                Menampilkan <span class="font-bold text-slate-800" id="showingStart">1</span> - <span class="font-bold text-slate-800" id="showingEnd">5</span> dari <span class="text-slate-400" id="showingTotal">100 data</span>
            </p>
            <div class="flex items-center gap-1" id="paginationContainer">
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50"><i class="fas fa-chevron-left text-[10px]"></i></button>
                <button class="h-8 w-8 flex items-center justify-center rounded-lg bg-indigo-600 text-white font-medium shadow-sm shadow-indigo-200">1</button>
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">2</button>
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 text-slate-400 hover:bg-slate-50"><i class="fas fa-chevron-right text-[10px]"></i></button>
            </div>
        </div>
    </div>
</section>

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
    .filter-btn-prodi.active {
        background: linear-gradient(135deg, #06b6d4 0%, #2563eb 100%) !important;
        color: white !important;
        border-color: transparent !important;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4) !important;
    }
    
    /* Custom scrollbar for table */
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #9333ea 0%, #4f46e5 100%);
        border-radius: 10px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #7e22ce 0%, #4338ca 100%);
    }

    /* Mobile card animation */
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

    .card-item {
        animation: slideIn 0.3s ease-out forwards;
    }

    /* Responsive text truncation */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<script>
    // ===============================================
    // DATA INITIALIZATION FROM SERVER
    // ===============================================
    var phpDataKAK = <?= json_encode($list_kak ?? []) ?>;
    var listJurusan = <?= json_encode($list_jurusan ?? []) ?>;

    console.log('📊 Data Initialization:');
    console.log('- KAK:', phpDataKAK.length, 'items');
    console.log('- Jurusan:', listJurusan);

    // Use real data dari server
    window.dataKAK = phpDataKAK;

    // ===============================================
    // HELPER FUNCTIONS
    // ===============================================
    function filterDataByPeriod(data, period) {
        var now = new Date();
        var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        
        return data.filter(function(item) {
            var itemDate = new Date(item.createdAt);
            
            switch(period) {
                case 'today':
                    return itemDate >= today;
                case 'week':
                    var weekAgo = new Date(today);
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    return itemDate >= weekAgo;
                case 'month':
                    var monthAgo = new Date(today);
                    monthAgo.setMonth(monthAgo.getMonth() - 1);
                    return itemDate >= monthAgo;
                case 'year':
                    var yearAgo = new Date(today);
                    yearAgo.setFullYear(yearAgo.getFullYear() - 1);
                    return itemDate >= yearAgo;
                default:
                    return true;
            }
        });
    }

    function getJurusanData(period) {
        var filteredData = filterDataByPeriod(window.dataKAK, period);
        
        var jurusanCounts = {};
        listJurusan.forEach(function(jurusan) {
            jurusanCounts[jurusan] = 0;
        });
        
        filteredData.forEach(function(item) {
            var jurusan = item.jurusanPenyelenggara;
            if (jurusanCounts.hasOwnProperty(jurusan)) {
                jurusanCounts[jurusan]++;
            }
        });
        
        // Sort by count descending
        var sortedJurusan = Object.keys(jurusanCounts).sort(function(a, b) {
            return jurusanCounts[b] - jurusanCounts[a];
        });
        
        var values = sortedJurusan.map(function(jurusan) {
            return jurusanCounts[jurusan];
        });
        
        return { labels: sortedJurusan, data: values };
    }

    function updateSummaryStats(data) {
        var total = data.reduce(function(sum, val) { return sum + val; }, 0);
        var max = data.length > 0 ? Math.max.apply(null, data) : 0;
        var avg = data.length > 0 ? (total / data.length).toFixed(1) : 0;
        var totalJurusan = data.filter(function(val) { return val > 0; }).length;
        
        document.getElementById('totalProdi').textContent = totalJurusan;
        document.getElementById('maxUsulan').textContent = max;
        document.getElementById('avgUsulan').textContent = avg;
        document.getElementById('totalUsulanProdi').textContent = total;
    }

    // ===============================================
    // COLOR PALETTE
    // ===============================================
    var colorPalette = [
        'rgba(59, 130, 246, 0.85)',   // Blue
        'rgba(16, 185, 129, 0.85)',   // Emerald
        'rgba(245, 158, 11, 0.85)',   // Amber
        'rgba(239, 68, 68, 0.85)',    // Red
        'rgba(139, 92, 246, 0.85)',   // Violet
        'rgba(236, 72, 153, 0.85)',   // Pink
        'rgba(20, 184, 166, 0.85)',   // Teal
        'rgba(249, 115, 22, 0.85)'    // Orange
    ];

    var borderColorPalette = [
        'rgb(37, 99, 235)',
        'rgb(5, 150, 105)',
        'rgb(217, 119, 6)',
        'rgb(220, 38, 38)',
        'rgb(109, 40, 217)',
        'rgb(219, 39, 119)',
        'rgb(13, 148, 136)',
        'rgb(234, 88, 12)'
    ];

    // ===============================================
    // BAR CHART - USULAN PER JURUSAN
    // ===============================================
    var jurusanCtx = document.getElementById('prodiChart').getContext('2d');
    var currentPeriod = 'today';
    var initialJurusanData = getJurusanData(currentPeriod);
    
    var jurusanChart = new Chart(jurusanCtx, {
        type: 'bar',
        data: {
            labels: initialJurusanData.labels,
            datasets: [{
                label: 'Jumlah Usulan',
                data: initialJurusanData.data,
                backgroundColor: colorPalette,
                borderColor: borderColorPalette,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 'flex',
                maxBarThickness: 80
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 16,
                    cornerRadius: 12,
                    titleFont: {
                        size: 15,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                            var percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                            return 'Jumlah: ' + context.parsed.y + ' usulan (' + percentage + '%)';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(6, 182, 212, 0.08)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '500'
                        },
                        color: '#6b7280',
                        padding: 8,
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '600'
                        },
                        color: '#4b5563',
                        maxRotation: 45,
                        minRotation: 25,
                        padding: 8
                    }
                }
            },
            animation: {
                duration: 750,
                easing: 'easeInOutQuart'
            }
        }
    });

    updateSummaryStats(initialJurusanData.data);

    // ===============================================
    // FILTER BUTTONS
    // ===============================================
    var filterButtons = document.querySelectorAll('.filter-btn-prodi');
    filterButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterButtons.forEach(function(b) {
                b.classList.remove('active');
                b.classList.remove('bg-gradient-to-r', 'from-cyan-500', 'to-blue-600', 'text-white');
                b.classList.add('bg-white', 'border-2', 'border-gray-200', 'text-gray-700');
            });
            this.classList.add('active');
            this.classList.remove('bg-white', 'border-2', 'border-gray-200', 'text-gray-700');
            this.classList.add('bg-gradient-to-r', 'from-cyan-500', 'to-blue-600', 'text-white');
            
            currentPeriod = this.getAttribute('data-filter');
            var newData = getJurusanData(currentPeriod);
            
            jurusanChart.data.labels = newData.labels;
            jurusanChart.data.datasets[0].data = newData.data;
            jurusanChart.update('active');
            
            updateSummaryStats(newData.data);
        });
    });

    // ===============================================
    // DONUT CHART (tetap sama)
    // ===============================================
    var donutCtx = document.getElementById('donutChart').getContext('2d');
    var donutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Disetujui', 'Menunggu', 'Revisi', 'Ditolak'],
            datasets: [{
                data: [
                    <?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?>,
                    <?php echo htmlspecialchars($stats['menunggu'] ?? 0); ?>,
                    <?php echo htmlspecialchars($stats['revisi'] ?? 0); ?>,
                    <?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?>
                ],
                backgroundColor: [
                    '#22c55e',
                    '#3b82f6',
                    '#f97316',
                    '#ef4444'
                ],
                borderWidth: 4,
                borderColor: '#ffffff',
                hoverOffset: 12,
                hoverBorderWidth: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '72%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.85)',
                    padding: 16,
                    cornerRadius: 12,
                    titleFont: {
                        size: 15,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    displayColors: true,
                    boxWidth: 15,
                    boxHeight: 15,
                    boxPadding: 8,
                    callbacks: {
                        label: function(context) {
                            var total = 0;
                            for (var i = 0; i < context.dataset.data.length; i++) {
                                total += context.dataset.data[i];
                            }
                            var value = context.parsed;
                            var percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });

    // ===============================================
    // BAR CHART - DANA KELUAR PER JURUSAN (LOAD VIA AJAX)
    // ===============================================
    var danaPerJurusanCtx = document.getElementById('danaPerJurusanChart').getContext('2d');
    var danaPerJurusanChart = null;

    // Load data dana via AJAX dengan error handling
    fetch('/docutrack/public/direktur/dashboard/api/dana-per-jurusan')
        .then(response => {
            console.log('Dana API Response Status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(result => {
            console.log('Dana API Result:', result);
            
            if (result.success && result.data) {
                var danaData = result.data;
                
                // Cek apakah ada data
                if (danaData.labels.length === 0 || danaData.labels[0] === 'Belum ada data') {
                    console.warn('Tidak ada data dana yang tersedia');
                    // Tampilkan pesan di chart area
                    var chartContainer = danaPerJurusanCtx.canvas.parentElement;
                    chartContainer.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-gray-400 italic">Belum ada data pencairan dana</p></div>';
                    return;
                }
                
                var danaColorPalette = [
                    'rgba(16, 185, 129, 0.90)',
                    'rgba(52, 211, 153, 0.85)',
                    'rgba(110, 231, 183, 0.85)',
                    'rgba(34, 197, 94, 0.85)',
                    'rgba(74, 222, 128, 0.85)',
                    'rgba(134, 239, 172, 0.85)',
                    'rgba(21, 128, 61, 0.85)',
                    'rgba(22, 163, 74, 0.85)'
                ];

                var danaBorderColorPalette = [
                    'rgb(5, 150, 105)',
                    'rgb(16, 185, 129)',
                    'rgb(52, 211, 153)',
                    'rgb(22, 163, 74)',
                    'rgb(34, 197, 94)',
                    'rgb(74, 222, 128)',
                    'rgb(20, 83, 45)',
                    'rgb(21, 128, 61)'
                ];

                danaPerJurusanChart = new Chart(danaPerJurusanCtx, {
                    type: 'bar',
                    data: {
                        labels: danaData.labels,
                        datasets: [{
                            label: 'Total Dana Keluar',
                            data: danaData.data,
                            backgroundColor: danaColorPalette,
                            borderColor: danaBorderColorPalette,
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false,
                            barThickness: 'flex',
                            maxBarThickness: 80
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.85)',
                                padding: 16,
                                cornerRadius: 12,
                                titleFont: {
                                    size: 15,
                                    weight: 'bold'
                                },
                                bodyFont: {
                                    size: 14
                                },
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        var label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        var formattedValue = new Intl.NumberFormat('id-ID', { 
                                            style: 'currency', 
                                            currency: 'IDR',
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }).format(context.parsed.y);
                                        
                                        var total = context.dataset.data.reduce(function(a, b) { return a + b; }, 0);
                                        var percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                        
                                        return label + formattedValue + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(16, 185, 129, 0.08)',
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '500'
                                    },
                                    color: '#6b7280',
                                    padding: 8,
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000) + ' Jt';
                                        }
                                        return new Intl.NumberFormat('id-ID', { 
                                            style: 'currency', 
                                            currency: 'IDR',
                                            minimumFractionDigits: 0,
                                            maximumFractionDigits: 0
                                        }).format(value);
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#4b5563',
                                    maxRotation: 45,
                                    minRotation: 25,
                                    padding: 8
                                }
                            }
                        },
                        animation: {
                            duration: 750,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error loading dana data:', error);
            var chartContainer = danaPerJurusanCtx.canvas.parentElement;
            chartContainer.innerHTML = '<div class="flex items-center justify-center h-full"><p class="text-red-400">Error: ' + error.message + '</p></div>';
        });

    // ===============================================
    // TABLE & PAGINATION (LOAD VIA AJAX)
    // ===============================================
    document.addEventListener('DOMContentLoaded', function() {
        const tableBody = document.getElementById('pengajuanTableBody');
        const searchInput = document.getElementById('searchPengajuan');
        const jurusanFilter = document.getElementById('filterJurusan');
        const paginationContainer = document.getElementById('paginationContainer');

        let currentPage = 1;
        let currentSearch = '';
        let currentJurusan = '';

        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR', 
                minimumFractionDigits: 0 
            }).format(amount);
        }

        function getJurusanColor(jurusan) {
            const colors = {
                'Teknik Informatika dan Komputer': 'bg-blue-100 text-blue-800 border-blue-200',
                'Akuntansi': 'bg-green-100 text-green-800 border-green-200',
                'Administrasi Niaga': 'bg-purple-100 text-purple-800 border-purple-200',
                'Teknik Elektro': 'bg-yellow-100 text-yellow-800 border-yellow-200',
                'Teknik Mesin': 'bg-red-100 text-red-800 border-red-200',
                'Teknik Sipil': 'bg-indigo-100 text-indigo-800 border-indigo-200',
                'Teknik Grafika dan Penerbitan': 'bg-pink-100 text-pink-800 border-pink-200',
                'Pascasarjana': 'bg-orange-100 text-orange-800 border-orange-200'
            };
            return colors[jurusan] || 'bg-gray-100 text-gray-800 border-gray-200';
        }

        function loadPengajuan() {
            const url = `/docutrack/public/direktur/dashboard/api/pengajuan?page=${currentPage}&search=${encodeURIComponent(currentSearch)}&jurusan=${encodeURIComponent(currentJurusan)}`;
            
            console.log('Loading pengajuan from:', url);
            
            // Show loading
            tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-10"><i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i></td></tr>';
            
            fetch(url)
                .then(response => {
                    console.log('Pengajuan API Response Status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(result => {
                    console.log('Pengajuan API Result:', result);
                    
                    if (result.success && result.data) {
                        renderTable(result.data);
                    } else {
                        throw new Error(result.error || 'Data tidak valid');
                    }
                })
                .catch(error => {
                    console.error('Error loading pengajuan:', error);
                    tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-10 text-red-500">Gagal memuat data: ${error.message}</td></tr>`;
                });
        }

        function renderTable(data) {
            tableBody.innerHTML = '';
            
            if (data.items.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-10 text-gray-400 italic">Tidak ada data</td></tr>';
                paginationContainer.innerHTML = '';
                document.getElementById('showingStart').textContent = '0';
                document.getElementById('showingEnd').textContent = '0';
                document.getElementById('showingTotal').textContent = '0';
                return;
            }

            data.items.forEach((item, index) => {
                const row = `
                    <tr class="bg-white border-b hover:bg-purple-50 transition-colors duration-200" style="animation-delay: ${index * 0.05}s">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-800 mb-1">${item.namaKegiatan}</div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-user mr-1"></i>${item.namaPJ || '-'} • 
                                <i class="fas fa-id-card mr-1"></i>${item.nimPelaksana || '-'} • 
                                <i class="fas fa-graduation-cap mr-1"></i>${item.prodiPenyelenggara || '-'}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold ${getJurusanColor(item.jurusanPenyelenggara)} border">
                                ${item.jurusanPenyelenggara}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-emerald-600 text-lg">
                                <i class="fas fa-money-bill-wave mr-1"></i>${formatCurrency(item.estimasi_dana)}
                            </div>
                        </td>
                    </tr>
                `;
                tableBody.innerHTML += row;
            });

            renderPagination(data);
        }

        function renderPagination(data) {
            paginationContainer.innerHTML = '';
            
            const totalPages = data.total_pages;
            const prevDisabled = currentPage === 1;
            const nextDisabled = currentPage === totalPages || totalPages === 0;

            // Previous button
            paginationContainer.innerHTML += `
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 ${prevDisabled ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-50'}" ${prevDisabled ? 'disabled' : ''} data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                const isActive = currentPage === i;
                paginationContainer.innerHTML += `
                    <button class="h-8 w-8 flex items-center justify-center rounded-lg ${isActive ? 'bg-indigo-600 text-white font-medium shadow-sm shadow-indigo-200' : 'border border-slate-200 text-slate-600 hover:bg-slate-50'}" data-page="${i}">
                        ${i}
                    </button>
                `;
            }

            // Next button
            paginationContainer.innerHTML += `
                <button class="h-8 w-8 flex items-center justify-center rounded-lg border border-slate-200 ${nextDisabled ? 'text-slate-400 cursor-not-allowed' : 'text-slate-600 hover:bg-slate-50'}" ${nextDisabled ? 'disabled' : ''} data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right text-[10px]"></i>
                </button>
            `;

            // Update showing info
            const startInfo = data.total > 0 ? ((currentPage - 1) * data.per_page) + 1 : 0;
            const endInfo = Math.min(currentPage * data.per_page, data.total);
            document.getElementById('showingStart').textContent = startInfo;
            document.getElementById('showingEnd').textContent = endInfo;
            document.getElementById('showingTotal').textContent = data.total;
        }

        // Event listeners
        searchInput.addEventListener('input', function() {
            currentSearch = this.value;
            currentPage = 1;
            loadPengajuan();
        });

        jurusanFilter.addEventListener('change', function() {
            currentJurusan = this.value;
            currentPage = 1;
            loadPengajuan();
        });

        paginationContainer.addEventListener('click', function(e) {
            const button = e.target.closest('button');
            if (button && !button.disabled) {
                const page = parseInt(button.dataset.page);
                if (page > 0 && !isNaN(page)) {
                    currentPage = page;
                    loadPengajuan();
                }
            }
        });

        // Initial load
        loadPengajuan();
    });
</script>