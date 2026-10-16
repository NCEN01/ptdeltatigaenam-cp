<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Dilempar App\Models\Concerns\HasRecordLimit ketika sebuah baris akan dibuat
 * padahal tabelnya sudah penuh.
 *
 * Pengecualian sendiri, bukan RuntimeException biasa: dengan begitu pemanggil
 * yang memang perlu menangani keadaan ini — penyemai yang idempoten, misalnya —
 * bisa menangkap persis hal ini tanpa ikut menelan galat lain di sekitarnya.
 *
 * Dalam keadaan normal pengecualian ini tidak akan pernah sampai ke layar
 * admin: Filament sudah menyembunyikan tombol "Buat" dan menolak rutenya
 * lewat RestrictsToPermission::canCreate() jauh sebelum penyimpanan dimulai.
 * Jadi kalau ia sampai muncul, yang terjadi adalah penambahan dari luar panel,
 * dan pesannya ditulis untuk dibaca di log oleh orang yang menulis kode itu.
 */
class RecordLimitReached extends RuntimeException
{
    public static function for(string $modelClass, int $max): self
    {
        $name = class_basename($modelClass);

        return new self(
            "Batas {$max} baris untuk {$name} sudah tercapai. Hapus atau sunting "
            ."baris yang sudah ada, atau ubah {$name}::MAX_RECORDS bila batasnya "
            .'memang perlu dinaikkan — dan periksa dulu tampilan yang memakainya '
            .'masih muat.'
        );
    }
}
