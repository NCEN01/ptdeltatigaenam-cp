<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\OfficeResource\Pages;
use App\Models\Office;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Office::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?int $navigationSort = 15;

    protected static ?string $navigationLabel = 'Alamat Kantor';

    protected static ?string $modelLabel = 'Kantor';

    protected static ?string $pluralModelLabel = 'Alamat Kantor';

    protected static ?string $accessPermission = 'manage_offices';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Kantor')
                ->description('Tampil di kolom Kontak pada footer dan sebagai kartu pemilih peta di halaman Kontak.')
                ->schema([
                    /* Nama kantor dwibahasa, bukan satu teks. Materi resmi
                       perusahaan memakai istilah Inggris dan itu dipertahankan
                       di versi Inggris situs, sementara versi Indonesia
                       memakai sebutan Indonesia — "Lokasi Kantor" dan "Head
                       Office" bukan terjemahan harfiah satu sama lain. */
                    Forms\Components\TextInput::make('name.id')
                        ->label('Nama Kantor (ID)')
                        ->required()
                        ->maxLength(80)
                        ->placeholder('Lokasi Kantor'),
                    Forms\Components\TextInput::make('name.en')
                        ->label('Nama Kantor (EN)')
                        ->maxLength(80)
                        ->placeholder('Head Office'),

                    /* Alamat ditulis sebagai satu kalimat tanpa pemutus baris
                       supaya bisa mengalir mengikuti lebar tempatnya dipasang:
                       kolom footer jauh lebih sempit daripada kartu di halaman
                       Kontak, dan pemutus baris yang dipatok akan jatuh di
                       tempat yang salah di salah satunya. */
                    Forms\Components\Textarea::make('address')
                        ->label('Alamat')
                        ->required()
                        ->rows(3)
                        ->maxLength(400)
                        ->helperText('Satu kalimat tanpa enter, agar rapi di footer maupun di kartu halaman Kontak.')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Telepon & Peta')
                ->schema([
                    Forms\Components\TextInput::make('phone')
                        ->label('Telepon (tampil)')
                        ->maxLength(50)
                        ->placeholder('(021) 5890 5002'),

                    /* Dipisah dari kolom di sebelahnya dengan sengaja: tautan
                       tel: menuntut bentuk hanya-angka berkode negara, dan
                       menurunkannya dari teks tampilan tidak bisa diandalkan —
                       kode wilayah 021 harus kehilangan nol depannya dan
                       mendapat +62, aturan yang berbeda untuk nomor seluler. */
                    Forms\Components\TextInput::make('tel')
                        ->label('Telepon (untuk tautan panggil)')
                        ->maxLength(30)
                        ->placeholder('+622158905002')
                        ->helperText('Hanya angka dengan kode negara.'),

                    Forms\Components\Textarea::make('maps')
                        ->label('Kata kunci peta')
                        ->rows(2)
                        ->maxLength(300)
                        ->helperText('Bukan alamat tampilan, melainkan yang dicarikan ke Google Maps. Buat lebih pendek dan tanpa tanda pisah agar ketemu.')
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Urutan & Status')
                ->schema([
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Urutan')
                        ->numeric()
                        ->default(0)
                        ->helperText('Angka kecil tampil lebih dulu.'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Tampilkan')
                        ->default(true)
                        ->helperText('Dimatikan, kantor ini tidak tampil di mana pun tetapi datanya tersimpan.'),
                ])->columns(2),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Kantor')->searchable(),
                Tables\Columns\TextColumn::make('address')->label('Alamat')->wrap()->limit(90),
                Tables\Columns\TextColumn::make('phone')->label('Telepon'),
                Tables\Columns\ToggleColumn::make('is_active')->label('Tampil'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum ada kantor')
            ->emptyStateDescription('Selama tabel ini kosong, situs memakai daftar kantor bawaan di config/company.php.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOffices::route('/'),
            'create' => Pages\CreateOffice::route('/create'),
            'edit' => Pages\EditOffice::route('/{record}/edit'),
        ];
    }
}
