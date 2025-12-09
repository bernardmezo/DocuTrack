</main> </div> <script src="/docutrack/public/assets/js/helpers.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // --- Logika Profile Dropdown ---
            const profileButton = document.getElementById('profile-menu-button');
            const profileMenu = document.getElementById('profile-menu');

            if (profileButton && profileMenu) {
                profileButton.addEventListener('click', function(event) {
                    event.stopPropagation();
                    profileMenu.classList.toggle('hidden');
                });
                window.addEventListener('click', function(event) {
                    if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
                        profileMenu.classList.add('hidden');
                    }
                });
                window.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && !profileMenu.classList.contains('hidden')) {
                        profileMenu.classList.add('hidden');
                    }
                });
            } // Akhir Profile Dropdown Logic

            // --- Logika Toggle Menu Mobile Admin ---
            const mobileMenuButtonAdmin = document.getElementById('mobile-admin-menu-button');
            const mobileMenuAdmin = document.getElementById('mobile-admin-menu');
            const openIconAdmin = document.getElementById('hamburger-admin-icon');
            const closeIconAdmin = document.getElementById('close-admin-icon');

            if (mobileMenuButtonAdmin && mobileMenuAdmin && openIconAdmin && closeIconAdmin) {
                mobileMenuButtonAdmin.addEventListener('click', () => {
                    mobileMenuAdmin.classList.toggle('hidden');
                    openIconAdmin.classList.toggle('hidden');
                    closeIconAdmin.classList.toggle('hidden');
                    const isOpen = !mobileMenuAdmin.classList.contains('hidden');
                    mobileMenuButtonAdmin.setAttribute('aria-expanded', isOpen.toString());
                });
            } // Akhir Mobile Menu Logic

        }); // Akhir DOMContentLoaded
    </script>
    
    <!-- Custom Page-specific Scripts -->
    <script src="/docutrack/public/assets/js/page-scripts/notifikasi.js"></script>

    </body>
</html>