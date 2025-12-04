<?php
// File: src/views/pages/admin/rincian_kegiatan.php

$id = $kegiatan_id?? 1;
?>

<main class="main-content font-poppins p-6 md:p-6 -mt-18 max-w-4xl mx-auto w-full relative z-10">

    <section class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        
        <div class="px-8 py-8 border-b border-slate-100 bg-white">
            <h1 class="text-xl font-semibold text-slate-800 tracking-tight">Lengkapi Rincian</h1>
            <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                Anda sedang mengisi data untuk kegiatan: <br>
                <!-- <span class="text-blue-600 font-medium text-base"><?php echo htmlspecialchars($namaKeg); ?></span> -->
            </p>
        </div>
        
        <form id="rincian-kegiatan-form" action="/docutrack/public/admin/pengajuan-kegiatan/submitrincian" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="kegiatan_id" value="<?php echo htmlspecialchars($id); ?>">

            <div class="px-8 py-8 space-y-8">
                
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="fas fa-user-tie"></i> Penanggung Jawab
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <div class="group">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="far fa-user text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input type="text" id="nama_pj" name="penanggung_jawab" 
                                    placeholder="Masukkan nama lengkap..."
                                    class="block w-full pl-10 pr-4 py-3 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-400 transition-all outline-none hover:border-slate-300 font-medium" 
                                    required>
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-sm font-medium text-slate-700 mb-2">NIM / NIP <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                    <i class="far fa-id-card text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                                </div>
                                <input type="text" id="nim_nip_pj" name="nim_nip_pj" 
                                    placeholder="Masukkan NIM atau NIP..."
                                    class="block w-full pl-10 pr-4 py-3 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-400 transition-all outline-none hover:border-slate-300 font-mono font-medium" 
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <i class="far fa-clock"></i> Waktu & Dokumen
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="group">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Mulai <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input required type="text" id="tanggal_mulai" name="tanggal_mulai" 
                                    class="block w-full pl-3.5 pr-10 py-3 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-400 transition-all outline-none cursor-pointer hover:border-slate-300" 
                                    placeholder="Pilih tanggal...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <i class="far fa-calendar text-slate-400 group-focus-within:text-blue-500"></i>
                                </div>
                            </div>
                        </div>
                        <div class="group">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Tanggal Selesai <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input required type="text" id="tanggal_selesai" name="tanggal_selesai" 
                                    class="block w-full pl-3.5 pr-10 py-3 text-sm text-slate-700 bg-white border border-slate-200 rounded-lg focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-400 transition-all outline-none cursor-pointer hover:border-slate-300" 
                                    placeholder="Pilih tanggal...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3.5 pointer-events-none">
                                    <i class="far fa-calendar-check text-slate-400 group-focus-within:text-blue-500"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Surat Pengantar <span class="text-red-500">*</span></label>
                        <div class="relative w-full border-2 border-dashed border-slate-300 rounded-xl bg-slate-50 hover:bg-blue-50/50 hover:border-blue-400 transition-all group cursor-pointer py-10 px-6" id="dropzone">
                            <input type="file" id="upload_surat" name="surat_pengantar" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept=".pdf,.doc,.docx" required>
                            
                            <div class="flex flex-col items-center justify-center gap-3 pointer-events-none">
                                <div class="w-14 h-14 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-blue-500"></i>
                                </div>
                                <div class="text-center">
                                    <p class="text-sm font-medium text-slate-700 group-hover:text-blue-600 transition-colors" id="file-label">
                                        <span class="underline decoration-blue-300 decoration-2 underline-offset-2">Klik untuk upload</span> atau drag file ke sini
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">Format: PDF atau DOCX (Maks. 5MB)</p>
                                </div>
                            </div>
                            
                            <div id="file-info" class="hidden absolute bottom-3 right-3 left-3 bg-white/95 backdrop-blur rounded-lg border border-emerald-100 p-2.5 items-center justify-center gap-2 shadow-md animate-fade-in-up">
                                <div class="bg-emerald-100 text-emerald-600 rounded-full p-1">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                                <span id="file-name-display" class="text-xs font-medium text-slate-700 truncate max-w-[200px]">filename.pdf</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-slate-50 px-8 py-6 border-t border-slate-200 flex flex-col sm:flex-row-reverse justify-between items-center gap-4">
                 <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 bg-blue-600 text-white font-medium px-8 py-3 rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 hover:-translate-y-0.5 transition-all duration-300">
                     <span>Simpan Data</span> <i class="fas fa-arrow-right text-xs"></i>
                 </button>
                 <a href="/docutrack/public/admin/pengajuan-kegiatan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-slate-500 font-medium px-6 py-3 rounded-xl hover:text-slate-800 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 transition-all duration-200">
                     Batal
                 </a>
            </div>
        </form>
    </section>

