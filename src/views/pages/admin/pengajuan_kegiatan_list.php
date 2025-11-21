<?php
// File: src/views/pages/admin/pengajuan_kegiatan.php

// Variabel $list_kegiatan diasumsikan dikirim dari AdminPengajuanKegiatanController
if (!isset($list_kegiatan)) {
    // Data dummy untuk tampilan jika controller belum mengirim
    $list_kegiatan = [
        ['id' => 1, 'nama' => 'Seminar Nasional', 'pengusul' => 'Putra (NIM), Prodi', 'status' => 'Disetujui'],
        ['id' => 2, 'nama' => 'Seminar BEM', 'pengusul' => 'Yopan (NIM), Prodi', 'status' => 'Disetujui'],
        ['id' => 3, 'nama' => 'Kulum', 'pengusul' => 'Bernadya (NIM), Prodi', 'status' => 'Disetujui'],
    ];
}
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <div class="stepper-tab-nav bg-white p-4 md:p-8 rounded-2xl shadow-lg overflow-hidden mb-8 max-w-2xl mx-auto">
        <nav aria-label="Progress">
            <ol role="list" class="relative z-0 flex justify-around w-full max-w-xl mx-auto"> 
                
                <div class="absolute left-0 right-0 top-6 w-full h-1.5 bg-gray-200 -z-10"></div> 
                
                <div id="tab-progress-line" class="stepper-line absolute left-0 top-6 w-0 h-1.5 -z-10 transition-all duration-700 ease-out bg-gradient-to-r from-blue-500 to-cyan-400 line-flow-animation"></div>

                <li class="relative tab-indicator" data-target="list">
                    <a href="#" class="group">
                        <div class="flex flex-col items-center w-32 md:w-40 text-center">
                            <span id="step-circle-1" class="flex items-center justify-center w-12 h-12 rounded-full ring-4 transition-all duration-300">
                                <span id="step-text-1" class="font-bold text-xl">1</span>
                            </span>
                            <div class="mt-2 md:mt-3">
                                <span id="step-title-1" class="block text-xs md:text-sm font-semibold">List Kegiatan</span>
                                <span id="step-subtitle-1" class="block text-[10px] md:text-xs">Aktif</span>
                            </div>
                        </div>
                    </a>
                </li>

                <li class="relative tab-indicator" data-target="form">
                    <a href="#" class="group">
                        <div class="flex flex-col items-center w-32 md:w-40 text-center">
                            <span id="step-circle-2" class="flex items-center justify-center w-12 h-12 rounded-full ring-4 transition-all duration-300">
                                <span id="step-text-2" class="font-medium text-lg">2</span>
                            </span>
                            <div class="mt-2 md:mt-3">
                                <span id="step-title-2" class="block text-xs md:text-sm font-medium">Rincian Kegiatan</span>
                                <span id="step-subtitle-2" class="block text-[10px] md:text-xs">Berikutnya</span>
                            </div>
                        </div>
                    </a>
                </li>
            </ol>
        </nav>
    </div>


    <section id="stage-list" class="stage-content bg-white p-4 md:p-7 rounded-2xl shadow-lg overflow-hidden mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 pb-5 border-b border-gray-200 gap-4">
            <h2 class="text-xl md:text-2xl font-bold text-gray-800">List Kegiatan di Setujui Verifikator</h2>
            <div class="relative w-full md:w-80">
                <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-gray-400 peer-focus-within:text-blue-600 transition-colors duration-200"></i>
                <input type="text" id="search-kegiatan-input" placeholder="Cari Nama Kegiatan..."
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
                        if (!empty($list_kegiatan)):
                            $nomor = 1;
                            foreach ($list_kegiatan as $item):
                                $status_class = 'text-green-600 bg-green-100'; // Asumsi semua disetujui
                    ?>
                                <tr class='hover:bg-gray-50 transition-colors'>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-700 font-medium'><?= $nomor++; ?>.</td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-800 font-medium'><?= htmlspecialchars($item['nama'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm text-gray-600'><?= htmlspecialchars($item['pengusul'] ?? 'N/A'); ?></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-xs font-semibold'><span class='px-3 py-1 rounded-full <?= $status_class; ?>'><?= htmlspecialchars($item['status'] ?? 'N/A'); ?></span></td>
                                    <td class='px-4 py-3 md:px-6 md:py-5 whitespace-nowrap text-sm font-medium'>
                                        <div class='flex gap-2 items-center'>
                                            <a href="/docutrack/public/admin/pengajuan-kegiatan/show/<?= $item['id']; ?>?ref=kegiatan" class='bg-blue-600 text-white px-3 py-1 md:px-4 md:py-1.5 rounded-md text-xs font-medium hover:bg-blue-700 transition-colors'>Lihat</a>
                                            <button type="button" class='bg-red-100 text-red-700 px-2 py-1 md:px-3 md:py-1.5 rounded-md text-xs font-medium hover:bg-red-200 transition-colors'><i class='fas fa-trash'></i></button>
                                        </div>
                                    </td>
                                </tr>
                    <?php
                            endforeach;
                        else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-500 italic">Belum ada kegiatan yang disetujui oleh Verifikator.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-end items-center mt-7 pt-5 border-t border-gray-100">
            <button type="button" class="btn-lanjut-form flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300">
                Lanjut <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </div>
    </section>

    <section id="stage-form" class="stage-content bg-white p-4 md:p-10 rounded-2xl shadow-lg overflow-hidden mb-8 hidden">
        <h2 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Rincian Rancangan Kegiatan</h2>
        
        <form id="rincian-kegiatan-form" action="#" method="POST"> 

            <div class="relative mb-6">
                <input required type="text" id="nama_penanggung_jawab" name="penanggung_jawab" 
                       class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                <label for="nama_penanggung_jawab" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Nama Penanggung Jawab Kegiatan</label>
            </div>
            
            <div id="pelaksana-container" class="space-y-3 mb-6">
                <div class="flex items-center gap-2 md:gap-3 repeater-row-pelaksana">
                    <div class="relative flex-grow">
                        <input required type="text" name="pelaksana[]" class="block w-full px-4 py-3.5 text-sm text-gray-800 bg-white rounded-lg border border-gray-300 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" ">
                        <label class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-4 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Nama Pelaksana Kegiatan</label>
                    </div>
                    <button type="button" class="text-gray-400 remove-row-btn-pelaksana flex-shrink-0 pt-3" disabled><i class="fas fa-trash"></i></button>
                </div>
            </div>
            <button type="button" id="tambah-pelaksana" class="mb-8 bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-blue-700 transition-all duration-300 flex items-center gap-2">
                 <i class="fas fa-plus"></i> Tambah Pelaksana
            </button>
            
            <div class="relative mb-6">
                <label for="upload_surat" class="block text-sm font-semibold text-gray-700 mb-2">Surat Pengantar (Format PDF, DOCX)</label>
                <div class="flex items-center w-full border border-gray-300 rounded-lg overflow-hidden shadow-sm focus-within:ring-2 focus-within:ring-blue-600 transition-all">
                    <input type="text" id="file_name_display" class="block w-full px-4 py-3.5 text-sm text-gray-700 bg-gray-50 pointer-events-none" placeholder="Belum ada file yang dipilih..." readonly>
                    <label for="upload_surat" class="bg-blue-600 text-white px-5 py-3.5 cursor-pointer hover:bg-blue-700 transition-colors flex-shrink-0 flex items-center gap-2 font-medium">
                        <i class="fas fa-upload"></i> Pilih File
                    </label>
                    <input type="file" id="upload_surat" name="surat_pengantar" class="hidden" accept=".pdf,.doc,.docx" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute top-3.5 left-3 text-gray-400 pointer-events-none"></i>
                    <input type="text" id="tanggal_mulai" name="tanggal_mulai" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-0 focus:border-blue-600 peer" placeholder="">
                    <label for="tanggal_mulai" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Tanggal Mulai</label>
                </div>
                <div class="relative">
                    <i class="fas fa-calendar-alt absolute top-3.5 left-3 text-gray-400 pointer-events-none"></i>
                    <input type="text" id="tanggal_selesai" name="tanggal_selesai" class="block w-full px-4 py-3.5 pl-10 text-sm text-gray-900 bg-white rounded-lg border border-gray-300 focus:ring-0 focus:border-blue-600 peer" placeholder="">
                    <label for="tanggal_selesai" class="floating-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-10 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 peer-focus:text-blue-600 pointer-events-none">Tanggal Selesai</label>
                </div>
            </div>

            <div class="flex justify-between items-center mt-10 pt-6 border-t border-gray-200">
                 <button type="button" id="back-to-list-btn" class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-gray-200 transition-all duration-300">
                     <i class="fas fa-arrow-left text-xs"></i> Kembali
                 </button>
                 <button type="submit" class="inline-flex items-center justify-center gap-2 bg-blue-600 text-white font-semibold px-5 py-2.5 rounded-lg shadow-md hover:bg-blue-700 transition-all duration-300">
                     Submit <i class="fas fa-check text-xs"></i>
                 </button>
            </div>
        </form>
    </section>

</main>

<?php
// Letakkan script eksternal di sini agar dimuat di akhir halaman, sebelum body ditutup oleh footer
// Ini adalah praktik yang lebih baik daripada meletakkan <script> di dalam <main>
?>
<script src="/docutrack/public/assets/js/admin/pengajuan-kegiatan.js"></script>