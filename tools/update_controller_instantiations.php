<?php
/**
 * Automated Script - Update Model Instantiations in Controllers
 * 
 * Replaces old pattern: $model = new adminModel();
 * With new DI pattern: $model = new adminModel($this->db);
 * 
 * @category Tools
 * @package  DocuTrack
 * @version  1.0.0
 */

// Configuration: Model patterns to update
$modelPatterns = [
    'adminModel',
    'ppkModel',
    'bendaharaModel',
    'verifikatorModel',
    'wadirModel',
    'superAdminModel',
    'LoginModel',
    'VerifikatorModel', // Note: capital V variant exists
];

// Find all controller files
$controllerPaths = [
    '../src/controllers/*.php',
    '../src/controllers/Admin/*.php',
    '../src/controllers/PPK/*.php',
    '../src/controllers/Bendahara/*.php',
    '../src/controllers/Verifikator/*.php',
    '../src/controllers/Wadir/*.php',
    '../src/controllers/Super_Admin/*.php',
];

$allFiles = [];
foreach ($controllerPaths as $pattern) {
    $files = glob($pattern);
    $allFiles = array_merge($allFiles, $files);
}

echo "🚀 Starting Controller Model Instantiation Update...\n\n";
echo "📁 Found " . count($allFiles) . " controller files\n\n";

$totalReplacements = 0;
$filesModified = 0;

foreach ($allFiles as $filePath) {
    $filename = basename($filePath);
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $fileReplacements = 0;
    
    foreach ($modelPatterns as $modelName) {
        // Pattern 1: $this->model = new ModelName();
        $pattern1 = "/(\\\$this->model\s*=\s*new\s+{$modelName}\s*\(\s*)\);/";
        $replacement1 = "$1\$this->db);";
        $content = preg_replace($pattern1, $replacement1, $content, -1, $count1);
        $fileReplacements += $count1;
        
        // Pattern 2: $model = new ModelName();
        $pattern2 = "/(\\\$model\s*=\s*new\s+{$modelName}\s*\(\s*)\);/";
        $replacement2 = "$1\$this->db);";
        $content = preg_replace($pattern2, $replacement2, $content, -1, $count2);
        $fileReplacements += $count2;
        
        // Pattern 3: $variableName = new ModelName();
        $pattern3 = "/(\\\$\w+Model\s*=\s*new\s+{$modelName}\s*\(\s*)\);/";
        $replacement3 = "$1\$this->db);";
        $content = preg_replace($pattern3, $replacement3, $content, -1, $count3);
        $fileReplacements += $count3;
    }
    
    if ($fileReplacements > 0) {
        // Backup original
        $backupPath = $filePath . '.backup_' . date('YmdHis');
        file_put_contents($backupPath, $originalContent);
        
        // Save updated content
        file_put_contents($filePath, $content);
        
        echo "✅ $filename\n";
        echo "   ├─ Replacements: $fileReplacements\n";
        echo "   └─ Backup: " . basename($backupPath) . "\n\n";
        
        $totalReplacements += $fileReplacements;
        $filesModified++;
    }
}

echo "\n🎉 Update Complete!\n\n";
echo "📊 Summary:\n";
echo "   - Files scanned: " . count($allFiles) . "\n";
echo "   - Files modified: $filesModified\n";
echo "   - Total replacements: $totalReplacements\n";
echo "   - Backups created with .backup_* extension\n\n";
echo "⚠️  IMPORTANT: Review changes and test all affected controllers!\n";
