# Urutan Prompt Bertahap untuk Claude Code
## Membangun Website PT Delta Tiga Enam (Laravel 11 + Filament 3 + Midtrans)

Dokumen ini berisi prompt yang dijalankan **berurutan** di Claude Code. Setiap tahap punya satu blok prompt yang tinggal disalin. Jangan loncat tahap—tiap tahap bergantung pada hasil tahap sebelumnya.

### Cara pakai
1. Buat folder kerja kosong, taruh `PRD.md` dan `database.sql` di dalamnya, buka folder itu dengan Claude Code.
2. Jalankan **Prompt 0 (Aturan Global)** lebih dulu agar Claude Code memahami konteks & batasan.
3. Lanjut Prompt 1, 2, 3, dst. Selesaikan & verifikasi satu tahap sebelum lanjut.
4. Di akhir tiap tahap, minta Claude Code commit dan laporkan ringkasan + apa yang belum.

---

## Prompt 0 — Aturan Global (jalankan sekali di awal)

```text
Kamu akan membangun website company profile PT Delta Tiga Enam secara bertahap.
Sebelum mulai, baca dan pahami dua berkas di root proyek: PRD.md dan database.sql.

Aturan yang berlaku untuk SELURUH sesi:
1. database.sql adalah SUMBER KEBENARAN skema. Semua migration Laravel harus identik
   dengannya (nama tabel, kolom, tipe data, enum, foreign key, index). Jangan menyimpang.
2. Ikuti PRD.md, khususnya Bagian 8 (Implementation Guide), untuk dependency, struktur
   folder, peta route, peta model, matriks role, dan Definition of Done.
3. Kerjakan PER TAHAP sesuai prompt yang kuberikan. Jangan mengerjakan tahap berikutnya
   sebelum kuminta. Di setiap tahap: buat rencana singkat dulu, lalu eksekusi.
4. Setelah tiap tahap selesai: jalankan verifikasi yang relevan, buat commit git dengan
   pesan yang jelas, lalu laporkan ringkasan perubahan + item yang belum tuntas.
5. Keamanan & kualitas: jangan menaruh secret di kode (pakai .env), validasi semua input,
   tulis kode bersih dan modular, dan jangan menghapus data/berkas tanpa konfirmasi.
6. Jika ada keputusan ambigu, tanyakan dulu sebelum menebak.

Konfirmasi bahwa kamu sudah membaca PRD.md dan database.sql dengan meringkas dalam 8-10
poin: tujuan produk, peran (admin transaksi, admin konten, pelanggan), dua jalur penagihan
(Midtrans vs invoice), kebutuhan multi-bahasa, dan daftar modul utama. Jangan menulis kode
dulu di tahap ini.
```

---

## Prompt 1 — Setup proyek & dependency

```text
TAHAP 1: Scaffolding & dependency.

1. Inisialisasi proyek Laravel 11 di folder ini (jika belum ada).
2. Install dependency sesuai PRD Bagian 8.2 & 8.3: filament/filament,
   spatie/laravel-permission, spatie/laravel-translatable,
   filament/spatie-laravel-translatable-plugin, laravel/sanctum, midtrans/midtrans-php,
   intervention/image, spatie/laravel-image-optimizer, barryvdh/laravel-dompdf,
   mcamara/laravel-localization. Untuk npm: tailwindcss, @tailwindcss/typography,
   alpinejs, swiper, aos, vite-plugin-pwa.
3. Jalankan filament:install --panels dan publish config spatie/permission.
4. Siapkan .env sesuai PRD Bagian 8.5 (placeholder untuk DB & Midtrans; jangan isi secret
   asli). Buat database lokal dan pastikan koneksi berhasil.
5. Inisialisasi git, buat .gitignore yang benar, commit awal.

Kriteria selesai: `php artisan about` berjalan tanpa error, Filament terpasang,
semua dependency terinstall, dan commit awal dibuat. Laporkan versi yang terpasang.
```

---

## Prompt 2 — Skema database, model, relasi & seeder

