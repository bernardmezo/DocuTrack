<?php
// File: src/views/layouts/direktur/header.php
// Pastikan session sudah dimulai (biasanya sudah dimulai di index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = $_SERVER['REQUEST_URI'];

function isActive($current, $target) {
    return (strpos($current, $target) !== false)
        ? 'nav-link-base nav-link-active'
        : 'nav-link-base nav-link-inactive';
}

// ============================================
// LOGIKA DATA USER (SINKRONISASI DENGAN CONTROLLER AKUN)
// ============================================

// 1. Ambil data dari session 'user_data' (Format baru dari Controller)
$userData = $_SESSION['user_data'] ?? [];

// 2. Tentukan Nama (Prioritas: Data Baru -> Session Lama -> Default)
$userName = $userData['username'] ?? $_SESSION['user_name'] ?? 'User';

// 3. Tentukan Role
$userRole = $userData['role'] ?? $_SESSION['user_role'] ?? 'admin';

// 4. Tentukan Foto Profile
$defaultImage = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=0D8ABC&color=fff&size=150';
$userImage = $userData['profile_image'] ?? $_SESSION['profile_image'] ?? $defaultImage;

// 5. Tentukan Background Header (BARU - jika ada)
$headerBg = $userData['header_bg'] ?? 'linear-gradient(to left, #17A18A, #006A9A, #114177)';

