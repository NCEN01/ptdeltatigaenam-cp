<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Jobs\BroadcastServicePromoJob;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?int $navigationSort = 20;

    protected static ?string $accessPermission = 'manage_services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Layanan (ID/EN)')->schema([
                Forms\Components\Select::make('service_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'slug')->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record): string => (string) ($record->getTranslation('name', 'id', false) ?: $record->name))
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(220),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(220),
                Forms\Components\TextInput::make('short_description.id')->label('Deskripsi Singkat (ID)')->maxLength(255),
                Forms\Components\TextInput::make('short_description.en')->label('Deskripsi Singkat (EN)')->maxLength(255),
                Forms\Components\RichEditor::make('description.id')->label('Deskripsi (ID)')->columnSpanFull(),
                Forms\Components\RichEditor::make('description.en')->label('Deskripsi (EN)')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Harga & Detail')->schema([
                Forms\Components\TextInput::make('price')->label('Harga (IDR)')->numeric()->default(0)->prefix('Rp')
                    ->helperText('Harga asli yang ditagih. Isi 0 bila ingin menampilkan "Hubungi kami". Diskon di bawah hanya untuk tampilan, tidak mengubah harga ini.'),
                Forms\Components\Toggle::make('discount_active')->label('Tampilkan Diskon (tampilan saja)')->default(false)->live(),
                Forms\Components\TextInput::make('discount_original_price')->label('Harga Sebelum Diskon (dicoret)')->numeric()->prefix('Rp')
                    ->helperText('Harga "coret" yang lebih tinggi dari harga asli. Persentase "Hemat" dihitung otomatis.')
                    ->visible(fn (Forms\Get $get) => (bool) $get('discount_active'))
                    ->required(fn (Forms\Get $get) => (bool) $get('discount_active')),
                // Satuan harga tidak selalu "peserta": headhunter dan konsultasi
                // dijual per penugasan. Dikosongkan berarti tetap memakai "peserta".
                Forms\Components\TextInput::make('price_label.id')->label('Satuan Harga (ID)')
                    ->placeholder('peserta')->maxLength(40)
                    ->helperText('Tampil setelah garis miring, mis. "Rp 8.500.000 / peserta". Kosongkan untuk memakai "peserta".'),
                Forms\Components\TextInput::make('price_label.en')->label('Satuan Harga (EN)')
                    ->placeholder('person')->maxLength(40),
                Forms\Components\TextInput::make('duration')->label('Durasi')->placeholder('2 hari'),
                /* Satu lokasi dan satu cara mengikuti untuk seluruh angkatan.
                   Dulu keduanya juga diisi lagi di setiap jadwal, dengan nilai
                   yang di seluruh data tidak pernah berbeda dari yang di sini. */
                Forms\Components\TextInput::make('location')->label('Lokasi')->placeholder('Jakarta Selatan, atau Di lokasi klien')->maxLength(200),
                Forms\Components\Select::make('mode')->label('Cara Mengikuti')
                    ->options(['offline' => 'Offline (tatap muka)', 'online' => 'Online (daring)', 'hybrid' => 'Hybrid (bauran)'])
                    ->default('offline')->native(false),
                /* Kuota TIDAK diisi di sini: kursi habis per angkatan, jadi ia
                   diisi di tiap batch. Satu gambar untuk kartu dan kepala halaman
                   program — spanduk terpisah sudah dilepas. */
                MediaUpload::for('image', 'service', 'services')->label('Gambar Layanan')
                    ->helperText('Tampil di kartu layanan dan sebagai gambar utama di halaman program.')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_purchasable')->label('Dapat Dibeli Online')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                Forms\Components\Toggle::make('broadcast_promo_on_create')
                    ->label('Broadcast Email Promo ke Pelanggan setelah dibuat')
                    ->helperText('Jika dinyalakan (dan layanan berstatus Aktif), email promo layanan baru akan otomatis dimasukkan ke antrean (queue) untuk seluruh pelanggan aktif & terverifikasi.')
                    ->default(false)
                    ->live()
                    ->dehydrated(false)
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('broadcast_promo_message')
                    ->label('Pesan Tambahan Promo (Opsional)')
                    ->placeholder('Contoh: Dapatkan potongan harga early bird untuk pendaftaran minggu ini...')
                    ->rows(2)
                    ->maxLength(500)
                    ->dehydrated(false)
                    ->visible(fn (Forms\Get $get, string $operation): bool => $operation === 'create' && (bool) $get('broadcast_promo_on_create'))
                    ->columnSpanFull(),
            ])->columns(3),

            /* Batch dan jadwal TIDAK lagi diisi di form ini.

               Dulu jadwal adalah repeater di dalam form layanan, dan itu
               membawa dua masalah. Pertama, admin yang hanya ingin menambah
               satu layanan tanpa angkatan apa pun tetap dihadapkan pada blok
               jadwal yang terisi satu baris kosong. Kedua — dan ini yang
               merusak data — repeater itu memakai ->orderColumn('start_date'):
               Filament menyimpan NOMOR URUT item ke kolom yang ditunjuk, jadi
               setiap kali layanan disimpan, tanggal mulai seluruh jadwalnya
               tertimpa angka 1, 2, 3 dan tersimpan sebagai 1970-01-01.

               Sekarang batch punya tabelnya sendiri di bawah form ini
               (ServiceResource\RelationManagers\SchedulesRelationManager),
               dengan tombol "Tambah Batch" yang terpisah. Layanan bisa
               disimpan tanpa batch sama sekali. */
            Forms\Components\Section::make('Batch & Jadwal')
                ->schema([
                    Forms\Components\Placeholder::make('info_batch')
                        ->hiddenLabel()
                        ->content(fn (string $operation): string => $operation === 'create'
                            ? 'Layanan boleh disimpan tanpa batch. Bila program ini dibuka dalam beberapa angkatan (batch) dengan tanggal tertentu, simpan layanan ini dulu, lalu tambahkan batch-nya di tabel "Batch & Jadwal" yang muncul di bawah halaman sunting.'
                            : 'Batch dikelola di tabel "Batch & Jadwal" di bawah form ini. Lokasi, cara mengikuti, dan harga setiap batch mengikuti isian layanan di atas.'),
                ]),

            Forms\Components\Section::make('Kegiatan')->schema([
                Forms\Components\Repeater::make('activities')
                    ->relationship()
                    ->label('Daftar Kegiatan')
                    ->mutateRelationshipDataBeforeFillUsing(function (array $data): array {
                        if (empty($data['id'] ?? null)) {
                            return $data;
                        }
                        $record = \App\Models\ServiceActivity::find($data['id']);
                        if (! $record) {
                            return $data;
                        }
                        foreach (['title', 'description'] as $field) {
                            $data[$field] = $record->getTranslations($field);
                        }
                        return $data;
                    })
                    ->schema([
                        Forms\Components\TextInput::make('title.id')->label('Judul (ID)')->required(),
                        Forms\Components\TextInput::make('title.en')->label('Judul (EN)'),
                        Forms\Components\Textarea::make('description.id')->label('Deskripsi (ID)')->rows(2),
                        Forms\Components\Textarea::make('description.en')->label('Deskripsi (EN)')->rows(2),
                        Forms\Components\Hidden::make('sort_order')->default(0),
                    ])->columns(2)->collapsible()->defaultItems(0)
                    ->itemLabel(fn (array $state): ?string =>
                        is_string($state['title']['id'] ?? null) ? $state['title']['id']
                        : (is_string($state['title'] ?? null) ? $state['title']
                        : 'Kegiatan')
                    ),
            ])->collapsed(),

            Forms\Components\Section::make('Halaman Program')
                ->description('Isi bagian-bagian halaman rincian program: mentor pengampu dan benefit.')
                ->collapsed()
                ->schema([
                    Forms\Components\Select::make('mentors')
                        ->label('Mentor Pengampu')
                        ->relationship('mentors', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->helperText('Dipilih dari pustaka Mentor. Belum ada namanya di daftar? Tambahkan lebih dulu di menu Layanan → Mentor.')
                        ->columnSpanFull(),

                    Forms\Components\Repeater::make('benefits')
                        ->relationship()
                        ->label('Benefit Khusus Program Ini')
                        ->helperText('Biarkan kosong agar program ini memakai daftar Benefit Bawaan. Begitu diisi satu baris saja, daftar bawaan tidak lagi dipakai untuk program ini — jadi isi selengkapnya atau jangan sama sekali.')
                        ->schema([
                            Forms\Components\TextInput::make('title.id')->label('Judul (ID)')->required()->maxLength(200),
                            Forms\Components\TextInput::make('title.en')->label('Judul (EN)')->maxLength(200),
                            Forms\Components\Textarea::make('description.id')->label('Keterangan (ID)')->rows(2),
                            Forms\Components\Textarea::make('description.en')->label('Keterangan (EN)')->rows(2),
                            Forms\Components\Select::make('icon')
                                ->label('Ikon')
                                ->options(\App\Filament\Resources\ProgramBenefitResource::iconOptions())
                                ->searchable()
                                ->native(false),
                            Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->reorderable('sort_order')
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => is_string($state['title']['id'] ?? null)
                            ? $state['title']['id']
                            : 'Benefit')
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('SEO (ID/EN)')->collapsed()->schema([
                Forms\Components\TextInput::make('meta_title.id')->label('Meta Title (ID)')->maxLength(200),
                Forms\Components\TextInput::make('meta_title.en')->label('Meta Title (EN)')->maxLength(200),
                Forms\Components\Textarea::make('meta_description.id')->label('Meta Description (ID)')->rows(2),
                Forms\Components\Textarea::make('meta_description.en')->label('Meta Description (EN)')->rows(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->disk('public')->square()->label(''),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->sortable()->wrap(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
                Tables\Columns\TextColumn::make('price')->label('Harga')->money('IDR')->sortable(),
                /* Jumlah batch aktif, dan sisa kursi batch terdekat. Kuota kini
                   milik batch, bukan layanan, jadi angkanya dibaca dari sana. */
                Tables\Columns\TextColumn::make('schedules_count')
                    ->label('Batch')
                    ->counts(['schedules' => fn (\Illuminate\Database\Eloquent\Builder $q) => $q->where('is_active', true)])
                    ->sortable(),
                Tables\Columns\TextColumn::make('available_seats')
                    ->label('Sisa Kursi (batch terdekat)')
                    ->state(fn (Service $record): string => $record->available_seats !== null ? (string) $record->available_seats : '-'),
                Tables\Columns\IconColumn::make('is_purchasable')->label('Online')->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->action(
                        Tables\Actions\Action::make('toggleActive')
                            ->requiresConfirmation()
                            ->modalHeading('Ubah Status Aktif')
                            ->modalDescription('Apakah Anda yakin ingin mengubah status aktif layanan ini?')
                            ->action(fn (Service $record) => $record->update(['is_active' => !$record->is_active]))
                    ),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_category_id')->relationship('category', 'slug')->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record): string => (string) ($record->getTranslation('name', 'id', false) ?: $record->name))->label('Kategori'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\Action::make('broadcastPromo')
                    ->label('Broadcast Promo')
                    ->icon('heroicon-o-megaphone')
                    ->color('gold')
                    ->requiresConfirmation()
                    ->modalHeading('Broadcast Email Promo Layanan')
                    ->modalDescription(fn (Service $record): string => 'Email promo untuk "' . ($record->getTranslation('title', 'id', false) ?: $record->title) . '" akan dikirim melalui antrean (queue) ke seluruh pelanggan aktif yang emailnya sudah terverifikasi.')
                    ->form([
                        Forms\Components\Textarea::make('custom_message')
                            ->label('Pesan Tambahan Promo (Opsional)')
                            ->placeholder('Contoh: Kuota angkatan pertama terbatas, daftar sebelum harga promo berakhir.')
                            ->rows(3)
                            ->maxLength(500),
                    ])
                    ->action(function (Service $record, array $data): void {
                        if (! $record->is_active) {
                            Notification::make()
                                ->title('Layanan masih berstatus nonaktif/draft')
                                ->body('Aktifkan layanan terlebih dahulu sebelum mengirim broadcast email promo.')
                                ->warning()
                                ->send();

                            return;
                        }

                        BroadcastServicePromoJob::dispatch(
                            $record->id,
                            $data['custom_message'] ?? null,
                            auth()->id(),
                        );

                        Notification::make()
                            ->title('Broadcast promo masuk antrean')
                            ->body('Email promo untuk "' . ($record->getTranslation('title', 'id', false) ?: $record->title) . '" sedang diproses di antrean queue.')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\SchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}