</div>
<!-- Vendor JS -->
<script src="/docutrack/public/assets/js/helpers.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // ============================================
    // MOBILE MENU FUNCTIONALITY
    // ============================================
    const mobileBtn = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    
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
        // Toggle mobile menu
        mobileBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = mobileMenu.classList.toggle('hidden') === false;
            setIcon(isOpen);
        });
        
        // Close menu when clicking outside
        window.addEventListener('click', function (e) {
            if (!mobileMenu.classList.contains('hidden') &&
                !mobileBtn.contains(e.target) &&
                !mobileMenu.contains(e.target)) {
                mobileMenu.classList.add('hidden');
                setIcon(false);
            }
        });
        
        // Close menu on Escape key
        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                setIcon(false);
            }
        });
        
        // Close menu when clicking on a link
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                setIcon(false);
            });
        });
        
        // Close mobile menu when resizing to desktop view
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth >= 1024) { // lg breakpoint (1024px)
                    mobileMenu.classList.add('hidden');
                    setIcon(false);
                }
            }, 250);
        });
    }
    
    // ============================================
    // PROFILE DROPDOWN FUNCTIONALITY
    // ============================================
    const profileBtn = document.getElementById('profile-menu-button');
    const profileMenu = document.getElementById('profile-menu');
    
    if (profileBtn && profileMenu) {
        // Toggle profile menu on click
        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('hidden');
        });
        
        // Close profile menu when clicking outside
        window.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.add('hidden');
            }
        });
        
        // Close profile menu on Escape key
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !profileMenu.classList.contains('hidden')) {
                profileMenu.classList.add('hidden');
            }
        });
    }
});
</script>
<script src="/docutrack/public/assets/js/page-scripts/notifikasi.js"></script>
</body>
</html>