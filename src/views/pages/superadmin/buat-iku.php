<?php
// File: src/views/pages/superadmin/buat-iku.php

if (!isset($list_iku)) {
    $list_iku = [];
}
if (!isset($pagination)) {
    $pagination = ['current_page' => 1, 'total_pages' => 1, 'total_items' => 0, 'showing_from' => 0, 'showing_to' => 0];
}
if (!isset($filters)) {
    $filters = ['search' => ''];
}

function build_url_iku($params = [])
{
    $current_params = $_GET;
    $merged = array_merge($current_params, $params);
    return '?' . http_build_query($merged);
}
?>

<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Main Content Section -->
    <section class="bg-gradient-to-br from-white to-blue-50/30 rounded-xl sm:rounded-2xl shadow-lg border border-blue-100/50 overflow-hidden transition-all duration-300 hover:shadow-xl">
        
        <!-- Header -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-blue-100/50">
            <div class="flex flex-col gap-4 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500 bg-clip-text text-transparent mb-1 animate-gradient">
                            Buat IKU
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500 leading-relaxed">Kelola Indikator Kinerja Utama (IKU) untuk mengukur keberhasilan program studi</p>
                    </div>
                    
                    <div class="flex-shrink-0">
                        <button onclick="openModalTambah()" class="w-full sm:w-auto px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i> 
                            <span>Tambah IKU</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <form method="GET" action="" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <div class="relative flex-1">
                    <input type="text" id="search-iku" name="search" 
                           value="<?= htmlspecialchars($filters['search']) ?>" 
                           placeholder="Cari IKU..." 
                           class="w-full pl-9 sm:pl-10 pr-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-sm bg-white border border-gray-200 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm"
                           style="color: #111827;"
                           autocomplete="off">
                    <i class="fas fa-search absolute left-3 sm:left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
                <button type="submit" class="hidden"></button> <!-- Implicit submit -->
                
                <button type="button" onclick="toggleHiddenList()" id="btnToggleHidden" class="px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl bg-white border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 hover:border-gray-300 transition-all shadow-sm flex items-center gap-2 justify-center">
                    <i class="fas fa-eye-slash text-xs sm:text-sm"></i>
                    <span class="hidden xs:inline">Lihat Tersembunyi</span>
                    <span class="xs:hidden">Tersembunyi</span>
                    <span id="hiddenCount" class="bg-gray-200 text-gray-700 px-1.5 sm:px-2 py-0.5 rounded-full text-xs font-bold min-w-[20px] text-center">0</span>
                </button>
            </form>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 border-b border-blue-100 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Nama Indikator
                        </th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-center bg-gradient-to-r from-blue-700 via-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody id="tbody-iku-desktop" class="divide-y divide-gray-100 bg-white/50">
                    <?php if (empty($list_iku)) : ?>
                        <tr>
                            <td colspan="2" class="px-6 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/40 text-gray-400 mb-4 shadow-sm border border-white/50">
                                    <i class="fas fa-search text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Data IKU tidak ditemukan.</p>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php
                        $delay = 0;
                        foreach ($list_iku as $item) :
                            $delay += 50;
                            ?>
                            <tr class="iku-row hover:bg-blue-50/40 transition-colors duration-150 group border-b border-gray-50 last:border-b-0" 
                                style="animation-delay: <?= $delay ?>ms;"
                                data-iku-id="<?= $item['id'] ?>">
                                
                                <td class="px-6 py-4">
                                    <div class="flex items-start gap-4">
                                        <span class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white text-xs font-bold shadow-sm group-hover:scale-105 transition-transform">
                                            <?= $item['id'] ?>
                                        </span>
                                        <div class="flex flex-col justify-center">
                                            <p class="font-semibold text-gray-800 text-sm group-hover:text-blue-600 transition-colors leading-snug">
                                                <?= htmlspecialchars($item['nama']) ?>
                                            </p>
                                            <?php if (!empty($item['deskripsi'])) : ?>
                                                <span class="text-xs text-gray-500 mt-1 line-clamp-1"><?= htmlspecialchars($item['deskripsi']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openModalEdit(<?= $item['id'] ?>, '<?= addslashes(htmlspecialchars($item['nama'])) ?>', '<?= addslashes(htmlspecialchars($item['deskripsi'] ?? '')) ?>')" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all shadow-sm flex items-center gap-1.5" title="Edit">
                                            <i class="fas fa-pen"></i> Edit
                                        </button>
                                        <button onclick="deleteIku(<?= $item['id'] ?>)" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all shadow-sm flex items-center gap-1.5" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <button onclick="toggleIkuRow(<?= $item['id'] ?>)" class="btn-toggle-visibility p-1.5 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-all" title="Sembunyikan" data-iku-id="<?= $item['id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden divide-y divide-gray-100" id="cards-iku">
            <?php if (empty($list_iku)) : ?>
                <div class="p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white/40 text-gray-400 mb-4 shadow-sm border border-white/50">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium text-sm">Data IKU tidak ditemukan.</p>
                </div>
            <?php else : ?>
                <?php foreach ($list_iku as $item) : ?>
                    <div class="iku-row p-4 hover:bg-blue-50/40 transition-colors space-y-3" data-iku-id="<?= $item['id'] ?>">
                        <!-- Header -->
                        <div class="flex items-start gap-3">
                            <span class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white text-xs font-bold shadow-sm">
                                <?= $item['id'] ?>
                            </span>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-semibold text-gray-800 text-sm leading-tight">
                                    <?= htmlspecialchars($item['nama']) ?>
                                </h4>
                                <?php if (!empty($item['deskripsi'])) : ?>
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2"><?= htmlspecialchars($item['deskripsi']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex items-center gap-2 pt-2 border-t border-gray-100">
                            <button onclick="openModalEdit(<?= $item['id'] ?>, '<?= addslashes(htmlspecialchars($item['nama'])) ?>', '<?= addslashes(htmlspecialchars($item['deskripsi'] ?? '')) ?>')" class="flex-1 px-3 py-2 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all flex items-center justify-center gap-1.5">
                                <i class="fas fa-pen text-[10px]"></i> 
                                <span>Edit</span>
                            </button>
                            <button onclick="deleteIku(<?= $item['id'] ?>)" class="flex-1 px-3 py-2 text-xs font-medium text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 hover:border-red-300 transition-all flex items-center justify-center gap-1.5">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                                <span>Hapus</span>
                            </button>
                            <button onclick="toggleIkuRow(<?= $item['id'] ?>)" class="btn-toggle-visibility px-3 py-2 text-gray-400 bg-gray-50 border border-gray-200 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-200 rounded-lg transition-all" title="Sembunyikan" data-iku-id="<?= $item['id'] ?>">
                                <i class="fas fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Footer with Pagination -->
        <div class="p-4 sm:p-5 border-t border-blue-100/50 bg-white/30 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                Menampilkan <span id="showing-iku" class="font-bold text-blue-700"><?= $pagination['showing_from'] ?>-<?= $pagination['showing_to'] ?></span> dari <span id="total-iku" class="font-bold text-gray-800"><?= $pagination['total_items'] ?></span> data
            </div>
            
            <?php if ($pagination['total_pages'] > 1) : ?>
                <div class="flex items-center gap-1 sm:gap-1.5">
                    <a href="<?= build_url_iku(['page' => $pagination['current_page'] - 1]) ?>" 
                       class="px-2 sm:px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105 <?= $pagination['current_page'] <= 1 ? 'bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed opacity-60' : 'bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600' ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>

                    <?php 
                    // Smart pagination for mobile
                    $start = max(1, $pagination['current_page'] - 1);
                    $end = min($pagination['total_pages'], $pagination['current_page'] + 1);
                    
                    if ($start > 1) : ?>
                        <a href="<?= build_url_iku(['page' => 1]) ?>" 
                           class="hidden sm:flex px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105 bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600">
                            1
                        </a>
                        <?php if ($start > 2) : ?>
                            <span class="hidden sm:inline px-2 text-gray-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++) : ?>
                        <?php if ($i === $pagination['current_page']) : ?>
                            <span class="px-2 sm:px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white border-transparent shadow-md">
                                <?= $i ?>
                            </span>
                        <?php else : ?>
                            <a href="<?= build_url_iku(['page' => $i]) ?>" 
                               class="px-2 sm:px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105 bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600">
                                <?= $i ?>
                            </a>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($end < $pagination['total_pages']) : ?>
                        <?php if ($end < $pagination['total_pages'] - 1) : ?>
                            <span class="hidden sm:inline px-2 text-gray-400">...</span>
                        <?php endif; ?>
                        <a href="<?= build_url_iku(['page' => $pagination['total_pages']]) ?>" 
                           class="hidden sm:flex px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105 bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600">
                            <?= $pagination['total_pages'] ?>
                        </a>
                    <?php endif; ?>

                    <a href="<?= build_url_iku(['page' => $pagination['current_page'] + 1]) ?>" 
                       class="px-2 sm:px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105 <?= $pagination['current_page'] >= $pagination['total_pages'] ? 'bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed opacity-60' : 'bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

    </section>
</main>

<!-- Toast Notification Container -->
<div id="toast-container" class="fixed top-4 sm:top-6 right-4 sm:right-6 z-[60] flex flex-col gap-2 sm:gap-3 w-[calc(100%-2rem)] sm:w-auto max-w-md">
</div>

<!-- Modal Tambah IKU -->
<div id="modalTambah" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col animate-modal-in">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2 sm:gap-3">
                    <div class="p-1.5 sm:p-2 bg-blue-100 rounded-lg text-blue-600">
                        <i class="fas fa-plus text-sm sm:text-base"></i>
                    </div>
                    <span>Tambah IKU Baru</span>
                </h3>
                <button onclick="closeModalTambah()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formTambah" method="POST" action="/docutrack/public/superadmin/buat-iku/store" class="p-4 sm:p-6 overflow-y-auto flex-1">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Indikator</label>
                    <input type="text" name="nama" required 
                           class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all text-sm"
                           placeholder="Masukkan nama indikator">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" rows="4" 
                              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-blue-400 focus:border-blue-400 transition-all text-sm resize-none"
                              placeholder="Masukkan deskripsi indikator"></textarea>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-6 flex-shrink-0">
                <button type="button" onclick="closeModalTambah()" 
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-2.5 border border-gray-300 rounded-lg sm:rounded-xl text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-all order-2 sm:order-1">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg sm:rounded-xl text-white text-sm font-semibold hover:from-blue-600 hover:to-blue-700 shadow-lg shadow-blue-500/30 transition-all order-1 sm:order-2">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit IKU -->
<div id="modalEdit" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] flex flex-col animate-modal-in">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2 sm:gap-3">
                    <div class="p-1.5 sm:p-2 bg-amber-100 rounded-lg text-amber-600">
                        <i class="fas fa-pencil-alt text-sm sm:text-base"></i>
                    </div>
                    <span>Edit IKU</span>
                </h3>
                <button onclick="closeModalEdit()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
        </div>
        
        <form id="formEdit" method="POST" action="" class="p-4 sm:p-6 overflow-y-auto flex-1">
            <input type="hidden" name="id" id="editId">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Indikator</label>
                    <input type="text" name="nama" id="editNama" required 
                           class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all text-sm"
                           placeholder="Masukkan nama indikator">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="editDeskripsi" rows="4" 
                              class="w-full px-3 sm:px-4 py-2 sm:py-2.5 border border-gray-300 rounded-lg sm:rounded-xl focus:ring-2 focus:ring-amber-400 focus:border-amber-400 transition-all text-sm resize-none"
                              placeholder="Masukkan deskripsi indikator"></textarea>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 mt-6 flex-shrink-0">
                <button type="button" onclick="closeModalEdit()" 
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-2.5 border border-gray-300 rounded-lg sm:rounded-xl text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-all order-2 sm:order-1">
                    Batal
                </button>
                <button type="submit" 
                        class="w-full sm:flex-1 px-4 sm:px-6 py-2 sm:py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 rounded-lg sm:rounded-xl text-white text-sm font-semibold hover:from-amber-600 hover:to-amber-700 shadow-lg shadow-amber-500/30 transition-all order-1 sm:order-2">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Daftar IKU Tersembunyi -->
<div id="modalHidden" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl max-w-3xl w-full max-h-[80vh] flex flex-col animate-modal-in">
        <div class="p-4 sm:p-6 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center justify-between">
                <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center gap-2 sm:gap-3">
                    <div class="p-1.5 sm:p-2 bg-gray-100 rounded-lg text-gray-600">
                        <i class="fas fa-eye-slash text-sm sm:text-base"></i>
                    </div>
                    <span>IKU Tersembunyi</span>
                </h3>
                <button onclick="closeModalHidden()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                    <i class="fas fa-times text-lg sm:text-xl"></i>
                </button>
            </div>
        </div>
        
        <div id="hiddenListContent" class="p-4 sm:p-6 overflow-y-auto flex-1">
            <!-- Content akan diisi oleh JavaScript -->
        </div>
    </div>
</div>

<style>
    @keyframes modalIn {
        0% { opacity: 0; transform: scale(0.95) translateY(-20px); }
        100% { opacity: 1; transform: scale(1) translateY(0); }
    }
    .animate-modal-in {
        animation: modalIn 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .iku-row.hidden-row {
        display: none !important;
    }
    .highlight-row {
        animation: highlight 2s ease-out;
    }
    @keyframes highlight {
        0%, 100% { background-color: transparent; }
        50% { background-color: rgba(59, 130, 246, 0.1); }
    }
    @keyframes gradient {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    .animate-gradient {
        background-size: 200% 200%;
        animation: gradient 3s ease infinite;
    }
    
    /* Mobile optimizations */
    @media (max-width: 640px) {
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    }
    
    /* Extra small devices breakpoint */
    @media (min-width: 475px) {
        .xs\:inline {
            display: inline;
        }
        .xs\:hidden {
            display: none;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateStatistics();
    
    // Auto-search submit on enter
    document.getElementById('search-iku').addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            this.form.submit();
        }
    });
});

