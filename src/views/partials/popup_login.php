<div id="popup-container" class="popup-container fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm hidden">

    <div id="login-card" class="form-card w-full max-w-md rounded-2xl bg-white/10 p-6 shadow-2xl backdrop-blur-lg md:p-10 border border-white/20">
        <h2 class="mb-8 text-center text-3xl font-bold text-white">Log In</h2>

        <?php 
            // Tampilkan pesan error jika ada di session
            if (isset($_SESSION['login_error'])): 
        ?>
            <div class="mb-4 rounded bg-red-100 p-3 text-center text-sm text-red-700">
                <?php 
                    echo htmlspecialchars($_SESSION['login_error']); 
                    // Hapus pesan setelah ditampilkan agar tidak muncul lagi saat refresh
                    unset($_SESSION['login_error']); 
                ?>
            </div>
        <?php endif; ?>

        <form action="/docutrack/public/login" method="POST">
            
            <div class="input-group relative mb-8">
                <input type="email" id="login-email" name="login_email" required 
                       class="peer w-full border-b-2 border-white/50 bg-transparent py-2.5 text-lg text-white placeholder-transparent outline-none" 
                       placeholder="Email">
                <label for="login-email" 
                       class="absolute left-0 top-2.5 text-lg text-white/70 transition-all duration-300 
                              peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-lg
                              peer-focus:top-[-20px] peer-focus:text-xs peer-focus:text-white
                              peer-[:not(:placeholder-shown)]:top-[-20px] peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-white">
                    Email
                </label>
            </div>
            
            <div class="input-group relative mb-8">
                <input type="password" id="login-password" name="login_password" required 
                       class="peer w-full border-b-2 border-white/50 bg-transparent py-2.5 text-lg text-white placeholder-transparent outline-none" 
                       placeholder="Password">
                <label for="login-password" 
                       class="absolute left-0 top-2.5 text-lg text-white/70 transition-all duration-300 
                              peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-lg
                              peer-focus:top-[-20px] peer-focus:text-xs peer-focus:text-white
                              peer-[:not(:placeholder-shown)]:top-[-20px] peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-white">
                    Password
                </label>
            </div>

            <div class="checkbox-group mb-8 flex items-center text-sm text-white">
                <input type="checkbox" id="show-password" class="mr-2.5 h-4 w-4 rounded border-gray-300 text-accent-teal focus:ring-accent-teal">
                <label for="show-password">Show Password</label>
            </div>

            <button type="submit" class="btn w-full rounded-lg border-none bg-white p-4 text-lg font-semibold text-primary-dark-green transition-colors duration-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-primary-dark-green/50">
                Login
            </button>
        </form>
        </div>

    </div>

<script>
    // Pastikan script ini dijalankan setelah DOM siap
    document.addEventListener('DOMContentLoaded', () => {
        // --- Ambil elemen ---
        const popupContainer = document.getElementById('popup-container');
        const loginCard = document.getElementById('login-card');
        const showPasswordCheckbox = document.getElementById('show-password');
        const loginPasswordInput = document.getElementById('login-password');
        const openLoginBtn = document.getElementById('open-login-btn'); // Tombol di header

        // --- 1. MEMBUKA POPUP ---
        if (openLoginBtn && popupContainer && loginCard) {
            openLoginBtn.addEventListener('click', (event) => {
                event.preventDefault(); // Mencegah link default jika tombolnya <a>
                popupContainer.classList.remove('hidden'); 
                loginCard.classList.remove('hidden'); // Pastikan kartu login terlihat
            });
        }
        
        // --- 2. MENUTUP POPUP SAAT KLIK BACKGROUND ---
        if (popupContainer && loginCard) {
            popupContainer.addEventListener('click', (e) => {
                // Hanya tutup jika target klik adalah container background-nya, BUKAN kartu di dalamnya
                if (e.target === popupContainer) {
                    popupContainer.classList.add('hidden');
                    // Tidak perlu menyembunyikan loginCard secara eksplisit karena sudah di dalam container
                }
            });
        }

        // --- 3. LOGIKA "SHOW PASSWORD" ---
        if (showPasswordCheckbox && loginPasswordInput) {
            showPasswordCheckbox.addEventListener('change', () => {
                loginPasswordInput.type = showPasswordCheckbox.checked ? 'text' : 'password';
            });
        }
        
        // --- 4. (Opsional) Menutup Popup dengan Tombol Escape ---
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && popupContainer && !popupContainer.classList.contains('hidden')) {
                 popupContainer.classList.add('hidden');
            }
        });

    }); // Akhir DOMContentLoaded
</script>