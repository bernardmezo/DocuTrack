<!-- Hero Section - Fully Responsive -->
<section id="home" class="relative min-h-screen flex items-center pb-12 sm:pb-16 md:pb-20 overflow-hidden pt-20 md:pt-0">
    
    <div class="absolute inset-0 bg-[linear-gradient(225deg,#014565_0%,#014565_35%,#00FFBC_100%)] z-0"></div>

    <!-- Background SVG - Hidden on Mobile -->
    <div class="bg-decoration absolute inset-0 z-[1] pointer-events-none overflow-hidden">
        <img src="/docutrack/public/assets/images/background/hero-sec.svg" 
            alt="Hero Background" 
            class="w-full h-full object-cover opacity-50">
    </div>

    <!-- PNJ Logo - Hidden on Mobile -->
    <div class="bg-decoration absolute left-0 top-0 bottom-0 w-1/2 z-[2] opacity-20 pointer-events-none">
    <div class="w-full h-full bg-gradient-to-r from-black/50 to-transparent flex items-start justify-start">
        <img src="/docutrack/public/assets/images/logo/pnj.png" 
            alt="logo-pnj" 
            class="w-[700px] max-w-full h-auto -ml-4 -mt-4">
    </div>
</div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">
            
            <!-- Text Content -->
            <div class="text-white text-center lg:text-left order-2 lg:order-1">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 sm:mb-6 leading-tight drop-shadow-lg">
                    Sistem<br>
                    Pengajuan TOR<br>
                    & Kegiatan PNJ
                </h1>
                <p class="text-base sm:text-lg text-gray-100 mb-6 sm:mb-8 max-w-md mx-auto lg:mx-0 leading-relaxed">
                    Ajukan, Pantau dan kelola dokumen TOR Anda secara online, cepat, transparant, dan efesien.
                </p>
                <button onclick="openLoginPopup()" class="group bg-gradient-to-tl from-[#3B82F6] to-[#22D3EE] text-white px-6 sm:px-8 py-3 sm:py-4 rounded-full font-bold shadow-[0_10px_20px_rgba(0,0,0,0.2)] transition-all duration-300 transform hover:-translate-y-1 text-sm sm:text-base">
                    Log In
                    <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>

            <!-- Image Content -->
            <div class="relative perspective-1000 order-1 lg:order-2">
                <div class="p-2 sm:p-4 relative z-10">
                    <img src="/docutrack/public/assets/images/icon/orang-main-laptop.png" 
                        alt="Document Management" 
                        class="w-full rounded-2xl max-w-md mx-auto lg:max-w-full">
                </div>

                <!-- Floating Icons - Hidden on mobile, visible on tablet+ -->
                <div class="hidden sm:block absolute float-animation top-4 left-4 z-20">
                    <img src="/docutrack/public/assets/images/icon/kiri-atas-hero.png" 
                        alt="kiri-atas-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>

                <div class="hidden md:block absolute float-animation top-1/2 -translate-y-1/2 -left-4 lg:-left-8 z-20">
                    <img src="/docutrack/public/assets/images/icon/kiri-hero.png" 
                        alt="kiri-hero" 
                        class="w-16 lg:w-[100px]">
                </div>

                <div class="hidden sm:block absolute float-animation bottom-1 left-4 z-20">
                    <img src="/docutrack/public/assets/images/icon/kiri-bawah-hero.png" 
                        alt="kiri-bawah-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>

                <div class="hidden sm:block absolute float-animation bottom-1 -right-2 lg:-right-4 z-20">
                    <img src="/docutrack/public/assets/images/icon/kanan-bawah-hero.png" 
                        alt="kanan-bawah-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>

                <div class="hidden sm:block absolute float-animation top-4 right-4 z-20">
                    <img src="/docutrack/public/assets/images/icon/kanan-atas-hero.png" 
                        alt="kanan-atas-hero" 
                        class="w-16 md:w-20 lg:w-[100px]">
                </div>
                
                <!-- Status Cards - Adjusted for mobile -->
                <div class="absolute top-4 left-1/4 -translate-x-1/4 sm:left-1/3 sm:-translate-x-1/3 bg-[#014565] text-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl border border-white/10 z-20 text-xs sm:text-sm">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="bg-white/20 p-1.5 sm:p-2 rounded-full">
                            <i class="fas fa-file-alt text-base sm:text-xl text-[#00FFBC]"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs font-light text-gray-300">Status</p>
                            <p class="text-xs sm:text-sm font-bold">Approved</p>
                        </div>
                    </div>
                </div>
                
                <div class="absolute bottom-20 sm:bottom-32 -right-2 sm:-right-4 bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 shadow-xl z-20 text-xs sm:text-sm max-w-[140px] sm:max-w-none">
                    <div class="flex items-center gap-2 sm:gap-3">
                        <div class="bg-yellow-100 p-1.5 sm:p-2 rounded-full flex-shrink-0">
                            <i class="fas fa-check-circle text-yellow-500 text-base sm:text-xl"></i>
                        </div>
                        <div>
                            <p class="text-[10px] sm:text-xs font-bold text-gray-800">Total Pengajuan</p>
                            <p class="text-[10px] sm:text-xs text-gray-500">1,234 Dokumen</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wave Bottom -->
    <div class="absolute bottom-0 left-0 right-0 z-[5]">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto block">
            <path fill="#f9fafb" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</section>

