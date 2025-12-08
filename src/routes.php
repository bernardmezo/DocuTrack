<?php

// src/routes.php

return [
    '/' => [
        'controller' => 'HomeController',
        'method'     => 'index',
        'middleware' => [],
    ],
    '/login' => [
        'controller' => 'AuthController',
        'method'     => 'handleLogin',
        'middleware' => [],
        'methods'    => ['POST'],
    ],
    '/logout' => [
        'controller' => 'AuthController',
        'method'     => 'logout',
        'middleware' => [],
    ],
    // Health Check routes (replaces direct access files like cek_koneksi.php)
    '/health-check' => [
        'controller' => 'HealthCheckController',
        'method'     => 'index',
        'middleware' => [], // Bisa ditambahkan ['AuthMiddleware', 'SuperAdminMiddleware'] untuk keamanan
    ],
    '/health-check/api' => [
        'controller' => 'HealthCheckController',
        'method'     => 'api',
        'middleware' => [],
    ],

    // API routes for Notifications
    '/api/notifikasi' => [
        'controller' => 'Api\NotifikasiController',
        'method'     => 'get',
        'middleware' => ['AuthMiddleware'],
        'methods'    => ['GET'],
    ],
    '/api/notifikasi/baca/{id}' => [
        'controller' => 'Api\NotifikasiController',
        'method'     => 'markAsRead',
        'middleware' => ['AuthMiddleware'],
        'methods'    => ['POST'],
    ],
    '/api/notifikasi/baca-semua' => [
        'controller' => 'Api\NotifikasiController',
        'method'     => 'markAllAsRead',
        'middleware' => ['AuthMiddleware'],
        'methods'    => ['POST'],
    ],

    // Admin routes
    '/admin/dashboard' => [
        'controller' => 'Admin\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/akun' => [
        'controller' => 'Admin\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/akun/update' => [
        'controller' => 'Admin\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    // Example of a dynamic route
    '/admin/detail-kak/show/{id}/{ref?}' => [
        'controller' => 'Admin\DetailKakController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-usulan' => [
        'controller' => 'Admin\PengajuanUsulanController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-usulan/detail/{id}' => [
        'controller' => 'Admin\PengajuanUsulanController',
        'method'     => 'detail',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-usulan/edit/{id}' => [
        'controller' => 'Admin\PengajuanUsulanController',
        'method'     => 'edit',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-usulan/update/{id}' => [
        'controller' => 'Admin\PengajuanUsulanController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-usulan/delete/{id}' => [
        'controller' => 'Admin\PengajuanUsulanController',
        'method'     => 'delete',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-usulan/store' => [
        'controller' => 'Admin\PengajuanUsulanController',
        'method'     => 'store',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-kegiatan/submitRincian' => [
        'controller' => 'Admin\AdminController',
        'method'     => 'submitRincian',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-kegiatan/submitrincian' => [
        'controller' => 'Admin\AdminController',
        'method'     => 'submitRincian',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-kegiatan' => [
        'controller' => 'Admin\PengajuanKegiatanController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-kegiatan/show/{id}' => [
        'controller' => 'Admin\PengajuanKegiatanController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-kegiatan/pdf/{id}' => [
        'controller' => 'Admin\PengajuanKegiatanController',
        'method'     => 'downloadPDF',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-lpj' => [
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-lpj/show/{id}' => [
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
    ],
    '/admin/pengajuan-lpj/verifikasi/{id}' => [
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'verifikasi',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-lpj/tolak/{id}' => [
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'tolak',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-lpj/upload-bukti' => [ // This is a specific action, not necessarily by ID
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'uploadBukti',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-lpj/submit' => [ // Specific action
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'submitLpj',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/admin/pengajuan-lpj/submit-revisi' => [ // Specific action
        'controller' => 'Admin\PengajuanLpjController',
        'method'     => 'submitRevisi',
        'middleware' => ['AuthMiddleware', 'AdminMiddleware'],
        'methods'    => ['POST'],
    ],

    // Verifikator routes
    '/verifikator/dashboard' => [
        'controller' => 'Verifikator\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/akun' => [
        'controller' => 'Verifikator\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/akun/update' => [
        'controller' => 'Verifikator\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
        'methods'    => ['POST'],
    ],
    '/verifikator/pengajuan-telaah' => [
        'controller' => 'Verifikator\TelaahController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/telaah/show/{id}' => [
        'controller' => 'Verifikator\TelaahController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/telaah/approve/{id}' => [
        'controller' => 'Verifikator\TelaahController',
        'method'     => 'approve',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/telaah/reject/{id}' => [
        'controller' => 'Verifikator\TelaahController',
        'method'     => 'reject',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/telaah/revise/{id}' => [
        'controller' => 'Verifikator\TelaahController',
        'method'     => 'revise',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],
    '/verifikator/riwayat-verifikasi' => [
        'controller' => 'Verifikator\RiwayatController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'VerifikatorMiddleware'],
    ],

    // Wadir routes
    '/wadir/dashboard' => [
        'controller' => 'Wadir\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/akun' => [
        'controller' => 'Wadir\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/akun/update' => [
        'controller' => 'Wadir\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
        'methods'    => ['POST'],
    ],
    '/wadir/pengajuan-kegiatan' => [
        'controller' => 'Wadir\PengajuanKegiatanController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/telaah/show/{id}' => [
        'controller' => 'Wadir\TelaahController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/telaah/approve/{id}' => [
        'controller' => 'Wadir\TelaahController',
        'method'     => 'approve',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/telaah/reject/{id}' => [
        'controller' => 'Wadir\TelaahController',
        'method'     => 'reject',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
        'methods'    => ['POST'],
    ],
    '/wadir/telaah/revise/{id}' => [
        'controller' => 'Wadir\TelaahController',
        'method'     => 'revise',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
        'methods'    => ['POST'],
    ],
    '/wadir/monitoring' => [
        'controller' => 'Wadir\MonitoringController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/monitoring/data' => [
        'controller' => 'Wadir\MonitoringController',
        'method'     => 'getData',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],
    '/wadir/riwayat-verifikasi' => [
        'controller' => 'Wadir\RiwayatController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'WadirMiddleware'],
    ],

    // PPK routes
    '/ppk/dashboard' => [
        'controller' => 'PPK\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/akun' => [
        'controller' => 'PPK\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/akun/update' => [
        'controller' => 'PPK\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
        'methods'    => ['POST'],
    ],
    '/ppk/pengajuan-kegiatan' => [
        'controller' => 'PPK\PengajuanKegiatanController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/monitoring' => [
        'controller' => 'PPK\MonitoringController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/monitoring/data' => [
        'controller' => 'PPK\MonitoringController',
        'method'     => 'getData',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/riwayat-verifikasi' => [
        'controller' => 'PPK\RiwayatController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/telaah/show/{id}' => [
        'controller' => 'PPK\TelaahController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/telaah/show/{id}/{ref?}' => [
        'controller' => 'PPK\TelaahController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/telaah/approve/{id}' => [
        'controller' => 'PPK\TelaahController',
        'method'     => 'approve',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
    ],
    '/ppk/telaah/reject/{id}' => [
        'controller' => 'PPK\TelaahController',
        'method'     => 'reject',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
        'methods'    => ['POST'],
    ],
    '/ppk/telaah/revise/{id}' => [
        'controller' => 'PPK\TelaahController',
        'method'     => 'revise',
        'middleware' => ['AuthMiddleware', 'PpkMiddleware'],
        'methods'    => ['POST'],
    ],

    // Bendahara routes
    '/bendahara/dashboard' => [
        'controller' => 'Bendahara\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/akun' => [
        'controller' => 'Bendahara\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/akun/update' => [
        'controller' => 'Bendahara\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
        'methods'    => ['POST'],
    ],
    '/bendahara/pencairan-dana' => [
        'controller' => 'Bendahara\PencairandanaController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/pencairan-dana/show/{id}/{ref?}' => [
        'controller' => 'Bendahara\PencairandanaController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/pencairan-dana/show/{id}' => [
        'controller' => 'Bendahara\PencairandanaController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/pencairan-dana/proses' => [
        'controller' => 'Bendahara\PencairandanaController',
        'method'     => 'proses',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
        'methods'    => ['POST'],
    ],
    '/bendahara/pengajuan-lpj' => [
        'controller' => 'Bendahara\PengajuanLpjController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/pengajuan-lpj/show/{id}/{ref?}' => [
        'controller' => 'Bendahara\PengajuanLpjController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/pengajuan-lpj/proses' => [
        'controller' => 'Bendahara\PengajuanLpjController',
        'method'     => 'proses',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
        'methods'    => ['POST'],
    ],
    '/bendahara/pengajuan-lpj/data' => [
        'controller' => 'Bendahara\PengajuanLpjController',
        'method'     => 'getLPJData',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
        'methods'    => ['GET'],
    ],
    '/bendahara/riwayat-verifikasi' => [
        'controller' => 'Bendahara\RiwayatverifikasiController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],
    '/bendahara/riwayat-verifikasi/show/{id}' => [
        'controller' => 'Bendahara\RiwayatverifikasiController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'BendaharaMiddleware'],
    ],

    // Super Admin routes
    '/superadmin' => [
        'controller' => 'SuperAdmin\SuperAdminController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/dashboard' => [
        'controller' => 'SuperAdmin\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/akun' => [
        'controller' => 'SuperAdmin\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/akun/update' => [
        'controller' => 'SuperAdmin\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/superadmin/kelola-akun' => [
        'controller' => 'SuperAdmin\KelolaakunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/kelola-akun/create' => [
        'controller' => 'SuperAdmin\KelolaakunController',
        'method'     => 'create',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/kelola-akun/store' => [
        'controller' => 'SuperAdmin\KelolaakunController',
        'method'     => 'store',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/superadmin/kelola-akun/show/{id}' => [
        'controller' => 'SuperAdmin\KelolaakunController',
        'method'     => 'show',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/kelola-akun/update/{id}' => [
        'controller' => 'SuperAdmin\KelolaakunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/superadmin/kelola-akun/delete/{id}' => [
        'controller' => 'SuperAdmin\KelolaakunController',
        'method'     => 'delete',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/superadmin/monitoring' => [
        'controller' => 'SuperAdmin\MonitoringController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/buat-iku' => [
        'controller' => 'SuperAdmin\BuatikuController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/buat-iku/create' => [
        'controller' => 'SuperAdmin\BuatikuController',
        'method'     => 'create',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/buat-iku/store' => [
        'controller' => 'SuperAdmin\BuatikuController',
        'method'     => 'store',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/superadmin/buat-iku/edit/{id}' => [
        'controller' => 'SuperAdmin\BuatikuController',
        'method'     => 'edit',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
    ],
    '/superadmin/buat-iku/update/{id}' => [
        'controller' => 'SuperAdmin\BuatikuController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],
    '/superadmin/buat-iku/delete/{id}' => [
        'controller' => 'SuperAdmin\BuatikuController',
        'method'     => 'delete',
        'middleware' => ['AuthMiddleware', 'SuperAdminMiddleware'],
        'methods'    => ['POST'],
    ],

    // Direktur routes
    '/direktur/dashboard' => [
        'controller' => 'Direktur\DashboardController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'DirekturMiddleware'], // Assuming DirekturMiddleware exists or will be created
    ],
    '/direktur/akun' => [
        'controller' => 'Direktur\AkunController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'DirekturMiddleware'],
    ],
    '/direktur/akun/update' => [
        'controller' => 'Direktur\AkunController',
        'method'     => 'update',
        'middleware' => ['AuthMiddleware', 'DirekturMiddleware'],
        'methods'    => ['POST'],
    ],
    '/direktur/monitoring' => [
        'controller' => 'Direktur\MonitoringController',
        'method'     => 'index',
        'middleware' => ['AuthMiddleware', 'DirekturMiddleware'],
    ],
];
