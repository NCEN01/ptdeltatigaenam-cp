<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\ServiceCategoryResource\Pages;
use App\Models\ServiceCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceCategoryResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = ServiceCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Kategori Layanan';

    protected static ?string $modelLabel = 'Kategori Layanan';

    protected static ?int $navigationSort = 10;

    protected static ?string $accessPermission = 'manage_services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas (ID/EN)')->schema([
                Forms\Components\TextInput::make('name.id')
                    ->label('Nama (ID)')
                    ->required()
                    ->maxLength(170),
                Forms\Components\TextInput::make('name.en')
                    ->label('Nama (EN)')
                    ->maxLength(170),
                /* Satu deskripsi saja, yang singkat. Deskripsi panjang tidak pernah
                   dibaca halaman mana pun — yang tampil di bawah nama kategori di
                   halaman Layanan hanyalah kalimat singkat ini. */
                Forms\Components\Textarea::make('short_description.id')->label('Deskripsi Singkat (ID)')->rows(2)->maxLength(255)
                    ->helperText('Tampil di bawah nama kategori pada halaman Layanan. Satu sampai dua kalimat.'),
                Forms\Components\Textarea::make('short_description.en')->label('Deskripsi Singkat (EN)')->rows(2)->maxLength(255),
            ])->columns(2),

            Forms\Components\Section::make('Ikon & Tampilan')->schema([
                // Kartu kategori di beranda memakai ikon, bukan foto. Kolom `icon`
                // sudah lama ada di tabel tetapi tidak pernah diisi maupun dibaca;
                // sekarang terpakai dan bisa dipilih admin.
                Forms\Components\Select::make('icon')
                    ->label('Ikon')
                    ->options([
                        'structure' => 'Bagan organisasi',
                        'people' => 'Orang / tim',
                        'search-user' => 'Pencarian orang',
                        'presentation' => 'Papan presentasi',
                        'certificate' => 'Lencana sertifikat',
                        'briefcase' => 'Koper kerja',
                        'chart' => 'Grafik',
                        'shield' => 'Perisai',
                        'target' => 'Sasaran',
                        'book' => 'Buku',
                    ])
                    ->native(false)
                    ->placeholder('Koper kerja (bawaan)')
                    ->helperText('Tampil pada kartu kategori di beranda. Dikosongkan akan memakai ikon koper.'),
                Forms\Components\Toggle::make('is_featured')->label('Unggulan')->default(false),
                Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
            ])->columns(2),

            Forms\Components\Section::make('SEO (ID/EN)')->collapsed()->schema([
                Forms\Components\TextInput::make('meta_title.id')->label('Meta Title (ID)')->maxLength(255),
                Forms\Components\TextInput::make('meta_title.en')->label('Meta Title (EN)')->maxLength(255),
                Forms\Components\Textarea::make('meta_description.id')->label('Meta Description (ID)')->rows(2),
                Forms\Components\Textarea::make('meta_description.en')->label('Meta Description (EN)')->rows(2),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
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