<!-- About Section - Fully Responsive with Corner-to-Corner Content -->
<section id="about" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-gray-50 relative overflow-hidden">
        <!-- Background Decoration -->
        <div class="absolute inset-0 z-0 pointer-events-none hidden md:block">
            <img src="/docutrack/public/assets/images/background/about-sec.svg" 
                alt="About Background" 
                class="w-full h-full object-cover opacity-20">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Main Laptop Image with Floating Cards -->
            <div class="relative h-[500px] sm:h-[600px] lg:h-[700px] mb-0 lg:mb-2">
                
                <!-- Top Left Card -->
                <div class="absolute sm:top[15%] lg:top-[-10%] left-[20%] z-20 float-animation ">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-7 lg:p-6 w-20 sm:w-28 lg:w-36">
                            <img src="/docutrack/public/assets/images/icon/kiri-about.png" 
                                alt="Chart" 
                                class="w-10 sm:w-14 lg:w-18 mx-auto drop-shadow-md">
                        </div>
                        <div class="absolute -bottom-1.5 sm:-bottom-2 left-4 sm:left-6 lg:left-8 w-5 sm:w-6 lg:w-8 h-5 sm:h-6 lg:h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                    </div>
                </div>

                <!-- Top Right Card -->
                <div class="absolute sm:top[15%] lg:top-[-10%] right-[20%] z-20 float-delay">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-4 lg:p-6 w-20 sm:w-28 lg:w-36">
                            <img src="/docutrack/public/assets/images/icon/kanan-about.svg" 
                                alt="Team" 
                                class="w-12 sm:w-16 lg:w-20 h-auto mx-auto drop-shadow-md">
                        </div>
                        <div class="absolute -bottom-1.5 sm:-bottom-2 left-4 sm:left-6 lg:left-8 w-5 sm:w-6 lg:w-8 h-5 sm:h-6 lg:h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                    </div>
                </div>

                <!-- Center Top Card -->
                <div class="absolute sm:top[15%] lg:top-[-10%] left-[50%] z-20 float-center">
                    <div class="relative">
                        <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-xl sm:rounded-2xl lg:rounded-3xl p-3 sm:p-4 lg:p-6 w-20 sm:w-28 lg:w-36">
                            <img src="/docutrack/public/assets/images/icon/tengah-about.png" 
                                alt="Document" 
                                class="w-10 sm:w-14 lg:w-18 mx-auto drop-shadow-md">
                        </div>
                        <div class="absolute -bottom-1.5 sm:-bottom-2 left-4 sm:left-6 lg:left-8 w-5 sm:w-6 lg:w-8 h-5 sm:h-6 lg:h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                    </div>
                </div>

                <!-- Main Laptop Image - Larger and Center positioned -->
                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-[450px] sm:max-w-[600px] lg:max-w-[800px] z-10">
                    <img src="/docutrack/public/assets/images/icon/laptop-about.svg" 
                        alt="Laptop About" 
                        class="w-full h-auto drop-shadow-2xl">
                </div>
            </div>

            <!-- Content Section -->
            <div class="relative z-10 max-w-4xl mx-auto">
                <div class="text-center p-6 sm:p-8 lg:p-12">

                    <!-- Content -->
                    <div class="relative z-10">
                        <div class="mb-4 sm:mb-6 inline-block custom-shadow">
                            <img src="/docutrack/public/assets/images/logo/docutrack-about.svg" 
                            alt="About Background" 
                            class="h-12 sm:h-16 lg:h-20">
                        </div>
                         

                        <p class="text-base sm:text-lg lg:text-xl xl:text-2xl leading-relaxed bg-[#274B8F] bg-clip-text text-transparent custom-shadow max-w-2xl mx-auto">
                            DocuTrack adalah platform digital yang mempermudah pengajuan dan pelacakan ToR (Term of Reference) untuk proyek kegiatan di lingkungan kampus yang terintegrasi. Semua proses pengajuan, verifikasi, hingga persetujuan dilakukan secara online, efisien, dan transparan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
