<?php
// File: src/views/pages/admin/pengajuan_usulan.php
?>

<style>
    /* Fix untuk Dropdown Styling */
    select {
        color: #1f2937 !important;
        background-color: #ffffff !important;
        -webkit-appearance: none;
        appearance: none;
    }
    select option[value=""], select option[disabled] { color: #9ca3af !important; }
    select option:not([value=""]):not([disabled]) { color: #1f2937 !important; }
    
    /* Firefox fix */
    @-moz-document url-prefix() { select { color: #1f2937 !important; } }
</style>

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

    <section id="form-section" class="transition-opacity duration-500 ease-out">
<!-- ======== MULAI FORM ========= -->
        <form id="kak-form-element" action="/docutrack/public/admin/pengajuan-usulan/store" method="POST" enctype="multipart/form-data">
            <div id="stepper-container" class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden mb-8"></div>

            <div class="form-content-wrapper relative min-h-[500px]">

                <div id="form-tahap-1" class="form-step active">
                    <section class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden">
                        <div class="mb-8">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6 text-center">Input Data Pengusul/Pelaksana</h2>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                
                                <div class="relative">
                                    <label for="nama_pengusul_step1" class="block text-sm font-medium text-gray-700 mb-2">Nama Pengusul</label>
                                    <input required type="text" id="nama_pengusul_step1" name="nama_pengusul_step1" 
                                        class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600" 
                                        placeholder="Masukkan nama pengusul">
                                </div>

                                <div class="relative">
                                    <label for="nim_nip" class="block text-sm font-medium text-gray-700 mb-2">NIM/NIP</label>
                                    <input required type="text" id="nim_nip" name="nim_nip" 
                                        class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600" 
                                        placeholder="Masukkan NIM atau NIP">
                                </div>

                                <div class="relative">
                                    <label for="jurusan" class="block text-sm font-medium text-gray-700 mb-2">Jurusan</label>
                                    <div class="relative">
                                        <select required id="jurusan" name="jurusan" onchange="updateProdi()"
                                            class="block w-full px-4 py-3.5 pr-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600 cursor-pointer">
                                            <option value="" disabled selected class="text-gray-500">Pilih Jurusan</option>
                                            <option value="Teknik Sipil">Teknik Sipil</option>
                                            <option value="Teknik Mesin">Teknik Mesin</option>
                                            <option value="Teknik Elektro">Teknik Elektro</option>
                                            <option value="Teknik Informatika dan Komputer">Teknik Informatika dan Komputer</option>
                                            <option value="Teknik Grafika dan Penerbitan">Teknik Grafika dan Penerbitan</option>
                                            <option value="Akuntansi">Akuntansi</option>
                                            <option value="Administrasi Niaga">Administrasi Niaga</option>
                                            <option value="Pascasarjana">Pascasarjana</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="relative">
                                    <label for="prodi" class="block text-sm font-medium text-gray-700 mb-2">Prodi</label>
                                    <div class="relative">
                                        <select required id="prodi" name="prodi" disabled
                                            class="block w-full px-4 py-3.5 pr-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600 disabled:bg-gray-100 disabled:text-gray-400 cursor-pointer">
                                            <option value="">Pilih Jurusan Terlebih Dahulu</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:col-span-2 relative">
                                    <label for="nama_kegiatan_step1" class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan</label>
                                    <input required type="text" id="nama_kegiatan_step1" name="nama_kegiatan_step1" 
                                        class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600" 
                                        placeholder="Masukkan nama kegiatan">
                                </div>

                                <div class="md:col-span-2 relative">
                                    <label for="wadir_tujuan" class="block text-sm font-medium text-gray-700 mb-2">Wadir Tujuan</label>
                                    <div class="relative">
                                        <select required id="wadir_tujuan" name="wadir_tujuan" 
                                            class="block w-full px-4 py-3.5 pr-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-2 focus:border-blue-600 focus:ring-blue-600 cursor-pointer">
                                            <option value="">Pilih Wadir Tujuan</option>
                                            <option value="1">Wadir 1 - Akademik</option>
                                            <option value="2">Wadir 2 - Umum & Keuangan</option>
                                            <option value="3">Wadir 3 - Kemahasiswaan</option>
                                            <option value="4">Wadir 4 - Kerjasama & Hubungan Luar</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                            <i class="fas fa-chevron-down text-gray-400"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-end items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                            <button type="button" class="btn-nav btn-lanjut w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-300" data-target-step="2" data-direction="next">
                                <span class="btn-text">Lanjut</span>
                                <i class="fas fa-arrow-right btn-icon text-xs"></i>
                            </button>
                        </div>
                    </section>
                </div>

                <div id="form-tahap-2" class="form-step inactive">
                    <section class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden">
                    

                            <div class="mb-8 animate-reveal" style="animation-delay: 0.1s;">
                                <h2 class="text-lg md:text-xl font-semibold text-gray-800 pb-3 mb-5 border-b border-gray-200">Informasi Dasar Kegiatan</h2>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                    <div class="relative">
                                        <i class="fas fa-user absolute top-3.5 left-3 text-gray-500 peer-focus:text-blue-600 pointer-events-none z-10"></i>
                                        <input required readonly type="text" id="nama_pengusul" name="nama_pengusul" 
                                            class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-600 bg-gray-100 rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer cursor-not-allowed" 
                                            placeholder=" ">
                                        <label for="nama_pengusul" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Nama Pengusul</label>
                                    </div>

                                    <div class="relative">
                                        <i class="fas fa-clipboard-list absolute top-3.5 left-3 text-gray-500 peer-focus:text-blue-600 pointer-events-none z-10"></i>
                                        <input required readonly type="text" id="nama_kegiatan_kak" name="nama_kegiatan" 
                                            class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-600 bg-gray-100 rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer cursor-not-allowed" 
                                            placeholder=" ">
                                        <label for="nama_kegiatan_kak" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Nama Kegiatan</label>
                                    </div>

                                    <div class="md:col-span-2 relative">
                                        <i class="fas fa-align-left absolute top-4 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                                        <textarea required id="gambaran_umum_kak" name="gambaran_umum" rows="5" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "></textarea>
                                        <label for="gambaran_umum_kak" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Gambaran Umum</label>
                                    </div>
                                    <div class="md:col-span-2 relative">
                                        <i class="fas fa-users absolute top-4 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                                        <textarea required id="penerima_manfaat" name="penerima_manfaat" rows="3" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "></textarea>
                                        <label for="penerima_manfaat" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Penerima Manfaat</label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-8 animate-reveal" style="animation-delay: 0.2s;">
                                <h2 class="text-lg md:text-xl font-semibold text-gray-800 pb-3 mb-5 border-b border-gray-200">Strategi Pencapaian Keluaran</h2>
                                <div class="relative">
                                    <i class="fas fa-tasks absolute top-4 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                                    <textarea required id="metode_pelaksanaan_kak" name="metode_pelaksanaan" rows="3" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" "></textarea>
                                    <label for="metode_pelaksanaan_kak" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Metode Pelaksanaan</label>
                                </div>
                                <div class="mt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tahapan Pelaksanaan</label>
                                    <div id="tahapan-container" class="space-y-3">
                                        <div class="flex items-center gap-2 md:gap-3 repeater-row-tahapan border border-transparent p-3 mb-3">
                                            <span class="tahapan-number text-gray-500 font-medium pt-3 flex-shrink-0">1.</span>
                                            <div class="relative flex-grow">
                                                <i class="fas fa-flag absolute top-3.5 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                                                <input required type="text" name="tahapan[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                                                <label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Tahap Persiapan & Koordinasi</label>
                                            </div>
                                            <button type="button" class="text-gray-400 cursor-not-allowed pt-3 flex-shrink-0" disabled><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                    <button type="button" id="tambah-tahapan" class="mt-3 bg-blue-100 text-blue-700 px-4 py-2 rounded-lg text-xs font-medium hover:bg-blue-200 transition-all duration-300 flex items-center gap-2">
                                        <i class="fas fa-plus"></i> Tambah Tahapan
                                    </button>
                                </div>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between items-center mt-12 pt-6 border-t border-gray-200 gap-4">
                                <button type="button" class="btn-kembali btn-nav w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all" data-target-step="1" data-direction="prev">
                                    <i class="fas fa-arrow-left text-xs"></i> Kembali
                                </button>
                                <button type="button" class="btn-nav btn-lanjut w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-300" data-target-step="3" data-direction="next">
                                    <span class="btn-text">Lanjut</span>
                                    <i class="fas fa-arrow-right btn-icon text-xs"></i>
                                </button>
                            </div>
                        
                    </section>
                </div>

                <div id="form-tahap-3" class="form-step inactive">
                    <div class="bg-white rounded-lg shadow-lg p-4 md:p-10">
                        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Indikator Kinerja Utama & Renstra</h2>
                        
                        <div class="animate-reveal" style="animation-delay: 0.3s;">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-800 pb-3 mb-5 border-b border-gray-200">Indikator Kinerja</h2>
                            <div class="hidden md:grid grid-cols-8 gap-4 mb-2 text-sm font-medium text-gray-500">
                                <div class="col-span-2">Bulan</div><div class="col-span-4">Indikator Keberhasilan</div><div class="col-span-1">Target (%)</div><div class="col-span-1">Aksi</div>
                            </div>
                            <div id="indikator-container" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-8 gap-4 items-start md:items-center repeater-row-indikator border md:border-transparent border-gray-100 rounded-lg p-3 md:p-0 mb-3 md:mb-0">
                                    <div class="col-span-1 md:col-span-2 select-wrapper pb-4">
                                        <label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Bulan</label>
                                        <div class="relative">
                                            <i class="fas fa-calendar-alt absolute top-1/2 -translate-y-1/2 left-3 text-gray-400 transition-colors duration-300 peer-focus-within:text-blue-600 pointer-events-none z-10"></i>
                                            <select required name="indikator_bulan[]" class="floating-select block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" onchange="this.setAttribute('filled', this.value !== '' ? 'true' : 'false')">
                                                <option value="" selected></option> <option value="1">Januari</option> <option value="2">Februari</option> <option value="3">Maret</option> <option value="4">April</option> <option value="5">Mei</option> <option value="6">Juni</option> <option value="7">Juli</option> <option value="8">Agustus</option> <option value="9">September</option> <option value="10">Oktober</option> <option value="11">November</option> <option value="12">Desember</option>
                                            </select>
                                            <label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Pilih Bulan</label>
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-4 pb-4">
                                        <label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Indikator</label>
                                        <div class="relative">
                                            <i class="fas fa-clipboard-check absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i>
                                            <input required type="text" name="indikator_nama[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                                            <label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Indikator Keberhasilan</label>
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-1 pb-4">
                                        <label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Target (%)</label>
                                        <div class="relative">
                                            <i class="fas fa-bullseye absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i>
                                            <input required type="number" min="0" max="100" name="indikator_target[]" class="block w-full px-4 py-3.5 pl-10 pr-7 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                                            <label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Target</label>
                                            <span class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-500 pointer-events-none text-sm">%</span>
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-1 text-right md:text-center mt-2 md:mt-0 pt-3 md:pt-0">
                                        <button type="button" class="text-gray-400 cursor-not-allowed" disabled><i class="fas fa-trash"></i></button>
                                    </div>
                                </div>
                            </div>
                            <button type="button" id="tambah-indikator" class="mt-4 bg-blue-500 text-white font-semibold px-4 py-2 rounded-lg shadow-sm hover:bg-blue-600 transition-all duration-300 flex items-center gap-2 text-sm">
                                <i class="fas fa-plus"></i> Tambah Indikator
                            </button>
                        </div>

                        <input type="hidden" id="indikator_kinerja_hidden" name="indikator_kinerja" value="">
                        <div class="mt-8 pt-6 border-t border-gray-200">
                            <label for="open-indicator-modal-btn" class="text-sm font-medium text-gray-700">IKU (Indikator Kinerja Utama) yang Dipilih:</label>
                            <div id="indicator-display-area" class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors">
                                <span id="indicator-tags-container" class="contents"></span>
                                <button type="button" id="open-indicator-modal-btn" class="ml-auto inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 flex-shrink-0">
                                    <i class="fas fa-plus-circle"></i> Tambah atau Ubah
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                            <button type="button" class="btn-nav btn-kembali w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all" data-target-step="2" data-direction="prev">
                                <i class="fas fa-arrow-left btn-icon"></i> <span class="btn-text">Kembali</span>
                            </button>
                            <button type="button" class="btn-nav btn-lanjut w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all" data-target-step="4" data-direction="next">
                                <span class="btn-text">Lanjut</span> <i class="fas fa-arrow-right btn-icon"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div id="form-tahap-4" class="form-step inactive">
                    <div class="bg-white rounded-lg shadow-lg p-4 md:p-10">
                        <div class="rab-header flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
                            <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex-shrink-0">Rincian Anggaran Biaya (RAB)</h2>
                            <div class="rab-actions-wrapper relative w-full sm:w-auto self-end sm:self-center">
                                <div class="rab-actions flex justify-end gap-4">
                                    <button type="button" class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-medium text-center text-white rounded-lg transition-all bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg" id="add-category-toggle-btn">
                                        <i class="fas fa-plus"></i> Tambah kategori
                                    </button>
                                </div>
                                <div class="category-popup absolute top-full right-0 mt-2 p-4 bg-white border border-gray-200 rounded-lg shadow-xl w-60 md:w-64 z-10 opacity-0 invisible -translate-y-2 transition-all" id="category-popup">
                                    <input type="text" id="new-category-name" placeholder="Tulis Kategori Baru" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                    <button type="button" class="w-full mt-2 px-4 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-300" id="create-category-btn">Create</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rab-main flex flex-col md:flex-row gap-4 md:gap-6">
                            <div class="category-sidebar flex-shrink-0 w-full md:w-60 bg-gray-50 rounded-lg p-2.5 overflow-x-auto whitespace-nowrap md:overflow-visible md:whitespace-normal" id="category-sidebar">
                                <div class="flex md:flex-col gap-2 md:gap-0">
                                </div>
                            </div>
                            <div class="rab-content flex-grow" id="rab-content">
                            </div>
                        </div>
                        
                        <input type="hidden" name="rab_data" id="rab_data_input">

                        <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                            <button type="button" class="btn-nav btn-kembali w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all" data-target-step="3" data-direction="prev">
                                <i class="fas fa-arrow-left btn-icon"></i> <span class="btn-text">Kembali</span>
                            </button>
                            <button type="submit" form="kak-form-element" class="btn-nav w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                                <i class="fas fa-check-circle btn-icon"></i> <span class="btn-text">Simpan & Ajukan</span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <div class="grand-total-container justify-between items-center bg-white p-4 md:p-6 rounded-lg shadow-lg mt-8 hidden">
                <h3 class="text-lg md:text-xl font-bold text-gray-800">Grand Total</h3>
                <span class="text-xl md:text-2xl font-bold text-blue-600" id="grand-total-display">RP 0</span>
            </div>
        </form>
<!-- AKHIR FORM -->
    </section>

    <div id="indicator-modal-backdrop" class="fixed inset-0 bg-black/60 z-[1010] hidden opacity-0 transition-opacity duration-300"></div>
     <div id="indicator-modal-content" class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-white rounded-lg shadow-xl z-[1020] w-[90%] max-w-md hidden opacity-0 scale-95 transition-all duration-300">
         <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Pilih Indikator Kinerja</h3>
            <button id="close-indicator-modal-btn" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-times text-xl"></i></button>
         </div>
         <div class="p-4">
            <input type="search" id="indicator-search-input" placeholder="Cari indikator..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div id="indicator-list-container" class="mt-4 max-h-60 overflow-y-auto modal-list pr-2">
                </div>
         </div>
         <div class="flex justify-end p-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <button id="done-indicator-modal-btn" class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all">Selesai</button>
         </div>
     </div>

</main>

<style>
/* ================================================
   STEPPER RESPONSIVENESS - MOBILE & TABLET FIX
   ================================================ */

/* Base Stepper Styling */
#stepper-container {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

#stepper-container nav {
    min-width: 100%;
}

#stepper-container ol {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    width: 100%;
    padding: 0;
    margin: 0;
}

/* ================================================
   MOBILE RESPONSIVE (< 768px)
   ================================================ */
@media (max-width: 767px) {
    /* Container */
    #stepper-container {
        padding: 1rem 0.5rem !important;
    }
    
    /* Stepper Items */
    #stepper-container ol {
        gap: 0.25rem;
        min-width: 100%;
    }
    
    #stepper-container li {
        flex: 1;
        min-width: 0;
        max-width: 25%;
    }
    
    /* Step Number Circle */
    #stepper-container span.flex.items-center.justify-center {
        width: 2.25rem !important;  /* 36px */
        height: 2.25rem !important; /* 36px */
        min-width: 2.25rem !important;
    }
    
    #stepper-container span.flex.items-center.justify-center span {
        font-size: 0.875rem !important; /* 14px */
    }
    
    /* Step Container */
    #stepper-container .flex.flex-col.items-center {
        width: 100% !important;
        min-width: 0;
        padding: 0 0.125rem;
    }
    
    /* Step Text - Main Label */
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        font-size: 0.625rem !important;  /* 10px */
        line-height: 1.1 !important;
        max-width: 100%;
        word-wrap: break-word;
        white-space: normal;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        margin-top: 0.25rem !important;
        padding: 0 0.125rem;
    }
    
    /* Step Text - Subtitle (Hide on Mobile) */
    #stepper-container .mt-2 span:last-child,
    #stepper-container .mt-3 span:last-child {
        display: none !important;
    }
    
    /* Progress Line Background */
    #stepper-container .absolute.left-0.top-6 {
        top: 1.125rem !important; /* Align with smaller circles */
        height: 2px !important;
    }
    
    /* Progress Line Fill */
    #stepper-container .stepper-line {
        top: 1.125rem !important;
        height: 2px !important;
    }
    
    /* Check Icon Size */
    #stepper-container i.fa-check {
        font-size: 0.75rem !important; /* 12px */
    }
    
    /* Ring Size Adjustment */
    #stepper-container .ring-4 {
        --tw-ring-offset-width: 0px !important;
        --tw-ring-width: 2px !important;
    }
}

