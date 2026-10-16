# Catatan Serah Terima — Sisa Pekerjaan Sebelum Produksi

Ditulis 17 Oktober 2026. Cabang: `dev`.
Repo: https://github.com/NCEN01/ptdeltatigaenam-cp

Dokumen ini daftar **yang belum selesai**, bukan laporan yang sudah selesai.
Tiap butir menyebut berkasnya, cara memastikan masalahnya masih ada, dan
alasan ia belum dikerjakan — supaya tidak perlu menelusuri ulang dari nol.

Butir bertanda **[KEPUTUSAN]** bukan pekerjaan teknis melainkan pilihan yang
harus diambil pemilik proyek lebih dulu.

---

## 1. Blokir rilis — wajib sebelum produksi

### 1.1 `.env` masih setelan pengembangan

```
APP_ENV=local            -> production
APP_DEBUG=true           -> false      ← paling berbahaya
APP_URL=http://localhost:8000          -> domain sungguhan (https)
MAIL_MAILER=log          -> smtp
CUSTOMER_AUTO_VERIFY=true -> false     ← verifikasi surel dilewati
MIDTRANS_IS_PRODUCTION=false -> true   (beserta kunci produksinya)
```

`APP_DEBUG=true` menampilkan jejak galat lengkap beserta isi `.env` kepada
siapa pun yang memicu galat. Ini bukan "sebaiknya dimatikan", ini kebocoran
kredensial.

`CUSTOMER_AUTO_VERIFY=true` membuat akun langsung terverifikasi tanpa pernah
membuka surel. Di produksi harus `false` — tetapi **jangan dimatikan sebelum
SMTP benar-benar jalan**, karena pendaftar akan terjebak di halaman "cek
surel Anda" yang suratnya tidak pernah datang.

### 1.2 Kata sandi admin masih `password`

Akun `admin@deltatigaenam.com` memakai kata sandi literal `password`.
Panelnya di `/d36-panel`. Ganti sebelum domain diumumkan.

### 1.3 Pengiriman surel belum pernah diuji

`MAIL_MAILER=log` — semua surat ditulis ke `storage/logs/laravel.log`, tidak
ada yang benar-benar terkirim. Yang terdampak:

- verifikasi surel pendaftar
- tautan atur ulang kata sandi
- `OrderPaidNotification` setelah pembayaran lunas
- pesan formulir kontak (masuk ke basis data, tetapi tidak dikirim ke
  siapa pun lewat surel)

Setelah SMTP disetel, uji keempatnya satu per satu.

---

## 2. Tugas yang memang ditujukan ke Haidar

### 2.1 Masuk dengan Google — **belum dikerjakan** **[KEPUTUSAN]**

Belum ada satu baris pun. Dua jalan, pilih salah satu:

**a. `laravel/socialite`** — sudah diuji `composer require --dry-run` dan
berhasil, tetapi membawa **5 paket baru**: `laravel/socialite`,
`league/oauth1-client`, `firebase/php-jwt`, `phpseclib/phpseclib`,
`paragonie/constant_time_encoding`.

**b. Ditulis tangan**, sekitar 80 baris dengan `Http` client Laravel: alur
authorization-code biasa. Wajib: parameter `state` disimpan di sesi dan
diperiksa saat callback (tanpa itu ada CSRF), dan `email_verified` dari
Google harus dicek sebelum akun ditautkan.

Belum dipilih karena menambah 5 dependensi tepat setelah membersihkan 28
kerentanan terasa langkah yang harus disetujui pemilik proyek, bukan
diputuskan sendiri.

Apa pun pilihannya, butuh **OAuth Client ID + Secret** dari Google Cloud
Console. Ikuti pola `config/captcha.php`: fitur mati total selama kunci
belum ada, menyala sendiri begitu diisi.

Satu hal yang perlu diputuskan juga: pelanggan yang mendaftar lewat Google
tidak punya kata sandi. Tabel `customers.password` saat ini `NOT NULL`.

### 2.2 Kunci reCAPTCHA — integrasinya **sudah selesai**, tinggal kuncinya

Jangan ditulis ulang. Yang ada dan sudah teruji:

- `config/captcha.php`
- `app/Rules/Recaptcha.php`
- `resources/views/components/recaptcha.blade.php`
- terpasang di formulir Kontak, Daftar, dan Kemitraan

