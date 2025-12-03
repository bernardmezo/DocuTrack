<?php
/**
 * Automated Script - Wrap Procedural Models in OOP Classes
 * 
 * Converts loose procedural functions to OOP class methods with DI.
 * 
 * @category Tools
 * @package  DocuTrack
 * @version  1.0.0
 */

$modelsToWrap = [
    [
        'name' => 'pengusulModel',
        'path' => '../src/model/pengusul/pengusulModel.php',
        'description' => 'Pengusul Management Model'
    ],
    [
        'name' => 'rancanganganKegModel',
        'path' => '../src/model/rancangan_kegiatan/rancanganganKegModel.php',
        'description' => 'Rancangan Kegiatan Model'
    ],
    [
        'name' => 'kakModel',
        'path' => '../src/model/kak/kakModel.php',
        'description' => 'KAK (Kerangka Acuan Kerja) Model'
    ],
    [
        'name' => 'prodiModel',
        'path' => '../src/model/prodi/prodiModel.php',
        'description' => 'Prodi Management Model'
    ],
    [
        'name' => 'lpjModel',
        'path' => '../src/model/lpj/lpjModel.php',
        'description' => 'LPJ (Laporan Pertanggungjawaban) Model'
    ],
    [
        'name' => 'pelaksanaModel',
        'path' => '../src/model/pelaksana/pelaksanaModel.php',
        'description' => 'Pelaksana Management Model'
    ],
    [
        'name' => 'kegiatanModel',
        'path' => '../src/model/kegiaitan/kegiatanModel.php',
        'description' => 'Kegiatan Management Model'
    ],
];

echo "🚀 Starting Procedural to OOP Class Wrapping...\n\n";

foreach ($modelsToWrap as $model) {
    $name = $model['name'];
    $path = $model['path'];
    $desc = $model['description'];
    
    echo "📁 Processing: $name\n";
    echo "   Path: $path\n";
    
    if (!file_exists($path)) {
        echo "   ⚠️  File not found, skipping...\n\n";
        continue;
    }
    
    $content = file_get_contents($path);
    $originalContent = $content;
    
    // Backup
    $backupPath = $path . '.backup_oop_' . date('YmdHis');
    file_put_contents($backupPath, $originalContent);
    
    // Extract all function definitions
    preg_match_all('/if\s*\(\s*!function_exists\([\'"](\w+)[\'"]\)\s*\)\s*\{/s', $content, $functionMatches);
    $functionCount = count($functionMatches[1]);
    
    echo "   Found $functionCount procedural functions\n";
    
    // Pattern 1: Remove if (!function_exists('...')) { wrappers
    $content = preg_replace('/if\s*\(\s*!function_exists\([\'"](\w+)[\'"]\)\s*\)\s*\{\s*/', '', $content);
    
    // Pattern 2: Convert function to public method
    $content = preg_replace('/^\s*function\s+(\w+)\s*\(/m', '    public function $1(', $content);
    
    // Pattern 3: Remove closing braces of if wrappers (tricky - remove single standalone closing braces)
    $content = preg_replace('/^\s*\}\s*$/m', '', $content);
    
    // Pattern 4: Replace $conn = $this->db with $conn = $this->db (already done, just validate)
    
    // Pattern 5: Add class wrapper at beginning
    $classHeader = "<?php\n/**\n * $name - $desc\n * \n * @category Model\n * @package  DocuTrack\n * @version  2.0.0 - Converted from procedural to OOP\n */\n\nclass $name {\n    /**\n     * @var mysqli Database connection instance\n     */\n    private " . '$db' . ";\n\n    /**\n     * Constructor - Dependency Injection\n     *\n     * @param mysqli " . '$db' . " Database connection\n     */\n    public function __construct(" . '$db' . ") {\n        " . '$this->db = $db' . ";\n    }\n\n";
    
    // Remove old PHP opening tag
    $content = preg_replace('/^<\?php\s*/', '', $content);
    $content = preg_replace('/\/\*\*.*?\*\//s', '', $content, 1); // Remove old comment block
    
    // Remove trailing ?>
    $content = rtrim($content);
    $content = preg_replace('/\?>\s*$/', '', $content);
    
    // Wrap with class
    $content = $classHeader . trim($content) . "\n}";
    
    // Save
    file_put_contents($path, $content);
    
    echo "   ✅ Wrapped in class $name\n";
    echo "   💾 Backup: " . basename($backupPath) . "\n\n";
}

echo "🎉 OOP Wrapping Complete!\n\n";
echo "⚠️  CRITICAL: Manual review required for:\n";
echo "   - Verify function signatures converted correctly\n";
echo "   - Check closing braces alignment\n";
echo "   - Test all model instantiations\n";
echo "   - Run PHPStan analysis\n\n";