/* ================================================
   SMALL MOBILE (< 375px) - iPhone SE, etc
   ================================================ */
@media (max-width: 374px) {
    #stepper-container span.flex.items-center.justify-center {
        width: 2rem !important;  /* 32px */
        height: 2rem !important; /* 32px */
        min-width: 2rem !important;
    }
    
    #stepper-container span.flex.items-center.justify-center span {
        font-size: 0.75rem !important; /* 12px */
    }
    
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        font-size: 0.5rem !important; /* 8px */
        -webkit-line-clamp: 2;
    }
    
    #stepper-container .absolute.left-0.top-6 {
        top: 1rem !important;
    }
    
    #stepper-container .stepper-line {
        top: 1rem !important;
    }
}

/* ================================================
   TABLET RESPONSIVE (768px - 1023px)
   ================================================ */
@media (min-width: 768px) and (max-width: 1023px) {
    #stepper-container {
        padding: 1.5rem 1rem !important;
    }
    
    #stepper-container ol {
        gap: 0.75rem;
    }
    
    #stepper-container span.flex.items-center.justify-center {
        width: 2.75rem !important;  /* 44px */
        height: 2.75rem !important; /* 44px */
    }
    
    #stepper-container span.flex.items-center.justify-center span {
        font-size: 1.125rem !important; /* 18px */
    }
    
    #stepper-container .flex.flex-col.items-center {
        width: 7rem !important; /* 112px */
    }
    
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        font-size: 0.75rem !important; /* 12px */
    }
    
    #stepper-container .mt-2 span:last-child,
    #stepper-container .mt-3 span:last-child {
        font-size: 0.625rem !important; /* 10px */
    }
}

