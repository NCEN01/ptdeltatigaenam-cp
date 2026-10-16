<?php

/*
|--------------------------------------------------------------------------
| Pesan galat validasi
|--------------------------------------------------------------------------
|
| Berkas ini sebelumnya tidak ada, dan ketiadaannya terlihat di setiap
| formulir situs. app.fallback_locale bernilai 'id', jadi ketika Laravel
| tidak menemukan terjemahan untuk locale 'id' ia jatuh kembali ke 'id'
| juga — ke dirinya sendiri — lalu mengembalikan kunci mentahnya. Pengunjung
| berbahasa Indonesia melihat "validation.required" dan "validation.email"
| alih-alih kalimat, di formulir kontak, pendaftaran, masuk, atur ulang kata
| sandi, checkout, dan pendaftaran kemitraan.
|
| Nada kalimatnya mengikuti nada situs: menyebut apa yang perlu dilakukan,
| bukan menyalahkan. "Alamat surel belum benar" lebih berguna daripada
| "Kolom email tidak valid", karena yang pertama menyebut apa yang harus
| diperbaiki sementara yang kedua hanya mengumumkan ada yang salah.
|
| :attribute diganti nama kolomnya. Daftar 'attributes' di bawah menamainya
| dalam bahasa Indonesia; tanpa itu pesannya berbunyi "Kolom customer_name
| wajib diisi".
|
*/

