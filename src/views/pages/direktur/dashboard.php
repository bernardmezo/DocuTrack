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
                        <i class="fas fa-chart-bar mr-2"></i>Usulan Per Program Studi
                    </h2>
                    <p class="text-sm text-gray-500">Distribusi pengajuan berdasarkan program studi</p>
                </div>
                <div class="flex gap-2 mt-4 sm:mt-0">
                    <button class="filter-btn-prodi active px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-600 text-white text-sm font-medium hover:from-cyan-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 whitespace-nowrap" data-filter="today">
                        <i class="fas fa-calendar-day mr-1"></i> Hari Ini
                    </button>
                    <button class="filter-btn-prodi px-4 py-2 rounded-lg bg-white border-2 border-gray-200 text-gray-700 text-sm font-medium hover:border-cyan-400 hover:text-cyan-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap" data-filter="week">
                        <i class="fas fa-calendar-week mr-1"></i> Minggu
                    </button>
                    <button class="filter-btn-prodi px-4 py-2 rounded-lg bg-white border-2 border-gray-200 text-gray-700 text-sm font-medium hover:border-cyan-400 hover:text-cyan-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap" data-filter="month">
                        <i class="fas fa-calendar-alt mr-1"></i> Bulan
                    </button>
                    <button class="filter-btn-prodi px-4 py-2 rounded-lg bg-white border-2 border-gray-200 text-gray-700 text-sm font-medium hover:border-cyan-400 hover:text-cyan-600 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105 whitespace-nowrap" data-filter="year">
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

</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<style>
    .filter-btn-prodi.active {
        background: linear-gradient(135deg, #06b6d4 0%, #2563eb 100%) !important;
        color: white !important;
        border-color: transparent !important;
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.4) !important;
    }
</style>
<script>
    // ===============================================
    // DATA INITIALIZATION
    // ===============================================
    var phpDataKAK = <?= json_encode($list_kak ?? []) ?>;
    var phpDataLPJ = <?= json_encode($list_lpj ?? []) ?>;
    var listProdi = <?= json_encode($list_prodi ?? []) ?>;
    
    console.log('📊 Data Initialization:');
    console.log('- KAK:', phpDataKAK.length, 'items');
    console.log('- LPJ:', phpDataLPJ.length, 'items');
    console.log('- Prodi:', listProdi);

    // Generate data simulasi jika diperlukan
    function generateSimulationDataProdi(count) {
        var statuses = ['menunggu', 'disetujui', 'ditolak', 'revisi'];
        var statusWeights = [0.35, 0.40, 0.15, 0.10];
        var data = [];
        var now = new Date();
        
        for (var i = 0; i < count; i++) {
            var randomDaysAgo = Math.floor(Math.random() * 365);
            var randomHours = Math.floor(Math.random() * 24);
            var date = new Date(now);
            date.setDate(date.getDate() - randomDaysAgo);
            date.setHours(randomHours, 0, 0, 0);
            
            var random = Math.random();
            var cumulativeWeight = 0;
            var selectedStatus = statuses[0];
            
            for (var j = 0; j < statuses.length; j++) {
                cumulativeWeight += statusWeights[j];
                if (random <= cumulativeWeight) {
                    selectedStatus = statuses[j];
                    break;
                }
            }
            
            var randomProdi = listProdi[Math.floor(Math.random() * listProdi.length)];
            
            data.push({
                id: i + 1,
                created_at: date.toISOString(),
                tanggal: date.toISOString().split('T')[0],
                status: selectedStatus,
                jurusan: randomProdi,
                nama: 'Usulan ' + (i + 1)
            });
        }
        
        return data.sort(function(a, b) {
            return new Date(b.created_at) - new Date(a.created_at);
        });
    }

    var hasValidData = phpDataKAK.length > 0 && phpDataKAK[0].hasOwnProperty('created_at');
    
    if (!hasValidData || phpDataKAK.length < 10) {
        console.log('⚠️ Menggunakan data simulasi untuk grafik');
        window.dataKAK = generateSimulationDataProdi(80);
        window.dataLPJ = generateSimulationDataProdi(60);
    } else {
        console.log('✅ Menggunakan data asli');
        window.dataKAK = phpDataKAK;
        window.dataLPJ = phpDataLPJ;
    }

    // ===============================================
    // HELPER FUNCTIONS
    // ===============================================
    function filterDataByPeriod(data, period) {
        var now = new Date();
        var today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        
        return data.filter(function(item) {
            var itemDate = new Date(item.created_at || item.tanggal);
            
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

    function getProdiData(period) {
        var allData = window.dataKAK.concat(window.dataLPJ);
        var filteredData = filterDataByPeriod(allData, period);
        
        var prodiCounts = {};
        listProdi.forEach(function(prodi) {
            prodiCounts[prodi] = 0;
        });
        
        filteredData.forEach(function(item) {
            var prodi = item.jurusan;
            if (prodiCounts.hasOwnProperty(prodi)) {
                prodiCounts[prodi]++;
            }
        });
        
        var sortedProdi = Object.keys(prodiCounts).sort(function(a, b) {
            return prodiCounts[b] - prodiCounts[a];
        });
        
        var values = sortedProdi.map(function(prodi) {
            return prodiCounts[prodi];
        });
        
        return { labels: sortedProdi, data: values };
    }

    function updateSummaryStats(data) {
        var total = data.reduce(function(sum, val) { return sum + val; }, 0);
        var max = Math.max.apply(null, data);
        var avg = data.length > 0 ? (total / data.length).toFixed(1) : 0;
        
        document.getElementById('totalProdi').textContent = data.length;
        document.getElementById('maxUsulan').textContent = max;
        document.getElementById('avgUsulan').textContent = avg;
        document.getElementById('totalUsulanProdi').textContent = total;
    }

    // ===============================================
    // COLOR PALETTE
    // ===============================================
    var colorPalette = [
        'rgba(59, 130, 246, 0.85)',   // Blue
    ];

    var borderColorPalette = [
        'rgb(14, 165, 233)',
    ];

    // ===============================================
    // BAR CHART - USULAN PER PRODI
    // ===============================================
    var prodiCtx = document.getElementById('prodiChart').getContext('2d');
    var currentPeriod = 'today';
    var initialProdiData = getProdiData(currentPeriod);
    
    var prodiChart = new Chart(prodiCtx, {
        type: 'bar',
        data: {
            labels: initialProdiData.labels,
            datasets: [{
                label: 'Jumlah Usulan',
                data: initialProdiData.data,
                backgroundColor: colorPalette,
                borderColor: borderColorPalette,
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 'flex',
                maxBarThickness: 60
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
                            var percentage = ((context.parsed.y / total) * 100).toFixed(1);
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

    updateSummaryStats(initialProdiData.data);

    // ===============================================
    // FILTER BUTTONS
    // ===============================================
    var filterButtons = document.querySelectorAll('.filter-btn-prodi');
    filterButtons.forEach(function(btn) {
        btn.addEventListener('click', function() {
            filterButtons.forEach(function(b) {
                b.classList.remove('active');
                b.classList.remove('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white');
                b.classList.add('bg-white', 'border-2', 'border-gray-200', 'text-gray-700');
            });
            this.classList.add('active');
            
            currentPeriod = this.getAttribute('data-filter');
            var newData = getProdiData(currentPeriod);
            
            prodiChart.data.labels = newData.labels;
            prodiChart.data.datasets[0].data = newData.data;
            prodiChart.update('active');
            
            updateSummaryStats(newData.data);
        });
    });

    // ===============================================
    // DONUT CHART
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
</script>
