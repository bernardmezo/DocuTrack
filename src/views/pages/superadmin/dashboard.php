<?php
// Command Center Dashboard
// Displays System Health, AI Insights, and Real-time Monitoring
?>

<main class="main-content font-poppins p-4 md:p-6 lg:p-8 -mt-8 md:-mt-20 max-w-[1400px] mx-auto w-full">

    <!-- Page Header -->
    <div class="mb-8 p-6 rounded-2xl bg-white/70 backdrop-blur-xl border border-white shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 p-4 opacity-[0.03] pointer-events-none">
            <i class="fas fa-microchip text-9xl"></i>
        </div>
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-blue-600 animate-pulse"></span>
                    <h1 class="text-2xl md:text-3xl font-black bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                        Command Center
                    </h1>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-indigo-50/50 rounded-full border border-indigo-100/50">
                    <i class="fas fa-sparkles text-indigo-500 text-[10px]"></i>
                    <p class="text-[10px] font-bold text-indigo-600/80 tracking-widest uppercase">AI-Powered Monitoring System</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.location.reload()" class="group flex items-center gap-2 px-5 py-2.5 bg-white text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-800 hover:text-white transition-all duration-300 shadow-sm border border-slate-100">
                    <i class="fas fa-sync-alt text-xs group-hover:rotate-180 transition-transform duration-700"></i>
                    <span>System Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Section 1: Key Metrics Grid -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
        
        <!-- Total Usulan Card -->
        <div class="bg-white rounded-xl p-5 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group relative overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-2">Total Usulan</h3>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl md:text-4xl font-bold text-gray-900"><?php echo htmlspecialchars($stats['total'] ?? 0); ?></span>
                            <?php if(!empty($stats['menunggu'])): ?>
                                <span class="text-xs font-semibold bg-blue-100 text-blue-700 px-2 py-1 rounded-full">
                                    +<?php echo htmlspecialchars($stats['menunggu']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl text-white shadow-lg shadow-blue-200 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-file-signature text-xl"></i>
                    </div>
                </div>
                
                <!-- Mini Stats -->
                <div class="grid grid-cols-3 gap-3 pt-4 border-t border-gray-100">
                    <div class="text-center">
                        <div class="text-lg md:text-xl font-bold text-emerald-600"><?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?></div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wide font-medium mt-0.5">Disetujui</div>
                    </div>
                    <div class="text-center border-x border-gray-100">
                        <div class="text-lg md:text-xl font-bold text-amber-600"><?php echo htmlspecialchars($stats['revisi'] ?? 0); ?></div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wide font-medium mt-0.5">Revisi</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg md:text-xl font-bold text-red-600"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></div>
                        <div class="text-[10px] text-gray-500 uppercase tracking-wide font-medium mt-0.5">Ditolak</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Card -->
        <div class="bg-white rounded-xl p-5 md:p-6 shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300 group relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-5">
                    <div class="flex-1">
                        <h3 class="text-xs font-semibold uppercase tracking-wider text-gray-500 mb-3">System Health</h3>
                        <div class="flex items-center gap-2">
                            <div class="relative flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo ($system_health['db_connection'] ? 'bg-emerald-400' : 'bg-red-400'); ?> opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 <?php echo ($system_health['db_connection'] ? 'bg-emerald-500' : 'bg-red-500'); ?>"></span>
                            </div>
                            <span class="text-sm font-semibold text-gray-800"><?php echo ($system_health['db_connection'] ? 'Online' : 'Offline'); ?></span>
                        </div>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-server text-xl"></i>
                    </div>
                </div>

                <!-- Memory Usage -->
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-gray-600 font-medium">Memory Usage</span>
                            <span class="font-semibold text-gray-800"><?php echo $system_health['memory_usage'] ?? '0 MB'; ?></span>
                        </div>
                        <?php
                            $memString = $system_health['memory_usage'] ?? '0';
                            $memVal = floatval($memString);
                            $percent = ($memVal > 0) ? min(($memVal / 256) * 100, 100) : 5; 
                            if ($percent < 5) $percent = 5;
                        ?>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-2 rounded-full transition-all duration-700 shadow-sm" style="width: <?php echo $percent; ?>%"></div>
                        </div>
                    </div>

                    <!-- PHP Version -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-600 font-medium">PHP Version</span>
                        <span class="text-xs font-mono font-semibold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-md"><?php echo $system_health['php_version'] ?? '-'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Analysis Card - Full Width on Mobile, 2 cols on Desktop -->
        <div class="sm:col-span-2 bg-gradient-to-br from-indigo-50 via-purple-50 to-pink-50 rounded-xl p-5 md:p-6 shadow-sm border border-indigo-100 hover:shadow-lg transition-all duration-300 group relative overflow-hidden">
            <!-- Decorative Elements -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full opacity-30 blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-pink-100 to-indigo-100 rounded-full opacity-30 blur-2xl group-hover:scale-150 transition-transform duration-700"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <h3 class="text-xs font-semibold uppercase tracking-wider text-indigo-700">AI System Analysis</h3>
                            <span id="ai-model-badge" class="px-2 py-1 rounded-full text-[10px] font-bold bg-white/80 text-indigo-700 border border-indigo-200 tracking-wide backdrop-blur-sm">
                                <?= \App\Config\AppConfig::AI_MODEL_NAME ?>
                            </span>
                        </div>
                        <p class="text-xs text-indigo-600/70">Automated security & performance insights</p>
                    </div>
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl text-white shadow-lg shadow-indigo-300 group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
                        <i class="fas fa-robot text-xl"></i>
                    </div>
                </div>
                
                <!-- AI Content Area -->
                <div class="bg-white/60 backdrop-blur-sm rounded-xl p-4 border border-white/50 shadow-inner min-h-[120px] overflow-y-auto custom-scrollbar" id="ai-summary-wrapper">
                    <div id="ai-summary-container" class="h-full flex flex-col justify-center">
                        <!-- Loading State -->
                        <div class="animate-pulse space-y-3">
                            <div class="h-2 bg-indigo-200/50 rounded-full w-full"></div>
                            <div class="h-2 bg-indigo-200/50 rounded-full w-11/12"></div>
                            <div class="h-2 bg-indigo-200/50 rounded-full w-4/5"></div>
                        </div>
                        <p class="text-[10px] text-indigo-400 animate-pulse font-mono mt-3 text-center">Initializing AI Neural Engine...</p>
                    </div>
                </div>
                
                <!-- Action Button -->
                <div class="mt-4 flex justify-end">
                    <a href="/docutrack/public/superadmin/ai-monitoring" class="inline-flex items-center gap-2 text-xs font-semibold text-indigo-700 hover:text-indigo-900 bg-white/80 hover:bg-white px-4 py-2 rounded-lg transition-all shadow-sm hover:shadow backdrop-blur-sm border border-indigo-100">
                        <span>Full Analysis</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Data Tables -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-6">
        
        <!-- Usulan Terbaru Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col hover:shadow-lg transition-all duration-300">
            <!-- Header -->
            <div class="px-5 md:px-6 py-4 md:py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50/50 to-transparent">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg shadow-sm">
                            <i class="fas fa-file-signature text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base md:text-lg">Usulan Terbaru</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pengajuan kegiatan masuk</p>
                        </div>
                    </div>
                    <a href="/docutrack/public/superadmin/monitoring" class="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-all shadow-sm hover:shadow">
                        View All
                    </a>
                </div>
            </div>
            
            <!-- Table Content -->
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/80 border-b border-gray-100 sticky top-0">
                        <tr>
                            <th class="px-4 md:px-6 py-3 font-semibold text-left">Pengusul</th>
                            <th class="px-4 md:px-6 py-3 font-semibold text-left">Kegiatan</th>
                            <th class="px-4 md:px-6 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($monitoring_kegiatan)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 md:py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-inbox text-3xl md:text-4xl text-gray-300"></i>
                                        </div>
                                        <h4 class="text-gray-900 font-semibold text-sm mb-1">Belum ada pengajuan</h4>
                                        <p class="text-gray-500 text-xs max-w-[220px]">Data usulan kegiatan akan muncul di sini secara otomatis</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($monitoring_kegiatan as $item): ?>
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <td class="px-4 md:px-6 py-4">
                                    <div class="font-semibold text-gray-900 truncate max-w-[150px]" title="<?php echo htmlspecialchars($item['pengusul']); ?>">
                                        <?php echo htmlspecialchars($item['pengusul']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5"><?php echo htmlspecialchars($item['prodi'] ?? '-'); ?></div>
                                </td>
                                <td class="px-4 md:px-6 py-4">
                                    <div class="text-gray-700 font-medium truncate max-w-[180px]" title="<?php echo htmlspecialchars($item['nama']); ?>">
                                        <?php echo htmlspecialchars($item['nama']); ?>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                        <i class="far fa-clock text-[10px]"></i> 
                                        <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-center">
                                    <?php
                                        $statusConfig = [
                                            'Disetujui' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200'],
                                            'Ditolak' => ['bg-red-50', 'text-red-700', 'border-red-200'],
                                            'Revisi' => ['bg-amber-50', 'text-amber-700', 'border-amber-200'],
                                            'Menunggu' => ['bg-blue-50', 'text-blue-700', 'border-blue-200']
                                        ];
                                        $s = $item['status'] ?? 'Menunggu';
                                        $style = $statusConfig[$s] ?? ['bg-gray-50', 'text-gray-700', 'border-gray-200'];
                                    ?>
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold border <?php echo implode(' ', $style); ?>">
                                        <?php echo htmlspecialchars($s); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- LPJ Masuk Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col hover:shadow-lg transition-all duration-300">
            <!-- Header -->
            <div class="px-5 md:px-6 py-4 md:py-5 border-b border-gray-100 bg-gradient-to-r from-emerald-50/50 to-transparent">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 text-emerald-600 rounded-lg shadow-sm">
                            <i class="fas fa-file-invoice-dollar text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-base md:text-lg">LPJ Masuk</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Laporan pertanggungjawaban</p>
                        </div>
                    </div>
                    <a href="/docutrack/public/superadmin/monitoring" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-2 rounded-lg transition-all shadow-sm hover:shadow">
                        View All
                    </a>
                </div>
            </div>
            
            <!-- Table Content -->
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/80 border-b border-gray-100 sticky top-0">
                        <tr>
                            <th class="px-4 md:px-6 py-3 font-semibold text-left">Kegiatan</th>
                            <th class="px-4 md:px-6 py-3 font-semibold text-left">Realisasi</th>
                            <th class="px-4 md:px-6 py-3 font-semibold text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($monitoring_lpj)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-12 md:py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 md:w-20 md:h-20 bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                                            <i class="fas fa-clipboard-list text-3xl md:text-4xl text-gray-300"></i>
                                        </div>
                                        <h4 class="text-gray-900 font-semibold text-sm mb-1">Belum ada LPJ masuk</h4>
                                        <p class="text-gray-500 text-xs max-w-[220px]">Laporan pertanggungjawaban akan ditampilkan di sini</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($monitoring_lpj as $item): ?>
                            <tr class="hover:bg-emerald-50/30 transition-colors group">
                                <td class="px-4 md:px-6 py-4">
                                    <div class="font-semibold text-gray-900 truncate max-w-[180px]" title="<?php echo htmlspecialchars($item['nama_kegiatan']); ?>">
                                        <?php echo htmlspecialchars($item['nama_kegiatan']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <i class="fas fa-user-circle text-[10px]"></i>
                                        <?php echo htmlspecialchars($item['pengusul']); ?>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-4">
                                    <div class="font-mono text-sm font-semibold text-gray-900">
                                        Rp <?php echo number_format($item['total_realisasi'] ?? 0, 0, ',', '.'); ?>
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-4 text-center">
                                    <?php
                                        $lpjConfig = [
                                            'Disetujui' => ['bg-emerald-50', 'text-emerald-700', 'border-emerald-200'],
                                            'Revisi' => ['bg-amber-50', 'text-amber-700', 'border-amber-200'],
                                            'Menunggu Verifikasi' => ['bg-blue-50', 'text-blue-700', 'border-blue-200']
                                        ];
                                        $s = $item['status_lpj'] ?? 'Menunggu Verifikasi';
                                        $style = $lpjConfig[$s] ?? ['bg-gray-50', 'text-gray-700', 'border-gray-200'];
                                    ?>
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-semibold border <?php echo implode(' ', $style); ?>">
                                        <?php echo htmlspecialchars($s); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </section>

</main>

<!-- Custom Scrollbar Styles -->
<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
    height: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #a5b4fc;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('ai-summary-container');
    const modelBadge = document.getElementById('ai-model-badge');
    
    // Wait 500ms before fetching to show skeleton (UX improvement)
    setTimeout(() => {
        fetch('/docutrack/public/superadmin/get-ai-analysis')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update Model Badge if provided
                    if (data.model && modelBadge) {
                        modelBadge.textContent = data.model;
                    }

                    // Format text with line breaks
                    let formattedText = data.data.replace(/\n/g, '<br>');
                    
                    container.innerHTML = `
                        <div class="text-sm text-gray-800 leading-relaxed animate-fade-in">
                            ${formattedText}
                        </div>
                    `;
                    container.classList.remove('h-full', 'justify-center');
                } else {
                    container.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full">
                            <div class="flex items-center gap-2 text-red-500 mb-2">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="text-xs font-semibold">Analysis Failed</span>
                            </div>
                            <p class="text-[10px] text-gray-500">${data.message}</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('AI Analysis Error:', error);
                container.innerHTML = `
                    <div class="flex flex-col items-center justify-center h-full">
                        <div class="flex items-center gap-2 text-red-400 mb-2">
                            <i class="fas fa-wifi"></i>
                            <span class="text-xs font-semibold">Connection Error</span>
                        </div>
                        <p class="text-[10px] text-gray-500">Unable to fetch AI analysis</p>
                    </div>
                `;
            });
    }, 500);
});
</script>