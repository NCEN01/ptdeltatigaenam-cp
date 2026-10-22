<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationLabel = 'Artikel Blog';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $accessPermission = 'manage_blog';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Artikel (ID/EN)')->schema([
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(280),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(280),
                Forms\Components\Textarea::make('excerpt.id')->label('Ringkasan (ID)')->rows(2),
                Forms\Components\Textarea::make('excerpt.en')->label('Ringkasan (EN)')->rows(2),
                Forms\Components\RichEditor::make('content.id')->label('Konten (ID)')->columnSpanFull()
                    ->helperText('Judul bagian memakai Heading 2 / Heading 3 akan otomatis masuk ke Daftar Isi artikel. Baris yang seluruhnya ditebalkan juga dianggap judul bagian.'),
                Forms\Components\RichEditor::make('content.en')->label('Konten (EN)')->columnSpanFull()
                    ->helperText('Setiap Heading 2 menjadi satu butir daftar isi artikel.'),
            ])->columns(2),

            /* Satu gambar untuk kartu blog sekaligus kepala halaman artikel.
               Kolom "Banner Header" yang dulu ada di sini tidak pernah dibaca
               halaman mana pun — admin mengunggah dua gambar, hanya satu yang
               tampil. */
            Forms\Components\Section::make('Gambar')->schema([
                MediaUpload::for('featured_image', 'blog', 'blog')
                    ->label('Gambar Artikel')
                    ->helperText('Tampil di kartu daftar blog dan di bagian atas halaman artikel.')
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Publikasi')->schema([
                Forms\Components\Select::make('blog_category_id')
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record): string => (string) ($record->getTranslation('name', 'id', false) ?: $record->name))
                    ->searchable()->preload()->label('Kategori'),

                /* Penulis yang tercantum di akhir artikel. Diketik bebas, bukan
                   dipilih dari akun admin: yang menulis tidak selalu orang yang
                   memegang akun panel. */
                Forms\Components\TextInput::make('author_name')
                    ->label('Ditulis oleh')
                    ->maxLength(120)
                    ->placeholder('Tim PT Delta Tiga Enam')
                    ->helperText('Mis. "Admin" atau "Dani Ramdani, S.Psi.". Kosongkan untuk memakai "Tim PT Delta Tiga Enam".'),

                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip',
                ])->default('draft')->required(),
                Forms\Components\DateTimePicker::make('published_at')->label('Tanggal Terbit'),

                /* Lokasi bersifat pilihan. Tidak setiap artikel terikat tempat —
                   tulisan tentang cara menyusun KPI tidak terjadi "di" mana pun.

                   Saklarnya tidak punya kolom sendiri: keadaannya dibaca dari
                   apakah lokasi terisi, dan dimatikan berarti lokasinya
                   dikosongkan saat disimpan. dehydratedWhenHidden() wajib ada —
                   tanpa itu Filament tidak mengirim kolom yang sedang
                   tersembunyi, sehingga mematikan saklar justru membiarkan
                   lokasi lama tetap tersimpan. */
                Forms\Components\Toggle::make('show_location')
                    ->label('Tampilkan lokasi')
                    ->helperText('Nyalakan bila artikel ini meliput kegiatan di tempat tertentu.')
                    ->live()
                    ->dehydrated(false)
                    ->afterStateHydrated(fn (Forms\Components\Toggle $component, ?\App\Models\BlogPost $record) => $component->state(filled($record?->location)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('location')
                    ->label('Lokasi')
                    ->maxLength(160)
                    ->placeholder('Jakarta Selatan, Indonesia')
                    ->helperText('Tampil di kartu blog dan halaman artikel.')
                    ->visible(fn (Forms\Get $get): bool => (bool) $get('show_location'))
                    ->dehydratedWhenHidden()
                    ->dehydrateStateUsing(fn (?string $state, Forms\Get $get): ?string => $get('show_location') ? (trim((string) $state) ?: null) : null)
                    ->columnSpanFull(),
            ])->columns(2),

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
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\ImageColumn::make('featured_image')->disk('public')->label('')->size(60),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
            Tables\Columns\TextColumn::make('location')->label('Lokasi')->icon('heroicon-m-map-pin')
                ->placeholder('-')->searchable()->toggleable(),
            Tables\Columns\TextColumn::make('status')->badge()->colors([
                'gray' => 'draft', 'success' => 'published', 'warning' => 'archived',
            ]),
            Tables\Columns\TextColumn::make('published_at')->dateTime('d M Y')->label('Terbit')->sortable(),
            Tables\Columns\TextColumn::make('views')->label('Dilihat')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip']),
            Tables\Filters\SelectFilter::make('blog_category_id')->relationship('category', 'slug')->getOptionLabelFromRecordUsing(fn (\Illuminate\Database\Eloquent\Model $record): string => (string) ($record->getTranslation('name', 'id', false) ?: $record->name))->label('Kategori'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}