// Toast Notification Function
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    
    const colors = {
        success: 'from-green-500 to-green-600',
        error: 'from-red-500 to-red-600',
        info: 'from-blue-500 to-blue-600'
    };
    
    const bgRings = {
        success: 'bg-green-500/10',
        error: 'bg-red-500/10',
        info: 'bg-blue-500/10'
    };
    
    const borderColor = {
        success: 'border-green-500',
        error: 'border-red-500',
        info: 'border-blue-500'
    };
    
    toast.className = `flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-3 sm:py-4 bg-white rounded-xl sm:rounded-2xl shadow-2xl border-l-4 ${borderColor[type]} w-full transition-all duration-500 ease-out backdrop-blur-sm`;
    toast.style.transform = 'translateX(120%)';
    toast.style.opacity = '0';
    
    toast.innerHTML = `
        <div class="flex-shrink-0 relative">
            <div class="absolute inset-0 ${bgRings[type]} rounded-full blur-xl"></div>
            <div class="relative w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-br ${colors[type]} flex items-center justify-center shadow-lg">
                <i class="fas ${icons[type]} text-white text-base sm:text-xl"></i>
            </div>
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-xs sm:text-sm leading-relaxed">${message}</p>
        </div>
        <button onclick="removeToast(this.parentElement)" class="flex-shrink-0 w-7 h-7 sm:w-8 sm:h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
            <i class="fas fa-times text-xs sm:text-sm"></i>
        </button>
    `;
    
    container.appendChild(toast);
    
    // Trigger slide-in animation
    setTimeout(() => {
        toast.style.transform = 'translateX(0)';
        toast.style.opacity = '1';
    }, 50);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        removeToast(toast);
    }, 4000);
}

