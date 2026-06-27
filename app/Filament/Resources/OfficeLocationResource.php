<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\OfficeLocationResource\Pages;
use App\Models\OfficeLocation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeLocationResource extends Resource
{
    use Translatable;
    use RestrictsToPermission;

    protected static ?string $model = OfficeLocation::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Kantor';

    protected static ?string $modelLabel = 'Kantor';

    protected static ?string $accessPermission = 'manage_offices';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama Kantor')->required(),
            Forms\Components\Select::make('type')->options([
                'pusat' => 'Pusat', 'pemasaran' => 'Pemasaran', 'operasional' => 'Operasional', 'lainnya' => 'Lainnya',
            ])->default('lainnya')->required(),
            Forms\Components\Textarea::make('address')->label('Alamat')->required()->rows(3)->columnSpanFull(),
            Forms\Components\TextInput::make('phone')->label('Telepon'),
            Forms\Components\TextInput::make('whatsapp')->label('WhatsApp'),
            Forms\Components\TextInput::make('email')->email()->label('Email'),
            Forms\Components\Textarea::make('map_embed')->label('Embed Peta (iframe)')->rows(2)->columnSpanFull(),
            Forms\Components\TextInput::make('latitude')->numeric(),
            Forms\Components\TextInput::make('longitude')->numeric(),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('type')->badge(),
            Tables\Columns\TextColumn::make('phone')->label('Telepon'),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOfficeLocations::route('/'),
            'create' => Pages\CreateOfficeLocation::route('/create'),
            'edit' => Pages\EditOfficeLocation::route('/{record}/edit'),
        ];
    }
}
