<?php
// File: src/views/pages/wadir/akun.php
// Pastikan $user sudah di-set dari controller
$user = $user ?? [
    'username' => $_SESSION['username'] ?? 'Wakil Direktur',
    'email' => $_SESSION['email'] ?? '',
    'role' => $_SESSION['role'] ?? 'wadir',
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

        /* --- HEADER BACKGROUND WITH GRADIENT --- */
        .full-cover-header {
            position: relative; 
            overflow: hidden; 
            transition: background 0.5s ease;
            background: linear-gradient(135deg, #00B89F 0%, #00B89F 20%, #006F96 50%, #00A2A0 75%, #00BFA6 100%);
            background-size: cover !important; 
            background-position: center center !important; 
            background-repeat: no-repeat !important; 
        }

        .full-cover-header.has-custom-bg {
            background-size: cover !important; 
            background-position: center center !important; 
            background-repeat: no-repeat !important; 
        }
        
        /* PROFILE IMAGE FLOATING */
        .profile-image-container {
            position: absolute;
            top: -60px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 20;
        }
        
        .profile-image-wrapper {
            width: 120px;
            height: 120px;
            position: relative;
        }
        
        .profile-image-border {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            background: white;
        }

        .card-shadow { box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); }
        .input-field { transition: all 0.3s ease; }
        .input-field:focus { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15); }
        
        /* CALENDAR STYLES */
        .calendar-day { transition: all 0.2s ease; cursor: pointer; display: flex; align-items: center; justify-content: center; height: 100%; min-height: 46px; max-height: 52px; width: 100%; aspect-ratio: 1 / 1; font-size: 0.875rem; } 
        .calendar-day:hover:not(.calendar-day-disabled) { transform: scale(1.05); background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
        .calendar-day-active { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; font-weight: 600; box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3); }
        .calendar-day-sunday { color: #ef4444; font-weight: 600; }
        .calendar-day-disabled { color: #d1d5db; background-color: #f9fafb; }
        
        .btn-save { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); transition: all 0.3s ease; }
        .btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4); }
        .edit-icon { cursor: pointer; transition: all 0.2s ease; }
        .edit-icon:hover { transform: rotate(90deg); color: #3b82f6; }
        .nav-btn { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
        .nav-btn:hover { background-color: #f3f4f6; color: #3b82f6; }
        
        /* WAVE ANIMATION */
        @keyframes wave { 0%, 100% { transform: rotate(0deg); } 25% { transform: rotate(20deg); } 75% { transform: rotate(-20deg); } }
        .animate-wave { animation: wave 2s ease-in-out infinite; display: inline-block; transform-origin: 70% 70%; }

        /* CUSTOM GRID LAYOUT */
        @media (min-width: 768px) {
            .custom-grid { display: grid; grid-template-columns: 480px 420px; gap: 1.5rem; align-items: start; justify-content: center; }
        }
    </style>

    <div class="min-h-screen p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            
            <form id="profileForm" action="/docutrack/public/wadir/akun/update" method="POST" enctype="multipart/form-data">
                
                <?php include(DOCUTRACK_ROOT . '/src/views/partials/_profileHeader.php'); ?>

                <div class="custom-grid grid grid-cols-1 md:grid-cols-[480px_420px] gap-6 items-start justify-center">
                    
                    <div class="w-full relative">
                        <div class="profile-image-container">
                            <div class="profile-image-wrapper">
                                <div class="profile-image-border">
                                    <img id="profilePreview" src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile" class="w-full h-full object-cover">
                                </div>
                                <label for="profileImageInput" class="absolute bottom-0 right-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center cursor-pointer hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg hover:scale-110 ring-3 ring-white">
                                    <i class="fas fa-camera text-xs"></i>
                                </label>
                                <input type="file" id="profileImageInput" name="profile_image" accept="image/*" class="hidden">
                            </div>
                        </div>
                        
                        <div class="bg-white rounded-3xl p-5 pt-16 card-shadow">
                            <div class="space-y-4">
                                <!-- Username Field -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1.5 text-xs">Username</label>
                                    <div class="relative">
                                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="input-field w-full pl-3 pr-11 py-2.5 text-sm border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500" readonly>
                                        <button type="button" onclick="toggleEdit('username')" class="absolute right-0 top-0 h-full w-11 flex items-center justify-center text-gray-400 hover:text-blue-500 transition-colors edit-icon">
                                            <i class="fas fa-edit text-base"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Email Field (Read-only) -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1.5 text-xs">Email</label>
                                    <div class="relative">
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="input-field w-full pl-3 pr-11 py-2.5 text-sm border-2 border-gray-100 rounded-lg bg-gray-50 text-gray-500 font-medium cursor-not-allowed" readonly>
                                        <div class="absolute right-0 top-0 h-full w-11 flex items-center justify-center text-gray-400 pointer-events-none">
                                            <i class="fas fa-lock text-base"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Password Field (Read-only) -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1.5 text-xs">Password</label>
                                    <div class="relative">
                                        <input type="password" name="password" id="password" value="••••••••" class="input-field w-full pl-3 pr-11 py-2.5 text-sm border-2 border-gray-100 rounded-lg bg-gray-50 text-gray-500 font-medium cursor-not-allowed" readonly>
                                        <div class="absolute right-0 top-0 h-full w-11 flex items-center justify-center text-gray-400 pointer-events-none">
                                            <i class="fas fa-lock text-base"></i>
                                        </div>
                                    </div>
                                </div>

                                <!-- Role Field (Read-only) -->
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-1.5 text-xs">Role</label>
                                    <div class="relative">
                                        <input type="text" value="<?php echo htmlspecialchars($user['role']); ?>" class="input-field w-full pl-3 pr-11 py-2.5 text-sm border-2 border-gray-100 rounded-lg bg-gray-50 text-gray-500 font-medium cursor-not-allowed" readonly>
                                        <div class="absolute right-0 top-0 h-full w-11 flex items-center justify-center text-gray-400 pointer-events-none">
                                            <i class="fas fa-lock text-base"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end gap-2.5 pt-5 mt-5 border-t border-gray-100">
                                <button type="button" onclick="goBack()" class="px-5 py-2.5 rounded-lg text-sm font-semibold text-gray-600 hover:bg-gray-100 border border-transparent hover:border-gray-200 transition-all flex items-center gap-2">
                                    <i class="fas fa-arrow-left text-xs"></i> <span>Kembali</span>
                                </button>
                                <button type="submit" class="btn-save text-white px-6 py-2.5 rounded-lg text-sm font-semibold shadow-lg flex items-center gap-2">
                                    <i class="fas fa-save text-xs"></i> <span>Simpan</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="w-full">
                        <div class="bg-white rounded-3xl p-5 pt-16 card-shadow">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 flex flex-col">
                                        <span class="text-blue-600 text-xl" id="currentMonth">November</span>
                                        <span id="currentYear" class="text-gray-500 text-xs font-medium mt-0.5">2025</span>
                                    </h3>
                                </div>
                                <div class="flex gap-1.5">
                                    <button type="button" onclick="changeMonth(-1)" class="nav-btn bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-600 w-8 h-8"><i class="fas fa-chevron-left text-xs"></i></button>
                                    <button type="button" onclick="resetToToday()" class="nav-btn bg-blue-50 text-blue-600 hover:bg-blue-100 w-8 h-8" title="Hari Ini"><i class="fas fa-calendar-day text-xs"></i></button>
                                    <button type="button" onclick="changeMonth(1)" class="nav-btn bg-gray-100 text-gray-600 hover:bg-blue-100 hover:text-blue-600 w-8 h-8"><i class="fas fa-chevron-right text-xs"></i></button>
                                </div>
                            </div>
                            <div class="grid grid-cols-7 gap-1.5 mb-2">
                                <div class="text-center text-[10px] font-bold text-gray-400 py-1.5">SEN</div>
                                <div class="text-center text-[10px] font-bold text-gray-400 py-1.5">SEL</div>
                                <div class="text-center text-[10px] font-bold text-gray-400 py-1.5">RAB</div>
                                <div class="text-center text-[10px] font-bold text-gray-400 py-1.5">KAM</div>
                                <div class="text-center text-[10px] font-bold text-gray-400 py-1.5">JUM</div>
                                <div class="text-center text-[10px] font-bold text-gray-400 py-1.5">SAB</div>
                                <div class="text-center text-[10px] font-bold text-red-400 py-1.5">MIN</div>
                            </div>
                            <div id="calendarDays" class="grid grid-cols-7 gap-1.5"></div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>

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

        // Handle form submission untuk mencatat bahwa form berhasil submit
        document.getElementById('profileForm').addEventListener('submit', function() {
            // Set flag di sessionStorage bahwa form telah disubmit
            sessionStorage.setItem('formSubmitted', 'true');
        });

        // Fungsi kembali yang lebih smart
        function goBack() {
            // Cek apakah form baru saja disubmit
            if (sessionStorage.getItem('formSubmitted') === 'true') {
                // Hapus flag
                sessionStorage.removeItem('formSubmitted');
                // Skip 1 history karena ada redirect setelah submit
                window.history.go(-2);
            } else {
                // Normal back
                window.history.back();
            }
        }

        let displayedDate = new Date();
        function generateCalendar() {
            const year = displayedDate.getFullYear();
            const month = displayedDate.getMonth();
            const now = new Date();
            const today = now.getDate();
            const isCurrentMonth = (year === now.getFullYear() && month === now.getMonth());
            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            document.getElementById('currentMonth').textContent = monthNames[month];
            document.getElementById('currentYear').textContent = year;
            const firstDay = new Date(year, month, 1).getDay(); 
            const daysInMonth = new Date(year, month + 1, 0).getDate();
            const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';
            
            const prevMonthDays = new Date(year, month, 0).getDate();
            for (let i = adjustedFirstDay - 1; i >= 0; i--) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day calendar-day-disabled rounded-lg';
                dayDiv.textContent = prevMonthDays - i;
                calendarDays.appendChild(dayDiv);
            }
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement('div');
                const checkDate = new Date(year, month, day);
                const isSunday = checkDate.getDay() === 0;
                const isToday = isCurrentMonth && day === today;
                let classes = 'calendar-day font-medium rounded-lg ';
                if (isToday) classes += 'calendar-day-active';
                else if (isSunday) classes += 'calendar-day-sunday hover:bg-red-50';
                else classes += 'text-gray-700 hover:bg-blue-50 hover:text-blue-600';
                dayDiv.className = classes;
                dayDiv.textContent = day;
                calendarDays.appendChild(dayDiv);
            }
            const totalCells = adjustedFirstDay + daysInMonth;
            const remainingCells = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
            for (let i = 1; i <= remainingCells; i++) {
                const nextDiv = document.createElement('div');
                nextDiv.className = 'calendar-day calendar-day-disabled rounded-lg';
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

        function toggleEdit(fieldId) {
            const field = document.getElementById(fieldId);
            if (field.hasAttribute('readonly')) {
                field.removeAttribute('readonly'); 
                field.focus(); 
                field.classList.remove('border-gray-200');
                field.classList.add('border-blue-500', 'bg-blue-50/20');
            } else { 
                field.setAttribute('readonly', true); 
                field.classList.remove('border-blue-500', 'bg-blue-50/20');
                field.classList.add('border-gray-200');
            }
        }
    </script>