/* ================================================
   DESKTOP (> 1024px) - Optimized & Responsive
   ================================================ */
@media (min-width: 1024px) {
    #stepper-container {
        padding: 2rem !important;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    
    #stepper-container ol {
        gap: 1.5rem;
        max-width: 100%;
    }
    
    #stepper-container li {
        flex: 1;
        max-width: 180px;
    }
    
    /* Step Number Circle */
    #stepper-container span.flex.items-center.justify-center {
        width: 3rem !important;  /* 48px */
        height: 3rem !important; /* 48px */
        min-width: 3rem !important;
    }
    
    #stepper-container span.flex.items-center.justify-center span {
        font-size: 1.25rem !important; /* 20px */
        font-weight: 700 !important;
    }
    
    /* Step Container */
    #stepper-container .flex.flex-col.items-center {
        width: 8rem !important; /* 128px */
        max-width: 100%;
    }
    
    /* Step Text - Main Label */
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        font-size: 0.875rem !important; /* 14px */
        line-height: 1.3 !important;
        font-weight: 600 !important;
    }
    
    /* Step Text - Subtitle */
    #stepper-container .mt-2 span:last-child,
    #stepper-container .mt-3 span:last-child {
        font-size: 0.75rem !important; /* 12px */
        line-height: 1.2 !important;
        margin-top: 0.125rem !important;
    }
    
    /* Check Icon Size */
    #stepper-container i.fa-check {
        font-size: 1.125rem !important; /* 18px */
    }
    
    /* Progress Line */
    #stepper-container .absolute.left-0.top-6 {
        top: 1.5rem !important;
        height: 6px !important;
    }
    
    #stepper-container .stepper-line {
        top: 1.5rem !important;
        height: 6px !important;
    }
}

