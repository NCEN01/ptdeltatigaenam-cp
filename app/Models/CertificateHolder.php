<?php

namespace App\Models;

use App\Models\Concerns\HasRecordLimit;
use Illuminate\Database\Eloquent\Model;

class CertificateHolder extends Model
{
    use HasRecordLimit;

    /**
     * Lima ribu pemegang sertifikat.
     *
     * Jauh di atas batas tabel lain di situs ini, dan memang harus: tabel ini
     * satu-satunya yang isinya tumbuh terus tanpa pernah dibuang, karena
     * riwayat itulah yang dicari pengunjung yang sedang memeriksa keaslian
     * sebuah sertifikat. Angka 5.000 bukan batas tata letak — halamannya
     * memakai pencarian dan pagination sepuluh per halaman, jadi berapa pun
     * isinya yang dirender tetap sepuluh baris.
     *
     * Yang dijaga angka ini adalah tiga query yang memindai seluruh baris
     * aktif pada setiap kunjungan: hitungan papan ringkasan, hitungan yang
     * kedaluwarsa, dan pengelompokan kualifikasi. Pada 5.000 baris ketiganya
     * masih di bawah sepuluh milidetik; pada ratusan ribu, halaman ini perlu
     * dirancang ulang dengan hitungan yang disimpan, bukan dihitung ulang —
     * dan batas ini yang memaksa percakapan itu terjadi sebelum halamannya
     * melambat, bukan sesudahnya.
     */
    public const MAX_RECORDS = 5000;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'issued_at' => 'date',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