```text
TAHAP 2: Database & domain model.

1. Buat migration untuk SEMUA tabel di database.sql, identik dengan definisinya
   (termasuk enum, JSON i18n, foreign key on delete, dan index). Urutkan agar FK valid.
2. Buat Eloquent model untuk setiap tabel domain sesuai peta di PRD Bagian 8.7.
   Tetapkan relasi (hasMany/belongsTo/belongsToMany) dan casting yang tepat.
3. Untuk model dengan kolom JSON i18n, gunakan trait HasTranslations (spatie) dan
   properti $translatable sesuai kolom yang ditandai i18n di database.sql.
4. Buat seeder sesuai PRD Bagian 8.14 dengan nilai awal yang sudah ada di blok seed
   database.sql (3 role, 5 kategori ID/EN, 4 paket kemitraan, 3 kantor, 3 misi,
   setting locale & kemitraan). AdminUserSeeder membuat 1 super_admin; ambil kredensial
   dari .env, jangan hardcode.
5. Jalankan `php artisan migrate:fresh --seed`.

Kriteria selesai: migrate:fresh --seed sukses tanpa error; struktur tabel hasil migrasi
cocok dengan database.sql; data seed masuk. Tampilkan ringkasan tabel & jumlah baris seed.
Commit.
```

---

## Prompt 3 — Autentikasi, role, dan panel admin

```text
TAHAP 3: Auth ganda + RBAC + akses panel.

1. Konfigurasi DUA guard di config/auth.php: `web` (model User, untuk admin panel) dan
   `customer` (model Customer, untuk website). Tambah provider customers.
2. Pastikan model User memakai Spatie HasRoles. Definisikan permission dan kaitkan ke
   role super_admin, admin_transaksi, admin_konten (lihat matriks PRD Bagian 8.9).
3. Konfigurasi panel Filament di /admin agar HANYA bisa diakses model User yang aktif
   (canAccessPanel). Implement login admin.
4. Buat mekanisme visibilitas resource/menu berbasis role (shouldRegisterNavigation /
   policy), siap dipakai tahap berikutnya — untuk sekarang cukup kerangkanya.

Kriteria selesai: super_admin bisa login ke /admin; guard customer terdaftar (belum ada
halaman publiknya, cukup konfigurasi). Uji bahwa user nonaktif/role salah tidak bisa masuk.
Commit.
```

---

## Prompt 4 — Media service (validasi, resize, WebP, keamanan)

```text
TAHAP 4: Pipeline media bersama (dipakai semua upload).

Implement MediaService sesuai PRD Bagian 3.10 & 8.12:
1. Validasi server-side: whitelist MIME & ekstensi, batas dimensi & ukuran file sesuai
   tabel standar (hero, banner, thumbnail, blog, logo, avatar, dll.).
2. Proses otomatis: resize ke dimensi target, kompres, konversi ke WebP (Intervention +
   spatie image-optimizer), buat varian responsif untuk srcset.
3. Keamanan: nama file di-hash/random, hapus metadata EXIF, sanitasi SVG, simpan di disk
   non-eksekusi (storage public via symlink).
4. Sediakan helper/komponen agar Filament FileUpload memakai pipeline ini.

Kriteria selesai: ada unit/manual test yang menunjukkan file > batas ditolak, dan file
valid otomatis menjadi WebP + varian responsif. Commit.
```

---

## Prompt 5 — Admin Konten: resource Filament untuk seluruh konten

```text
TAHAP 5: Filament Resources untuk Admin Konten (role admin_konten + super_admin).

Buat resource CRUD (dengan field dwibahasa ID/EN memakai plugin Translatable, dan upload
via MediaService) untuk: ServiceCategory, Service (beserta relasi ServiceActivity &
ServiceSchedule sebagai relation manager/repeater), Banner (placement + varian mobile),
BlogCategory, BlogPost, BlogTag, Portfolio (+ PortfolioImage), Client, Testimonial,
Partner, PartnershipPackage, PartnershipBenefit, OfficeLocation, CompanyMission,
Setting (halaman pengaturan), dan ContactMessage (read + tandai dibaca).

Terapkan visibilitas sesuai matriks PRD Bagian 8.9: resource ini TIDAK tampil untuk
admin_transaksi. Sertakan slug otomatis, sort_order, toggle is_active, dan validasi.

Kriteria selesai: admin_konten bisa menambah/ubah/hapus seluruh konten di atas dengan
input ID & EN; admin_transaksi tidak melihat menu konten. Commit.
```

