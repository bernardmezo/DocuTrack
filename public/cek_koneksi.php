<?php
/**
 * DocuTrack - Database Connection Check & Setup
 * ==============================================
 * Akses: http://localhost/DocuTrack/public/cek_koneksi.php
 * 
 * Fitur:
 * - Cek koneksi ke MySQL
 * - Auto-setup database jika belum ada
 * - Tampilkan status tabel
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Config
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'db_docutrack2';

// Check if user wants to run setup
$runSetup = isset($_GET['setup']) && $_GET['setup'] === 'true';

?>
<!DOCTYPE html>
<html>
<head>
    <title>DocuTrack - Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 20px; border-radius: 10px; margin: 15px 0; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .success { color: #2e7d32; }
        .error { color: #c62828; }
        .warning { color: #ef6c00; }
        .info { color: #1565c0; }
        h1 { color: #333; }
        h2 { color: #555; margin-top: 0; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #4CAF50; color: white; }
        tr:nth-child(even) { background: #f9f9f9; }
        .btn { display: inline-block; padding: 12px 24px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin: 5px; }
        .btn:hover { background: #45a049; }
        .btn-danger { background: #f44336; }
        .btn-danger:hover { background: #da190b; }
        .status-badge { padding: 5px 10px; border-radius: 3px; font-size: 12px; }
        .status-ok { background: #e8f5e9; color: #2e7d32; }
        .status-missing { background: #ffebee; color: #c62828; }
    </style>
</head>
<body>
    <h1>🗄️ DocuTrack Database Setup</h1>

<?php
// Run setup if requested
if ($runSetup) {
    require_once '../src/model/db_setup.php';
    $setup = new DatabaseSetup($host, $user, $pass, $db);
    $setup->run();
    echo "<div class='card'><p class='success'>✅ Setup completed! <a href='cek_koneksi.php'>Refresh to check status</a></p></div>";
    exit;
}

echo "<div class='card'>";
echo "<h2>📊 Connection Status</h2>";
echo "<table>";
echo "<tr><td>Host</td><td><b>$host</b></td></tr>";
echo "<tr><td>User</td><td><b>$user</b></td></tr>";
echo "<tr><td>Database</td><td><b>$db</b></td></tr>";
echo "</table>";
echo "</div>";

// 1. Test MySQL connection
$conn = @mysqli_connect($host, $user, $pass);

if (!$conn) {
    echo "<div class='card'>";
    echo "<h2 class='error'>❌ MySQL Connection Failed</h2>";
    echo "<p>Error: " . mysqli_connect_error() . "</p>";
    echo "<p><b>Solutions:</b></p>";
    echo "<ul>";
    echo "<li>Start XAMPP MySQL from Control Panel</li>";
    echo "<li>Check if port 3306 is available</li>";
    echo "<li>Verify root password</li>";
    echo "</ul>";
    echo "</div>";
    exit;
}

echo "<div class='card'><p class='success'>✅ MySQL Server Connected</p></div>";

// 2. Check database
$db_exists = mysqli_select_db($conn, $db);

if (!$db_exists) {
    echo "<div class='card'>";
    echo "<h2 class='warning'>⚠️ Database Not Found</h2>";
    echo "<p>Database '$db' does not exist.</p>";
    echo "<a href='?setup=true' class='btn'>🚀 Run Auto-Setup</a>";
    echo "</div>";
    exit;
}

echo "<div class='card'><p class='success'>✅ Database '$db' Connected</p></div>";

// 3. Check tables
echo "<div class='card'>";
echo "<h2>📋 Tables Status</h2>";

// OPTIMIZED: 
// - tbl_posisi merged into tbl_role
// - tbl_rancangan_kegiatan removed (duplicate)
// - tbl_log_actions merged into tbl_activity_logs
$expectedTables = [
    'tbl_role', 'tbl_status_utama', 'tbl_wadir', 'tbl_kategori_rab',
    'tbl_jurusan', 'tbl_prodi', 'tbl_user', 'tbl_kegiatan', 'tbl_kak',
    'tbl_indikator_kak', 'tbl_tahapan_pelaksanaan', 'tbl_rab',
    'tbl_lpj', 'tbl_lpj_item', 'tbl_progress_history', 'tbl_revisi_comment',
    'tbl_activity_logs'
];

$result = mysqli_query($conn, "SHOW TABLES");
$existingTables = [];
while ($row = mysqli_fetch_array($result)) {
    $existingTables[] = $row[0];
}

echo "<table>";
echo "<tr><th>Table</th><th>Status</th><th>Row Count</th></tr>";

$missingTables = 0;
foreach ($expectedTables as $table) {
    $exists = in_array($table, $existingTables);
    $status = $exists ? "<span class='status-badge status-ok'>✓ OK</span>" : "<span class='status-badge status-missing'>✗ Missing</span>";
    
    $count = '-';
    if ($exists) {
        $countResult = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM `$table`");
        if ($countResult) {
            $countRow = mysqli_fetch_assoc($countResult);
            $count = $countRow['cnt'];
        }
    } else {
        $missingTables++;
    }
    
    echo "<tr><td>$table</td><td>$status</td><td>$count</td></tr>";
}
echo "</table>";

if ($missingTables > 0) {
    echo "<p class='warning'>⚠️ $missingTables tables are missing.</p>";
    echo "<a href='?setup=true' class='btn'>🔧 Run Setup to Create Missing Tables</a>";
}
echo "</div>";

// 4. Show users
echo "<div class='card'>";
echo "<h2>👥 Users</h2>";

$userResult = mysqli_query($conn, "SELECT u.*, r.namaRole FROM tbl_user u JOIN tbl_role r ON u.roleId = r.roleId ORDER BY u.roleId, u.userId");

if ($userResult && mysqli_num_rows($userResult) > 0) {
    echo "<table>";
    echo "<tr><th>Email</th><th>Name</th><th>Role</th><th>Jurusan</th></tr>";
    while ($row = mysqli_fetch_assoc($userResult)) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
        echo "<td>" . htmlspecialchars($row['namaRole']) . "</td>";
        echo "<td>" . ($row['namaJurusan'] ?? '-') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p class='info'>💡 All passwords: <code>password123</code></p>";
} else {
    echo "<p class='warning'>No users found. <a href='?setup=true' class='btn'>Run Setup</a></p>";
}
echo "</div>";

// 5. Actions
echo "<div class='card'>";
echo "<h2>🔧 Actions</h2>";
echo "<a href='?setup=true' class='btn'>🚀 Re-run Full Setup</a>";
echo "<a href='index.php' class='btn'>🏠 Go to App</a>";
echo "<a href='../index.html' class='btn'>📄 Landing Page</a>";
echo "</div>";

mysqli_close($conn);
?>

</body>
</html>