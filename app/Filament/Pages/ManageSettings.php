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

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }

    private function put(string $key, mixed $value, string $type, string $group): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
    }
}