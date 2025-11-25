<?php
// File: src/controllers/Verifikator/VerifikatorTelaahController.php

require_once '../src/core/Controller.php';

class VerifikatorTelaahController extends Controller {
    
    /**
     * --- DATA MASTER LENGKAP (STRUKTUR PNJ) ---
     * 'jurusan' = Induk (Digunakan untuk Filter di Dropdown)
     * 'prodi'   = Spesifik (Digunakan untuk Tampilan di Tabel)
     */
    private $list_usulan_all = [
        // 1. DISSETUJUI (TIK - TI)
        1 => [
            'id' => 1, 
            'nama' => 'Seminar Nasional Teknologi', 
            'pengusul' => 'Ahmad Rizki', 
            'nim' => '2201001',
            'jurusan' => 'Teknik Informatika dan Komputer', // Induk
            'prodi' => 'Teknik Informatika', // Anak
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-01-15',
            'kode_mak' => '5241.001.052.A.521211',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Ahmad Rizki',
                'nim_pengusul' => '2201001',
                'nama_penanggung_jawab' => 'Dr. Techn. Budi Santoso',
                'nip_penanggung_jawab' => '197501012000121001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Seminar Nasional Teknologi', 
                'gambaran_umum' => 'Seminar nasional yang membahas perkembangan teknologi terkini di Indonesia (AI, Cloud, Big Data).', 
                'penerima_manfaat' => 'Mahasiswa TIK dan Umum.',
                'metode_pelaksanaan' => 'Hybrid (Daring via Zoom dan Luring di Auditorium).',
                'tahapan_kegiatan' => "1. Perencanaan & Persiapan (Minggu 1)\n2. Promosi & Registrasi (Minggu 2)\n3. Pelaksanaan Seminar (Hari H)\n4. Laporan Pertanggungjawaban",
                'surat_pengantar' => 'Surat_Pengantar_1.pdf',
                'tanggal_mulai' => '2024-01-15',
                'tanggal_selesai' => '2024-01-16'
            ],
            'iku' => ['Mendapat Pekerjaan'],
            'indikator' => [['bulan' => 'Januari', 'nama' => 'Peserta', 'target' => 200]],
            'rab' => [
                'Belanja Jasa' => [['id'=>1, 'uraian'=>'Honor Pembicara', 'rincian'=>'3 Orang', 'vol1'=>3, 'sat1'=>'Org', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>2000000]]
            ]
        ],

        // 2. REVISI (TIK - TMD)
        2 => [
            'id' => 2, 
            'nama' => 'Workshop AI & Machine Learning', 
            'pengusul' => 'Siti Nurhaliza', 
            'nim' => '2201002',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'Teknik Multimedia Digital',
            'status' => 'Revisi',
            'tanggal_pengajuan' => '2024-01-18',
            'kode_mak' => '',
            'komentar_revisi' => [
                'gambaran_umum' => 'Mohon jelaskan output spesifik dari workshop ini.',
                'rab_belanja_jasa' => 'Honor instruktur harap disesuaikan dengan SBM.'
            ],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Siti Nurhaliza',
                'nim_pengusul' => '2201002',
                'nama_penanggung_jawab' => 'Rina Suharti, M.Kom',
                'nip_penanggung_jawab' => '198205052005012003',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Workshop AI & Machine Learning', 
                'gambaran_umum' => 'Workshop penerapan AI menggunakan Python.', 
                'penerima_manfaat' => 'Mahasiswa TIK.',
                'metode_pelaksanaan' => 'Praktikum Lab.',
                'tahapan_kegiatan' => "1. Instalasi\n2. Materi Dasar\n3. Coding\n4. Presentasi",
                'surat_pengantar' => 'Surat_Pengantar_2.pdf',
                'tanggal_mulai' => '2024-01-18',
                'tanggal_selesai' => '2024-01-19'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [['bulan' => 'Januari', 'nama' => 'Peserta', 'target' => 50]],
            'rab' => [
                'Belanja Barang' => [['id'=>1, 'uraian'=>'Modul', 'rincian'=>'Buku', 'vol1'=>50, 'sat1'=>'Buku', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>50000]]
            ]
        ],

