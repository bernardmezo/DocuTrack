<?php

// Load Bootstrap (untuk load .env dan autoloader)
require_once __DIR__ . '/../src/bootstrap.php';

use App\Core\Mailer;

// Cek apakah script dijalankan via CLI
if (php_sapi_name() !== 'cli') {
    die("Script ini hanya boleh dijalankan melalui CLI.");
}

echo "========================================\n";
echo "  DocuTrack SMTP Email Tester\n";
echo "========================================\n";

// Ambil argumen email tujuan
$to = $argv[1] ?? null;

if (!$to) {
    echo "Usage: php tools/test_email.php <email_tujuan>\n";
    echo "Contoh: php tools/test_email.php email.saya@gmail.com\n";
    exit(1);
}

echo "Mencoba mengirim email ke: {$to} ...\n";
echo "SMTP Host: " . $_ENV['SMTP_HOST'] . "\n";
echo "SMTP Port: " . $_ENV['SMTP_PORT'] . "\n";
echo "SMTP User: " . $_ENV['SMTP_USER'] . "\n";

$mailer = new Mailer();

// Data dummy untuk view
$data = [
    'nama_penerima' => 'Tester',
    'pesan_pembuka' => 'Ini adalah email tes untuk memverifikasi konfigurasi SMTP DocuTrack.',
    'status_color_class' => 'bg-blue',
    'status_label' => 'TESTING',
    'detail_kegiatan' => [
        'namaKegiatan' => 'Tes Koneksi SMTP',
        'pemilikKegiatan' => 'System Administrator',
        'createdAt' => date('Y-m-d H:i:s')
    ],
    'catatan_tambahan' => 'Jika Anda menerima email ini, berarti konfigurasi SMTP sudah benar.',
    'link_action' => 'http://localhost/docutrack/public'
];

if ($mailer->send($to, '[DocuTrack] Test Email Connection', 'notification', $data)) {
    echo "\n[BERHASIL] Email berhasil dikirim!\n";
    echo "Silakan cek inbox (atau folder spam) Anda.\n";
} else {
    echo "\n[GAGAL] Email gagal dikirim. Cek log error atau konfigurasi .env Anda.\n";
}

