<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManagePromoBar extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Bar Promo Atas';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Bar Promo Atas';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('manage_settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'topbar_active' => (bool) Setting::get('topbar_active'),
            'topbar_promo_active' => (bool) Setting::get('topbar_promo_active'),
            'topbar_promo_text' => Setting::get('topbar_promo_text') ?: ['id' => '', 'en' => ''],
            'topbar_promo_price_old' => Setting::get('topbar_promo_price_old'),
            'topbar_promo_price_new' => Setting::get('topbar_promo_price_new'),
            'topbar_promo_note' => Setting::get('topbar_promo_note') ?: ['id' => '', 'en' => ''],
            'topbar_promo_cta' => Setting::get('topbar_promo_cta') ?: ['id' => '', 'en' => ''],
            'topbar_promo_link' => Setting::get('topbar_promo_link'),
            'topbar_agenda_active' => (bool) Setting::get('topbar_agenda_active'),
            'topbar_agenda_text' => Setting::get('topbar_agenda_text') ?: ['id' => '', 'en' => ''],
            'topbar_agenda_cta' => Setting::get('topbar_agenda_cta') ?: ['id' => '', 'en' => ''],
            'topbar_agenda_link' => Setting::get('topbar_agenda_link'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Bar Promo Atas (announcement)')
                ->description('Strip tipis di atas navbar. Bisa menampilkan 2 info bergantian (promo & agenda). Teks tombol otomatis menjadi tautan ke halaman tujuan. Mendukung 2 bahasa (ID/EN).')
                ->schema([
                    Toggle::make('topbar_active')->label('Aktifkan bar promo atas')->live()->columnSpanFull(),

                    Fieldset::make('Slide 1 — Promo / Diskon')
                        ->schema([
                            Toggle::make('topbar_promo_active')->label('Tampilkan slide promo')->live()->columnSpanFull(),
                            TextInput::make('topbar_promo_text.id')->label('Teks (ID)')->placeholder('Promo Sertifikasi BNSP')->maxLength(80)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_text.en')->label('Teks (EN)')->placeholder('BNSP Certification Promo')->maxLength(80)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_price_old')->label('Harga Coret (opsional)')->placeholder('Rp 15.000.000')->maxLength(30)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_price_new')->label('Harga Promo')->placeholder('Rp 9.000.000')->maxLength(30)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_note.id')->label('Catatan (ID)')->placeholder('hanya berlaku hari ini')->maxLength(60)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_note.en')->label('Catatan (EN)')->placeholder('today only')->maxLength(60)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_cta.id')->label('Teks Tombol (ID)')->placeholder('Checkout Sekarang')->maxLength(30)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_cta.en')->label('Teks Tombol (EN)')->placeholder('Checkout Now')->maxLength(30)->visible(fn ($get) => (bool) $get('topbar_promo_active')),
                            TextInput::make('topbar_promo_link')->label('Tautan Tujuan')->placeholder('/checkout/6  atau  /layanan/sertifikasi-bnsp')->helperText('Path tanpa kode bahasa. Contoh: /checkout/6 atau /layanan/slug. Kode bahasa (id/en) ditambahkan otomatis.')->maxLength(255)->visible(fn ($get) => (bool) $get('topbar_promo_active'))->columnSpanFull(),
                        ])->columns(2)->visible(fn ($get) => (bool) $get('topbar_active')),

                    Fieldset::make('Slide 2 — Agenda / Pelatihan')
                        ->schema([
                            Toggle::make('topbar_agenda_active')->label('Tampilkan slide agenda')->live()->columnSpanFull(),
                            TextInput::make('topbar_agenda_text.id')->label('Teks (ID)')->placeholder('Pelatihan & sertifikasi terbaru — lihat jadwal terdekat')->maxLength(100)->visible(fn ($get) => (bool) $get('topbar_agenda_active')),
                            TextInput::make('topbar_agenda_text.en')->label('Teks (EN)')->placeholder('Latest training & certification — see upcoming schedule')->maxLength(100)->visible(fn ($get) => (bool) $get('topbar_agenda_active')),
                            TextInput::make('topbar_agenda_cta.id')->label('Teks Tombol (ID)')->placeholder('Lihat Agenda')->maxLength(30)->visible(fn ($get) => (bool) $get('topbar_agenda_active')),
                            TextInput::make('topbar_agenda_cta.en')->label('Teks Tombol (EN)')->placeholder('View Agenda')->maxLength(30)->visible(fn ($get) => (bool) $get('topbar_agenda_active')),
                            TextInput::make('topbar_agenda_link')->label('Tautan (opsional)')->placeholder('kosongkan → halaman Agenda')->helperText('Kosongkan untuk mengarah ke halaman Agenda, atau isi path tanpa kode bahasa.')->maxLength(255)->visible(fn ($get) => (bool) $get('topbar_agenda_active'))->columnSpanFull(),
                        ])->columns(2)->visible(fn ($get) => (bool) $get('topbar_active')),
                ]),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $this->put('topbar_active', ! empty($data['topbar_active']) ? '1' : '0', 'text', 'topbar');
        $this->put('topbar_promo_active', ! empty($data['topbar_promo_active']) ? '1' : '0', 'text', 'topbar');
        $this->put('topbar_promo_text', json_encode($data['topbar_promo_text'] ?? ['id' => '', 'en' => '']), 'json', 'topbar');
        $this->put('topbar_promo_price_old', $data['topbar_promo_price_old'] ?? '', 'text', 'topbar');
        $this->put('topbar_promo_price_new', $data['topbar_promo_price_new'] ?? '', 'text', 'topbar');
        $this->put('topbar_promo_note', json_encode($data['topbar_promo_note'] ?? ['id' => '', 'en' => '']), 'json', 'topbar');
        $this->put('topbar_promo_cta', json_encode($data['topbar_promo_cta'] ?? ['id' => '', 'en' => '']), 'json', 'topbar');
        $this->put('topbar_promo_link', $data['topbar_promo_link'] ?? '', 'text', 'topbar');
        $this->put('topbar_agenda_active', ! empty($data['topbar_agenda_active']) ? '1' : '0', 'text', 'topbar');
        $this->put('topbar_agenda_text', json_encode($data['topbar_agenda_text'] ?? ['id' => '', 'en' => '']), 'json', 'topbar');
        $this->put('topbar_agenda_cta', json_encode($data['topbar_agenda_cta'] ?? ['id' => '', 'en' => '']), 'json', 'topbar');
        $this->put('topbar_agenda_link', $data['topbar_agenda_link'] ?? '', 'text', 'topbar');

        Notification::make()->title('Bar promo disimpan')->success()->send();
    }

    private function put(string $key, mixed $value, string $type, string $group): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
    }
}
