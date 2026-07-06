<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\PartnershipPackageResource\Pages;
use App\Models\PartnershipPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnershipPackageResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = PartnershipPackage::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Kemitraan';

    protected static ?string $navigationLabel = 'Paket Kemitraan';

    protected static ?string $modelLabel = 'Paket Kemitraan';

    protected static ?int $navigationSort = 1;

    protected static ?string $accessPermission = 'manage_partnership_packages';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Info Paket')->schema([
                Forms\Components\Select::make('tier')->options([
                    'blue' => 'Blue', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum',
                ])->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                Forms\Components\ColorPicker::make('color')->label('Warna Aksen'),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            ])->columns(4),

            Forms\Components\Section::make('Konten (ID/EN)')->schema([
                Forms\Components\TextInput::make('name.id')->label('Nama (ID)')->required()->maxLength(200),
                Forms\Components\TextInput::make('name.en')->label('Nama (EN)')->maxLength(200),
                Forms\Components\TextInput::make('tagline.id')->label('Tagline (ID)')->maxLength(200),
                Forms\Components\TextInput::make('tagline.en')->label('Tagline (EN)')->maxLength(200),
                Forms\Components\Textarea::make('description.id')->label('Deskripsi (ID)')->rows(3),
                Forms\Components\Textarea::make('description.en')->label('Deskripsi (EN)')->rows(3),
                Forms\Components\TextInput::make('price_note.id')->label('Catatan Harga (ID)')->placeholder('mulai dari'),
                Forms\Components\TextInput::make('price_note.en')->label('Catatan Harga (EN)')->placeholder('starting from'),
            ])->columns(2),

            Forms\Components\Section::make('Fitur (ID/EN)')->schema([
                Forms\Components\Repeater::make('features.id')
                    ->label('Fitur (ID)')
                    ->simple(Forms\Components\TextInput::make('item')->required())
                    ->columnSpan(6)
                    ->helperText('Daftar fitur dalam Bahasa Indonesia.'),
                Forms\Components\Repeater::make('features.en')
                    ->label('Fitur (EN)')
                    ->simple(Forms\Components\TextInput::make('item')->required())
                    ->columnSpan(6)
                    ->helperText('Feature list in English.'),
            ])->columns(12),

            Forms\Components\Section::make('Harga & Status')->schema([
                Forms\Components\TextInput::make('price')->label('Harga Indikatif')->numeric()->prefix('Rp')->helperText('Kosongkan untuk "by quote".'),
                Forms\Components\Toggle::make('is_highlighted')->label('Disorot')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            Tables\Columns\TextColumn::make('tier')->badge(),
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('price')->money('IDR')->placeholder('By quote'),
            Tables\Columns\IconColumn::make('is_highlighted')->label('Disorot')->boolean(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnershipPackages::route('/'),
            'create' => Pages\CreatePartnershipPackage::route('/create'),
            'edit' => Pages\EditPartnershipPackage::route('/{record}/edit'),
        ];
    }
}