        // 3. TELAH DIREVISI (Elektro - TOLI)
        3 => [
            'id' => 3, 
            'nama' => 'Lomba Cerdas Cermat', 
            'pengusul' => 'Budi Santoso', 
            'nim' => '2202001',
            'jurusan' => 'Teknik Elektro',
            'prodi' => 'Teknik Otomasi Listrik Industri',
            'status' => 'Telah Direvisi',
            'tanggal_pengajuan' => '2024-01-20',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Budi Santoso',
                'nim_pengusul' => '2202001',
                'nama_penanggung_jawab' => 'Ir. Bambang Electro, M.T',
                'nip_penanggung_jawab' => '197003031998021001',
                'jurusan' => 'Teknik Elektro',
                'nama_kegiatan' => 'Lomba Cerdas Cermat', 
                'gambaran_umum' => 'Lomba cerdas cermat tingkat SMA se-Jabodetabek.', 
                'penerima_manfaat' => 'Siswa SMA.',
                'metode_pelaksanaan' => 'Luring di Aula.',
                'tahapan_kegiatan' => "1. Penyisihan\n2. Semifinal\n3. Final",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-02-15',
                'tanggal_selesai' => '2024-02-16'
            ],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'Februari', 'nama' => 'Tim Peserta', 'target' => 30]],
            'rab' => [
                'Belanja Hadiah' => [['id'=>1, 'uraian'=>'Piala', 'rincian'=>'Set', 'vol1'=>3, 'sat1'=>'Set', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>250000]]
            ]
        ],

        // 4. MENUNGGU (TIK - TICK)
        4 => [
            'id' => 4, 
            'nama' => 'Pelatihan Web Development', 
            'pengusul' => 'Dewi Lestari', 
            'nim' => '2203001',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'Teknologi Industri Cetak Kemasan',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-01-22',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Dewi Lestari',
                'nim_pengusul' => '2203001',
                'nama_penanggung_jawab' => 'Eko Prasetyo, M.Kom',
                'nip_penanggung_jawab' => '198808082015011005',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Pelatihan Web Development', 
                'gambaran_umum' => 'Pelatihan fullstack web dev.', 
                'penerima_manfaat' => 'Mahasiswa.',
                'metode_pelaksanaan' => 'Bootcamp.',
                'tahapan_kegiatan' => "1. HTML/CSS\n2. JS\n3. PHP/Laravel",
                'surat_pengantar' => 'Surat_4.pdf',
                'tanggal_mulai' => '2024-01-22',
                'tanggal_selesai' => '2024-01-24'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [['bulan' => 'Januari', 'nama' => 'Lulusan', 'target' => 40]],
            'rab' => [
                'Belanja Jasa' => [['id'=>3, 'uraian'=>'Instruktur', 'rincian'=>'Senior Dev', 'vol1'=>2, 'sat1'=>'Org', 'vol2'=>3, 'sat2'=>'Hari', 'harga'=>1500000]]
            ]
        ],

        // 5. DITOLAK (TIK - TI)
        5 => [
            'id' => 5, 
            'nama' => 'Turnamen E-Sport Kampus', 
            'pengusul' => 'Eko Prasetyo', 
            'nim' => '2203002',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'Teknik Informatika',
            'status' => 'Ditolak',
            'tanggal_pengajuan' => '2024-01-25',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => 'Anggaran terlalu besar dan tidak prioritas.',
            'kak' => [
                'nama_pengusul' => 'Eko Prasetyo',
                'nim_pengusul' => '2203002',
                'nama_penanggung_jawab' => 'Drs. Gaming, M.Pd',
                'nip_penanggung_jawab' => '199001012015011001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Turnamen E-Sport Kampus', 
                'gambaran_umum' => 'Turnamen Mobile Legends.', 
                'penerima_manfaat' => 'Mahasiswa.',
                'metode_pelaksanaan' => 'Kompetisi.',
                'tahapan_kegiatan' => "1. Daftar\n2. Main\n3. Menang",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-03-01',
                'tanggal_selesai' => '2024-03-02'
            ],
            'iku' => ['Prestasi Mahasiswa'],
            'indikator' => [],
            'rab' => []
        ],

        // 6. MENUNGGU (Akuntansi - Akun Keu)
        6 => [
            'id' => 6, 
            'nama' => 'Seminar Kewirausahaan', 
            'pengusul' => 'Fitri Handayani', 
            'nim' => '2204001',
            'jurusan' => 'Akuntansi',
            'prodi' => 'Akuntansi Keuangan',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-01',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Fitri Handayani',
                'nim_pengusul' => '2204001',
                'nama_penanggung_jawab' => 'Dra. Wira Usaha, MM',
                'nip_penanggung_jawab' => '196502021990012001',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Seminar Kewirausahaan', 
                'gambaran_umum' => 'Membangun jiwa entrepreneur mahasiswa.', 
                'penerima_manfaat' => 'Mahasiswa PNJ.',
                'metode_pelaksanaan' => 'Talkshow.',
                'tahapan_kegiatan' => "1. Pembukaan\n2. Materi\n3. Tanya Jawab",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-02-20',
                'tanggal_selesai' => '2024-02-20'
            ],
            'iku' => ['Wirausaha'],
            'indikator' => [['bulan' => 'Februari', 'nama' => 'Peserta', 'target' => 150]],
            'rab' => [
                'Belanja Barang' => [['id'=>1, 'uraian'=>'Snack', 'rincian'=>'Box', 'vol1'=>150, 'sat1'=>'Org', 'vol2'=>1, 'sat2'=>'Kali', 'harga'=>20000]]
            ]
        ],

        // 7. DISETUJUI (Akuntansi - Keu & Bank)
        7 => [
            'id' => 7, 
            'nama' => 'Pelatihan Dasar Akuntansi UMKM', 
            'pengusul' => 'Rina Wati', 
            'nim' => '2204002',
            'jurusan' => 'Akuntansi',
            'prodi' => 'Keuangan dan Perbankan',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-02-05',
            'kode_mak' => '5241.001.052.C.521216',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Rina Wati',
                'nim_pengusul' => '2204002',
                'nama_penanggung_jawab' => 'Dra. Akuntani, M.Ak',
                'nip_penanggung_jawab' => '197001011995012001',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Pelatihan Dasar Akuntansi UMKM', 
                'gambaran_umum' => 'Pelatihan pembukuan sederhana untuk UMKM Depok.', 
                'penerima_manfaat' => 'Pelaku UMKM.',
                'metode_pelaksanaan' => 'Workshop.',
                'tahapan_kegiatan' => "1. Rekrutmen Peserta\n2. Pelatihan\n3. Pendampingan",
                'surat_pengantar' => 'Surat_7.pdf',
                'tanggal_mulai' => '2024-04-10',
                'tanggal_selesai' => '2024-04-12'
            ],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [['bulan' => 'April', 'nama' => 'Peserta UMKM', 'target' => 30]],
            'rab' => [
                'Belanja Jasa' => [['id'=>4, 'uraian'=>'Narasumber', 'rincian'=>'Dosen', 'vol1'=>2, 'sat1'=>'Org', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>1000000]]
            ]
        ],

        // 8. MENUNGGU (TGP - Desain Grafis)
        8 => [
            'id' => 8, 
            'nama' => 'Workshop Fotografi', 
            'pengusul' => 'Qori Amanda', 
            'nim' => '2206002',
            'jurusan' => 'Teknik Grafika dan Penerbitan',
            'prodi' => 'Desain Grafis',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-03-01',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Qori Amanda',
                'nim_pengusul' => '2206002',
                'nama_penanggung_jawab' => 'Dosen Fotografi, M.Sn',
                'nip_penanggung_jawab' => '198505052010011009',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'nama_kegiatan' => 'Workshop Fotografi', 
                'gambaran_umum' => 'Workshop teknik foto produk.', 
                'penerima_manfaat' => 'Mahasiswa TGP.',
                'metode_pelaksanaan' => 'Praktik Studio.',
                'tahapan_kegiatan' => "1. Teori\n2. Praktik\n3. Review",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-03-20',
                'tanggal_selesai' => '2024-03-22'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [['bulan' => 'Maret', 'nama' => 'Peserta', 'target' => 30]],
            'rab' => [
                'Belanja Barang' => [['id'=>1, 'uraian'=>'Sewa Alat', 'rincian'=>'Lampu Studio', 'vol1'=>1, 'sat1'=>'Paket', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>2000000]]
            ]
        ],

        // 9. TELAH DIREVISI (Akuntansi - Manaj Keu)
        9 => [
            'id' => 9, 
            'nama' => 'Webinar Digital Marketing', 
            'pengusul' => 'Intan Permata', 
            'nim' => '2204003',
            'jurusan' => 'Akuntansi',
            'prodi' => 'Manajemen Keuangan',
            'status' => 'Telah Direvisi',
            'tanggal_pengajuan' => '2024-02-10',
            'kode_mak' => '',
            'komentar_revisi' => ['gambaran_umum' => 'Jelaskan tools yang dipakai.'],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Intan Permata',
                'nim_pengusul' => '2204003',
                'nama_penanggung_jawab' => 'Siti Marketing, SE',
                'nip_penanggung_jawab' => '198505052010012005',
                'jurusan' => 'Akuntansi',
                'nama_kegiatan' => 'Webinar Digital Marketing', 
                'gambaran_umum' => 'Webinar strategi pemasaran digital untuk UMKM.', 
                'penerima_manfaat' => 'UMKM dan Mahasiswa.',
                'metode_pelaksanaan' => 'Daring (Zoom).',
                'tahapan_kegiatan' => "1. Registrasi\n2. Webinar\n3. Sertifikat",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-02-25',
                'tanggal_selesai' => '2024-02-25'
            ],
            'iku' => ['Wirausaha'],
            'indikator' => [['bulan' => 'Februari', 'nama' => 'Peserta', 'target' => 300]],
            'rab' => [
                'Belanja Jasa' => [['id'=>1, 'uraian'=>'Zoom', 'rincian'=>'Sewa Akun', 'vol1'=>1, 'sat1'=>'Pkt', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>500000]]
            ]
        ],

        // 10. DISETUJUI (Mesin - Mesin)
        10 => [
            'id' => 10, 
            'nama' => 'Pelatihan Microsoft Office', 
            'pengusul' => 'Joko Susilo', 
            'nim' => '2205001',
            'jurusan' => 'Teknik Mesin',
            'prodi' => 'Teknik Mesin',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-02-15',
            'kode_mak' => '5241.001.052.E.521220',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Joko Susilo',
                'nim_pengusul' => '2205001',
                'nama_penanggung_jawab' => 'Ir. Mesin Kuat, M.T',
                'nip_penanggung_jawab' => '197808082005011001',
                'jurusan' => 'Teknik Mesin',
                'nama_kegiatan' => 'Pelatihan Microsoft Office', 
                'gambaran_umum' => 'Pelatihan Word, Excel, PPT untuk administrasi.', 
                'penerima_manfaat' => 'Mahasiswa Mesin.',
                'metode_pelaksanaan' => 'Lab Komputer.',
                'tahapan_kegiatan' => "1. Pre-test\n2. Pelatihan\n3. Post-test",
                'surat_pengantar' => 'Surat_10.pdf',
                'tanggal_mulai' => '2024-03-01',
                'tanggal_selesai' => '2024-03-02'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [['bulan' => 'Maret', 'nama' => 'Lulusan', 'target' => 40]],
            'rab' => [
                'Belanja Barang' => [['id'=>1, 'uraian'=>'Modul', 'rincian'=>'Buku', 'vol1'=>40, 'sat1'=>'Buku', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>35000]]
            ]
        ],

        // 11. MENUNGGU (Mesin - Konversi Energi)
        11 => [
            'id' => 11, 
            'nama' => 'Lomba Karya Tulis Ilmiah', 
            'pengusul' => 'Kartika Sari', 
            'nim' => '2205002',
            'jurusan' => 'Teknik Mesin',
            'prodi' => 'Teknik Konversi Energi',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-18',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Kartika Sari',
                'nim_pengusul' => '2205002',
                'nama_penanggung_jawab' => 'Dr. Energi, M.Sc',
                'nip_penanggung_jawab' => '198001012008012001',
                'jurusan' => 'Teknik Mesin',
                'nama_kegiatan' => 'Lomba Karya Tulis Ilmiah', 
                'gambaran_umum' => 'Lomba KTI tema energi terbarukan.', 
                'penerima_manfaat' => 'Mahasiswa Nasional.',
                'metode_pelaksanaan' => 'Hybrid.',
                'tahapan_kegiatan' => "1. Submit Paper\n2. Review\n3. Final",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-04-01',
                'tanggal_selesai' => '2024-04-02'
            ],
            'iku' => ['Prestasi'],
            'indikator' => [['bulan' => 'April', 'nama' => 'Karya Masuk', 'target' => 50]],
            'rab' => [
                'Belanja Hadiah' => [['id'=>1, 'uraian'=>'Hadiah', 'rincian'=>'Uang Tunai', 'vol1'=>1, 'sat1'=>'Pkt', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>5000000]]
            ]
        ],

        // 12. DISETUJUI (TGP - Penerbitan)
        12 => [
            'id' => 12, 
            'nama' => 'Pelatihan Public Speaking', 
            'pengusul' => 'Linda Wijaya', 
            'nim' => '2206001',
            'jurusan' => 'Teknik Grafika dan Penerbitan',
            'prodi' => 'Penerbitan',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-02-20',
            'kode_mak' => '5241.001.052.F.521221',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Linda Wijaya',
                'nim_pengusul' => '2206001',
                'nama_penanggung_jawab' => 'Dra. Komunikasi, M.I.Kom',
                'nip_penanggung_jawab' => '197505052005012001',
                'jurusan' => 'Teknik Grafika dan Penerbitan',
                'nama_kegiatan' => 'Pelatihan Public Speaking', 
                'gambaran_umum' => 'Pelatihan bicara depan umum.', 
                'penerima_manfaat' => 'Mahasiswa Penerbitan.',
                'metode_pelaksanaan' => 'Workshop.',
                'tahapan_kegiatan' => "1. Teori\n2. Praktik",
                'surat_pengantar' => 'Surat_12.pdf',
                'tanggal_mulai' => '2024-03-10',
                'tanggal_selesai' => '2024-03-10'
            ],
            'iku' => ['Kompetensi Lulusan'],
            'indikator' => [['bulan' => 'Maret', 'nama' => 'Peserta', 'target' => 40]],
            'rab' => [
                'Belanja Jasa' => [['id'=>1, 'uraian'=>'Trainer', 'rincian'=>'Pro', 'vol1'=>1, 'sat1'=>'Org', 'vol2'=>1, 'sat2'=>'Hari', 'harga'=>2000000]]
            ]
        ],

        // 13. REVISI (TIK - TMJ)
        13 => [
            'id' => 13, 
            'nama' => 'Workshop Video Editing', 
            'pengusul' => 'Muhammad Iqbal', 
            'nim' => '2201003',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'Teknik Multimedia Jaringan',
            'status' => 'Revisi',
            'tanggal_pengajuan' => '2024-02-22',
            'kode_mak' => '',
            'komentar_revisi' => ['rab_belanja_barang' => 'Kurangi biaya konsumsi.'],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Muhammad Iqbal',
                'nim_pengusul' => '2201003',
                'nama_penanggung_jawab' => 'Pak Multimedia, S.Kom',
                'nip_penanggung_jawab' => '199001012018011001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Workshop Video Editing', 
                'gambaran_umum' => 'Editing video dengan Premiere Pro.', 
                'penerima_manfaat' => 'Mahasiswa TMJ.',
                'metode_pelaksanaan' => 'Lab Multimedia.',
                'tahapan_kegiatan' => "1. Basic Cut\n2. Effect\n3. Rendering",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-03-15',
                'tanggal_selesai' => '2024-03-16'
            ],
            'iku' => ['Kompetensi Teknis'],
            'indikator' => [['bulan' => 'Maret', 'nama' => 'Peserta', 'target' => 30]],
            'rab' => [
                'Belanja Barang' => [['id'=>1, 'uraian'=>'Snack', 'rincian'=>'Box', 'vol1'=>30, 'sat1'=>'Org', 'vol2'=>2, 'sat2'=>'Hari', 'harga'=>25000]]
            ]
        ],

        // 14. MENUNGGU (TIK - TMD)
        14 => [
            'id' => 14, 
            'nama' => 'Donor Darah Bersama', 
            'pengusul' => 'Nanda Pratama', 
            'nim' => '2203003',
            'jurusan' => 'Teknik Informatika dan Komputer',
            'prodi' => 'Teknik Multimedia Digital',
            'status' => 'Menunggu',
            'tanggal_pengajuan' => '2024-02-25',
            'kode_mak' => '',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Nanda Pratama',
                'nim_pengusul' => '2203003',
                'nama_penanggung_jawab' => 'Humas PNJ',
                'nip_penanggung_jawab' => '198001012000011001',
                'jurusan' => 'Teknik Informatika dan Komputer',
                'nama_kegiatan' => 'Donor Darah Bersama', 
                'gambaran_umum' => 'Kerjasama dengan PMI.', 
                'penerima_manfaat' => 'Umum.',
                'metode_pelaksanaan' => 'Layanan Medis.',
                'tahapan_kegiatan' => "1. Daftar\n2. Cek\n3. Donor",
                'surat_pengantar' => '',
                'tanggal_mulai' => '2024-03-20',
                'tanggal_selesai' => '2024-03-20'
            ],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [['bulan' => 'Maret', 'nama' => 'Kantong Darah', 'target' => 50]],
            'rab' => [
                'Belanja Konsumsi' => [['id'=>1, 'uraian'=>'Susu', 'rincian'=>'Kotak', 'vol1'=>50, 'sat1'=>'Pcs', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>5000]]
            ]
        ],

        // 15. DISETUJUI (Elektro - Broadband)
        15 => [
            'id' => 15, 
            'nama' => 'Bakti Sosial Ramadhan', 
            'pengusul' => 'Oktavia Ningsih', 
            'nim' => '2202002',
            'jurusan' => 'Teknik Elektro',
            'prodi' => 'Broadband Multimedia',
            'status' => 'Disetujui',
            'tanggal_pengajuan' => '2024-02-28',
            'kode_mak' => '5241.001.052.B.521225',
            'komentar_revisi' => [],
            'komentar_penolakan' => '',
            'kak' => [
                'nama_pengusul' => 'Oktavia Ningsih',
                'nim_pengusul' => '2202002',
                'nama_penanggung_jawab' => 'Ka. Prodi Elektro',
                'nip_penanggung_jawab' => '197202021999011001',
                'jurusan' => 'Teknik Elektro',
                'nama_kegiatan' => 'Bakti Sosial Ramadhan', 
                'gambaran_umum' => 'Santunan anak yatim.', 
                'penerima_manfaat' => 'Anak Yatim.',
                'metode_pelaksanaan' => 'Kunjungan Panti.',
                'tahapan_kegiatan' => "1. Survey\n2. Donasi\n3. Penyerahan",
                'surat_pengantar' => 'Surat_15.pdf',
                'tanggal_mulai' => '2024-03-25',
                'tanggal_selesai' => '2024-03-25'
            ],
            'iku' => ['Pengabdian Masyarakat'],
            'indikator' => [['bulan' => 'Maret', 'nama' => 'Penerima', 'target' => 50]],
            'rab' => [
                'Belanja Barang' => [['id'=>1, 'uraian'=>'Sembako', 'rincian'=>'Paket', 'vol1'=>50, 'sat1'=>'Pkt', 'vol2'=>1, 'sat2'=>'Keg', 'harga'=>100000]]
            ]
        ],
    ];

    /**
     * METHOD: index()
     * Menampilkan halaman daftar antrian telaah.
     */
    public function index($data_dari_router = []) {
        
        // Filter hanya "Menunggu" dan "Telah Direvisi"
        $list_usulan = [];
        $jurusan_set = [];
        
        foreach ($this->list_usulan_all as $usulan) {
            $status_lower = strtolower($usulan['status']);
            if ($status_lower === 'menunggu' || $status_lower === 'telah direvisi') {
                $list_usulan[] = $usulan;
                if (!empty($usulan['jurusan'])) {
                    $jurusan_set[$usulan['jurusan']] = true;
                }
            }
        }
        
        // Sorting: Prioritaskan "Telah Direvisi"
        usort($list_usulan, function($a, $b) {
            $priority = ['telah direvisi' => 1, 'menunggu' => 2];
            $a_status = strtolower($a['status']);
            $b_status = strtolower($b['status']);
            
            $a_prio = $priority[$a_status] ?? 99;
            $b_prio = $priority[$b_status] ?? 99;
            
            if ($a_prio === $b_prio) {
                return strcmp($a['tanggal_pengajuan'], $b['tanggal_pengajuan']);
            }
            return $a_prio - $b_prio;
        });

        // Kirim data ke View (Pagination & Filtering akan di-handle oleh JavaScript di client-side)
        // Kita kirim FULL LIST (bukan paginated) agar JS bisa handle pagination dengan benar.
        $data = array_merge($data_dari_router, [
            'title' => 'Antrian Pengajuan Telaah',
            'list_usulan' => $list_usulan, 
            'jurusan_list' => array_keys($jurusan_set),
            'jumlah_menunggu' => count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'menunggu')),
            'jumlah_telah_direvisi' => count(array_filter($list_usulan, fn($u) => strtolower($u['status']) === 'telah direvisi'))
        ]);

        $this->view('pages/verifikator/pengajuan_telaah', $data, 'verifikator');
    }

    /**
     * METHOD: show()
     * Menampilkan halaman detail telaah.
     */
    public function show($id, $data_dari_router = []) {
        $ref = $_GET['ref'] ?? '';
        $base_url = "/docutrack/public/verifikator";
        
        switch ($ref) {
            case 'dashboard': $back_url = $base_url . '/dashboard'; break;
            case 'pengajuan-telaah': $back_url = $base_url . '/pengajuan-telaah'; break;
            case 'riwayat-verifikasi': $back_url = $base_url . '/riwayat-verifikasi'; break;
            default: $back_url = $_SERVER['HTTP_REFERER'] ?? $base_url . '/pengajuan-telaah'; break;
        }

        $usulan_dipilih = $this->list_usulan_all[$id] ?? null;
        
        if (!$usulan_dipilih) {
            $_SESSION['error'] = "Usulan dengan ID $id tidak ditemukan.";
            header("Location: $back_url");
            exit;
        }
        
        $status = $usulan_dipilih['status'];
        $surat_pengantar_url = '';
        if (!empty($usulan_dipilih['kak']['surat_pengantar'])) {
            $surat_pengantar_url = '/docutrack/public/uploads/surat/' . $usulan_dipilih['kak']['surat_pengantar'];
        }

        $data = array_merge($data_dari_router, [
            'title' => 'Telaah Usulan - ' . htmlspecialchars($usulan_dipilih['kak']['nama_kegiatan']),
            'status' => $status,
            'user_role' => $_SESSION['user_role'] ?? 'verifikator',
            'id' => $id,
            'kegiatan_data' => $usulan_dipilih['kak'],
            'iku_data' => $usulan_dipilih['iku'],
            'indikator_data' => $usulan_dipilih['indikator'],
            'rab_data' => $usulan_dipilih['rab'],
            'kode_mak' => $usulan_dipilih['kode_mak'] ?? '',
            'komentar_revisi' => $usulan_dipilih['komentar_revisi'] ?? [],
            'komentar_penolakan' => $usulan_dipilih['komentar_penolakan'] ?? '',
            'surat_pengantar_url' => $surat_pengantar_url,
            'back_url' => $back_url
        ]);

        $this->view('pages/verifikator/telaah_detail', $data, 'verifikator');
    }

    public function approve($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/verifikator/pengajuan-telaah');
            exit;
        }
        $_SESSION['success'] = 'Usulan berhasil disetujui dengan Kode MAK: ' . ($_POST['kode_mak'] ?? 'N/A');
        header('Location: /docutrack/public/verifikator/pengajuan-telaah');
        exit;
    }

    public function reject($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/verifikator/pengajuan-telaah');
            exit;
        }
        $_SESSION['success'] = 'Usulan berhasil ditolak dengan alasan: ' . ($_POST['alasan_penolakan'] ?? 'N/A');
        header('Location: /docutrack/public/verifikator/pengajuan-telaah');
        exit;
    }

    public function revise($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /docutrack/public/verifikator/pengajuan-telaah');
            exit;
        }
        $komentar = $_POST['komentar'] ?? [];
        $count = count(array_filter($komentar));
        $_SESSION['success'] = "Komentar revisi berhasil dikirim! Total: $count catatan.";
        header('Location: /docutrack/public/verifikator/pengajuan-telaah');
        exit;
    }
}