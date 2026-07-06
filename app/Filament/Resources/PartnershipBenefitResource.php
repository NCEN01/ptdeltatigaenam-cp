<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\PartnershipBenefitResource\Pages;
use App\Models\PartnershipBenefit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnershipBenefitResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = PartnershipBenefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Kemitraan';

    protected static ?string $navigationLabel = 'Manfaat Kemitraan';

    protected static ?string $modelLabel = 'Manfaat Kemitraan';

    protected static ?int $navigationSort = 2;

    protected static ?string $accessPermission = 'manage_partnership_packages';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Manfaat (ID/EN)')->schema([
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(200),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(200),
                Forms\Components\TextInput::make('icon')
                    ->label('Ikon (heroicon)')
                    ->placeholder('heroicon-o-check-badge'),
                Forms\Components\Textarea::make('description.id')
                    ->label('Deskripsi (ID)')
                    ->rows(3),
                Forms\Components\Textarea::make('description.en')
                    ->label('Deskripsi (EN)')
                    ->rows(3),
                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable(),
            Tables\Columns\TextColumn::make('icon')->color('gray'),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnershipBenefits::route('/'),
            'create' => Pages\CreatePartnershipBenefit::route('/create'),
            'edit' => Pages\EditPartnershipBenefit::route('/{record}/edit'),
        ];
    }
}