Seluruhnya **tidak aktif** sampai dua baris ini diisi di `.env`:

```
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=
```

Ambil di https://www.google.com/recaptcha/admin/create → Challenge (v2) →
"I'm not a robot" Checkbox. Daftarkan domain produksi **dan** `localhost`.

Sudah diuji dua arah dengan kunci percobaan: widget muncul, skrip termuat,
kiriman tanpa centang ditolak dengan pesan berbahasa Indonesia. Kunci
percobaannya sudah dihapus dari `.env`.

### 2.3 Alur pembayaran belum pernah dijalankan sungguhan

`orders`, `transactions`, dan `invoices` **semuanya 0 baris**. Idempotensi
webhook sudah diperbaiki dan diuji di tingkat basis data, tetapi alur
lengkap dari checkout sampai lunas belum pernah dijalankan satu kali pun.

Yang perlu diuji di Midtrans sandbox:

1. checkout → Snap terbuka → bayar → status pesanan jadi `paid`
2. `seats_taken` bertambah **tepat sekali**
3. webhook dikirim dua kali (tombol "resend" di dasbor Midtrans) →
   `seats_taken` **tidak** bertambah lagi, surel **tidak** terkirim dua kali
4. pembayaran kedaluwarsa → status jadi `expired`
5. pembayaran gagal → status jadi `failed`

Butir 3 adalah yang paling penting dan yang paling mudah terlewat.

### 2.4 Validasi masukan — **sudah selesai**, jangan ditulis ulang

`app/Rules/PersonName.php` (nama tolak angka) dan
`app/Rules/PhoneNumber.php` (telepon tolak huruf) sudah dipasang di seluruh
pengontrol. 20 kasus uji, semuanya benar.

Catatan penting kalau menambah kolom baru: `PersonName` **tidak boleh**
dipasang di kolom nama perusahaan. "PT 3M Indonesia" dan "Bank BTPN Syariah"
sah mengandung angka.

Kolom telepon berkode negara (`<x-phone-field>`, bawaan +62) baru dipasang di
halaman **Daftar**. Kalau diinginkan di Kontak, Kemitraan, dan Checkout,
komponennya tinggal dipakai — kontraknya sudah satu kolom `phone` seperti
biasa.

---

## 3. Keamanan — sisa yang perlu keputusan

`composer audit` turun dari 35 peringatan jadi **7**. Yang tersisa tidak bisa
diselesaikan dengan `composer update` biasa.

### 3.1 Laravel 11 → 12 **[KEPUTUSAN]**

4 peringatan, satu **HIGH**: CRLF injection pada aturan validasi `email`
(GHSA-5vg9-5847-vvmq). Perbaikan resminya **hanya ada di Laravel 12.60** —
seluruh jalur Laravel 11 terdampak tanpa rilis tambalan.

Penangkal sementara sudah dipasang: `app/Rules/SafeEmail.php` menolak seluruh
karakter kendali C0 di tujuh kolom surel.

**Harus jujur soal ini:** kerentanannya **tidak berhasil direproduksi** di
11.57. Enam varian CRLF dicoba — di ujung, di awal, di tengah, beserta bentuk
terkutipnya — dan aturan bawaan menolak keenamnya. Jadi `SafeEmail` adalah
lapisan pertahanan, bukan tambalan lubang yang terbukti menganga. Jangan
jadikan temuan ini alasan tunggal untuk upgrade mayor; timbang bersama
manfaat lain Laravel 12.

Kalau upgrade diambil: Filament 3 dan Livewire 3 ikut harus diperiksa
kecocokannya, dan itu pekerjaan tersendiri — bukan `composer update`.

### 3.2 `enshrined/svg-sanitize` **[KEPUTUSAN]**

3 peringatan medium (stored XSS lewat DTD entity, bypass `xlink:HrEf`
campuran huruf besar-kecil, DoS). Terdampak `<=0.22.0`, sementara
`composer.json` mengikat `^0.22.0` sehingga perbaikannya di luar jangkauan.

Periksa dulu seberapa terpapar: paket ini dipakai membersihkan SVG yang
**diunggah admin**, bukan pengunjung. Kalau tidak ada admin yang mengunggah
SVG, risikonya kecil — tetapi melonggarkan batasan ke `^0.23` tetap langkah
yang murah.

### 3.3 Yang sudah selesai (jangan diulang)

