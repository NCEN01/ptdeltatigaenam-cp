<?php

namespace App\Filament\Resources\OfficeResource\Pages;

use App\Filament\Resources\OfficeResource;
use App\Models\Office;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOffices extends ListRecords
{
    protected static string $resource = OfficeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Kuota ditulis apa adanya. Ketika ketiga slotnya terpakai, Filament
     * hanya menyembunyikan tombol "Buat" — dan tombol yang hilang tanpa
     * penjelasan terbaca seperti izin yang dicabut, bukan kuota yang habis.
     */
    public function getSubheading(): ?string
    {
        if (Office::remainingSlots() === 0) {
            return "Ketiga slot sudah terpakai. Sunting atau hapus salah satunya untuk menggantinya.";
        }

        return "Terpakai " . Office::recordCount() . " dari " . Office::maxRecords() . " slot. Yang tidak diisi tidak akan tampil.";
    }
}
