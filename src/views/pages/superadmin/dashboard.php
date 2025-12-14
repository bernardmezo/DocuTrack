<?php
// Command Center Dashboard
// Displays System Health, AI Insights, and Real-time Monitoring
?>

<main class="main-content font-poppins p-4 md:p-6 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Section 1: Command Center Header (Stats & System Health) -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Stats Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group relative overflow-hidden">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Usulan</h3>
                    <div class="flex items-baseline gap-2 mt-1">
                        <span class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($stats['total'] ?? 0); ?></span>
                        <?php if(!empty($stats['menunggu'])): ?>
                            <span class="text-xs font-medium bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full border border-blue-100">
                                +<?php echo htmlspecialchars($stats['menunggu']); ?> new
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <i class="fas fa-file-signature text-xl"></i>
                </div>
            </div>
            <!-- Mini Stats Grid -->
            <div class="grid grid-cols-3 gap-2 mt-4 pt-4 border-t border-gray-50">
                <div class="text-center">
                    <span class="block text-lg font-bold text-gray-700"><?php echo htmlspecialchars($stats['disetujui'] ?? 0); ?></span>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wide">ACC</span>
                </div>
                <div class="text-center border-l border-gray-100">
                    <span class="block text-lg font-bold text-gray-700"><?php echo htmlspecialchars($stats['revisi'] ?? 0); ?></span>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wide">Rev</span>
                </div>
                <div class="text-center border-l border-gray-100">
                    <span class="block text-lg font-bold text-gray-700"><?php echo htmlspecialchars($stats['ditolak'] ?? 0); ?></span>
                    <span class="text-[10px] text-gray-400 uppercase tracking-wide">Tolak</span>
                </div>
            </div>
        </div>

        <!-- Infrastructure Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative group">
             <div class="flex justify-between items-start mb-6">
                <div>
                    <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">System Health</h3>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full <?php echo ($system_health['db_connection'] ? 'bg-emerald-400' : 'bg-red-400'); ?> opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 <?php echo ($system_health['db_connection'] ? 'bg-emerald-500' : 'bg-red-500'); ?>"></span>
                        </div>
                        <span class="text-sm font-medium text-gray-700"><?php echo ($system_health['db_connection'] ? 'Database Online' : 'DB Connection Error'); ?></span>
                    </div>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <i class="fas fa-server text-xl"></i>
                </div>
            </div>

            <!-- Memory Progress -->
            <div class="mb-4">
                 <div class="flex justify-between text-xs mb-1">
                    <span class="text-gray-500">Memory Usage</span>
                    <span class="font-medium text-gray-700"><?php echo $system_health['memory_usage'] ?? '0 MB'; ?></span>
                </div>
                <?php
                    // Parse memory usage for visual bar (simple heuristic)
                    $memString = $system_health['memory_usage'] ?? '0';
                    $memVal = floatval($memString);
                    // Assume 256MB as a visual baseline for "100%" to show meaningful progress
                    $percent = ($memVal > 0) ? min(($memVal / 256) * 100, 100) : 5; 
                    if ($percent < 5) $percent = 5;
                ?>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]" style="width: <?php echo $percent; ?>%"></div>
                </div>
            </div>

             <!-- PHP Version -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                <span class="text-xs text-gray-500">PHP Version</span>
                <span class="text-xs font-mono font-medium text-gray-600 bg-gray-100 px-2 py-0.5 rounded"><?php echo $system_health['php_version'] ?? '-'; ?></span>
            </div>
        </div>

        <!-- AI Analysis Card -->
        <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative group overflow-hidden">
             <!-- Decorative -->
             <div class="absolute top-0 right-0 p-6 opacity-5 group-hover:opacity-10 transition-opacity">
                <i class="fas fa-brain text-8xl text-indigo-900 transform rotate-12 translate-x-4 -translate-y-4"></i>
             </div>

            <div class="flex justify-between items-start mb-4 relative z-10">
                <div>
                     <div class="flex items-center gap-2 mb-1">
                        <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">AI System Analysis</h3>
                        <span id="ai-model-badge" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-100 tracking-wide">
                            <?= \App\Config\AppConfig::AI_MODEL_NAME ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-400">Automated security & performance insights</p>
                </div>
                <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors shadow-sm">
                    <i class="fas fa-robot text-xl"></i>
                </div>
            </div>
            
            <div class="bg-indigo-50/30 rounded-lg p-4 border border-indigo-50 h-24 overflow-y-auto custom-scrollbar relative z-10" id="ai-summary-wrapper">
                <div id="ai-summary-container" class="h-full flex flex-col justify-center">
                     <div class="animate-pulse flex flex-col gap-2">
                        <div class="h-2 bg-indigo-200/50 rounded w-full"></div>
                        <div class="h-2 bg-indigo-200/50 rounded w-5/6"></div>
                        <div class="h-2 bg-indigo-200/50 rounded w-4/6"></div>
                    </div>
                    <p class="text-[10px] text-indigo-400 animate-pulse font-mono mt-2 text-center">Initializing AI Neural Engine...</p>
                </div>
            </div>
            <div class="mt-3 text-right relative z-10">
                <a href="/docutrack/public/superadmin/ai-monitoring" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 transition-colors flex items-center justify-end gap-1">
                    Full Analysis <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Section 2: Real-time Operations -->
    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Left: Usulan Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full hover:shadow-md transition-shadow">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-xl">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">Usulan Terbaru</h3>
                </div>
                <a href="/docutrack/public/superadmin/monitoring" class="text-xs font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">
                    View All
                </a>
            </div>
            
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 font-medium">Pengusul</th>
                            <th class="px-6 py-3 font-medium">Kegiatan</th>
                            <th class="px-6 py-3 font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($monitoring_kegiatan)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <!-- Empty State SVG - Folder/Document -->
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-gray-900 font-semibold text-sm mb-1">Belum ada pengajuan baru</h4>
                                        <p class="text-gray-500 text-xs max-w-[200px]">Data usulan kegiatan dari unit kerja akan muncul di sini secara otomatis.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($monitoring_kegiatan as $item): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($item['pengusul']); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($item['prodi'] ?? '-'); ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 font-medium truncate max-w-[200px]" title="<?php echo htmlspecialchars($item['nama']); ?>">
                                        <?php echo htmlspecialchars($item['nama']); ?>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5 flex items-center gap-1">
                                        <i class="far fa-clock text-[10px]"></i> <?php echo date('d M Y', strtotime($item['created_at'])); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $statusConfig = [
                                            'Disetujui' => ['bg-green-100', 'text-green-700'],
                                            'Ditolak' => ['bg-red-100', 'text-red-700'],
                                            'Revisi' => ['bg-yellow-100', 'text-yellow-800'],
                                            'Menunggu' => ['bg-blue-100', 'text-blue-700']
                                        ];
                                        $s = $item['status'] ?? 'Menunggu';
                                        $style = $statusConfig[$s] ?? ['bg-gray-100', 'text-gray-800'];
                                    ?>
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $style[0] . ' ' . $style[1]; ?>">
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

        <!-- Right: LPJ Masuk -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col h-full hover:shadow-md transition-shadow">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white rounded-t-xl">
                 <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">LPJ Masuk</h3>
                </div>
                <a href="/docutrack/public/superadmin/monitoring" class="text-xs font-medium text-emerald-600 hover:text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-lg transition-colors">
                    View All
                </a>
            </div>
            <div class="flex-1 overflow-x-auto p-0">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-400 uppercase bg-gray-50/50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 font-medium">Kegiatan</th>
                            <th class="px-6 py-3 font-medium">Realisasi</th>
                            <th class="px-6 py-3 font-medium text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($monitoring_lpj)): ?>
                            <tr>
                                <td colspan="3" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <!-- Empty State SVG - Clipboard/List -->
                                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                        </div>
                                        <h4 class="text-gray-900 font-semibold text-sm mb-1">Belum ada LPJ masuk</h4>
                                        <p class="text-gray-500 text-xs max-w-[200px]">Laporan pertanggungjawaban yang masuk akan ditampilkan di sini.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($monitoring_lpj as $item): ?>
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 truncate max-w-[180px]" title="<?php echo htmlspecialchars($item['nama_kegiatan']); ?>">
                                        <?php echo htmlspecialchars($item['nama_kegiatan']); ?>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        <i class="fas fa-user-circle text-[10px] mr-1 text-gray-400"></i><?php echo htmlspecialchars($item['pengusul']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs font-medium text-gray-600">
                                    Rp <?php echo number_format($item['total_realisasi'] ?? 0, 0, ',', '.'); ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                        $lpjConfig = [
                                            'Disetujui' => ['bg-emerald-100', 'text-emerald-700'],
                                            'Revisi' => ['bg-yellow-100', 'text-yellow-800'],
                                            'Menunggu Verifikasi' => ['bg-blue-100', 'text-blue-700']
                                        ];
                                        $s = $item['status_lpj'] ?? 'Menunggu Verifikasi';
                                        $style = $lpjConfig[$s] ?? ['bg-gray-100', 'text-gray-800'];
                                    ?>
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $style[0] . ' ' . $style[1]; ?>">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('ai-summary-container');
        const modelBadge = document.getElementById('ai-model-badge');
        
        // Wait 1 second before fetching to allow UI to settle and show skeleton (UX)
        setTimeout(() => {
            fetch('/docutrack/public/superadmin/get-ai-analysis')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Update Model Badge if provided
                        if (data.model && modelBadge) {
                            modelBadge.textContent = data.model;
                        }

                        // Convert newlines to <br> for proper formatting
                        // Also handle bullet points if AI returns them
                        let formattedText = data.data.replace(/\n/g, '<br>');
                        
                        container.innerHTML = `
                            <p class="text-sm text-gray-700 leading-relaxed font-mono animate-fade-in">
                                ${formattedText}
                            </p>
                        `;
                        container.classList.remove('h-full', 'justify-center'); // Remove centering if content is long
                    } else {
                        container.innerHTML = `
                            <div class="flex items-center gap-2 text-red-500">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span class="text-xs font-medium">Analysis Failed</span>
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">${data.message}</p>
                        `;
                    }
                })
                .catch(error => {
                    console.error('AI Analysis Error:', error);
                     container.innerHTML = `
                        <div class="flex items-center gap-2 text-red-400">
                            <i class="fas fa-wifi"></i>
                            <span class="text-xs font-medium">Connection Error</span>
                        </div>
                    `;
                });
        }, 500);
    });
</script>
