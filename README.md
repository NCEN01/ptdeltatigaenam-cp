# PT Delta Tiga Enam — Website Company Profile

Website company profile + admin panel + payment gateway untuk PT Delta Tiga Enam
(human capital, pelatihan, sertifikasi profesi). Dibangun dengan **Laravel 11 ·
Filament 3 · MySQL/MariaDB · Midtrans**, dwibahasa **ID/EN**, responsif penuh + PWA.

## Fitur

- **Publik (dwibahasa ID/EN):** landing dengan hero beranimasi + rotasi kategori unggulan,
  About, Layanan (kategori/detail/jadwal), Portofolio, Blog, Kemitraan Corporate, Kontak.
- **Akun pelanggan:** registrasi, verifikasi email, login, reset password, profil,
  riwayat pesanan (guard `customer`, terpisah dari admin).
- **Pembelian online (Midtrans Snap):** checkout → order → Snap → webhook → status.
- **Kemitraan Corporate (by invoice):** halaman program + form Daftar Kemitraan →
  lead ke Admin Transaksi → invoice (PDF) — terpisah dari Midtrans.
- **Admin Panel (`/admin`, Filament):**
  - *Admin Konten:* kategori/layanan/jadwal, banner, blog, portofolio, klien, testimoni,
    mitra, paket & manfaat kemitraan, kantor, misi, pengaturan, pesan kontak — semua dwibahasa.
  - *Admin Transaksi:* pesanan & transaksi Midtrans (read + ekspor CSV), dashboard +
    grafik, pendaftaran kemitraan (alur status), invoice + generate PDF.
  - RBAC: `super_admin`, `admin_transaksi`, `admin_konten` (spatie/laravel-permission).
- **Media pipeline:** validasi + resize + konversi WebP + varian responsif + sanitasi SVG.

## Prasyarat

- PHP 8.2+ (ekstensi: `pdo_mysql`, `mbstring`, `gd`, `intl`, `zip`, `bcmath`, `fileinfo`, `openssl`, `exif`)
- Composer 2 · Node.js 20+ & npm · MySQL 8 / MariaDB 10.6+
- (XAMPP Windows: aktifkan `extension=intl` di `php.ini`.)

## Setup

```bash
# 1. Dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
# Edit .env: DB_*, MIDTRANS_*, ADMIN_EMAIL/ADMIN_PASSWORD, MAIL_*

# 3. Database
php artisan migrate:fresh --seed
php artisan storage:link

# 4. Assets
npm run build      # atau: npm run dev
```

### Variabel `.env` penting

| Key | Keterangan |
|---|---|
| `APP_LOCALE` / `APP_FALLBACK_LOCALE` | `id` |
| `DB_DATABASE` | `deltatigaenam` |
| `MIDTRANS_SERVER_KEY` / `MIDTRANS_CLIENT_KEY` | kunci sandbox Midtrans |
| `MIDTRANS_IS_PRODUCTION` | `false` (sandbox) |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | kredensial super admin awal (seeder) |
| `MAIL_MAILER` | `log` untuk dev (verifikasi & notifikasi tertulis di log) |

## Menjalankan

```bash
php artisan serve      # http://127.0.0.1:8000
npm run dev            # HMR aset (opsional saat develop)
```

- Situs publik: `/` → redirect `/id` (atau `/en`).
- Admin panel: `/admin` (login dengan kredensial `ADMIN_*`).

## Uji Midtrans (sandbox)

Lihat **`docs/MIDTRANS_SANDBOX.md`**. Ringkas: isi kunci sandbox, jalankan `ngrok http 8000`,
set Payment Notification URL ke `https://<ngrok>/midtrans/callback`, lalu uji
beli → bayar (simulator) → webhook → order `paid`.

## Pengujian

```bash
php artisan test
```
Mencakup: media pipeline, RBAC & panel, invoice + PDF, situs publik + i18n,
auth pelanggan, dan webhook Midtrans (signature, idempotensi).

## Struktur singkat

```
app/
  Filament/        # Resources (konten + transaksi), Pages, Widgets, MediaUpload
  Http/Controllers # Publik, Checkout, MidtransWebhook, Auth/*, Account/*, Sitemap
  Http/Middleware  # SetLocale, EnsureCustomerEmailVerified
  Models/          # Domain models (+ HasTranslations untuk kolom JSON i18n)
  Services/        # MediaService, InvoiceService, MidtransService
resources/views/
  components/       # layout, header, footer, field, page-header, cta-band, auth-layout
  pages/            # home, about, services, portfolio, blog, contact, partnership, checkout
  auth/ account/    # auth & area akun pelanggan
database/           # migrations (identik database.sql), seeders
lang/{id,en}/       # string UI statis
```

## Catatan desain

Identitas: **navy dominan + putih**, aksen **gold** tipis. Tipografi **Clash Display +
General Sans** (Fontshare) + **IBM Plex Mono**. Minimalis-editorial, whitespace berani,
kontras tipografi ekstrem, motion bertujuan (AOS/Swiper, hormati `prefers-reduced-motion`).

---
© PT Delta Tiga Enam · www.deltatigaenam.com
