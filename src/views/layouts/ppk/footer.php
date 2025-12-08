<?php

/**
 * Footer layout for PPK
 *
 * @package DocuTrack
 */

?>
        </main>
    </div>
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
            }

            // --- Logika Toggle Menu Mobile ---
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const openIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon'); 
            if (mobileMenuButton && mobileMenu && openIcon && closeIcon) {
                mobileMenuButton.addEventListener('click', () => {
                    mobileMenu.classList.toggle('hidden');
                    openIcon.classList.toggle('hidden');
                    closeIcon.classList.toggle('hidden');
                    const isOpen = !mobileMenu.classList.contains('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', isOpen.toString());
                });
            }
        });
    </script>

    <!-- Custom Page-specific Scripts -->
    <script src="/docutrack/public/assets/js/page-scripts/notifikasi.js"></script>
</body>
</html>
