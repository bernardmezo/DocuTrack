<?php
// File: src/views/pages/bendahara/riwayat-verifikasi.php

// Dummy data untuk testing
if (!isset($list_riwayat)) {
    $list_riwayat = [
        
    ];
}

// Hitung statistik
$stats = [
    'total' => count($list_riwayat),
    'danaDiberikan' => count(array_filter($list_riwayat, fn($item) => strtolower($item['status']) === 'dana diberikan')),
    'revisi' => count(array_filter($list_riwayat, fn($item) => strtolower($item['status']) === 'revisi'))
];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <!-- Enhanced Table Riwayat with Filters & Pagination -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <h3 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i>
                    Riwayat Verifikasi
                </h3>
                
                <!-- Filter Controls -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <div class="relative">
                        <select id="filter-status-riwayat" class="pl-10 pr-10 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400">
                            <option value="">Semua Status</option>
                            <option value="dana diberikan">Dana Diberikan</option>
                            <option value="revisi">Revisi</option>
                        </select>
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <div class="relative">
                        <select id="filter-jurusan-riwayat" class="pl-10 pr-10 py-2 border border-gray-300  rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400">
                            <option value="">Semua Jurusan</option>
                            <option value="Teknik Informatika">Teknik Informatika</option>
                            <option value="Sistem Informasi">Sistem Informasi</option>
                            <option value="Manajemen">Manajemen</option>
                            <option value="Akuntansi">Akuntansi</option>
                            <option value="Desain Grafis">Desain Grafis</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <button id="reset-filter-riwayat" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        Reset
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-4 relative">
                <input type="text" id="search-riwayat" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <div class="overflow-y-auto overflow-x-auto" style="max-height: 500px;">
            <table class="w-full min-w-[900px]" id="table-riwayat">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Tgl. Diputuskan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-riwayat" class="divide-y divide-gray-100">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination Riwayat -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-sm text-gray-600">
                    Menampilkan <span id="showing-riwayat" class="font-semibold text-gray-800">0</span> dari <span id="total-riwayat" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-riwayat" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>

<script>
    window.dataRiwayat = <?= json_encode($list_riwayat) ?>;
</script>
<script src="/docutrack/public/assets/js/bendahara/riwayat-verifikasi.js"></script>