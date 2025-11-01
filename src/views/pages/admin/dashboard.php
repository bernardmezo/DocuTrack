<?php
// File: src/views/pages/admin/pengajuan_usulan.php
if (!isset($antrian_kak)) { $antrian_kak = [['id' => 1, 'nama' => 'Contoh Revisi', 'pengusul' => 'User (Dummy)', 'status' => 'Revisi']]; }
if (!isset($stats)) { $stats = ['total'=>0, 'disetujui'=>0, 'ditolak'=>0, 'menunggu'=>0]; }
if (!isset($tahapan_kak)) { $tahapan_kak = ['Pengajuan', 'Validasi', 'ACC WD', 'ACC PPK', 'Dana Cair', 'LPJ']; }
if (!isset($tahap_sekarang_kak)) { $tahap_sekarang_kak = 'Pengajuan'; }
if (!isset($icons_kak)) { $icons_kak = [ 'Pengajuan' => 'fa-file-alt', 'Validasi' => 'fa-check-double', 'ACC WD' => 'fa-user-check', 'ACC PPK' => 'fa-stamp', 'Dana Cair' => 'fa-wallet', 'LPJ' => 'fa-file-invoice-dollar' ]; }
if (!isset($tahapan_lpj)) { $tahapan_lpj = ['Pengajuan', 'Validasi', 'ACC WD', 'ACC PPK', 'Selesai']; }
if (!isset($tahap_sekarang_lpj)) { $tahap_sekarang_lpj = 'Pengajuan'; }
if (!isset($icons_lpj)) { $icons_lpj = [ 'Pengajuan' => 'fa-file-invoice', 'Validasi' => 'fa-check-double', 'ACC WD' => 'fa-user-graduate', 'ACC PPK' => 'fa-gavel', 'Selesai' => 'fa-flag-checkered' ]; }
if (!isset($list_kak)) { $list_kak = $antrian_kak; }
if (!isset($list_lpj)) { $list_lpj = []; }
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

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

    <section class="bg-white p-6 rounded-xl shadow-lg mb-8 overflow-hidden"> 
        <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-8 text-center">Progres Pengajuan KAK</h3> 
        <div class="px-4 sm:px-6"> <div class="relative w-full h-20 flex items-start"> 
            <?php
                $posisi_sekarang_kak = array_search($tahap_sekarang_kak, $tahapan_kak); 
                if ($posisi_sekarang_kak === false) $posisi_sekarang_kak = 0;
                $total_langkah_kak = count($tahapan_kak) - 1;
                $lebar_progress_line_kak = $posisi_sekarang_kak >= 0 && $total_langkah_kak > 0 ? ($posisi_sekarang_kak / $total_langkah_kak) * 100 : 0;
            ?>
            <div class="absolute top-[18px] left-0 w-full h-1 bg-gray-200 rounded-full z-0"></div> 
            <div class="absolute top-[18px] left-0 h-1 bg-gradient-to-r from-green-500 to-blue-500 rounded-full z-0 transition-all duration-500 ease-out" style="width: <?php echo $lebar_progress_line_kak; ?>%;"></div> 
            <?php foreach ($tahapan_kak as $index => $nama_tahap): 
                $is_completed_kak = $index < $posisi_sekarang_kak; $is_active_kak = $index == $posisi_sekarang_kak;
                $node_style_kak = 'bg-gray-300 border-gray-400'; $icon_color_kak = 'text-gray-500'; $text_color_kak = 'text-gray-500'; $animation_kak = '';
                if ($is_completed_kak) { $node_style_kak = 'bg-green-500 border-green-600 shadow-sm shadow-green-200'; $icon_color_kak = 'text-white'; $text_color_kak = 'text-green-600 font-medium'; } 
                elseif ($is_active_kak) { $node_style_kak = 'bg-blue-500 border-blue-600 ring-4 ring-blue-200 shadow-lg shadow-blue-300 scale-110'; $icon_color_kak = 'text-white'; $text_color_kak = 'text-blue-600 font-bold'; $animation_kak = 'animate-pulse-lg'; }
                $left_position_kak = $total_langkah_kak > 0 ? ($index / $total_langkah_kak) * 100 : 0;
            ?>
            <div class="relative z-10 flex flex-col items-center group cursor-pointer" style="position: absolute; top:0; left: <?php echo $left_position_kak; ?>%; transform: translateX(-50%);" title="<?php echo htmlspecialchars($nama_tahap); ?>">
                <div class="w-9 h-9 rounded-full border-4 flex items-center justify-center transition-all duration-300 <?php echo $node_style_kak; ?> <?php echo $animation_kak; ?>"> 
                    <i class="fas <?php echo $icons_kak[$nama_tahap] ?? 'fa-question-circle'; ?> fa-sm <?php echo $icon_color_kak; ?>"></i> 
                </div>
                <span class="mt-1 text-xs text-center w-20 <?php echo $text_color_kak; ?> hidden sm:block"><?php echo htmlspecialchars($nama_tahap); ?></span> 
            </div>
            <?php endforeach; ?>
        </div> </div> 
    </section>
    <section class="bg-white p-6 rounded-xl shadow-lg mb-8 overflow-hidden">
        <h3 class="text-lg md:text-xl font-semibold text-gray-800 mb-8 text-center">Progres Pengajuan LPJ</h3>
        <div class="px-4 sm:px-6"> <div class="relative w-full h-20 flex items-start"> 
            <?php
                $posisi_sekarang_lpj = array_search($tahap_sekarang_lpj, $tahapan_lpj);
                if ($posisi_sekarang_lpj === false) $posisi_sekarang_lpj = 0;
                $total_langkah_lpj = count($tahapan_lpj) - 1;
                $lebar_progress_line_lpj = $posisi_sekarang_lpj >= 0 && $total_langkah_lpj > 0 ? ($posisi_sekarang_lpj / $total_langkah_lpj) * 100 : 0;
            ?>
            <div class="absolute top-[18px] left-0 w-full h-1 bg-gray-200 rounded-full z-0"></div> 
            <div class="absolute top-[18px] left-0 h-1 bg-gradient-to-r from-green-500 to-blue-500 rounded-full z-0 transition-all duration-500 ease-out" style="width: <?php echo $lebar_progress_line_lpj; ?>%;"></div> 
            <?php foreach ($tahapan_lpj as $index => $nama_tahap):
                $is_completed_lpj = $index < $posisi_sekarang_lpj; $is_active_lpj = $index == $posisi_sekarang_lpj;
                $node_style_lpj = 'bg-gray-300 border-gray-400'; $icon_color_lpj = 'text-gray-500'; $text_color_lpj = 'text-gray-500'; $animation_lpj = '';
                if ($is_completed_lpj) { $node_style_lpj = 'bg-green-500 border-green-600 shadow-sm shadow-green-200'; $icon_color_lpj = 'text-white'; $text_color_lpj = 'text-green-600 font-medium'; } 
                elseif ($is_active_lpj) { $node_style_lpj = 'bg-blue-500 border-blue-600 ring-4 ring-blue-200 shadow-lg shadow-blue-300 scale-110'; $icon_color_lpj = 'text-white'; $text_color_lpj = 'text-blue-600 font-bold'; $animation_lpj = 'animate-pulse-lg'; }
                $left_position_lpj = $total_langkah_lpj > 0 ? ($index / $total_langkah_lpj) * 100 : 0;
            ?>
            <div class="relative z-10 flex flex-col items-center group cursor-pointer" style="position: absolute; top:0; left: <?php echo $left_position_lpj; ?>%; transform: translateX(-50%);" title="<?php echo htmlspecialchars($nama_tahap); ?>">
                <div class="w-9 h-9 rounded-full border-4 flex items-center justify-center transition-all duration-300 <?php echo $node_style_lpj; ?> <?php echo $animation_lpj; ?>"> 
                    <i class="fas <?php echo $icons_lpj[$nama_tahap] ?? 'fa-question-circle'; ?> fa-sm <?php echo $icon_color_lpj; ?>"></i> 
                </div>
                <span class="mt-1 text-xs text-center w-20 <?php echo $text_color_lpj; ?> hidden sm:block"><?php echo htmlspecialchars($nama_tahap); ?></span> 
            </div>
            <?php endforeach; ?>
        </div> </div> 
    </section>

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-xl font-semibold text-gray-800">List Pengajuan KAK (Semua Status)</h3>
        </div>
        <div class="overflow-y-auto overflow-x-auto max-h-96">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Nama Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($list_kak)): ?>
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada data pengajuan KAK.</td></tr>
                    <?php else: ?>
                        <?php foreach ($list_kak as $item): 
                            $status_class = match(strtolower($item['status'] ?? '')){
                                'disetujui' => 'text-green-600 bg-green-100', 
                                'ditolak' => 'text-red-600 bg-red-100', 
                                'revisi' => 'text-yellow-700 bg-yellow-100', 
                                default => 'text-gray-600 bg-gray-100',
                            }; 
                        ?>
                            <tr class='hover:bg-gray-50 transition-colors'>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-800 font-medium'><?php echo htmlspecialchars($item['nama']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-xs font-semibold'><span class='px-3 py-1 rounded-full <?php echo $status_class; ?>'><?php echo htmlspecialchars($item['status']); ?></span></td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm font-medium'>
                                    <div class='flex gap-2'>
                                        <a href="/docutrack/public/admin/pengajuan-kegiatan/show/<?php echo $item['id'] ?? ''; ?>?ref=dashboard" class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>Lihat</a>
                                        <button class='bg-red-100 text-red-700 px-3 py-1.5 rounded-md text-xs font-medium hover:bg-red-200 transition-colors'><i class='fas fa-trash'></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-xl font-semibold text-gray-800">List Pengajuan LPJ (Semua Status)</h3>
        </div>
        <div class="overflow-y-auto overflow-x-auto max-h-96">
            <table class="w-full min-w-[700px]">
                 <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Nama Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider sticky top-0 bg-gray-50 z-10">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                     <?php if (empty($list_lpj)): ?>
                        <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Belum ada data pengajuan LPJ.</td></tr>
                    <?php else: ?>
                        <?php foreach ($list_lpj as $item): 
                            $status_class = match(strtolower($item['status'] ?? '')){
                                'setuju' => 'text-green-600 bg-green-100', 
                                'revisi' => 'text-yellow-700 bg-yellow-100', 
                                'menunggu' => 'text-gray-600 bg-gray-100',
                                default => 'text-gray-600 bg-gray-100',
                            }; 
                        ?>
                            <tr class='hover:bg-gray-50 transition-colors'>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-800 font-medium'><?php echo htmlspecialchars($item['nama']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-xs font-semibold'><span class='px-3 py-1 rounded-full <?php echo $status_class; ?>'><?php echo htmlspecialchars($item['status']); ?></span></td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm font-medium'>
                                    <div class='flex gap-2'>
                                        <a href="/docutrack/public/admin/pengajuan-lpj/show/<?php echo $item['id'] ?? ''; ?>?ref=dashboard" class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>Lihat RAB</a>
                                        <button class='bg-red-100 text-red-700 px-3 py-1.5 rounded-md text-xs font-medium hover:bg-red-200 transition-colors'><i class='fas fa-trash'></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>