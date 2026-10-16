<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\ProgramBenefitResource\Pages;
use App\Models\ProgramBenefit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Benefit BAWAAN program — yang berlaku untuk setiap program yang tidak
 * punya daftarnya sendiri.
 *
 * Benefit khusus satu program tidak diatur di sini melainkan di dalam
 * halaman Layanan program itu. Daftar di sini sengaja disaring ke baris
 * yang service_id-nya kosong supaya keduanya tidak pernah tercampur dalam
 * satu tabel di layar admin.
 */
class ProgramBenefitResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = ProgramBenefit::class;

    protected static ?string $navigationIcon = 'heroicon-o-gift';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Benefit Bawaan';

    protected static ?string $modelLabel = 'Benefit Bawaan';

    protected static ?string $pluralModelLabel = 'Benefit Bawaan';

    protected static ?int $navigationSort = 40;

    protected static ?string $accessPermission = 'manage_services';

    /** Hanya benefit bawaan; yang khusus program diurus dari halaman Layanan. */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNull('service_id');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Benefit')
                ->description('Berlaku untuk semua program. Program yang perlu daftar berbeda bisa mengisinya sendiri dari halaman Layanan; begitu diisi, daftar bawaan ini tidak dipakai untuk program tersebut.')
                ->schema([
                    Forms\Components\TextInput::make('title.id')
                        ->label('Judul (ID)')
                        ->placeholder('Sertifikat digital setelah program selesai')
                        ->required()
                        ->maxLength(200),

                    Forms\Components\TextInput::make('title.en')
                        ->label('Judul (EN)')
                        ->maxLength(200),

                    Forms\Components\Textarea::make('description.id')
                        ->label('Keterangan (ID)')
                        ->rows(2)
                        ->helperText('Boleh dikosongkan bila judulnya sudah cukup menjelaskan.'),

                    Forms\Components\Textarea::make('description.en')
                        ->label('Keterangan (EN)')
                        ->rows(2),

                    Forms\Components\Select::make('icon')
                        ->label('Ikon')
                        ->options(self::iconOptions())
                        ->searchable()
                        ->native(false)
                        ->placeholder('Pilih ikon')
                        ->helperText('Memakai kumpulan ikon yang sama dengan kartu kategori layanan.'),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->width(60),
                Tables\Columns\TextColumn::make('title')->label('Judul')->searchable()->weight('medium'),
                Tables\Columns\TextColumn::make('description')->label('Keterangan')->limit(60)->color('gray')->placeholder('-'),
                Tables\Columns\TextColumn::make('icon')->label('Ikon')->badge()->color('gray')->placeholder('-'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada benefit bawaan')
            ->emptyStateDescription('Selama daftar ini kosong, bagian "Yang kamu dapatkan" tidak tampil di halaman program mana pun.');
    }

    /**
     * Nama ikon yang dikenali <x-category-icon>. Diambil dari komponennya
     * sendiri supaya daftar pilihan di sini tidak pernah menawarkan ikon
     * yang tidak ada gambarnya.
     */
    public static function iconOptions(): array
    {
        $names = \App\Support\CategoryIcons::names();

        return array_combine($names, array_map(
            fn (string $n) => ucwords(str_replace(['-', '_'], ' ', $n)),
            $names
        ));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgramBenefits::route('/'),
            'create' => Pages\CreateProgramBenefit::route('/create'),
            'edit' => Pages\EditProgramBenefit::route('/{record}/edit'),
        ];
    }
}
