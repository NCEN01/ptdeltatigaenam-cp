<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    use Translatable;
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
            Forms\Components\Section::make('Informasi Layanan')->schema([
                Forms\Components\Select::make('service_category_id')
                    ->label('Kategori')
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->searchable()->preload()->required(),
                Forms\Components\TextInput::make('title')->label('Judul')->required()->maxLength(220)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                Forms\Components\TextInput::make('slug')->required()->maxLength(220)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('short_description')->label('Deskripsi Singkat')->maxLength(255),
                Forms\Components\RichEditor::make('description')->label('Deskripsi')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Harga & Detail')->schema([
                Forms\Components\TextInput::make('price')->label('Harga (IDR)')->numeric()->default(0)->prefix('Rp'),
                Forms\Components\TextInput::make('price_label')->label('Label Harga')->placeholder('per peserta'),
                Forms\Components\TextInput::make('duration')->label('Durasi')->placeholder('2 hari'),
                Forms\Components\TextInput::make('location')->label('Lokasi'),
                MediaUpload::for('image', 'service', 'services')->label('Gambar Layanan')->columnSpanFull(),
                Forms\Components\Toggle::make('is_purchasable')->label('Dapat Dibeli Online')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            ])->columns(3),

            Forms\Components\Section::make('Kegiatan')->schema([
                Forms\Components\Repeater::make('activities')
                    ->relationship()
                    ->label('Daftar Kegiatan')
                    ->schema([
                        Forms\Components\TextInput::make('title.id')->label('Judul (ID)')->required(),
                        Forms\Components\TextInput::make('title.en')->label('Judul (EN)'),
                        Forms\Components\Textarea::make('description.id')->label('Deskripsi (ID)')->rows(2),
                        Forms\Components\Textarea::make('description.en')->label('Deskripsi (EN)')->rows(2),
                        Forms\Components\Hidden::make('sort_order')->default(0),
                    ])->columns(2)->collapsible()->defaultItems(0)
                    ->itemLabel(fn (array $state): ?string => $state['title']['id'] ?? 'Kegiatan'),
            ])->collapsed(),

            Forms\Components\Section::make('Jadwal / Batch')->schema([
                Forms\Components\Repeater::make('schedules')
                    ->relationship()
                    ->label('Daftar Jadwal')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')->label('Mulai')->required(),
                        Forms\Components\DatePicker::make('end_date')->label('Selesai'),
                        Forms\Components\TimePicker::make('start_time')->label('Jam Mulai')->seconds(false),
                        Forms\Components\TimePicker::make('end_time')->label('Jam Selesai')->seconds(false),
                        Forms\Components\TextInput::make('location')->label('Lokasi'),
                        Forms\Components\Select::make('mode')->options(['offline' => 'Offline', 'online' => 'Online', 'hybrid' => 'Hybrid'])->default('offline'),
                        Forms\Components\TextInput::make('quota')->label('Kuota')->numeric(),
                        Forms\Components\TextInput::make('price_override')->label('Harga Khusus')->numeric()->prefix('Rp'),
                        Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                    ])->columns(3)->collapsible()->defaultItems(0),
            ])->collapsed(),

            Forms\Components\Section::make('SEO')->collapsed()->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title'),
                Forms\Components\Textarea::make('meta_description')->label('Meta Description')->rows(2),
            ]),
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
                Tables\Columns\IconColumn::make('is_purchasable')->label('Online')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
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
