<?php
// File: src/views/pages/super_admin/kelola-akun.php

// Data Simulasi: Role Baru & Jurusan PNJ
if (!isset($list_users)) { 
    $list_users = [
        ['id' => 1, 'nama' => 'Dr. Ahmad Pengusul', 'email' => 'ahmad.p@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Informatika dan Komputer', 'status' => 'Aktif', 'last_login' => '2024-01-15 08:30:00'],
        ['id' => 2, 'nama' => 'Siti Verifikator, M.Ak', 'email' => 'siti.v@pnj.ac.id', 'role' => 'Verifikator', 'jurusan' => 'Akuntansi', 'status' => 'Aktif', 'last_login' => '2024-01-15 09:15:00'],
        ['id' => 3, 'nama' => 'Budi PPK, S.T', 'email' => 'budi.ppk@pnj.ac.id', 'role' => 'PPK', 'jurusan' => 'Teknik Sipil', 'status' => 'Aktif', 'last_login' => '2024-01-14 14:20:00'],
        ['id' => 4, 'nama' => 'Rina Bendahara', 'email' => 'rina.b@pnj.ac.id', 'role' => 'Bendahara', 'jurusan' => 'Administrasi Niaga', 'status' => 'Aktif', 'last_login' => '2024-01-15 10:00:00'],
        ['id' => 5, 'nama' => 'Prof. Wadir Dua', 'email' => 'wadir2@pnj.ac.id', 'role' => 'Wadir', 'jurusan' => 'Manajemen Pusat', 'status' => 'Aktif', 'last_login' => '2024-01-10 11:00:00'],
        ['id' => 6, 'nama' => 'Dian Grafika', 'email' => 'dian.g@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Grafika dan Penerbitan', 'status' => 'Tidak Aktif', 'last_login' => '2023-12-20 15:00:00'],
        ['id' => 7, 'nama' => 'Joko Mesin', 'email' => 'joko.m@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Mesin', 'status' => 'Aktif', 'last_login' => '2024-01-15 13:30:00'],
        ['id' => 8, 'nama' => 'Sarah Elektro', 'email' => 'sarah.e@pnj.ac.id', 'role' => 'Pengusul', 'jurusan' => 'Teknik Elektro', 'status' => 'Aktif', 'last_login' => '2024-01-12 09:00:00'],
    ]; 
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-blue-400 to-blue-500 hover:shadow-[0_0_20px_rgba(59,130,246,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h3 class="text-4xl font-bold mb-1" id="totalAkun">0</h3>
                    <p class="text-sm font-medium opacity-80">Total User</p>
                </div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-users fa-xl"></i>
                </div>
            </div>
            <div class="relative z-10 mt-4 flex items-center text-xs font-medium bg-white/20 w-fit px-2 py-1 rounded-lg">
                <i class="fas fa-database mr-1"></i> Terdaftar
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-green-400 to-green-500 hover:shadow-[0_0_20px_rgba(34,197,94,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h3 class="text-4xl font-bold mb-1" id="totalAktif">0</h3>
                    <p class="text-sm font-medium opacity-80">Akun Aktif</p>
                </div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-user-check fa-xl"></i>
                </div>
            </div>
            <div class="relative z-10 mt-4 flex items-center text-xs font-medium bg-white/20 w-fit px-2 py-1 rounded-lg">
                <span id="persenAktif" class="mr-1 font-bold">0%</span> dari total
            </div>
        </div>
        <div class="relative group p-6 rounded-xl shadow-md overflow-hidden text-white bg-gradient-to-br from-red-400 to-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.5)] hover:-translate-y-1 transition-all duration-300 ease-out">
            <div class="absolute inset-0 z-0 opacity-[0.04] bg-[repeating-linear-gradient(45deg,transparent,transparent_4px,rgba(255,255,255,0.5)_4px,rgba(255,255,255,0.5)_5px)] [background-size:10px_10px]"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <h3 class="text-4xl font-bold mb-1" id="totalTidakAktif">0</h3>
                    <p class="text-sm font-medium opacity-80">Tidak Aktif</p>
                </div>
                <div class="p-3 rounded-full bg-white/10 opacity-80 group-hover:opacity-100 transition-opacity">
                    <i class="fas fa-user-slash fa-xl"></i>
                </div>
            </div>
            <div class="relative z-10 mt-4 flex items-center text-xs font-medium bg-white/20 w-fit px-2 py-1 rounded-lg">
                <span id="persenTidakAktif" class="mr-1 font-bold">0%</span> non-aktif
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-br from-white to-blue-50/30 rounded-2xl shadow-lg border border-blue-100/50 overflow-hidden transition-all duration-300 hover:shadow-xl">
        
        <div class="p-6 border-b border-blue-100/50">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent mb-1">
                        Manajemen Akun
                    </h2>
                    <p class="text-sm text-gray-500">Kelola akun Pengusul, PPK, Bendahara, dll.</p>
                </div>
                
                <div>
                    <button id="btn-tambah-user" class="px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white text-sm font-semibold hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center gap-2">
                        <i class="fas fa-user-plus"></i> Tambah User
                    </button>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4">
                
                <div class="relative flex-1">
                    <input type="text" id="search-users" placeholder="Cari nama, email..." 
                           class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm bg-white border border-gray-200 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm"
                           style="color: #111827;">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
                
                <div class="relative">
                    <select id="filter-jurusan" 
                            class="pl-10 pr-8 py-2.5 rounded-xl text-sm bg-white border border-gray-200 text-gray-900 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm cursor-pointer appearance-none min-w-[220px]"
                            style="color: #111827; background-color: #ffffff;">
                        <option value="" class="text-gray-500 font-medium">Semua Jurusan</option>
                        <option value="Teknik Informatika dan Komputer" class="text-gray-900">Teknik Informatika & Komputer</option>
                        <option value="Teknik Sipil" class="text-gray-900">Teknik Sipil</option>
                        <option value="Teknik Mesin" class="text-gray-900">Teknik Mesin</option>
                        <option value="Teknik Elektro" class="text-gray-900">Teknik Elektro</option>
                        <option value="Teknik Grafika dan Penerbitan" class="text-gray-900">Teknik Grafika & Penerbitan</option>
                        <option value="Akuntansi" class="text-gray-900">Akuntansi</option>
                        <option value="Administrasi Niaga" class="text-gray-900">Administrasi Niaga</option>
                        <option value="Manajemen Pusat" class="text-gray-900">Manajemen Pusat</option>
                    </select>
                    <i class="fas fa-university absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-10"></i>
                </div>
                
                <div class="relative">
                    <select id="filter-role" 
                            class="pl-10 pr-8 py-2.5 rounded-xl text-sm bg-white border border-gray-200 text-gray-900 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all shadow-sm cursor-pointer appearance-none min-w-[160px]"
                            style="color: #111827; background-color: #ffffff;">
                        <option value="" class="text-gray-500 font-medium">Semua Role</option>
                        <option value="Pengusul" class="text-gray-900">Pengusul</option>
                        <option value="Verifikator" class="text-gray-900">Verifikator</option>
                        <option value="PPK" class="text-gray-900">PPK</option>
                        <option value="Bendahara" class="text-gray-900">Bendahara</option>
                        <option value="Wadir" class="text-gray-900">Wadir</option>
                    </select>
                    <i class="fas fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 z-10"></i>
                    <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none z-10"></i>
                </div>
                
                <button id="reset-filter" class="px-4 py-2.5 rounded-xl bg-gray-50 border border-gray-200 text-gray-600 text-sm font-medium hover:bg-gray-100 hover:text-gray-800 transition-all shadow-sm">
                    <i class="fas fa-redo"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="table-users">
                <thead class="bg-blue-50/50 border-b border-blue-100 text-left">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">User Profile</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">Jurusan</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-800 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-blue-800 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tbody-users" class="divide-y divide-gray-100 bg-white/50">
                </tbody>
            </table>
        </div>

        <div class="p-5 border-t border-blue-100/50 bg-white/30 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-gray-600">
                Menampilkan <span id="showing-users" class="font-bold text-blue-700">0</span> dari <span id="total-users" class="font-bold text-gray-800">0</span> akun
            </div>
            <div id="pagination-users" class="flex gap-1 flex-wrap justify-center"></div>
        </div>

    </section>

</main>

<div id="toast-container" class="fixed top-6 right-6 z-[60] flex flex-col gap-3 w-auto max-w-md">
</div>

<div id="modal-user" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm overflow-y-auto">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 my-8 transform transition-all">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <h3 id="modal-title" class="text-xl font-bold text-gray-800">Tambah User Baru</h3>
            <button id="modal-close" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <form id="form-user" class="p-6 space-y-4">
            <input type="hidden" id="user-id">
            <input type="hidden" id="is-edit-mode" value="false">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap *</label>
                    <input type="text" id="user-nama" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 placeholder-gray-400"
                           placeholder="Dr. Nama Lengkap">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                    <input type="email" id="user-email" required
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 placeholder-gray-400"
                           placeholder="email@pnj.ac.id">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Role *</label>
                    <select id="user-role" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 cursor-pointer">
                        <option value="" disabled selected hidden class="text-gray-400">Pilih Role</option>
                        <option value="Pengusul">Pengusul</option>
                        <option value="Verifikator">Verifikator</option>
                        <option value="PPK">PPK</option>
                        <option value="Bendahara">Bendahara</option>
                        <option value="Wadir">Wadir</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Jurusan *</label>
                    <select id="user-jurusan" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 cursor-pointer">
                        <option value="" disabled selected hidden class="text-gray-400">Pilih Jurusan</option>
                        <option value="Teknik Informatika dan Komputer">Teknik Informatika & Komputer</option>
                        <option value="Teknik Sipil">Teknik Sipil</option>
                        <option value="Teknik Mesin">Teknik Mesin</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Grafika dan Penerbitan">Teknik Grafika & Penerbitan</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Administrasi Niaga">Administrasi Niaga</option>
                        <option value="Manajemen Pusat">Manajemen Pusat</option>
                    </select>
                </div>
            </div>

            <div id="add-mode-fields" class="space-y-4 pt-2 border-t border-gray-100">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Keamanan Akun</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                        <input type="password" id="add-password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900"
                               placeholder="******">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password *</label>
                        <input type="password" id="add-confirm-password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900"
                               placeholder="******">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Keamanan (Captcha) *</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="flex items-center gap-2">
                            <div id="captcha-preview" class="select-none bg-gray-100 border border-gray-300 rounded-lg px-6 py-2.5 text-xl font-mono font-bold tracking-widest text-gray-700 text-center w-32 relative overflow-hidden">
                                <div class="absolute inset-0 w-full h-full opacity-20 bg-[repeating-linear-gradient(45deg,transparent,transparent_2px,#000_2px,#000_3px)]"></div>
                                <span class="relative z-10" id="captcha-text">ABCD</span>
                            </div>
                            <button type="button" id="refresh-captcha" class="p-2.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all" title="Refresh Captcha">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        <input type="text" id="captcha-input" 
                               class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900 uppercase"
                               placeholder="Masukkan kode di atas">
                    </div>
                </div>
            </div>

            <div id="edit-mode-fields" class="space-y-4 pt-2 border-t border-gray-100 hidden">
                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Ubah Password (Opsional)</h4>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Lama (Admin) *</label>
                    <input type="password" id="edit-old-password" 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900"
                           placeholder="Masukkan password Anda untuk verifikasi">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru</label>
                        <input type="password" id="edit-new-password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900"
                               placeholder="Kosongkan jika tidak diubah">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                        <input type="password" id="edit-confirm-password" 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400/50 focus:border-blue-400 transition-all text-gray-900"
                               placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>
            
            <div id="status-field" class="hidden pt-2 border-t border-gray-100">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all">
                        <input type="radio" name="status" value="Aktif" checked
                               class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-400/50">
                        <span class="text-sm text-gray-700 font-medium">Aktif</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer bg-gray-50 px-4 py-2 rounded-lg border border-gray-200 hover:bg-gray-100 transition-all">
                        <input type="radio" name="status" value="Tidak Aktif"
                               class="w-4 h-4 text-blue-600 focus:ring-2 focus:ring-blue-400/50">
                        <span class="text-sm text-gray-700 font-medium">Tidak Aktif</span>
                    </label>
                </div>
            </div>
            
            <div class="flex gap-3 pt-4 border-t border-gray-100 mt-4">
                <button type="button" id="modal-cancel"
                        class="flex-1 px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 px-5 py-2.5 rounded-lg bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold hover:from-blue-600 hover:to-blue-700 transition-all shadow-md">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modal-delete" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-trash-alt text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Hapus User?</h3>
            <p class="text-gray-600 mb-6">
                Apakah Anda yakin ingin menghapus user <strong id="delete-user-name"></strong>? 
                Tindakan ini tidak dapat dibatalkan.
            </p>
            
            <div class="flex gap-3">
                <button id="delete-cancel"
                        class="flex-1 px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50 transition-all">
                    Batal
                </button>
                <button id="delete-confirm"
                        class="flex-1 px-5 py-2.5 rounded-lg bg-gradient-to-r from-red-500 to-red-600 text-white font-semibold hover:from-red-600 hover:to-red-700 transition-all shadow-md">
                    <i class="fas fa-trash-alt mr-2"></i>Hapus
                </button>
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

    // Fields Groups
    const addModeFields = document.getElementById('add-mode-fields');
    const editModeFields = document.getElementById('edit-mode-fields');
    const statusField = document.getElementById('status-field');

    // Captcha Elements
    const captchaTextEl = document.getElementById('captcha-text');
    const captchaInputEl = document.getElementById('captcha-input');
    const refreshCaptchaBtn = document.getElementById('refresh-captcha');
    let currentCaptcha = '';

    // Generate Random Captcha
    function generateCaptcha() {
        const chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
        let result = "";
        for (let i = 0; i < 5; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        currentCaptcha = result;
        captchaTextEl.textContent = result.split('').join(' '); // Add spacing for look
    }

    refreshCaptchaBtn.addEventListener('click', generateCaptcha);
    
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
    }
    
    function closeModal(modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    
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
        
        toast.className = `flex items-center gap-4 px-6 py-4 bg-white rounded-2xl shadow-2xl border-l-4 ${borderColor[type]} w-full transition-all duration-500 ease-out backdrop-blur-sm`;
        toast.style.transform = 'translateX(120%)';
        toast.style.opacity = '0';
        
        toast.innerHTML = `
            <div class="flex-shrink-0 relative">
                <div class="absolute inset-0 ${bgRings[type]} rounded-full blur-xl"></div>
                <div class="relative w-12 h-12 rounded-full bg-gradient-to-br ${colors[type]} flex items-center justify-center shadow-lg">
                    <i class="fas ${icons[type]} text-white text-xl"></i>
                </div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-800 text-sm leading-relaxed">${message}</p>
            </div>
            <button onclick="removeToast(this.parentElement)" class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-all">
                <i class="fas fa-times text-sm"></i>
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
    
    // Tambah User
    document.getElementById('btn-tambah-user').addEventListener('click', () => {
        modalTitle.textContent = 'Tambah User Baru';
        formUser.reset();
        
        // Reset Dropdowns to placeholder
        document.getElementById('user-role').value = "";
        document.getElementById('user-jurusan').value = "";

        document.getElementById('user-id').value = '';
        document.getElementById('is-edit-mode').value = 'false';
        
        // UI Toggles
        statusField.classList.add('hidden');
        addModeFields.classList.remove('hidden');
        editModeFields.classList.add('hidden');
        
        // Require Add Mode Fields
        document.getElementById('add-password').setAttribute('required', '');
        document.getElementById('add-confirm-password').setAttribute('required', '');
        document.getElementById('captcha-input').setAttribute('required', '');
        
        // Remove Require Edit Mode Fields
        document.getElementById('edit-old-password').removeAttribute('required');

        generateCaptcha();
        openModal(modalUser);
    });
    
    // Edit User
    window.editUser = (id) => {
        const user = dataUsers.find(u => u.id === id);
        if (!user) return;
        
        modalTitle.textContent = 'Edit User';
        formUser.reset();
        
        document.getElementById('user-id').value = user.id;
        document.getElementById('is-edit-mode').value = 'true';
        document.getElementById('user-nama').value = user.nama;
        document.getElementById('user-email').value = user.email;
        document.getElementById('user-role').value = user.role;
        document.getElementById('user-jurusan').value = user.jurusan;
        
        document.querySelector(`input[name="status"][value="${user.status}"]`).checked = true;
        
        // UI Toggles
        statusField.classList.remove('hidden');
        addModeFields.classList.add('hidden');
        editModeFields.classList.remove('hidden');

        // Remove Require Add Mode Fields
        document.getElementById('add-password').removeAttribute('required');
        document.getElementById('add-confirm-password').removeAttribute('required');
        document.getElementById('captcha-input').removeAttribute('required');

        // Require Old Password for Security
        document.getElementById('edit-old-password').setAttribute('required', '');
        
        openModal(modalUser);
    };
    
    // Delete User
    let deleteUserId = null;
    window.deleteUser = (id) => {
        const user = dataUsers.find(u => u.id === id);
        if (!user) return;
        
        deleteUserId = id;
        document.getElementById('delete-user-name').textContent = user.nama;
        openModal(modalDelete);
    };
    
    // Close Modals
    document.getElementById('modal-close').addEventListener('click', () => closeModal(modalUser));
    document.getElementById('modal-cancel').addEventListener('click', () => closeModal(modalUser));
    document.getElementById('delete-cancel').addEventListener('click', () => closeModal(modalDelete));
    
    // Submit Form User
    formUser.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const userId = document.getElementById('user-id').value;
        const isEditMode = document.getElementById('is-edit-mode').value === 'true';
        
        // --- VALIDATION LOGIC ---
        if (!isEditMode) {
            // Validate Add Mode
            const pass = document.getElementById('add-password').value;
            const confirm = document.getElementById('add-confirm-password').value;
            const captchaInput = document.getElementById('captcha-input').value.toUpperCase();

            if (pass !== confirm) {
                showToast('Password dan Konfirmasi Password tidak cocok!', 'error');
                return;
            }

            if (captchaInput.replace(/\s/g, '') !== currentCaptcha) {
                showToast('Kode Captcha salah!', 'error');
                generateCaptcha();
                document.getElementById('captcha-input').value = '';
                return;
            }
        } else {
            // Validate Edit Mode
            const oldPass = document.getElementById('edit-old-password').value;
            const newPass = document.getElementById('edit-new-password').value;
            const confirmNew = document.getElementById('edit-confirm-password').value;

            if (oldPass === "") {
                 showToast('Masukkan password admin untuk konfirmasi!', 'error');
                 return;
            }
            
            if (newPass !== "" && newPass !== confirmNew) {
                showToast('Password baru tidak cocok!', 'error');
                return;
            }
        }

        const userData = {
            nama: document.getElementById('user-nama').value,
            email: document.getElementById('user-email').value,
            role: document.getElementById('user-role').value,
            jurusan: document.getElementById('user-jurusan').value,
            status: isEditMode ? document.querySelector('input[name="status"]:checked').value : 'Aktif'
        };
        
        if (userId) {
            // Update existing user
            const index = dataUsers.findIndex(u => u.id == userId);
            if (index !== -1) {
                dataUsers[index] = { ...dataUsers[index], ...userData };
                showToast('User berhasil diperbarui!', 'success');
            }
        } else {
            // Add new user
            const newUser = {
                id: dataUsers.length > 0 ? Math.max(...dataUsers.map(u => u.id)) + 1 : 1,
                ...userData,
                last_login: new Date().toISOString().slice(0, 19).replace('T', ' ')
            };
            dataUsers.push(newUser);
            showToast('User baru berhasil ditambahkan!', 'success');
        }
        
        closeModal(modalUser);
        window.usersTable.allData = dataUsers;
        window.usersTable.applyFilters();
        updateStatistics(dataUsers);
    });
    
    // Confirm Delete
    document.getElementById('delete-confirm').addEventListener('click', () => {
        const index = dataUsers.findIndex(u => u.id === deleteUserId);
        if (index !== -1) {
            const deletedUser = dataUsers.splice(index, 1)[0];
            showToast(`User "${deletedUser.nama}" berhasil dihapus!`, 'success');
            
            closeModal(modalDelete);
            window.usersTable.allData = dataUsers;
            window.usersTable.applyFilters();
            updateStatistics(dataUsers);
        }
    });
    
    class UsersTableManager {
        constructor(data) {
            this.allData = data;
            this.filteredData = data;
            this.currentPage = 1;
            this.itemsPerPage = ITEMS_PER_PAGE;
            
            this.tbody = document.getElementById('tbody-users');
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
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white text-xs font-bold shadow-sm group-hover:scale-105 transition-transform">
                                ${initials}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-800 text-sm">
                                    ${item.nama}
                                </span>
                                <span class="text-xs text-gray-500">${item.email}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold border ${roleStyle}">
                            ${item.role}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        ${item.jurusan}
                    </td>
                    <td class="px-6 py-4">
                         <div class="flex items-center gap-2 px-2.5 py-1 rounded-full border w-fit ${statusText}">
                            <div class="w-2 h-2 rounded-full ${statusDot}"></div>
                            <span class="text-xs font-medium">${item.status}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
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

        render() {
            const start = (this.currentPage - 1) * this.itemsPerPage;
            const end = start + this.itemsPerPage;
            const pageData = this.filteredData.slice(start, end);
            
            if (pageData.length === 0) {
                this.tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">Tidak ada data ditemukan</td></tr>`;
            } else {
                this.tbody.innerHTML = pageData.map(item => this.createRow(item)).join('');
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
            const btnBase = "px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-200 border shadow-sm transform hover:scale-105";
            const activeBtn = "bg-gradient-to-r from-blue-500 to-blue-600 text-white border-transparent shadow-md";
            const inactiveBtn = "bg-white border-gray-200 text-gray-600 hover:border-blue-400 hover:text-blue-600";
            const disabledBtn = "bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed opacity-60";

            buttons.push(`<button onclick="window.usersTable.goToPage(${this.currentPage - 1})" ${this.currentPage === 1 ? 'disabled' : ''} class="${btnBase} ${this.currentPage === 1 ? disabledBtn : inactiveBtn}"><i class="fas fa-chevron-left"></i></button>`);
            
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= this.currentPage - 1 && i <= this.currentPage + 1)) {
                    buttons.push(`<button onclick="window.usersTable.goToPage(${i})" class="${btnBase} ${i === this.currentPage ? activeBtn : inactiveBtn}">${i}</button>`);
                } else if (i === this.currentPage - 2 || i === this.currentPage + 2) {
                    buttons.push(`<span class="px-2 text-gray-400 text-xs self-end pb-2">...</span>`);
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
});
</script>