<?php

return [

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA v2 ("Saya bukan robot")
    |--------------------------------------------------------------------------
    |
    | Kotak centang Google yang diisi pengunjung sebelum mengirim formulir.
    | Dipasang sebagai lapisan kedua, bukan pengganti: perangkap honeypot
    | (App\Http\Middleware\PreventSpamHoneypot) dan pembatasan throttle:5,1
    | tetap berjalan seperti sebelumnya.
    |
    | Dipilih v2 kotak centang, bukan v3 skor. v3 bekerja diam-diam dan menolak
    | pengunjung tanpa memberi tahu alasannya — dan ketika ia keliru menilai,
    | yang ditolak tidak punya cara apa pun untuk membuktikan dirinya manusia.
    | Pada formulir yang isinya permintaan penawaran bernilai besar, pengunjung
    | yang tertolak diam-diam adalah kerugian yang tidak pernah terlihat.
    |
    | KUNCINYA BELUM DIISI, dan selama itu seluruh fitur ini TIDAK AKTIF:
    | komponennya tidak merender apa pun dan aturannya meloloskan semua
    | kiriman, sehingga situs berjalan persis seperti sebelum fitur ini ada.
    | Begitu kedua kunci diisi di .env, ia menyala sendiri tanpa perubahan
    | kode sedikit pun.
    |
    | Mengambil kuncinya: https://www.google.com/recaptcha/admin/create
    | Pilih "Challenge (v2)" lalu "I'm not a robot Checkbox", dan daftarkan
    | domain produksi beserta localhost untuk pengujian.
    |
    */

    'site_key' => env('RECAPTCHA_SITE_KEY'),

    'secret_key' => env('RECAPTCHA_SECRET_KEY'),

    /*
    | Menyala sendiri hanya ketika KEDUANYA terisi. Satu kunci saja tidak
    | cukup: site key tanpa secret membuat kotaknya tampil tetapi tidak pernah
    | bisa diperiksa, dan setiap kiriman akan ditolak.
    */
    'enabled' => env('RECAPTCHA_SITE_KEY') && env('RECAPTCHA_SECRET_KEY'),

    /*
    | Batas waktu permintaan ke peladen Google. Dibuat pendek dengan sengaja:
    | kalau Google sedang tidak bisa dihubungi, pengunjung tidak boleh
    | menunggu lama di depan tombol kirim yang membeku.
    */
    'timeout' => (int) env('RECAPTCHA_TIMEOUT', 5),

    /*
    | Apa yang dilakukan ketika peladen Google tidak bisa dihubungi sama
    | sekali — jaringan putus, DNS gagal, atau batas waktu terlampaui.
    |
    | true  = kiriman diloloskan (mengutamakan pengunjung yang sah)
    | false = kiriman ditolak    (mengutamakan penahanan bot)
    |
    | Bawaannya true. Formulir di situs ini adalah jalan masuk calon
    | pelanggan, dan gangguan di pihak Google tidak boleh menutup jalan itu —
    | terlebih honeypot dan throttle masih berjaga di belakangnya.
    */
    'allow_on_failure' => (bool) env('RECAPTCHA_ALLOW_ON_FAILURE', true),

];
