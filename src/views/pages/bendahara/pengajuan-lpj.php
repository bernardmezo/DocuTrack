<?php
// File: src/views/pages/bendahara/pengajuan-lpj.php
// Ensure data is available
if (!isset($list_lpj)) { $list_lpj = []; }
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <?php if(isset($success_message) && $success_message): ?>
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-700 font-medium"><?= htmlspecialchars($success_message) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <?php if(isset($error_message) && $error_message): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
            <p class="text-red-700 font-medium"><?= htmlspecialchars($error_message) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Table LPJ -->
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
                        <select id="filter-jurusan-lpj"
                                style="color: #374151 !important; font-size: 14px !important; min-width: 280px !important;"
                                class="pl-10 pr-10 py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400 focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                            <option value="Teknik Elektro" style="color: #374151 !important;">Teknik Elektro</option>
                            <option value="Teknik Mesin" style="color: #374151 !important;">Teknik Mesin</option>
                            <option value="Teknik Sipil" style="color: #374151 !important;">Teknik Sipil</option>
                            <option value="Teknik Informatika dan Komputer" style="color: #374151 !important;">Teknik Informatika dan Komputer</option>
                            <option value="Teknik Grafika dan Penerbitan" style="color: #374151 !important;">Teknik Grafika dan Penerbitan</option>
                            <option value="Akuntansi" style="color: #374151 !important;">Akuntansi</option>
                            <option value="Administrasi Niaga" style="color: #374151 !important;">Administrasi Niaga</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
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
                <input type="text" id="search-lpj" placeholder="Cari nama kegiatan, mahasiswa, atau NIM..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <div class="border border-gray-100 rounded-lg" style="min-height: 400px;">
            <table class="w-full min-w-[1000px]" id="table-lpj">
                <thead class="bg-gradient-to-r from-green-50 to-emerald-50">
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
    window.dataLPJ = <?= json_encode($list_lpj) ?>;
</script>
<script src="/docutrack/public/assets/js/bendahara/pengajuan-lpj.js"></script>