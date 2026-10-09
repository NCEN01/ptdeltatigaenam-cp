<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageStats extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Statistik Perusahaan';

    protected static ?string $title = 'Statistik Perusahaan';

    protected static ?int $navigationSort = 95;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('manage_settings');
    }

    public function mount(): void
    {
        $this->form->fill([

            // hero_stat_* dilepas: beranda dan Tentang kini membaca empat angka
            // yang sama lewat Company::stats(), jadi set kedua itu tidak punya
            // pembaca. Barisnya ditinggal di basis data apa adanya, tidak
            // dihapus — menghapus baris pengaturan tidak menambah apa pun dan
            // menutup jalan kalau suatu saat beranda perlu angkanya sendiri.
            'about_stat_1_value' => Setting::get('about_stat_1_value', '10+'),
            'about_stat_1_label' => Setting::get('about_stat_1_label') ?: ['id' => 'Tahun pengalaman', 'en' => 'Years of experience'],
            'about_stat_2_value' => Setting::get('about_stat_2_value', '500+'),
            'about_stat_2_label' => Setting::get('about_stat_2_label') ?: ['id' => 'Profesional terlatih', 'en' => 'Professionals trained'],
            'about_stat_3_value' => Setting::get('about_stat_3_value', '50+'),
            'about_stat_3_label' => Setting::get('about_stat_3_label') ?: ['id' => 'Klien korporat', 'en' => 'Corporate clients'],
            'about_stat_4_value' => Setting::get('about_stat_4_value', '20+'),
            'about_stat_4_label' => Setting::get('about_stat_4_label') ?: ['id' => 'Program sertifikasi', 'en' => 'Certification programs'],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Statistik Perusahaan')
                ->description('Empat angka ini muncul di dua tempat sekaligus: di bawah bagian Kategori Layanan pada halaman Beranda, dan di halaman Tentang Kami. Mengubahnya di sini mengubah keduanya. Kolom yang dikosongkan tidak ditampilkan.')
                ->schema([
                    TextInput::make('about_stat_1_value')->label('Stat #1 — Angka')->required()->maxLength(20),
                    TextInput::make('about_stat_1_label.id')->label('Stat #1 — Label (ID)')->required()->maxLength(100),
                    TextInput::make('about_stat_1_label.en')->label('Stat #1 — Label (EN)')->maxLength(100),
                    TextInput::make('about_stat_2_value')->label('Stat #2 — Angka')->required()->maxLength(20),
                    TextInput::make('about_stat_2_label.id')->label('Stat #2 — Label (ID)')->required()->maxLength(100),
                    TextInput::make('about_stat_2_label.en')->label('Stat #2 — Label (EN)')->maxLength(100),
                    TextInput::make('about_stat_3_value')->label('Stat #3 — Angka')->required()->maxLength(20),
                    TextInput::make('about_stat_3_label.id')->label('Stat #3 — Label (ID)')->required()->maxLength(100),
                    TextInput::make('about_stat_3_label.en')->label('Stat #3 — Label (EN)')->maxLength(100),
                    TextInput::make('about_stat_4_value')->label('Stat #4 — Angka')->required()->maxLength(20),
                    TextInput::make('about_stat_4_label.id')->label('Stat #4 — Label (ID)')->required()->maxLength(100),
                    TextInput::make('about_stat_4_label.en')->label('Stat #4 — Label (EN)')->maxLength(100),
                ])->columns(3),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (['about_stat_1', 'about_stat_2', 'about_stat_3', 'about_stat_4'] as $prefix) {
            $valKey = "{$prefix}_value";
            $labelKey = "{$prefix}_label";
            if (array_key_exists($valKey, $data)) {
                Setting::updateOrCreate(['key' => $valKey], ['value' => $data[$valKey], 'type' => 'text', 'group' => 'stats']);
            }
            if (array_key_exists($labelKey, $data)) {
                Setting::updateOrCreate(['key' => $labelKey], ['value' => json_encode($data[$labelKey]), 'type' => 'json', 'group' => 'stats']);
            }
        }

        Notification::make()->title('Statistik Perusahaan disimpan')->success()->send();
    }
}