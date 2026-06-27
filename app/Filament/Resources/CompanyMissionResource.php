<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\CompanyMissionResource\Pages;
use App\Models\CompanyMission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CompanyMissionResource extends Resource
{
    use Translatable;
    use RestrictsToPermission;

    protected static ?string $model = CompanyMission::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Misi Perusahaan';

    protected static ?string $modelLabel = 'Misi';

    protected static ?string $accessPermission = 'manage_missions';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('content')->label('Isi Misi')->required()->rows(3)->columnSpanFull(),
            Forms\Components\TextInput::make('icon')->label('Ikon (heroicon)'),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            Tables\Columns\TextColumn::make('content')->label('Misi')->wrap()->limit(80),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyMissions::route('/'),
            'create' => Pages\CreateCompanyMission::route('/create'),
            'edit' => Pages\EditCompanyMission::route('/{record}/edit'),
        ];
    }
}
