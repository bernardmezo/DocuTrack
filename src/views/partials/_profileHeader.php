<?php
$user = $user ?? [];
$joinDate = $user['created_at'] ?? date('Y-m-d');
$date = new DateTime($joinDate);
$formattedDate = $date->format('l, j F Y');
?>
<div class="w-full mb-6">
    <div class="max-w-[924px] mx-auto">
        <div id="profileHeader" 
             class="full-cover-header rounded-3xl p-8 relative text-white card-shadow min-h-[250px] flex flex-col justify-center w-full">
            
            <div class="absolute inset-0 z-0 pointer-events-none overflow-hidden rounded-3xl">
                <img src="/docutrack/public/assets/images/icon/background-profile-header.svg" class="w-full h-full object-cover z-60" alt="">
            </div>
            
            <div class="relative z-10 w-full">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold mb-1 flex items-center gap-2 drop-shadow-md">
                            Hi <span class="inline-block animate-wave">👋</span>, <?php echo htmlspecialchars($user['username'] ?? 'User'); ?>
                        </h1>
                        <p class="text-white/90 text-sm font-medium mb-1 drop-shadow-md">Role</p>
                        <div class="inline-block bg-white/20 backdrop-blur-md px-3 py-1 rounded-lg border border-white/20 shadow-sm">
                            <p class="text-white text-sm font-semibold drop-shadow-sm"><?php echo htmlspecialchars($user['role'] ?? 'N/A'); ?></p>
                        </div>
                        <p class="mt-2 text-white/95 text-sm drop-shadow-md">
                            <i class="far fa-calendar-alt mr-1"></i> Bergabung: <?php echo $formattedDate; ?>
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <div class="text-right">
                            <p class="text-5xl font-bold tracking-tight drop-shadow-lg" id="serverTime">
                                <?php echo date('H:i'); ?>
                            </p>
                            <p class="text-sm text-white/90 mt-1 drop-shadow-md">Waktu Indonesia Barat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
