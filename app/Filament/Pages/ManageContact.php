<?php

namespace App\Filament\Pages;

use App\Filament\Pages\Concerns\ManagesSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

/**
 * Kontak perusahaan dan media sosial. Dibaca Company::contact() untuk kartu
 * kontak di halaman Kontak dan kolom "Lokasi & Kontak" di footer. Semula
 * bagian dari "Pengaturan Situs"; alamat kantornya ada di menu Alamat Kantor
 * tepat di bawahnya.
 */
class ManageContact extends Page implements HasForms
{
    use InteractsWithForms, ManagesSettings;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Kontak & Media Sosial';

    protected static ?string $title = 'Kontak & Media Sosial';

    protected static string $view = 'filament.pages.manage-settings';

    protected function settingTypes(): array
    {
        return [
            'site_name' => ['text', 'general'],
            'site_email' => ['text', 'general'],
            'site_hours' => ['json', 'general'],
            'site_phone' => ['text', 'general'],
            'site_phone_tel' => ['text', 'general'],
            'site_whatsapp_label' => ['text', 'general'],
            'site_whatsapp' => ['text', 'general'],
            'linkedin_url' => ['text', 'general'],
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Kontak')
                ->description('Tampil di halaman Kontak dan di footer seluruh halaman.')
                ->schema([
                    TextInput::make('site_email')->label('Email')->email()->required()->maxLength(255)
                        ->placeholder('info@deltatigaenam.com'),
                    TextInput::make('site_name')->label('Nama Perusahaan')->required()->maxLength(100)
                        ->helperText('Dipakai sebagai nama situs saat tautan dibagikan ke WhatsApp atau media sosial.'),
                    TextInput::make('site_hours.id')->label('Jam Operasional (ID)')->maxLength(80)
                        ->placeholder('Senin sampai Jumat · 08.00-17.00'),
                    TextInput::make('site_hours.en')->label('Jam Operasional (EN)')->maxLength(80)
                        ->placeholder('Mon to Fri · 8am-5pm'),
                    /* Telepon dan WhatsApp disimpan dua rupa: yang tampil untuk
                       dibaca manusia, dan yang hanya-angka untuk tautan panggil
                       dan chat. Menurunkan yang kedua dari yang pertama tidak
                       bisa diandalkan: kode wilayah 021 harus kehilangan nol
                       depannya dan mendapat +62, aturan yang berbeda untuk
                       nomor seluler. */
                    TextInput::make('site_phone')->label('Telepon (tampil)')->required()->maxLength(30)
                        ->placeholder('(021) 5890 5002'),
                    TextInput::make('site_phone_tel')->label('Telepon (untuk tautan panggil)')->maxLength(30)
                        ->placeholder('+622158905002')
                        ->helperText('Hanya angka dengan kode negara.'),
                    TextInput::make('site_whatsapp_label')->label('WhatsApp (tampil)')->maxLength(30)
                        ->placeholder('0818 834 766'),
                    TextInput::make('site_whatsapp')->label('WhatsApp (untuk tautan chat)')->maxLength(30)
                        ->placeholder('62818834766')
                        ->helperText('Diawali 62, tanpa tanda plus dan tanpa spasi.'),
                ])->columns(2),

            Section::make('Media Sosial')
                ->schema([
                    TextInput::make('linkedin_url')->label('LinkedIn')->url()->maxLength(255)
                        ->placeholder('https://linkedin.com/company/deltatigaenam'),
                ]),
        ])->statePath('data');
    }
}
