<?php
session_start();

// Pastikan GD aktif
if (!function_exists('imagecreatetruecolor')) {
    die('GD Library tidak aktif di server!');
}

// Ukuran gambar
$width = 150;
$height = 50;

// Buat gambar
$image = imagecreatetruecolor($width, $height);

// Warna
$bg_color = imagecolorallocate($image, 245, 245, 245);
$text_color = imagecolorallocate($image, 0, 0, 0);
$noise_color = imagecolorallocate($image, 150, 150, 150);

// Background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Tambah sedikit noise (titik-titik)
for ($i = 0; $i < 50; $i++) {
    imageellipse($image, rand(0, $width), rand(0, $height), 1, 1, $noise_color);
}

// Generate kode acak (5 karakter)
$captcha_code = substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789'), 0, 5);
$_SESSION['captcha_code'] = $captcha_code;

// Tulis teks di tengah
$font_file = __DIR__ . '/arial.ttf'; // pastikan arial.ttf ada di folder public/
if (file_exists($font_file)) {
    $font_size = 20;
    $textbox = imagettfbbox($font_size, 0, $font_file, $captcha_code);
    $x = ($width - ($textbox[2] - $textbox[0])) / 2;
    $y = ($height - ($textbox[5] - $textbox[1])) / 2 + 35;
    imagettftext($image, $font_size, 0, $x, $y, $text_color, $font_file, $captcha_code);
} else {
    // fallback jika tidak ada font TTF
    imagestring($image, 5, 40, 18, $captcha_code, $text_color);
}

// Header dan output
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
exit;
