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

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Agenda di Halaman Layanan';

    protected static ?string $title = 'Agenda di Halaman Layanan';

    protected static ?int $navigationSort = 96;

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
            'service_agenda_limit' => (string) Setting::get('service_agenda_limit', '3'),
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
                ->description('Isi kartunya diambil dari menu Agenda. Tambah atau sunting agendanya di sana; yang diatur di sini hanya berapa banyak yang tampil. Hanya agenda berstatus "published" yang muncul, dan yang paling dekat tanggalnya didahulukan.')
                ->schema([
                    TextInput::make('service_agenda_limit')
                        ->label('Jumlah kartu yang ditampilkan')
                        ->numeric()->minValue(1)->maxValue(12)->required()
                        ->helperText('3 atau 6 paling rapi, karena kartunya tersusun tiga per baris.'),
                ]),

            Section::make('Berkas jadwal (PDF atau Excel)')
                ->description('Pengunjung dapat membuka atau mengunduh berkas ini untuk melihat jadwal lengkapnya. Kosongkan bila belum ada; tombolnya otomatis tidak ditampilkan.')
                ->schema([
                    FileUpload::make('service_agenda_file')
                        ->label('Berkas jadwal')
                        ->disk('public')
                        ->directory('agenda')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->maxSize(10240)
                        ->helperText('Format PDF, XLS, atau XLSX. Maksimal 10 MB.')
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
        $put('service_agenda_limit', (string) ($data['service_agenda_limit'] ?? 3), 'text');

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
