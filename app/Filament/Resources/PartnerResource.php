<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Forms\Components\MediaUpload;
use App\Filament\Resources\PartnerResource\Pages;
use App\Models\Partner;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Partner::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Mitra (Logo)';

    protected static ?string $modelLabel = 'Mitra';

    protected static ?string $accessPermission = 'manage_partners';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama')->required()->maxLength(200),
            Forms\Components\TextInput::make('registration_number')->label('No. Registrasi')->maxLength(100)
                ->placeholder('Contoh: 8141312')->helperText('Nomor registrasi mitra yang tampil di bawah logo pada halaman depan.'),
            Forms\Components\TextInput::make('website_url')->label('Website')->url()->maxLength(500),
            MediaUpload::for('logo', 'logo', 'partners')->label('Logo'),
            // Situs mengurutkan mitra dengan sort_order, tetapi kolomnya tidak
            // pernah ada di form ini: setiap mitra baru masuk dengan nilai 0 dan
            // urutannya tidak bisa diatur sama sekali dari CMS.
            Forms\Components\TextInput::make('sort_order')->label('Urutan')->numeric()->default(0)
                ->helperText('Makin kecil makin depan. Bisa juga digeser langsung dari daftarnya.'),
            Forms\Components\Toggle::make('is_active')->label('Aktif')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        // Diurutkan sama seperti di situs, dan bisa digeser dengan menyeret
        // barisnya. Sebelumnya daftar ini urut waktu dibuat, sehingga susunan
        // di sini tidak ada hubungannya dengan susunan logo di halaman.
        return $table->defaultSort('sort_order')->reorderable('sort_order')->columns([
            Tables\Columns\ImageColumn::make('logo')->disk('public')->label('')->size(80),
            Tables\Columns\TextColumn::make('name')->label('Nama')->searchable(),
            Tables\Columns\TextColumn::make('registration_number')->label('No. Registrasi')->searchable()->placeholder('—'),
            Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartners::route('/'),
            'create' => Pages\CreatePartner::route('/create'),
            'edit' => Pages\EditPartner::route('/{record}/edit'),
        ];
    }
}