---

## Prompt 6 — Admin Transaksi: order, dashboard, kemitraan & invoice

```text
TAHAP 6: Filament untuk Admin Transaksi (role admin_transaksi + super_admin).

1. Resource Order & Transaction (READ-only untuk transaksi Midtrans) dengan filter status
   & tanggal, halaman detail, dan ekspor.
2. Dashboard transaksi + widget: total pendapatan, grafik tren, transaksi terbaru.
3. Resource PartnershipRegistration (lead form kemitraan) dengan alur status
   (baru → dihubungi → meeting_dijadwalkan → penawaran_dikirim → invoice_diterbitkan →
   lunas → selesai/dibatalkan) dan field assigned_to.
4. Resource Invoice + InvoiceItem (relation manager): hitung subtotal/pajak/total otomatis,
   status draft→terkirim→lunas, dan aksi "Generate PDF" via dompdf disimpan ke storage
   (file_path). Sediakan InvoiceService.

Terapkan visibilitas: modul ini TIDAK tampil untuk admin_konten.

Kriteria selesai: admin_transaksi bisa melihat order/transaksi, mengelola lead kemitraan,
membuat invoice + unduh PDF; menu konten tidak terlihat. Commit.
```

---

## Prompt 7 — Fondasi frontend: layout, i18n, Tailwind, PWA

```text
TAHAP 7: Fondasi website publik.

1. Setup Tailwind dengan token warna brand (navy, biru muda, putih, aksen gold) dan
   tipografi; konfigurasi Vite + Alpine + Swiper + AOS/GSAP.
2. i18n: middleware SetLocale dari prefix URL /{locale}, integrasi mcamara/laravel-localization,
   route lokal, tag hreflang, dan language switcher ID/EN di header. String UI statis di
   resources/lang/id & en.
3. Layout publik: header (nav + switcher bahasa + tombol login), footer (kontak ringkas,
   sosial, kantor). Mobile-first & responsif.
4. PWA: vite-plugin-pwa (manifest, ikon, service worker, offline shell dasar).

Kriteria selesai: halaman kerangka tampil di /id dan /en, switcher mengubah bahasa UI,
hreflang muncul, layout responsif, dan PWA installable. Commit.
```

---

## Prompt 8 — Halaman publik: landing, about, layanan, portofolio, blog, kontak

```text
TAHAP 8: Halaman konten publik (mengambil data dari DB, dwibahasa).

1. Landing page: hero section dengan background beranimasi + Swiper merotasi kategori
   layanan unggulan (is_featured); ringkasan layanan, sorotan portofolio, testimoni, mitra,
   dan blog terbaru. Scroll reveal via AOS/GSAP.
2. About Us: profil, visi, misi (dinamis dari DB).
3. Layanan: daftar kategori + detail layanan (kegiatan, jadwal, harga) dengan tombol
   Beli/Daftar (mengarah ke checkout pada tahap berikutnya).
4. Portofolio: kegiatan + galeri, daftar klien, testimoni. Blog: daftar + detail artikel.
5. Kontak: tampilkan 3 kantor + peta, dan form kontak yang menyimpan ke contact_messages.

Kriteria selesai: semua halaman tampil rapi & responsif di ID/EN dengan data nyata dari DB;
form kontak tersimpan dan terlihat di admin. Commit.
```

---

## Prompt 9 — Auth pelanggan & area akun

```text
TAHAP 9: Registrasi/login pelanggan (guard customer) + area akun.

1. Implement register, verifikasi email, login, logout, dan reset password untuk guard
   customer (boleh pakai pola Fortify/Breeze yang disesuaikan ke guard customer).
2. Halaman profil pelanggan (edit data + bahasa pilihan/preferred_locale).
3. Area akun "Riwayat Pesanan" yang menampilkan order milik pelanggan beserta status.
4. Lindungi route akun & checkout dengan middleware auth:customer.

Kriteria selesai: pelanggan bisa daftar→verifikasi→login→lihat profil & riwayat pesanan;
route akun tidak bisa diakses tanpa login. Akun pelanggan tidak bisa masuk /admin. Commit.
```

---

## Prompt 10 — Checkout & integrasi Midtrans

