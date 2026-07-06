<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Testimoni';

    protected static ?string $modelLabel = 'Testimoni';

    protected static ?string $accessPermission = 'manage_portfolio';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')->schema([
                Forms\Components\TextInput::make('author_name')->label('Nama')->required()->maxLength(150),
                Forms\Components\TextInput::make('author_company')->label('Perusahaan')->maxLength(200),
                MediaUpload::for('author_photo', 'avatar', 'testimonials')
                    ->label('Foto')
                    ->helperText('Disarankan 200 × 200 px, maks 80 KB.'),
                Forms\Components\Select::make('rating')->options([5 => '⭐⭐⭐⭐⭐', 4 => '⭐⭐⭐⭐', 3 => '⭐⭐⭐', 2 => '⭐⭐', 1 => '⭐'])->label('Rating'),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),

            Forms\Components\Section::make('Testimoni (ID/EN)')
                ->description('Isi teks dalam Bahasa Indonesia dan English.')
                ->schema([
                    Forms\Components\TextInput::make('author_position.id')
                        ->label('Jabatan (ID)')
                        ->maxLength(200),
                    Forms\Components\TextInput::make('author_position.en')
                        ->label('Jabatan (EN)')
                        ->maxLength(200),
                    Forms\Components\Textarea::make('content.id')
                        ->label('Isi Testimoni (ID)')
                        ->required()
                        ->rows(4),
                    Forms\Components\Textarea::make('content.en')
                        ->label('Isi Testimoni (EN)')
                        ->rows(4),
                ])->columns(2),

            Forms\Components\Section::make('Relasi (opsional)')->schema([
                Forms\Components\Select::make('client_id')->relationship('client', 'name')->searchable()->label('Klien'),
                Forms\Components\Select::make('portfolio_id')->relationship('portfolio', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title)->searchable()->label('Portofolio'),
            ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\ImageColumn::make('author_photo')->disk('public')->circular()->label(''),
            Tables\Columns\TextColumn::make('author_name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('author_company')->label('Perusahaan'),
            Tables\Columns\TextColumn::make('rating')->label('Rating')->badge(),
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
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}