<!-- Features Section - Fully Responsive with Better Text Spacing -->
<section id="detail" class="relative w-full min-h-screen bg-gray-50 overflow-hidden flex items-center py-12 sm:py-16 md:py-20">
    
    <!-- Background -->
    <div class="absolute top-0 right-0 h-full w-full lg:w-1/2 z-0 pointer-events-none opacity-30 lg:opacity-100">
        <img src="/docutrack/public/assets/images/background/detail-sec.svg" 
            alt="Detail Background" 
            class="w-full h-full object-cover">
    </div>

    <div class="relative z-10 w-full">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
                
                <!-- Feature Cards -->
                <div class="w-full lg:w-3/5 order-2 lg:order-1">
                    
                    <div class="grid sm:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                        
                        <div class="relative bg-gradient-to-tl from-[#274B8F] to-[#22D3EE] text-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-white/70 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-paper-plane text-xl sm:text-2xl text-black"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Pengajuan TOR Online</h3>
                                <p class="text-white/95 text-xs sm:text-sm leading-relaxed">
                                    Ajukan TOR kapan saja tanpa kertas. Cukup isi form dan upload dokumen.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-white/10 rounded-full"></div>
                        </div>

                        <div class="relative bg-gradient-to-tl from-[#274B8F] to-[#22D3EE] text-white rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-white/70 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-search text-xl sm:text-2xl text-black"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3">Tracking Status</h3>
                                <p class="text-white/95 text-xs sm:text-sm leading-relaxed">
                                    Pantau proses pengajuan TOR anda secara real-time dan transparant.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-white/10 rounded-full"></div>
                        </div>

                    </div>

                    <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                        
                        <div class="relative bg-white border border-gray-100 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-gray-100 rounded-xl flex items-center justify-center text-gray-700">
                                    <i class="fas fa-clock text-xl sm:text-2xl"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-800">Hemat Waktu</h3>
                                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                                    Dengan adanya sistem digital untuk pengajuan TOR dan LPJ, proses manual dapat dipangkas signifikan. Pengajuan lebih cepat tanpa tatap muka.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-gray-50 rounded-full"></div>
                        </div>

                        <div class="relative bg-white border border-gray-100 rounded-2xl sm:rounded-3xl p-6 sm:p-8 shadow-xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                            <div class="relative z-10">
                                <div class="w-12 h-12 sm:w-14 sm:h-14 mb-3 sm:mb-4 bg-gray-100 rounded-xl flex items-center justify-center text-gray-700">
                                    <i class="fas fa-database text-xl sm:text-2xl"></i>
                                </div>
                                <h3 class="text-lg sm:text-xl font-bold mb-2 sm:mb-3 text-gray-800">Data Terstruktur</h3>
                                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                                    Semua data pengajuan TOR/LPJ tersimpan rapi. Riwayat pengajuan terdokumentasi dengan baik untuk audit dan pelacakan.
                                </p>
                            </div>
                            <div class="absolute -bottom-8 sm:-bottom-12 -right-8 sm:-right-12 w-32 sm:w-40 h-32 sm:h-40 bg-gray-50 rounded-full"></div>
                        </div>

                    </div>
                </div>

                <!-- Title Section - Constrained within background boundary -->
                <div class="w-full lg:w-2/5 text-center lg:text-right order-1 lg:order-2">
                    <div class="lg:max-w-[90%] lg:ml-auto">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-[38px] xl:text-[42px] font-bold text-gray-800 lg:text-white leading-[1.2] drop-shadow-2xl">
                            Other features &<br>advantages
                        </h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Process Section - Fully Responsive -->
<section id="proses" class="py-12 sm:py-16 md:py-20 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <img src="/docutrack/public/assets/images/proses-bisnis/section-proses.svg" 
            alt="Process Diagram" 
            class="w-full h-auto drop-shadow-md">
    </div>

</section>