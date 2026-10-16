<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use App\Models\Faq;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFaqs extends ListRecords
{
    protected static string $resource = FaqResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Kuota ditulis apa adanya di bawah judul halaman.
     *
     * Ketika keenam slotnya terpakai, Filament hanya menyembunyikan tombol
     * "Buat" — dan tombol yang hilang tanpa penjelasan terbaca seperti izin
     * yang dicabut atau panel yang rusak, bukan seperti kuota yang habis.
     * Kalimat ini yang membedakan keduanya.
     */
    public function getSubheading(): ?string
    {
        if (Faq::remainingSlots() === 0) {
            return 'Keenam slot sudah terpakai. Sunting atau hapus salah satunya untuk menggantinya.';
        }

        return 'Terpakai '.Faq::recordCount().' dari '.Faq::maxRecords().' slot.';
    }
}
