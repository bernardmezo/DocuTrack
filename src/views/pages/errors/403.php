<?php
// Function to get the base URL
function getBaseUrl_403() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $dir = dirname($script_name);
    // Ensure the directory path ends with a slash
    $base_path = rtrim($dir, '/\\') . '/';
    return $protocol . $host . $base_path;
}
$baseUrl = getBaseUrl_403();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    <?php echo '<script src="https://cdn.tailwindcss.com"></script>'; ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center bg-white p-12 rounded-lg shadow-lg">
        <h1 class="text-6xl font-bold text-red-500">403</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">Akses Ditolak</h2>
        <p class="text-gray-600 mt-2">Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.</p>
        <a href="<?= $baseUrl ?>" class="mt-6 inline-block bg-blue-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">Kembali ke Beranda</a>
    </div>
</body>
</html>
