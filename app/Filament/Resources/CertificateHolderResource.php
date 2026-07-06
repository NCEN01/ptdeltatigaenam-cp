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
                Forms\Components\TextInput::make('ujk_number')->label('No. UJK')->maxLength(50),
                Forms\Components\TextInput::make('certificate_number')->label('No. Sertifikat')->maxLength(100),
                Forms\Components\TextInput::make('qualification')->label('Kualifikasi')->maxLength(200),
            ])->columns(2),
            Forms\Components\Section::make('Masa Berlaku')->schema([
                Forms\Components\DatePicker::make('issued_at')->label('Tanggal Terbit'),
                Forms\Components\DatePicker::make('expires_at')->label('Tanggal Berakhir'),
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
