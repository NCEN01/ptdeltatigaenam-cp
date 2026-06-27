# Menguji Pembayaran Midtrans (Sandbox)

Alur online (pembelian layanan) memakai **Midtrans Snap**. Webhook server-to-server
adalah sumber kebenaran status pesanan.

## 1. Kunci sandbox
1. Daftar/masuk ke https://dashboard.sandbox.midtrans.com
2. Settings → Access Keys: salin **Server Key** dan **Client Key**.
3. Isi `.env`:
   ```
   MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
   MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
   MIDTRANS_IS_PRODUCTION=false
   ```
4. `php artisan config:clear`

## 2. Menjalankan aplikasi
```
php artisan serve            # http://127.0.0.1:8000
npm run dev                  # atau: npm run build
```
Pastikan MySQL (XAMPP/MariaDB) berjalan.

## 3. Webhook lokal via tunnel (ngrok)
Midtrans perlu menjangkau `POST /midtrans/callback` dari internet.
```
ngrok http 8000
```
Salin URL https publik (mis. `https://abcd-1234.ngrok-free.app`), lalu di
**Dashboard Sandbox → Settings → Configuration → Payment Notification URL** isikan:
```
https://abcd-1234.ngrok-free.app/midtrans/callback
```
Catatan: route ini dikecualikan dari CSRF dan tidak memakai prefix locale.

## 4. Alur uji
1. Daftar akun pelanggan → verifikasi email (link muncul di `storage/logs/laravel.log`
   karena `MAIL_MAILER=log`).
2. Buka detail layanan → pilih jadwal → **Daftar/Beli** → konfirmasi → **Bayar**.
3. Snap terbuka. Gunakan simulator pembayaran sandbox (mis. kartu
   `4811 1111 1111 1114`, atau VA/QRIS lewat simulator Midtrans).
4. Setelah sukses, Midtrans mengirim notifikasi ke webhook → `orders.status` menjadi
   `paid`, baris `transactions` tercatat, email konfirmasi tertulis di log.
5. Cek **/admin** (Admin Transaksi) → Pesanan & Transaksi muncul; dashboard memuat angka.

## 5. Verifikasi tanda tangan
`signature_key = sha512(order_id + status_code + gross_amount + server_key)`.
Webhook menolak (403) bila tanda tangan tidak valid dan bersifat idempoten
(notifikasi berulang tidak menggandakan email atau status).
