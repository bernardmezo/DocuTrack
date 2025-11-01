</main> <footer class="main-footer text-center p-8 bg-primary-dark-green text-white">
        <div class="container max-w-6xl mx-auto px-8">
            <p>&copy; <?php echo date("Y"); ?> Docutrack PNJ. All Rights Reserved.</p>
        </div>
    </footer>

    <?php
        // Pastikan path ini benar (sesuaikan jika perlu)
        // Ini mengasumsikan popup_login.php ada di src/views/partials/
        $popup_path = '../src/views/partials/popup_login.php';
        if (file_exists($popup_path)) {
            include $popup_path;
        } else {
            // Tampilkan pesan error jika file tidak ditemukan
            echo "";
        }
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // --- 1. Logika Scroll Header ---
        const header = document.querySelector('.main-header');
        if (header) { // Tambah pengecekan
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    header.classList.add('bg-primary-dark-green/70', 'backdrop-blur-lg', 'shadow-xl');
                    header.classList.remove('bg-transparent');
                } else {
                    header.classList.remove('bg-primary-dark-green/70', 'backdrop-blur-lg', 'shadow-xl');
                    header.classList.add('bg-transparent');
                }
            });
        }

        // --- 2. Logika Toggle Menu Mobile ---
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        if (menuToggle && mainNav) { // Tambah pengecekan
            menuToggle.addEventListener('click', () => {
                // Toggle kelas untuk menampilkan/menyembunyikan menu
                mainNav.classList.toggle('hidden');
                mainNav.classList.toggle('flex'); // Tampilkan sebagai flex saat aktif
                
                // Toggle kelas 'active' pada tombol untuk animasi hamburger -> close
                menuToggle.classList.toggle('active');
            });
        }

        // --- 3. Logika Scroll Animation Timeline ---
        const timelineContainer = document.querySelector('.timeline-container');
        const timelineItems = document.querySelectorAll('.timeline-item');
        let processedItems = 0;

        // Cek apakah elemen timeline ada sebelum melanjutkan
        if (timelineContainer && timelineItems.length > 0) {
            // Sembunyikan semua item di awal
            timelineItems.forEach((item) => {
                item.classList.add('hidden');
            });

            const observerOptions = {
                root: null, // relatif terhadap viewport
                rootMargin: '0px',
                threshold: 0.3 // Muncul saat 30% item terlihat
            };

            const timelineObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    // Jika item masuk ke viewport dan merupakan timeline item
                    if (entry.isIntersecting && entry.target.classList.contains('timeline-item')) {
                        const item = entry.target;
                        
                        // Hentikan pengamatan item ini agar animasi hanya sekali
                        observer.unobserve(item);

                        // Tambahkan sedikit delay sebelum animasi muncul
                        setTimeout(() => {
                            item.classList.remove('hidden');
                            item.classList.add('show'); // Kelas 'show' memicu transisi

                            // Hitung progres garis animasi
                            processedItems++;
                            const percentage = (processedItems / timelineItems.length) * 100;
                            
                            // Perbarui tinggi garis animasi
                            timelineContainer.style.setProperty('--line-height-progress', `${percentage}%`);

                        }, 200); // delay 200ms
                    }
                });
            }, observerOptions);

            // Mulai amati setiap item timeline
            timelineItems.forEach((item) => {
                timelineObserver.observe(item);
            });

            // Set tinggi garis animasi awal ke 0%
            timelineContainer.style.setProperty('--line-height-progress', '0%');
        } // Akhir cek elemen timeline

        // --- 4. Logika Popup Login ---
        // Logika ini sekarang ada di dalam file popup_login.php yang di-include.
        // Tidak perlu ditambahkan lagi di sini.

    }); // Akhir DOMContentLoaded
    </script>
</body>
</html>