</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. File Upload UI Logic
    const fileInput = document.getElementById('upload_surat');
    const fileInfo = document.getElementById('file-info');
    const fileNameDisplay = document.getElementById('file-name-display');
    const dropzone = document.getElementById('dropzone');
    const fileLabelDefault = document.getElementById('file-label');

    if(fileInput) {
        fileInput.addEventListener('change', (e) => {
            if (fileInput.files.length > 0) {
                const name = fileInput.files[0].name;
                fileNameDisplay.textContent = name;
                fileInfo.classList.remove('hidden');
                fileInfo.classList.add('flex');
                dropzone.classList.add('border-emerald-400', 'bg-emerald-50/20');
                dropzone.classList.remove('border-slate-300', 'bg-slate-50', 'hover:border-blue-400', 'hover:bg-blue-50/50');
                fileLabelDefault.innerHTML = `<span class="text-emerald-600 font-semibold">File siap diupload</span>`;
            }
        });
        ['dragenter', 'dragover'].forEach(eventName => { dropzone.addEventListener(eventName, (e) => { e.preventDefault(); dropzone.classList.add('border-blue-500', 'bg-blue-50'); }, false); });
        ['dragleave', 'drop'].forEach(eventName => { dropzone.addEventListener(eventName, (e) => { e.preventDefault(); dropzone.classList.remove('border-blue-500', 'bg-blue-50'); }, false); });
    }

    // 2. Date Picker
    if (typeof flatpickr !== 'undefined') {
        const config = {
            altInput: true, altFormat: "j F Y", dateFormat: "Y-m-d", allowInput: true, disableMobile: "true",
            onOpen: function(selectedDates, dateStr, instance) {
                instance.calendarContainer.classList.add("shadow-xl", "border-0", "rounded-xl", "font-poppins", "mt-2");
            }
        };
        flatpickr("#tanggal_mulai", config);
        flatpickr("#tanggal_selesai", config);
    }

    // 3. SweetAlert2 Form Submission
    const form = document.getElementById('rincian-kegiatan-form');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault(); 

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            Swal.fire({
                title: 'Konfirmasi Simpan',
                text: "Apakah data rincian kegiatan sudah sesuai?",
                icon: 'warning',
                iconColor: '#f59e0b',
                showCancelButton: true,
                reverseButtons: true,
                confirmButtonText: 'Ya, Simpan Data',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl font-poppins border border-slate-100 shadow-2xl',
                    title: 'text-xl font-bold text-slate-800',
                    htmlContainer: 'text-sm text-slate-500',
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium shadow-lg shadow-blue-200 transition-all',
                    cancelButton: 'bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-6 py-2.5 rounded-xl font-medium mr-3 transition-all'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Sedang Menyimpan...',
                        html: 'Mohon tunggu beberapa saat.',
                        timer: 1500,
                        timerProgressBar: true,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: {
                            popup: 'rounded-2xl font-poppins',
                            title: 'text-lg font-semibold text-slate-700',
                            htmlContainer: 'text-sm text-slate-500'
                        }
                    }).then((result) => {
                        if (result.dismiss === Swal.DismissReason.timer) {
                            form.submit();
                        }
                    });
                }
            });
        });
    }
});
</script>

<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up { animation: fadeInUp 0.3s ease-out forwards; }
</style>