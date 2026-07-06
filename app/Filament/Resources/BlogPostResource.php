<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Artikel Blog';

    protected static ?string $modelLabel = 'Artikel';

    protected static ?string $accessPermission = 'manage_blog';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Artikel (ID/EN)')->schema([
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
                Forms\Components\Textarea::make('excerpt.id')->label('Ringkasan (ID)')->rows(2),
                Forms\Components\Textarea::make('excerpt.en')->label('Ringkasan (EN)')->rows(2),
                Forms\Components\RichEditor::make('content.id')->label('Konten (ID)')->columnSpanFull(),
                Forms\Components\RichEditor::make('content.en')->label('Konten (EN)')->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Media')->schema([
                MediaUpload::for('featured_image', 'blog', 'blog')->label('Gambar Utama'),
                MediaUpload::for('banner_image', 'blog_banner', 'blog')->label('Banner Header'),
            ])->columns(2),

            Forms\Components\Section::make('Publikasi')->schema([
                Forms\Components\Select::make('blog_category_id')->relationship('category', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->searchable()->preload()->label('Kategori'),
                Forms\Components\Select::make('tags')->relationship('tags', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->multiple()->preload()->label('Tag'),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip',
                ])->default('draft')->required(),
                Forms\Components\DateTimePicker::make('published_at')->label('Tanggal Terbit'),
            ])->columns(2),

            Forms\Components\Section::make('SEO (ID/EN)')->collapsed()->schema([
                Forms\Components\TextInput::make('meta_title.id')->label('Meta Title (ID)')->maxLength(200),
                Forms\Components\TextInput::make('meta_title.en')->label('Meta Title (EN)')->maxLength(200),
                Forms\Components\Textarea::make('meta_description.id')->label('Meta Description (ID)')->rows(2),
                Forms\Components\Textarea::make('meta_description.en')->label('Meta Description (EN)')->rows(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\ImageColumn::make('featured_image')->disk('public')->label('')->size(60),
            Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('category.name')->label('Kategori')->badge(),
            Tables\Columns\TextColumn::make('status')->badge()->colors([
                'gray' => 'draft', 'success' => 'published', 'warning' => 'archived',
            ]),
            Tables\Columns\TextColumn::make('published_at')->dateTime('d M Y')->label('Terbit')->sortable(),
            Tables\Columns\TextColumn::make('views')->label('Dilihat')->sortable(),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options(['draft' => 'Draft', 'published' => 'Terbit', 'archived' => 'Arsip']),
            Tables\Filters\SelectFilter::make('blog_category_id')->relationship('category', 'slug')->label('Kategori'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}