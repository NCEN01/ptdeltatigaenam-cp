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
                'id' => 'Materi yang Anda terima mengikuti standar kompetensi dan aturan sektor yang berlaku saat program berjalan, bukan versi tahun lalu.',
                'en' => 'What you receive follows the competency standards and sector rules in force while the programme runs, not last year’s version.',
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
                'id' => 'Angkatan Anda memakai materi yang sudah diperbaiki dari masukan angkatan sebelumnya, bukan salinan yang sama berulang.',
                'en' => 'Your intake works from material already corrected by the previous intake’s feedback, not the same copy run again.',
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
                'id' => 'Untuk bidang yang belum punya skema baku, kami susunkan bersama asesor yang menanganinya langsung di lapangan.',
                'en' => 'For a field with no settled scheme yet, we build one with the assessors who work in it on the ground.',
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
                'id' => 'Anda tidak ditinggal begitu kelas selesai. Pendampingan berlanjut sampai kompetensinya terpakai di pekerjaan harian.',
                'en' => 'You are not left once the class ends. Support continues until the competency is in daily use.',
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
                'id' => 'Setiap rekomendasi datang dengan langkah yang bisa Anda jalankan minggu itu juga, lengkap dengan penanggung jawabnya.',
                'en' => 'Every recommendation arrives with steps you can start that week, each with a named owner.',
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

    /*
    |--------------------------------------------------------------------------
    | Nilai Perusahaan (halaman Tentang)
    |--------------------------------------------------------------------------
    |
    | Daftar terpisah dari 'values' di atas, dan memang harus terpisah.
    |
    | 'values' adalah akronim DELTA dan sudah dipakai seksi "Alasan Memilih
    | Kami" di beranda. Menampilkan daftar yang sama di halaman Tentang membuat
    | pengunjung membaca lima judul yang persis sama dua kali dalam satu
    | kunjungan, dan yang kedua tidak menambah apa pun.
    |
    | Isinya pun menjawab pertanyaan yang berbeda. Yang di beranda menjawab
    | "apa untungnya bagi saya"; yang di sini menjawab "lembaga ini bekerja
    | dengan cara seperti apa" — sikap, bukan manfaat. Karena itu judulnya
    | berbahasa Indonesia di versi Indonesia, bukan satu kata Inggris yang
    | harus membentuk akronim.
    |
    | Dibaca lewat App\Support\Company::aboutValues().
    |
    */

    'about_values' => [
        [
            'title' => [
                'id' => 'Kompetensi dulu, sertifikat kemudian',
                'en' => 'Competency first, certificate after',
            ],
            'detail' => [
                'id' => 'Sertifikat hanya catatan atas sesuatu yang sudah terbukti. Yang dikejar lebih dulu kemampuan pesertanya, baru kertasnya.',
                'en' => 'A certificate only records something already proven. The ability comes first, the paper second.',
            ],
        ],
        [
            'title' => [
                'id' => 'Terbuka untuk diperiksa',
                'en' => 'Open to inspection',
            ],
            'detail' => [
                'id' => 'Nomor sertifikat, skema yang dipakai, dan lembaga penerbitnya dibuka di situs ini. Yang bisa diperiksa orang lain adalah yang pantas dijanjikan.',
                'en' => 'Certificate numbers, the scheme used, and the issuing body are published on this site. What others can verify is what deserves to be promised.',
            ],
        ],
        [
            'title' => [
                'id' => 'Satu standar untuk semua ukuran',
                'en' => 'One standard at every size',
            ],
            'detail' => [
                'id' => 'Perusahaan yang mengirim sepuluh peserta mendapat asesor, materi, dan laporan yang sama dengan yang mengirim ratusan.',
                'en' => 'A company sending ten participants gets the same assessors, material, and reporting as one sending hundreds.',
            ],
        ],
        [
            'title' => [
                'id' => 'Berani mengatakan belum bisa',
                'en' => 'Willing to say not yet',
            ],
            'detail' => [
                'id' => 'Kebutuhan yang berada di luar jangkauan kami disampaikan apa adanya, lengkap dengan saran ke mana sebaiknya dibawa.',
                'en' => 'A need beyond what we can do is said plainly, together with where it is better taken.',
            ],
        ],
        [
            'title' => [
                'id' => 'Yang mengajar ikut diuji',
                'en' => 'Those who teach are assessed too',
            ],
            'detail' => [
                'id' => 'Pengajar dan asesor menjalani pemutakhiran kompetensi secara berkala. Lembaga yang berhenti belajar tidak pantas mengajarkan kompetensi.',
                'en' => 'Instructors and assessors renew their own competency on a schedule. An institution that stops learning has no business teaching competency.',
            ],
        ],
    ],

];
