<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TransactionResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Transaksi Midtrans';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?int $navigationSort = 3;

    protected static ?string $accessPermission = 'view_transactions';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Detail Transaksi')->schema([
                Infolists\Components\TextEntry::make('midtrans_order_id')->label('Midtrans Order ID')->copyable(),
                Infolists\Components\TextEntry::make('transaction_id')->label('Transaction ID')->copyable(),
                Infolists\Components\TextEntry::make('payment_type')->label('Metode'),
                Infolists\Components\TextEntry::make('transaction_status')->label('Status')->badge(),
                Infolists\Components\TextEntry::make('gross_amount')->money('IDR'),
                Infolists\Components\TextEntry::make('bank')->placeholder('—'),
                Infolists\Components\TextEntry::make('va_number')->label('VA Number')->placeholder('—'),
                Infolists\Components\TextEntry::make('transaction_time')->dateTime('d M Y H:i')->placeholder('—'),
                Infolists\Components\TextEntry::make('settlement_time')->dateTime('d M Y H:i')->placeholder('—'),
            ])->columns(3),
            Infolists\Components\Section::make('Pesanan Terkait')->schema([
                Infolists\Components\TextEntry::make('order.order_number')->label('Nomor Pesanan'),
                Infolists\Components\TextEntry::make('order.customer_name')->label('Pemesan'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('midtrans_order_id')->label('Midtrans ID')->searchable()->limit(24),
                Tables\Columns\TextColumn::make('order.customer_name')->label('Pemesan')->searchable(),
                Tables\Columns\TextColumn::make('payment_type')->label('Metode')->badge(),
                Tables\Columns\TextColumn::make('gross_amount')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('transaction_status')->label('Status')->badge()->color(fn ($state) => match ($state) {
                    'settlement', 'capture' => 'success', 'pending' => 'warning',
                    'expire', 'cancel', 'deny' => 'danger', default => 'gray',
                }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Waktu')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_status')->options([
                    'settlement' => 'Settlement', 'capture' => 'Capture', 'pending' => 'Pending',
                    'expire' => 'Expire', 'cancel' => 'Cancel', 'deny' => 'Deny',
                ]),
            ])
            ->actions([Tables\Actions\ViewAction::make()]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
