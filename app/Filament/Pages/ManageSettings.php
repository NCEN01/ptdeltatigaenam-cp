<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
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

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    /* shouldRegisterNavigation() yang mengembalikan false dilepas dari sini.
       Halaman ini tidak pernah tampil di sidebar, padahal isinya masih
       dipakai halaman depan dan tidak dipindahkan ke mana pun: alamat surel
       dan telepon di footer, naskah Tentang dan Visi di halaman Tentang,
       intro Kemitraan, judul seksi Lembaga Mitra, dan ajakan Konsultasi
       Program di setiap halaman program.

       Yang dipindah keluar dulu hanya sebagiannya — bar promo, statistik, dan
       agenda halaman Layanan kini punya halamannya sendiri — tetapi sisanya
       tetap di sini tanpa jalan masuk. Satu-satunya cara admin mencapainya
       adalah mengetahui dan mengetik sendiri alamat /d36-panel/manage-settings,
       dan tidak ada apa pun di panel yang memberi tahu alamat itu. */

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
            'program_consult_title' => Setting::get('program_consult_title') ?: ['id' => '', 'en' => ''],
            'program_consult_desc' => Setting::get('program_consult_desc') ?: ['id' => '', 'en' => ''],
            'program_consult_cta' => Setting::get('program_consult_cta') ?: ['id' => '', 'en' => ''],
            'program_consult_whatsapp' => Setting::get('program_consult_whatsapp'),
            'company_profile_file' => Setting::get('company_profile_file'),
            'site_phone_tel' => Setting::get('site_phone_tel'),
            'site_whatsapp' => Setting::get('site_whatsapp'),
            'site_whatsapp_label' => Setting::get('site_whatsapp_label'),
            'site_hours' => Setting::get('site_hours') ?: ['id' => '', 'en' => ''],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Profil Perusahaan')->schema([
                TextInput::make('site_name')->label('Nama Situs')->required()->maxLength(100),
                TextInput::make('linkedin_url')->label('LinkedIn')->url()->nullable()->maxLength(255),
            ])->columns(2),

            Section::make('Kontak')
                ->description('Dipakai bersama oleh kartu kontak di halaman Kontak dan kolom Kontak di footer. Sebelumnya keduanya menuliskannya sendiri-sendiri, sehingga satu nomor yang berganti harus disunting di dua berkas.')
                ->schema([
                    TextInput::make('site_email')->label('Email')->email()->required()->maxLength(255)
                        ->placeholder('info@deltatigaenam.com'),

                    /* Jam operasional dwibahasa: ia kalimat, bukan angka, dan
                       "Senin sampai Jumat" tidak berarti apa-apa di versi
                       Inggris situs ini. */
                    TextInput::make('site_hours.id')->label('Jam Operasional (ID)')->maxLength(80)
                        ->placeholder('Senin sampai Jumat · 08.00-17.00'),
                    TextInput::make('site_hours.en')->label('Jam Operasional (EN)')->maxLength(80)
                        ->placeholder('Mon to Fri · 8am-5pm'),

                    /* Nomor telepon disimpan dua rupa dengan sengaja. Yang
                       tampil ditulis untuk dibaca manusia; tautan tel:
                       menuntut bentuk hanya-angka berkode negara. Menurunkan
                       yang kedua dari yang pertama tidak bisa diandalkan —
                       kode wilayah 021 harus kehilangan nol depannya dan
                       mendapat +62, aturan yang berbeda untuk nomor seluler. */
                    TextInput::make('site_phone')->label('Telepon (tampil)')->required()->maxLength(30)
                        ->placeholder('(021) 5890 5002'),
                    TextInput::make('site_phone_tel')->label('Telepon (untuk tautan panggil)')->maxLength(30)
                        ->placeholder('+622158905002')
                        ->helperText('Hanya angka dengan kode negara. Dipakai tombol panggil di ponsel.'),

                    TextInput::make('site_whatsapp_label')->label('WhatsApp (tampil)')->maxLength(30)
                        ->placeholder('0818 834 766'),
                    TextInput::make('site_whatsapp')->label('WhatsApp (untuk tautan chat)')->maxLength(30)
                        ->placeholder('62818834766')
                        ->helperText('Diawali 62, tanpa tanda plus dan tanpa spasi.'),
                ])->columns(2),

            Section::make('Dokumen Profil Perusahaan (PDF)')
                ->description('Berkas yang diunduh pengunjung dari kartu "Profil Perusahaan" di halaman Tentang. Kosongkan bila belum ada; kartunya otomatis tidak ditampilkan.')
                ->schema([
                    /* Sebelumnya berkas ini dibaca langsung dari
                       public/documents/company-profile.pdf, nama yang dipatok
                       di dalam Blade. Memperbaruinya menuntut akses FTP atau
                       akses ke peladen, dan kalau berkasnya belum ada, halaman
                       Tentang tetap menampilkan kartu beserta tombol unduhnya
                       yang menjawab 404 — ukuran berkasnya saja yang berubah
                       dari "2,3 MB · PDF" menjadi "PDF".

                       Satu berkas saja: FileUpload tanpa ->multiple() memang
                       menyimpan satu nilai, dan yang baru menggantikan yang
                       lama. PDF saja karena ini dokumen untuk dibaca, bukan
                       diolah — dan PDF satu-satunya yang terbuka langsung di
                       peramban tanpa mengunduh apa pun lebih dulu. */
                    FileUpload::make('company_profile_file')
                        ->label('Berkas profil perusahaan')
                        ->disk('public')
                        ->directory('documents')
                        ->acceptedFileTypes(['application/pdf'])
                        ->maxSize(20480)
                        ->helperText('Format PDF saja, maksimal 20 MB. Satu berkas — mengunggah yang baru menggantikan yang lama.')
                        ->downloadable()
                        ->openable()
                        ->columnSpanFull(),
                ]),

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

            Section::make('Konsultasi Program Gratis')
                ->description('Ajakan berkonsultasi di bagian bawah setiap halaman program. Satu teks ini dipakai semua program, jadi mengubahnya sekali berlaku di mana-mana.')
                ->schema([
                    TextInput::make('program_consult_title.id')->label('Judul (ID)')->nullable()->maxLength(160)
                        ->placeholder('Konsultasi Program Gratis')
                        ->helperText('Kosongkan untuk memakai teks bawaan.'),
                    TextInput::make('program_consult_title.en')->label('Judul (EN)')->nullable()->maxLength(160),
                    Textarea::make('program_consult_desc.id')->label('Keterangan (ID)')->nullable()->maxLength(600)->rows(2)
                        ->placeholder('Punya pertanyaan dan ingin berbicara langsung dengan tim kami?'),
                    Textarea::make('program_consult_desc.en')->label('Keterangan (EN)')->nullable()->maxLength(600)->rows(2),
                    TextInput::make('program_consult_cta.id')->label('Teks Tombol (ID)')->nullable()->maxLength(60)
                        ->placeholder('Konsultasi Sekarang'),
                    TextInput::make('program_consult_cta.en')->label('Teks Tombol (EN)')->nullable()->maxLength(60),
                    TextInput::make('program_consult_whatsapp')->label('Nomor WhatsApp')->nullable()->maxLength(30)
                        ->placeholder('62818834766')
                        ->helperText('Diawali 62, tanpa tanda plus dan tanpa spasi. Dikosongkan berarti tombolnya menuju halaman Kontak.'),
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
        $this->put('site_phone_tel', $data['site_phone_tel'] ?? null, 'text', 'general');
        $this->put('site_whatsapp', $data['site_whatsapp'] ?? null, 'text', 'general');
        $this->put('site_whatsapp_label', $data['site_whatsapp_label'] ?? null, 'text', 'general');
        $this->put('site_hours', json_encode($data['site_hours'] ?? []), 'json', 'general');
        $this->put('company_tagline', json_encode($data['company_tagline']), 'json', 'general');
        $this->put('company_about', json_encode($data['company_about']), 'json', 'general');
        $this->put('company_vision', json_encode($data['company_vision']), 'json', 'general');
        $this->put('partnership_intro', json_encode($data['partnership_intro']), 'json', 'partnership');
        $this->put('partnership_partners_title', json_encode($data['partnership_partners_title']), 'json', 'partnership');
        $this->put('partnership_partners_desc', json_encode($data['partnership_partners_desc']), 'json', 'partnership');

        $this->put('program_consult_title', json_encode($data['program_consult_title'] ?? []), 'json', 'services');
        $this->put('program_consult_desc', json_encode($data['program_consult_desc'] ?? []), 'json', 'services');
        $this->put('program_consult_cta', json_encode($data['program_consult_cta'] ?? []), 'json', 'services');
        $this->put('program_consult_whatsapp', $data['program_consult_whatsapp'] ?? null, 'text', 'services');

        /* FileUpload mengembalikan array saat berkasnya baru diunggah dan
           string saat tidak disentuh — pola yang sama ditangani
           ManageServiceAgenda untuk berkas jadwal. */
        $berkas = $data['company_profile_file'] ?? null;
        $this->put('company_profile_file', is_array($berkas) ? (reset($berkas) ?: null) : $berkas, 'text', 'general');

        Notification::make()->title('Pengaturan disimpan')->success()->send();
    }

    private function put(string $key, mixed $value, string $type, string $group): void
    {
        Setting::updateOrCreate(['key' => $key], ['value' => $value, 'type' => $type, 'group' => $group]);
    }
}