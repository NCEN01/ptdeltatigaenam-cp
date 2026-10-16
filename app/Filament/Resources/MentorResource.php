<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\MentorResource\Pages;
use App\Models\Mentor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

/**
 * Pustaka mentor. Satu mentor didaftarkan sekali di sini, lalu dipilih dari
 * halaman Layanan untuk program mana saja yang diampunya — fotonya tidak
 * perlu diunggah ulang, dan memperbarui bionya berlaku di seluruh program
 * sekaligus.
 */
class MentorResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Mentor::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Layanan';

    protected static ?string $navigationLabel = 'Mentor';

    protected static ?string $modelLabel = 'Mentor';

    protected static ?string $pluralModelLabel = 'Mentor';

    protected static ?int $navigationSort = 4;

    protected static ?string $accessPermission = 'manage_services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identitas')
                ->description('Tampil di kartu mentor pada halaman program.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Lengkap')
                        ->helperText('Boleh dengan gelar, misalnya "Budi Santoso, S.T., M.M."')
                        ->required()
                        ->maxLength(150),

                    Forms\Components\FileUpload::make('photo')
                        ->label('Foto')
                        ->image()
                        ->imageEditor()
                        ->directory('mentors')
                        // Potret tegak: kartunya menampilkan foto di kolom kiri
                        // yang lebih tinggi daripada lebarnya.
                        ->imageCropAspectRatio('3:4')
                        ->maxSize(2048)
                        ->helperText('Potret tegak, maksimal 2 MB. Boleh dikosongkan — bila kosong, huruf depan namanya yang tampil.'),

                    Forms\Components\TextInput::make('role.id')
                        ->label('Keahlian (ID)')
                        ->placeholder('Ahli K3 Umum')
                        ->helperText('Satu baris pendek di bawah nama.')
                        ->maxLength(150),

                    Forms\Components\TextInput::make('role.en')
                        ->label('Keahlian (EN)')
                        ->maxLength(150),

                    Forms\Components\Textarea::make('bio.id')
                        ->label('Bio Singkat (ID)')
                        ->rows(4)
                        ->helperText('Dua sampai tiga kalimat: pengalaman, tempat bekerja, dan apa yang diajarkannya.')
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('bio.en')
                        ->label('Bio Singkat (EN)')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Forms\Components\Section::make('Pengaturan')
                ->schema([
                    Forms\Components\TextInput::make('linkedin_url')
                        ->label('Tautan LinkedIn')
                        ->url()
                        ->prefixIcon('heroicon-o-link')
                        ->placeholder('https://www.linkedin.com/in/…')
                        ->helperText('Boleh dikosongkan. Bila diisi, lambang LinkedIn muncul di kartunya.')
                        ->maxLength(255),

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0)
                        ->helperText('Angka kecil tampil lebih dulu.'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true)
                        ->helperText('Dimatikan berarti mentor ini tidak tampil di halaman program mana pun, tanpa perlu melepasnya satu per satu.'),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn () => null),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('role')
                    ->label('Keahlian')
                    ->limit(40)
                    ->color('gray')
                    ->placeholder('-'),

                // Berapa program yang diampunya. Angka ini yang memberi tahu
                // admin bahwa menyunting bio di sini berdampak ke mana saja.
                Tables\Columns\TextColumn::make('services_count')
                    ->label('Program')
                    ->counts('services')
                    ->badge()
                    ->color('info'),

                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Belum ada mentor')
            ->emptyStateDescription('Tambahkan mentor di sini sekali, lalu pilih dari halaman Layanan untuk program yang diampunya.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMentors::route('/'),
            'create' => Pages\CreateMentor::route('/create'),
            'edit' => Pages\EditMentor::route('/{record}/edit'),
        ];
    }
}
