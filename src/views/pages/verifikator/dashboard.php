<?php
// File: src/views/pages/verifikator/dashboard.php

// Ambil data dari controller
$stats = $stats ?? ['total' => 0, 'disetujui' => 0, 'ditolak' => 0, 'pending' => 0];
$list_usulan = $list_usulan ?? [];
?>

<main class="main-content font-poppins p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['total']); ?></h3><p class="text-sm font-medium opacity-80">Total Usulan</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-layer-group fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['disetujui']); ?></h3><p class="text-sm font-medium opacity-80">Disetujui</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-check-circle fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['ditolak']); ?></h3><p class="text-sm font-medium opacity-80">Ditolak</p></div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity"><i class="fas fa-times-circle fa-xl"></i></div>
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-yellow-900 bg-gradient-to-br from-yellow-300 to-yellow-400 hover:shadow-[0_0_20px_rgba(250,204,21,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.05] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(0,0,0,0.3)_4px,rgba(0,0,0,0.3)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div><h3 class="text-5xl font-bold mb-1"><?php echo htmlspecialchars($stats['pending']); ?></h3><p class="text-sm font-medium opacity-80">Pending</p></div>
                <div class="p-3 rounded-full bg-black/10 opacity-80 group-hover:opacity-100 transition-opacity text-yellow-800"><i class="fas fa-hourglass-half fa-xl"></i></div>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="flex justify-between items-center p-6 border-b border-gray-200 flex-shrink-0">
            <h3 class="text-xl font-semibold text-gray-800">Daftar Usulan Masuk (Semua Status)</h3>
        </div>
        <div class="overflow-y-auto overflow-x-auto max-h-96">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($list_usulan)): ?>
                        <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">Tidak ada usulan masuk.</td></tr>
                    <?php else: ?>
                        <?php $nomor = 1; ?>
                        <?php foreach ($list_usulan as $item): 
                            
                            // --- PERBAIKAN: Logika Status Dinamis (Lengkap) ---
                            $status_text = htmlspecialchars($item['status'] ?? 'N/A');
                            $status_lower = strtolower($status_text);
                            $status_class = '';
                            $icon_class = '';
                            $row_class = 'bg-white';

                            switch ($status_lower) {
                                case 'disetujui':
                                    $status_class = 'text-green-700 bg-green-100';
                                    $icon_class = 'fas fa-check-circle';
                                    $row_class = 'bg-white opacity-80'; // Selesai (redup)
                                    break;
                                case 'ditolak':
                                    $status_class = 'text-red-700 bg-red-100';
                                    $icon_class = 'fas fa-times-circle';
                                    $row_class = 'bg-white opacity-80'; // Selesai (redup)
                                    break;
                                case 'revisi':
                                    $status_class = 'text-yellow-700 bg-yellow-100';
                                    $icon_class = 'fas fa-exclamation-triangle';
                                    $row_class = 'bg-white'; // Menunggu Admin
                                    break;
                                case 'telah direvisi':
                                    $status_class = 'text-purple-700 bg-purple-100';
                                    $icon_class = 'fas fa-sync-alt'; 
                                    $row_class = 'bg-purple-50 font-medium'; // Perlu Aksi
                                    break;
                                default: // 'Menunggu'
                                    $status_class = 'text-gray-700 bg-gray-100';
                                    $icon_class = 'fas fa-hourglass-half';
                                    $row_class = 'bg-gray-50 font-medium'; // Perlu Aksi
                            }
                            // --- Akhir Logika Status ---
                        ?>
                            <tr class='<?php echo $row_class; ?> hover:bg-gray-100 transition-colors'>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-700'><?php echo $nomor++; ?>.</td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-900'><?php echo htmlspecialchars($item['nama']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm text-gray-600'><?php echo htmlspecialchars($item['pengusul']); ?></td>
                                <td class='px-6 py-5 whitespace-nowrap text-xs font-semibold'>
                                    <span class='inline-flex items-center gap-1.5 px-3 py-1 rounded-full <?php echo $status_class; ?>'>
                                        <i class='<?php echo $icon_class; ?>'></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td class='px-6 py-5 whitespace-nowrap text-sm font-medium'>
                                    <div class='flex gap-2'>
                                        <a href="/docutrack/public/verifikator/telaah/show/<?php echo $item['id'] ?? ''; ?>?ref=dashboard" 
                                           class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>
                                           Lihat
                                        </a>
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