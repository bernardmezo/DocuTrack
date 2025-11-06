<?php 
// Pastikan session dimulai jika Anda akan menggunakan $_SESSION
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<div id="popup-container-register" class="popup-container fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm hidden">

    <div id="register-card" class="form-card w-full max-w-md rounded-2xl bg-white/10 p-6 shadow-2xl backdrop-blur-lg md:p-10 border border-white/20">
        <h2 class="mb-8 text-center text-3xl font-bold text-white">Register</h2>

        <?php 
            // Tampilkan pesan error register jika ada
            if (isset($_SESSION['register_error'])): 
        ?>
            <div class="mb-4 rounded bg-red-100 p-3 text-center text-sm text-red-700">
                <?php 
                    echo htmlspecialchars($_SESSION['register_error']); 
                    unset($_SESSION['register_error']); // Hapus setelah ditampilkan
                ?>
            </div>
        <?php endif; ?>
        
        <?php 
            // Tampilkan pesan sukses register jika ada
            if (isset($_SESSION['register_success'])): 
        ?>
            <div class="mb-4 rounded bg-green-100 p-3 text-center text-sm text-green-700">
                <?php 
                    echo htmlspecialchars($_SESSION['register_success']); 
                    unset($_SESSION['register_success']); // Hapus setelah ditampilkan
                ?>
            </div>
        <?php endif; ?>

        <form action="/docutrack/public/register" method="POST">
            
            <div class="input-group relative mb-8">
                <input type="text" id="register-nama" name="register_nama_lengkap" required 
                       class="peer w-full border-b-2 border-white/50 bg-transparent py-2.5 text-lg text-white placeholder-transparent outline-none" 
                       placeholder="Nama Lengkap">
                <label for="register-nama" 
                       class="absolute left-0 top-2.5 text-lg text-white/70 transition-all duration-300 
                              peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-lg
                              peer-focus:top-[-20px] peer-focus:text-xs peer-focus:text-white
                              peer-[:not(:placeholder-shown)]:top-[-20px] peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-white">
                    Nama Lengkap
                </label>
            </div>
            
            <div class="input-group relative mb-8">
                <input type="email" id="register-email" name="register_email" required 
                       class="peer w-full border-b-2 border-white/50 bg-transparent py-2.5 text-lg text-white placeholder-transparent outline-none" 
                       placeholder="Email">
                <label for="register-email" 
                       class="absolute left-0 top-2.5 text-lg text-white/70 transition-all duration-300 
                              peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-lg
                              peer-focus:top-[-20px] peer-focus:text-xs peer-focus:text-white
                              peer-[:not(:placeholder-shown)]:top-[-20px] peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-white">
                    Email
                </label>
            </div>
            
            <div class="input-group relative mb-8">
                <input type="password" id="register-password" name="register_password" required 
                       class="peer w-full border-b-2 border-white/50 bg-transparent py-2.5 text-lg text-white placeholder-transparent outline-none" 
                       placeholder="Password">
                <label for="register-password" 
                       class="absolute left-0 top-2.5 text-lg text-white/70 transition-all duration-300 
                              peer-placeholder-shown:top-2.5 peer-placeholder-shown:text-lg
                              peer-focus:top-[-20px] peer-focus:text-xs peer-focus:text-white
                              peer-[:not(:placeholder-shown)]:top-[-20px] peer-[:not(:placeholder-shown)]:text-xs peer-[:not(:placeholder-shown)]:text-white">
                    Password
                </label>
            </div>

            <div class="input-group relative mb-8">
                <label for="register-role" 
                       class="absolute left-0 top-[-20px] text-xs text-white">
                    Role
                </label>
                <select id="register-role" name="register_role" required 
                        class="w-full border-b-2 border-white/50 bg-transparent py-2.5 text-lg text-white outline-none focus:border-white appearance-none">
                    <option value="admin" class="bg-gray-800 text-white">User</option>
                    <option value="ppk" class="bg-gray-800 text-white">PPK</option>
                    <option value="bendahara" class="bg-gray-800 text-white">Bendahara</option>
                    <option value="wadir" class="bg-gray-800 text-white">Wadir</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-white">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                </div>
            </div>

            <div class="checkbox-group mb-8 flex items-center text-sm text-white">
                <input type="checkbox" id="show-password-register" class="mr-2.5 h-4 w-4 rounded border-gray-300 text-accent-teal focus:ring-accent-teal">
                <label for="show-password-register">Show Password</label>
            </div>

            <!-- Bagian CAPTCHA -->
            <div class="input-group relative mb-8 text-center">
                <img 
                    src="/docutrack/public/chaptcha.php"
                    alt="kode chaptcha"
                    id="captcha-image"
                    class="mx-auto mb-2 rounded border border-gray-400 bg-white shadow-md"
                >

                <div class="flex justify-center items-center gap-2">
                    <input 
                        type="text" 
                        name="captcha_input" 
                        required
                        placeholder="Masukkan kode di atas"
                        class="w-2/3 rounded-lg border border-white/50 bg-transparent py-2 px-3 text-white placeholder-gray-300 outline-none focus:border-white text-center"
                    >

                    <button 
                        type="button"
                        id="refresh-captcha"
                        class="text-white text-xl hover:text-gray-300"
                        title="Refresh CAPTCHA">⟳</button>
                </div>
            </div>


            <button type="submit" class="btn w-full rounded-lg border-none bg-white p-4 text-lg font-semibold text-primary-dark-green transition-colors duration-300 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2 focus:ring-offset-primary-dark-green/50">
                Register
            </button>
        </form>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const refreshCaptchaBtn = document.getElementById('refresh-captcha');
    const captchaImage = document.getElementById('captcha-image');

        if (refreshCaptchaBtn && captchaImage) {
        refreshCaptchaBtn.addEventListener('click', () => {
            captchaImage.src = '/docutrack/public/chaptcha.php?' + Date.now();
        });
    }

    // --- POPUP LOGIC, SHOW PASSWORD DLL ---
    const registerPopupContainer = document.getElementById('popup-container-register');
    const registerPasswordInput = document.getElementById('register-password');
    const showRegisterPasswordCheckbox = document.getElementById('show-password-register');
    const openRegisterBtn = document.getElementById('open-register-btn');

    if (openRegisterBtn && registerPopupContainer) {
        openRegisterBtn.addEventListener('click', (event) => {
            event.preventDefault();
            registerPopupContainer.classList.remove('hidden');
        });
    }

    if (registerPopupContainer) {
        registerPopupContainer.addEventListener('click', (e) => {
            if (e.target === registerPopupContainer) {
                registerPopupContainer.classList.add('hidden');
            }
        });
    }

    if (showRegisterPasswordCheckbox && registerPasswordInput) {
        showRegisterPasswordCheckbox.addEventListener('change', () => {
            registerPasswordInput.type = showRegisterPasswordCheckbox.checked ? 'text' : 'password';
        });
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && registerPopupContainer && !registerPopupContainer.classList.contains('hidden')) {
            registerPopupContainer.classList.add('hidden');
        }
    });
});
</script>
