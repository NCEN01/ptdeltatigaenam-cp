<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\PartnershipPackageResource\Pages;
use App\Models\PartnershipPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnershipPackageResource extends Resource
{
    use Translatable;
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
            Forms\Components\Select::make('tier')->options([
                'blue' => 'Blue', 'silver' => 'Silver', 'gold' => 'Gold', 'platinum' => 'Platinum',
            ])->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name')->label('Nama Tampilan')->required(),
            Forms\Components\TextInput::make('tagline')->label('Tagline'),
            Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(3)->columnSpanFull(),
            Forms\Components\Repeater::make('features')->label('Fitur (per bahasa)')->simple(
                Forms\Components\TextInput::make('feature')->required(),
            )->columnSpanFull()->helperText('Daftar fitur untuk bahasa yang aktif.'),
            Forms\Components\TextInput::make('price')->label('Harga Indikatif')->numeric()->prefix('Rp')->helperText('Kosongkan untuk "by quote".'),
            Forms\Components\TextInput::make('price_note')->label('Catatan Harga')->placeholder('mulai dari'),
            Forms\Components\ColorPicker::make('color')->label('Warna Aksen'),
            Forms\Components\Toggle::make('is_highlighted')->label('Disorot')->default(false),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ])->columns(2);
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
