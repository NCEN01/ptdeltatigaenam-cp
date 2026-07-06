<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\PortfolioResource\Pages;
use App\Models\Portfolio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PortfolioResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Portfolio::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Portofolio';

    protected static ?string $modelLabel = 'Portofolio';

    protected static ?string $accessPermission = 'manage_portfolio';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Proyek')->schema([
                Forms\Components\TextInput::make('title.id')
                    ->label('Judul (ID)')
                    ->required()
                    ->maxLength(280)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                Forms\Components\TextInput::make('title.en')
                    ->label('Judul (EN)')
                    ->maxLength(280),
                Forms\Components\TextInput::make('slug')->required()->maxLength(280)->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('client_name')->label('Nama Klien')->maxLength(200),
                Forms\Components\Select::make('service_category_id')
                    ->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)
                    ->preload()
                    ->searchable()
                    ->label('Kategori'),
                Forms\Components\DatePicker::make('project_date')->label('Tanggal Proyek'),
                Forms\Components\Textarea::make('short_description.id')->label('Deskripsi Singkat (ID)')->rows(2),
                Forms\Components\Textarea::make('short_description.en')->label('Deskripsi Singkat (EN)')->rows(2),
                Forms\Components\RichEditor::make('content.id')->label('Konten (ID)'),
                Forms\Components\RichEditor::make('content.en')->label('Konten (EN)'),
                MediaUpload::for('cover_image', 'portfolio', 'portfolio')->label('Cover')->columnSpanFull(),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Galeri')->schema([
                Forms\Components\Repeater::make('images')->relationship()->label('Foto Galeri')->schema([
                    MediaUpload::for('image', 'portfolio', 'portfolio/gallery')->label('Foto')->required(),
                    Forms\Components\TextInput::make('caption.id')->label('Keterangan (ID)'),
                    Forms\Components\TextInput::make('caption.en')->label('Keterangan (EN)'),
                ])->columns(3)->collapsible()->defaultItems(0),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('project_date', 'desc')->columns([
            Tables\Columns\ImageColumn::make('cover_image')->disk('public')->label('')->size(60),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('client_name')->label('Klien'),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
            Tables\Columns\IconColumn::make('is_featured')->label('Unggulan')->boolean(),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPortfolios::route('/'),
            'create' => Pages\CreatePortfolio::route('/create'),
            'edit' => Pages\EditPortfolio::route('/{record}/edit'),
        ];
    }
}