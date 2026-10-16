<?php

namespace App\Filament\Resources\FaqResource\Pages;

use App\Filament\Resources\FaqResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFaq extends CreateRecord
{
    protected static string $resource = FaqResource::class;

    /**
     * Tanpa mutasi data di sini: formulirnya mengirim question dan answer
     * sebagai array per bahasa, dan Faq::booted() yang meluruskannya saat
     * menyimpan. Satu jalur pelurusan untuk Create maupun Edit.
     */
}
