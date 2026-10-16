<?php

namespace App\Models\Concerns;

use App\Exceptions\RecordLimitReached;

/**
 * Batas jumlah baris untuk tabel yang isinya dipakai di tempat dengan ruang
 * tetap: dua kartu Instagram di beranda, enam tanya-jawab, empat paket
 * kemitraan, dan deretan logo mitra serta klien.
 *
 * Batasnya ditegakkan di model, bukan hanya di panel admin. Tombol "Buat" yang
 * disembunyikan Filament hanya menutup satu jalan masuk; penyemai, perintah
 * artisan, impor massal, dan kode yang ditulis nanti tetap bisa menambah baris
 * ke-27 tanpa ada yang menghalangi. Yang muncul kemudian bukan pesan galat
 * melainkan hal yang jauh lebih sulit dilacak: tata letak yang jebol di satu
 * halaman, atau data yang tersimpan rapi di basis data tetapi tidak pernah
 * tampil sama sekali.
 *
 * Pemakaian:
 *
 *     class InstagramUpdate extends Model
 *     {
 *         use HasRecordLimit;
 *
 *         public const MAX_RECORDS = 2;
 *     }
 *
 * App\Filament\Concerns\RestrictsToPermission membaca batas yang sama lewat
 * hasRoomForMore(), jadi angkanya hanya ditulis sekali di model dan panel
 * admin ikut menyesuaikan sendiri tanpa perlu disetel ulang per resource.
 */
trait HasRecordLimit
{
    /** Batas baris. Model yang memakai trait ini wajib punya MAX_RECORDS. */
    public static function maxRecords(): int
    {
        return (int) static::MAX_RECORDS;
    }

    public static function recordCount(): int
    {
        return static::query()->count();
    }

    public static function hasRoomForMore(): bool
    {
        return static::recordCount() < static::maxRecords();
    }

    public static function remainingSlots(): int
    {
        return max(0, static::maxRecords() - static::recordCount());
    }

    protected static function bootHasRecordLimit(): void
    {
        /* creating, bukan saving: penyuntingan baris yang sudah ada tidak boleh
           ikut terhalang. Kalau batasnya pernah terlampaui — misalnya angkanya
           diturunkan setelah datanya terisi — admin justru harus tetap bisa
           menyunting dan menghapus yang sudah ada untuk kembali ke bawah batas. */
        static::creating(function (self $model): void {
            if (static::hasRoomForMore()) {
                return;
            }

            throw RecordLimitReached::for(static::class, static::maxRecords());
        });
    }
}
