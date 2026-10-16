<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Pengaturan seksi "Agenda Mendatang" di halaman Layanan.
 *
 * Kartu agendanya sendiri tidak disunting di sini: ia dibaca dari menu Agenda
 * yang sudah ada, jadi satu agenda cukup dimasukkan sekali dan muncul di
 * mana pun ia ditampilkan. Yang diatur di halaman ini hanya hal yang khusus
 * milik seksi tersebut — kata-katanya, berapa kartu yang tampil, dan berkas
 * jadwal yang bisa diunduh pengunjung.
 */
class ManageServiceAgenda extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Agenda di Halaman Layanan';

    protected static ?string $title = 'Agenda di Halaman Layanan';

    protected static ?int $navigationSort = 50;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->can('manage_settings');
    }

    public function mount(): void
    {
        $this->form->fill([
            'service_agenda_active' => (bool) Setting::get('service_agenda_active', true),
            'service_agenda_title' => Setting::get('service_agenda_title') ?: ['id' => 'Agenda Mendatang', 'en' => 'Upcoming Agenda'],
            'service_agenda_intro' => Setting::get('service_agenda_intro') ?: [
                'id' => 'Jadwal kelas dan uji kompetensi yang sudah terbuka pendaftarannya.',
                'en' => 'Classes and competence assessments already open for registration.',
            ],
            'service_agenda_limit' => (string) Setting::get('service_agenda_limit', '6'),
            'service_agenda_show_past' => (bool) Setting::get('service_agenda_show_past', true),
            'service_agenda_file' => Setting::get('service_agenda_file'),
            'service_agenda_file_label' => Setting::get('service_agenda_file_label') ?: [
                'id' => 'Jadwal lengkap',
                'en' => 'Full schedule',
            ],
            'service_agenda_file_note' => Setting::get('service_agenda_file_note') ?: [
                'id' => 'Berisi seluruh jadwal pelatihan dan uji kompetensi periode berjalan.',
                'en' => 'Contains every training and assessment date for the current period.',
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Tampilkan seksi')
                ->description('Seksi ini muncul di halaman Layanan, tepat di bawah "Tiga cara mengikuti program".')
                ->schema([
                    Toggle::make('service_agenda_active')
                        ->label('Tampilkan seksi Agenda di halaman Layanan')
                        ->helperText('Matikan untuk menyembunyikan seluruh seksi, termasuk berkas jadwalnya.')
                        ->columnSpanFull(),
                ]),

            Section::make('Judul & pengantar')
                ->schema([
                    TextInput::make('service_agenda_title.id')->label('Judul (ID)')->required()->maxLength(80),
                    TextInput::make('service_agenda_title.en')->label('Judul (EN)')->maxLength(80),
                    Textarea::make('service_agenda_intro.id')->label('Pengantar (ID)')->rows(2)->maxLength(200),
                    Textarea::make('service_agenda_intro.en')->label('Pengantar (EN)')->rows(2)->maxLength(200),
                ])->columns(2),

            Section::make('Kartu agenda')
                ->description('Isi kartunya diambil dari menu Agenda. Tambah atau sunting agendanya di sana; yang diatur di sini hanya berapa banyak yang tampil. Hanya agenda berstatus "published" yang muncul. Kartunya bisa digeser ke samping, jadi jumlah yang banyak tetap muat tanpa memanjangkan halaman.')
                ->schema([
                    TextInput::make('service_agenda_limit')
                        ->label('Jumlah kartu yang ditampilkan')
                        ->numeric()->minValue(1)->maxValue(24)->required()
                        ->helperText('Urutannya: yang akan datang lebih dulu, paling dekat tanggalnya paling depan, lalu yang sudah berakhir. Tiga kartu terlihat sekaligus di layar lebar, sisanya digeser.'),
                    Toggle::make('service_agenda_show_past')
                        ->label('Tampilkan juga agenda yang sudah berakhir')
                        ->helperText('Ditandai abu-abu dengan gambar meredup dan tanpa tombol pendaftaran. Berguna sebagai bukti kelasnya memang berjalan, tapi di periode sepi ia bisa mendominasi daftarnya.')
                        ->columnSpanFull(),
                ])->columns(1),

            Section::make('Berkas jadwal (PDF)')
                ->description('Satu berkas PDF. Pengunjung dapat membuka atau mengunduhnya untuk melihat jadwal lengkapnya. Kosongkan bila belum ada; tombolnya otomatis tidak ditampilkan.')
                ->schema([
                    /* Hanya PDF. Sebelumnya XLS dan XLSX juga diterima, dan
                       keduanya membawa dua persoalan yang tidak terlihat di
                       panel: berkas Excel tidak bisa dibuka langsung di
                       peramban sehingga tombol "Lihat" di halaman Layanan
                       hanya mengunduhnya diam-diam, dan jadwal yang dibagikan
                       sebagai lembar kerja ikut membawa rumus serta lembar
                       tersembunyi yang tidak pernah dimaksudkan untuk dibaca
                       publik.

                       Satu berkas, bukan beberapa: FileUpload tanpa
                       ->multiple() memang menyimpan satu nilai, dan
                       ServiceController::agendaFile() membaca tepat satu
                       pengaturan. Berkas baru menggantikan yang lama. */
                    FileUpload::make('service_agenda_file')
                        ->label('Berkas jadwal (PDF)')
                        ->disk('public')
                        ->directory('agenda')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(10240)
                        ->helperText('Format PDF saja, maksimal 10 MB. Satu berkas — mengunggah yang baru menggantikan yang lama.')
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                    TextInput::make('service_agenda_file_label.id')->label('Teks tombol (ID)')->maxLength(60),
                    TextInput::make('service_agenda_file_label.en')->label('Teks tombol (EN)')->maxLength(60),
                    Textarea::make('service_agenda_file_note.id')->label('Keterangan berkas (ID)')->rows(2)->maxLength(160),
                    Textarea::make('service_agenda_file_note.en')->label('Keterangan berkas (EN)')->rows(2)->maxLength(160),
                ])->columns(2),
        ])->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $put = function (string $key, mixed $value, string $type) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $type === 'json' ? json_encode($value) : $value, 'type' => $type, 'group' => 'service_agenda'],
            );
        };

        $put('service_agenda_active', ! empty($data['service_agenda_active']) ? '1' : '0', 'text');
        $put('service_agenda_limit', (string) ($data['service_agenda_limit'] ?? 6), 'text');
        $put('service_agenda_show_past', ! empty($data['service_agenda_show_past']) ? '1' : '0', 'text');

        // FileUpload mengembalikan array saat berkasnya baru diunggah dan string
        // saat tidak diubah; keduanya diratakan jadi satu jalur berkas.
        $file = $data['service_agenda_file'] ?? null;
        $put('service_agenda_file', is_array($file) ? (reset($file) ?: null) : $file, 'text');

        foreach (['service_agenda_title', 'service_agenda_intro', 'service_agenda_file_label', 'service_agenda_file_note'] as $key) {
            $put($key, $data[$key] ?? ['id' => '', 'en' => ''], 'json');
        }

        Notification::make()->title('Pengaturan agenda disimpan')->success()->send();
    }
}