/* ================================================
   LARGE DESKTOP (> 1440px) - Extra Space
   ================================================ */
@media (min-width: 1441px) {
    #stepper-container {
        max-width: 1400px;
    }
    
    #stepper-container ol {
        gap: 2rem;
    }
    
    #stepper-container li {
        max-width: 200px;
    }
    
    /* Slightly larger elements for big screens */
    #stepper-container span.flex.items-center.justify-center {
        width: 3.5rem !important;  /* 56px */
        height: 3.5rem !important; /* 56px */
    }
    
    #stepper-container span.flex.items-center.justify-center span {
        font-size: 1.5rem !important; /* 24px */
    }
    
    #stepper-container .flex.flex-col.items-center {
        width: 10rem !important; /* 160px */
    }
    
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        font-size: 1rem !important; /* 16px */
    }
    
    #stepper-container .mt-2 span:last-child,
    #stepper-container .mt-3 span:last-child {
        font-size: 0.875rem !important; /* 14px */
    }
}

/* ================================================
   MEDIUM DESKTOP (1024px - 1440px) - Balanced
   ================================================ */
@media (min-width: 1024px) and (max-width: 1440px) {
    #stepper-container {
        padding: 1.75rem 1.5rem !important;
    }
    
    #stepper-container ol {
        gap: 1.25rem;
    }
}

