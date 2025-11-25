<?php
// File: src/controllers/Admin/AdminPengajuanLpjController.php

require_once '../src/core/Controller.php';

class AdminPengajuanLpjController extends Controller {
    
    /**
     * Menampilkan HALAMAN LIST PENGAJUAN LPJ
     */
    public function index($data_dari_router = []) { 
        
        // Data dummy dengan PRODI dan JURUSAN
        $list_lpj_dummy = [
            [
                'id' => 1, 
                'nama' => 'Seminar Nasional Teknologi AI', 
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '190101001',
                'prodi' => 'D4 Teknik Informatika',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-16 days')),
                'status' => 'Setuju'
            ],
            [
                'id' => 2, 
                'nama' => 'Workshop UI/UX Design 2024', 
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '190101002',
                'prodi' => 'D4 Teknik Grafika dan Penerbitan',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'status' => 'Revisi'
            ],
            [
                'id' => 3,
                'nama' => 'Lomba Robotika Nasional',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '190101003',
                'prodi' => 'D4 Teknik Telekomunikasi',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'status' => 'Menunggu'
            ],
            [
                'id' => 4,
                'nama' => 'Pentas Seni dan Kewirausahaan',
                'nama_mahasiswa' => 'Dewi Lestari',
                'nim' => '190101004',
                'prodi' => 'D4 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'status' => 'Menunggu_Upload'
            ],
            [
                'id' => 5,
                'nama' => 'Kunjungan Industri Manufaktur',
                'nama_mahasiswa' => 'Riko Saputra',
                'nim' => '190101005',
                'prodi' => 'D3 Teknik Mesin',
                'jurusan' => 'Teknik Mesin',
                'tanggal_pengajuan' => date('Y-m-d H:i:s'),
                'status' => 'Menunggu_Upload'
            ],
            [
                'id' => 6,
                'nama' => 'Lomba Coding Tingkat Kampus',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '190101006',
                'prodi' => 'D4 Sistem Informasi Kota Cerdas',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'status' => 'Setuju'
            ],
            [
                'id' => 7,
                'nama' => 'Festival Musik Kampus',
                'nama_mahasiswa' => 'Linda Sari',
                'nim' => '190101007',
                'prodi' => 'D3 Administrasi Bisnis',
                'jurusan' => 'Administrasi Niaga',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'status' => 'Menunggu_Upload'
            ],
            [
                'id' => 8,
                'nama' => 'Bakti Sosial Lingkungan',
                'nama_mahasiswa' => 'Hendra Wijaya',
                'nim' => '190101008',
                'prodi' => 'D4 Teknik Elektronika',
                'jurusan' => 'Teknik Elektro',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-7 days')),
                'status' => 'Menunggu'
            ],
            [
                'id' => 9,
                'nama' => 'Workshop Machine Learning',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'nim' => '190101009',
                'prodi' => 'D4 Teknik Multimedia dan Jaringan',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-4 days')),
                'status' => 'Menunggu'
            ],
            [
                'id' => 10,
                'nama' => 'Pelatihan Fotografi Produk',
                'nama_mahasiswa' => 'Kartika Dewi',
                'nim' => '190101010',
                'prodi' => 'D3 Desain Grafis',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'tanggal_pengajuan' => date('Y-m-d H:i:s', strtotime('-1 week')),
                'status' => 'Setuju'
            ],
        ];
        
        $data = array_merge($data_dari_router, [
            'title' => 'List Pengajuan LPJ',
            'list_lpj' => $list_lpj_dummy 
        ]);

        $this->view('pages/admin/pengajuan_lpj_list', $data, 'app');
    }

