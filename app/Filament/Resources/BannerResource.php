<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Banner';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $accessPermission = 'manage_banners';

    protected const PLACEMENT_OPTIONS = [
        'home_hero'   => 'Beranda (Hero)',
        'about'       => 'Tentang',
        'services'    => 'Layanan',
        'certificate' => 'Sertifikat',
        'portfolio'   => 'Portofolio',
        'blog'        => 'Blog',
        'agenda'      => 'Agenda',
        'partnership' => 'Kemitraan',
        'contact'     => 'Kontak',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Penempatan')
                ->description('Tentukan di halaman mana banner ini akan tampil.')
                ->schema([
                    Forms\Components\Select::make('placement')
                        ->options(self::PLACEMENT_OPTIONS)
                        ->default('home_hero')
                        ->required()
                        ->live()
                        ->label('Halaman'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),

            Forms\Components\Section::make('Konten')
                ->description('Isi teks dalam Bahasa Indonesia dan English.')
                ->schema([
                    Forms\Components\TextInput::make('title.id')
                        ->label('Judul (ID)')
                        ->required()
                        ->maxLength(250),
                    Forms\Components\TextInput::make('title.en')
                        ->label('Judul (EN)')
                        ->maxLength(250),

                    /* Deskripsi kepala halaman punya tiga keadaan, dan dua di
                       antaranya dulu tidak bisa dibedakan. Mengosongkan kolom
                       subjudul tidak menghilangkan deskripsinya: <x-page-header>
                       hanya menimpa kalimat bawaan halaman kalau subjudulnya
                       terisi, jadi kolom yang dikosongkan justru memunculkan
                       kembali teks yang ditulis di Blade.

                       Saklar ini memisahkan "jangan tampilkan apa pun" dari
                       "pakai kalimat bawaan halaman", sehingga ketiga keadaannya
                       bisa dipilih admin: mati, nyala dan diisi, atau nyala dan
                       dibiarkan kosong. */
                    Forms\Components\Toggle::make('show_subtitle')
                        ->label('Tampilkan deskripsi')
                        ->helperText('Matikan bila kepala halaman ini sebaiknya hanya berisi judul.')
                        ->default(true)
                        ->live()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('subtitle.id')
                        ->label('Deskripsi (ID)')
                        ->placeholder('Kosongkan untuk memakai kalimat bawaan halaman')
                        ->helperText('Opsional. Dibiarkan kosong, kalimat bawaan halaman yang dipakai.')
                        ->maxLength(500)
                        ->visible(fn (Forms\Get $get): bool => (bool) $get('show_subtitle')),
                    Forms\Components\TextInput::make('subtitle.en')
                        ->label('Deskripsi (EN)')
                        ->placeholder('Leave empty to use the page default')
                        ->maxLength(500)
                        ->visible(fn (Forms\Get $get): bool => (bool) $get('show_subtitle')),
                    Forms\Components\TextInput::make('button_text.id')
                        ->label('Teks Tombol (ID)')
                        ->maxLength(100)
                        ->visible(fn (Forms\Get $get): bool => $get('placement') === 'home_hero'),
                    Forms\Components\TextInput::make('button_text.en')
                        ->label('Teks Tombol (EN)')
                        ->maxLength(100)
                        ->visible(fn (Forms\Get $get): bool => $get('placement') === 'home_hero'),
                    Forms\Components\TextInput::make('link_url')
                        ->label('URL Tautan')
                        ->url()
                        ->visible(fn (Forms\Get $get): bool => $get('placement') === 'home_hero'),
                ])->columns(2),

            Forms\Components\Section::make('Gambar')->schema([
                MediaUpload::for('image', 'hero', 'banners')
                    ->label('Gambar Banner')
                    ->required(),
            ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->disk('public')->label('')->size(80),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('placement')
                    ->label('Halaman')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => self::PLACEMENT_OPTIONS[$state] ?? $state),
                Tables\Columns\ToggleColumn::make('show_subtitle')
                    ->label('Deskripsi')
                    ->tooltip('Matikan agar kepala halaman ini hanya berisi judul.'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('placement')
                    ->label('Halaman')
                    ->options(self::PLACEMENT_OPTIONS),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit'   => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}