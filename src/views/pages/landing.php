<!-- Hero Section -->
    <section id="home" class="relative min-h-[950px] flex items-center pb-20 overflow-hidden">
    
    <div class="absolute inset-0 bg-[linear-gradient(225deg,#014565_0%,#014565_35%,#00FFBC_100%)] z-0"></div>

    <div class="absolute inset-0 z-[1] pointer-events-none overflow-hidden">
    <svg viewBox="0 0 1440 602" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="height: 680px; width: 100%;">
        <path d="M0 43.5103C367.157 130.01 328.504 323.596 397.293 409.51C483.28 516.903 993.514 663.545 796.711 436.478C599.907 209.411 617.381 -123.473 783.376 86.9024C949.371 297.278 1063.87 615.046 1205.49 516.787C1347.12 418.528 1316.31 571.585 1440 559.303" stroke="#18ADD8" stroke-opacity="0.5" stroke-width="40"/>
        <path d="M0 86.9024C335.012 182.902 276.268 323.437 345.058 409.51C431.044 517.101 986.223 699.737 789.42 472.25C592.617 244.762 610.09 -88.7389 776.085 122.026C942.08 332.791 1056.58 651.148 1198.2 552.707C1339.83 454.266 1309.02 607.607 1432.71 595.302" stroke="#18ADD8" stroke-opacity="0.5" stroke-width="10"/>
    </svg>
    </div>

    <div class="absolute left-0 top-0 bottom-0 w-1/2 z-[2] opacity-20 pointer-events-none">
        <div class="w-full h-full bg-gradient-to-r from-black/50 to-transparent">
            <img src="/docutrack/public/assets/images/logo/pnj.png" alt="logo-pnj" class="w-[700px] block">
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center relative z-10">
        
        <div class="text-white relative">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight drop-shadow-lg">
                Sistem<br>
                Pengajuan TOR<br>
                & Kegiatan PNJ
            </h1>
            <p class="text-lg text-gray-100 mb-8 max-w-md leading-relaxed">
                Ajukan, Pantau dan kelola dokumen TOR Anda secara online, cepat, transparant, dan efesien.
            </p>
            <button onclick="openLoginPopup()" class="group bg-gradient-to-tl from-[#3B82F6] to-[#22D3EE] text-white px-8 py-4 rounded-full font-bold shadow-[0_10px_20px_rgba(0,0,0,0.2)] transition-all duration-300 transform hover:-translate-y-1">
                Log In
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </button>
        </div>

        <div class="relative perspective-1000" style="animation-duration: 3s;">
            <div class="p-4 relative z-10">
                <img src="/docutrack/public/assets/images/icon/orang-main-laptop.png" alt="Document Management" class="w-full rounded-2xl">
            </div>

            <div class="absolute float-animation top-4 left-4 p-0 z-20">
                <img src="/docutrack/public/assets/images/icon/kiri-atas-hero.png" alt="kiri-atas-hero" class="w-[100px] block">
            </div>

            <div class="absolute float-animation top-1/2 -translate-y-1/2 -left-8 z-20">
                <img src="/docutrack/public/assets/images/icon/kiri-hero.png" alt="kiri-hero" class="w-[100px] block">
            </div>

            <div class="absolute float-animation bottom-1 left-4 z-20">
                <img src="/docutrack/public/assets/images/icon/kiri-bawah-hero.png" alt="kiri-bawah-hero" class="w-[100px] block">
            </div>

            <div class="absolute float-animation bottom-1 -right-4 z-20">
                <img src="/docutrack/public/assets/images/icon/kanan-bawah-hero.png" alt="kanan-bawah-hero" class="w-[100px] block">
            </div>

            <div class="absolute float-animation top-4 right-4 z-20">
                <img src="/docutrack/public/assets/images/icon/kanan-atas-hero.png" alt="kanan-atas-hero" class="w-[100px] block">
            </div>
            
            <!-- Status Approved - Kiri Atas (Menempel di gambar) -->
            <div class="absolute top-4 left-1/3 -translate-x-1/3 bg-[#014565] text-white rounded-2xl p-4 shadow-xl border border-white/10 z-20">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-full">
                        <i class="fas fa-file-alt text-xl text-[#00FFBC]"></i>
                    </div>
                    <div>
                        <p class="text-xs font-light text-gray-300">Status</p>
                        <p class="text-sm font-bold">Approved</p>
                    </div>
                </div>
            </div>
            
            <!-- Total Pengajuan - Kanan Bawah (Menempel di gambar) -->
            <div class="absolute bottom-32 -right-4 bg-white rounded-2xl p-4 shadow-xl z-20">
                <div class="flex items-center gap-3">
                    <div class="bg-yellow-100 p-2 rounded-full">
                        <i class="fas fa-check-circle text-yellow-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-800">Total Pengajuan</p>
                        <p class="text-xs text-gray-500">1,234 Dokumen</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <div class="absolute bottom-0 left-0 right-0 z-[5]">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-full h-auto block">
                    <path fill="#f9fafb" fill-opacity="1" d="M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,197.3C1248,171,1344,149,1392,138.7L1440,128L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
        </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-gray-50 relative min-h-[950px]">
    <div class="w-full"> 
        
        <div class="grid md:grid-cols-2 gap-12 items-center">
             <div class="relative">
                    <div class="relative z-10">
                       <svg width="572" height="646" viewBox="0 0 572 646" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M-2.77441 645.329V107.239C6.68437 94.0677 42.7863 107.239 78.6418 38.8177C107.549 -16.3436 247.53 -15.54 261.629 59.5034C269.851 103.261 318.706 143.819 337.476 156.301C443.556 226.844 541.415 152.615 541.415 282.006C541.415 376.682 377.622 622.078 274.094 522.542C164.036 416.727 71.1278 648.6 -2.77441 645.329Z" fill="url(#paint0_linear_3706_3906)"/>
                        <ellipse opacity="0.3" cx="198.86" cy="248.462" rx="148.758" ry="150.988" fill="#014565"/>
                        <g filter="url(#filter0_f_3706_3906)">
                        <ellipse cx="250.423" cy="322.961" rx="177.047" ry="62.4848" transform="rotate(-10.9396 250.423 322.961)" fill="black"/>
                        </g>
                        <defs>
                        <filter id="filter0_f_3706_3906" x="-70.3455" y="106.472" width="641.538" height="432.978" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
                        <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/>
                        <feGaussianBlur stdDeviation="73.2643" result="effect1_foregroundBlur_3706_3906"/>
                        </filter>
                        <linearGradient id="paint0_linear_3706_3906" x1="-2.77441" y1="322.483" x2="533.194" y2="322.483" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#114177"/>
                        <stop offset="0.514423" stop-color="#17A18A"/>
                        <stop offset="1" stop-color="#00ECC5"/>
                        </linearGradient>
                        </defs>
                        </svg>
                    </div>
                    
                    <!-- Floating Chat Bubbles -->
                    <div class="absolute -top-24 left-8 z-20 float-animation">
                        <div class="relative drop-shadow-xl">
                            <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-[2rem] p-6 w-48 text-center">
                                <img src="/docutrack/public/assets/images/icon/kiri-about.png" alt="Chart" class="w-24 mx-auto mb-0 drop-shadow-md">
                            </div>
                            <div class="absolute -bottom-2 left-10 w-8 h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                        </div>
                    </div>

                    <div class="absolute top-8 right-32 z-20" style="animation: float 3s ease-in-out 0.5s infinite;">
                        <div class="relative drop-shadow-xl">
                            <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-[2rem] p-6 w-48 text-center">
                                <img src="/docutrack/public/assets/images/icon/kanan-about.svg" alt="Team" class="w-28 h-auto mx-auto mb-0 drop-shadow-md">
                            </div>
                            <div class="absolute -bottom-2 left-10 w-8 h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                        </div>
                    </div>

                    <div class="absolute -top-24 left-72 z-20" style="animation: float 3s ease-in-out 1s infinite;">
                        <div class="relative drop-shadow-xl">
                            <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-[2rem] p-6 w-48 text-center">
                                <img src="/docutrack/public/assets/images/icon/tengah-about.png" alt="Document" class="w-24 mx-auto mb-0 drop-shadow-md">
                            </div>
                            <div class="absolute -bottom-2 left-10 w-8 h-8 bg-[#274B8F] rotate-45 rounded-sm -z-10"></div>
                        </div>
                    </div>

                    <!-- Laptop -->
                    <div class="absolute -bottom-32 -left-12 w-64 z-20">
                        
                    </div>
                </div>

                <!-- Content -->
                <div class="relative z-10 drop-shadow-2xl max-w-3xl mx-auto ml-8">

                    <div class="absolute top-12 -left-4 w-12 h-12 bg-[#22D3EE] rotate-45 rounded-sm -z-10"></div>

                    <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] text-white rounded-[2.5rem] p-12 relative z-10 overflow-hidden">
                        
                        <div class="absolute inset-y-0 left-0 z-0 flex items-center pointer-events-none">
                            <img src="/docutrack/public/assets/images/icon/kiri-asset.svg" alt="" class="h-full w-auto object-cover opacity-30 -ml-10"> 
                            </div>

                        <div class="absolute bottom-0 right-0 z-0 pointer-events-none">
                            <svg width="104" height="85" viewBox="0 0 104 85" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.5" d="M103.647 1.42653C91.3658 -0.812126 78.7488 -0.412175 66.634 2.59982C54.5191 5.61181 43.1838 11.1669 33.3808 18.8962C23.5778 26.6254 15.5314 36.3519 9.77617 47.4297C4.02091 58.5075 0.688438 70.6831 9.13441e-05 83.1477L26.8246 84.629C27.3028 75.9697 29.6179 67.5112 33.6161 59.8154C37.6144 52.1195 43.2043 45.3624 50.0145 39.9927C56.8248 34.6231 64.6996 30.7639 73.1159 28.6715C81.5322 26.579 90.2974 26.3012 98.8293 27.8564L103.647 1.42653Z" fill="white"/>
                            </svg>
                        </div>


                    <div class="relative z-10">
                        <h2 class="text-4xl font-bold mb-4 flex items-center gap-2">
                            Docutrack PNJ
                        </h2>
                        <p class="text-lg text-white/90 leading-relaxed font-medium">
                            DocuTrack adalah platform digital yang mempermudah pengajuan dan pelacakan ToR (Term of Reference) untuk proyek kegiatan di lingkungan kampus yang terintegrasi. Semua proses pengajuan, verifikasi, hingga persetujuan dilakukan secara online, efisien, dan transparan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="absolute left-0 right-0 top-[100px] z-0 pointer-events-none overflow-hidden">
                    <svg style="height: 750px; width: 100%;" viewBox="0 0 1440 601" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g opacity="0.4">
                    <path d="M1440.5 43.5098C1072.45 130.01 1111.2 323.596 1042.24 409.51C956.047 516.902 444.572 663.545 641.854 436.478C839.135 209.411 821.62 -123.474 655.221 86.9019C488.822 297.277 374.049 615.046 232.08 516.786C90.1106 418.527 120.994 571.585 -2.99883 559.302" stroke="#18ADD8" stroke-width="40"/>
                    <path d="M1440.5 86.9019C1104.67 182.902 1163.56 323.437 1094.6 409.51C1008.41 517.101 451.881 699.737 649.162 472.249C846.444 244.762 828.928 -88.7394 662.529 122.026C496.131 332.791 381.357 651.148 239.388 552.706C97.4192 454.265 128.302 607.606 4.30969 595.301" stroke="#18ADD8" stroke-width="10"/>
                    </g>
                    </svg>
            </div>
        </div>
    </div>
    </section>

    <!-- Features Section -->
    <section id="detail" class="relative w-full min-h-screen bg-gray-50 overflow-hidden flex items-center">
        <div class="absolute top-0 right-0 h-full w-full lg:w-1/2 z-0 pointer-events-none">
            <svg class="w-full h-full" viewBox="0 0 729 633" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M309.717 177.032C231.381 -13.2504 588.168 -6.18044 729.161 4.42348V633C729.161 615.523 713.256 564.899 649.635 502.218C570.11 423.867 453.475 403.248 394.564 455.679C335.654 508.109 -27.2368 571.144 1.63216 387.931C24.5186 242.686 387.549 366.088 309.717 177.032Z" fill="url(#paint0_linear_new)"/>
                <defs>
                    <linearGradient id="paint0_linear_new" x1="0%" y1="0%" x2="100%" y2="100%">
                        <stop offset="0%" stop-color="#14b8a6" />
                        <stop offset="50%" stop-color="#0f766e" />
                        <stop offset="100%" stop-color="#172554" />
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <div class="relative z-10 w-full h-full flex items-center">
            <div class="w-full px-6 py-16 md:px-12 lg:py-24">
                <div class="flex flex-col lg:flex-row items-center lg:items-stretch gap-12 max-w-[1400px]">
                    
                    <div class="w-full lg:w-1/2 xl:w-3/5">
                        
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            
                            <div class="relative bg-gradient-to-tl from-[#274B8F] to-[#22D3EE] text-white rounded-3xl p-8 shadow-2xl overflow-hidden group hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative z-10">
                                    <div class="w-14 h-14 mb-4 bg-white/70 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-paper-plane text-2xl text-black"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Pengajuan TOR Online</h3>
                                    <p class="text-white/95 text-sm leading-relaxed">
                                        Ajukan TOR kapan saja tanpa kertas. Cukup isi form dan upload dokumen.
                                    </p>
                                </div>
                                <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-white/10 rounded-full"></div>
                            </div>

                            <div class="relative bg-gradient-to-tl from-[#274B8F] to-[#22D3EE] text-white rounded-3xl p-8 shadow-2xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative z-10">
                                    <div class="w-14 h-14 mb-4 bg-white/70 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-search text-2xl text-black"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3">Tracking Status</h3>
                                    <p class="text-white/95 text-sm leading-relaxed">
                                        Pantau proses pengajuan TOR anda secara real-time dan transparant.
                                    </p>
                                </div>
                                <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-white/10 rounded-full"></div>
                            </div>

                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="relative bg-white border border-gray-100 rounded-3xl p-8 shadow-xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative z-10">
                                    <div class="w-14 h-14 mb-4 bg-gray-100 rounded-xl flex items-center justify-center text-gray-700">
                                        <i class="fas fa-clock text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-gray-800">Hemat Waktu</h3>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Dengan adanya sistem digital untuk pengajuan TOR dan LPJ, proses manual dapat dipangkas signifikan. Pengajuan lebih cepat tanpa tatap muka.
                                    </p>
                                </div>
                                <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-gray-50 rounded-full"></div>
                            </div>
                            <div class="relative bg-white border border-gray-100 rounded-3xl p-8 shadow-xl overflow-hidden hover:-translate-y-1 transition-transform duration-300">
                                <div class="relative z-10">
                                    <div class="w-14 h-14 mb-4 bg-gray-100 rounded-xl flex items-center justify-center text-gray-700">
                                        <i class="fas fa-database text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold mb-3 text-gray-800">Data Terstruktur</h3>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        Semua data pengajuan TOR/LPJ tersimpan rapi. Riwayat pengajuan terdokumentasi dengan baik untuk audit dan pelacakan.
                                    </p>
                                </div>
                                <div class="absolute -bottom-12 -right-12 w-40 h-40 bg-gray-50 rounded-full"></div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full lg:w-1/2 xl:w-2/5 lg:absolute lg:right-0 lg:top-1/2 lg:-translate-y-1/2 lg:pr-8 xl:pr-16 text-right flex flex-col justify-center items-end relative pt-12 lg:pt-0">
                        <h2 class="text-3xl lg:text-4xl xl:text-[45px] font-bold text-white leading-[1.2] drop-shadow-2xl relative z-10">
                            Other features &<br>advantages
                        </h2>
                    </div>

                </div>
            </div>
        </div>
    </section>

    
    <!-- Process Section -->
    <section id="proses" class="py-20 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
    
    <div class="absolute inset-0 z-0 pointer-events-none flex items-center justify-center">
        
        

             
    </div>

    <div class="relative z-10 container mx-auto px-4">
        <img src="/docutrack/public/assets/images/proses-bisnis/section-proses.svg" 
             alt="Team" 
             class="w-full h-auto mx-auto mb-0 drop-shadow-md">
    </div>

    <!-- Process Section 
    <section id="proses" class="py-20 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
        
        Background Wave Pattern
        <div class="absolute inset-0 z-0 pointer-events-none opacity-20">
            <svg class="w-full h-full" viewBox="0 0 1440 601" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1440.5 43.5098C1072.45 130.01 1111.2 323.596 1042.24 409.51C956.047 516.902 444.572 663.545 641.854 436.478C839.135 209.411 821.62 -123.474 655.221 86.9019C488.822 297.277 374.049 615.046 232.08 516.786C90.1106 418.527 120.994 571.585 -2.99883 559.302" stroke="#18ADD8" stroke-width="40"/>
            </svg>
        </div>
        
        <div class="relative z-10 container mx-auto px-4">
            
            Section Header
            <div class="text-center mb-12">
                <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    Alur Proses Pengajuan
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                    Proses pengajuan TOR yang mudah dan transparan dari awal hingga akhir
                </p>
                <p class="text-sm text-gray-500">
                    <i class="fas fa-hand-pointer mr-2"></i>
                    Scroll ke samping untuk melihat proses selanjutnya
                </p>
            </div>
            
            Horizontal Timeline Container
            <div class="relative max-w-7xl mx-auto">
                
                Horizontal Timeline Line
                <div class="absolute top-32 left-0 right-0 h-1 bg-gray-200 hidden md:block z-0">
                    <div class="timeline-line h-full bg-gradient-to-r from-[#22D3EE] to-[#274B8F]"></div>
                </div>
                
                Scrollable Container
                <div class="scroll-container custom-scrollbar overflow-x-auto pb-8">
                    <div class="flex gap-8 md:gap-12 px-4" style="min-width: max-content;">
                        
                        Step 1 - Pengaju/Pemohon
                        <div class="process-card from-left flex-shrink-0 w-80">
                            <div class="relative">
                                Timeline Dot
                                <div class="absolute left-1/2 transform -translate-x-1/2 -top-8 w-8 h-8 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full border-4 border-white shadow-lg z-10 hidden md:block"></div>
                                
                                <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 mt-4">
                                    <div class="text-center mb-4">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full flex items-center justify-center float-icon">
                                            <i class="fas fa-user-edit text-3xl text-white"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Pengaju/Pemohon</h3>
                                    <p class="text-gray-600 text-sm mb-3 text-center">(Pengelola Program/Kegiatan)</p>
                                    <div class="w-40 h-40 mx-auto mb-4">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/man-working-on-laptop-5521002-4617430.png" alt="Pengaju" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-gray-500 text-sm text-center">
                                        Mengajukan proposal kegiatan melalui sistem dengan melengkapi dokumen yang diperlukan
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        Step 2 - Verifikator
                        <div class="process-card flex-shrink-0 w-80">
                            <div class="relative">
                                <div class="absolute left-1/2 transform -translate-x-1/2 -top-8 w-8 h-8 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full border-4 border-white shadow-lg z-10 hidden md:block"></div>
                                
                                <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 mt-4">
                                    <div class="text-center mb-4">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full flex items-center justify-center float-icon" style="animation-delay: 0.2s;">
                                            <i class="fas fa-clipboard-check text-3xl text-white"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Verifikator</h3>
                                    <p class="text-gray-600 text-sm mb-3 text-center">(Admin/Sekretariat Direktorat)</p>
                                    <div class="w-40 h-40 mx-auto mb-4">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/woman-checking-document-5521012-4617440.png" alt="Verifikator" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-gray-500 text-sm text-center">
                                        Memverifikasi kelengkapan dokumen dan kesesuaian proposal dengan ketentuan yang berlaku
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        Step 3 - Kepala Direktorat
                        <div class="process-card flex-shrink-0 w-80">
                            <div class="relative">
                                <div class="absolute left-1/2 transform -translate-x-1/2 -top-8 w-8 h-8 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full border-4 border-white shadow-lg z-10 hidden md:block"></div>
                                
                                <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 mt-4">
                                    <div class="text-center mb-4">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full flex items-center justify-center float-icon" style="animation-delay: 0.4s;">
                                            <i class="fas fa-user-tie text-3xl text-white"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Kepala Direktorat</h3>
                                    <p class="text-gray-600 text-sm mb-3 text-center">(Eselon II)</p>
                                    <div class="w-40 h-40 mx-auto mb-4">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/business-meeting-5521019-4617447.png" alt="Kepala Direktorat" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-gray-500 text-sm text-center">
                                        Melakukan review dan memberikan persetujuan awal terhadap proposal kegiatan
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        Step 4 - Staff Direktur
                        <div class="process-card flex-shrink-0 w-80">
                            <div class="relative">
                                <div class="absolute left-1/2 transform -translate-x-1/2 -top-8 w-8 h-8 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full border-4 border-white shadow-lg z-10 hidden md:block"></div>
                                
                                <div class="bg-white rounded-2xl p-6 shadow-xl border border-gray-100 mt-4">
                                    <div class="text-center mb-4">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full flex items-center justify-center float-icon" style="animation-delay: 0.6s;">
                                            <i class="fas fa-users text-3xl text-white"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800 mb-2 text-center">Staff Direktur</h3>
                                    <p class="text-gray-600 text-sm mb-3 text-center">(Sekretariat Direktur)</p>
                                    <div class="w-40 h-40 mx-auto mb-4">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/team-discussion-5521024-4617452.png" alt="Staff Direktur" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-gray-500 text-sm text-center">
                                        Melakukan kajian mendalam dan memberikan rekomendasi kepada Direktur
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        Step 5 - Direktur (Final)
                        <div class="process-card flex-shrink-0 w-80">
                            <div class="relative">
                                <div class="absolute left-1/2 transform -translate-x-1/2 -top-8 w-8 h-8 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full border-4 border-white shadow-lg z-10 hidden md:block"></div>
                                
                                <div class="bg-gradient-to-br from-[#22D3EE] to-[#274B8F] text-white rounded-2xl p-6 shadow-xl mt-4">
                                    <div class="text-center mb-4">
                                        <div class="w-20 h-20 mx-auto mb-4 bg-white/20 rounded-full flex items-center justify-center float-icon" style="animation-delay: 0.8s;">
                                            <i class="fas fa-stamp text-3xl text-white"></i>
                                        </div>
                                    </div>
                                    <h3 class="text-xl font-bold mb-2 text-center">Direktur</h3>
                                    <p class="text-white/90 text-sm mb-3 text-center">(Persetujuan Akhir)</p>
                                    <div class="w-40 h-40 mx-auto mb-4">
                                        <img src="https://cdni.iconscout.com/illustration/premium/thumb/approved-document-5521030-4617458.png" alt="Direktur" class="w-full h-full object-contain">
                                    </div>
                                    <p class="text-white/80 text-sm text-center">
                                        Memberikan persetujuan final dan pengesahan terhadap proposal kegiatan
                                    </p>
                                    <div class="mt-4 text-center">
                                        <div class="inline-flex items-center gap-2 bg-white/20 rounded-full px-4 py-2">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="text-sm font-semibold">Selesai</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                
                 Navigation Arrows (Optional)
                <div class="flex justify-center gap-4 mt-8">
                    <button onclick="scrollLeft()" class="w-12 h-12 bg-white rounded-full shadow-lg flex items-center justify-center hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-left text-gray-600"></i>
                    </button>
                    <button onclick="scrollRight()" class="w-12 h-12 bg-gradient-to-br from-[#22D3EE] to-[#274B8F] rounded-full shadow-lg flex items-center justify-center hover:opacity-90 transition-opacity">
                        <i class="fas fa-chevron-right text-white"></i>
                    </button>
                </div>
            </div>
            
        </div>
        
    </section> -->

</section>