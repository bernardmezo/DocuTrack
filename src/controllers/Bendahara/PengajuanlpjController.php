<?php
// File: src/controllers/Bendahara/PengajuanlpjController.php

require_once '../src/core/Controller.php';

class BendaharaPengajuanlpjController extends Controller {
    
    private $list_lpj_all = [];

    public function __construct() {
        // Hitung tanggal hari ini
        $today = new DateTime();
        
        // LPJ ID 1: Disetujui 3 hari lalu - Tenggat 14 hari dari persetujuan = sisa 11 hari
        $tgl_pengajuan_1 = (clone $today)->modify('-5 days');
        $tgl_persetujuan_1 = (clone $today)->modify('-3 days');
        
        // LPJ ID 2: Status Revisi - Belum disetujui
        $tgl_pengajuan_2 = (clone $today)->modify('-17 days');
        
        // LPJ ID 3: Status Menunggu - Belum disetujui
        $tgl_pengajuan_3 = (clone $today)->modify('-2 days');
        
        // LPJ ID 4: Status Telah Direvisi - Belum disetujui
        $tgl_pengajuan_4 = (clone $today)->modify('-8 days');
        
        // LPJ ID 5: Disetujui 10 hari lalu - Tenggat hampir habis = sisa 4 hari (kritis)
        $tgl_pengajuan_5 = (clone $today)->modify('-12 days');
        $tgl_persetujuan_5 = (clone $today)->modify('-10 days');
        
        // LPJ ID 6: Disetujui 15 hari lalu - Sudah terlambat 1 hari
        $tgl_pengajuan_6 = (clone $today)->modify('-17 days');
        $tgl_persetujuan_6 = (clone $today)->modify('-15 days');
        
        // LPJ ID 7: Disetujui 1 hari lalu - Masih banyak waktu = sisa 13 hari
        $tgl_pengajuan_7 = (clone $today)->modify('-3 days');
        $tgl_persetujuan_7 = (clone $today)->modify('-1 days');
        
        $this->list_lpj_all = [
            // LPJ ID 1: Sudah Disetujui - Disetujui 3 hari lalu, tenggat 14 hari dari persetujuan = sisa 11 hari
            1 => [
                'id' => 1,
                'nama' => 'Seminar Nasional Teknologi AI',
                'nama_mahasiswa' => 'Budi Santoso',
                'nim' => '190101001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'D4 Teknik Informatika',
                'tanggal_pengajuan' => $tgl_pengajuan_1->format('Y-m-d H:i:s'),
                'tanggal_persetujuan' => $tgl_persetujuan_1->format('Y-m-d H:i:s'),
                'tenggat_lpj' => (clone $tgl_persetujuan_1)->modify('+14 days')->format('Y-m-d 23:59:59'),
                'status' => 'Disetujui',
                'kak' => [
                    'nama_kegiatan' => 'Seminar Nasional Teknologi AI',
                    'pengusul' => 'Budi Santoso'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_1_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 40,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Kali',
                            'harga_satuan' => 20000,
                            'harga_plan' => 1600000,
                            'bukti_file' => 'bukti_snack.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_1_2',
                            'uraian' => 'Makan Siang',
                            'rincian' => 'Nasi Kotak',
                            'vol1' => 40,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 30000,
                            'harga_plan' => 1200000,
                            'bukti_file' => 'bukti_makan_siang.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_1_3',
                            'uraian' => 'Modul Pelatihan',
                            'rincian' => 'Buku Panduan',
                            'vol1' => 40,
                            'sat1' => 'Buku',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 25000,
                            'harga_plan' => 1000000,
                            'bukti_file' => 'bukti_modul.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_1_4',
                            'uraian' => 'Honor Narasumber',
                            'rincian' => 'Dosen Akuntansi',
                            'vol1' => 2,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 1000000,
                            'harga_plan' => 2000000,
                            'bukti_file' => 'bukti_narasumber.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_1_5',
                            'uraian' => 'Transport Peserta',
                            'rincian' => 'Uang Transport',
                            'vol1' => 30,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 50000,
                            'harga_plan' => 1500000,
                            'bukti_file' => 'bukti_transport.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Lainnya' => [
                        [
                            'id' => 'item_1_6',
                            'uraian' => 'Sertifikat',
                            'rincian' => 'Cetak Sertifikat',
                            'vol1' => 40,
                            'sat1' => 'Lembar',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 5000,
                            'harga_plan' => 200000,
                            'bukti_file' => 'bukti_sertifikat.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_1_7',
                            'uraian' => 'Spanduk',
                            'rincian' => 'Cetak Spanduk',
                            'vol1' => 2,
                            'sat1' => 'Buah',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 150000,
                            'harga_plan' => 300000,
                            'bukti_file' => 'bukti_spanduk.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 2: Dalam Status Revisi - Belum disetujui, tidak ada tenggat
            2 => [
                'id' => 2,
                'nama' => 'Workshop UI/UX Design 2024',
                'nama_mahasiswa' => 'Siti Aminah',
                'nim' => '190101002',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'D4 Teknik Grafika',
                'tanggal_pengajuan' => $tgl_pengajuan_2->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Revisi',
                'kak' => [
                    'nama_kegiatan' => 'Workshop UI/UX Design 2024',
                    'pengusul' => 'Siti Aminah'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_2_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 50,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Kali',
                            'harga_satuan' => 20000,
                            'harga_plan' => 2000000,
                            'bukti_file' => 'bukti_snack_workshop.pdf',
                            'komentar' => 'Total tidak sesuai dengan nota. Mohon cek kembali perhitungan.'
                        ],
                        [
                            'id' => 'item_2_2',
                            'uraian' => 'Makan Siang',
                            'rincian' => 'Nasi Kotak',
                            'vol1' => 50,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 30000,
                            'harga_plan' => 1500000,
                            'bukti_file' => 'bukti_makan.pdf',
                            'komentar' => 'Total tidak sesuai nota. Cek kembali perhitungan.'
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_2_3',
                            'uraian' => 'Honor Narasumber',
                            'rincian' => 'Praktisi UI/UX',
                            'vol1' => 2,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 1500000,
                            'harga_plan' => 3000000,
                            'bukti_file' => 'bukti_narasumber_uiux.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 3: Menunggu Verifikasi - Belum disetujui, tidak ada tenggat
            3 => [
                'id' => 3,
                'nama' => 'Lomba Robotika Nasional',
                'nama_mahasiswa' => 'Andi Pratama',
                'nim' => '190101003',
                'jurusan' => 'Teknik Elektro',
                'prodi' => 'D4 Teknik Elektronika',
                'tanggal_pengajuan' => $tgl_pengajuan_3->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Menunggu',
                'kak' => [
                    'nama_kegiatan' => 'Lomba Robotika Nasional',
                    'pengusul' => 'Andi Pratama'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_3_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 60,
                            'sat1' => 'Orang',
                            'vol2' => 3,
                            'sat2' => 'Kali',
                            'harga_satuan' => 18000,
                            'harga_plan' => 3240000,
                            'bukti_file' => 'bukti_snack_robotika.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_3_2',
                            'uraian' => 'Makan Siang',
                            'rincian' => 'Nasi Kotak',
                            'vol1' => 60,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Hari',
                            'harga_satuan' => 28000,
                            'harga_plan' => 3360000,
                            'bukti_file' => 'bukti_makan_robotika.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_3_3',
                            'uraian' => 'Honor Juri',
                            'rincian' => 'Dosen Teknik',
                            'vol1' => 3,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Hari',
                            'harga_satuan' => 500000,
                            'harga_plan' => 3000000,
                            'bukti_file' => 'bukti_juri.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_3_4',
                            'uraian' => 'Transport Tim',
                            'rincian' => 'Sewa Bus',
                            'vol1' => 2,
                            'sat1' => 'Unit',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 1500000,
                            'harga_plan' => 3000000,
                            'bukti_file' => 'bukti_transport_bus.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Lainnya' => [
                        [
                            'id' => 'item_3_5',
                            'uraian' => 'Sertifikat',
                            'rincian' => 'Cetak Sertifikat',
                            'vol1' => 60,
                            'sat1' => 'Lembar',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 8000,
                            'harga_plan' => 480000,
                            'bukti_file' => 'bukti_sertifikat_robotika.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_3_6',
                            'uraian' => 'Piala & Medali',
                            'rincian' => 'Trophy',
                            'vol1' => 3,
                            'sat1' => 'Set',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 400000,
                            'harga_plan' => 1200000,
                            'bukti_file' => 'bukti_piala.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 4: Telah Direvisi - Belum disetujui, tidak ada tenggat
            4 => [
                'id' => 4,
                'nama' => 'Pentas Seni dan Kewirausahaan',
                'nama_mahasiswa' => 'Dewi Lestari',
                'nim' => '190101004',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'D4 Administrasi Bisnis',
                'tanggal_pengajuan' => $tgl_pengajuan_4->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Telah Direvisi',
                'kak' => [
                    'nama_kegiatan' => 'Pentas Seni dan Kewirausahaan',
                    'pengusul' => 'Dewi Lestari'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_4_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 100,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Kali',
                            'harga_satuan' => 15000,
                            'harga_plan' => 1500000,
                            'bukti_file' => 'bukti_snack_pentas_rev.pdf',
                            'komentar' => 'Sudah diperbaiki sesuai nota asli'
                        ],
                        [
                            'id' => 'item_4_2',
                            'uraian' => 'Dekorasi Panggung',
                            'rincian' => 'Backdrop & Lighting',
                            'vol1' => 1,
                            'sat1' => 'Paket',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 3500000,
                            'harga_plan' => 3500000,
                            'bukti_file' => 'bukti_dekorasi.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_4_3',
                            'uraian' => 'Sound System',
                            'rincian' => 'Sewa Sound',
                            'vol1' => 1,
                            'sat1' => 'Paket',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 2000000,
                            'harga_plan' => 2000000,
                            'bukti_file' => 'bukti_sound_rev.pdf',
                            'komentar' => 'Bukti sudah dilengkapi'
                        ],
                        [
                            'id' => 'item_4_4',
                            'uraian' => 'Dokumentasi',
                            'rincian' => 'Foto & Video',
                            'vol1' => 2,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 800000,
                            'harga_plan' => 1600000,
                            'bukti_file' => 'bukti_dokumentasi_rev.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 5: Disetujui 10 hari lalu - Sisa 4 hari (KRITIS!)
            5 => [
                'id' => 5,
                'nama' => 'Pelatihan Leadership Mahasiswa',
                'nama_mahasiswa' => 'Rudi Hermawan',
                'nim' => '190101005',
                'jurusan' => 'Administrasi Niaga',
                'prodi' => 'D4 Manajemen Pemasaran',
                'tanggal_pengajuan' => $tgl_pengajuan_5->format('Y-m-d H:i:s'),
                'tanggal_persetujuan' => $tgl_persetujuan_5->format('Y-m-d H:i:s'),
                'tenggat_lpj' => (clone $tgl_persetujuan_5)->modify('+14 days')->format('Y-m-d 23:59:59'),
                'status' => 'Disetujui',
                'kak' => [
                    'nama_kegiatan' => 'Pelatihan Leadership Mahasiswa',
                    'pengusul' => 'Rudi Hermawan'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_5_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 30,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Kali',
                            'harga_satuan' => 20000,
                            'harga_plan' => 1200000,
                            'bukti_file' => 'bukti_snack_leadership.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_5_2',
                            'uraian' => 'Honor Pelatih',
                            'rincian' => 'Motivator',
                            'vol1' => 1,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Hari',
                            'harga_satuan' => 2000000,
                            'harga_plan' => 4000000,
                            'bukti_file' => 'bukti_pelatih.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 6: Disetujui 15 hari lalu - TERLAMBAT 1 hari!
            6 => [
                'id' => 6,
                'nama' => 'Festival Budaya Nusantara',
                'nama_mahasiswa' => 'Fitri Rahmawati',
                'nim' => '190101006',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'prodi' => 'D3 Desain Grafis',
                'tanggal_pengajuan' => $tgl_pengajuan_6->format('Y-m-d H:i:s'),
                'tanggal_persetujuan' => $tgl_persetujuan_6->format('Y-m-d H:i:s'),
                'tenggat_lpj' => (clone $tgl_persetujuan_6)->modify('+14 days')->format('Y-m-d 23:59:59'),
                'status' => 'Disetujui',
                'kak' => [
                    'nama_kegiatan' => 'Festival Budaya Nusantara',
                    'pengusul' => 'Fitri Rahmawati'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_6_1',
                            'uraian' => 'Dekorasi Stand',
                            'rincian' => 'Properti Budaya',
                            'vol1' => 5,
                            'sat1' => 'Set',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 500000,
                            'harga_plan' => 2500000,
                            'bukti_file' => 'bukti_dekorasi_budaya.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_6_2',
                            'uraian' => 'Sewa Panggung',
                            'rincian' => 'Panggung Outdoor',
                            'vol1' => 1,
                            'sat1' => 'Paket',
                            'vol2' => 2,
                            'sat2' => 'Hari',
                            'harga_satuan' => 3000000,
                            'harga_plan' => 6000000,
                            'bukti_file' => 'bukti_panggung.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 7: Disetujui 1 hari lalu - Sisa 13 hari (AMAN)
            7 => [
                'id' => 7,
                'nama' => 'Kompetisi Coding Hackathon',
                'nama_mahasiswa' => 'Ahmad Fauzi',
                'nim' => '190101007',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'D4 Sistem Informasi Kota Cerdas',
                'tanggal_pengajuan' => $tgl_pengajuan_7->format('Y-m-d H:i:s'),
                'tanggal_persetujuan' => $tgl_persetujuan_7->format('Y-m-d H:i:s'),
                'tenggat_lpj' => (clone $tgl_persetujuan_7)->modify('+14 days')->format('Y-m-d 23:59:59'),
                'status' => 'Disetujui',
                'kak' => [
                    'nama_kegiatan' => 'Kompetisi Coding Hackathon',
                    'pengusul' => 'Ahmad Fauzi'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_7_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Meal Box',
                            'vol1' => 50,
                            'sat1' => 'Orang',
                            'vol2' => 3,
                            'sat2' => 'Kali',
                            'harga_satuan' => 25000,
                            'harga_plan' => 3750000,
                            'bukti_file' => 'bukti_meal_hackathon.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Lainnya' => [
                        [
                            'id' => 'item_7_2',
                            'uraian' => 'Hadiah Pemenang',
                            'rincian' => 'Uang Tunai + Trophy',
                            'vol1' => 3,
                            'sat1' => 'Juara',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 2000000,
                            'harga_plan' => 6000000,
                            'bukti_file' => 'bukti_hadiah.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 8: Tambahan - Teknik Elektro Telekomunikasi
            8 => [
                'id' => 8,
                'nama' => 'Seminar Teknologi 5G',
                'nama_mahasiswa' => 'Fajar Nugraha',
                'nim' => '190101008',
                'jurusan' => 'Teknik Elektro',
                'prodi' => 'D4 Teknik Telekomunikasi',
                'tanggal_pengajuan' => (clone $today)->modify('-6 days')->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Menunggu',
                'kak' => [
                    'nama_kegiatan' => 'Seminar Teknologi 5G',
                    'pengusul' => 'Fajar Nugraha'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_8_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 35,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Kali',
                            'harga_satuan' => 18000,
                            'harga_plan' => 630000,
                            'bukti_file' => 'bukti_snack_5g.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_8_2',
                            'uraian' => 'Honor Narasumber',
                            'rincian' => 'Pakar Telekomunikasi',
                            'vol1' => 1,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 1500000,
                            'harga_plan' => 1500000,
                            'bukti_file' => 'bukti_narasumber_5g.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 9: Tambahan - Teknik Mesin
            9 => [
                'id' => 9,
                'nama' => 'Pelatihan Mesin CNC',
                'nama_mahasiswa' => 'Hendra Wijaya',
                'nim' => '190101009',
                'jurusan' => 'Teknik Mesin',
                'prodi' => 'D4 Teknik Perancangan Manufaktur',
                'tanggal_pengajuan' => (clone $today)->modify('-4 days')->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Menunggu',
                'kak' => [
                    'nama_kegiatan' => 'Pelatihan Mesin CNC',
                    'pengusul' => 'Hendra Wijaya'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_9_1',
                            'uraian' => 'Material Latihan',
                            'rincian' => 'Aluminium & Baja',
                            'vol1' => 20,
                            'sat1' => 'Kg',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 50000,
                            'harga_plan' => 1000000,
                            'bukti_file' => 'bukti_material.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_9_2',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 25,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Kali',
                            'harga_satuan' => 20000,
                            'harga_plan' => 1000000,
                            'bukti_file' => 'bukti_snack_cnc.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_9_3',
                            'uraian' => 'Instruktur CNC',
                            'rincian' => 'Teknisi Berpengalaman',
                            'vol1' => 1,
                            'sat1' => 'Orang',
                            'vol2' => 3,
                            'sat2' => 'Hari',
                            'harga_satuan' => 800000,
                            'harga_plan' => 2400000,
                            'bukti_file' => 'bukti_instruktur.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 10: Tambahan - Akuntansi
            10 => [
                'id' => 10,
                'nama' => 'Seminar Akuntansi Forensik',
                'nama_mahasiswa' => 'Rina Sari',
                'nim' => '190101010',
                'jurusan' => 'Akuntansi',
                'prodi' => 'D4 Akuntansi Manajerial',
                'tanggal_pengajuan' => (clone $today)->modify('-7 days')->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Menunggu',
                'kak' => [
                    'nama_kegiatan' => 'Seminar Akuntansi Forensik',
                    'pengusul' => 'Rina Sari'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_10_1',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 50,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Kali',
                            'harga_satuan' => 22000,
                            'harga_plan' => 1100000,
                            'bukti_file' => 'bukti_snack_forensik.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_10_2',
                            'uraian' => 'Makan Siang',
                            'rincian' => 'Nasi Kotak',
                            'vol1' => 50,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 30000,
                            'harga_plan' => 1500000,
                            'bukti_file' => 'bukti_makan_forensik.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_10_3',
                            'uraian' => 'Honor Narasumber',
                            'rincian' => 'Auditor Forensik',
                            'vol1' => 1,
                            'sat1' => 'Orang',
                            'vol2' => 1,
                            'sat2' => 'Hari',
                            'harga_satuan' => 2000000,
                            'harga_plan' => 2000000,
                            'bukti_file' => 'bukti_narasumber_forensik.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Lainnya' => [
                        [
                            'id' => 'item_10_4',
                            'uraian' => 'Sertifikat',
                            'rincian' => 'Cetak Sertifikat',
                            'vol1' => 50,
                            'sat1' => 'Lembar',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 7000,
                            'harga_plan' => 350000,
                            'bukti_file' => 'bukti_sertifikat_forensik.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 11: Tambahan - Teknik Informatika TMJ
            11 => [
                'id' => 11,
                'nama' => 'Workshop Jaringan Komputer',
                'nama_mahasiswa' => 'Joko Susilo',
                'nim' => '190101011',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'prodi' => 'D4 Teknik Multimedia dan Jaringan',
                'tanggal_pengajuan' => (clone $today)->modify('-9 days')->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Menunggu',
                'kak' => [
                    'nama_kegiatan' => 'Workshop Jaringan Komputer',
                    'pengusul' => 'Joko Susilo'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_11_1',
                            'uraian' => 'Kabel Jaringan',
                            'rincian' => 'UTP Cat 6',
                            'vol1' => 100,
                            'sat1' => 'Meter',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 5000,
                            'harga_plan' => 500000,
                            'bukti_file' => 'bukti_kabel.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_11_2',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 30,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Kali',
                            'harga_satuan' => 20000,
                            'harga_plan' => 1200000,
                            'bukti_file' => 'bukti_snack_jaringan.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_11_3',
                            'uraian' => 'Honor Instruktur',
                            'rincian' => 'Network Engineer',
                            'vol1' => 2,
                            'sat1' => 'Orang',
                            'vol2' => 2,
                            'sat2' => 'Hari',
                            'harga_satuan' => 750000,
                            'harga_plan' => 3000000,
                            'bukti_file' => 'bukti_instruktur_jaringan.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ],
            
            // LPJ ID 12: Tambahan - Teknik Sipil
            12 => [
                'id' => 12,
                'nama' => 'Pelatihan AutoCAD Civil 3D',
                'nama_mahasiswa' => 'Kartika Dewi',
                'nim' => '190101012',
                'jurusan' => 'Teknik Sipil',
                'prodi' => 'D4 Teknik Konstruksi Gedung',
                'tanggal_pengajuan' => (clone $today)->modify('-5 days')->format('Y-m-d H:i:s'),
                'tenggat_lpj' => null,
                'status' => 'Menunggu',
                'kak' => [
                    'nama_kegiatan' => 'Pelatihan AutoCAD Civil 3D',
                    'pengusul' => 'Kartika Dewi'
                ],
                'rab' => [
                    'Belanja Barang' => [
                        [
                            'id' => 'item_12_1',
                            'uraian' => 'Modul Pelatihan',
                            'rincian' => 'Buku Panduan AutoCAD',
                            'vol1' => 30,
                            'sat1' => 'Buku',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 35000,
                            'harga_plan' => 1050000,
                            'bukti_file' => 'bukti_modul_autocad.pdf',
                            'komentar' => null
                        ],
                        [
                            'id' => 'item_12_2',
                            'uraian' => 'Konsumsi Peserta',
                            'rincian' => 'Snack Box',
                            'vol1' => 30,
                            'sat1' => 'Orang',
                            'vol2' => 3,
                            'sat2' => 'Kali',
                            'harga_satuan' => 18000,
                            'harga_plan' => 1620000,
                            'bukti_file' => 'bukti_snack_autocad.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Jasa' => [
                        [
                            'id' => 'item_12_3',
                            'uraian' => 'Honor Instruktur',
                            'rincian' => 'Arsitek CAD',
                            'vol1' => 1,
                            'sat1' => 'Orang',
                            'vol2' => 3,
                            'sat2' => 'Hari',
                            'harga_satuan' => 900000,
                            'harga_plan' => 2700000,
                            'bukti_file' => 'bukti_instruktur_cad.pdf',
                            'komentar' => null
                        ]
                    ],
                    'Belanja Lainnya' => [
                        [
                            'id' => 'item_12_4',
                            'uraian' => 'Sertifikat',
                            'rincian' => 'Cetak Sertifikat',
                            'vol1' => 30,
                            'sat1' => 'Lembar',
                            'vol2' => 1,
                            'sat2' => 'Kegiatan',
                            'harga_satuan' => 8000,
                            'harga_plan' => 240000,
                            'bukti_file' => 'bukti_sertifikat_cad.pdf',
                            'komentar' => null
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Halaman List LPJ - HANYA MENUNGGU
     */
    public function index($data_dari_router = []) {
        $list_lpj = [];
        foreach ($this->list_lpj_all as $lpj) {
            // Filter: Bendahara hanya melihat LPJ yang statusnya "Menunggu" saja
            if ($lpj['status'] === 'Menunggu') {
                $list_lpj[] = [
                    'id' => $lpj['id'],
                    'nama' => $lpj['nama'],
                    'nama_mahasiswa' => $lpj['nama_mahasiswa'],
                    'nim' => $lpj['nim'],
                    'jurusan' => $lpj['jurusan'],
                    'prodi' => $lpj['prodi'],
                    'tanggal_pengajuan' => $lpj['tanggal_pengajuan'],
                    'tenggat_lpj' => $lpj['tenggat_lpj'],
                    'status' => $lpj['status']
                ];
            }
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Pengajuan LPJ - Bendahara',
            'list_lpj' => $list_lpj
        ]);

        $this->view('pages/bendahara/pengajuan-lpj', $data, 'bendahara');
    }

    /**
     * Method untuk mendapatkan data LPJ (untuk Dashboard)
     */
    public function getLPJData() {
        $list_lpj = [];
        foreach ($this->list_lpj_all as $lpj) {
            if (in_array($lpj['status'], ['Menunggu', 'Telah Direvisi', 'Revisi', 'Disetujui'])) {
                $list_lpj[] = [
                    'id' => $lpj['id'],
                    'nama' => $lpj['nama'],
                    'nama_mahasiswa' => $lpj['nama_mahasiswa'],
                    'nim' => $lpj['nim'],
                    'jurusan' => $lpj['jurusan'],
                    'prodi' => $lpj['prodi'],
                    'tanggal_pengajuan' => $lpj['tanggal_pengajuan'],
                    'tenggat_lpj' => $lpj['tenggat_lpj'],
                    'status' => $lpj['status']
                ];
            }
        }
        return $list_lpj;
    }

    /**
     * Halaman Detail LPJ untuk Verifikasi
     */
    public function show($id, $data_dari_router = []) {
        // Ambil referrer dari query string
        $ref = $_GET['ref'] ?? 'lpj';
        $base_url = "/docutrack/public/bendahara";
        
        if ($ref === 'dashboard') {
            $back_url = $base_url . '/dashboard';
        } else {
            $back_url = $base_url . '/pengajuan-lpj';
        }

        $lpj_dipilih = $this->list_lpj_all[$id] ?? null;
        
        if (!$lpj_dipilih) {
            header("Location: $back_url");
            exit;
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail LPJ - ' . htmlspecialchars($lpj_dipilih['nama']),
            'status' => $lpj_dipilih['status'],
            'kegiatan_data' => array_merge($lpj_dipilih['kak'], [
                'id' => $lpj_dipilih['id'],
                'nama_kegiatan' => $lpj_dipilih['nama'],
                'nama_mahasiswa' => $lpj_dipilih['nama_mahasiswa'],
                'nim' => $lpj_dipilih['nim']
            ]),
            'rab_items' => $lpj_dipilih['rab'],
            'tanggal_persetujuan' => $lpj_dipilih['tanggal_persetujuan'] ?? null,
            'back_url' => $back_url
        ]);

        $this->view('pages/bendahara/pengajuan-lpj-detail', $data, 'bendahara');
    }

    /**
     * Proses Verifikasi LPJ (Setuju atau Revisi)
     */
    public function proses() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/bendahara/pengajuan-lpj');
            exit;
        }

        $lpj_id = $_POST['lpj_id'] ?? null;
        $action = $_POST['action'] ?? null;
        
        if (!$lpj_id || !$action) {
            $_SESSION['flash_message'] = 'Data tidak lengkap!';
            $_SESSION['flash_type'] = 'error';
            header('Location: /docutrack/public/bendahara/pengajuan-lpj');
            exit;
        }

        try {
            if ($action === 'setuju') {
                $_SESSION['flash_message'] = 'LPJ berhasil disetujui!';
                $_SESSION['flash_type'] = 'success';
                
            } elseif ($action === 'revisi') {
                $komentar = $_POST['komentar'] ?? [];
                $catatan_umum = trim($_POST['catatan_umum'] ?? '');
                
                // Validasi: Minimal ada 1 komentar
                $hasComment = false;
                foreach ($komentar as $item_id => $comment) {
                    if (!empty(trim($comment))) {
                        $hasComment = true;
                        break;
                    }
                }
                
                if (!$hasComment) {
                    $_SESSION['flash_message'] = 'Mohon isi minimal 1 komentar untuk item yang perlu direvisi!';
                    $_SESSION['flash_type'] = 'error';
                    header('Location: /docutrack/public/bendahara/pengajuan-lpj/show/' . $lpj_id);
                    exit;
                }
                
                $_SESSION['flash_message'] = 'Permintaan revisi berhasil dikirim ke Admin!';
                $_SESSION['flash_type'] = 'success';
                
            } else {
                throw new Exception('Action tidak valid');
            }

        } catch (Exception $e) {
            $_SESSION['flash_message'] = 'Terjadi kesalahan: ' . $e->getMessage();
            $_SESSION['flash_type'] = 'error';
        }

        header('Location: /docutrack/public/bendahara/pengajuan-lpj');
        exit;
    }
}