function removeToast(toast) {
    if (!toast || !toast.parentElement) return;
    toast.style.transform = 'translateX(120%)';
    toast.style.opacity = '0';
    setTimeout(() => {
        if (toast.parentElement) toast.remove();
    }, 500);
}

// Make removeToast available globally
window.removeToast = removeToast;

// Update Statistics
function updateStatistics() {
    const allRows = document.querySelectorAll('.iku-row');
    const hiddenRows = document.querySelectorAll('.iku-row.hidden-row');
    
    const total = allRows.length;
    const hidden = hiddenRows.length;
    const visible = total - hidden;
    
    const persenVisible = total > 0 ? ((visible / total) * 100).toFixed(0) : 0;
    const persenHidden = total > 0 ? ((hidden / total) * 100).toFixed(0) : 0;
    
    animateValue('totalIku', 0, total, 800);
    animateValue('totalVisible', 0, visible, 800);
    animateValue('totalHidden', 0, hidden, 800);
    
    if (document.getElementById('persenVisible')) {
        document.getElementById('persenVisible').textContent = persenVisible + '%';
    }
    if (document.getElementById('persenHidden')) {
        document.getElementById('persenHidden').textContent = persenHidden + '%';
    }
}

function animateValue(id, start, end, duration) {
    const element = document.getElementById(id);
    if(!element) return;
    let current = start;
    const range = end - start;
    const increment = end > start ? 1 : -1;
    const stepTime = Math.abs(Math.floor(duration / range));
    
    if (range === 0) { element.textContent = end; return; }

    const timer = setInterval(function() {
        current += increment;
        element.textContent = current;
        if (current == end) { clearInterval(timer); }
    }, Math.min(stepTime, 50));
    
    setTimeout(() => { element.textContent = end; clearInterval(timer); }, duration + 10);
}

