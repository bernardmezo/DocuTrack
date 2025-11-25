<?php
// File: src/controllers/PPK/TelaahController.php

require_once '../src/core/Controller.php';

class PPKTelaahController extends Controller {
    
    /**
     * --- SIMULASI DATABASE MASTER (KONSISTEN) ---
     * Ini adalah "sumber kebenaran" untuk PPK.
     * Data telah diperbarui dengan struktur:
     * 1. Identitas Penanggung Jawab
     * 2. Strategi Pencapaian Keluaran (Metode & Tahapan)
     */
    private $list_usulan_all = [
        1 => [
            'id' => 1, 
            'nama' => 'Seminar Nasional Teknologi', 
            'pengusul' => 'Ahmad Rizki', 
            'nim' => '2201001',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-01-15',
            'kode_mak' => '5241.001.052.A.521211',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Ahmad Rizki',
                'nim_pengusul' => '2201001',
                'nama_penanggung_jawab' => 'Dr. Techn. Budi Santoso',
                'nip_penanggung_jawab' => '197501012000121001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Seminar Nasional Teknologi', 
                'gambaran_umum' => 'Seminar nasional yang membahas perkembangan teknologi terkini di Indonesia dengan menghadirkan pembicara dari industri dan akademisi.', 
                'penerima_manfaat' => 'Mahasiswa Teknik Informatika, Sistem Informasi, dan umum.',
                'metode_pelaksanaan' => 'Hybrid (Daring via Zoom dan Luring di Auditorium).',
                'tahapan_kegiatan' => "1. Perencanaan & Persiapan (Minggu 1)\n2. Promosi & Registrasi (Minggu 2)\n3. Pelaksanaan Seminar (Hari H)\n4. Laporan Pertanggungjawaban",
                'surat_pengantar' => 'Surat_Pengantar_Seminar_Teknologi_2024.pdf',
                'tanggal_mulai' => '2024-01-15',
                'tanggal_selesai' => '2024-01-16'
            ],
            'iku' => ['Mendapat Pekerjaan', 'Kompetensi Teknis'],
            'indikator' => [
                ['bulan' => 'Januari', 'nama' => 'Peserta Terdaftar', 'target' => 180],
                ['bulan' => 'Januari', 'nama' => 'Peserta Hadir', 'target' => 200]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Konsumsi Rapat', 'rincian'=>'Snack Box', 'vol1'=>15, 'sat1'=>'Orang', 'vol2'=>3, 'sat2'=>'Kali', 'harga'=>15000],
                    ['id'=>2, 'uraian'=>'Snack Peserta', 'rincian'=>'Box Snack', 'vol1'=>200, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>20000],
                    ['id'=>4, 'uraian'=>'Seminar Kit', 'rincian'=>'Tas, Pulpen, Notes', 'vol1'=>200, 'sat1'=>'Paket', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>50000]
                ],
                'Belanja Jasa' => [
                    ['id'=>5, 'uraian'=>'Honorarium Pembicara', 'rincian'=>'Narasumber Eksternal', 'vol1'=>3, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>2000000]
                ]
            ]
        ],
        2 => [
            'id' => 2, 
            'nama' => 'Workshop AI & Machine Learning', 
            'pengusul' => 'Siti Nurhaliza', 
            'nim' => '2201002',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-01-18',
            'kode_mak' => '5241.001.052.A.521212',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Siti Nurhaliza',
                'nim_pengusul' => '2201002',
                'nama_penanggung_jawab' => 'Rina Suharti, M.Kom',
                'nip_penanggung_jawab' => '198205052005012003',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Workshop AI & Machine Learning', 
                'gambaran_umum' => 'Workshop praktis tentang penerapan AI dan Machine Learning dalam pengembangan aplikasi menggunakan Python dan TensorFlow.', 
                'penerima_manfaat' => 'Mahasiswa TIK dan praktisi data.',
                'metode_pelaksanaan' => 'Praktikum Hands-on di Laboratorium Komputer.',
                'tahapan_kegiatan' => "1. Instalasi Tools\n2. Pengenalan Konsep Dasar\n3. Sesi Coding (Supervised Learning)\n4. Presentasi Project",
                'surat_pengantar' => 'Surat_Pengantar_Workshop_AIML_2024.pdf',
                'tanggal_mulai' => '2024-01-18',
                'tanggal_selesai' => '2024-01-19'
            ],
            'iku' => ['Kompetensi Teknis', 'Prestasi Mahasiswa'],
            'indikator' => [
                ['bulan' => 'Januari', 'nama' => 'Peserta Workshop', 'target' => 50],
                ['bulan' => 'Januari', 'nama' => 'Proyek AI Selesai', 'target' => 80]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Modul Pelatihan', 'rincian'=>'Buku Panduan AI', 'vol1'=>50, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>50000],
                    ['id'=>2, 'uraian'=>'Snack Peserta', 'rincian'=>'Snack Box', 'vol1'=>50, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>20000]
                ],
                'Belanja Jasa' => [
                    ['id'=>4, 'uraian'=>'Honorarium Instruktur', 'rincian'=>'Instruktur AI', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>1500000]
                ]
            ]
        ],
        3 => [
            'id' => 3, 
            'nama' => 'Lomba Cerdas Cermat', 
            'pengusul' => 'Budi Santoso', 
            'nim' => '2202001',
            'jurusan' => 'Teknik Elektro',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-01-20',
            'kode_mak' => '5241.001.052.B.521214',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Budi Santoso',
                'nim_pengusul' => '2202001',
                'nama_penanggung_jawab' => 'Ir. Bambang Electro, M.T',
                'nip_penanggung_jawab' => '197003031998021001',
                'jurusan' => 'Teknik Elektro',
                'nama_kegiatan' => 'Lomba Cerdas Cermat', 
                'gambaran_umum' => 'Kompetisi cerdas cermat tingkat SMA se-Jakarta untuk meningkatkan minat belajar siswa dalam bidang sains dan teknologi.', 
                'penerima_manfaat' => 'Siswa SMA se-Jakarta.',
                'metode_pelaksanaan' => 'Sistem Gugur (Penyisihan Online, Final Luring).',
                'tahapan_kegiatan' => "1. Pendaftaran\n2. Technical Meeting\n3. Babak Penyisihan\n4. Grand Final",
                'surat_pengantar' => 'Surat_Pengantar_Lomba_Cerdas_Cermat_2024.pdf',
                'tanggal_mulai' => '2024-02-15',
                'tanggal_selesai' => '2024-02-16'
            ],
            'iku' => ['Prestasi', 'Reputasi Institusi'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Jumlah Tim', 'target' => 30]
            ],
            'rab' => [
                'Belanja Hadiah' => [
                    ['id'=>1, 'uraian'=>'Piala Juara', 'rincian'=>'Set Piala 1-3', 'vol1'=>3, 'sat1'=>'Set', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>250000]
                ],
                'Belanja Konsumsi' => [
                    ['id'=>3, 'uraian'=>'Snack Peserta', 'rincian'=>'Box Snack', 'vol1'=>100, 'sat1'=>'Box', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>20000]
                ]
            ]
        ],
        4 => [
            'id' => 4, 
            'nama' => 'Pelatihan Web Development', 
            'pengusul' => 'Dewi Lestari', 
            'nim' => '2203001',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-01-22',
            'kode_mak' => '5241.001.052.B.521213',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Dewi Lestari',
                'nim_pengusul' => '2203001',
                'nama_penanggung_jawab' => 'Eko Prasetyo, M.Kom',
                'nip_penanggung_jawab' => '198808082015011005',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Pelatihan Web Development', 
                'gambaran_umum' => 'Pelatihan intensif pembuatan website modern menggunakan React.js dan Node.js.', 
                'penerima_manfaat' => 'Mahasiswa TIK.',
                'metode_pelaksanaan' => 'Bootcamp (Pelatihan Intensif).',
                'tahapan_kegiatan' => "1. Pengenalan HTML/CSS\n2. JavaScript Basic\n3. React Framework\n4. Deployment",
                'surat_pengantar' => 'Surat_Pengantar_Web_Dev_2024.pdf',
                'tanggal_mulai' => '2024-01-22',
                'tanggal_selesai' => '2024-01-24'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [
                ['bulan' => 'Januari', 'nama' => 'Peserta Lulus', 'target' => 40]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Modul', 'rincian'=>'E-Book Premium', 'vol1'=>40, 'sat1'=>'Akses', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>45000]
                ],
                'Belanja Jasa' => [
                    ['id'=>3, 'uraian'=>'Instruktur', 'rincian'=>'Senior Dev', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>3, 'sat2'=>'Hari', 'harga'=>1500000]
                ]
            ]
        ],
        6 => [
            'id' => 6, 
            'nama' => 'Seminar Kewirausahaan', 
            'pengusul' => 'Fitri Handayani', 
            'nim' => '2204001',
            'jurusan' => 'Akuntansi',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-01',
            'kode_mak' => '5241.001.052.C.521215',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Fitri Handayani',
                'nim_pengusul' => '2204001',
                'nama_penanggung_jawab' => 'Dra. Wira Usaha, MM',
                'nip_penanggung_jawab' => '196502021990012001',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Seminar Kewirausahaan', 
                'gambaran_umum' => 'Seminar mindset entrepreneur dengan menghadirkan pengusaha muda sukses.', 
                'penerima_manfaat' => 'Mahasiswa PNJ.',
                'metode_pelaksanaan' => 'Talkshow Interaktif.',
                'tahapan_kegiatan' => "1. Keynote Speech\n2. Sesi Sharing\n3. Tanya Jawab\n4. Networking Session",
                'surat_pengantar' => 'Surat_Pengantar_Seminar_Wirausaha_2024.pdf',
                'tanggal_mulai' => '2024-02-20',
                'tanggal_selesai' => '2024-02-20'
            ],
            'iku' => ['Wirausaha'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Peserta Hadir', 'target' => 150]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Banner', 'rincian'=>'Cetak Banner', 'vol1'=>5, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>150000]
                ],
                'Belanja Jasa' => [
                    ['id'=>3, 'uraian'=>'Honorarium', 'rincian'=>'Pembicara', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>1500000]
                ]
            ]
        ],
        9 => [
            'id' => 9, 
            'nama' => 'Webinar Digital Marketing', 
            'pengusul' => 'Intan Permata', 
            'nim' => '2204002',
            'jurusan' => 'Akuntansi',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-10',
            'kode_mak' => '5241.001.052.C.521216',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Intan Permata',
                'nim_pengusul' => '2204002',
                'nama_penanggung_jawab' => 'Siti Marketing, SE',
                'nip_penanggung_jawab' => '198505052010012005',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Webinar Digital Marketing', 
                'gambaran_umum' => 'Webinar online membahas strategi digital marketing untuk UMKM (SEO, SEM, Social Media).', 
                'penerima_manfaat' => 'Pelaku UMKM dan Mahasiswa.',
                'metode_pelaksanaan' => 'Webinar Daring (Zoom).',
                'tahapan_kegiatan' => "1. Registrasi Online\n2. Pelaksanaan Webinar\n3. Pembagian E-Sertifikat",
                'surat_pengantar' => 'Surat_Pengantar_Webinar_DM_2024.pdf',
                'tanggal_mulai' => '2024-02-25',
                'tanggal_selesai' => '2024-02-25'
            ],
            'iku' => ['Wirausaha'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Peserta Online', 'target' => 300]
            ],
            'rab' => [
                'Belanja Jasa' => [
                    ['id'=>1, 'uraian'=>'Zoom Pro', 'rincian'=>'Sewa Akun 1 Bulan', 'vol1'=>1, 'sat1'=>'Paket', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>500000]
                ]
            ]
        ],
        11 => [
            'id' => 11, 
            'nama' => 'Lomba Karya Tulis Ilmiah', 
            'pengusul' => 'Kartika Sari', 
            'nim' => '2205001',
            'jurusan' => 'Teknik Mesin',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-15',
            'kode_mak' => '5241.001.052.D.521217',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Kartika Sari',
                'nim_pengusul' => '2205001',
                'nama_penanggung_jawab' => 'Dr. Eng. Mesin, M.T',
                'nip_penanggung_jawab' => '197808082005011001',
                'jurusan' => 'Teknik Mesin',
                'nama_kegiatan' => 'Lomba Karya Tulis Ilmiah', 
                'gambaran_umum' => 'Kompetisi KTI mahasiswa tingkat nasional tema inovasi teknologi hijau.', 
                'penerima_manfaat' => 'Mahasiswa Nasional.',
                'metode_pelaksanaan' => 'Seleksi Paper (Online) & Presentasi Finalis (Luring).',
                'tahapan_kegiatan' => "1. Call for Paper\n2. Desk Evaluation\n3. Final Presentation\n4. Awarding",
                'surat_pengantar' => 'Surat_Pengantar_Lomba_KTI_2024.pdf',
                'tanggal_mulai' => '2024-03-10',
                'tanggal_selesai' => '2024-03-12'
            ],
            'iku' => ['Prestasi', 'Publikasi Ilmiah'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Karya Masuk', 'target' => 50]
            ],
            'rab' => [
                'Belanja Hadiah' => [
                    ['id'=>1, 'uraian'=>'Uang Pembinaan', 'rincian'=>'Juara 1-3', 'vol1'=>1, 'sat1'=>'Paket', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>10000000]
                ],
                'Belanja Jasa' => [
                    ['id'=>3, 'uraian'=>'Juri', 'rincian'=>'Dosen Pakar', 'vol1'=>5, 'sat1'=>'Orang', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>1000000]
                ]
            ]
        ],
        14 => [
            'id' => 14, 
            'nama' => 'Donor Darah Bersama', 
            'pengusul' => 'Nanda Pratama', 
            'nim' => '2203003',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-22',
            'kode_mak' => '5241.001.052.E.521218',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Nanda Pratama',
                'nim_pengusul' => '2203003',
                'nama_penanggung_jawab' => 'Humas PNJ',
                'nip_penanggung_jawab' => '198001012000011001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Donor Darah Bersama', 
                'gambaran_umum' => 'Kegiatan sosial donor darah bekerjasama dengan PMI Kota Depok.', 
                'penerima_manfaat' => 'Masyarakat umum dan pasien RS.',
                'metode_pelaksanaan' => 'Layanan Kesehatan Langsung.',
                'tahapan_kegiatan' => "1. Registrasi\n2. Cek Kesehatan\n3. Pengambilan Darah\n4. Pemulihan",
                'surat_pengantar' => 'Surat_Pengantar_Donor_Darah_2024.pdf',
                'tanggal_mulai' => '2024-03-15',
                'tanggal_selesai' => '2024-03-15'
            ],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [
                ['bulan' => 'Februari', 'nama' => 'Kantong Darah', 'target' => 80]
            ],
            'rab' => [
                'Belanja Konsumsi' => [
                    ['id'=>1, 'uraian'=>'Paket Suplemen', 'rincian'=>'Susu & Roti', 'vol1'=>100, 'sat1'=>'Paket', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>25000]
                ]
            ]
        ],
        17 => [
            'id' => 17, 
            'nama' => 'Workshop Fotografi', 
            'pengusul' => 'Qori Amanda', 
            'nim' => '2206002',
            'jurusan' => 'Teknik Grafika dan Penerbitan',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-03-01',
            'kode_mak' => '5241.001.052.F.521219',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Qori Amanda',
                'nim_pengusul' => '2206002',
                'nama_penanggung_jawab' => 'Dosen Fotografi, M.Sn',
                'nip_penanggung_jawab' => '198505052010011009',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'nama_kegiatan' => 'Workshop Fotografi', 
                'gambaran_umum' => 'Workshop teknik fotografi produk dan editing untuk keperluan komersial.', 
                'penerima_manfaat' => 'Mahasiswa Desain dan Fotografer Pemula.',
                'metode_pelaksanaan' => 'Teori dan Praktik Studio.',
                'tahapan_kegiatan' => "1. Teori Pencahayaan\n2. Praktik Pemotretan Produk\n3. Teknik Editing (Post-processing)",
                'surat_pengantar' => 'Surat_Pengantar_Workshop_Foto_2024.pdf',
                'tanggal_mulai' => '2024-03-20',
                'tanggal_selesai' => '2024-03-22'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [
                ['bulan' => 'Maret', 'nama' => 'Peserta', 'target' => 30]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Sewa Lighting', 'rincian'=>'Studio Set', 'vol1'=>1, 'sat1'=>'Paket', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>2000000]
                ],
                'Belanja Jasa' => [
                    ['id'=>4, 'uraian'=>'Instruktur', 'rincian'=>'Fotografer Pro', 'vol1'=>2, 'sat1'=>'Orang', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>1500000]
                ]
            ]
        ],
        21 => [
            'id' => 21, 
            'nama' => 'Pekan Olahraga', 
            'pengusul' => 'Umar Faruq', 
            'nim' => '2205002',
            'jurusan' => 'Teknik Mesin',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-03-12',
            'kode_mak' => '5241.001.052.G.521220',
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Umar Faruq',
                'nim_pengusul' => '2205002',
                'nama_penanggung_jawab' => 'Ka. Unit Olahraga',
                'nip_penanggung_jawab' => '197501012000011005',
                'jurusan' => 'Teknik Mesin',
                'nama_kegiatan' => 'Pekan Olahraga', 
                'gambaran_umum' => 'Event olahraga antar jurusan (Futsal, Basket, Voli).', 
                'penerima_manfaat' => 'Mahasiswa PNJ.',
                'metode_pelaksanaan' => 'Pertandingan Sistem Kompetisi.',
                'tahapan_kegiatan' => "1. Pendaftaran Tim\n2. Technical Meeting\n3. Pertandingan\n4. Final & Penyerahan Hadiah",
                'surat_pengantar' => 'Surat_Pengantar_Pekan_Olahraga_2024.pdf',
                'tanggal_mulai' => '2024-04-01',
                'tanggal_selesai' => '2024-04-03'
            ],
            'iku' => ['Kesehatan', 'Aktivitas Mahasiswa'],
            'indikator' => [
                ['bulan' => 'Maret', 'nama' => 'Peserta', 'target' => 500]
            ],
            'rab' => [
                'Belanja Barang' => [
                    ['id'=>1, 'uraian'=>'Medali', 'rincian'=>'Set Emas/Perak/Perunggu', 'vol1'=>30, 'sat1'=>'Buah', 'vol2'=>1, 'sat2'=>'Kegiatan', 'harga'=>75000]
                ],
                'Belanja Jasa' => [
                    ['id'=>6, 'uraian'=>'Wasit', 'rincian'=>'Wasit Berlisensi', 'vol1'=>10, 'sat1'=>'Orang', 'vol2'=>3, 'sat2'=>'Hari', 'harga'=>200000]
                ]
            ]
        ]
    ];

    /**
     * Menampilkan HALAMAN DETAIL untuk PPK menelaah KAK/RAB.
     * Dipanggil oleh rute: /PPK/telaah/show/{id}
     */
    public function show($id, $data_dari_router = []) {
        
        // --- 1. Tentukan URL Kembali (Dinamis) ---
        $base_url = "/docutrack/public/ppk";
        $ref = $_GET['ref'] ?? '';

        switch ($ref) {
            case 'kegiatan':
            case 'pengajuan-kegiatan':
                $back_url = $base_url . '/pengajuan-kegiatan';
                break;
            case 'riwayat-verifikasi':
                $back_url = $base_url . '/riwayat-verifikasi';
                break;
            case 'dashboard':
                $back_url = $base_url . '/dashboard';
                break;
            default:
                // fallback otomatis ke halaman sebelumnya jika ada
                $back_url = $_SERVER['HTTP_REFERER'] ?? $base_url . '/dashboard';
                break;
        }

        // --- 2. Ambil Data dari Master List (Simulasi) ---
        $usulan_dipilih = $this->list_usulan_all[$id] ?? null;
        
        if (!$usulan_dipilih) {
            return not_found("Usulan dengan ID $id tidak ditemukan.");
        }
        
        $status = $usulan_dipilih['status'];
        // --- Akhir Simulasi ---

        // 3. Handle Surat Pengantar URL
        $surat_pengantar_url = '';
        if (!empty($usulan_dipilih['kak']['surat_pengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $usulan_dipilih['kak']['surat_pengantar'];
        }

        // 4. Kirim data (TERMASUK ROLE & STATUS) ke View
        $data = array_merge($data_dari_router, [
            'title' => 'Persetujuan PPK - ' . htmlspecialchars($usulan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'PPK', // <-- Mengirim 'PPK'
            
            // Data Payload
            'kegiatan_data' => $usulan_dipilih['kak'],
            'iku_data' => $usulan_dipilih['iku'],
            'indikator_data' => $usulan_dipilih['indikator'],
            'rab_data' => $usulan_dipilih['rab'],
            'kode_mak' => $usulan_dipilih['kode_mak'] ?? '',
            'komentar_penolakan' => $usulan_dipilih['komentar_penolakan'] ?? '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        // Panggil view 'telaah_detail' KHUSUS PPK
        // dan gunakan layout 'PPK'
        $this->view('pages/ppk/telaah_detail', $data, 'PPK');
    }
}