    public function show($id, $data_dari_router = []) {
        
        $ref = $_GET['ref'] ?? 'lpj'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-lpj';

        $list_lpj_all = [
            1 => [
                'nama' => 'Seminar Nasional Teknologi AI', 
                'pengusul' => 'Budi Santoso', 
                'status' => 'Setuju'
            ],
            2 => [
                'nama' => 'Workshop UI/UX Design 2024', 
                'pengusul' => 'Siti Aminah', 
                'status' => 'Revisi'
            ],
            3 => [
                'nama' => 'Lomba Robotika Nasional', 
                'pengusul' => 'Andi Pratama', 
                'status' => 'Menunggu'
            ],
            4 => [
                'nama' => 'Pentas Seni dan Kewirausahaan', 
                'pengusul' => 'Dewi Lestari', 
                'status' => 'Menunggu_Upload'
            ],
            5 => [
                'nama' => 'Kunjungan Industri Manufaktur', 
                'pengusul' => 'Riko Saputra', 
                'status' => 'Menunggu_Upload'
            ],
        ];
        
        $kegiatan_dipilih = $list_lpj_all[$id] ?? null;
        if (!$kegiatan_dipilih) {
            header("Location: $back_url");
            exit;
        }
        
        $status = $kegiatan_dipilih['status'];

        $kegiatan_data_dummy = [
            'nama_kegiatan' => $kegiatan_dipilih['nama'],
            'pengusul' => $kegiatan_dipilih['pengusul']
        ];
        
        // RAB items based on status
        if (strtolower($status) === 'menunggu_upload') {
            $rab_items_merged = [
                'Belanja Jasa' => [
                    [
                        'id' => 'item_1', 
                        'uraian' => 'Sewa Sound System', 
                        'rincian' => 'Sound system lengkap dengan mic wireless',
                        'vol1' => '1',
                        'sat1' => 'Paket',
                        'vol2' => '1',
                        'sat2' => 'Hari',
                        'harga_satuan' => 500000,
                        'harga_plan' => 500000, 
                        'bukti_file' => null,
                        'komentar' => null
                    ],
                ],
                'Belanja Konsumsi' => [
                    [
                        'id' => 'item_2', 
                        'uraian' => 'Snack Peserta', 
                        'rincian' => 'Snack box + air mineral',
                        'vol1' => '50',
                        'sat1' => 'Pax',
                        'vol2' => '1',
                        'sat2' => 'Kali',
                        'harga_satuan' => 5000,
                        'harga_plan' => 250000, 
                        'bukti_file' => null,
                        'komentar' => null
                    ],
                ]
            ];
            $komentar_revisi = [];
            
        } elseif (strtolower($status) === 'menunggu') {
            $rab_items_merged = [
                'Belanja Jasa' => [
                    [
                        'id' => 'item_1', 
                        'uraian' => 'Sewa Sound System', 
                        'rincian' => 'Sound system lengkap dengan mic wireless',
                        'vol1' => '1',
                        'sat1' => 'Paket',
                        'vol2' => '1',
                        'sat2' => 'Hari',
                        'harga_satuan' => 480000,
                        'harga_plan' => 480000, 
                        'bukti_file' => 'bukti_sound_system.pdf',
                        'komentar' => null
                    ],
                ]
            ];
            $komentar_revisi = [];
            
        } elseif (strtolower($status) === 'revisi') {
            $rab_items_merged = [
                'Belanja Jasa' => [
                    [
                        'id' => 'item_1', 
                        'uraian' => 'Sewa Sound System', 
                        'rincian' => 'Sound system lengkap dengan mic wireless',
                        'vol1' => '1',
                        'sat1' => 'Paket',
                        'vol2' => '1',
                        'sat2' => 'Hari',
                        'harga_satuan' => 450000,
                        'harga_plan' => 450000, 
                        'bukti_file' => 'bukti_sound_system.pdf', 
                        'komentar' => 'Jumlah tidak sesuai dengan nota. Mohon cek kembali.'
                    ],
                ]
            ];
            $komentar_revisi = [
                'pesan_umum' => 'Mohon perbaiki item yang diberi komentar dan upload ulang bukti yang sesuai.'
            ];
            
        } else {
            $rab_items_merged = [
                'Belanja Jasa' => [
                    [
                        'id' => 'item_1', 
                        'uraian' => 'Sewa Sound System', 
                        'rincian' => 'Sound system lengkap dengan mic wireless',
                        'vol1' => '1',
                        'sat1' => 'Paket',
                        'vol2' => '1',
                        'sat2' => 'Hari',
                        'harga_satuan' => 500000,
                        'harga_plan' => 500000, 
                        'bukti_file' => 'bukti_sound_system.pdf', 
                        'komentar' => null
                    ],
                ]
            ];
            $komentar_revisi = [];
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($kegiatan_data_dummy['nama_kegiatan']),
            'status' => $status,
            'kegiatan_data' => $kegiatan_data_dummy,
            'rab_items' => $rab_items_merged,
            'komentar_revisi' => $komentar_revisi,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_lpj', $data, 'app');
    }
    
    public function uploadBukti() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Bukti berhasil diupload',
            'filename' => 'bukti_' . time() . '.pdf'
        ]);
    }
    
    public function submitLpj() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'LPJ berhasil diajukan ke Bendahara'
        ]);
    }
    
    public function submitRevisi() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Revisi LPJ berhasil disubmit'
        ]);
    }
}