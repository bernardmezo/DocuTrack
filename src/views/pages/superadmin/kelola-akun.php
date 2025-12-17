<?php

// File: src/views/pages/superadmin/kelola-akun.php

// Ensure data variables are set
if (!isset($list_users)) $list_users = [];
?>

<main class="main-content font-poppins p-3 sm:p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Statistics Cards -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 md:gap-6 mb-6 sm:mb-8">
        <!-- Total Users -->
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h3 class="text-3xl sm:text-4xl font-bold mb-1" id="totalAkun">0</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Total User</p>
                </div>
                <div class="p-2.5 sm:p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-users text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
            <div class="relative z-10 mt-3 sm:mt-4 flex items-center text-xs font-medium bg-white/20 w-fit px-2 py-1 rounded-lg">
                <i class="fas fa-database mr-1"></i> Terdaftar
            </div>
        </div>
        
        <!-- Active Users -->
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h3 class="text-3xl sm:text-4xl font-bold mb-1" id="totalAktif">0</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Akun Aktif</p>
                </div>
                <div class="p-2.5 sm:p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-user-check text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
            <div class="relative z-10 mt-3 sm:mt-4 flex items-center text-xs font-medium bg-white/20 w-fit px-2 py-1 rounded-lg">
                <span id="persenAktif" class="mr-1 font-bold">0%</span> dari total
            </div>
        </div>
        
        <!-- Inactive Users -->
        <div class="relative group p-4 sm:p-5 md:p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out sm:col-span-2 lg:col-span-1">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h3 class="text-3xl sm:text-4xl font-bold mb-1" id="totalTidakAktif">0</h3>
                    <p class="text-xs sm:text-sm font-medium opacity-80">Tidak Aktif</p>
                </div>
                <div class="p-2.5 sm:p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-user-slash text-lg sm:text-xl md:fa-xl"></i>
                </div>
            </div>
            <div class="relative z-10 mt-3 sm:mt-4 flex items-center text-xs font-medium bg-white/20 w-fit px-2 py-1 rounded-lg">
                <span id="persenTidakAktif" class="mr-1 font-bold">0%</span> non-aktif
            </div>
        </div>
    </section>

    <!-- Management Section -->
    <section class="bg-gradient-to-br from-white to-blue-50/30 rounded-xl sm:rounded-2xl shadow-lg border border-blue-100/50 overflow-hidden transition-all duration-300 hover:shadow-xl">
        
        <!-- Header -->
        <div class="p-4 sm:p-5 md:p-6 border-b border-blue-100/50">
            <div class="flex flex-col gap-4 mb-4 sm:mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-0.5 sm:mb-1">
                            Manajemen Akun
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-500">Kelola akun Pengusul, PPK, Bendahara, dll.</p>
                    </div>
                    
                    <button id="btn-tambah-user" class="px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white text-xs sm:text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center gap-1.5 sm:gap-2 whitespace-nowrap">
                        <i class="fas fa-user-plus"></i>
                        <span class="hidden xs:inline">Tambah</span>
                        <span class="hidden sm:inline">User</span>
                    </button>
                </div>
                
                <!-- Filter Toggle Button (Mobile) -->
                <button id="toggle-filters" class="lg:hidden flex items-center justify-center gap-2 w-full px-4 py-2.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 text-sm font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-filter"></i>
                    <span>Filter & Pencarian</span>
                    <i class="fas fa-chevron-down ml-auto transition-transform" id="filter-icon"></i>
                </button>
            </div>
            
            <!-- Filters Container -->
            <div id="filters-container" class="hidden lg:flex flex-col sm:flex-row gap-3 sm:gap-4">
                
                <!-- Search Input -->
                <div class="relative flex-1">
                    <input type="text" id="search-users" placeholder="Cari nama, email..." 
                           class="w-full pl-9 sm:pl-10 pr-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-xs sm:text-sm bg-white border border-gray-200 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm"
                           style="color: #111827;">
                    <i class="fas fa-search absolute left-3 sm:left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                </div>
                
                <!-- Jurusan Filter -->
                <div class="relative">
                    <select id="filter-jurusan" 
                            class="w-full sm:w-auto pl-9 sm:pl-10 pr-8 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-xs sm:text-sm bg-white border border-gray-200 text-gray-900 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm cursor-pointer appearance-none sm:min-w-[220px]"
                            style="color: #111827; background-color: #ffffff;">
                        <option value="" class="text-gray-500 font-medium">Semua Jurusan</option>
                        <?php foreach($list_jurusan ?? [] as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-university absolute left-3 sm:left-3.5 top-1/2 -translate-y-1/2 text-gray-400 z-10 text-sm"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-10"></i>
                </div>
                
                <!-- Role Filter -->
                <div class="relative">
                    <select id="filter-role" 
                            class="w-full sm:w-auto pl-9 sm:pl-10 pr-8 py-2 sm:py-2.5 rounded-lg sm:rounded-xl text-xs sm:text-sm bg-white border border-gray-200 text-gray-900 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm cursor-pointer appearance-none sm:min-w-[160px]"
                            style="color: #111827; background-color: #ffffff;">
                        <option value="" class="text-gray-500 font-medium">Semua Role</option>
                        <?php foreach($list_roles ?? [] as $role): ?>
                            <option value="<?= htmlspecialchars($role['namaRole']) ?>"><?= htmlspecialchars($role['namaRole']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <i class="fas fa-id-badge absolute left-3 sm:left-3.5 top-1/2 -translate-y-1/2 text-gray-400 z-10 text-sm"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-10"></i>
                </div>
                
                <!-- Reset Button -->
                <button id="reset-filter" class="px-4 py-2 sm:py-2.5 rounded-lg sm:rounded-xl bg-gray-50 border border-gray-200 text-gray-600 text-xs sm:text-sm font-medium hover:bg-gray-100 hover:text-gray-800 transition-all shadow-sm">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>

        <!-- Table (Desktop) & Cards (Mobile) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full" id="table-users">
                <thead class="bg-blue-50/50 border-b border-blue-100 text-left">
                    <tr>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">User Profile</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">Role</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">Jurusan</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-xs font-bold text-blue-800 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-users" class="divide-y divide-gray-100 bg-white/50">
                </tbody>
            </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="md:hidden" id="mobile-cards-container">
            <!-- Cards will be inserted here by JavaScript -->
        </div>

        <!-- Footer / Pagination -->
        <div class="p-4 sm:p-5 border-t border-blue-100/50 bg-white/30 flex flex-col sm:flex-row items-center justify-between gap-3 sm:gap-4">
            <div class="text-xs sm:text-sm text-gray-600 text-center sm:text-left">
                Menampilkan <span id="showing-users" class="font-bold text-blue-700">0</span> dari <span id="total-users" class="font-bold text-gray-800">0</span> akun
            </div>
            <div id="pagination-users" class="flex gap-1 flex-wrap justify-center"></div>
        </div>

    </section>

</main>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-4 sm:top-6 right-4 sm:right-6 z-[60] flex flex-col gap-3 w-auto max-w-[calc(100vw-2rem)] sm:max-w-md"></div>

<!-- Modal User -->
<div id="modal-user" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm overflow-y-auto p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-2xl my-4 sm:my-8 transform transition-all">
        <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200">
            <h3 id="modal-title" class="text-lg sm:text-xl font-bold text-gray-800">Tambah User Baru</h3>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 transition-colors p-2 -mr-2">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="form-user" method="POST" action="/docutrack/public/superadmin/kelola-akun/store" class="p-4 sm:p-6 space-y-4">
            <input type="hidden" id="user-id" name="id">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" id="user-nama" name="nama" required
                           class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 placeholder-gray-400 text-sm"
                           placeholder="Dr. Nama Lengkap">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input type="email" id="user-email" name="email" required
                           class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 placeholder-gray-400 text-sm"
                           placeholder="email@pnj.ac.id">
                </div>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role *</label>
                    <select id="user-role" name="roleId" required
                            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 cursor-pointer text-sm">
                        <option value="" disabled selected hidden class="text-gray-400">Pilih Role</option>
                        <?php foreach($list_roles ?? [] as $role): ?>
                            <option value="<?= $role['roleId'] ?>"><?= htmlspecialchars($role['namaRole']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan</label>
                    <select id="user-jurusan" name="namaJurusan"
                            class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 cursor-pointer text-sm">
                        <option value="" selected class="text-gray-400">Pilih Jurusan (Opsional)</option>
                         <?php foreach($list_jurusan ?? [] as $jurusan): ?>
                            <option value="<?= htmlspecialchars($jurusan) ?>"><?= htmlspecialchars($jurusan) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div id="password-section" class="space-y-4 pt-2 border-t border-gray-100">
                <h4 class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider">Keamanan Akun</h4>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password <span id="pass-req">*</span></label>
                        <input type="password" id="user-password" name="password"
                               class="w-full px-3 sm:px-4 py-2 sm:py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 text-sm"
                               placeholder="******">
                    </div>
                </div>
            </div>
            
            <div id="status-field" class="hidden pt-2 border-t border-gray-100">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                <div class="flex gap-3 sm:gap-4">
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-3 sm:px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all flex-1 justify-center">
                        <input type="radio" name="status" value="Aktif" checked
                               class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-400/50">
                        <span class="text-sm text-gray-700 font-medium">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-3 sm:px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all flex-1 justify-center">
                        <input type="radio" name="status" value="Tidak Aktif"
                               class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-400/50">
                        <span class="text-sm text-gray-700 font-medium">Tidak Aktif</span>
                    </label>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4 border-t border-gray-100 mt-4">
                <button type="button" id="modal-cancel"
                        class="flex-1 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all text-sm">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-md text-sm">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delete -->
<div id="modal-delete" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-xl sm:rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <div class="p-5 sm:p-6 text-center">
            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-red-600 text-xl sm:text-2xl"></i>
            </div>
            <h3 class="text-lg sm:text-xl font-bold text-gray-800 mb-2">Hapus User?</h3>
            <p class="text-sm sm:text-base text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus user <strong id="delete-user-name"></strong>? 
                Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <div class="flex gap-3">
                <button id="delete-cancel"
                        class="flex-1 px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all text-sm">
                    Batal
                </button>
                <form id="form-delete" method="POST" action="" class="flex-1">
                    <button type="submit"
                            class="w-full px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md text-sm">
                        <i class="fas fa-trash-alt mr-2"></i>Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    window.dataUsers = <?= json_encode($list_users) ?>;
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const dataUsers = window.dataUsers;
    const ITEMS_PER_PAGE = 10;
    
    // Modal Elements
    const modalUser = document.getElementById('modal-user');
    const modalDelete = document.getElementById('modal-delete');
    const formUser = document.getElementById('form-user');
    const modalTitle = document.getElementById('modal-title');
    const statusField = document.getElementById('status-field');

    // Mobile Filter Toggle
    const toggleFilters = document.getElementById('toggle-filters');
    const filtersContainer = document.getElementById('filters-container');
    const filterIcon = document.getElementById('filter-icon');
    
    if (toggleFilters) {
        toggleFilters.addEventListener('click', () => {
            const isHidden = filtersContainer.classList.contains('hidden');
            if (isHidden) {
                filtersContainer.classList.remove('hidden');
                filtersContainer.classList.add('flex');
                filterIcon.classList.add('rotate-180');
            } else {
                filtersContainer.classList.add('hidden');
                filtersContainer.classList.remove('flex');
                filterIcon.classList.remove('rotate-180');
            }
        });
    }

    function updateStatistics(data) {
        const total = data.length;
        const aktif = data.filter(u => u.status === 'Aktif').length;
        const tidakAktif = data.filter(u => u.status === 'Tidak Aktif').length;
        
        const persenAktif = total > 0 ? ((aktif / total) * 100).toFixed(0) : 0;
        const persenTidakAktif = total > 0 ? ((tidakAktif / total) * 100).toFixed(0) : 0;
        
        animateValue('totalAkun', 0, total, 800);
        animateValue('totalAktif', 0, aktif, 800);
        animateValue('totalTidakAktif', 0, tidakAktif, 800);
        
        document.getElementById('persenAktif').textContent = persenAktif + '%';
        document.getElementById('persenTidakAktif').textContent = persenTidakAktif + '%';
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
    
    // Modal Functions
    function openModal(modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }
    
    // Tambah User
    document.getElementById('btn-tambah-user').addEventListener('click', () => {
        modalTitle.textContent = 'Tambah User Baru';
        formUser.reset();
        formUser.action = '/docutrack/public/superadmin/kelola-akun/store';
        
        document.getElementById('user-id').value = '';
        
        statusField.classList.add('hidden');
        document.getElementById('pass-req').classList.remove('hidden');
        document.getElementById('user-password').setAttribute('required', '');
        
        openModal(modalUser);
    });
    
    // Edit User
    window.editUser = (id) => {
        const user = dataUsers.find(u => u.id == id);
        if (!user) return;
        
        modalTitle.textContent = 'Edit User';
        formUser.reset();
        formUser.action = `/docutrack/public/superadmin/kelola-akun/update/${id}`;
        
        document.getElementById('user-id').value = user.id;
        document.getElementById('user-nama').value = user.nama;
        document.getElementById('user-email').value = user.email;
        
        const roleSelect = document.getElementById('user-role');
        for (let i = 0; i < roleSelect.options.length; i++) {
            if (roleSelect.options[i].text === user.role) {
                roleSelect.selectedIndex = i;
                break;
            }
        }

        document.getElementById('user-jurusan').value = user.jurusan || '';
        
        if (user.status) {
            const statusRadio = document.querySelector(`input[name="status"][value="${user.status}"]`);
            if (statusRadio) statusRadio.checked = true;
        }
        
        // UI Toggles untuk mode edit
        statusField.classList.remove('hidden');
        document.getElementById('pass-req').classList.add('hidden');
        document.getElementById('user-password').removeAttribute('required');
        
        openModal(modalUser);
    };
    
    // Delete User
    window.deleteUser = (id) => {
        const user = dataUsers.find(u => u.id == id);
        if (!user) return;
        
        document.getElementById('delete-user-name').textContent = user.nama;
        document.getElementById('form-delete').action = `/docutrack/public/superadmin/kelola-akun/delete/${id}`;
        openModal(modalDelete);
    };
    
    // Close Modals
    document.getElementById('modal-close').addEventListener('click', () => closeModal(modalUser));
    document.getElementById('modal-cancel').addEventListener('click', () => closeModal(modalUser));
    document.getElementById('delete-cancel').addEventListener('click', () => closeModal(modalDelete));
    
    // Close modal when clicking outside
    modalUser.addEventListener('click', (e) => {
        if (e.target === modalUser) closeModal(modalUser);
    });
    modalDelete.addEventListener('click', (e) => {
        if (e.target === modalDelete) closeModal(modalDelete);
    });
    
    class UsersTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('tbody-users');
            this.mobileContainer = document.getElementById('mobile-cards-container');
            this.paginationContainer = document.getElementById('pagination-users');
            this.showingSpan = document.getElementById('showing-users');
            this.totalSpan = document.getElementById('total-users');
            this.searchInput = document.getElementById('search-users');
            this.filterJurusan = document.getElementById('filter-jurusan');
            this.filterRole = document.getElementById('filter-role');
            this.resetBtn = document.getElementById('reset-filter');
            
            this.init();
        }
        
        init() {
            updateStatistics(this.allData);
            this.render();
            this.attachEventListeners();
        }
        
        attachEventListeners() {
            const handleFilter = () => {
                this.currentPage = 1;
                this.applyFilters();
            };

            this.searchInput.addEventListener('input', debounce(handleFilter, 300));
            this.filterJurusan.addEventListener('change', handleFilter);
            this.filterRole.addEventListener('change', handleFilter);
            this.resetBtn.addEventListener('click', () => {
                this.searchInput.value = '';
                this.filterJurusan.value = '';
                this.filterRole.value = '';
                handleFilter();
            });
        }
        
        applyFilters() {
            const searchTerm = this.searchInput.value.toLowerCase().trim();
            const jurusanFilter = this.filterJurusan.value;
            const roleFilter = this.filterRole.value;

            this.filteredData = this.allData.filter(item => {
                const matchSearch = !searchTerm || 
                    item.nama.toLowerCase().includes(searchTerm) ||
                    item.email.toLowerCase().includes(searchTerm);
                
                const matchJurusan = !jurusanFilter || item.jurusan === jurusanFilter;
                const matchRole = !roleFilter || item.role === roleFilter;
                
                return matchSearch && matchJurusan && matchRole;
            });

            this.render();
        }
        
        createRow(item) {
            const initials = item.nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            
            const roleColors = {
                'Pengusul': 'text-blue-700 bg-blue-100 border-blue-200',
                'Verifikator': 'text-purple-700 bg-purple-100 border-purple-200',
                'PPK': 'text-orange-700 bg-orange-100 border-orange-200',
                'Bendahara': 'text-emerald-700 bg-emerald-100 border-emerald-200',
                'Wadir': 'text-rose-700 bg-rose-100 border-rose-200'
            };
            const roleStyle = roleColors[item.role] || 'text-gray-700 bg-gray-100';
            const statusDot = item.status === 'Aktif' ? 'bg-green-500' : 'bg-gray-400';
            const statusText = item.status === 'Aktif' ? 'text-green-700 bg-green-50 border-green-200' : 'text-gray-600 bg-gray-100 border-gray-200';

            return `
                <tr class="hover:bg-blue-50/40 transition-colors duration-150 group border-b border-gray-50 last:border-b-0">
                    <td class="px-4 lg:px-6 py-3 lg:py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm group-hover:scale-105 transition-transform">
                                ${initials}
                            </div>
                            <div class="flex flex-col min-w-0">
                                <span class="font-semibold text-gray-800 text-sm truncate">
                                    ${item.nama}
                                </span>
                                <span class="text-xs text-gray-500 truncate">${item.email}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border ${roleStyle} whitespace-nowrap">
                            ${item.role}
                        </span>
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-sm text-gray-600">
                        ${item.jurusan}
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4">
                         <div class="flex items-center gap-2 px-2.5 py-1 rounded-full border w-fit ${statusText}">
                            <div class="w-2 h-2 rounded-full ${statusDot}"></div>
                            <span class="text-xs font-medium">${item.status}</span>
                        </div>
                    </td>
                    <td class="px-4 lg:px-6 py-3 lg:py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <button onclick="editUser(${item.id})" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 hover:border-blue-300 transition-all shadow-sm flex items-center gap-1.5">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                            <button onclick="deleteUser(${item.id})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        createMobileCard(item) {
            const initials = item.nama.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
            
            const roleColors = {
                'Pengusul': 'text-blue-700 bg-blue-100 border-blue-200',
                'Verifikator': 'text-purple-700 bg-purple-100 border-purple-200',
                'PPK': 'text-orange-700 bg-orange-100 border-orange-200',
                'Bendahara': 'text-emerald-700 bg-emerald-100 border-emerald-200',
                'Wadir': 'text-rose-700 bg-rose-100 border-rose-200'
            };
            const roleStyle = roleColors[item.role] || 'text-gray-700 bg-gray-100';
            const statusDot = item.status === 'Aktif' ? 'bg-green-500' : 'bg-gray-400';
            const statusText = item.status === 'Aktif' ? 'text-green-700 bg-green-50 border-green-200' : 'text-gray-600 bg-gray-100 border-gray-200';

            return `
                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-sm font-bold shadow-sm flex-shrink-0">
                            ${initials}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-800 text-sm mb-0.5 truncate">${item.nama}</h3>
                            <p class="text-xs text-gray-500 truncate">${item.email}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="px-2 py-0.5 rounded-md text-xs font-semibold border ${roleStyle}">
                                    ${item.role}
                                </span>
                                <div class="flex items-center gap-1.5 px-2 py-0.5 rounded-full border ${statusText}">
                                    <div class="w-1.5 h-1.5 rounded-full ${statusDot}"></div>
                                    <span class="text-xs font-medium">${item.status}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex items-center gap-1.5 text-xs text-gray-600 mb-3">
                            <i class="fas fa-university text-gray-400"></i>
                            <span class="truncate">${item.jurusan}</span>
                        </div>
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editUser(${item.id})" class="px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 transition-all flex items-center gap-1.5">
                                <i class="fas fa-pen"></i> Edit
                            </button>
                            <button onclick="deleteUser(${item.id})" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        render() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            if (pageData.length === 0) {
                this.tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Tidak ada data ditemukan</td></tr>`;
                this.mobileContainer.innerHTML = `<div class="p-8 text-center text-gray-400 italic">Tidak ada data ditemukan</div>`;
            } else {
                // Desktop table
                this.tbody.innerHTML = pageData.map(item => this.createRow(item)).join('');
                // Mobile cards
                this.mobileContainer.innerHTML = `<div class="p-4 space-y-3">${pageData.map(item => this.createMobileCard(item)).join('')}</div>`;
            }
            
            this.renderPagination();
            this.updateInfo();
        }
        
        updateInfo() {
            const start = (this.currentPage - 1) * this.itemsPerPage + 1;
            const end = Math.min(start + this.itemsPerPage - 1, this.filteredData.length);
            this.showingSpan.textContent = this.filteredData.length > 0 ? `${start}-${end}` : '0';
            this.totalSpan.textContent = this.filteredData.length;
        }

        renderPagination() {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (totalPages <= 1) { this.paginationContainer.innerHTML = ''; return; }
            
            let buttons = [];
            const btnBase = "px-2.5 sm:px-3 py-1.5 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105";
            const activeBtn = "bg-gradient-to-r from-blue-500 to-blue-600 text-white border-transparent shadow-md";
            const inactiveBtn = "bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600";
            const disabledBtn = "bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed opacity-60";

            buttons.push(`<button onclick="window.usersTable.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''} class="${btnBase} ${this.currentPage === 1 ? disabledBtn : inactiveBtn}"><i class="fas fa-chevron-left"></i></button>`);
            
            // Simplified pagination for mobile
            if (window.innerWidth < 640) {
                buttons.push(`<span class="px-3 py-1.5 text-sm font-medium text-gray-600">${this.currentPage} / ${totalPages}</span>`);
            } else {
                for (let i = 1; i <= totalPages; i++) {
                    if (i === 1 || i === totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                        buttons.push(`<button onclick="window.usersTable.goToPage(${i})" class="${btnBase} ${i === this.currentPage ? activeBtn : inactiveBtn}">${i}</button>`);
                    } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                        buttons.push(`<span class="px-2 text-gray-400 text-xs self-end pb-2">...</span>`);
                    }
                }
            }
            
            buttons.push(`<button onclick="window.usersTable.goToPage(${this.currentPage + 1})" ${this.currentPage === totalPages ? 'disabled' : ''} class="${btnBase} ${this.currentPage === totalPages ? disabledBtn : inactiveBtn}"><i class="fas fa-chevron-right"></i></button>`);
            
            this.paginationContainer.innerHTML = buttons.join('');
        }

        goToPage(page) {
            const totalPages = Math.ceil(this.filteredData.length / this.itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                this.currentPage = page;
                this.render();
                // Scroll to top on mobile
                if (window.innerWidth < 768) {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            }
        }
    }
    
    function debounce(func, wait) {
        let timeout;
        return function(...args) {
            const later = () => { clearTimeout(timeout); func(...args); };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    window.usersTable = new UsersTableManager(dataUsers);
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            window.usersTable.renderPagination();
        }, 250);
    });
});
</script>