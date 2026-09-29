<?php

return [

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
    | Namanya tidak diterjemahkan: "Head Office" dan "Operational Office"
    | adalah sebutan yang dipakai perusahaan sendiri di materi resminya,
    | jadi sama di kedua bahasa.
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

    'offices' => [
        [
            'name' => 'Head Office',
            'address' => 'Gedung Bursa Efek Indonesia Tower 1 Level 3, Unit 304, Jalan Jendral Sudirman Kav. 52-53, Senayan, Kebayoran Baru, Jakarta Selatan – DKI Jakarta 12190',
            'phone' => '(021) 5890 5002',
            'tel' => '+622158905002',
            'maps' => 'Gedung Bursa Efek Indonesia Tower 1, Jalan Jenderal Sudirman Kav. 52-53, Senayan, Kebayoran Baru, Jakarta Selatan 12190',
        ],
        [
            'name' => 'Operational Office',
            'address' => 'Delta House, Pejaten Mas Estate Blok D3 No. 6, Pejaten, Kramatwatu, Serang – Banten 42161',
            'phone' => '0818 834 766',
            'tel' => '+62818834766',
            'maps' => 'Pejaten Mas Estate Blok D3 No. 6, Pejaten, Kramatwatu, Serang, Banten 42161',
        ],
    ],

];
