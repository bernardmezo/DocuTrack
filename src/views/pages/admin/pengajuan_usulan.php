<?php
// File: src/views/pages/admin/pengajuan_usulan.php

// Variabel $antrian_kak dan $daftar_pengusul
// sekarang DIJAMIN ada karena dikirim oleh controller.
// Blok data dummy sudah dihapus.
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <section id="queue-section" class="bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8 transition-opacity duration-500 ease-out">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <h2 class="text-xl md:text-2xl font-semibold text-gray-800 flex-shrink-0">Antrian Pengajuan KAK</h2>
            
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-kak-input" placeholder="Cari Nama Kegiatan..."
                       class="peer w-full pl-10 pr-4 py-2.5 text-sm text-gray-800 bg-gray-50 rounded-full border border-gray-300 focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition-all duration-200 shadow-sm"
                       aria-label="Cari Kegiatan">
            </div>
            </div>
        
        <div class="overflow-x-auto max-h-96 border border-gray-100 rounded-lg">
            <table class="w-full min-w-[700px]">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Kegiatan</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Pengusul</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 md:px-6 md:py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php
                        if (!empty($antrian_kak)):
                            $nomor = 1;
                            foreach ($antrian_kak as $item):
                                $status_class = match (strtolower($item['status'] ?? '')) {
                                     'disetujui' => 'text-green-600 bg-green-100',
                                     'ditolak' => 'text-red-600 bg-red-100',
                                     'revisi' => 'text-yellow-700 bg-yellow-100',
                                     default => 'text-gray-600 bg-gray-100',
                                };
                    ?>
                                <tr class='hover:bg-gray-50 transition-colors'>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700 font-medium'><?= $nomor++; ?>.</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-800 font-medium'><?= htmlspecialchars($item['nama_kegiatan'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?= htmlspecialchars($item['nama_pengusul'] ?? 'N/A'); ?> (<?= htmlspecialchars($item['nama_prodi'] ?? 'N/A'); ?>)</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold'><span class='px-3 py-1 rounded-full <?= $status_class; ?>'><?= htmlspecialchars($item['status'] ?? 'N/A'); ?></span></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium'>
                                        <div class='flex gap-2 items-center'>
                                            <button class='bg-blue-100 text-blue-700 px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-200 transition-colors'>Lihat</button>
                                            <button class='bg-red-100 text-red-700 px-2 py-1 md:px-3 md:py-1.5 rounded-md text-xs font-medium hover:bg-red-200 transition-colors'><i class='fas fa-trash'></i></button>
                                        </div>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500 italic">Belum ada data pengajuan dalam antrian.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="flex flex-col sm:flex-row justify-between items-center mt-7 pt-5 border-t border-gray-100 gap-4">
            <button class="flex items-center justify-center gap-2 bg-gray-100 text-gray-500 px-5 py-2.5 rounded-lg text-sm font-medium w-full sm:w-auto cursor-not-allowed" disabled>
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </button>
            <button type="button" id="start-form-btn" class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-300 w-full sm:w-auto">
                Lanjut Isi Form
                <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </div>
    </section>

    <section id="form-section" class="hidden transition-opacity duration-500 ease-out opacity-0">

        <div id="stepper-container" class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden mb-8">
            </div>

        <div class="form-content-wrapper relative min-h-[500px]">

            <div id="form-tahap-1" class="form-step inactive">
                <section class="bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden">
                    <form id="kak-form-element" action="#" method="POST" onsubmit="event.preventDefault(); /* Handle submit via JS */">

                        <!-- Input hidden untuk data RAB (diisi oleh JS) -->
                        <input type="hidden" id="rab_data_hidden" name="rab_data">

                        <div class="mb-8 animate-reveal" style="animation-delay: 0.1s;">
                            <h2 class="text-lg md:text-xl font-semibold text-gray-800 pb-3 mb-5 border-b border-gray-200">Informasi Dasar Kegiatan</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                                
                                <!-- [DIUBAH] Input teks menjadi dropdown -->
                                <div class="relative">
                                    <i class="fas fa-user-tie absolute top-3.5 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                                    <input required type="text" id="nama_pengusul" name="nama_pengusul" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                                    <label for="nama_pengusul" class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Nama Pengusul</label>
                                </div>
                                <!-- Akhir Perubahan -->

                                <div class="relative">
                                    <i class="fas fa-clipboard-list absolute top-3.5 left-3 text-gray-400 peer-focus:text-blue-600 pointer-events-none"></i>
                                    <input required type="text" id="nama_kegiatan_kak" name="nama_kegiatan" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
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
                            <h2 class="text-lg md:text-xl font-semibold text-gray-800 pb-3 mb-5 border-b border-gray-200">formtegi Pencapaian Keluaran</h2>
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
                                            <!-- [DIUBAH] name="indikator_nama[]" menjadi "indikator_keberhasilan[]" -->
                                            <input required type="text" name="indikator_keberhasilan[]" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                                            <label class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Indikator Keberhasilan</label>
                                        </div>
                                    </div>
                                    <div class="col-span-1 md:col-span-1 pb-4">
                                        <label class="block md:hidden text-xs font-medium text-gray-500 mb-1">Target (%)</label>
                                        <div class="relative">
                                            <i class="fas fa-bullseye absolute top-3.5 left-3 text-gray-400 transition-colors duration-300 peer-focus:text-blue-600 pointer-events-none"></i>
                                            <input required type="number" min="0" max="100" name="target_persen[]" class="block w-full px-4 py-3.5 pl-10 pr-7 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
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

                        <div class="flex flex-col sm:flex-row justify-between items-center mt-12 pt-6 border-t border-gray-200 gap-4">
                            <button type="button" id="back-to-queue-btn" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all">
                                <i class="fas fa-arrow-left text-xs"></i> Kembali ke Antrian
                            </button>
                            <button type="button" class="btn-nav btn-lanjut w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-blue-300 focus:ring-offset-2 transition-all duration-300" data-target-step="2" data-direction="next">
                                <span class="btn-text">Lanjut</span>
                                <i class="fas fa-arrow-right btn-icon text-xs"></i>
                            </button>
                        </div>
                    </form>
                </section>
            </div>

            <div id="form-tahap-2" class="form-step inactive">
                 <div class="bg-white rounded-lg shadow-lg p-4 md:p-10">
                     <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Indikator Kinerja Utama & Renstra</h2>
                     <!-- [DIUBAH] name="indikator_kinerja" menjadi "indikator_kerja_utama_renstra" -->
                     <input type="hidden" id="indikator_kinerja_hidden" name="indikator_kerja_utama_renstra" value="">
                     <label for="open-indicator-modal-btn" class="text-sm font-medium text-gray-700">Indikator yang Dipilih:</label>
                     <div id="indicator-display-area" class="mt-2 flex flex-wrap items-center gap-2 p-3 min-h-[60px] w-full text-sm text-gray-900 bg-white rounded-lg border border-gray-300 transition-colors">
                         <span id="indicator-tags-container" class="contents"></span>
                         <button type="button" id="open-indicator-modal-btn" class="ml-auto inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 flex-shrink-0">
                             <i class="fas fa-plus-circle"></i> Tambah atau Ubah
                         </button>
                     </div>
                     <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                        <button type="button" class="btn-nav btn-kembali w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all" data-target-step="1" data-direction="prev">
                            <i class="fas fa-arrow-left btn-icon"></i> <span class="btn-text">Kembali</span>
                        </button>
                        <button type="button" class="btn-nav btn-lanjut w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-all" data-target-step="3" data-direction="next">
                            <span class="btn-text">Lanjut</span> <i class="fas fa-arrow-right btn-icon"></i>
                        </button>
                     </div>
                 </div>
            </div>

            <div id="form-tahap-3" class="form-step inactive">
                 <div class="bg-white rounded-lg shadow-lg p-4 md:p-10">
                      <div class="rab-header flex flex-col sm:flex-row justify-between items-start mb-6 gap-4">
                         <h2 class="text-xl md:text-2xl font-bold text-gray-800 flex-shrink-0">Rincian Anggaran Biaya (RAB)</h2>
                         <div class="rab-actions-wrapper relative w-full sm:w-auto self-end sm:self-center">
                             <div class="rab-actions flex justify-end gap-4">
                                 <button class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 text-xs md:text-sm font-medium text-center text-white rounded-lg transition-all bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-md hover:shadow-lg" id="add-category-toggle-btn">
                                     <i class="fas fa-plus"></i> Tambah kategori
                                 </button>
                             </div>
                             <div class="category-popup absolute top-full right-0 mt-2 p-4 bg-white border border-gray-200 rounded-lg shadow-xl w-60 md:w-64 z-10 opacity-0 invisible -translate-y-2 transition-all" id="category-popup">
                                 <input type="text" id="new-category-name" placeholder="Tulis Kategori Baru" class="w-full p-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                 <button class="w-full mt-2 px-4 py-2 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-all focus:outline-none focus:ring-2 focus:ring-blue-300" id="create-category-btn">Create</button>
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
                       <div class="flex flex-col sm:flex-row justify-between items-center mt-10 pt-6 border-t border-gray-200 gap-4">
                          <button type="button" class="btn-nav btn-kembali w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-4 focus:ring-gray-100 transition-all" data-target-step="2" data-direction="prev">
                              <i class="fas fa-arrow-left btn-icon"></i> <span class="btn-text">Kembali</span>
                          </button>
                          <button type="submit" form="kak-form-element" class="btn-nav w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:ring-green-300 transition-all">
                              <i class="fas fa-check-circle btn-icon"></i> <span class="btn-text">Simpan & Ajukan</span>
                          </button>
                       </div>
                 </div>
            </div>

        </div>
        <div class="grand-total-container flex justify-between items-center bg-white p-4 md:p-6 rounded-lg shadow-lg mt-8 hidden">
            <h3 class="text-lg md:text-xl font-bold text-gray-800">Grand Total</h3>
            <span class="text-xl md:text-2xl font-bold text-blue-600" id="grand-total-display">RP 0</span>
        </div>

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

<?php
// Script eksternal akan dimuat oleh footer.php, jadi kita letakkan script spesifik halaman di sini.
// Pastikan file footer.php memuat helpers.js dan flatpickr.js
?>
<script src="/docutrack/public/assets/js/page-scripts/pengajuan-usulan.js">
</script>