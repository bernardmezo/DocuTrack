<?php

namespace App\ViewModels;

use App\Services\KegiatanService;
use App\Services\LpjService;

class DashboardViewModel
{
    private $kegiatanService;
    private $lpjService;

    // Static configuration for UI steps
    private $tahapan_kak = [
        'Draft',
        'Verifikasi',
        'Validasi',
        'Persetujuan',
        'Disetujui'
    ];

    private $icons_kak = [
        'Draft' => 'fa-pencil-alt',
        'Verifikasi' => 'fa-search',
        'Validasi' => 'fa-clipboard-check',
        'Persetujuan' => 'fa-stamp',
        'Disetujui' => 'fa-check-circle'
    ];

    private $tahapan_lpj = [
        'Menunggu Upload',
        'Verifikasi',
        'Revisi',
        'Disetujui'
    ];

    private $icons_lpj = [
        'Menunggu Upload' => 'fa-upload',
        'Verifikasi' => 'fa-search',
        'Revisi' => 'fa-edit',
        'Disetujui' => 'fa-check-double'
    ];

    public function __construct(KegiatanService $kegiatanService, LpjService $lpjService)
    {
        $this->kegiatanService = $kegiatanService;
        $this->lpjService = $lpjService;
    }

    public function getDashboardData($userId, $jurusan = null)
    {
        // 1. Get Statistics
        $stats = $this->kegiatanService->getDashboardStats();

        // 2. Get Lists
        $list_kak = $this->kegiatanService->getDashboardKAK($jurusan);
        $list_lpj = $this->lpjService->getDashboardLPJ();

        // 3. Determine Current Global Status (Mock logic for dashboard visualization)
        // In a real app, this might be aggregated or specific to the user's latest item
        $tahap_sekarang_kak = 'Verifikasi';
        if (!empty($list_kak)) {
             // Example: Take the status of the most recent item
             $latest = $list_kak[0];
             $tahap_sekarang_kak = $latest['status_text'] ?? 'Draft';
        }

        $tahap_sekarang_lpj = 'Menunggu Upload';
        if (!empty($list_lpj)) {
             $latest = $list_lpj[0];
             $tahap_sekarang_lpj = $latest['status'] ?? 'Menunggu Upload';
        }

        // 4. Return View Data Structure
        return [
            'stats' => $stats,
            'list_kak' => $list_kak,
            'list_lpj' => $list_lpj,

            // UI Configurations
            'tahapan_kak' => $this->tahapan_kak,
            'icons_kak' => $this->icons_kak,
            'tahap_sekarang_kak' => $tahap_sekarang_kak,

            'tahapan_lpj' => $this->tahapan_lpj,
            'icons_lpj' => $this->icons_lpj,
            'tahap_sekarang_lpj' => $tahap_sekarang_lpj
        ];
    }
}
