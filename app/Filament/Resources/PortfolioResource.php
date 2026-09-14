<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\PortfolioResource\Pages;
use App\Models\Portfolio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Portofolio';

    protected static ?string $modelLabel = 'Portofolio';

    protected static ?string $accessPermission = 'manage_portfolio';

    public static function form(Form $form): Form
    {
        // Dikelompokkan mengikuti pola BlogPostResource (Konten -> Media -> Detail),
        // menggantikan satu seksi berisi 13 field campur aduk. Editor konten diberi
        // lebar penuh; sebelumnya terjepit di satu kolom.
        return $form->schema([
            Forms\Components\Section::make('Proyek (ID/EN)')->schema([
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(280)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(280),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(280)
                    ->unique(ignoreRecord: true)
                    ->helperText('Terisi otomatis dari Judul (ID) saat membuat baru.')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('short_description.id')->label('Deskripsi Singkat (ID)')->rows(3),
                Forms\Components\Textarea::make('short_description.en')->label('Deskripsi Singkat (EN)')->rows(3),
                Forms\Components\RichEditor::make('content.id')->label('Konten (ID)')->columnSpanFull(),
                Forms\Components\RichEditor::make('content.en')->label('Konten (EN)')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Media')->schema([
                MediaUpload::for('cover_image', 'portfolio', 'portfolio')->label('Cover')->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Detail Proyek')->schema([
                Forms\Components\TextInput::make('client_name')->label('Nama Klien')->maxLength(200),
                Forms\Components\TextInput::make('location')
                    ->label('Lokasi')
                    ->maxLength(160)
                    ->placeholder('Jakarta Selatan, Indonesia')
                    ->helperText('Tampil di kartu portofolio dan halaman detailnya.'),
                Forms\Components\Select::make('service_category_id')
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->preload()
                    ->searchable()
                    ->label('Kategori'),
                Forms\Components\DatePicker::make('project_date')->label('Tanggal Proyek'),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->helperText('Nonaktif berarti proyek ini tidak tampil di situs.')
                    ->columnSpanFull(),
            ])->columns(2),

            // Bukan lagi "Galeri": di situs bagian ini bernama "Dokumentasi Kegiatan",
            // jadi nama di CMS dibuat sama persis supaya admin tahu apa yang ia sunting.
            Forms\Components\Section::make('Dokumentasi Kegiatan')
                ->description('Foto-foto pelaksanaan kegiatan. Tampil di bagian bawah halaman portofolio ini. Boleh dikosongkan bila belum ada dokumentasi.')
                ->schema([
                    Forms\Components\Repeater::make('images')
                        ->relationship()
                        ->label('Daftar Foto')
                        ->addActionLabel('Tambah Foto Dokumentasi')
                        // Tanpa ini setiap baris yang dilipat hanya bertuliskan "Images",
                        // sehingga admin harus membuka satu per satu untuk tahu isinya.
                        ->itemLabel(fn (array $state): string => filled($state['caption']['id'] ?? null)
                            ? Str::limit($state['caption']['id'], 60)
                            : 'Foto tanpa keterangan')
                        // Relasi images() sudah orderBy('sort_order'), tetapi repeater tidak
                        // pernah menuliskan kolom itu — semua foto tersimpan sort_order 0 dan
                        // urutan hasil seretan tidak bertahan. orderColumn() menyimpannya.
                        ->orderColumn('sort_order')
                        ->reorderableWithButtons()
                        ->schema([
                            MediaUpload::for('image', 'portfolio', 'portfolio/gallery')->label('Foto')->required(),
                            Forms\Components\TextInput::make('caption.id')
                                ->label('Keterangan (ID)')
                                ->maxLength(200)
                                ->placeholder('Sesi diskusi kelompok, hari ke-2')
                                ->helperText('Keterangan singkat di bawah foto. Boleh dikosongkan.'),
                            Forms\Components\TextInput::make('caption.en')
                                ->label('Keterangan (EN)')
                                ->maxLength(200),
                        ])->columns(3)->collapsible()->defaultItems(0),
                ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('project_date', 'desc')->columns([
            Tables\Columns\ImageColumn::make('cover_image')->disk('public')->label('')->size(60),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('client_name')->label('Klien'),
            Tables\Columns\TextColumn::make('location')->label('Lokasi')->icon('heroicon-m-map-pin')
                ->placeholder('—')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}