// Fungsi Modal Tambah
function openModalTambah() {
    const modal = document.getElementById('modalTambah');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.getElementById('formTambah').reset();
    document.body.style.overflow = 'hidden'; // Prevent body scroll
}

function closeModalTambah() {
    const modal = document.getElementById('modalTambah');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = ''; // Restore body scroll
}

// Fungsi Modal Edit
function openModalEdit(id, nama, deskripsi) {
    const modal = document.getElementById('modalEdit');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    document.getElementById('editId').value = id;
    document.getElementById('editNama').value = nama;
    document.getElementById('editDeskripsi').value = deskripsi;
    
    document.getElementById('formEdit').action = '/docutrack/public/superadmin/buat-iku/update/' + id;
    document.body.style.overflow = 'hidden'; // Prevent body scroll
}

function closeModalEdit() {
    const modal = document.getElementById('modalEdit');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = ''; // Restore body scroll
}

function deleteIku(id) {
    if(confirm('Apakah Anda yakin ingin menghapus IKU ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/docutrack/public/superadmin/buat-iku/delete/' + id;
        document.body.appendChild(form);
        form.submit();
    }
}

// Fungsi Toggle Show/Hide Row
function toggleIkuRow(id) {
    const desktopRow = document.querySelector(`#tbody-iku-desktop [data-iku-id="${id}"]`);
    const mobileCard = document.querySelector(`#cards-iku [data-iku-id="${id}"]`);
    const btn = document.querySelector(`.btn-toggle-visibility[data-iku-id="${id}"]`);
    
    // Toggle both desktop and mobile views
    [desktopRow, mobileCard].forEach(element => {
        if (element) {
            element.classList.toggle('hidden-row');
        }
    });
    
    if (desktopRow || mobileCard) {
        const isHidden = (desktopRow && desktopRow.classList.contains('hidden-row')) || 
                        (mobileCard && mobileCard.classList.contains('hidden-row'));
        
        // Update all buttons with same data-iku-id
        document.querySelectorAll(`.btn-toggle-visibility[data-iku-id="${id}"]`).forEach(button => {
            const icon = button.querySelector('i');
            if (isHidden) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                button.title = 'Tampilkan';
            } else {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                button.title = 'Sembunyikan';
            }
        });
        
        showToast(isHidden ? 'IKU disembunyikan' : 'IKU ditampilkan', 'info');
        updateHiddenCount();
        updateStatistics();
    }
}

