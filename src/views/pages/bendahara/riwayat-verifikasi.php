<?php
// File: src/views/pages/bendahara/riwayat-verifikasi.php

// Dummy data untuk testing
if (!isset($list_riwayat)) {
    $list_riwayat = [];
}

// Hitung statistik
$stats = [
    'total' => count($list_riwayat),
    'danaDiberikan' => count(array_filter($list_riwayat, fn($item) => strtolower($item['status']) === 'dana diberikan')),
    'revisi' => count(array_filter($list_riwayat, fn($item) => strtolower($item['status']) === 'revisi'))
];
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">
    
    <!-- Enhanced Table Riwayat with Filters & Pagination - RESPONSIVE -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8 flex flex-col">
        <!-- Header Section -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-gray-200 flex-shrink-0">
            <!-- Title and Filters Container -->
            <div class="flex flex-col gap-4">
                <!-- Title -->
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-blue-600"></i>
                    <span>Riwayat Verifikasi</span>
                </h3>
                
                <!-- Filter Controls - Stack on Mobile -->
                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <!-- Status Filter -->
                    <div class="relative flex-1 sm:flex-initial">
                        <select id="filter-status-riwayat" 
                                style="color: #374151 !important; font-size: 14px !important;"
                                class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-green-400 focus:ring-2 focus:ring-green-500 focus:border-green-500 sm:min-w-[180px]">
                            <option value="" selected style="color: #374151 !important;">Semua Status</option>
                            <option value="Dana Diberikan" style="color: #374151 !important;">Dana Diberikan</option>
                            <option value="Revisi" style="color: #374151 !important;">Revisi</option>
                        </select>
                        <i class="fas fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <!-- Jurusan Filter -->
                    <div class="relative flex-1">
                        <select id="filter-jurusan-riwayat"
                                style="color: #374151 !important; font-size: 14px !important;"
                                class="w-full pl-9 pr-8 py-2 sm:py-2.5 border border-gray-300 rounded-lg bg-white transition-all duration-200 appearance-none cursor-pointer hover:border-blue-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="" selected style="color: #374151 !important;">Semua Jurusan</option>
                            <option value="Teknik Informatika dan Komputer" style="color: #374151 !important;">Teknik Informatika dan Komputer</option>
                            <option value="Akuntansi" style="color: #374151 !important;">Akuntansi</option>
                            <option value="Administrasi Niaga" style="color: #374151 !important;">Administrasi Niaga</option>
                            <option value="Teknik Sipil" style="color: #374151 !important;">Teknik Sipil</option>
                            <option value="Teknik Mesin" style="color: #374151 !important;">Teknik Mesin</option>
                            <option value="Teknik Elektro" style="color: #374151 !important;">Teknik Elektro</option>
                            <option value="Teknik Grafika dan Penerbitan" style="color: #374151 !important;">Teknik Grafika dan Penerbitan</option>
                        </select>
                        <i class="fas fa-graduation-cap absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm pointer-events-none"></i>
                        <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                    </div>
                    
                    <!-- Reset Button -->
                    <button id="reset-filter-riwayat" class="w-full sm:w-auto px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-redo text-xs"></i>
                        <span>Reset</span>
                    </button>
                </div>
            </div>
            
            <!-- Search Bar -->
            <div class="mt-3 sm:mt-4 relative">
                <input type="text" id="search-riwayat" placeholder="Cari nama kegiatan, pengusul, atau NIM..." class="w-full pl-9 sm:pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs sm:text-sm"></i>
            </div>
        </div>

        <!-- Desktop Table View (Hidden on Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <div class="overflow-y-auto" style="max-height: 500px;">
                <table class="min-w-full" id="table-riwayat">
                    <thead class="bg-gradient-to-r from-blue-50 to-indigo-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">No</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[250px]">Nama Kegiatan</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Tgl. Diputuskan</th>
                            <th class="px-3 sm:px-4 md:px-6 py-3 sm:py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody id="tbody-riwayat" class="divide-y divide-gray-100 bg-white">
                        <!-- Data will be populated by JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View (Visible on Mobile Only) -->
        <div class="md:hidden overflow-y-auto" style="max-height: 500px;">
            <div id="mobile-riwayat-list" class="p-3 space-y-3">
                <!-- Mobile cards will be populated by JavaScript -->
            </div>
        </div>

        <!-- Pagination Riwayat - Responsive -->
        <div class="p-3 sm:p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
                <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                    Menampilkan <span id="showing-riwayat" class="font-semibold text-gray-800">0</span> dari <span id="total-riwayat" class="font-semibold text-gray-800">0</span> data
                </div>
                <div id="pagination-riwayat" class="flex gap-1 flex-wrap justify-center"></div>
            </div>
        </div>
    </section>

</main>

<style>
    /* Mobile Card Styling */
    .mobile-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        cursor: pointer;
    }
    
    .mobile-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        border-color: #3b82f6;
    }
    
    .mobile-card:active {
        transform: translateY(0);
    }
    
    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e5e7eb;
    }
    
    .mobile-card-number {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
    }
    
    .mobile-card-row {
        margin-bottom: 0.875rem;
    }
    
    .mobile-card-row:last-of-type {
        margin-bottom: 0;
    }
    
    .mobile-card-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.375rem;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .mobile-card-label i {
        color: #3b82f6;
        font-size: 0.75rem;
    }
    
    .mobile-card-value {
        font-size: 0.9rem;
        color: #1f2937;
        font-weight: 500;
        line-height: 1.5;
    }
    
    .mobile-card-kegiatan {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        line-height: 1.4;
    }
    
    .mobile-card-kegiatan:hover {
        color: #3b82f6;
    }
    
    .mobile-card-mahasiswa {
        font-size: 0.85rem;
        color: #4b5563;
        margin-top: 0.25rem;
    }
    
    .mobile-card-prodi {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .mobile-card-footer {
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .mobile-card-date {
        font-size: 0.8rem;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    /* Status Badge Styling */
    .status-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }
    
    .status-badge i {
        font-size: 0.625rem;
    }
    
    .status-dana-diberikan {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border: 1px solid #86efac;
    }
    
    .status-revisi {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        border: 1px solid #fcd34d;
    }
    
    .status-disetujui {
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        color: #166534;
        border: 1px solid #86efac;
    }
    
    .status-ditolak {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
        border: 1px solid #fca5a5;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1.5rem;
        color: #6b7280;
    }
    
    .empty-state i {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .empty-state-text {
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .empty-state-subtext {
        font-size: 0.85rem;
        color: #9ca3af;
        margin-top: 0.5rem;
    }
    
    /* Smooth animations */
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
    
    .mobile-card {
        animation: slideIn 0.3s ease forwards;
    }
</style>

<script>
    // Pastikan data dikirim dari controller
    window.dataRiwayat = <?= json_encode($list_riwayat ?? []) ?>;
</script>
<script src="/docutrack/public/assets/js/bendahara/riwayat-verifikasi.js"></script>