- 8 paket diperbarui, 28 peringatan hilang, termasuk **11 HIGH**
  (XSS commonmark, bypass pemeriksaan host guzzle)
- idempotensi webhook Midtrans: indeks unik `midtrans_order_id` +
  `lockForUpdate` + surel dipindah ke `DB::afterCommit`
- honeypot dan `throttle:5,1` sudah ada sejak awal di seluruh formulir publik
- header keamanan (`AddSecurityHeaders`) dan HSTS sudah terpasang

---

## 4. Performa — satu pekerjaan besar tersisa

### 4.1 Bundel JavaScript 318 kB (116 kB ter-gzip)

GSAP, Swiper, Alpine, dan AOS semuanya dimuat di **setiap** halaman. Swiper
(~45 kB) hanya dipakai carousel hero beranda dan beberapa rel.

Memecahnya dengan `import()` dinamis bisa memangkas sekitar 45 kB untuk
halaman yang tidak punya carousel. **Belum dikerjakan karena berisiko
memecah carousel**, dan tidak ada uji otomatis yang akan menangkapnya.
Kerjakan setelah ada waktu menguji manual tiap halaman bercarousel:
beranda (hero, kategori, pelatihan terbaru, agenda, berita), Layanan (rel
agenda), Portofolio (rel penutup), Blog (terus membaca).

### 4.2 Yang sudah selesai (jangan diulang)

- CSS turun 228 → 172 kB: `storage/framework/views` dilepas dari
  `tailwind.config.js`, yang membuat kelas panel admin Filament ikut
  terbangun ke bundel situs publik
- font pindah dari `@import` ke `<link rel="preconnect">` + stylesheet
- `Setting::get()` tidak lagi menembak basis data untuk kunci yang kosong
  (`Cache::rememberForever` tidak menyimpan `null`)
- dua query mati di `HomeController` dilepas
- jadwal yang sudah lewat tidak lagi dimuat di halaman Layanan
- **nol N+1**; 3–16 query per halaman, 22–91 ms

---

## 5. Belum diverifikasi — bukan "belum ada", tapi "belum dibuktikan"

### 5.1 Tampilan responsif belum pernah dilihat di peramban sungguhan

Audit yang dilakukan **statis** — membaca kelas Tailwind di berkas Blade:

- tidak ada pola luapan mendatar
- tidak ada kisi yang melompat 1 → 4 kolom tanpa langkah tablet
- gambar: semua terkunci rasio oleh induk atau `@class`

Yang **belum** dilakukan: membuka tiap halaman di lebar 360 / 768 / 1280 /
1920 dan melihatnya. Tidak ada peramban headless terpasang, dan menambah
Puppeteer/Playwright ke proyek orang lain bukan keputusan yang diambil
sendiri.

Kalau mau diotomatiskan: `npm i -D playwright`, lalu skrip yang membuka 12
halaman × 2 bahasa × 4 lebar dan memeriksa
`document.documentElement.scrollWidth > window.innerWidth`.

Prioritaskan halaman terpanjang dan terpadat: **Beranda**, **Layanan**,
**Kemitraan**, **Checkout**.

### 5.2 Panel admin belum diuji dengan mengklik

Audit peran dilakukan dengan memanggil `canViewAny()` dan
`shouldRegisterNavigation()` lewat pengguna sementara di dalam transaksi —
31 menu × 3 peran, hasilnya bersih. Tetapi **tidak ada satu formulir pun yang
benar-benar disimpan lewat antarmuka**.

Yang paling perlu diklik sungguhan, karena menyimpan terjemahan dwibahasa
lewat jalur yang berbeda antara Create dan Edit:

- Banner, FAQ, Mentor, Manfaat Kemitraan, Alamat Kantor, Benefit Bawaan

Periksa khususnya: isi kolom ID **dan** EN, simpan, buka lagi — pastikan
kolom EN tidak kosong dan tidak tersarang ganda (`{"id":{"id":"…"}}`).

---

## 6. Sisa kecil — rapi-rapi, bukan bug

