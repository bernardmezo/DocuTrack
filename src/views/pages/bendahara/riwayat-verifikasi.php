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

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-[70px] max-w-7xl mx-auto w-full">
    
    <section id="riwayat-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8 flex flex-col">
        
        <div class="mb-6 pb-5 border-b border-gray-200">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">Riwayat Verifikasi Bendahara</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar semua usulan yang telah Anda proses (Dana Diberikan atau Revisi).</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-3 mb-6">
            <div class="relative flex-1">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 z-10"></i>
                <input type="text" id="search-riwayat" placeholder="Cari Nama Kegiatan..."
                       class="w-full pl-11 pr-4 py-2.5 text-sm text-gray-900 font-medium bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm hover:border-gray-400"
                       aria-label="Cari Kegiatan">
            </div>
            
            <div class="relative w-full lg:w-60">
                <i class="fas fa-filter absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                <select id="filter-status-riwayat" 
                        style="color: #374151 !important;"
                        class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                    <option value="" style="color: #374151 !important; font-weight: 600;">Semua Status</option>
                    <option value="dana diberikan" style="color: #374151 !important; font-weight: 600;">Dana Diberikan</option>
                    <option value="revisi" style="color: #374151 !important; font-weight: 600;">Revisi</option>
                </select>
                <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
            </div>
            
            <div class="relative w-full lg:w-80">
                <i class="fas fa-graduation-cap absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 pointer-events-none z-10"></i>
                <select id="filter-jurusan-riwayat"
                        style="color: #374151 !important;"
                        class="w-full pl-11 pr-10 py-2.5 text-sm font-semibold bg-white rounded-lg border-2 border-gray-300 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 shadow-sm appearance-none cursor-pointer hover:border-gray-400 hover:bg-gray-50">
                    <option value="" style="color: #374151 !important; font-weight: 600;">Semua Jurusan</option>
                    <option value="teknik informatika dan komputer" style="color: #374151 !important; font-weight: 600;">Teknik Informatika dan Komputer</option>
                    <option value="akuntansi" style="color: #374151 !important; font-weight: 600;">Akuntansi</option>
                    <option value="administrasi niaga" style="color: #374151 !important; font-weight: 600;">Administrasi Niaga</option>
                    <option value="teknik sipil" style="color: #374151 !important; font-weight: 600;">Teknik Sipil</option>
                    <option value="teknik mesin" style="color: #374151 !important; font-weight: 600;">Teknik Mesin</option>
                    <option value="teknik elektro" style="color: #374151 !important; font-weight: 600;">Teknik Elektro</option>
                    <option value="teknik grafika dan penerbitan" style="color: #374151 !important; font-weight: 600;">Teknik Grafika dan Penerbitan</option>
                </select>
                <i class="fas fa-chevron-down absolute top-1/2 right-4 -translate-y-1/2 text-gray-600 pointer-events-none text-xs"></i>
            </div>
        </div>
        
        <div class="overflow-x-auto border border-gray-100 rounded-lg">
            <table class="w-full min-w-[900px]" id="table-riwayat">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Kegiatan & Pengusul</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Tgl. Diputuskan</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-riwayat" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center px-6 py-4 border-t border-gray-200 gap-4 mt-4">
            <div id="pagination-info" class="text-sm text-gray-600"></div>
            <div id="pagination-riwayat" class="flex gap-1"></div>
        </div>
        </div>
    </section>

</main>

<script>
    // Pastikan data dikirim dari controller
    window.dataRiwayat = <?= json_encode($list_riwayat ?? []) ?>;
</script>
<script src="/docutrack/public/assets/js/bendahara/riwayat-verifikasi.js"></script>
