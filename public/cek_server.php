<?php
// File: public/cek_server.php

function return_bytes($val) {
    $val = trim($val);
    $last = strtolower($val[strlen($val)-1]);
    $val = (int)$val;
    switch($last) {
        case 'g': $val *= 1024;
        case 'm': $val *= 1024;
        case 'k': $val *= 1024;
    }
    return $val;
}

$max_upload = ini_get('upload_max_filesize');
$max_post = ini_get('post_max_size');

$max_upload_bytes = return_bytes($max_upload);
$max_post_bytes = return_bytes($max_post);
$limit_required = 5 * 1024 * 1024; // 5 MB

$upload_status = $max_upload_bytes >= $limit_required;
$post_status = $max_post_bytes >= $limit_required;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Konfigurasi Server</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-10 font-sans">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-4">Diagnosa Server (DocuTrack Requirements)</h1>
        
        <div class="space-y-4">
            <!-- Upload Max Filesize -->
            <div class="flex items-center justify-between p-4 rounded-lg border <?= $upload_status ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?>">
                <div>
                    <p class="font-semibold text-gray-700">upload_max_filesize</p>
                    <p class="text-sm text-gray-500">Batas ukuran file per upload</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold <?= $upload_status ? 'text-green-700' : 'text-red-700' ?>"><?= $max_upload ?></p>
                    <p class="text-xs <?= $upload_status ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $upload_status ? '✅ SUPPORT (Min 5MB)' : '❌ TIDAK SUPPORT (Butuh 5MB)' ?>
                    </p>
                </div>
            </div>

            <!-- Post Max Size -->
            <div class="flex items-center justify-between p-4 rounded-lg border <?= $post_status ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' ?>">
                <div>
                    <p class="font-semibold text-gray-700">post_max_size</p>
                    <p class="text-sm text-gray-500">Batas total data POST (termasuk file)</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold <?= $post_status ? 'text-green-700' : 'text-red-700' ?>"><?= $max_post ?></p>
                    <p class="text-xs <?= $post_status ? 'text-green-600' : 'text-red-600' ?>">
                        <?= $post_status ? '✅ SUPPORT (Min 5MB)' : '❌ TIDAK SUPPORT (Butuh 5MB)' ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="mt-8 p-4 bg-blue-50 text-blue-800 rounded-md text-sm">
            <strong>Rekomendasi:</strong><br>
            Jika status masih <span class="text-red-600 font-bold">MERAH</span>, silakan edit file <code>php.ini</code> di server Anda:<br>
            <code class="block mt-2 bg-gray-800 text-white p-2 rounded">
                upload_max_filesize = 10M<br>
                post_max_size = 10M
            </code>
            <br>
            Lalu restart Apache/XAMPP.
        </div>
        
        <div class="mt-6 text-center">
            <a href="/docutrack/public/cek_server.php" class="text-blue-600 hover:underline">Refresh Halaman</a>
        </div>
    </div>
</body>
</html>
