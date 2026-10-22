<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\ManagesSettings;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * Isi halaman Tentang yang bukan daftar: naskah profil, visi, tagline, dan
 * berkas PDF Profil Perusahaan. Semula bagian dari "Pengaturan Situs".
 */
class ManageAboutPage extends Page implements HasForms
{
    use InteractsWithForms, ManagesSettings;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 15;

    protected static ?string $navigationLabel = 'Halaman Tentang';

    protected static ?string $title = 'Halaman Tentang';

    protected static string $view = 'filament.pages.manage-settings';

    protected function settingTypes(): array
    {
        return [
            'company_profile_file' => ['file', 'general'],
            'company_tagline' => ['json', 'general'],
            'company_about' => ['json', 'general'],
            'company_vision' => ['json', 'general'],
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Profil Perusahaan (PDF)')
                ->description('Berkas yang dibuka dan diunduh pengunjung dari kartu "Profil Perusahaan" di halaman Tentang. Kosongkan bila belum ada; kartunya otomatis tidak ditampilkan.')
                ->schema([
                    FileUpload::make('company_profile_file')
                        ->label('Berkas profil perusahaan')
                        ->disk('public')
                        ->directory('documents')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(20480)
                        ->helperText('PDF saja, maksimal 20 MB. Satu berkas: mengunggah yang baru menggantikan yang lama.')
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                ]),

            Section::make('Naskah Halaman Tentang')
                ->description('Tagline juga dipakai sebagai deskripsi bawaan halaman di hasil pencarian Google.')
                ->schema([
                    Textarea::make('company_tagline.id')->label('Tagline (ID)')->required()->maxLength(500)->rows(2),
                    Textarea::make('company_tagline.en')->label('Tagline (EN)')->maxLength(500)->rows(2),
                    Textarea::make('company_about.id')->label('Tentang Perusahaan (ID)')->required()->maxLength(2000)->rows(5),
                    Textarea::make('company_about.en')->label('Tentang Perusahaan (EN)')->maxLength(2000)->rows(5),
                    Textarea::make('company_vision.id')->label('Visi (ID)')->required()->maxLength(1000)->rows(3),
                    Textarea::make('company_vision.en')->label('Visi (EN)')->maxLength(1000)->rows(3),
                ])->columns(2),
        ])->statePath('data');
    }
}
