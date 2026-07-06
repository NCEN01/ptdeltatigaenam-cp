<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\InstagramUpdateResource\Pages;
use App\Models\InstagramUpdate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstagramUpdateResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = InstagramUpdate::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Instagram Update';

    protected static ?string $modelLabel = 'Instagram Update';

    protected static ?int $navigationSort = 50;

    protected static ?string $accessPermission = 'manage_instagram_updates';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Gambar')
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->label('Gambar Update')
                        ->image()
                        ->disk('public')
                        ->directory('instagram')
                        ->required()
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Konten (ID/EN)')
                ->description('Isi teks dalam Bahasa Indonesia dan English.')
                ->schema([
                    Forms\Components\TextInput::make('batch_label.id')
                        ->label('Label Batch (ID)')
                        ->maxLength(100)
                        ->placeholder('BATCH IV'),
                    Forms\Components\TextInput::make('batch_label.en')
                        ->label('Label Batch (EN)')
                        ->maxLength(100),
                    Forms\Components\TextInput::make('title.id')
                        ->label('Judul (ID)')
                        ->required()
                        ->maxLength(250),
                    Forms\Components\TextInput::make('title.en')
                        ->label('Judul (EN)')
                        ->maxLength(250),
                    Forms\Components\TextInput::make('company.id')
                        ->label('Perusahaan (ID)')
                        ->maxLength(200)
                        ->placeholder('PT Delta Tiga Enam'),
                    Forms\Components\TextInput::make('company.en')
                        ->label('Perusahaan (EN)')
                        ->maxLength(200),
                    Forms\Components\TextInput::make('date_range.id')
                        ->label('Rentang Tanggal (ID)')
                        ->maxLength(100)
                        ->placeholder('19 - 22 Agustus 2025'),
                    Forms\Components\TextInput::make('date_range.en')
                        ->label('Rentang Tanggal (EN)')
                        ->maxLength(100),
                ])->columns(2),

            Forms\Components\Section::make('Tautan & Status')
                ->schema([
                    Forms\Components\TextInput::make('instagram_url')
                        ->label('URL Instagram')
                        ->url()
                        ->placeholder('https://www.instagram.com/deltatigaenam/...')
                        ->maxLength(500),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])->columns(2),

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
                    ->label('Judul')->searchable()->wrap(),
                Tables\Columns\TextColumn::make('batch_label')
                    ->label('Batch')
                    ->badge(),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
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
            'index'  => Pages\ListInstagramUpdates::route('/'),
            'create' => Pages\CreateInstagramUpdate::route('/create'),
            'edit'   => Pages\EditInstagramUpdate::route('/{record}/edit'),
        ];
    }
}