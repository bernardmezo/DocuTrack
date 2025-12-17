<?php
// File: src/views/layouts/bendahara/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = $_SERVER['REQUEST_URI'] ?? '';

// Data User
$userData = $_SESSION['user_data'] ?? [];
$userName = $userData['username'] ?? $_SESSION['user_name'] ?? 'User';
$userRole = $userData['role'] ?? $_SESSION['user_role'] ?? 'bendahara';
$defaultImage = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=0D8ABC&color=fff&size=150';
$userImage = $userData['profile_image'] ?? $_SESSION['profile_image'] ?? $defaultImage;
$headerBg = $userData['header_bg'] ?? 'linear-gradient(to left, #17A18A, #006A9A, #114177)';

// Link Akun
switch (strtolower($userRole)) {
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
    case 'super administrator': // Menangani format dari dummy data
    case 'superadmin':
        $akun_link = '/docutrack/public/superadmin/akun';
        break;
    default:
        $akun_link = '/docutrack/public/bendahara/akun';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Docutrack Bendahara'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link href="/docutrack/public/assets/css/output.css" rel="stylesheet">
</head>
<body class="font-poppins bg-gradient-to-br from-gray-100 to-teal-100 min-h-screen">
<div class="main-wrapper font-poppins">
    <div class="top-section bg-gradient-to-l from-[#17A18A] via-[#006A9A] to-[#114177] p-4 sm:p-6 pb-4 md:pb-20 text-white shadow-lg">
        <header class="flex justify-between items-center pb-3 sm:pb-5 border-b border-white/20 max-w-7xl mx-auto">
            
            <div class="flex items-center gap-3 sm:gap-4 md:gap-10 flex-1 min-w-0">
                <!-- Logo -->
                <div class="w-32 sm:w-40 md:w-auto flex-shrink-0">
                    <a href="/docutrack/public/bendahara/dashboard">
                        <svg class="w-full h-auto" width="195" height="47" viewBox="0 0 195 47" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g filter="url(#filter0_d_194_31)"><path d="M179.658 26.2678C174.418 24.2673 168.504 26.2678 166.917 27.1013C172.632 26.625 173.585 27.1013 178.586 29.3638C185.139 32.3283 189.104 26.1884 190.136 24.0054C188.787 25.2358 184.897 28.2682 179.658 26.2678Z" fill="white" stroke="white" stroke-width="0.341658"/><path d="M176.085 30.555C171.227 28.2687 168.305 27.7368 167.393 28.0543C169.536 29.7214 174.085 33.4127 177.99 34.3653C181.896 35.3179 186.405 32.5395 188.231 30.0786C185.968 31.6663 180.943 32.8412 176.085 30.555Z" fill="white" stroke="white" stroke-width="0.341658"/><path d="M180.149 21.1713C174.482 19.2149 168.93 23.8971 166.95 26.1927C168.071 24.8985 171.909 22.6991 178.292 24.2551C184.675 25.8111 188.749 22.1279 189.998 20.2702C189.304 20.6662 186.106 23.2277 180.149 21.1713Z" fill="white" stroke="white" stroke-width="0.341658"/><ellipse cx="183.468" cy="13.527" rx="4.64394" ry="4.64394" fill="#FF0000"/><path d="M4.39161 10.9131H13.753C16.3724 10.9131 18.6274 11.3914 20.5179 12.3481C22.4084 13.3047 23.8547 14.6827 24.8569 16.4821C25.8591 18.2815 26.3602 20.434 26.3602 22.9395C26.3602 25.445 25.8591 27.6088 24.8569 29.431C23.8775 31.2304 22.4425 32.6084 20.552 33.565C18.6615 34.5217 16.3952 35 13.753 35H4.39161V10.9131ZM13.3772 30.6951C18.5021 30.6951 21.0645 28.1099 21.0645 22.9395C21.0645 17.7918 18.5021 15.218 13.3772 15.218H9.72147V30.6951H13.3772ZM38.1029 35.2733C36.3262 35.2733 34.766 34.9203 33.4221 34.2142C32.0783 33.4853 31.0419 32.4717 30.313 31.1734C29.5842 29.8524 29.2197 28.3035 29.2197 26.5269C29.2197 24.7503 29.5842 23.2128 30.313 21.9145C31.0419 20.5934 32.0783 19.5798 33.4221 18.8737C34.766 18.1676 36.3262 17.8146 38.1029 17.8146C39.8795 17.8146 41.4397 18.1676 42.7836 18.8737C44.1274 19.5798 45.1638 20.5934 45.8927 21.9145C46.6215 23.2128 46.986 24.7503 46.986 26.5269C46.986 28.3035 46.6215 29.8524 45.8927 31.1734C45.1638 32.4717 44.1274 33.4853 42.7836 34.2142C41.4397 34.9203 39.8795 35.2733 38.1029 35.2733ZM38.1029 30.6951C41.222 30.6951 42.7816 29.3066 42.7816 26.5269C42.7816 23.7473 41.222 22.3588 38.1029 22.3588C34.9838 22.3588 33.4242 23.7473 33.4242 26.5269C33.4242 29.3066 34.9838 30.6951 38.1029 30.6951ZM59.1414 35.2733C57.3611 35.2733 55.8072 34.9203 54.4797 34.2142C53.1523 33.4853 52.1323 32.4717 51.4197 31.1734C50.7071 29.8524 50.3508 28.3035 50.3508 26.5269V17.8146H54.5542V26.5269C54.5542 29.3066 56.0833 30.6951 59.1414 30.6951C62.1995 30.6951 63.7286 29.3066 63.7286 26.5269V17.8146H67.932V26.5269C67.932 28.3035 67.5757 29.8524 66.8631 31.1734C66.1505 32.4717 65.1305 33.4853 63.8031 34.2142C62.4756 34.9203 60.9217 35.2733 59.1414 35.2733ZM72.844 14.1713V17.8146H76.4997V35H80.7031V17.8146H84.3588V14.1713H72.844ZM96.444 35H92.2406V10.9131H101.314C103.868 10.9131 106.062 11.3914 107.896 12.3481C109.73 13.3047 111.134 14.6827 112.108 16.4821C113.082 18.2815 113.569 20.434 113.569 22.9395C113.569 25.123 113.18 27.014 112.402 28.6125C111.624 30.211 110.42 31.4284 108.79 32.2647C107.16 33.101 105.101 33.5191 102.613 33.5191H100.34V22.3588H102.613C105.732 22.3588 107.292 23.7473 107.292 26.5269C107.292 29.3066 105.732 30.6951 102.613 30.6951H100.34V35H96.444ZM124.114 35.2733C122.334 35.2733 120.78 34.9203 119.452 34.2142C118.125 33.4853 117.105 32.4717 116.392 31.1734C115.68 29.8524 115.323 28.3035 115.323 26.5269C115.323 24.7503 115.68 23.2128 116.392 21.9145C117.105 20.5934 118.125 19.5798 119.452 18.8737C120.78 18.1676 122.334 17.8146 124.114 17.8146C125.894 17.8146 127.448 18.1676 128.776 18.8737C130.103 19.5798 131.123 20.5934 131.836 21.9145C132.548 23.2128 132.905 24.7503 132.905 26.5269C132.905 28.3035 132.548 29.8524 131.836 31.1734C131.123 32.4717 130.103 33.4853 128.776 34.2142C127.448 34.9203 125.894 35.2733 124.114 35.2733ZM124.114 30.6951C127.233 30.6951 128.793 29.3066 128.793 26.5269C128.793 23.7473 127.233 22.3588 124.114 22.3588C120.995 22.3588 119.435 23.7473 119.435 26.5269C119.435 29.3066 120.995 30.6951 124.114 30.6951ZM143.211 35.2733C141.431 35.2733 139.877 34.9203 138.549 34.2142C137.222 33.4853 136.202 32.4717 135.489 31.1734C134.777 29.8524 134.42 28.3035 134.42 26.5269C134.42 24.7503 134.777 23.2128 135.489 21.9145C136.202 20.5934 137.222 19.5798 138.549 18.8737C139.877 18.1676 141.431 17.8146 143.211 17.8146C144.991 17.8146 146.545 18.1676 147.873 18.8737C149.2 19.5798 150.22 20.5934 150.933 21.9145C151.645 23.2128 152.002 24.7503 152.002 26.5269C152.002 28.3035 151.645 29.8524 150.933 31.1734C150.22 32.4717 149.2 33.4853 147.873 34.2142C146.545 34.9203 144.991 35.2733 143.211 35.2733ZM143.211 30.6951C146.33 30.6951 147.89 29.3066 147.89 26.5269C147.89 23.7473 146.33 22.3588 143.211 22.3588C140.092 22.3588 138.532 23.7473 138.532 26.5269C138.532 29.3066 140.092 30.6951 143.211 30.6951ZM156.411 35V10.9131H160.614V23.2277L166.463 17.8146H171.537L165.11 23.7473L171.862 35H166.917L162.238 26.8524L160.614 28.2815V35H156.411Z" fill="white"/></g><defs><filter id="filter0_d_194_31" x="0.291611" y="8.88541" width="194.144" height="37.5799" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="2.05001"/><feGaussianBlur stdDeviation="2.05001"/><feComposite in2="hardAlpha" operator="out"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_194_31"/><feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_194_31" result="shape"/></filter></defs>
                        </svg>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center justify-center flex-1">
                    <nav>
                        <ul class="flex gap-2 xl:gap-4">
                            <li>
                                <a href="/docutrack/public/bendahara/dashboard"
                                   class="flex items-center gap-2 px-3 xl:px-4 py-2 rounded-full transition-colors text-sm xl:text-base whitespace-nowrap <?= isActive($current, '/bendahara/dashboard') ?>">
                                    <i class="fas fa-th-large text-xs xl:text-sm"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="/docutrack/public/bendahara/pencairan-dana"
                                   class="flex items-center gap-2 px-3 xl:px-4 py-2 rounded-full transition-colors text-sm xl:text-base whitespace-nowrap <?= isActive($current, '/bendahara/pencairan-dana') ?>">
                                    <i class="fas fa-file-signature text-xs xl:text-sm"></i> Pencairan Dana
                                </a>
                            </li>
                            <li>
                                <a href="/docutrack/public/bendahara/pengajuan-lpj"
                                   class="flex items-center gap-2 px-3 xl:px-4 py-2 rounded-full transition-colors text-sm xl:text-base whitespace-nowrap <?= isActive($current, '/bendahara/pengajuan-lpj') ?>">
                                    <i class="fas fa-file-invoice text-xs xl:text-sm"></i> Pengajuan LPJ
                                </a>
                            </li>
                            <li>
                                <a href="/docutrack/public/bendahara/riwayat-verifikasi"
                                   class="flex items-center gap-2 px-3 xl:px-4 py-2 rounded-full transition-colors text-sm xl:text-base whitespace-nowrap <?= isActive($current, '/bendahara/riwayat-verifikasi') ?>">
                                    <i class="fas fa-book-open text-xs xl:text-sm"></i> Riwayat Verifikasi
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Right Side: Notifikasi + Profil + Mobile Menu Button -->
            <div class="flex items-center gap-2 sm:gap-4 md:gap-6">
                
                <!-- Notifikasi -->
                <div class="relative" id="notification-container">
                    <div id="notification-icon-button" class="relative text-xl text-gray-200 hover:text-white cursor-pointer transition-colors duration-200">
                        <i class="fas fa-bell"></i>
                        <span id="notification-count" class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-600 text-[9px] font-bold text-white ring-2 ring-[#0A4A7F] <?php echo (isset($unread_notifications_count) && $unread_notifications_count > 0) ? '' : 'hidden'; ?>">
                            <?php echo isset($unread_notifications_count) ? $unread_notifications_count : 0; ?>
                        </span>
                    </div>
                    <div id="notification-dropdown" class="absolute right-0 mt-3 w-80 bg-white rounded-lg shadow-xl py-2 z-50 hidden border border-gray-100">
                        <div class="flex justify-between items-center px-4 py-2 border-b">
                            <h3 class="font-semibold text-gray-800">Notifikasi</h3>
                            <button id="mark-all-as-read-btn" class="text-sm text-blue-600 hover:underline">Tandai semua dibaca</button>
                        </div>
                        <div id="notification-list" class="max-h-80 overflow-y-auto">
                            <?php if (!empty($notifications) && is_array($notifications)): ?>
                                <?php foreach ($notifications as $notif): ?>
                                    <div class="notification-item px-4 py-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 transition-colors" 
                                         data-id="<?= htmlspecialchars($notif['id']) ?>">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 mt-1">
                                                <i class="fas fa-bell text-blue-500"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($notif['title'] ?? 'Notifikasi') ?></p>
                                                <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($notif['message'] ?? '') ?></p>
                                                <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($notif['created_at'] ?? '') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-gray-500 py-4">Tidak ada notifikasi baru.</div>
                            <?php endif; ?>
                        </div>
                        <div class="px-4 py-2 border-t text-center">
                            <a href="#" id="view-all-notifications-link" class="text-sm text-blue-600 hover:underline">Lihat semua notifikasi</a>
                        </div>
                    </div>
                </div>

                <!-- Profil (Desktop) -->
                <div class="relative hidden sm:block">
                    <div id="profile-menu-button" class="flex items-center gap-2 sm:gap-3 cursor-pointer group">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-cover bg-center ring-2 ring-offset-2 ring-offset-[#0A4A7F] ring-white flex-shrink-0"
                             style="background-image: url('<?php echo htmlspecialchars($userImage); ?>')">
                        </div>
                        
                        <div class="hidden md:block">
                            <div class="font-semibold text-sm text-white truncate max-w-[120px] xl:max-w-none"><?php echo htmlspecialchars($userName); ?></div>
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

                <!-- Mobile Menu Button -->
                <button
                    id="mobile-menu-button"
                    class="lg:hidden relative z-50 text-white p-2 rounded-lg hover:bg-white/10 transition-colors duration-200 flex items-center justify-center w-10 h-10"
                    aria-label="Toggle Menu"
                    aria-expanded="false">
                    <i id="menu-icon" class="fas fa-bars text-xl transition-transform duration-300"></i>
                </button>

            </div>
        </header>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden mt-4 pb-2 max-w-7xl mx-auto">
            <nav class="bg-white/10 backdrop-blur-sm rounded-lg overflow-hidden shadow-lg">
                <ul class="divide-y divide-white/10">
                    <!-- User Info (Mobile Only) -->
                    <li class="sm:hidden px-4 py-3 bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-cover bg-center ring-2 ring-white"
                                 style="background-image: url('<?php echo htmlspecialchars($userImage); ?>')">
                            </div>
                            <div>
                                <div class="font-semibold text-sm text-white"><?php echo htmlspecialchars($userName); ?></div>
                                <div class="text-xs text-gray-200"><?php echo htmlspecialchars($userRole); ?></div>
                            </div>
                        </div>
                    </li>

                    <!-- Navigation Links -->
                    <li>
                        <a href="/docutrack/public/bendahara/dashboard"
                           class="flex items-center gap-3 px-4 py-3 transition-colors <?= isActiveMobile($current, '/bendahara/dashboard') ?>">
                            <i class="fas fa-th-large text-base w-5"></i>
                            <span class="text-sm">Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="/docutrack/public/bendahara/pencairan-dana"
                           class="flex items-center gap-3 px-4 py-3 transition-colors <?= isActiveMobile($current, '/bendahara/pencairan-dana') ?>">
                            <i class="fas fa-file-signature text-base w-5"></i>
                            <span class="text-sm">Pencairan Dana</span>
                        </a>
                    </li>
                    <li>
                        <a href="/docutrack/public/bendahara/pengajuan-lpj"
                           class="flex items-center gap-3 px-4 py-3 transition-colors <?= isActiveMobile($current, '/bendahara/pengajuan-lpj') ?>">
                            <i class="fas fa-file-invoice text-base w-5"></i>
                            <span class="text-sm">Pengajuan LPJ</span>
                        </a>
                    </li>
                    <li>
                        <a href="/docutrack/public/bendahara/riwayat-verifikasi"
                           class="flex items-center gap-3 px-4 py-3 transition-colors <?= isActiveMobile($current, '/bendahara/riwayat-verifikasi') ?>">
                            <i class="fas fa-book-open text-base w-5"></i>
                            <span class="text-sm">Riwayat Verifikasi</span>
                        </a>
                    </li>

                    <!-- Account Links (Mobile Only) -->
                    <li class="sm:hidden">
                        <a href="<?php echo $akun_link; ?>"
                           class="flex items-center gap-3 px-4 py-3 text-gray-200 hover:bg-white/10 font-medium transition-all duration-200">
                            <i class="fas fa-user-circle text-base w-5"></i>
                            <span class="text-sm">Akun Saya</span>
                        </a>
                    </li>
                    <li class="sm:hidden">
                        <a href="/docutrack/public/logout"
                           class="flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-red-500/20 font-medium transition-all duration-200">
                            <i class="fas fa-sign-out-alt text-base w-5"></i>
                            <span class="text-sm">Logout</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
