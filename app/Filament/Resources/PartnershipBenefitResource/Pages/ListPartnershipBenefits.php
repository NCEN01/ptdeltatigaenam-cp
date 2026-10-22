<?php

namespace App\Filament\Resources\PartnershipBenefitResource\Pages;

use App\Filament\Resources\PartnershipBenefitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

/**
 * Tanpa pengalih bahasa (LocaleSwitcher) dan concern Translatable-nya.
 *
 * Keduanya menuntut resource-nya memakai trait Translatable milik plugin
 * Spatie; resource ini tidak, sehingga halaman daftar ini menjawab galat 500
 * setiap kali dibuka. Pengalih itu juga tidak dibutuhkan: seperti seluruh
 * resource lain di panel ini, form-nya sudah menampilkan kolom ID dan EN
 * berdampingan.
 */
class ListPartnershipBenefits extends ListRecords
{
    protected static string $resource = PartnershipBenefitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
