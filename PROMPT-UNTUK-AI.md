# Prompt untuk AI Assistant

Salin seluruh teks di dalam kotak di bawah ini, lalu tempelkan ke AI coding
assistant Anda (Claude Code, Cursor, Copilot, atau sejenisnya) setelah membuka
folder proyek ini. Sesuaikan bagian yang bertanda kurung siku.

---

```
Kamu membantuku menyiapkan sebuah proyek web supaya bisa jalan di komputerku,
untuk aku presentasikan ke klien. Aku bukan yang membangun proyek ini, jadi
tolong jangan mengubah desain, fitur, atau isi kontennya sama sekali. Tugasmu
murni membuatnya berjalan.

TENTANG PROYEKNYA
- Nama: Website PT Delta Tiga Enam (company profile + panel admin)
- Stack: Laravel 11, PHP 8.2, Filament 3, MySQL/MariaDB, Tailwind CSS 3,
  Alpine.js, Vite 6. Tidak ada React dan tidak ada TypeScript.
- Bahasa situs: Indonesia dan Inggris
- Sistem operasi aku: [Windows / macOS / Linux]
- Aku pakai: [XAMPP / Laragon / Docker / instalasi terpisah]

LANGKAH PERTAMAMU
Baca berkas SETUP-LOKAL.md di akar proyek ini. Isinya panduan lengkap
langkah demi langkah, termasuk nilai .env yang harus diubah, cara mengimpor
database dari setup/deltatigaenam.sql, dan daftar masalah yang umum muncul.
Ikuti panduan itu, jangan mengarang langkah sendiri.

CARA KERJA YANG AKU MAU
1. Kerjakan satu langkah dalam satu waktu. Jalankan perintahnya, tunjukkan
   hasilnya, baru lanjut ke langkah berikutnya.
2. Sebelum menjalankan perintah apa pun, jelaskan dalam satu kalimat singkat
   apa gunanya. Aku ingin paham, bukan sekadar menyalin.
3. Kalau ada error, baca pesannya dan lihat storage/logs/laravel.log baris
   paling bawah sebelum menebak penyebabnya.
4. Setelah semuanya jalan, pandu aku memeriksa daftar periksa di bagian
   "Pastikan semuanya benar sebelum presentasi" pada SETUP-LOKAL.md.

LARANGAN KERAS
- JANGAN menjalankan `php artisan test`, `php artisan migrate:fresh`,
  `php artisan migrate:refresh`, atau `php artisan db:wipe`. Sebagian test
  memakai RefreshDatabase yang MENGHAPUS SELURUH TABEL lebih dulu. Ini pernah
  terjadi di proyek ini dan seluruh isi situs hilang.
- JANGAN mengubah berkas di resources/views/, resources/css/, app/, atau
  database/migrations/. Aku hanya menjalankan, bukan mengembangkan.
- JANGAN menjalankan `composer update` atau `npm update`. Cukup
  `composer install` dan `npm install` yang memasang versi terkunci.
- JANGAN commit atau push apa pun ke Git.
- Kalau menurutmu ada yang perlu diperbaiki di kodenya, katakan saja padaku.
  Jangan langsung mengubahnya.

Mulai dari membaca SETUP-LOKAL.md, lalu beri tahu aku apa yang perlu aku
pasang lebih dulu di komputerku.
```

---

## Setelah situsnya jalan

Kalau nanti Anda ingin AI membantu memahami isi situs untuk bahan presentasi,
gunakan prompt kedua ini:

```
Situsnya sudah jalan di http://localhost:8000/id. Bantu aku menyiapkan bahan
presentasi untuk klien.

Baca berkas README.md dan PRODUCT.md di proyek ini, lalu telusuri halaman
utamanya di resources/views/pages/. Buatkan aku ringkasan berisi:

1. Daftar halaman yang ada, dan apa fungsi masing-masing bagi pengunjung
2. Apa saja yang bisa diubah sendiri oleh klien lewat panel admin
   (/d36-panel), dan apa yang tertanam di kode
3. Tiga sampai lima hal yang paling layak ditonjolkan saat demo

Tulis dalam Bahasa Indonesia, untuk dibacakan ke orang non-teknis. Jangan
menyebut nama berkas atau istilah teknis di ringkasannya.
```

Untuk daftar lengkap tulisan yang tertanam di kode dan mana yang bisa diubah
lewat panel admin, lihat `KONTEN-EDIT.md`.
