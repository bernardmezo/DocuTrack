<?php
// File: src/views/pages/admin/rincian_kegiatan.php

$id = $kegiatan_id?? 1;
?>

<main class="main-content font-poppins p-3 sm:p-4 md:p-6 mt-4 sm:-mt-18 max-w-4xl mx-auto w-full relative z-10">

    <section class="bg-white rounded-xl sm:rounded-2xl shadow-sm border border-slate-200 overflow-hidden animate-slide-up">
        
        <div class="px-4 sm:px-6 md:px-8 py-6 sm:py-7 md:py-8 border-b border-slate-100 bg-gradient-to-br from-white to-slate-50">
            <h1 class="text-lg sm:text-xl md:text-2xl font-semibold text-slate-800 tracking-tight">Lengkapi Rincian</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                Anda sedang mengisi data untuk kegiatan: <br>
                <!-- <span class="text-blue-600 font-medium text-sm sm:text-base"><?php echo htmlspecialchars($namaKeg); ?></span> -->
            </p>
        </div>
        
        <form id="rincian-kegiatan-form" action="/docutrack/public/admin/pengajuan-kegiatan/submitrincian" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="kegiatan_id" value="<?php echo htmlspecialchars($id); ?>">

            <div class="px-4 sm:px-6 md:px-8 py-6 sm:py-7 md:py-8 space-y-6 sm:space-y-7 md:space-y-8">
                
                <!-- Penanggung Jawab Section -->
                <div class="animate-fade-in" style="animation-delay: 0.1s;">
                    <h3 class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 sm:mb-4 flex items-center gap-2">
                        <i class="fas fa-user-tie text-xs sm:text-sm"></i> 
                        <span>Penanggung Jawab</span>
                    </h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-5 md:gap-6">
                        
                        <!-- Nama Lengkap -->
                        <div class="group">
                            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-3.5 pointer-events-none">
                                    <i class="far fa-user text-sm sm:text-base text-slate-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                                </div>
                                <input type="text" id="nama_pj" name="penanggung_jawab" 
                                    placeholder="Masukkan nama lengkap..."
                                    class="block w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-700 bg-white border border-slate-200 rounded-lg sm:rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 placeholder-slate-400 transition-all duration-200 outline-none hover:border-slate-300 font-medium" 
                                    required>
                            </div>
                        </div>

                        <!-- NIM/NIP -->
                        <div class="group">
                            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                NIM / NIP <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 sm:pl-3.5 pointer-events-none">
                                    <i class="far fa-id-card text-sm sm:text-base text-slate-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                                </div>
                                <input type="text" id="nim_nip_pj" name="nim_nip_pj" 
                                    placeholder="Masukkan NIM atau NIP..."
                                    class="block w-full pl-9 sm:pl-10 pr-3 sm:pr-4 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-700 bg-white border border-slate-200 rounded-lg sm:rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 placeholder-slate-400 transition-all duration-200 outline-none hover:border-slate-300 font-mono font-medium" 
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100"></div>

                <!-- Waktu & Dokumen Section -->
                <div class="animate-fade-in" style="animation-delay: 0.2s;">
                    <h3 class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-wider mb-3 sm:mb-4 flex items-center gap-2">
                        <i class="far fa-clock text-xs sm:text-sm"></i> 
                        <span>Waktu & Dokumen</span>
                    </h3>
                    
                    <!-- Date Inputs -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5 md:gap-6 mb-5 sm:mb-6">
                        <!-- Tanggal Mulai -->
                        <div class="group">
                            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Tanggal Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input required type="text" id="tanggal_mulai" name="tanggal_mulai" 
                                    class="block w-full pl-3 sm:pl-3.5 pr-9 sm:pr-10 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-700 bg-white border border-slate-200 rounded-lg sm:rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 placeholder-slate-400 transition-all duration-200 outline-none cursor-pointer hover:border-slate-300" 
                                    placeholder="Pilih tanggal...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 sm:pr-3.5 pointer-events-none">
                                    <i class="far fa-calendar text-sm sm:text-base text-slate-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Tanggal Selesai -->
                        <div class="group">
                            <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                                Tanggal Selesai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input required type="text" id="tanggal_selesai" name="tanggal_selesai" 
                                    class="block w-full pl-3 sm:pl-3.5 pr-9 sm:pr-10 py-2.5 sm:py-3 text-xs sm:text-sm text-slate-700 bg-white border border-slate-200 rounded-lg sm:rounded-xl focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 placeholder-slate-400 transition-all duration-200 outline-none cursor-pointer hover:border-slate-300" 
                                    placeholder="Pilih tanggal...">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 sm:pr-3.5 pointer-events-none">
                                    <i class="far fa-calendar-check text-sm sm:text-base text-slate-400 group-focus-within:text-blue-500 transition-colors duration-200"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-slate-700 mb-1.5 sm:mb-2">
                            Surat Pengantar <span class="text-red-500">*</span>
                        </label>
                        <div class="relative w-full border-2 border-dashed border-slate-300 rounded-xl sm:rounded-2xl bg-slate-50 hover:bg-blue-50/50 hover:border-blue-400 transition-all duration-300 group cursor-pointer py-6 sm:py-8 md:py-10 px-4 sm:px-5 md:px-6" id="dropzone">
                            <input type="file" id="upload_surat" name="surat_pengantar" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" accept=".pdf,.doc,.docx" required>
                            
                            <div class="flex flex-col items-center justify-center gap-2 sm:gap-3 pointer-events-none">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-full bg-white border border-slate-200 shadow-sm flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cloud-upload-alt text-xl sm:text-2xl text-blue-500"></i>
                                </div>
                                <div class="text-center">
                                    <p class="text-xs sm:text-sm font-medium text-slate-700 group-hover:text-blue-600 transition-colors duration-200" id="file-label">
                                        <span class="underline decoration-blue-300 decoration-2 underline-offset-2">Klik untuk upload</span> 
                                        <span class="hidden sm:inline">atau drag file ke sini</span>
                                    </p>
                                    <p class="text-[10px] sm:text-xs text-slate-400 mt-1">Format: PDF atau DOCX (Maks. 5MB)</p>
                                </div>
                            </div>
                            
                            <div id="file-info" class="hidden absolute bottom-2 sm:bottom-3 right-2 sm:right-3 left-2 sm:left-3 bg-white/95 backdrop-blur rounded-lg border border-emerald-100 p-2 sm:p-2.5 items-center justify-center gap-1.5 sm:gap-2 shadow-md animate-fade-in-up">
                                <div class="bg-emerald-100 text-emerald-600 rounded-full p-0.5 sm:p-1 flex-shrink-0">
                                    <i class="fas fa-check text-[8px] sm:text-[10px]"></i>
                                </div>
                                <span id="file-name-display" class="text-[10px] sm:text-xs font-medium text-slate-700 truncate flex-1 min-w-0">filename.pdf</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="bg-slate-50 px-4 sm:px-6 md:px-8 py-4 sm:py-5 md:py-6 border-t border-slate-200 flex flex-col sm:flex-row-reverse justify-between items-stretch sm:items-center gap-3 sm:gap-4">
                 <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 sm:gap-2.5 bg-blue-600 text-white font-medium px-6 sm:px-8 py-2.5 sm:py-3 rounded-xl shadow-lg shadow-blue-200 hover:bg-blue-700 hover:shadow-blue-300 hover:-translate-y-0.5 active:translate-y-0 transition-all duration-300 text-sm sm:text-base">
                     <span>Simpan Data</span> 
                     <i class="fas fa-arrow-right text-xs"></i>
                 </button>
                 <a href="/docutrack/public/admin/pengajuan-kegiatan" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 text-slate-500 font-medium px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl hover:text-slate-800 hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200 transition-all duration-200 text-sm sm:text-base">
                     <i class="fas fa-arrow-left text-xs sm:hidden"></i>
                     <span>Batal</span>
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

        // Drag and drop handlers
        ['dragenter', 'dragover'].forEach(eventName => { 
            dropzone.addEventListener(eventName, (e) => { 
                e.preventDefault(); 
                dropzone.classList.add('border-blue-500', 'bg-blue-50', 'scale-[1.02]'); 
            }, false); 
        });
        
        ['dragleave', 'drop'].forEach(eventName => { 
            dropzone.addEventListener(eventName, (e) => { 
                e.preventDefault(); 
                dropzone.classList.remove('border-blue-500', 'bg-blue-50', 'scale-[1.02]'); 
            }, false); 
        });
    }

    // 2. Date Picker
    if (typeof flatpickr !== 'undefined') {
        const config = {
            altInput: true, 
            altFormat: "j F Y", 
            dateFormat: "Y-m-d", 
            allowInput: true, 
            disableMobile: "true",
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
                    title: 'text-lg sm:text-xl font-bold text-slate-800',
                    htmlContainer: 'text-xs sm:text-sm text-slate-500',
                    confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white px-5 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium shadow-lg shadow-blue-200 transition-all text-sm',
                    cancelButton: 'bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 px-5 sm:px-6 py-2 sm:py-2.5 rounded-xl font-medium mr-2 sm:mr-3 transition-all text-sm'
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
                            title: 'text-base sm:text-lg font-semibold text-slate-700',
                            htmlContainer: 'text-xs sm:text-sm text-slate-500'
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

    // 4. Add smooth reveal animations on scroll (optional enhancement)
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fade-in').forEach(el => observer.observe(el));
});
</script>

<style>
    /* Smooth Animations */
    @keyframes fadeInUp {
        from { 
            opacity: 0; 
            transform: translateY(10px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes slideUp {
        from { 
            opacity: 0; 
            transform: translateY(20px); 
        }
        to { 
            opacity: 1; 
            transform: translateY(0); 
        }
    }

    @keyframes fadeIn {
        from { 
            opacity: 0; 
        }
        to { 
            opacity: 1; 
        }
    }

    .animate-fade-in-up { 
        animation: fadeInUp 0.3s ease-out forwards; 
    }

    .animate-slide-up {
        animation: slideUp 0.5s ease-out forwards;
    }

    .animate-fade-in {
        opacity: 0;
        animation: fadeIn 0.6s ease-out forwards;
    }

    /* Responsive touch improvements */
    @media (max-width: 640px) {
        input, button, select {
            font-size: 16px; /* Prevents zoom on iOS */
        }
    }

    /* Custom scrollbar for better mobile experience */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Smooth focus transitions */
    input:focus, button:focus {
        outline: none;
    }

    /* Enhanced mobile tap feedback */
    @media (hover: none) {
        button:active {
            transform: scale(0.98);
        }
    }
</style>