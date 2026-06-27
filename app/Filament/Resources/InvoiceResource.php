<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Models\Invoice;
use App\Services\InvoiceService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InvoiceResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Kemitraan';

    protected static ?string $navigationLabel = 'Invoice';

    protected static ?string $modelLabel = 'Invoice';

    protected static ?int $navigationSort = 4;

    protected static ?string $accessPermission = 'manage_invoices';

    public const STATUSES = [
        'draft' => 'Draft',
        'terkirim' => 'Terkirim',
        'lunas' => 'Lunas',
        'jatuh_tempo' => 'Jatuh Tempo',
        'dibatalkan' => 'Dibatalkan',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ditagihkan Kepada')->schema([
                Forms\Components\Select::make('partnership_registration_id')
                    ->relationship('registration', 'registration_number')
                    ->label('Pendaftaran Kemitraan')->searchable()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($reg = \App\Models\PartnershipRegistration::find($state)) {
                            $set('bill_to_company', $reg->company_name);
                            $set('bill_to_address', $reg->company_address);
                            $set('bill_to_pic', $reg->pic_name);
                            $set('bill_to_email', $reg->email);
                        }
                    })->live(),
                Forms\Components\TextInput::make('bill_to_company')->label('Perusahaan')->required(),
                Forms\Components\TextInput::make('bill_to_pic')->label('PIC'),
                Forms\Components\TextInput::make('bill_to_email')->email()->label('Email'),
                Forms\Components\Textarea::make('bill_to_address')->label('Alamat')->rows(2)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Item')->schema([
                Forms\Components\Repeater::make('items')->relationship()->label('Rincian')
                    ->schema([
                        Forms\Components\TextInput::make('description')->label('Deskripsi')->required()->columnSpan(2),
                        Forms\Components\TextInput::make('quantity')->label('Qty')->numeric()->default(1)->required()->live(onBlur: true),
                        Forms\Components\TextInput::make('unit_price')->label('Harga Satuan')->numeric()->default(0)->required()->prefix('Rp')->live(onBlur: true),
                        Forms\Components\Hidden::make('sort_order')->default(0),
                    ])->columns(4)->defaultItems(1)
                    ->reorderable()->collapsible(),
            ]),

            Forms\Components\Section::make('Total & Status')->schema([
                Forms\Components\TextInput::make('tax')->label('Pajak (Rp)')->numeric()->default(0)->prefix('Rp'),
                Forms\Components\Select::make('status')->options(self::STATUSES)->default('draft')->required(),
                Forms\Components\DatePicker::make('issued_date')->label('Tanggal Terbit')->default(now()),
                Forms\Components\DatePicker::make('due_date')->label('Jatuh Tempo'),
                Forms\Components\Textarea::make('notes')->label('Catatan')->rows(2)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('Nomor')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('bill_to_company')->label('Perusahaan')->searchable(),
                Tables\Columns\TextColumn::make('total')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->formatStateUsing(fn ($s) => self::STATUSES[$s] ?? $s)
                    ->color(fn ($state) => match ($state) {
                        'lunas' => 'success', 'draft' => 'gray', 'jatuh_tempo', 'dibatalkan' => 'danger', default => 'warning',
                    }),
                Tables\Columns\IconColumn::make('file_path')->label('PDF')->boolean()
                    ->trueIcon('heroicon-o-document-check')->falseIcon('heroicon-o-minus'),
                Tables\Columns\TextColumn::make('issued_date')->date('d M Y')->label('Terbit')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(self::STATUSES),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generatePdf')
                    ->label('Generate PDF')->icon('heroicon-o-document-arrow-down')->color('gold')
                    ->action(function (Invoice $record) {
                        app(InvoiceService::class)->recalculate($record);
                        app(InvoiceService::class)->generatePdf($record);
                        Notification::make()->title('PDF dibuat')->success()->send();
                    }),
                Tables\Actions\Action::make('downloadPdf')
                    ->label('Unduh')->icon('heroicon-o-arrow-down-tray')
                    ->visible(fn (Invoice $r) => filled($r->file_path))
                    ->url(fn (Invoice $r) => \Illuminate\Support\Facades\Storage::disk('public')->url($r->file_path), shouldOpenInNewTab: true),
            ])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
            'view' => Pages\ViewInvoice::route('/{record}'),
        ];
    }
}
