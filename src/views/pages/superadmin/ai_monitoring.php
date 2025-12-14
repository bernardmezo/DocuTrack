<?php
// File: src/views/pages/superadmin/ai_monitoring.php
?>

<main class="main-content font-poppins p-4 md:p-7 -mt-8 md:-mt-20 max-w-7xl mx-auto w-full">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-robot text-blue-600 mr-2"></i>AI & Security Monitoring
        </h1>
    </div>

    <?php if (isset($error_message) && $error_message): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700"><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Section 1: System Status -->
    <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Card A: Threat Detection Engine -->
        <?php
            $mode = $security_mode ?? 'off';
            $modeColors = [
                'silent' => 'blue',
                'block' => 'green',
                'off' => 'gray'
            ];
            $curColor = $modeColors[$mode];
            $modeLabels = [
                'silent' => 'Silent Logging',
                'block' => 'Active Blocking',
                'off' => 'Disabled'
            ];
        ?>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-<?php echo $curColor; ?>-500">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">Threat Detection Engine</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $modeLabels[$mode]; ?></h3>
                    <p class="text-xs text-<?php echo $curColor; ?>-600 mt-2 flex items-center">
                        <span class="w-2 h-2 bg-<?php echo $curColor; ?>-500 rounded-full mr-1 <?php echo $mode !== 'off' ? 'animate-pulse' : ''; ?>"></span>
                        Mode: <?php echo ucfirst($mode); ?>
                    </p>
                </div>
                <div class="p-4 bg-<?php echo $curColor; ?>-50 rounded-full text-<?php echo $curColor; ?>-600">
                    <i class="fas fa-shield-alt fa-2x"></i>
                </div>
            </div>
            
            <!-- Controls -->
            <div class="border-t border-gray-100 pt-3 flex gap-2">
                <form action="/docutrack/public/superadmin/ai-monitoring/toggle" method="POST" class="contents">
                    <button name="mode" value="silent" class="flex-1 px-3 py-1.5 text-xs font-medium rounded border <?php echo $mode === 'silent' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'; ?>">
                        Silent
                    </button>
                    <button name="mode" value="block" class="flex-1 px-3 py-1.5 text-xs font-medium rounded border <?php echo $mode === 'block' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'; ?>">
                        Block
                    </button>
                    <button name="mode" value="off" class="flex-1 px-3 py-1.5 text-xs font-medium rounded border <?php echo $mode === 'off' ? 'bg-gray-100 text-gray-700 border-gray-300' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50'; ?>">
                        Off
                    </button>
                </form>
            </div>
        </div>

        <!-- Card B: AI Intelligence API -->
        <?php 
            $apiKeyExists = !empty($_ENV['GEMINI_API_KEY']);
            $apiStatus = $apiKeyExists ? 'Connected' : 'Disconnected';
            $apiColor = $apiKeyExists ? 'green' : 'red';
            $apiIcon = $apiKeyExists ? 'fa-brain' : 'fa-unlink';
        ?>
        <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-<?php echo $apiColor; ?>-500 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wider">AI Intelligence API</p>
                <h3 class="text-2xl font-bold text-gray-800 mt-1"><?php echo $apiStatus; ?></h3>
                <?php if (!$apiKeyExists): ?>
                    <p class="text-xs text-red-500 mt-2">Check .env configuration</p>
                <?php else: ?>
                    <p class="text-xs text-green-600 mt-2">Gemini Pro Model Ready</p>
                <?php endif; ?>
            </div>
            <div class="p-4 bg-<?php echo $apiColor; ?>-50 rounded-full text-<?php echo $apiColor; ?>-600">
                <i class="fas <?php echo $apiIcon; ?> fa-2x"></i>
            </div>
        </div>
    </section>

    <!-- Section 2: Daily Log Insight -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-microchip text-purple-600"></i>
                Daily Error Analysis
            </h3>
            <form action="/docutrack/public/superadmin/ai-monitoring/scan" method="POST">
                <button type="submit" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:text-blue-600 transition-all duration-200 shadow-sm flex items-center gap-2">
                    <i class="fas fa-sync-alt text-xs"></i>
                    Refresh Analysis
                </button>
            </form>
        </div>
        <div class="p-6">
            <div class="bg-gray-50 rounded-lg p-5 border border-gray-200 font-mono text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">
                <?php if (isset($summary) && $summary): ?>
                    <?php echo htmlspecialchars($summary['summary_text']); ?>
                    <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between text-xs text-gray-500">
                        <span>Last analyzed: <?php echo htmlspecialchars($summary['created_at']); ?></span>
                        <span>Error Count: <?php echo htmlspecialchars($summary['error_count']); ?></span>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-400">
                        <i class="fas fa-robot fa-3x mb-3 opacity-50"></i>
                        <p>No analysis generated yet. Click "Refresh Analysis" to start.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Section 3: Recent Threats -->
    <section class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle text-orange-500"></i>
                Intercepted Threats
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Severity</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Payload Snippet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (isset($alerts) && count($alerts) > 0): ?>
                        <?php foreach ($alerts as $alert): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">
                                    <?php echo date('Y-m-d H:i', strtotime($alert['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                    <?php echo htmlspecialchars($alert['ip_address']); ?>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-800 font-medium">
                                    <?php echo htmlspecialchars($alert['detection_type']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $severityClass = 'bg-gray-100 text-gray-800';
                                        if ($alert['severity'] === 'high') $severityClass = 'bg-red-100 text-red-700';
                                        elseif ($alert['severity'] === 'medium') $severityClass = 'bg-yellow-100 text-yellow-800';
                                    ?>
                                    <span class="px-2 py-1 text-xs font-bold rounded-full uppercase <?php echo $severityClass; ?>">
                                        <?php echo htmlspecialchars($alert['severity']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate font-mono" title="<?php echo htmlspecialchars($alert['input_payload']); ?>">
                                    <?php echo htmlspecialchars(substr($alert['input_payload'], 0, 50)) . (strlen($alert['input_payload']) > 50 ? '...' : ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-400">
                                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-check text-green-500 text-2xl"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-600">All Clear</p>
                                    <p class="text-sm">No threats detected in recent activity.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

</main>
