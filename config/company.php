<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Halaman yang ditampilkan
    |--------------------------------------------------------------------------
    |
    | Saklar untuk menyembunyikan satu halaman dari pengunjung tanpa menghapus
    | apa pun. Ubah ke true, dan halamannya muncul kembali utuh — tautannya di
    | menu atas, di tautan cepat footer, di bilah promo, seksinya di beranda,
    | dan halamannya sendiri. Tidak ada berkas yang perlu dipulihkan.
    |
    | Isinya sendiri tidak disentuh: datanya tetap di basis data dan tetap bisa
    | disunting lewat panel admin selagi halamannya disembunyikan, jadi begitu
    | dinyalakan lagi ia langsung berisi.
    |
    | 'agenda' => false sejak 29 September 2026 atas permintaan, sementara
    | sampai isi situs benar-benar final.
    |
    */

    'pages' => [
        'agenda' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Kantor
    |--------------------------------------------------------------------------
    |
    | Satu sumber untuk alamat kantor. Sebelumnya daftar yang sama ditulis
    | ulang di ContactController, PageController, dan footer — tiga salinan
    | yang harus diperbarui bersamaan setiap kali alamatnya berubah, dan
    | tidak ada yang mengingatkan kalau salah satunya terlewat.
    |
    | Namanya mengikuti bahasa. Materi resmi perusahaan memakai istilah Inggris
    | dan itu dipertahankan di versi Inggris, sementara versi Indonesia memakai
    | sebutan Indonesia. Nilainya dibaca lewat App\Support\Company::offices(),
    | yang memilih satu bahasa lalu mengembalikannya sebagai teks biasa —
    | sehingga pemakainya di footer, halaman kontak, dan peta sematannya tidak
    | perlu tahu bahwa aslinya dwibahasa.
    |
    | 'address' ditulis sebagai satu kalimat tanpa pemutus baris supaya bisa
    | mengalir mengikuti lebar tempatnya dipasang — kolom footer jauh lebih
    | sempit daripada kartu di halaman kontak, dan pemutus baris yang dipatok
    | akan jatuh di tempat yang salah di salah satunya.
    |
    | 'maps' adalah kata kunci pencarian untuk peta sematan di halaman
    | kontak, bukan alamat tampilan: ia sengaja dibuat lebih pendek dan tanpa
    | tanda pisah supaya Google Maps menemukannya.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Nilai perusahaan
    |--------------------------------------------------------------------------
    |
    | Huruf depan kelimanya mengeja DELTA, nama perusahaan itu sendiri. Karena
    | itu namanya tidak diterjemahkan: begitu jadi Dinamis/Keunggulan/Terdepan/
    | Transenden/Aksi, susunannya berbunyi D-K-T-T-A dan akronimnya hilang.
    | Urutannya pun tidak boleh diubah.
    |
    | Dibaca lewat App\Support\Company::values(), yang memilih keterangan sesuai
    | bahasa aktif. Diletakkan di sini karena dipakai dua halaman — beranda dan
    | Tentang Kami; ditulis di masing-masing controller, keduanya akan menyimpang
    | begitu salah satu disunting.
    |
    | 'img' menunjuk ke public/images/values/<nama>.jpg.
    |
    | Tiap nilai punya dua keterangan, dan keduanya tidak boleh sama:
    |
    |   'desc'   satu kalimat pernyataan, dipakai seksi "Alasan Memilih Kami"
    |            di beranda. Tugasnya menyatakan sikapnya.
    |
    |   'detail' penjelasan yang lebih dalam, dipakai seksi "Nilai Perusahaan"
    |            di halaman Tentang Kami. Tugasnya menyebut apa yang
    |            benar-benar dikerjakan, sehingga nilai itu bisa diperiksa dan
    |            bukan sekadar diklaim.
    |
    | Dua halaman yang menampilkan kalimat yang sama persis membuat yang kedua
    | terasa sebagai salinan, dan tidak satu pun dari keduanya menambah
    | keterangan baru bagi pembaca yang sudah membaca yang pertama.
    |
    */

    'values' => [
        [
            'title' => 'Dynamic',
            'img' => 'dynamic',
            'desc' => [
                'id' => 'Selalu adaptif terhadap perubahan dan inovasi dalam dunia kerja.',
                'en' => 'Always adaptive to change and innovation in the world of work.',
            ],
            'detail' => [
                'id' => 'Silabus kami ditinjau ulang setiap kali standar kompetensi atau aturan sektornya berubah, sehingga peserta mempelajari ketentuan yang berlaku hari ini.',
                'en' => 'We revisit the syllabus whenever a competency standard or a sector regulation changes, so participants learn the rules that apply today.',
            ],
        ],
        [
            'title' => 'Excellence',
            'img' => 'excellence',
            'desc' => [
                'id' => 'Unggul dalam setiap layanan yang diberikan.',
                'en' => 'Excelling in every service we deliver.',
            ],
            'detail' => [
                'id' => 'Setiap kelas ditutup dengan evaluasi peserta, dan hasilnya kami pakai menyusun ulang materi sebelum angkatan berikutnya dibuka.',
                'en' => 'Every class closes with a participant evaluation, and we rebuild the material from those results before the next intake opens.',
            ],
        ],
        [
            'title' => 'Leading',
            'img' => 'leading',
            'desc' => [
                'id' => 'Pionir dalam pengembangan solusi human capital yang inovatif.',
                'en' => 'A pioneer in developing innovative human capital solutions.',
            ],
            'detail' => [
                'id' => 'Kami menyusun skema pelatihan untuk bidang yang belum punya rujukan baku, bersama asesor dan praktisi yang menanganinya langsung di lapangan.',
                'en' => 'We build training schemes for fields that have no settled reference yet, alongside the assessors and practitioners who handle them on the ground.',
            ],
        ],
        [
            'title' => 'Transcendent',
            'img' => 'transcendent',
            'desc' => [
                'id' => 'Melampaui ekspektasi klien dan memberikan nilai tambah yang berkelanjutan.',
                'en' => 'Exceeding client expectations and delivering sustainable added value.',
            ],
            'detail' => [
                'id' => 'Pendampingan tidak berhenti di hari terakhir kelas. Kami menemani penerapannya sampai kompetensi itu benar-benar terpakai di pekerjaan sehari-hari.',
                'en' => 'Support does not stop on the last day of class. We stay with the rollout until the competency is genuinely being used in daily work.',
            ],
        ],
        [
            'title' => 'Action',
            'img' => 'action',
            'desc' => [
                'id' => 'Berorientasi pada hasil dan tindakan nyata untuk mencapai tujuan.',
                'en' => 'Results-oriented, with real action to achieve goals.',
            ],
            'detail' => [
                'id' => 'Setiap rekomendasi kami sertai langkah yang bisa langsung dijalankan, lengkap dengan penanggung jawab dan tenggat waktunya.',
                'en' => 'Every recommendation comes with steps that can start immediately, each with an owner and a deadline.',
            ],
        ],
    ],

    'offices' => [
        [
            'name' => ['id' => 'Lokasi Kantor', 'en' => 'Head Office'],
            'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, Senayan, Kebayoran Baru, Jakarta Selatan – DKI Jakarta 12190',
            'phone' => '(021) 5890 5002',
            'tel' => '+622158905002',
            'maps' => 'Gedung Bursa Efek Indonesia Tower 1, Jalan Jenderal Sudirman Kav. 52-53, Senayan, Kebayoran Baru, Jakarta Selatan 12190',
        ],
        [
            'name' => ['id' => 'Kantor Operasional', 'en' => 'Operational Office'],
            'address' => 'Delta House, Pejaten Mas Estate Blok D3 No. 6, Pejaten, Kramatwatu, Serang – Banten 42161',
            'phone' => '0818 834 766',
            'tel' => '+62818834766',
            'maps' => 'Pejaten Mas Estate Blok D3 No. 6, Pejaten, Kramatwatu, Serang, Banten 42161',
        ],
    ],

];
