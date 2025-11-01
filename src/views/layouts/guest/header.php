<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'Docutrack PNJ'; // Ambil title dari controller ?></title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">
    
    <link href="/docutrack/public/assets/css/output.css" rel="stylesheet"> 
    
    </head>
<body>

    <header class="main-header bg-transparent p-4 fixed w-full top-0 z-1000 transition-all duration-400 ease-in-out">
        <div class="container max-w-6xl mx-auto px-8 flex justify-between md:justify-center items-center relative">
            
            <button class="menu-toggle group md:hidden z-1001" aria-label="Toggle navigation">
                <div class="hamburger-icon space-y-1.5">
                    <span class="block w-6 h-0.5 bg-white transition-all duration-300 ease-in-out group-[.active]:rotate-45 group-[.active]:translate-y-2"></span>
                    <span class="block w-6 h-0.5 bg-white transition-all duration-300 ease-in-out group-[.active]:opacity-0"></span>
                    <span class="block w-6 h-0.5 bg-white transition-all duration-300 ease-in-out group-[.active]:-rotate-45 group-[.active]:-translate-y-2"></span>
                </div>
            </button>

            <nav class="main-nav hidden md:flex flex-col md:flex-row items-center absolute md:relative top-full left-0 w-full md:w-auto 
                         bg-primary-dark-green/70 backdrop-blur-lg md:bg-transparent md:backdrop-blur-none 
                         shadow-lg md:shadow-none p-4 md:p-0 transition-all duration-300 ease-in-out">
                <ul class="flex flex-col md:flex-row list-none w-full md:w-auto text-center gap-4 md:gap-0 md:items-center">
                    <li class="md:mx-[25px]">
                        <a href="#hero" class="text-white font-normal text-base py-1 relative after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-0 after:left-0 after:bg-accent-teal after:transition-all after:duration-200 after:ease-in-out hover:after:w-full">HOME</a>
                    </li>
                    <li class="md:mx-[25px]">
                        <a href="#about" class="text-white font-normal text-base py-1 relative after:content-[''] after:absolute after:w-0 after:h-0.5 after:bottom-0 after:left-0 after:bg-accent-teal after:transition-all after:duration-200 after:ease-in-out hover:after:w-full">ABOUT</a>
                    </li>
                    <li class="md:mx-[25px]"> 
                        <a href="/docutrack/public/login" id="open-login-btn" class="btn-brilliant"> 
                            LOGIN
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
    <?php
        include '../src/views/partials/popup_login.php'; 
    ?>

    <script>
    document.addEventListener('DOMContentLoaded', () => {

        // 1. Logika Scroll Header
        const header = document.querySelector('.main-header');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                header.classList.add('bg-primary-dark-green/70', 'backdrop-blur-lg', 'shadow-xl');
                header.classList.remove('bg-transparent');
            } else {
                header.classList.remove('bg-primary-dark-green/70', 'backdrop-blur-lg', 'shadow-xl');
                header.classList.add('bg-transparent');
            }
        });

        // 2. Logika Toggle Menu
        const menuToggle = document.querySelector('.menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        if (menuToggle && mainNav) { // Tambahkan pengecekan
            menuToggle.addEventListener('click', () => {
                mainNav.classList.toggle('hidden');
                mainNav.classList.toggle('flex');
                menuToggle.classList.toggle('active');
            });
        }

        // 3. Logika Scroll Animation Timeline
        const timelineContainer = document.querySelector('.timeline-container');
        const timelineItems = document.querySelectorAll('.timeline-item');
        let processedItems = 0;

        // Sembunyikan item awal
        timelineItems.forEach((item) => {
            item.classList.add('hidden'); 
        });

        const observerOptions = { root: null, rootMargin: '0px', threshold: 0.3 };

        const timelineObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.target.classList.contains('timeline-item')) {
                    const item = entry.target;
                    observer.unobserve(item); 
                    
                    setTimeout(() => { // Tambah delay sedikit
                        item.classList.remove('hidden');
                        item.classList.add('show');
                        
                        processedItems++;
                        const percentage = (processedItems / timelineItems.length) * 100;
                        
                        if (timelineContainer) {
                            timelineContainer.style.setProperty('--line-height-progress', `${percentage}%`);
                        }
                    }, 200); // delay 200ms
                }
            });
        }, observerOptions);
        
        // Mulai mengamati
        timelineItems.forEach((item) => {
            timelineObserver.observe(item);
        });
        
        // Set garis awal ke 0%
        if (timelineContainer) {
            timelineContainer.style.setProperty('--line-height-progress', '0%');
        }
    });
    </script>
</body>
</html>