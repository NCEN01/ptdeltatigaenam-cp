<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Transaksi Terbaru';

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can('view_transactions');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Transaction::query()->with('order')->latest())
            ->paginated([5, 10])
            ->columns([
                Tables\Columns\TextColumn::make('midtrans_order_id')->label('Midtrans ID')->limit(20),
                Tables\Columns\TextColumn::make('order.customer_name')->label('Pemesan'),
                Tables\Columns\TextColumn::make('payment_type')->label('Metode')->badge(),
                Tables\Columns\TextColumn::make('gross_amount')->money('IDR'),
                Tables\Columns\TextColumn::make('transaction_status')->label('Status')->badge()
                    ->color(fn ($state) => match ($state) {
                        'settlement', 'capture' => 'success', 'pending' => 'warning',
                        'expire', 'cancel', 'deny' => 'danger', default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Waktu'),
            ]);
    }
}
