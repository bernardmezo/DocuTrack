<?php
// Function to get the base URL
function getBaseUrl_404() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $script_name = $_SERVER['SCRIPT_NAME'];
    $dir = dirname($script_name);
    // Ensure the directory path ends with a slash
    $base_path = rtrim($dir, '/\') . '/';
    return $protocol . $host . $base_path;
}
$baseUrl = getBaseUrl_404();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center bg-white p-12 rounded-lg shadow-lg">
        <h1 class="text-6xl font-bold text-blue-500">404</h1>
        <h2 class="text-2xl font-semibold text-gray-800 mt-4">Halaman Tidak Ditemukan</h2>
        <p class="text-gray-600 mt-2">Maaf, halaman yang Anda cari tidak ada atau telah dipindahkan.</p>
        <a href="<?= $baseUrl ?>" class="mt-6 inline-block bg-blue-500 text-white font-semibold px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">Kembali ke Beranda</a>
    </div>
</body>
</html>
