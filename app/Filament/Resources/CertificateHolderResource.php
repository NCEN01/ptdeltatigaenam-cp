<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\CertificateHolderResource\Pages;
use App\Models\CertificateHolder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CertificateHolderResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = CertificateHolder::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Pemegang Sertifikat';

    protected static ?string $modelLabel = 'Pemegang Sertifikat';

    protected static ?string $pluralModelLabel = 'Pemegang Sertifikat';

    protected static ?string $accessPermission = 'manage_certificates';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Peserta')->schema([
                Forms\Components\TextInput::make('participant_name')->label('Nama Peserta')->required()->maxLength(200),
                Forms\Components\TextInput::make('company_name')->label('Nama Perusahaan')->maxLength(200),

                /* No. UJK sengaja tidak diminta unik: ia nomor sesi uji
                   kompetensi, dan satu sesi diikuti banyak peserta. Menuntutnya
                   unik akan menolak data yang justru benar. */
                Forms\Components\TextInput::make('ujk_number')->label('No. UJK')->maxLength(50)
                    ->helperText('Nomor sesi uji kompetensi. Boleh sama untuk peserta satu angkatan.'),

                /* Nomor sertifikat diperiksa keunikannya di sini, bukan hanya
                   oleh indeks unik di basis data. Keduanya memang perlu: indeks
                   yang menjamin, tetapi kalau hanya indeks yang menjaga, admin
                   yang mengetik nomor ganda mendapat layar galat basis data
                   dan kehilangan seluruh isian formulirnya. Di sini ia dapat
                   kalimat di bawah kolomnya, dengan isian yang masih utuh.

                   ignoreRecord() supaya menyunting baris tanpa mengubah
                   nomornya tidak tertolak oleh barisnya sendiri. */
                Forms\Components\TextInput::make('certificate_number')->label('No. Sertifikat')->maxLength(100)
                    ->unique(ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'Nomor sertifikat ini sudah terdaftar. Periksa lagi — satu nomor hanya boleh dipakai satu sertifikat.',
                    ])
                    ->helperText('Harus unik. Kosongkan bila nomornya belum diterbitkan.'),

                Forms\Components\TextInput::make('qualification')->label('Kualifikasi')->maxLength(200),
            ])->columns(2),
            Forms\Components\Section::make('Masa Berlaku')->schema([
                Forms\Components\DatePicker::make('issued_at')->label('Tanggal Terbit'),

                /* Tanggal berakhir tidak boleh mendahului tanggal terbit.
                   Terbalik, <x-certificate-status> menandai sertifikat yang
                   baru diterbitkan sebagai sudah kedaluwarsa, dan papan
                   ringkasan di halaman publik ikut menghitungnya begitu. */
                Forms\Components\DatePicker::make('expires_at')->label('Tanggal Berakhir')
                    ->afterOrEqual('issued_at')
                    ->validationMessages([
                        'after_or_equal' => 'Tanggal berakhir tidak boleh lebih awal daripada tanggal terbit.',
                    ]),
                Forms\Components\Toggle::make('is_active')->label('Tampilkan di website')->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('ujk_number')->label('No. UJK')->sortable()->searchable(),
            Tables\Columns\TextColumn::make('participant_name')->label('Peserta')->searchable()->wrap(),
            Tables\Columns\TextColumn::make('company_name')->label('Perusahaan')->searchable()->wrap()->toggleable(),
            Tables\Columns\TextColumn::make('certificate_number')->label('No. Sertifikat')->searchable(),
            Tables\Columns\TextColumn::make('qualification')->label('Kualifikasi')->toggleable(),
            Tables\Columns\TextColumn::make('expires_at')->date('d M Y')->label('Berakhir')->sortable(),
            Tables\Columns\IconColumn::make('is_active')->boolean()->label('Aktif'),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_active')->label('Status'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCertificateHolders::route('/'),
            'create' => Pages\CreateCertificateHolder::route('/create'),
            'edit' => Pages\EditCertificateHolder::route('/{record}/edit'),
        ];
    }
}
