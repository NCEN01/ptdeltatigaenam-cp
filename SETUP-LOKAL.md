# Menjalankan Website PT Delta Tiga Enam di Komputer Sendiri

Panduan ini untuk menyiapkan website PT Delta Tiga Enam di laptop Anda supaya
bisa dibuka dan dipresentasikan ke klien. Isinya sudah lengkap: seluruh layanan,
portofolio, artikel, agenda, data sertifikat, logo klien, logo mitra, dan semua
gambar yang tampil di situs.

Perkiraan waktu: **15–25 menit**, sebagian besar menunggu unduhan.

---

## 1. Yang perlu dipasang lebih dulu

| Perangkat | Versi minimum | Catatan |
|---|---|---|
| PHP | 8.2 | Sudah termasuk bila memakai XAMPP 8.2+ |
| MySQL atau MariaDB | MySQL 8 / MariaDB 10.6 | Sudah termasuk di XAMPP |
| Composer | 2.x | https://getcomposer.org |
| Node.js + npm | Node 20+ | https://nodejs.org |
| Git | apa saja | |

**Cara tercepat di Windows:** pasang [XAMPP](https://www.apachefriends.org)
(PHP 8.2 ke atas), lalu Composer, Node.js, dan Git secara terpisah.

### Ekstensi PHP yang wajib aktif

`pdo_mysql` · `mbstring` · `gd` · `intl` · `zip` · `bcmath` · `fileinfo` · `openssl` · `exif`

Di XAMPP hampir semuanya sudah aktif **kecuali `intl`**. Buka
`C:\xampp\php\php.ini`, cari baris `;extension=intl`, hapus titik komanya
sehingga menjadi `extension=intl`, lalu simpan dan restart Apache.

Periksa dengan:

```bash
php -m
```

Pastikan `intl` muncul di daftarnya. Tanpa ini proyek akan gagal jalan.

---

## 2. Ambil kode dari GitHub

```bash
git clone <URL-REPO-DARI-SADAM> delta-tiga-enam
cd delta-tiga-enam
```

Bila memakai XAMPP, letakkan foldernya di `C:\xampp\htdocs\`.

---

## 3. Pasang dependensi

```bash
composer install
npm install
```

Keduanya butuh koneksi internet dan memakan waktu paling lama di langkah ini.

---

## 4. Siapkan berkas konfigurasi

```bash
cp .env.example .env
php artisan key:generate
```

Buka `.env` dengan editor teks, lalu **ubah baris-baris berikut**. Enam baris ini
penting; sisanya biarkan apa adanya.

```env
APP_URL=http://localhost:8000
APP_LOCALE=id

DB_DATABASE=deltatigaenam
DB_USERNAME=root
DB_PASSWORD=

ADMIN_EMAIL=admin@deltatigaenam.com
ADMIN_PASSWORD=isi-kata-sandi-bebas-di-sini

CUSTOMER_AUTO_VERIFY=true
MAIL_MAILER=log
```

Penjelasan singkat, supaya tidak menebak-nebak:

- **`APP_LOCALE=id`** — bawaan berkas contoh adalah `en`. Kalau tidak diubah,
  situs terbuka dalam Bahasa Inggris.
- **`APP_URL=http://localhost:8000`** — sesuai port yang dipakai `php artisan serve`.
- **`DB_PASSWORD`** dikosongkan karena MySQL bawaan XAMPP memang tanpa kata sandi.
- **`ADMIN_PASSWORD`** Anda tentukan sendiri; itu yang dipakai masuk panel admin.
  Kalau dibiarkan kosong, akun admin tidak akan dibuat.
- **`CUSTOMER_AUTO_VERIFY=true`** hanya untuk lokal, supaya bisa mendaftar akun
  pelanggan tanpa perlu server email. **Jangan dipakai di server sungguhan.**
- **`MAIL_MAILER=log`** membuat semua email ditulis ke
  `storage/logs/laravel.log`, bukan dikirim betulan.

---

## 5. Buat database dan masukkan isinya

Nyalakan **MySQL** dari XAMPP Control Panel lebih dulu, lalu:

```bash
# Buat database kosong
"C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE deltatigaenam CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Masukkan seluruh isi situs
"C:\xampp\mysql\bin\mysql.exe" -u root deltatigaenam < setup/deltatigaenam.sql
```

Di macOS atau Linux, cukup `mysql` tanpa path lengkapnya.

**Alternatif lewat phpMyAdmin** bila lebih nyaman dengan tampilan:
buka http://localhost/phpmyadmin → **New** → nama database `deltatigaenam`,
collation `utf8mb4_unicode_ci` → **Create** → pilih database itu → tab
**Import** → pilih berkas `setup/deltatigaenam.sql` → **Go**.

### Buat akun admin

Berkas database sengaja dikirim **tanpa akun pengguna**, jadi akun admin Anda
dibuat sendiri dari `.env` yang tadi diisi:

```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminUserSeeder
```

---

## 6. Sambungkan folder gambar dan bangun tampilan

```bash
php artisan storage:link
npm run build
```

`storage:link` membuat pintasan supaya gambar yang tersimpan bisa diakses
browser. `npm run build` menyusun CSS dan JavaScript situs.

> **Windows:** bila `storage:link` gagal dengan pesan soal *symlink*, tutup
> terminal lalu buka lagi **sebagai Administrator** dan ulangi perintahnya.

---

## 7. Jalankan

```bash
php artisan serve
```

Biarkan terminal ini terbuka selama presentasi.

| Halaman | Alamat |
|---|---|
| Situs (Indonesia) | http://localhost:8000/id |
| Situs (Inggris) | http://localhost:8000/en |
| Panel Admin | http://localhost:8000/d36-panel |

Masuk ke panel admin memakai `ADMIN_EMAIL` dan `ADMIN_PASSWORD` yang Anda isi
di `.env` tadi.

---

## 8. Pastikan semuanya benar sebelum presentasi

Buka http://localhost:8000/id dan periksa daftar berikut:

- [ ] Gambar besar di bagian paling atas muncul, tidak kosong
- [ ] Bagian **Portofolio** menampilkan tiga foto kegiatan
- [ ] Bagian **Klien Kami** menampilkan logo berwarna yang berjalan
  (Pertamina, Bank Indonesia, Telkom, dan lainnya)
- [ ] Buka **/id/sertifikat** — tabel berisi 25 nama, dan panel kanan
  menampilkan 22 berlaku / 1 segera berakhir / 2 kedaluwarsa
- [ ] Buka **/id/layanan** — daftar layanan tampil beserta harganya
- [ ] Buka **/id/kemitraan** — empat paket kemitraan tampil
- [ ] Masuk ke **/d36-panel** dengan akun yang tadi dibuat

Kalau semua tercentang, situsnya siap dipresentasikan.

---

## Kalau ada yang bermasalah

| Gejala | Penyebab & solusi |
|---|---|
| Halaman kosong putih / error 500 | Lihat `storage/logs/laravel.log` baris paling bawah. Paling sering: `APP_KEY` belum dibuat → jalankan `php artisan key:generate` |
| `could not find driver` | `pdo_mysql` belum aktif di `php.ini` |
| `Class "IntlDateFormatter" not found` | Ekstensi `intl` belum aktif — lihat langkah 1 |
| Semua gambar rusak | `php artisan storage:link` belum dijalankan, atau gagal karena izin symlink |
| Tampilan berantakan tanpa warna | `npm run build` belum dijalankan |
| Situs terbuka dalam Bahasa Inggris | `APP_LOCALE` di `.env` masih `en`, ubah ke `id` |
| Habis mengubah `.env` tapi tidak berubah | `php artisan config:clear` lalu `php artisan cache:clear` |
| `SQLSTATE[HY000] [1049] Unknown database` | Database `deltatigaenam` belum dibuat — ulangi langkah 5 |
| Port 8000 sudah dipakai | `php artisan serve --port=8080`, lalu sesuaikan `APP_URL` |

### Satu peringatan penting

**Jangan pernah menjalankan `php artisan test`, `php artisan migrate:fresh`,
atau `php artisan db:wipe` di komputer ini.** Sebagian test memakai
`RefreshDatabase`, yang menghapus seluruh tabel lebih dulu. Ini pernah terjadi
dan seluruh isi situs hilang. Kalau memang perlu menjalankan test, pastikan
`phpunit.xml` tetap memakai SQLite di memori seperti sekarang.

---

## Yang tidak ikut dikirim, dan alasannya

Berkas database ini berisi **struktur seluruh tabel**, tetapi **data hanya untuk
tabel konten** — layanan, portofolio, blog, agenda, sertifikat, mitra, klien,
testimoni, paket kemitraan, pengaturan, dan banner.

Tabel berikut sengaja dikirim kosong karena berisi data pribadi orang atau
catatan transaksi, dan tidak diperlukan untuk presentasi:

`users` · `customers` · `contact_messages` · `partnership_registrations` ·
`orders` · `order_participants` · `invoices` · `invoice_items` · `transactions` ·
`sessions` · `notifications`

Akibatnya di komputer Anda: riwayat pesanan kosong, kotak masuk pesan kontak
kosong, dan tidak ada akun pelanggan. Semuanya bisa dicoba sendiri dengan
mendaftar akun baru lewat http://localhost:8000/id/daftar.

Pembayaran Midtrans juga tidak aktif karena `MIDTRANS_SERVER_KEY` kosong. Kalau
perlu mendemokan alur pembayaran, minta kunci sandbox ke Sadam.
