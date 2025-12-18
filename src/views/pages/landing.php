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

                <!-- Title Section - Moved further right with better responsive control -->
                <div class="w-full lg:w-2/5 text-center lg:text-right order-1 lg:order-2 lg:pr-8 xl:pr-12">
                    <div class="lg:max-w-none lg:ml-auto lg:pl-8 xl:pl-16 2xl:pl-24">
                        <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-[38px] xl:text-[42px] 2xl:text-[46px] font-bold text-gray-800 lg:text-white leading-[1.2] drop-shadow-2xl">
                            Other features &<br>advantages
                        </h2>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- Process Section - Wind Flow Theme -->
<section id="proses" class="py-12 sm:py-16 md:py-20 lg:py-24 bg-white relative overflow-hidden">
    
    <!-- Animated Wind Background -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <!-- Decorative Circles -->
        <div class="absolute top-10 left-10 w-64 h-64 bg-gradient-to-br from-teal-100/30 to-cyan-100/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/4 right-20 w-96 h-96 bg-gradient-to-br from-blue-100/25 to-teal-100/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-20 left-1/4 w-80 h-80 bg-gradient-to-br from-cyan-100/30 to-blue-100/25 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 right-1/3 w-72 h-72 bg-gradient-to-br from-teal-100/20 to-cyan-100/30 rounded-full blur-3xl"></div>
        
        <!-- Floating Clouds -->
        <div class="cloud cloud-1 absolute w-32 h-16 bg-teal-200/15 rounded-full blur-2xl"></div>
        <div class="cloud cloud-2 absolute w-24 h-12 bg-cyan-200/12 rounded-full blur-2xl"></div>
        <div class="cloud cloud-3 absolute w-40 h-20 bg-blue-200/15 rounded-full blur-2xl"></div>
        
        <!-- Wind Lines -->
        <svg class="absolute inset-0 w-full h-full opacity-20" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="windGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#0d9488;stop-opacity:0" />
                    <stop offset="50%" style="stop-color:#0891b2;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#0e7490;stop-opacity:0" />
                </linearGradient>
            </defs>
            <path class="wind-line wind-line-1" d="M-100 100 Q 200 120, 500 100 T 1100 100" stroke="url(#windGradient)" stroke-width="2" fill="none"/>
            <path class="wind-line wind-line-2" d="M-100 300 Q 200 280, 500 300 T 1100 300" stroke="url(#windGradient)" stroke-width="2" fill="none"/>
            <path class="wind-line wind-line-3" d="M-100 500 Q 200 520, 500 500 T 1100 500" stroke="url(#windGradient)" stroke-width="2" fill="none"/>
        </svg>
        
        <!-- Decorative Dots Pattern -->
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, #0d9488 1px, transparent 1px); background-size: 30px 30px;"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Section Header -->
        <div class="text-center mb-12 sm:mb-16 md:mb-20">
            <div class="inline-block mb-4 floating">
            </div>
            <div class="inline-block mb-4">
                <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold px-8 py-4 text-transparent bg-clip-text bg-gradient-to-r from-teal-700 via-cyan-700 to-blue-700 custom-shadow">
                    Tahapan Pengajuan
                </h2>
            </div>
        </div>

        <!-- Flowing Path Container -->
        <div class="relative">
            
            <!-- Curved SVG Path for Desktop -->
            <svg class="hidden lg:block absolute inset-0 w-full h-full pointer-events-none" style="height: 2800px;">
                <defs>
                    <linearGradient id="pathGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" style="stop-color:#0d9488;stop-opacity:0.4" />
                        <stop offset="50%" style="stop-color:#0e7490;stop-opacity:0.6" />
                        <stop offset="100%" style="stop-color:#0891b2;stop-opacity:0.4" />
                    </linearGradient>
                </defs>
                <!-- Flowing S-curve path -->
                <path class="flowing-path" 
                      d="M 100 50 
                         Q 300 150, 500 250 
                         Q 700 350, 500 450 
                         Q 300 550, 500 650
                         Q 700 750, 500 850
                         Q 300 950, 500 1050
                         Q 700 1150, 500 1250
                         Q 300 1350, 500 1450
                         Q 700 1550, 500 1650
                         Q 300 1750, 500 1850
                         Q 700 1950, 500 2050
                         Q 300 2150, 500 2250
                         Q 700 2350, 500 2450
                         Q 300 2550, 400 2650" 
                      stroke="url(#pathGradient)" 
                      stroke-width="3" 
                      fill="none" 
                      stroke-dasharray="10 5"
                      opacity="0.5"/>
            </svg>

            <!-- Mobile Curved Path -->
            <div class="lg:hidden absolute left-12 top-0 bottom-0 w-0.5">
                <div class="w-full h-full bg-gradient-to-b from-teal-400 via-cyan-500 to-blue-500 opacity-40 rounded-full"></div>
            </div>

            <!-- Process Steps with Wind Flow Animation -->
            <div class="space-y-8 sm:space-y-12 md:space-y-16 lg:space-y-24">
                
                <!-- Step 1 - Pengajuan Kegiatan -->
                <div class="relative lg:ml-0" style="animation-delay: 0.1s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-teal-600 to-teal-700 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    1
                                </div>
                                <div class="absolute inset-0 bg-teal-500/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-teal-500/30 to-teal-600/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-teal-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-600 to-teal-700 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-paper-plane text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pengajuan Kegiatan</h3>
                                            <span class="px-3 py-1 bg-teal-50 rounded-full text-teal-700 text-xs font-semibold border border-teal-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul membuat dan mengajukan proposal kegiatan melalui sistem secara online
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2 - Verifikasi -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.2s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-teal-700 to-cyan-700 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    2
                                </div>
                                <div class="absolute inset-0 bg-teal-600/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-teal-600/30 to-cyan-700/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-teal-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-700 to-cyan-700 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-check-double text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Verifikasi Pengajuan</h3>
                                            <span class="px-3 py-1 bg-teal-50 rounded-full text-teal-700 text-xs font-semibold border border-teal-200">
                                                <i class="fas fa-user-check mr-1"></i>Verifikator
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Verifikator memeriksa kelengkapan dan keabsahan dokumen pengajuan dengan teliti
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3 - Komitmen PPK -->
                <div class="relative lg:ml-0" style="animation-delay: 0.3s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-cyan-700 to-cyan-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    3
                                </div>
                                <div class="absolute inset-0 bg-cyan-600/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-cyan-600/30 to-cyan-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-cyan-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-700 to-cyan-800 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-handshake text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pembuatan Komitmen</h3>
                                            <span class="px-3 py-1 bg-cyan-50 rounded-full text-cyan-700 text-xs font-semibold border border-cyan-200">
                                                <i class="fas fa-user-tie mr-1"></i>PPK
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pejabat Pembuat Komitmen membuat komitmen anggaran untuk mendukung kegiatan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4 - Persetujuan Wadir -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.4s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-cyan-800 to-sky-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    4
                                </div>
                                <div class="absolute inset-0 bg-cyan-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-cyan-700/30 to-sky-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-cyan-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-cyan-800 to-sky-800 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-stamp text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Persetujuan Final</h3>
                                            <span class="px-3 py-1 bg-cyan-50 rounded-full text-cyan-700 text-xs font-semibold border border-cyan-200">
                                                <i class="fas fa-user-shield mr-1"></i>Wakil Direktur
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Wakil Direktur memberikan persetujuan akhir untuk pelaksanaan kegiatan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5 - Penyiapan Dana -->
                <div class="relative lg:ml-0" style="animation-delay: 0.5s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-sky-700 to-blue-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    5
                                </div>
                                <div class="absolute inset-0 bg-sky-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-sky-700/30 to-blue-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-sky-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-sky-700 to-blue-800 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-money-check-alt text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Penyiapan Dana</h3>
                                            <span class="px-3 py-1 bg-sky-50 rounded-full text-sky-700 text-xs font-semibold border border-sky-200">
                                                <i class="fas fa-wallet mr-1"></i>Bendahara
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Bendahara memproses dan menyiapkan dana sesuai anggaran yang telah disetujui
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 6 - Pelaksanaan Kegiatan -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.6s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-blue-700 to-blue-900 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    6
                                </div>
                                <div class="absolute inset-0 bg-blue-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-blue-700/30 to-blue-900/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-blue-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-700 to-blue-900 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-tasks text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pelaksanaan Kegiatan</h3>
                                            <span class="px-3 py-1 bg-blue-50 rounded-full text-blue-700 text-xs font-semibold border border-blue-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul menerima dana dan melaksanakan kegiatan sesuai dengan rencana yang disetujui
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 7 - Pembuatan LPJ -->
                <div class="relative lg:ml-0" style="animation-delay: 0.7s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-blue-800 to-indigo-900 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    7
                                </div>
                                <div class="absolute inset-0 bg-blue-800/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-blue-800/30 to-indigo-900/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-blue-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-blue-800 to-indigo-900 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-file-invoice text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pembuatan LPJ</h3>
                                            <span class="px-3 py-1 bg-blue-50 rounded-full text-blue-700 text-xs font-semibold border border-blue-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul menyusun laporan pertanggungjawaban atas kegiatan yang telah dilaksanakan
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 8 - Verifikasi LPJ -->
                <div class="relative lg:ml-auto lg:mr-0 lg:max-w-3xl" style="animation-delay: 0.8s;">
                    <div class="flex items-start gap-4 lg:gap-6 flex-row-reverse lg:flex-row">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-indigo-800 to-cyan-900 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    8
                                </div>
                                <div class="absolute inset-0 bg-indigo-800/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-indigo-800/30 to-cyan-900/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-indigo-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-indigo-800 to-cyan-900 rounded-2xl flex items-center justify-center shadow-lg order-2 sm:order-1">
                                        <i class="fas fa-clipboard-check text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1 order-1 sm:order-2">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Pemeriksaan LPJ</h3>
                                            <span class="px-3 py-1 bg-indigo-50 rounded-full text-indigo-700 text-xs font-semibold border border-indigo-200">
                                                <i class="fas fa-wallet mr-1"></i>Bendahara
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Bendahara memeriksa kelengkapan dan kesesuaian LPJ dengan realisasi anggaran
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 9 - Penyerahan Hard Copy -->
                <div class="relative lg:ml-0" style="animation-delay: 0.9s;">
                    <div class="flex items-start gap-4 lg:gap-6">
                        <div class="flex-shrink-0 wind-card-float">
                            <div class="relative">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 lg:w-20 lg:h-20 bg-gradient-to-br from-teal-700 to-cyan-800 rounded-full flex items-center justify-center text-white font-bold text-xl sm:text-2xl shadow-xl z-10 relative">
                                    9
                                </div>
                                <div class="absolute inset-0 bg-teal-700/40 rounded-full animate-ping-slow"></div>
                                <div class="absolute -inset-2 bg-gradient-to-r from-teal-700/30 to-cyan-800/30 rounded-full blur-md"></div>
                            </div>
                        </div>

                        <div class="flex-1 wind-card-float-content">
                            <div class="bg-white rounded-3xl p-5 sm:p-6 lg:p-8 shadow-xl border border-teal-100 hover:shadow-2xl transition-all duration-500 hover:-translate-y-2">
                                <div class="flex flex-col sm:flex-row items-start gap-4">
                                    <div class="flex-shrink-0 w-14 h-14 sm:w-16 sm:h-16 bg-gradient-to-br from-teal-700 to-cyan-800 rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-box-open text-white text-xl sm:text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-800">Penyerahan Dokumen</h3>
                                            <span class="px-3 py-1 bg-teal-50 rounded-full text-teal-700 text-xs font-semibold border border-teal-200">
                                                <i class="far fa-user mr-1"></i>Pengusul
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                                            Pengusul menyerahkan hard copy LPJ sebagai dokumentasi dan arsip fisik
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Completion Badge with Wind Effect -->
            <div class="mt-16 sm:mt-20 lg:mt-24 flex justify-center">
                <div class="relative floating">
                    <div class="inline-flex items-center gap-3 sm:gap-4 px-6 sm:px-8 lg:px-10 py-4 sm:py-5 lg:py-6 bg-gradient-to-r from-teal-700 via-cyan-700 to-teal-700 rounded-3xl shadow-2xl text-white">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 lg:w-16 lg:h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-check-circle text-2xl sm:text-3xl lg:text-4xl"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm font-medium opacity-90">Proses Selesai</p>
                            <p class="text-base sm:text-lg lg:text-xl font-bold">Kegiatan Tuntas</p>
                        </div>
                    </div>
                    <!-- Glow effect -->
                    <div class="absolute inset-0 bg-gradient-to-r from-teal-700 via-cyan-700 to-teal-700 rounded-3xl blur-xl opacity-50 -z-10"></div>
                </div>
            </div>

        </div>

    </div>
