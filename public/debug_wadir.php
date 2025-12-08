<!DOCTYPE html>
<html>
<head>
    <title>Wadir Login Debug</title>
</head>
<body>
    <h1>Wadir Login & Access Test</h1>
    
    <?php
    session_start();
    
    if (isset($_POST['action']) && $_POST['action'] == 'login') {
        // Simulate Wadir login
        $_SESSION['user_id'] = 10;
        $_SESSION['role_id'] = 3;
        $_SESSION['nama'] = 'Wakil Direktur';
        $_SESSION['email'] = 'wadir@gmail.com';
        echo "<p style='color: green;'>✓ Login sebagai Wadir berhasil!</p>";
    }
    
    if (isset($_POST['action']) && $_POST['action'] == 'logout') {
        session_destroy();
        session_start();
        echo "<p style='color: orange;'>✓ Logout berhasil!</p>";
    }
    ?>
    
    <h2>Current Session</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h2>Actions</h2>
    <form method="POST">
        <button type="submit" name="action" value="login">Login as Wadir</button>
        <button type="submit" name="action" value="logout">Logout</button>
    </form>
    
    <h2>Test URLs</h2>
    <?php if (isset($_SESSION['user_id'])): ?>
        <ul>
            <li><a href="/docutrack/public/wadir/dashboard">Dashboard Wadir</a></li>
            <li><a href="/docutrack/public/wadir/telaah/show/1?ref=kegiatan" target="_blank">Telaah Kegiatan ID 1</a></li>
            <li><a href="/docutrack/public/wadir/pengajuan-kegiatan">Pengajuan Kegiatan</a></li>
        </ul>
    <?php else: ?>
        <p>Silakan login terlebih dahulu</p>
    <?php endif; ?>
</body>
</html>
