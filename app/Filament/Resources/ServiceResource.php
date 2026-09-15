<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?int $navigationSort = 2;

    protected static ?string $accessPermission = 'manage_services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Layanan (ID/EN)')->schema([
                Forms\Components\Select::make('service_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(220)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(220),
                Forms\Components\TextInput::make('slug')->required()->maxLength(220)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('short_description.id')->label('Deskripsi Singkat (ID)')->maxLength(255),
                Forms\Components\TextInput::make('short_description.en')->label('Deskripsi Singkat (EN)')->maxLength(255),
                Forms\Components\RichEditor::make('description.id')->label('Deskripsi (ID)')->columnSpanFull(),
                Forms\Components\RichEditor::make('description.en')->label('Deskripsi (EN)')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Harga & Detail')->schema([
                Forms\Components\TextInput::make('price')->label('Harga (IDR)')->numeric()->default(0)->prefix('Rp')
                    ->helperText('Harga asli yang ditagih. Isi 0 bila ingin menampilkan "Hubungi kami". Diskon di bawah hanya untuk tampilan — tidak mengubah harga ini.'),
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
                Forms\Components\TextInput::make('location')->label('Lokasi')->placeholder('Cilegon'),
                Forms\Components\Select::make('mode')->options(['offline' => 'Offline', 'online' => 'Online', 'hybrid' => 'Hybrid'])->default('offline'),
                Forms\Components\TextInput::make('quota')->label('Kuota Peserta')->numeric(),
                MediaUpload::for('image', 'service', 'services')->label('Gambar Layanan')->columnSpanFull(),
                Forms\Components\Toggle::make('is_purchasable')->label('Dapat Dibeli Online')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(3),

            // Lima field di bawah ini tampil di situs tetapi sebelumnya tidak bisa
            // diisi admin sama sekali: lokasi, cara mengikuti, kuota, kursi terisi,
            // dan harga khusus batch. Nilainya hanya bisa masuk lewat seeder.
            //
            // maxItems(1)->addable(false) juga dilepas. Tujuh layanan sudah punya
            // tiga jadwal, sedangkan situs menampilkan seluruhnya; dengan batas satu
            // item admin tidak dapat menambah angkatan baru, dan membuka layanan
            // berjadwal tiga lalu menyimpannya berbenturan dengan batas itu.
            Forms\Components\Section::make('Jadwal / Batch')->schema([
                Forms\Components\Repeater::make('schedules')->relationship()->label('Jadwal')
                    ->addActionLabel('Tambah Jadwal')
                    // Tanpa ini tiap baris yang dilipat hanya bertuliskan "Schedules".
                    ->itemLabel(fn (array $state): string => filled($state['start_date'] ?? null)
                        ? \Illuminate\Support\Carbon::parse($state['start_date'])->translatedFormat('d M Y')
                        : 'Jadwal baru')
                    ->orderColumn('start_date')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')->label('Mulai')->required(),
                        Forms\Components\DatePicker::make('end_date')->label('Selesai'),
                        Forms\Components\Select::make('mode')
                            ->label('Cara Mengikuti')
                            ->options(['offline' => 'Offline', 'online' => 'Online', 'hybrid' => 'Hybrid'])
                            ->default('offline')
                            ->helperText('Tampil sebagai label pada kartu jadwal di situs.'),
                        Forms\Components\TimePicker::make('start_time')->label('Jam Mulai')->seconds(false),
                        Forms\Components\TimePicker::make('end_time')->label('Jam Selesai')->seconds(false),
                        Forms\Components\TextInput::make('location')->label('Lokasi')->maxLength(200)
                            ->placeholder('Jakarta Selatan, atau Di lokasi klien'),
                        Forms\Components\TextInput::make('quota')->label('Kuota Peserta')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('seats_taken')->label('Kursi Terisi')->numeric()->minValue(0)->default(0)
                            ->helperText('Sisa kursi dan bilah keterisian di situs dihitung dari sini.'),
                        Forms\Components\TextInput::make('price_override')->label('Harga Khusus Batch (IDR)')->numeric()->prefix('Rp')
                            ->helperText('Kosongkan untuk memakai harga layanan.'),
                        Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true)
                            ->helperText('Nonaktif berarti jadwal ini tidak tampil di situs.'),
                    ])->columns(3)->collapsible()->defaultItems(1),
            ])->collapsed(),

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
                Tables\Columns\TextColumn::make('available_seats')
                    ->label('Stok Tersedia')
                    ->sortable(query: function (\Illuminate\Database\Eloquent\Builder $query, string $direction) {
                        $query->orderByRaw('CASE WHEN quota IS NULL THEN 1 ELSE 0 END ' . $direction)
                              ->orderByRaw('(quota - seats_taken) ' . $direction);
                    })
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
                Tables\Filters\SelectFilter::make('service_category_id')->relationship('category', 'slug')->label('Kategori'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
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