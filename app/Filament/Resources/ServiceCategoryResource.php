<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Models\ServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceCategoryResource extends Resource
{
    use Translatable;
    use RestrictsToPermission;

    protected static ?string $model = ServiceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Kategori Layanan';

    protected static ?string $modelLabel = 'Kategori Layanan';

    protected static ?int $navigationSort = 1;

    protected static ?string $accessPermission = 'manage_services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(170)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(170)
                    ->unique(ignoreRecord: true)
                    ->helperText('URL unik, otomatis dari nama (boleh diubah).'),
                Forms\Components\TextInput::make('short_description')->label('Deskripsi Singkat')->maxLength(255),
                Forms\Components\Textarea::make('description')->label('Deskripsi')->rows(4),
                Forms\Components\TextInput::make('icon')->label('Ikon (heroicon)')->placeholder('heroicon-o-academic-cap'),
            ])->columns(2),

            Forms\Components\Section::make('Media & Tampilan')->schema([
                MediaUpload::for('image', 'thumbnail', 'service-categories')->label('Gambar'),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
                Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0),
            ])->columns(2),

            Forms\Components\Section::make('SEO')->collapsed()->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(255),
                Forms\Components\Textarea::make('meta_description')->label('Meta Description')->rows(2),
            ])->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('image')->label('Gambar')->disk('public')->square(),
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->toggleable()->color('gray'),
                Tables\Columns\IconColumn::make('is_featured')->label('Unggulan')->boolean(),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
                Tables\Columns\TextColumn::make('services_count')->counts('services')->label('Layanan'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TernaryFilter::make('is_featured')->label('Unggulan'),
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
            'index' => Pages\ListServiceCategories::route('/'),
            'create' => Pages\CreateServiceCategory::route('/create'),
            'edit' => Pages\EditServiceCategory::route('/{record}/edit'),
        ];
    }
}
