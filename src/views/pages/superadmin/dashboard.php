<?php
// File: src/views/pages/superadmin/dashboard.php
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

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