| Hal | Berkas | Catatan |
|---|---|---|
| Komponen mati | `components/skyline.blade.php` (3 kB), `components/cta-band.blade.php` (1,6 kB) | Hanya muncul di dalam komentar. Aman dihapus. |
| Halaman mati | `resources/views/welcome.blade.php` | Bawaan Laravel, tidak ada rute yang menunjuknya. |
| Kelas tidak pernah terbentuk | `components/layout.blade.php:133` | `text-current/60` — Tailwind tidak bisa memberi opasitas pada `currentColor`, jadi tombol tutup pesan sekilas tampil penuh dan `hover:text-current` tidak berefek. |
| Izin yatim | `database/seeders/RoleSeeder.php` | `manage_missions` diberikan ke peran tetapi tidak menjaga apa pun. |
| Saklar tanpa akibat | 5 model | `is_featured` bisa dinyalakan admin di Service, ServiceCategory, Testimonial — tetapi hanya berpengaruh di satu tempat (`pages/services/index.blade.php:468`, lencana "Unggulan"). Admin menyalakannya dan tidak terjadi apa-apa. Pilih: sambungkan, atau lepas togglenya. |
| 39 berkas terhapus belum di-stage | `storage/app/public/banners/*.webp` | Sengaja tidak disentuh — penghapusan aset bukan keputusan yang diambil sendiri. Pastikan tidak ada banner aktif yang menunjuk berkas itu sebelum di-commit. |
| `.btn-outline-gold` | `resources/css/app.css` | Didefinisikan, tidak dipakai di mana pun. |

---

## 7. Hal yang mudah terlewat — baca sebelum menyentuh kode

Empat jebakan yang sudah memakan waktu di sesi ini. Semuanya **gagal diam**:
tidak ada pesan galat, hanya hasil yang salah.

**1. Skala opasitas Tailwind hanya kelipatan 5.**
`bg-navy-950/82` tidak menghasilkan kelas apa pun — bukan galat, hanya
hilang. Pernah membuat 10 kelas tabir tidak pernah terbentuk. Setelah
`npm run build`, pastikan kelasnya benar-benar ada di CSS hasil bangunan,
jangan hanya menghitung angkanya di kepala.

**2. `overflow` pada leluhur mematikan `position: sticky`.**
Satu baris `body { overflow-x: hidden }` mematikan **delapan** elemen sticky
di seluruh situs selama berbulan-bulan: panel pembelian Layanan, ringkasan
Checkout, daftar isi halaman hukum, navigasi Akun, sisi kanan Blog dan
Portofolio, kolom FAQ beranda, rel Kemitraan. Sudah diperbaiki — jangan
pasang kembali `overflow-hidden` di jalur leluhur elemen sticky.

**3. `peer-checked:` hanya berlaku untuk saudara sekandung.**
Ditulis di elemen cucu, ia tidak pernah menyala. Pakai
`peer-checked:[&>svg]:…` di induknya.

**4. `Cache::rememberForever` tidak menyimpan `null`.**
Kunci pengaturan yang kosong akan di-query ulang setiap permintaan,
selamanya. Sudah diperbaiki di `Setting::get()` dengan satu entri untuk
seluruh tabel — jangan dikembalikan ke cache per kunci.

---

## 8. Perintah yang TIDAK boleh dijalankan

```
php artisan migrate:fresh      ← pernah menghapus basis data kerja
php artisan migrate:refresh
php artisan db:wipe
php artisan test               ← RefreshDatabase menghapus basis data kerja
```

Basis data pengembangan di mesin ini berisi data sungguhan yang belum pernah
dicadangkan: 25 pemegang sertifikat, 23 portofolio, 20 artikel, 20 testimoni.

Untuk menguji, pakai transaksi yang dibatalkan:

```php
DB::beginTransaction();
// … coba apa pun di sini …
DB::rollBack();
```

Itu pola yang dipakai seluruh pengujian di sesi ini.

---

## 9. Urutan yang disarankan

1. `.env` produksi + ganti kata sandi admin + SMTP (§1) — blokir rilis
2. Uji keempat jalur surel setelah SMTP jalan (§1.3)
3. Uji alur pembayaran di sandbox, terutama webhook ganda (§2.3)
4. Isi kunci reCAPTCHA (§2.2) — 5 menit, integrasinya sudah siap
5. Klik seluruh formulir panel admin (§5.2)
6. Verifikasi responsif di peramban sungguhan (§5.1)
7. **[KEPUTUSAN]** Login Google (§2.1)
8. **[KEPUTUSAN]** Laravel 12 dan svg-sanitize (§3.1, §3.2)
9. Pemecahan bundel JS (§4.1)
10. Rapi-rapi (§6)
