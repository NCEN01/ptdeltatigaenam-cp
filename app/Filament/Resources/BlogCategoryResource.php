<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\BlogCategoryResource\Pages;
use App\Models\BlogCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogCategoryResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = BlogCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Kategori Blog';

    protected static ?string $modelLabel = 'Kategori Blog';

    protected static ?string $accessPermission = 'manage_blog';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name.id')
                ->label('Nama (ID)')
                ->required()
                ->maxLength(100)
                ->live(onBlur: true)
                ->afterStateUpdated(fn (Forms\Set $set, ?string $state, string $operation) => $operation === 'create' ? $set('slug', Str::slug((string) $state)) : null),
            Forms\Components\TextInput::make('name.en')
                ->label('Nama (EN)')
                ->maxLength(100),
            Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(150),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('slug')->color('gray'),
            Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Artikel'),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogCategories::route('/'),
            'create' => Pages\CreateBlogCategory::route('/create'),
            'edit' => Pages\EditBlogCategory::route('/{record}/edit'),
        ];
    }
}