</section>

<style>
/* Custom Shadow for Title */
.custom-shadow {
    filter: drop-shadow(0 6px 25px rgba(34, 211, 238, 0.7)) 
            drop-shadow(0 12px 45px rgba(39, 75, 143, 0.6))
            drop-shadow(0 2px 15px rgba(51, 171, 160, 0.5));
}

/* Wind Flow Animations */
@keyframes cloudFloat {
    0%, 100% {
        transform: translateX(-100px) translateY(0);
    }
    50% {
        transform: translateX(calc(100vw + 100px)) translateY(-20px);
    }
}

@keyframes windFlow {
    0% {
        stroke-dashoffset: 1000;
        opacity: 0;
    }
    50% {
        opacity: 1;
    }
    100% {
        stroke-dashoffset: 0;
        opacity: 0;
    }
}

@keyframes floating {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

@keyframes ping-slow {
    0% {
        transform: scale(1);
        opacity: 1;
    }
    75%, 100% {
        transform: scale(2);
        opacity: 0;
    }
}

/* Cloud animations */
.cloud-1 {
    top: 10%;
    animation: cloudFloat 40s infinite linear;
}

.cloud-2 {
    top: 30%;
    animation: cloudFloat 50s infinite linear 5s;
}

.cloud-3 {
    top: 60%;
    animation: cloudFloat 45s infinite linear 10s;
}

/* Wind line animations */
.wind-line-1 {
    animation: windFlow 8s infinite ease-in-out;
}

.wind-line-2 {
    animation: windFlow 8s infinite ease-in-out 2s;
}

.wind-line-3 {
    animation: windFlow 8s infinite ease-in-out 4s;
}

/* Flowing path animation */
.flowing-path {
    stroke-dasharray: 20 10;
    animation: dashFlow 3s linear infinite;
}

@keyframes dashFlow {
    to {
        stroke-dashoffset: -30;
    }
}

/* Card float animations */
.wind-card-float {
    animation: floating 3s ease-in-out infinite;
}

.wind-card-float-content {
    animation: floating 3s ease-in-out infinite 0.2s;
}

.floating {
    animation: floating 4s ease-in-out infinite;
}

/* Ping animation */
.animate-ping-slow {
    animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 500ms;
}

/* Hover effects */
.group:hover .group-hover\:translate-x-1 {
    transform: translateX(0.25rem);
}

.group:hover .group-hover\:-translate-y-1 {
    transform: translateY(-0.25rem);
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .wind-card-float,
    .wind-card-float-content {
        animation: none;
    }
}
</style>