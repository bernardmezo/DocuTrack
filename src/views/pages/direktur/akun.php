<?php
// File: src/views/pages/direktur/akun.php
// Pastikan $user sudah di-set dari controller
$user = $user ?? [
    'username' => $_SESSION['username'] ?? 'Mr. Direktur',
    'email' => $_SESSION['email'] ?? '',
    'role' => $_SESSION['role'] ?? 'direktur',
    'profile_image' => $_SESSION['profile_image'] ?? 'https://via.placeholder.com/150/333333/FFFFFF/?text=AT',
    'created_at' => $_SESSION['created_at'] ?? date('Y-m-d')
];

// Format tanggal bergabung
$joinDate = $user['created_at'];
$date = new DateTime($joinDate);
$formattedDate = $date->format('l, j F Y');
?>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f5f5f5;
        }
        /* Default Gradient */
        .gradient-header {
            background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
            position: relative;
            overflow: hidden;
            transition: background 0.5s ease;
            /* FIX IMAGE POSITIONING: Center focus */
            background-position: center center !important;
            background-size: cover !important;
            background-repeat: no-repeat !important;
        }
        .gradient-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }
        .gradient-header::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
            border-radius: 50%;
            z-index: 1;
        }
        .card-shadow {
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .input-field {
            transition: all 0.3s ease;
        }
        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }
        /* Calendar Styles */
        .calendar-day {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .calendar-day:hover:not(.calendar-day-disabled) {
            transform: scale(1.1);
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }
        .calendar-day-active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
        }
        .calendar-day-sunday {
            color: #ef4444;
            font-weight: 600;
        }
        .calendar-day-disabled {
            color: #d1d5db;
            background-color: #f9fafb;
        }
        .btn-save {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        .edit-icon {
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .edit-icon:hover {
            transform: rotate(90deg);
            color: #3b82f6;
        }
        .nav-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .nav-btn:hover {
            background-color: #f3f4f6;
            color: #3b82f6;
        }
        
        /* FIX: Button positioning */
        .bg-change-btn-wrapper {
            position: absolute;
            right: 2rem;
            bottom: 2rem;
            z-index: 30;
        }
        
        @media (max-width: 640px) {
            .bg-change-btn-wrapper {
                right: 1rem;
                bottom: 1rem;
            }
        }
    </style>
</head>

    <div class="min-h-screen p-4 md:p-8">
        
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <form id="profileForm" action="/docutrack/public/direktur/akun/update" method="POST" enctype="multipart/form-data" class="contents lg:col-span-2">
                    
                    <div class="lg:col-span-2">
                        
                        <div id="profileHeader" 
                             class="gradient-header rounded-3xl p-8 mb-6 relative text-white card-shadow min-h-[250px] flex flex-col justify-center"
                             style="background-image: <?php echo isset($user['header_bg']) ? $user['header_bg'] : 'none'; ?>;">
                            
                            <div class="absolute inset-0 bg-black/20 rounded-3xl z-0"></div>

                            <div class="relative z-10">
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h1 class="text-3xl font-bold mb-1 flex items-center gap-2">
                                            Hi <span class="inline-block animate-wave">👋</span>, <?php echo htmlspecialchars($user['username']); ?>
                                        </h1>
                                        <p class="text-white/80 text-sm font-medium mb-1">Role</p>
                                        <div class="inline-block bg-white/20 backdrop-blur-md px-3 py-1 rounded-lg">
                                            <p class="text-white text-sm font-semibold">
                                                <?php echo htmlspecialchars($user['role']); ?>
                                            </p>
                                        </div>
                                        <p class="mt-2 text-white/90 text-sm">
                                            <i class="far fa-calendar-alt mr-1"></i> Bergabung: <?php echo $formattedDate; ?>
                                        </p>
                                    </div>
                                    <div class="mt-4 sm:mt-0">
                                        <div class="text-right">
                                            <p class="text-5xl font-bold tracking-tight" id="currentTime">12:00</p>
                                            <p class="text-sm text-white/80 mt-1">Waktu Server</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FIX: Button dengan positioning yang lebih baik -->
                            <div class="bg-change-btn-wrapper">
                                <button type="button" onclick="document.getElementById('bgInput').click()" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-2.5 rounded-xl text-sm font-medium transition-all duration-300 flex items-center gap-2 group border border-white/10 shadow-lg">
                                    <span>Ganti Background</span>
                                    <i class="fas fa-image group-hover:scale-110 transition-transform"></i>
                                </button>
                                <input type="file" id="bgInput" name="header_bg" accept="image/*" class="hidden">
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl p-8 card-shadow">
                            
                            <div class="flex justify-center mb-8 -mt-20 relative z-20">
                                <div class="relative">
                                    <div class="w-36 h-36 rounded-full overflow-hidden border-4 border-white shadow-xl bg-gray-200">
                                        <img 
                                            id="profilePreview" 
                                            src="<?php echo htmlspecialchars($user['profile_image']); ?>" 
                                            alt="Profile" 
                                            class="w-full h-full object-cover"
                                        >
                                    </div>
                                    <label for="profileImageInput" class="absolute bottom-2 right-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full w-10 h-10 flex items-center justify-center cursor-pointer hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:scale-110 ring-4 ring-white">
                                        <i class="fas fa-camera text-sm"></i>
                                    </label>
                                    <input type="file" id="profileImageInput" name="profile_image" accept="image/*" class="hidden">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                
                                <div>
                                    <label class="block text-gray-800 font-semibold mb-2 text-sm">Username</label>
                                    <div class="relative">
                                        <input 
                                            type="text" 
                                            name="username" 
                                            id="username"
                                            value="<?php echo htmlspecialchars($user['username']); ?>" 
                                            class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 pr-10"
                                            readonly
                                        >
                                        <button type="button" onclick="toggleEdit('username')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-blue-500 transition-colors edit-icon">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-800 font-semibold mb-2 text-sm">Password</label>
                                    <div class="relative">
                                        <input 
                                            type="password" 
                                            name="password" 
                                            id="password"
                                            value="<?php echo htmlspecialchars($user['password'] ?? '123456'); ?>" 
                                            class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500 pr-20"
                                            readonly
                                        >
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-2">
                                            <button type="button" onclick="togglePasswordVisibility()" class="text-gray-400 hover:text-blue-500 transition-colors p-1" title="Lihat Password">
                                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                            </button>
                                            <button type="button" onclick="toggleEdit('password')" class="text-gray-400 hover:text-blue-500 transition-colors edit-icon p-1">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-gray-800 font-semibold mb-2 text-sm">Role</label>
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        value="<?php echo htmlspecialchars($user['role']); ?>" 
                                        class="input-field w-full px-4 py-3 border-2 border-gray-100 rounded-xl bg-gray-50 text-gray-500 font-medium cursor-not-allowed"
                                        readonly
                                    >
                                    <i class="fas fa-lock absolute right-4 top-1/2 -translate-y-1/2 text-gray-300"></i>
                                </div>
                            </div>

                            <div class="mb-8">
                                <label class="block text-gray-800 font-semibold mb-2 text-sm">Email</label>
                                <input 
                                    type="email" 
                                    name="email" 
                                    value="<?php echo htmlspecialchars($user['email']); ?>" 
                                    class="input-field w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none focus:border-blue-500"
                                    placeholder="Masukkan email Anda"
                                >
                            </div>

                            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <a href="javascript:history.back()" class="px-6 py-3 rounded-xl font-semibold text-gray-600 hover:bg-gray-100 border border-transparent hover:border-gray-200 transition-all flex items-center gap-2">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Kembali</span>
                                </a>
                                <button type="submit" class="btn-save text-white px-8 py-3 rounded-xl font-semibold shadow-lg flex items-center gap-2">
                                    <i class="fas fa-save"></i>
                                    <span>Simpan Perubahan</span>
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl p-6 card-shadow sticky top-8">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 flex flex-col">
                                    <span class="text-blue-600 text-2xl" id="currentMonth">November</span>
                                    <span id="currentYear" class="text-gray-500 text-sm font-medium">2025</span>
                                </h3>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="changeMonth(-1)" class="nav-btn bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-600">
                                    <i class="fas fa-chevron-left text-xs"></i>
                                </button>
                                <button onclick="resetToToday()" class="nav-btn bg-blue-50 text-blue-600 hover:bg-blue-100" title="Hari Ini">
                                    <i class="fas fa-calendar-day text-xs"></i>
                                </button>
                                <button onclick="changeMonth(1)" class="nav-btn bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-600">
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <div class="calendar">
                            <div class="grid grid-cols-7 gap-2 mb-2">
                                <div class="text-center text-xs font-bold text-gray-400">SEN</div>
                                <div class="text-center text-xs font-bold text-gray-400">SEL</div>
                                <div class="text-center text-xs font-bold text-gray-400">RAB</div>
                                <div class="text-center text-xs font-bold text-gray-400">KAM</div>
                                <div class="text-center text-xs font-bold text-gray-400">JUM</div>
                                <div class="text-center text-xs font-bold text-gray-400">SAB</div>
                                <div class="text-center text-xs font-bold text-red-400">MIN</div>
                            </div>
                            <div id="calendarDays" class="grid grid-cols-7 gap-2">
                                </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        // ===============================================
        // 1. JAM DIGITAL (REAL-TIME)
        // ===============================================
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('currentTime').textContent = hours + ':' + minutes;
        }
        updateClock();
        setInterval(updateClock, 1000);

        // ===============================================
        // 2. PREVIEW BACKGROUND HEADER (LOGIKA BARU - AUTO ADJUST)
        // ===============================================
        document.getElementById('bgInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const header = document.getElementById('profileHeader');
                    // Ganti background ke gambar yang dipilih
                    header.style.backgroundImage = `url('${e.target.result}')`;
                    header.style.backgroundBlendMode = 'normal';
                    
                    // FIX: Ensure it centers professionally
                    header.style.backgroundPosition = 'center center';
                    header.style.backgroundSize = 'cover';
                    header.style.backgroundRepeat = 'no-repeat';
                };
                reader.readAsDataURL(file);
            }
        });

        // ===============================================
        // 3. PREVIEW FOTO PROFIL
        // ===============================================
        document.getElementById('profileImageInput').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profilePreview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // ===============================================
        // 4. KALENDER INTERAKTIF
        // ===============================================
        let displayedDate = new Date(); // State tanggal

        function generateCalendar() {
            const year = displayedDate.getFullYear();
            const month = displayedDate.getMonth();
            
            const now = new Date();
            const today = now.getDate();
            const isCurrentMonth = (year === now.getFullYear() && month === now.getMonth());
            
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            document.getElementById('currentMonth').textContent = monthNames[month];
            document.getElementById('currentYear').textContent = year;
            
            // Logic Hari (Senin = 0 di grid kita)
            const firstDay = new Date(year, month, 1).getDay(); // 0=Sun, 1=Mon
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            
            // Konversi agar Senin jadi indeks awal (0)
            // Minggu (0) -> 6
            // Senin (1) -> 0
            const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;
            
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';
            
            // Previous Month Fillers
            const prevMonthDays = new Date(year, month, 0).getDate();
            for (let i = adjustedFirstDay - 1; i >= 0; i--) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day calendar-day-disabled text-center py-2.5 rounded-lg text-sm';
                dayDiv.textContent = prevMonthDays - i;
                calendarDays.appendChild(dayDiv);
            }
            
            // Current Month Days
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement('div');
                const checkDate = new Date(year, month, day);
                const isSunday = checkDate.getDay() === 0;
                const isToday = isCurrentMonth && day === today;
                
                let classes = 'calendar-day text-center py-2.5 rounded-lg text-sm font-medium ';
                if (isToday) {
                    classes += 'calendar-day-active';
                } else if (isSunday) {
                    classes += 'calendar-day-sunday hover:bg-red-50';
                } else {
                    classes += 'text-gray-700 hover:bg-blue-50 hover:text-blue-600';
                }

                dayDiv.className = classes;
                dayDiv.textContent = day;
                calendarDays.appendChild(dayDiv);
            }
            
            // Next Month Fillers
            const totalCells = adjustedFirstDay + daysInMonth;
            const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
            for (let i = 1; i <= remainingCells; i++) {
                const nextDiv = document.createElement('div');
                nextDiv.className = 'calendar-day calendar-day-disabled text-center py-2.5 text-sm';
                nextDiv.textContent = i;
                calendarDays.appendChild(nextDiv);
            }
        }

        function changeMonth(offset) {
            displayedDate.setMonth(displayedDate.getMonth() + offset);
            generateCalendar();
        }

        function resetToToday() {
            displayedDate = new Date();
            generateCalendar();
        }
        
        generateCalendar();

        // ===============================================
        // 5. FITUR EDIT & PASSWORD
        // ===============================================
        function toggleEdit(fieldId) {
            const field = document.getElementById(fieldId);
            if (field.hasAttribute('readonly')) {
                field.removeAttribute('readonly');
                field.focus();
                field.classList.add('border-blue-500', 'bg-blue-50/20');
                if (fieldId === 'password') {
                    field.value = '';
                    field.placeholder = 'Masukkan password baru';
                }
            } else {
                field.setAttribute('readonly', true);
                field.classList.remove('border-blue-500', 'bg-blue-50/20');
            }
        }

        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // ===============================================
        // 6. ANIMASI WAVE (LAMBAIAN TANGAN)
        // ===============================================
        const style = document.createElement('style');
        style.textContent = `
            @keyframes wave {
                0%, 100% { transform: rotate(0deg); }
                25% { transform: rotate(20deg); }
                75% { transform: rotate(-20deg); }
            }
            .animate-wave {
                animation: wave 2s ease-in-out infinite;
                display: inline-block;
                transform-origin: 70% 70%;
            }
        `;
        document.head.appendChild(style);
    </script>