// Update counter IKU tersembunyi
function updateHiddenCount() {
    const hiddenRows = document.querySelectorAll('.iku-row.hidden-row');
    const count = hiddenRows.length;
    const countBadge = document.getElementById('hiddenCount');
    countBadge.textContent = count;
    
    // Update button appearance
    const btnToggle = document.getElementById('btnToggleHidden');
    if (count > 0) {
        countBadge.classList.remove('bg-gray-200', 'text-gray-700');
        countBadge.classList.add('bg-red-500', 'text-white');
    } else {
        countBadge.classList.remove('bg-red-500', 'text-white');
        countBadge.classList.add('bg-gray-200', 'text-gray-700');
    }
}

// Toggle Modal Hidden List
function toggleHiddenList() {
    const hiddenRows = document.querySelectorAll('.iku-row.hidden-row');
    
    if (hiddenRows.length === 0) {
        showToast('Tidak ada IKU yang disembunyikan', 'info');
        return;
    }
    
    openModalHidden();
}

function openModalHidden() {
    const modal = document.getElementById('modalHidden');
    const content = document.getElementById('hiddenListContent');
    const hiddenRows = document.querySelectorAll('.iku-row.hidden-row');
    
    if (hiddenRows.length === 0) {
        content.innerHTML = `
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-gray-100 text-gray-400 mb-4">
                    <i class="fas fa-eye text-xl sm:text-2xl"></i>
                </div>
                <p class="text-gray-500 font-medium text-sm">Tidak ada IKU yang disembunyikan</p>
            </div>
        `;
    } else {
        let html = '<div class="space-y-2 sm:space-y-3">';
        hiddenRows.forEach((row) => {
            const id = row.getAttribute('data-iku-id');
            const namaEl = row.querySelector('.font-semibold');
            const deskripsiEl = row.querySelector('.text-gray-500.mt-1, .text-xs.text-gray-500');
            const nama = namaEl ? namaEl.textContent.trim() : '';
            const deskripsi = deskripsiEl ? deskripsiEl.textContent.trim() : '';
            
            html += `
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 p-3 sm:p-4 bg-gray-50 rounded-lg sm:rounded-xl border border-gray-200 hover:border-gray-300 transition-all">
                    <div class="flex items-start gap-2 sm:gap-3 flex-1 min-w-0">
                        <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 text-white text-xs font-bold shadow-sm">
                            ${id}
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-gray-800 font-semibold text-sm leading-tight">${nama}</p>
                            ${deskripsi ? `<p class="text-xs text-gray-500 mt-1 line-clamp-2">${deskripsi}</p>` : ''}
                        </div>
                    </div>
                    <button onclick="unhideIku(${id})" class="w-full sm:w-auto sm:ml-3 px-3 sm:px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white text-xs sm:text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-2 shadow-sm flex-shrink-0">
                        <i class="fas fa-eye text-xs"></i>
                        <span>Tampilkan</span>
                    </button>
                </div>
            `;
        });
        html += '</div>';
        content.innerHTML = html;
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden'; // Prevent body scroll
}

function closeModalHidden() {
    const modal = document.getElementById('modalHidden');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = ''; // Restore body scroll
}

// Fungsi untuk menampilkan kembali IKU
function unhideIku(id) {
    const desktopRow = document.querySelector(`#tbody-iku-desktop [data-iku-id="${id}"]`);
    const mobileCard = document.querySelector(`#cards-iku [data-iku-id="${id}"]`);
    
    [desktopRow, mobileCard].forEach(element => {
        if (element) {
            element.classList.remove('hidden-row');
        }
    });
    
    // Update all toggle buttons
    document.querySelectorAll(`.btn-toggle-visibility[data-iku-id="${id}"]`).forEach(btn => {
        const icon = btn.querySelector('i');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        btn.title = 'Sembunyikan';
    });
    
    updateHiddenCount();
    updateStatistics();
    showToast('IKU berhasil ditampilkan kembali', 'success');
    
    // Refresh modal content
    const hiddenRows = document.querySelectorAll('.iku-row.hidden-row');
    if (hiddenRows.length === 0) {
        closeModalHidden();
    } else {
        openModalHidden();
    }
    
    // Scroll ke row yang ditampilkan
    setTimeout(() => {
        const targetRow = desktopRow || mobileCard;
        if (targetRow) {
            targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
            targetRow.classList.add('highlight-row');
            setTimeout(() => targetRow.classList.remove('highlight-row'), 2000);
        }
    }, 300);
}

// Close modal ketika klik di luar
document.addEventListener('click', function(event) {
    const modalTambah = document.getElementById('modalTambah');
    const modalEdit = document.getElementById('modalEdit');
    const modalHidden = document.getElementById('modalHidden');
    
    if (event.target === modalTambah) {
        closeModalTambah();
    }
    if (event.target === modalEdit) {
        closeModalEdit();
    }
    if (event.target === modalHidden) {
        closeModalHidden();
    }
});

// ESC key untuk close modal
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModalTambah();
        closeModalEdit();
        closeModalHidden();
    }
});
</script>