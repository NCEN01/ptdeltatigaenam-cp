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
                    Forms\Components\TextInput::make('subtitle.id')
                        ->label('Subjudul (ID)')
                        ->maxLength(500),
                    Forms\Components\TextInput::make('subtitle.en')
                        ->label('Subjudul (EN)')
                        ->maxLength(500),
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