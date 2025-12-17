    </main>
</div>

<!-- Vendor JS -->
<script src="/docutrack/public/assets/js/helpers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mobileBtn = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    
    // Profile Elements
    const profileBtn = document.getElementById('profile-menu-button');
    const profileMenu = document.getElementById('profile-menu');
    
    function setIcon(isOpen) {
        if (isOpen) {
            menuIcon.classList.remove('fa-bars');
            menuIcon.classList.add('fa-times', 'rotate-90');
            mobileBtn.setAttribute('aria-expanded', 'true');
        } else {
            menuIcon.classList.remove('fa-times', 'rotate-90');
            menuIcon.classList.add('fa-bars');
            mobileBtn.setAttribute('aria-expanded', 'false');
        }
    }
    
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = mobileMenu.classList.toggle('hidden') === false;
            setIcon(isOpen);
        });
        
        // Klik di luar
        window.addEventListener('click', function (e) {
            if (!mobileMenu.classList.contains('hidden') &&
                !mobileBtn.contains(e.target) &&
                !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                setIcon(false);
            }
        });
        
        // Escape
        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                setIcon(false);
            }
        });
        
        // Klik link menu
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                setIcon(false);
            });
        });
    }
    
    // ==========================================
    // EVENT LISTENERS (PROFILE MENU)
    // ==========================================
    if (profileBtn && profileMenu) {
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });
        
        window.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });
    }
});
</script>




<!-- Custom Page-specific Scripts -->
<script src="/docutrack/public/assets/js/page-scripts/notifikasi.js"></script>

</body>
</html>
