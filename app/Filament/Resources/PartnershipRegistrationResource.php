<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\PartnershipRegistrationResource\Pages;
use App\Models\Invoice;
use App\Models\PartnershipRegistration;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PartnershipRegistrationResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = PartnershipRegistration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?string $navigationGroup = 'Kemitraan';

    protected static ?string $navigationLabel = 'Pendaftaran Kemitraan';

    protected static ?string $modelLabel = 'Pendaftaran Kemitraan';

    protected static ?int $navigationSort = 3;

    protected static ?string $accessPermission = 'manage_partnership_registrations';

    public const STATUSES = [
        'baru' => 'Baru',
        'dihubungi' => 'Dihubungi',
        'meeting_dijadwalkan' => 'Meeting Dijadwalkan',
        'penawaran_dikirim' => 'Penawaran Dikirim',
        'invoice_diterbitkan' => 'Invoice Diterbitkan',
        'lunas' => 'Lunas',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'baru')->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Perusahaan')->schema([
                Forms\Components\TextInput::make('registration_number')->label('Nomor')->disabled()->dehydrated(false),
                Forms\Components\TextInput::make('company_name')->label('Nama Perusahaan')->required(),
                Forms\Components\Textarea::make('company_address')->label('Alamat')->required()->rows(2)->columnSpanFull(),
                Forms\Components\TextInput::make('pic_name')->label('Nama PIC')->required(),
                Forms\Components\TextInput::make('pic_position')->label('Jabatan PIC'),
                Forms\Components\TextInput::make('phone')->label('No. Telp/WhatsApp')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
            ])->columns(2),
            Forms\Components\Section::make('Paket & Jadwal')->schema([
                Forms\Components\Select::make('partnership_package_id')->relationship('package', 'slug')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name)->label('Paket')->searchable(),
                Forms\Components\DateTimePicker::make('preferred_meeting_at')->label('Jadwal Diinginkan'),
                Forms\Components\DateTimePicker::make('alternative_meeting_at')->label('Jadwal Alternatif'),
                Forms\Components\Textarea::make('notes')->label('Catatan')->rows(3)->columnSpanFull(),
            ])->columns(3),
            Forms\Components\Section::make('Pemrosesan')->schema([
                Forms\Components\Select::make('status')->options(self::STATUSES)->default('baru')->required(),
                Forms\Components\Select::make('assigned_to')->label('Ditangani Oleh')
                    ->options(fn () => User::whereHas('roles', fn ($q) => $q->whereIn('name', ['admin_transaksi', 'super_admin']))->pluck('name', 'id'))
                    ->searchable(),
                Forms\Components\Toggle::make('is_read')->label('Sudah Dibaca'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('registration_number')->label('Nomor')->searchable(),
                Tables\Columns\TextColumn::make('company_name')->label('Perusahaan')->searchable(),
                Tables\Columns\TextColumn::make('pic_name')->label('PIC'),
                Tables\Columns\TextColumn::make('package.name')->label('Paket')->badge()->placeholder('—'),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($state) => self::STATUSES[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'baru' => 'warning', 'lunas', 'selesai' => 'success',
                        'dibatalkan' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('assignee.name')->label('Penanggung Jawab')->placeholder('—'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y')->label('Masuk')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(self::STATUSES),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('createInvoice')
                    ->label('Buat Invoice')
                    ->icon('heroicon-o-document-plus')
                    ->color('gold')
                    ->url(fn (PartnershipRegistration $record) => InvoiceResource::getUrl('create', [
                        'registration' => $record->id,
                    ]))
                    ->visible(fn () => auth()->user()?->can('manage_invoices')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Pendaftaran Kemitraan Secara Massal')
                        ->modalDescription('Apakah Anda yakin ingin menghapus pendaftaran kemitraan yang dipilih secara permanen?'),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPartnershipRegistrations::route('/'),
            'create' => Pages\CreatePartnershipRegistration::route('/create'),
            'edit' => Pages\EditPartnershipRegistration::route('/{record}/edit'),
            'view' => Pages\ViewPartnershipRegistration::route('/{record}'),
        ];
    }
}