/* ================================================
   LANDSCAPE MODE FIX
   ================================================ */
@media (max-height: 500px) and (orientation: landscape) {
    #stepper-container {
        padding: 0.75rem 0.5rem !important;
    }
    
    #stepper-container span.flex.items-center.justify-center {
        width: 2rem !important;
        height: 2rem !important;
    }
    
    #stepper-container .mt-2,
    #stepper-container .mt-3 {
        margin-top: 0.25rem !important;
    }
}

/* ================================================
   ANIMATION & TRANSITIONS
   ================================================ */
#stepper-container * {
    transition: all 0.3s ease;
}

/* Prevent horizontal scroll on body */
#stepper-container::-webkit-scrollbar {
    height: 0;
    display: none;
}

/* ================================================
   FLUID SCALING FOR IN-BETWEEN SIZES
   ================================================ */
@media (min-width: 768px) and (max-width: 1023px) {
    /* Fluid font sizes using clamp */
    #stepper-container span.flex.items-center.justify-center span {
        font-size: clamp(1rem, 2vw, 1.25rem) !important;
    }
    
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        font-size: clamp(0.75rem, 1.5vw, 0.875rem) !important;
    }
}

@media (min-width: 1024px) and (max-width: 1440px) {
    /* Fluid sizing for medium desktop */
    #stepper-container span.flex.items-center.justify-center {
        width: clamp(2.75rem, 3.5vw, 3rem) !important;
        height: clamp(2.75rem, 3.5vw, 3rem) !important;
    }
    
    #stepper-container span.flex.items-center.justify-center span {
        font-size: clamp(1.125rem, 1.5vw, 1.25rem) !important;
    }
}

