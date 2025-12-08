<?php
// File: src/views/pages/superadmin/dashboard.php
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <!-- Notification Bell Icon (Placeholder - Integrate with your existing UI) -->
    <div class="relative mb-6">
        <button id="notification-bell" class="relative p-2 rounded-full bg-white shadow-md text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fas fa-bell"></i>
            <?php if (!empty($unread_notifications_count) && $unread_notifications_count > 0): ?>
                <span class="absolute top-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-red-500 text-xs text-white flex items-center justify-center"><?= $unread_notifications_count ?></span>
            <?php endif; ?>
        </button>

        <!-- Notifications Dropdown (Hidden by default, show with JS) -->
        <div id="notification-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg z-50 hidden max-h-80 overflow-y-auto">
            <div class="p-4 border-b border-gray-200">
                <h4 class="text-lg font-semibold text-gray-800">Notifikasi Anda</h4>
            </div>
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification):
                    $tipeLog = strtoupper($notification['tipe_log'] ?? 'INFORMASI');
                    $badgeClass = '';
                    switch ($tipeLog) {
                        case 'APPROVAL':
                        case 'PENCAIRAN':
                            $badgeClass = 'bg-green-100 text-green-800';
                            break;
                        case 'REJECTION':
                            $badgeClass = 'bg-red-100 text-red-800';
                            break;
                        case 'REVISION':
                            $badgeClass = 'bg-yellow-100 text-yellow-800';
                            break;
                        default:
                            $badgeClass = 'bg-blue-100 text-blue-800';
                            break;
                    }
                ?>
                    <a href="<?= htmlspecialchars($notification['link'] ?? '#') ?>" class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 <?= (isset($notification['status']) && $notification['status'] === 'BELUM_DIBACA') ? 'bg-blue-50' : '' ?>">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($notification['judul'] ?? 'Notifikasi') ?></p>
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold <?= $badgeClass ?>"><?= htmlspecialchars($tipeLog) ?></span>
                        </div>
                        <p class="text-xs text-gray-600 mt-1"><?= htmlspecialchars($notification['pesan'] ?? '') ?></p>
                        <p class="text-xs text-gray-400 mt-1"><?= htmlspecialchars($notification['created_at'] ?? '') ?></p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="px-4 py-3 text-sm text-gray-500">Tidak ada notifikasi baru.</div>
            <?php endif; ?>
        </div>
    </div>

    <!-- JavaScript for Notification Dropdown Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const bell = document.getElementById('notification-bell');
            const dropdown = document.getElementById('notification-dropdown');

            bell.addEventListener('click', function (event) {
                event.stopPropagation(); // Prevent document click from closing it immediately
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function (event) {
                if (!dropdown.classList.contains('hidden') && !bell.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>

    <h1 class="text-2xl font-bold mb-4">Super Admin Dashboard</h1>
    <p>This is a placeholder dashboard view for Super Admin. Notifications are integrated.</p>

    <!-- Stats, KAK/LPJ lists, etc. from controller could go here -->
    <?php if (isset($stats)): ?>
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">Stats:</h2>
            <pre><?php print_r($stats); ?></pre>
        </div>
    <?php endif; ?>

    <?php if (isset($list_prodi)): ?>
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">List Prodi:</h2>
            <pre><?php print_r($list_prodi); ?></pre>
        </div>
    <?php endif; ?>

    <?php if (isset($list_kak)): ?>
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">List KAK:</h2>
            <pre><?php print_r($list_kak); ?></pre>
        </div>
    <?php endif; ?>

    <?php if (isset($list_lpj)): ?>
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">List LPJ:</h2>
            <pre><?php print_r($list_lpj); ?></pre>
        </div>
    <?php endif; ?>

</main>