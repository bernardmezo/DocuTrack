<?php
// File: src/controllers/Admin/AdminDetailKAKController.php

require_once '../src/core/Controller.php';

class AdminDetailKAKController extends Controller {
    
    private $list_kegiatan_all = [
        102 => [
            'id' => 102, 
            'nama' => 'Seminar Nasional Artificial Intelligence 2025', 
            'pengusul' => 'Himpunan Mahasiswa TI', 
            'status' => 'Disetujui', 
            'kode_mak' => '5241.001.052.A.521211',
            'komentar' => [], 
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Andi Pratama',
                'nim_pengusul' => '481701001', // Field NIM Pengusul
                'nama_penanggung_jawab' => 'Dr. Budi Hartanto, M.Kom', // Baru
                'nip_penanggung_jawab' => '197805152008011002', // Baru
                'jurusan' => 'Teknik Informatika',
                'nama_kegiatan' => 'Seminar Nasional Artificial Intelligence 2025',
                'gambaran_umum' => 'Seminar nasional yang membahas perkembangan terkini dalam bidang Artificial Intelligence dan Machine Learning.',
                'penerima_manfaat' => 'Mahasiswa Teknik Informatika, Sistem Informasi, dan umum yang tertarik dengan teknologi AI.',
                'metode_pelaksanaan' => 'Kegiatan dilaksanakan secara hybrid (offline dan online) dengan metode presentasi, diskusi panel, dan sesi tanya jawab. Peserta akan mendapatkan materi dalam bentuk modul digital dan sertifikat elektronik.', // Baru
                'tahapan_kegiatan' => "Persiapan (Minggu 1-2): Koordinasi narasumber, pembuatan materi promosi\nPelaksanaan (Minggu 3): Seminar hari pertama (keynote speaker dan sesi 1), hari kedua (sesi 2 dan panel diskusi)\nEvaluasi (Minggu 4): Penyusunan laporan dan distribusi sertifikat", // Baru
                'surat_pengantar' => 'Surat_Pengantar_Seminar_AI_2025.pdf',
                'tanggal_mulai' => '2025-01-15',
                'tanggal_selesai' => '2025-01-16'
            ],
            'iku' => ['Mendapat Pekerjaan', 'Prestasi Mahasiswa'],
            'indikator' => [
                ['bulan' => 'Januari', 'nama' => 'Peserta Terdaftar', 'target' => 80],
                ['bulan' => 'Januari', 'nama' => 'Peserta Hadir', 'target' => 90],
                ['bulan' => 'Januari', 'nama' => 'Tingkat Kepuasan', 'target' => 85]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Konsumsi Rapat', 'rincian'=>'Snack Box', 'vol1'=>20, 'sat1'=>'Orang', 'vol2'=>4, 'sat2'=>'Kali', 'harga'=>15000],
                    ['id'=>2, 'uraian'=>'Konsumsi Seminar', 'rincian'=>'Snack Box Peserta', 'vol1'=>200, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>20000],
                    ['id'=>3, 'uraian'=>'Makan Siang', 'rincian'=>'Nasi Kotak', 'vol1'=>200, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>35000],
                    ['id'=>4, 'uraian'=>'Seminar Kit', 'rincian'=>'Tas, Pulpen, Notes', 'vol1'=>200, 'sat1'=>'Paket', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>50000]
                ],
                'Belanja Jasa' => [
                    ['id'=>5, 'uraian'=>'Honor Pembicara', 'rincian'=>'Narasumber Eksternal', 'vol1'=>3, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>2000000],
                    ['id'=>6, 'uraian'=>'Honor Moderator', 'rincian'=>'Moderator Sesi', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>500000],
                    ['id'=>7, 'uraian'=>'Sewa Sound System', 'rincian'=>'Sound Lengkap', 'vol1'=>1, 'sat1'=>'Set', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>1500000],
                    ['id'=>8, 'uraian'=>'Dokumentasi', 'rincian'=>'Foto dan Video', 'vol1'=>1, 'sat1'=>'Paket', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>1500000]
                ],
                'Belanja Lainnya' => [
                    ['id'=>9, 'uraian'=>'Sertifikat Peserta', 'rincian'=>'Cetak Sertifikat', 'vol1'=>200, 'sat1'=>'Lembar', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>5000],
                    ['id'=>10, 'uraian'=>'Spanduk Acara', 'rincian'=>'Cetak Spanduk 5x1m', 'vol1'=>3, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>200000],
                    ['id'=>11, 'uraian'=>'Backdrop', 'rincian'=>'Cetak Backdrop 4x3m', 'vol1'=>1, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>750000]
                ]
            ]
        ],
        103 => [
            'id' => 103, 
            'nama' => 'Workshop UI/UX Design Fundamental', 
            'pengusul' => 'BEM Fakultas', 
            'status' => 'Revisi', 
            'kode_mak' => '',
            'komentar' => [
                'rab_belanja_jasa' => 'Harga sewa sound system terlalu mahal, mohon sesuaikan dengan SBM.',
                'gambaran_umum' => 'Mohon jelaskan lebih detail tentang materi yang akan disampaikan.'
            ], 
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Budi Santoso',
                'nim_pengusul' => '441701003',
                'nama_penanggung_jawab' => 'Rina Suharti, M.Kom',
                'nip_penanggung_jawab' => '198201012010012005',
                'jurusan' => 'Sistem Informasi',
                'nama_kegiatan' => 'Workshop UI/UX Design Fundamental',
                'gambaran_umum' => 'Workshop yang membahas dasar-dasar desain UI/UX untuk pemula.',
                'penerima_manfaat' => 'Mahasiswa yang tertarik dengan desain digital dan pengembangan aplikasi.',
                'metode_pelaksanaan' => 'Workshop tatap muka di laboratorium komputer dengan panduan instruktur.',
                'tahapan_kegiatan' => '1. Registrasi\n2. Sesi Teori Dasar\n3. Sesi Praktik Figma\n4. Review Hasil Karya',
                'surat_pengantar' => '',
                'tanggal_mulai' => '',
                'tanggal_selesai' => ''
            ],
            'iku' => ['Prestasi Mahasiswa', 'Kompetensi Lulusan'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Peserta Workshop', 'target' => 50],
                ['bulan' => 'Februari', 'nama' => 'Proyek Selesai', 'target' => 80]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>12, 'uraian'=>'Konsumsi Peserta', 'rincian'=>'Snack Box', 'vol1'=>60, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Kali', 'harga'=>20000],
                    ['id'=>13, 'uraian'=>'Makan Siang', 'rincian'=>'Nasi Kotak', 'vol1'=>60, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>30000],
                    ['id'=>14, 'uraian'=>'Modul Workshop', 'rincian'=>'Buku Panduan', 'vol1'=>60, 'sat1'=>'Buku', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>35000]
                ],
                'Belanja Jasa' => [
                    ['id'=>15, 'uraian'=>'Honor Instruktur', 'rincian'=>'Instruktur UI/UX', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>1500000],
                    ['id'=>16, 'uraian'=>'Sewa Sound System', 'rincian'=>'Sound System', 'vol1'=>1, 'sat1'=>'Set', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>800000]
                ],
                'Belanja Lainnya' => [
                    ['id'=>17, 'uraian'=>'Sertifikat', 'rincian'=>'Cetak Sertifikat', 'vol1'=>60, 'sat1'=>'Lembar', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>5000],
                    ['id'=>18, 'uraian'=>'Banner Acara', 'rincian'=>'X-Banner', 'vol1'=>2, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>150000]
                ]
            ]
        ],
        104 => [
            'id' => 104, 
            'nama' => 'Turnamen E-Sport Kampus', 
            'pengusul' => 'Komunitas Gaming', 
            'status' => 'Ditolak', 
            'kode_mak' => '',
            'komentar' => [],
            'komentar_penolakan' => 'Anggaran tidak sesuai dengan SBM. Kegiatan belum memiliki relevansi dengan program kerja fakultas.',
            'kak' => [
                'nama_pengusul' => 'Joko Susilo',
                'nim_pengusul' => '431701004',
                'nama_penanggung_jawab' => 'Drs. Gaming Santoso, M.Pd',
                'nip_penanggung_jawab' => '199001012015011001',
                'jurusan' => 'Manajemen',
                'nama_kegiatan' => 'Turnamen E-Sport Kampus',
                'gambaran_umum' => 'Kompetisi game online antar mahasiswa untuk membangun komunitas gaming di kampus.',
                'penerima_manfaat' => 'Mahasiswa yang memiliki hobi gaming dan ingin berkompetisi.',
                'metode_pelaksanaan' => 'Turnamen sistem gugur (bracket) yang diselenggarakan di Aula Kampus.',
                'tahapan_kegiatan' => '1. Pendaftaran Online\n2. Technical Meeting\n3. Babak Penyisihan\n4. Grand Final',
                'surat_pengantar' => '',
                'tanggal_mulai' => '',
                'tanggal_selesai' => ''
            ],
            'iku' => ['Prestasi Mahasiswa'],
            'indikator' => [
                ['bulan' => 'Maret', 'nama' => 'Jumlah Tim Peserta', 'target' => 32],
                ['bulan' => 'Maret', 'nama' => 'Penonton Live', 'target' => 500]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>19, 'uraian'=>'Konsumsi Peserta', 'rincian'=>'Snack Box', 'vol1'=>150, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>15000],
                    ['id'=>20, 'uraian'=>'Air Mineral', 'rincian'=>'Air 600ml', 'vol1'=>300, 'sat1'=>'Botol', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>4000]
                ],
                'Belanja Hadiah' => [
                    ['id'=>21, 'uraian'=>'Hadiah Juara 1', 'rincian'=>'Uang + Trophy', 'vol1'=>1, 'sat1'=>'Tim', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>5000000],
                    ['id'=>22, 'uraian'=>'Hadiah Juara 2', 'rincian'=>'Uang + Trophy', 'vol1'=>1, 'sat1'=>'Tim', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>3000000],
                    ['id'=>23, 'uraian'=>'Hadiah Juara 3', 'rincian'=>'Uang + Trophy', 'vol1'=>1, 'sat1'=>'Tim', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>2000000]
                ],
                'Belanja Jasa' => [
                    ['id'=>24, 'uraian'=>'Sewa Venue', 'rincian'=>'Sewa Aula', 'vol1'=>1, 'sat1'=>'Ruang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>2000000],
                    ['id'=>25, 'uraian'=>'Sewa PC Gaming', 'rincian'=>'PC Gaming Set', 'vol1'=>20, 'sat1'=>'Unit', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>250000]
                ]
            ]
        ],
        105 => [
            'id' => 105, 
            'nama' => 'Pelatihan Dasar Akuntansi UMKM', 
            'pengusul' => 'Hima Akuntansi', 
            'status' => 'Menunggu', 
            'kode_mak' => '',
            'komentar' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Rina Wati',
                'nim_pengusul' => '421701005',
                'nama_penanggung_jawab' => 'Dra. Akuntani, M.Ak',
                'nip_penanggung_jawab' => '197505052000012001',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Pelatihan Dasar Akuntansi UMKM',
                'gambaran_umum' => 'Pelatihan untuk pelaku UMKM agar dapat mengelola keuangan usaha dengan lebih baik.',
                'penerima_manfaat' => 'Pelaku UMKM di wilayah sekitar kampus.',
                'metode_pelaksanaan' => 'Pelatihan kelas dengan studi kasus nyata UMKM.',
                'tahapan_kegiatan' => '1. Sosialisasi ke UMKM\n2. Pelaksanaan Materi Dasar\n3. Pendampingan Pembukuan',
                'surat_pengantar' => '',
                'tanggal_mulai' => '',
                'tanggal_selesai' => ''
            ],
            'iku' => ['Pengabdian Masyarakat', 'Kerjasama dengan Industri'],
            'indikator' => [
                ['bulan' => 'April', 'nama' => 'Peserta UMKM', 'target' => 30],
                ['bulan' => 'April', 'nama' => 'UMKM Terapkan Pembukuan', 'target' => 70]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>26, 'uraian'=>'Konsumsi Peserta', 'rincian'=>'Snack Box', 'vol1'=>40, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Kali', 'harga'=>20000],
                    ['id'=>27, 'uraian'=>'Makan Siang', 'rincian'=>'Nasi Kotak', 'vol1'=>40, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>30000],
                    ['id'=>28, 'uraian'=>'Modul Pelatihan', 'rincian'=>'Buku Panduan', 'vol1'=>40, 'sat1'=>'Buku', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>25000]
                ],
                'Belanja Jasa' => [
                    ['id'=>29, 'uraian'=>'Honor Narasumber', 'rincian'=>'Dosen Akuntansi', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>1000000],
                    ['id'=>30, 'uraian'=>'Transport Peserta', 'rincian'=>'Uang Transport', 'vol1'=>30, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>50000]
                ],
                'Belanja Lainnya' => [
                    ['id'=>31, 'uraian'=>'Sertifikat', 'rincian'=>'Cetak Sertifikat', 'vol1'=>40, 'sat1'=>'Lembar', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>5000],
                    ['id'=>32, 'uraian'=>'Spanduk', 'rincian'=>'Cetak Spanduk', 'vol1'=>2, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>150000]
                ]
            ]
        ],
        106 => [
            'id' => 106, 
            'nama' => 'Pameran Seni Rupa Digital', 
            'pengusul' => 'UKM Seni', 
            'status' => 'Disetujui', 
            'kode_mak' => '5241.001.052.B.521212',
            'komentar' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Doni Irawan',
                'nim_pengusul' => '461701006',
                'nama_penanggung_jawab' => 'Bapak Seniwan, M.Sn',
                'nip_penanggung_jawab' => '198008082008011005',
                'jurusan' => 'Desain Grafis',
                'nama_kegiatan' => 'Pameran Seni Rupa Digital',
                'gambaran_umum' => 'Pameran karya seni digital dari mahasiswa. Menampilkan digital painting, 3D art, dan motion graphics.',
                'penerima_manfaat' => 'Mahasiswa seni dan masyarakat umum yang ingin mengapresiasi seni digital.',
                'metode_pelaksanaan' => 'Pameran terbuka di Galeri Kampus dengan sistem tiket gratis.',
                'tahapan_kegiatan' => '1. Kurasi Karya\n2. Setting Display Pameran\n3. Opening Ceremony\n4. Pameran Berlangsung',
                'surat_pengantar' => 'Surat_Pengantar_Pameran_Seni_2025.pdf',
                'tanggal_mulai' => '2025-02-20',
                'tanggal_selesai' => '2025-02-25'
            ],
            'iku' => ['Prestasi Mahasiswa', 'Reputasi Institusi'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Karya Dipamerkan', 'target' => 100],
                ['bulan' => 'Februari', 'nama' => 'Pengunjung', 'target' => 80]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>33, 'uraian'=>'Frame Display', 'rincian'=>'Frame Karya Digital', 'vol1'=>50, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>75000],
                    ['id'=>34, 'uraian'=>'Katalog Pameran', 'rincian'=>'Cetak Katalog', 'vol1'=>200, 'sat1'=>'Buku', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>30000],
                    ['id'=>35, 'uraian'=>'Konsumsi Opening', 'rincian'=>'Snack Box', 'vol1'=>100, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kali', 'harga'=>25000],
                    ['id'=>36, 'uraian'=>'Konsumsi Panitia', 'rincian'=>'Makan Siang', 'vol1'=>20, 'sat1'=>'Orang', 'vol2'=>5, 'sat2'=>'Hari', 'harga'=>30000]
                ],
                'Belanja Jasa' => [
                    ['id'=>37, 'uraian'=>'Sewa Galeri', 'rincian'=>'Ruang Pameran', 'vol1'=>1, 'sat1'=>'Ruang', 'vol2'=>5, 'sat2'=>'Hari', 'harga'=>1000000],
                    ['id'=>38, 'uraian'=>'Jasa Kurator', 'rincian'=>'Kurator Pameran', 'vol1'=>1, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>2500000],
                    ['id'=>39, 'uraian'=>'Sewa Monitor', 'rincian'=>'Monitor 42 Inch', 'vol1'=>10, 'sat1'=>'Unit', 'vol2'=>5, 'sat2'=>'Hari', 'harga'=>150000],
                    ['id'=>40, 'uraian'=>'Dokumentasi', 'rincian'=>'Foto dan Video', 'vol1'=>1, 'sat1'=>'Paket', 'vol2'=>5, 'sat2'=>'Hari', 'harga'=>500000]
                ],
                'Belanja Lainnya' => [
                    ['id'=>41, 'uraian'=>'Sertifikat Seniman', 'rincian'=>'Cetak Sertifikat', 'vol1'=>60, 'sat1'=>'Lembar', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>7500],
                    ['id'=>42, 'uraian'=>'Spanduk', 'rincian'=>'Cetak Spanduk 6x2m', 'vol1'=>3, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>300000],
                    ['id'=>43, 'uraian'=>'Backdrop Utama', 'rincian'=>'Cetak Backdrop 5x3m', 'vol1'=>1, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>1000000]
                ]
            ]
        ]
    ];

    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? 'kegiatan'; 
        $base_url = "/docutrack/public/admin";
        $back_url = ($ref === 'dashboard') ? $base_url . '/dashboard' : $base_url . '/pengajuan-kegiatan';

        $kegiatan_dipilih = $this->list_kegiatan_all[$id] ?? null;
        
        if (!$kegiatan_dipilih) {
            return not_found("Kegiatan dengan ID $id tidak ditemukan.");
        }
        
        $status = $kegiatan_dipilih['status'];

        $surat_pengantar_url = '';
        if (!empty($kegiatan_dipilih['kak']['surat_pengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $kegiatan_dipilih['kak']['surat_pengantar'];
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Detail Kegiatan - ' . htmlspecialchars($kegiatan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'admin',
            'kegiatan_data' => $kegiatan_dipilih['kak'],
            'iku_data' => $kegiatan_dipilih['iku'],
            'indikator_data' => $kegiatan_dipilih['indikator'],
            'rab_data' => $kegiatan_dipilih['rab'],
            'kode_mak' => $kegiatan_dipilih['kode_mak'],
            'komentar_revisi' => $kegiatan_dipilih['komentar'],
            'komentar_penolakan' => $kegiatan_dipilih['komentar_penolakan'] ?? '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/admin/detail_kak', $data, 'app');
    }
}