/* ================================================
   PREVENT LAYOUT SHIFT
   ================================================ */
#stepper-container {
    min-height: 120px;
}

@media (min-width: 1024px) {
    #stepper-container {
        min-height: 160px;
    }
}

/* ================================================
   ACCESSIBILITY - Touch Targets
   ================================================ */
@media (max-width: 767px) {
    #stepper-container a,
    #stepper-container li {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
}

/* ================================================
   DESKTOP INTERACTIVITY & HOVER STATES
   ================================================ */
@media (min-width: 1024px) {
    /* Hover effect on completed steps */
    #stepper-container li a:hover .bg-gradient-to-br {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
    }
    
    /* Hover effect on future steps */
    #stepper-container li a:hover .bg-gray-200 {
        background-color: #e5e7eb;
        transform: scale(1.02);
    }
    
    /* Smooth transitions */
    #stepper-container span.flex.items-center.justify-center {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    /* Active step pulse animation (optional) */
    @keyframes pulse-ring {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(59, 130, 246, 0);
        }
    }
    
    #stepper-container .ring-blue-500 {
        animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    /* Cursor pointer for interactive elements */
    #stepper-container a {
        cursor: pointer;
    }
    
    /* Focus visible for keyboard navigation */
    #stepper-container a:focus-visible {
        outline: 2px solid #3b82f6;
        outline-offset: 4px;
        border-radius: 0.5rem;
    }
}

/* ================================================
   FIX FOR VERY LONG TEXT
   ================================================ */
@media (max-width: 767px) {
    /* Truncate jika text terlalu panjang */
    #stepper-container .mt-2 span:first-child,
    #stepper-container .mt-3 span:first-child {
        max-height: 2.2em;
    }
}

/* ================================================
   PRINT STYLES
   ================================================ */
@media print {
    #stepper-container {
        page-break-inside: avoid;
    }
}
</style>

<?php
// Script eksternal
?>
<script src="/docutrack/public/assets/js/admin/pengajuan-usulan.js"></script>