```text
TAHAP 10: Pembelian layanan online via Midtrans (lihat PRD Bagian 8.10).

1. Alur checkout: dari detail layanan + pilih jadwal → wajib login customer → konfirmasi
   data → buat Order (status pending) dengan order_number unik dan snapshot data pemesan.
2. MidtransService.createSnapToken(order): kirim transaction_details, item_details,
   customer_details; muat Snap.js dengan MIDTRANS_CLIENT_KEY di frontend.
3. MidtransWebhookController di POST /midtrans/callback (kecualikan dari CSRF, tanpa locale):
   verifikasi signature_key = sha512(order_id+status_code+gross_amount+server_key);
   map transaction_status ke orders.status; updateOrCreate transactions; idempoten.
4. Kirim email konfirmasi saat status paid. Tampilkan halaman sukses/pending/gagal.

Kriteria selesai: di mode SANDBOX Midtrans, alur beli→bayar→webhook→order paid berfungsi,
transaksi tercatat, dan tampil di dashboard Admin Transaksi. Commit.
```

---

## Prompt 11 — Halaman & form Kemitraan Corporate (by invoice)

```text
TAHAP 11: Program Kemitraan Corporate (lihat PRD Bagian 3.8 & 8.11).

1. Halaman Kemitraan: penjelasan program, Manfaat Program Kemitraan Corporate (dari
   partnership_benefits), dan Penawaran Paket (kartu Blue/Silver/Gold/Platinum).
2. Form "Daftar Kemitraan": A. Informasi Perusahaan (nama, alamat, PIC, jabatan PIC,
   No.Telp/WhatsApp, email); B. Pilihan Paket; C. Penjadwalan Presentasi/Meeting
   (tanggal/waktu + alternatif); D. Catatan Tambahan. Tombol Kirim TANPA pembayaran online.
3. Submit menyimpan partnership_registration (status baru) + notifikasi ke Admin Transaksi.
   Pastikan terhubung ke alur invoice yang dibuat di Tahap 6.

Catatan: gunakan "tim PT Delta Tiga Enam" pada teks penjadwalan (bukan nama lain).

Kriteria selesai: pengunjung bisa mengisi & mengirim form; lead muncul di admin; admin bisa
menindaklanjuti hingga menerbitkan invoice. Commit.
```

---

## Prompt 12 — Pemolesan, SEO, performa & verifikasi akhir

```text
TAHAP 12: Finalisasi & Definition of Done.

1. Poles animasi/transisi hero & scroll; pastikan halus di mobile (tanpa jank).
2. SEO: meta title/description dinamis per bahasa, hreflang, sitemap.xml, Open Graph.
3. Performa & aksesibilitas: lazy-load + srcset gambar, kontras warna, navigasi keyboard.
4. Jalankan dan centang seluruh checklist Definition of Done di PRD Bagian 8.15.
   Perbaiki item yang belum lolos.
5. Tulis README ringkas: cara setup, env yang dibutuhkan, perintah build/run, dan cara uji
   Midtrans sandbox.

Kriteria selesai: semua item DoD terpenuhi; Lighthouse Performance & SEO >= 90 di halaman
utama; README lengkap. Commit final dan beri ringkasan status proyek menyeluruh.
```

---

## Tips agar Claude Code bekerja optimal
- **Satu tahap = satu fokus.** Selesaikan & verifikasi sebelum lanjut; ini menjaga konteks tetap relevan dan mengurangi error.
- **Minta rencana dulu.** Di tiap tahap, biarkan Claude Code memaparkan rencana singkat sebelum menulis kode, lalu setujui/koreksi.
- **Commit per tahap.** Memudahkan rollback bila satu tahap bermasalah.
- **Midtrans pakai sandbox** dulu; webhook lokal bisa diuji dengan tunnel (mis. ngrok) — minta Claude Code menyiapkan instruksinya.
- **Jika konteks terasa penuh**, mulai sesi baru dan awali lagi dengan Prompt 0 (Aturan Global) + sebutkan tahap terakhir yang selesai.
- **Isi nyata menyusul.** Detail tiap paket kemitraan, artikel blog, dan portofolio diisi via panel admin setelah aplikasi jadi.
- **Jangan lewati verifikasi.** Definition of Done (PRD §8.15) adalah penentu "selesai", bukan sekadar kode yang jalan.
