<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\ManagesSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * Teks lepas halaman Kemitraan: kalimat pembuka di kepala halaman, dan judul
 * serta deskripsi seksi Lembaga Mitra. Semula bagian dari "Pengaturan Situs",
 * tempat yang tidak akan diduga admin yang sedang mengurus kemitraan.
 */
class ManagePartnershipCopy extends Page implements HasForms
{
    use InteractsWithForms, ManagesSettings;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Kemitraan';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Teks Halaman Kemitraan';

    protected static ?string $title = 'Teks Halaman Kemitraan';

    protected static string $view = 'filament.pages.manage-settings';

    protected function settingTypes(): array
    {
        return [
            'partnership_intro' => ['json', 'partnership'],
            'partnership_partners_title' => ['json', 'partnership'],
            'partnership_partners_desc' => ['json', 'partnership'],
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Pembuka Halaman')
                ->description('Kalimat di bawah judul besar halaman Kemitraan.')
                ->schema([
                    Textarea::make('partnership_intro.id')->label('Intro (ID)')->required()->maxLength(1000)->rows(3),
                    Textarea::make('partnership_intro.en')->label('Intro (EN)')->maxLength(1000)->rows(3),
                ])->columns(2),

            Section::make('Seksi Lembaga Mitra')
                ->description('Judul dan kalimat di atas deretan logo mitra. Logonya sendiri diatur di Konten, menu Mitra (Logo).')
                ->schema([
                    TextInput::make('partnership_partners_title.id')->label('Judul (ID)')->maxLength(160)
                        ->helperText('Apit satu penggalan dengan tanda bintang untuk dimiringkan, mis. Lembaga sertifikasi yang *bekerja sama* dengan kami. Kosongkan untuk memakai teks bawaan.'),
                    TextInput::make('partnership_partners_title.en')->label('Judul (EN)')->maxLength(160),
                    Textarea::make('partnership_partners_desc.id')->label('Deskripsi (ID)')->maxLength(600)->rows(3)
                        ->helperText('Kosongkan untuk memakai teks bawaan.'),
                    Textarea::make('partnership_partners_desc.en')->label('Deskripsi (EN)')->maxLength(600)->rows(3),
                ])->columns(2),
        ])->statePath('data');
    }
}
