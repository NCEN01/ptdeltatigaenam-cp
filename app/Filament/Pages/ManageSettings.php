<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('manage_settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'site_name' => Setting::get('site_name'),
            'site_email' => Setting::get('site_email'),
            'site_phone' => Setting::get('site_phone'),
            'linkedin_url' => Setting::get('linkedin_url'),
            'company_tagline' => Setting::get('company_tagline') ?: ['id' => '', 'en' => ''],
            'company_about' => Setting::get('company_about') ?: ['id' => '', 'en' => ''],
            'company_vision' => Setting::get('company_vision') ?: ['id' => '', 'en' => ''],
            'partnership_intro' => Setting::get('partnership_intro') ?: ['id' => '', 'en' => ''],
            'partnership_partners_title' => Setting::get('partnership_partners_title') ?: ['id' => '', 'en' => ''],
            'partnership_partners_desc' => Setting::get('partnership_partners_desc') ?: ['id' => '', 'en' => ''],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Profil Perusahaan')->schema([
                TextInput::make('site_name')->label('Nama Situs')->required()->maxLength(100),
                TextInput::make('site_email')->label('Email')->email()->required()->maxLength(255),
                TextInput::make('site_phone')->label('Telepon')->required()->maxLength(30),
                TextInput::make('linkedin_url')->label('LinkedIn')->url()->nullable()->maxLength(255),
            ])->columns(2),

            Section::make('Tentang (ID/EN)')->schema([
                Textarea::make('company_tagline.id')->label('Tagline (ID)')->required()->maxLength(500)->rows(2),
                Textarea::make('company_tagline.en')->label('Tagline (EN)')->nullable()->maxLength(500)->rows(2),
                Textarea::make('company_about.id')->label('Tentang (ID)')->required()->maxLength(2000)->rows(4),
                Textarea::make('company_about.en')->label('Tentang (EN)')->nullable()->maxLength(2000)->rows(4),
                Textarea::make('company_vision.id')->label('Visi (ID)')->required()->maxLength(1000)->rows(2),
                Textarea::make('company_vision.en')->label('Visi (EN)')->nullable()->maxLength(1000)->rows(2),
            ])->columns(2),

            Section::make('Kemitraan')->schema([
                Textarea::make('partnership_intro.id')->label('Intro Kemitraan (ID)')->required()->maxLength(1000)->rows(3),
                Textarea::make('partnership_intro.en')->label('Intro Kemitraan (EN)')->nullable()->maxLength(1000)->rows(3),
            ])->columns(2),

            Section::make('Seksi Lembaga Mitra (Halaman Kemitraan)')
                ->description('Judul dan kalimat di atas deretan logo mitra. Logonya sendiri diatur di menu Konten → Mitra (Logo).')
                ->schema([
                    TextInput::make('partnership_partners_title.id')->label('Judul (ID)')->nullable()->maxLength(160)
                        // Satu penggalan boleh dimiringkan tanpa perlu mengetik HTML.
                        // Tanpa bintang, judulnya tampil tegak seluruhnya.
                        ->helperText('Apit satu penggalan dengan tanda bintang untuk dimiringkan, mis. Lembaga sertifikasi yang *bekerja sama* dengan kami. Kosongkan untuk memakai teks bawaan.'),
                    TextInput::make('partnership_partners_title.en')->label('Judul (EN)')->nullable()->maxLength(160),
                    Textarea::make('partnership_partners_desc.id')->label('Deskripsi (ID)')->nullable()->maxLength(600)->rows(3)
                        ->helperText('Kosongkan untuk memakai teks bawaan.'),
                    Textarea::make('partnership_partners_desc.en')->label('Deskripsi (EN)')->nullable()->maxLength(600)->rows(3),
                ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->put('site_name', $data['site_name'], 'text', 'general');
        $this->put('site_email', $data['site_email'], 'text', 'general');
        $this->put('site_phone', $data['site_phone'], 'text', 'general');
        $this->put('linkedin_url', $data['linkedin_url'], 'text', 'general');
        $this->put('company_tagline', json_encode($data['company_tagline']), 'json', 'general');
        $this->put('company_about', json_encode($data['company_about']), 'json', 'general');
        $this->put('company_vision', json_encode($data['company_vision']), 'json', 'general');
        $this->put('partnership_intro', json_encode($data['partnership_intro']), 'json', 'partnership');
        $this->put('partnership_partners_title', json_encode($data['partnership_partners_title']), 'json', 'partnership');
        $this->put('partnership_partners_desc', json_encode($data['partnership_partners_desc']), 'json', 'partnership');

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }

    private function put(string $key, mixed $value, string $type, string $group): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
    }
}