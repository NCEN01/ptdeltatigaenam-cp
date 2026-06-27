<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\BannerResource\Pages;
use App\Models\Banner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BannerResource extends Resource
{
    use Translatable;
    use RestrictsToPermission;

    protected static ?string $model = Banner::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Banner';

    protected static ?string $modelLabel = 'Banner';

    protected static ?string $accessPermission = 'manage_banners';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Konten')->schema([
                Forms\Components\TextInput::make('title')->label('Judul'),
                Forms\Components\TextInput::make('subtitle')->label('Subjudul'),
                Forms\Components\TextInput::make('button_text')->label('Teks Tombol'),
                Forms\Components\TextInput::make('link_url')->label('URL Tautan')->url(),
            ])->columns(2),
            Forms\Components\Section::make('Gambar')->schema([
                MediaUpload::for('image', 'hero', 'banners')->label('Gambar Desktop')->required(),
                MediaUpload::for('image_mobile', 'hero_mobile', 'banners')->label('Gambar Mobile'),
            ])->columns(2),
            Forms\Components\Section::make('Penempatan & Tayang')->schema([
                Forms\Components\Select::make('placement')->options([
                    'home_hero' => 'Home Hero', 'home_section' => 'Home Section',
                    'service_category' => 'Kategori Layanan', 'service' => 'Layanan',
                    'blog' => 'Blog', 'portfolio' => 'Portofolio', 'about' => 'About', 'global' => 'Global',
                ])->default('home_hero')->required(),
                Forms\Components\Select::make('service_category_id')->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($r) => $r->name)->searchable()->label('Kategori (opsional)'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                Forms\Components\DateTimePicker::make('starts_at')->label('Mulai Tayang'),
                Forms\Components\DateTimePicker::make('ends_at')->label('Akhir Tayang'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            Tables\Columns\ImageColumn::make('image')->disk('public')->label('')->size(80),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
            Tables\Columns\TextColumn::make('placement')->badge(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->filters([
            Tables\Filters\SelectFilter::make('placement')->options([
                'home_hero' => 'Home Hero', 'home_section' => 'Home Section', 'global' => 'Global',
            ]),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBanners::route('/'),
            'create' => Pages\CreateBanner::route('/create'),
            'edit' => Pages\EditBanner::route('/{record}/edit'),
        ];
    }
}
