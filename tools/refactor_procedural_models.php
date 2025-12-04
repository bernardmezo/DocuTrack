<?php
/**
 * Batch Refactor Script - Convert Procedural Models to OOP with DI
 * 
 * This script automates conversion of procedural functions with global $conn
 * to OOP class with dependency injection pattern.
 * 
 * @category Tools
 * @package  DocuTrack
 * @version  1.0.0
 */

// Configuration
$modelsToRefactor = [
    'rab' => '../src/model/rab/rabModel.php',
    'pengusul' => '../src/model/pengusul/pengusulModel.php',
    'rancangan_kegiatan' => '../src/model/rancangan_kegiatan/rancanganganKegModel.php',
    'kak' => '../src/model/kak/kakModel.php',
    'prodi' => '../src/model/prodi/prodiModel.php',
    'lpj' => '../src/model/lpj/lpjModel.php',
    'pelaksana' => '../src/model/pelaksana/pelaksanaModel.php',
    'kegiatan' => '../src/model/kegiaitan/kegiatanModel.php',
];

echo "🚀 Starting Batch Refactoring Process...\n\n";

foreach ($modelsToRefactor as $modelName => $filePath) {
    echo "📁 Processing: $modelName ($filePath)\n";
    
    if (!file_exists($filePath)) {
        echo "   ⚠️  File not found, skipping...\n\n";
        continue;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    
    // Count global $conn occurrences
    $globalCount = substr_count($content, 'global $conn');
    echo "   Found $globalCount instances of 'global \$conn'\n";
    
    // Pattern 1: Replace "global $conn;" with "$conn = \$this->db;"
    $content = preg_replace(
        '/^\s*global\s+\$conn;\s*$/m',
        '        $conn = $this->db; // Refactored: use instance property instead of global',
        $content
    );
    
    $changedCount = substr_count($content, 'Refactored: use instance property instead of global');
    echo "   ✅ Replaced $changedCount instances\n";
    
    // Backup original file
    $backupPath = $filePath . '.backup_' . date('YmdHis');
    file_put_contents($backupPath, $originalContent);
    echo "   💾 Backup created: $backupPath\n";
    
    // Save refactored content
    file_put_contents($filePath, $content);
    echo "   ✨ Refactored file saved\n";
    
    echo "   ✅ $modelName completed!\n\n";
}

echo "🎉 Batch Refactoring Complete!\n\n";
echo "📊 Summary:\n";
echo "   - Total models processed: " . count($modelsToRefactor) . "\n";
echo "   - Backups created in same directory with .backup_* extension\n";
echo "   - Review changes with: git diff src/model/\n\n";
echo "⚠️  IMPORTANT: Test all affected functionality before committing!\n";