// ============================================
// TENTUKAN LINK AKUN BERDASARKAN ROLE
// ============================================
switch (strtolower($userRole)) {
    case 'admin':
        $akun_link = '/docutrack/public/admin/akun';
        break;
    case 'verifikator':
        $akun_link = '/docutrack/public/verifikator/akun';
        break;
    case 'wadir':
        $akun_link = '/docutrack/public/wadir/akun';
        break;
    case 'ppk':
        $akun_link = '/docutrack/public/ppk/akun';
        break;
    case 'bendahara':
        $akun_link = '/docutrack/public/bendahara/akun';
        break;
    case 'super-admin':
        $akun_link = '/docutrack/public/super_admin/akun';
        break;
    case 'direktur':
        $akun_link = '/docutrack/public/direktur/akun';
        break;
    default:
        $akun_link = '/docutrack/public/'; // Fallback ke home
        break;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Judul Halaman Dinamis -->
    <title><?php echo htmlspecialchars($title ?? 'Docutrack Bendahara'); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Flatpickr CSS (Dibutuhkan oleh halaman Pengajuan Usulan) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <!-- Link ke CSS yang sudah di-compile dari Tailwind -->
    <link href="/docutrack/public/assets/css/output.css" rel="stylesheet">

</head>
<body class="font-poppins bg-gradient-to-br from-gray-100 to-teal-100 min-h-screen">
    <!-- Wrapper utama -->
    <div class="main-wrapper">
        <!-- Bagian atas berwarna -->
        <div class="top-section bg-gradient-to-l from-[#17A18A] via-[#006A9A] to-[#114177] p-6 pb-4 md:pb-20 text-white shadow-lg">
            <header class="flex justify-between font-poppins items-center pb-5 border-b border-white/20 max-w-7xl mx-auto">
                <!-- Sisi Kiri: Logo & Navigasi Desktop -->
                <div class="flex items-center gap-4 md:gap-10">
                    <!-- Logo lebih kecil di mobile -->
                    <div class="w-40 md:w-auto">
                        <a href="/docutrack/public/bendahara/dashboard"> <!-- Link logo ke dashboard -->
                            <svg width="195" height="47" viewBox="0 0 195 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <!-- Kode SVG Anda di sini... -->
                                <g filter="url(#filter0_d_194_31)"><path d="M179.658 26.2678C174.418 24.2673 168.504 26.2678 166.917 27.1013C172.632 26.625 173.585 27.1013 178.586 29.3638C185.139 32.3283 189.104 26.1884 190.136 24.0054C188.787 25.2358 184.897 28.2682 179.658 26.2678Z" fill="white" stroke="white" stroke-width="0.341658"/><path d="M176.085 30.555C171.227 28.2687 168.305 27.7368 167.393 28.0543C169.536 29.7214 174.085 33.4127 177.99 34.3653C181.896 35.3179 186.405 32.5395 188.231 30.0786C185.968 31.6663 180.943 32.8412 176.085 30.555Z" fill="white" stroke="white" stroke-width="0.341658"/><path d="M180.149 21.1713C174.482 19.2149 168.93 23.8971 166.95 26.1927C168.071 24.8985 171.909 22.6991 178.292 24.2551C184.675 25.8111 188.749 22.1279 189.998 20.2702C189.304 20.6662 186.106 23.2277 180.149 21.1713Z" fill="white" stroke="white" stroke-width="0.341658"/><ellipse cx="183.468" cy="13.527" rx="4.64394" ry="4.64394" fill="#FF0000"/><path d="M4.39161 10.9131H13.753C16.3724 10.9131 18.6274 11.3914 20.5179 12.3481C22.4084 13.3047 23.8547 14.6827 24.8569 16.4821C25.8591 18.2815 26.3602 20.434 26.3602 22.9395C26.3602 25.445 25.8591 27.6088 24.8569 29.431C23.8775 31.2304 22.4425 32.6084 20.552 33.565C18.6615 34.5217 16.3952 35 13.753 35H4.39161V10.9131ZM13.3772 30.6951C18.5021 30.6951 21.0645 28.1099 21.0645 22.9395C21.0645 17.7918 18.5021 15.218 13.3772 15.218H9.72147V30.6951H13.3772ZM38.1029 35.2733C36.3262 35.2733 34.766 34.9203 33.4221 34.2142C32.0783 33.4853 31.0419 32.4717 30.313 31.1734C29.5842 29.8524 29.2197 28.3035 29.2197 26.5269C29.2197 24.7503 29.5842 23.2128 30.313 21.9145C31.0419 20.5934 32.0783 19.5798 33.4221 18.8737C34.766 18.1676 36.3262 17.8146 38.1029 17.8146C39.8795 17.8146 41.4397 18.1676 42.7836 18.8737C44.1274 19.5798 45.1638 20.5934 45.8927 21.9145C46.6215 23.2128 46.986 24.7503 46.986 26.5269C46.986 28.3035 46.6215 29.8524 45.8927 31.1734C45.1638 32.4717 44.1274 33.4853 42.7836 34.2142C41.4397 34.9203 39.8795 35.2733 38.1029 35.2733ZM38.1029 31.3443C40.6083 31.3443 41.8611 29.7385 41.8611 26.5269C41.8611 24.9097 41.5308 23.7025 40.8703 22.9053C40.2325 22.1081 39.31 21.7095 38.1029 21.7095C35.5974 21.7095 34.3446 23.3153 34.3446 26.5269C34.3446 29.7385 35.5974 31.3443 38.1029 31.3443ZM58.1203 35.2733C55.387 35.2733 53.2346 34.4989 51.6629 32.9501C50.0913 31.4012 49.3055 29.2943 49.3055 26.6294C49.3055 24.8755 49.6813 23.3381 50.433 22.017C51.1846 20.6731 52.2438 19.6368 53.6104 18.9079C54.977 18.179 56.56 17.8146 58.3594 17.8146C59.5894 17.8146 60.7738 18.0082 61.9127 18.3954C63.0515 18.7599 63.974 19.2609 64.6801 19.8987L63.3135 23.4178C62.6529 22.8939 61.9241 22.4953 61.1269 22.222C60.3524 21.9259 59.5894 21.7778 58.8378 21.7778C57.4939 21.7778 56.4462 22.1764 55.6945 22.9736C54.9656 23.7708 54.6012 24.9553 54.6012 26.5269C54.6012 28.0985 54.9656 29.2943 55.6945 30.1143C56.4462 30.9115 57.4939 31.3101 58.8378 31.3101C59.5894 31.3101 60.3524 31.1734 61.1269 30.9001C61.9241 30.604 62.6529 30.194 63.3135 29.6701L64.6801 33.2234C63.9285 33.8611 62.9718 34.3622 61.8102 34.7267C60.6486 35.0911 59.4186 35.2733 58.1203 35.2733ZM83.1686 18.2246V35H78.1463V32.6084C77.6224 33.4739 76.9277 34.1345 76.0621 34.59C75.2194 35.0456 74.2627 35.2733 73.1922 35.2733C71.0739 35.2733 69.4909 34.6925 68.4432 33.5309C67.4182 32.3465 66.9057 30.5698 66.9057 28.201V18.2246H72.0647V28.3035C72.0647 29.3057 72.2697 30.046 72.6797 30.5243C73.1125 31.0026 73.7616 31.2418 74.6272 31.2418C75.6294 31.2418 76.438 30.9001 77.0529 30.2168C77.6907 29.5335 78.0096 28.6338 78.0096 27.5177V18.2246H83.1686ZM96.4204 31.4809C96.9443 31.4809 97.491 31.4468 98.0604 31.3784L97.7871 35.1367C97.1265 35.2278 96.466 35.2733 95.8054 35.2733C93.2544 35.2733 91.3867 34.7153 90.2022 33.5992C89.0406 32.4831 88.4598 30.7862 88.4598 28.5085V22.0853H85.2824V18.2246H88.4598V13.3047H93.6188V18.2246H97.8212V22.0853H93.6188V28.4743C93.6188 30.4787 94.5527 31.4809 96.4204 31.4809ZM112.332 22.0512L109.428 22.3587C107.993 22.4953 106.979 22.9053 106.387 23.5886C105.795 24.2492 105.499 25.1375 105.499 26.2536V35H100.34V18.2246H105.294V21.0603C106.137 19.1243 107.879 18.0651 110.521 17.8829L112.025 17.7804L112.332 22.0512ZM121.501 17.8146C124.007 17.8146 125.852 18.4068 127.036 19.5912C128.243 20.7756 128.847 22.6092 128.847 25.0919V35H123.961V32.5059C123.619 33.3714 123.05 34.0547 122.253 34.5558C121.456 35.0342 120.522 35.2733 119.451 35.2733C118.312 35.2733 117.276 35.0456 116.342 34.59C115.431 34.1345 114.702 33.4967 114.155 32.6767C113.632 31.8567 113.37 30.9457 113.37 29.9435C113.37 28.7135 113.677 27.7455 114.292 27.0394C114.93 26.3333 115.943 25.8208 117.333 25.5019C118.722 25.183 120.624 25.0236 123.039 25.0236H123.927V24.4086C123.927 23.4064 123.71 22.7003 123.278 22.2903C122.845 21.8803 122.093 21.6753 121.023 21.6753C120.203 21.6753 119.292 21.8234 118.289 22.1195C117.287 22.4156 116.331 22.8256 115.42 23.3495L114.053 19.8987C115.01 19.3065 116.183 18.8168 117.572 18.4296C118.984 18.0196 120.294 17.8146 121.501 17.8146ZM120.579 31.7201C121.581 31.7201 122.389 31.3898 123.004 30.7293C123.619 30.046 123.927 29.169 123.927 28.0985V27.5177H123.346C121.501 27.5177 120.203 27.6657 119.451 27.9618C118.722 28.2579 118.358 28.7932 118.358 29.5676C118.358 30.1826 118.563 30.6951 118.973 31.1051C119.406 31.5151 119.941 31.7201 120.579 31.7201ZM140.699 35.2733C137.966 35.2733 135.813 34.4989 134.241 32.9501C132.67 31.4012 131.884 29.2943 131.884 26.6294C131.884 24.8755 132.26 23.3381 133.011 22.017C133.763 20.6731 134.822 19.6368 136.189 18.9079C137.556 18.179 139.139 17.8146 140.938 17.8146C142.168 17.8146 143.352 18.0082 144.491 18.3954C145.63 18.7599 146.553 19.2609 147.259 19.8987L145.892 23.4178C145.231 22.8939 144.503 22.4953 143.705 22.222C142.931 21.9259 142.168 21.7778 141.416 21.7778C140.072 21.7778 139.025 22.1764 138.273 22.9736C137.544 23.7708 137.18 24.9553 137.18 26.5269C137.18 28.0985 137.544 29.2943 138.273 30.1143C139.025 30.9115 140.072 31.3101 141.416 31.3101C142.168 31.3101 142.931 31.1734 143.705 30.9001C144.503 30.604 145.231 30.194 145.892 29.6701L147.259 33.2234C146.507 33.8611 145.55 34.3622 144.389 34.7267C143.227 35.0911 141.997 35.2733 140.699 35.2733ZM161.169 35L154.746 27.5177V35H149.587V10.9131H154.746V25.3994L160.93 18.2588H167.08L160.041 26.2536L167.49 35H161.169Z" fill="#33ABA0"/></g><defs><filter id="filter0_d_194_31" x="0.391602" y="8.88306" width="193.899" height="34.3904" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4"/><feGaussianBlur stdDeviation="2"/><feComposite in2="hardAlpha" operator="out"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_194_31"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_194_31" result="shape"/></filter></defs></svg>
                        </a>
                    </div>

                    <!-- Navigasi Desktop -->
                    <div class="flex items-center gap-4 md:gap-10">
                        <nav>
                            <ul class="flex gap-4">
                                <li>
                                    <a href="/docutrack/public/direktur/dashboard" 
                                    class="<?= isActive($current, '/direktur/dashboard'); ?>">
                                        <i class="fas fa-th-large text-sm"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="/docutrack/public/direktur/monitoring" 
                                    class="<?= isActive($current, '/direktur/monitoring'); ?>">
                                        <i class="fas fa-desktop text-sm"></i> Monitoring
                                    </a>
                                </li>
                            </ul>
                        </nav>
                     </div>
                </div>

                <!-- Sisi Kanan: Notif, Profil, Hamburger -->
                <div class="flex items-center gap-4 md:gap-6">
                    <!-- Ikon Notifikasi -->
                    <div class="relative text-xl text-gray-200 hover:text-white cursor-pointer transition-colors duration-200">
                        <i class="fas fa-bell"></i>
                        <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white ring-2 ring-[#0A4A7F]">
                            <?php echo 10; // Contoh Notif ?>
                        </span>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="relative">
                        <div id="profile-menu-button" class="flex items-center gap-3 cursor-pointer group">
                             <div class="w-10 h-10 rounded-full bg-cover bg-center ring-2 ring-offset-2 ring-offset-[#0A4A7F] ring-white"
                                  style="background-image: url('<?php echo htmlspecialchars($userImage); ?>')">
                             </div>
                             
                             <div class="hidden sm:block">
                                  <div class="font-semibold text-sm text-white"><?php echo htmlspecialchars($userName); ?></div>
                                  <div class="text-xs text-gray-300"><?php echo htmlspecialchars($userRole); ?></div>
                             </div>
                        </div>
                        
                        <div id="profile-menu" class="absolute right-0 mt-3 w-48 bg-white rounded-lg shadow-xl py-2 z-50 hidden border border-gray-100">
                             <a href="<?php echo $akun_link; ?>" class="flex items-center justify-between px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
                                 <span>Akun Saya</span>
                                 <i class="fas fa-user-circle text-gray-400"></i>
                             </a>
                             <hr class="my-1 border-gray-200">
                             <a href="/docutrack/public/logout" class="flex items-center justify-between px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                 <span>Logout</span>
                                 <i class="fas fa-sign-out-alt text-red-400"></i>
                             </a>
                        </div>
                    </div>

                    <!-- Tombol Hamburger (Mobile) -->
                    <button id="mobile-bendahara-menu-button" class="md:hidden p-2 rounded-md text-gray-200 hover:text-white hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" aria-controls="mobile-admin-menu" aria-expanded="false">
                        <span class="sr-only">Buka menu utama</span>
                        <!-- Ikon Hamburger -->
                        <svg id="hamburger-bendahara-icon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
                        <!-- Ikon Close (X) -->
                        <svg id="close-bendahara-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            </header>
        </div> <!-- Akhir top-section -->

    <!-- Konten utama halaman dimulai di sini (akan ditutup oleh footer.php) -->
    <main class="main-content ..."></main>