return [

    'accepted' => 'Kolom :attribute harus disetujui.',
    'accepted_if' => 'Kolom :attribute harus disetujui bila :other bernilai :value.',
    'active_url' => 'Kolom :attribute bukan alamat yang sah.',
    'after' => 'Kolom :attribute harus berisi tanggal setelah :date.',
    'after_or_equal' => 'Kolom :attribute harus berisi tanggal setelah atau sama dengan :date.',
    'alpha' => 'Kolom :attribute hanya boleh berisi huruf.',
    'alpha_dash' => 'Kolom :attribute hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
    'alpha_num' => 'Kolom :attribute hanya boleh berisi huruf dan angka.',
    'array' => 'Kolom :attribute harus berupa larik.',
    'before' => 'Kolom :attribute harus berisi tanggal sebelum :date.',
    'before_or_equal' => 'Kolom :attribute harus berisi tanggal sebelum atau sama dengan :date.',

    'between' => [
        'array' => 'Kolom :attribute harus berisi antara :min sampai :max butir.',
        'file' => 'Berkas :attribute harus berukuran antara :min sampai :max kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai antara :min sampai :max.',
        'string' => 'Kolom :attribute harus terdiri dari :min sampai :max karakter.',
    ],

    'boolean' => 'Kolom :attribute hanya boleh bernilai benar atau salah.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'current_password' => 'Kata sandi yang dimasukkan salah.',
    'date' => 'Kolom :attribute bukan tanggal yang sah.',
    'date_equals' => 'Kolom :attribute harus berisi tanggal yang sama dengan :date.',
    'date_format' => 'Format kolom :attribute tidak sesuai dengan :format.',
    'decimal' => 'Kolom :attribute harus memiliki :decimal angka di belakang koma.',
    'declined' => 'Kolom :attribute harus ditolak.',
    'different' => 'Kolom :attribute dan :other harus berbeda.',
    'digits' => 'Kolom :attribute harus terdiri dari :digits angka.',
    'digits_between' => 'Kolom :attribute harus terdiri dari :min sampai :max angka.',
    'dimensions' => 'Ukuran gambar :attribute tidak sesuai.',
    'distinct' => 'Kolom :attribute berisi nilai yang terduplikasi.',
    'doesnt_end_with' => 'Kolom :attribute tidak boleh diakhiri salah satu dari: :values.',
    'doesnt_start_with' => 'Kolom :attribute tidak boleh diawali salah satu dari: :values.',
    'email' => 'Kolom :attribute harus berupa alamat surel yang sah.',
    'ends_with' => 'Kolom :attribute harus diakhiri salah satu dari: :values.',
    'enum' => 'Pilihan :attribute tidak sah.',
    'exists' => 'Pilihan :attribute tidak sah.',
    'file' => 'Kolom :attribute harus berupa berkas.',
    'filled' => 'Kolom :attribute wajib diisi.',

    'gt' => [
        'array' => 'Kolom :attribute harus berisi lebih dari :value butir.',
        'file' => 'Berkas :attribute harus lebih besar dari :value kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai lebih dari :value.',
        'string' => 'Kolom :attribute harus lebih dari :value karakter.',
    ],

    'gte' => [
        'array' => 'Kolom :attribute harus berisi :value butir atau lebih.',
        'file' => 'Berkas :attribute harus berukuran :value kilobita atau lebih.',
        'numeric' => 'Kolom :attribute harus bernilai :value atau lebih.',
        'string' => 'Kolom :attribute harus terdiri dari :value karakter atau lebih.',
    ],

    'image' => 'Kolom :attribute harus berupa gambar.',
    'in' => 'Pilihan :attribute tidak sah.',
    'in_array' => 'Kolom :attribute tidak ada di dalam :other.',
    'integer' => 'Kolom :attribute harus berupa bilangan bulat.',
    'ip' => 'Kolom :attribute harus berupa alamat IP yang sah.',
    'ipv4' => 'Kolom :attribute harus berupa alamat IPv4 yang sah.',
    'ipv6' => 'Kolom :attribute harus berupa alamat IPv6 yang sah.',
    'json' => 'Kolom :attribute harus berupa teks JSON yang sah.',
    'lowercase' => 'Kolom :attribute harus ditulis dengan huruf kecil.',

    'lt' => [
        'array' => 'Kolom :attribute harus berisi kurang dari :value butir.',
        'file' => 'Berkas :attribute harus lebih kecil dari :value kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai kurang dari :value.',
        'string' => 'Kolom :attribute harus kurang dari :value karakter.',
    ],

    'lte' => [
        'array' => 'Kolom :attribute tidak boleh berisi lebih dari :value butir.',
        'file' => 'Berkas :attribute tidak boleh lebih besar dari :value kilobita.',
        'numeric' => 'Kolom :attribute tidak boleh bernilai lebih dari :value.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :value karakter.',
    ],

    'max' => [
        'array' => 'Kolom :attribute tidak boleh berisi lebih dari :max butir.',
        'file' => 'Berkas :attribute tidak boleh lebih besar dari :max kilobita.',
        'numeric' => 'Kolom :attribute tidak boleh bernilai lebih dari :max.',
        'string' => 'Kolom :attribute tidak boleh lebih dari :max karakter.',
    ],

    'mimes' => 'Kolom :attribute harus berupa berkas berjenis: :values.',
    'mimetypes' => 'Kolom :attribute harus berupa berkas berjenis: :values.',

    'min' => [
        'array' => 'Kolom :attribute harus berisi sekurang-kurangnya :min butir.',
        'file' => 'Berkas :attribute harus berukuran sekurang-kurangnya :min kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai sekurang-kurangnya :min.',
        'string' => 'Kolom :attribute harus terdiri dari sekurang-kurangnya :min karakter.',
    ],

    'multiple_of' => 'Kolom :attribute harus merupakan kelipatan dari :value.',
    'not_in' => 'Pilihan :attribute tidak sah.',
    'not_regex' => 'Format kolom :attribute tidak sah.',
    'numeric' => 'Kolom :attribute harus berupa angka.',

    'password' => [
        'letters' => 'Kata sandi harus memuat sekurang-kurangnya satu huruf.',
        'mixed' => 'Kata sandi harus memuat huruf besar dan huruf kecil.',
        'numbers' => 'Kata sandi harus memuat sekurang-kurangnya satu angka.',
        'symbols' => 'Kata sandi harus memuat sekurang-kurangnya satu tanda baca.',
        'uncompromised' => 'Kata sandi ini pernah bocor dalam kebocoran data. Mohon pilih kata sandi lain.',
    ],

    'present' => 'Kolom :attribute harus ada.',
    'prohibited' => 'Kolom :attribute tidak boleh diisi.',
    'prohibited_if' => 'Kolom :attribute tidak boleh diisi bila :other bernilai :value.',
    'prohibited_unless' => 'Kolom :attribute tidak boleh diisi kecuali :other bernilai :values.',
    'prohibits' => 'Kolom :attribute membuat :other tidak boleh diisi.',
    'regex' => 'Format kolom :attribute tidak sah.',
    'required' => 'Kolom :attribute wajib diisi.',
    'required_array_keys' => 'Kolom :attribute harus memuat kunci: :values.',
    'required_if' => 'Kolom :attribute wajib diisi bila :other bernilai :value.',
    'required_if_accepted' => 'Kolom :attribute wajib diisi bila :other disetujui.',
    'required_unless' => 'Kolom :attribute wajib diisi kecuali :other bernilai :values.',
    'required_with' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_with_all' => 'Kolom :attribute wajib diisi bila terdapat :values.',
    'required_without' => 'Kolom :attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => 'Kolom :attribute wajib diisi bila tidak terdapat satu pun dari :values.',
    'same' => 'Kolom :attribute dan :other harus sama.',

    'size' => [
        'array' => 'Kolom :attribute harus berisi :size butir.',
        'file' => 'Berkas :attribute harus berukuran :size kilobita.',
        'numeric' => 'Kolom :attribute harus bernilai :size.',
        'string' => 'Kolom :attribute harus terdiri dari :size karakter.',
    ],

    'starts_with' => 'Kolom :attribute harus diawali salah satu dari: :values.',
    'string' => 'Kolom :attribute harus berupa teks.',
    'timezone' => 'Kolom :attribute harus berupa zona waktu yang sah.',
    'unique' => ':attribute sudah terdaftar.',
    'uploaded' => 'Berkas :attribute gagal diunggah.',
    'uppercase' => 'Kolom :attribute harus ditulis dengan huruf besar.',
    'url' => 'Format :attribute tidak sah.',
    'uuid' => 'Kolom :attribute harus berupa UUID yang sah.',

    /*
    |--------------------------------------------------------------------------
    | Pesan khusus per kolom
    |--------------------------------------------------------------------------
    |
    | Dipakai hanya bila kalimat umum di atas terasa kaku untuk kolom tertentu.
    |
    */

    'custom' => [
        'terms' => [
            'accepted' => 'Mohon setujui Syarat & Ketentuan dan Kebijakan Privasi untuk melanjutkan.',
        ],
        'password' => [
            'confirmed' => 'Ulangan kata sandi tidak cocok dengan kata sandi yang Anda ketik.',
        ],
        'email' => [
            'unique' => 'Surel ini sudah terdaftar. Silakan masuk atau gunakan surel lain.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Nama kolom
    |--------------------------------------------------------------------------
    |
    | Tanpa daftar ini pesannya berbunyi "Kolom customer_name wajib diisi".
    | Yang disebut di sini hanya kolom yang benar-benar dilihat pengunjung di
    | formulir publik; kolom panel admin memakai label Filament sendiri.
    |
    */

    'attributes' => [
        'name' => 'nama',
        'email' => 'alamat surel',
        'phone' => 'nomor telepon',
        'company' => 'nama perusahaan',
        'subject' => 'subjek',
        'message' => 'pesan',
        'password' => 'kata sandi',
        'password_confirmation' => 'ulangan kata sandi',
        'terms' => 'persetujuan',
        'preferred_locale' => 'bahasa pilihan',
        'customer_name' => 'nama peserta',
        'customer_email' => 'alamat surel',
        'customer_phone' => 'nomor telepon',
        'customer_company' => 'nama perusahaan',
        'quantity' => 'jumlah peserta',
        'notes' => 'catatan',
        'pic_name' => 'nama penanggung jawab',
        'company_name' => 'nama perusahaan',
        'package_id' => 'paket',
        'service_id' => 'layanan',
        'schedule_id' => 'jadwal',
        'participants' => 'daftar peserta',
        'token' => 'token',
    ],

];
