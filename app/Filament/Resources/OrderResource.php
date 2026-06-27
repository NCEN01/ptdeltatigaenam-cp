<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\RestrictsToPermission;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Response;

class OrderResource extends Resource
{
    use RestrictsToPermission;

    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Transaksi';

    protected static ?string $navigationLabel = 'Pesanan';

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?int $navigationSort = 2;

    protected static ?string $accessPermission = 'view_orders';

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Pesanan')->schema([
                Infolists\Components\TextEntry::make('order_number')->label('Nomor')->copyable(),
                Infolists\Components\TextEntry::make('status')->badge()->color(fn ($state) => match ($state) {
                    'paid' => 'success', 'pending' => 'warning',
                    'expired', 'cancelled', 'failed' => 'danger', default => 'gray',
                }),
                Infolists\Components\TextEntry::make('total_amount')->money('IDR'),
                Infolists\Components\TextEntry::make('paid_at')->dateTime('d M Y H:i')->placeholder('—'),
            ])->columns(4),
            Infolists\Components\Section::make('Pemesan')->schema([
                Infolists\Components\TextEntry::make('customer_name')->label('Nama'),
                Infolists\Components\TextEntry::make('customer_email')->label('Email')->copyable(),
                Infolists\Components\TextEntry::make('customer_phone')->label('Telepon'),
                Infolists\Components\TextEntry::make('customer_company')->label('Perusahaan')->placeholder('—'),
            ])->columns(2),
            Infolists\Components\Section::make('Layanan')->schema([
                Infolists\Components\TextEntry::make('service.title')->label('Layanan')->placeholder('—'),
                Infolists\Components\TextEntry::make('quantity')->label('Jumlah'),
                Infolists\Components\TextEntry::make('unit_price')->money('IDR'),
                Infolists\Components\TextEntry::make('subtotal')->money('IDR'),
                Infolists\Components\TextEntry::make('tax')->money('IDR'),
            ])->columns(3),
            Infolists\Components\Section::make('Transaksi Midtrans')->schema([
                Infolists\Components\RepeatableEntry::make('transactions')->schema([
                    Infolists\Components\TextEntry::make('payment_type')->label('Metode'),
                    Infolists\Components\TextEntry::make('transaction_status')->label('Status')->badge(),
                    Infolists\Components\TextEntry::make('gross_amount')->money('IDR'),
                    Infolists\Components\TextEntry::make('transaction_time')->dateTime('d M Y H:i'),
                ])->columns(4),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')->label('Nomor')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('customer_name')->label('Pemesan')->searchable(),
                Tables\Columns\TextColumn::make('service.title')->label('Layanan')->limit(30)->placeholder('—'),
                Tables\Columns\TextColumn::make('total_amount')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn ($state) => match ($state) {
                    'paid' => 'success', 'pending' => 'warning',
                    'expired', 'cancelled', 'failed' => 'danger', default => 'gray',
                })->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d M Y H:i')->label('Dibuat')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending', 'paid' => 'Lunas', 'expired' => 'Kedaluwarsa',
                    'cancelled' => 'Dibatalkan', 'failed' => 'Gagal', 'refunded' => 'Dikembalikan',
                ]),
                Tables\Filters\Filter::make('created_at')->form([
                    \Filament\Forms\Components\DatePicker::make('from')->label('Dari'),
                    \Filament\Forms\Components\DatePicker::make('until')->label('Sampai'),
                ])->query(function ($query, array $data) {
                    return $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                        ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d));
                }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Ekspor CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => static::exportCsv()),
            ])
            ->actions([Tables\Actions\ViewAction::make()]);
    }

    public static function exportCsv()
    {
        $filename = 'orders-'.now()->format('Ymd-His').'.csv';

        return Response::streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Nomor', 'Pemesan', 'Email', 'Layanan', 'Total', 'Status', 'Dibuat', 'Dibayar']);
            Order::with('service')->orderByDesc('created_at')->chunk(200, function ($orders) use ($out) {
                foreach ($orders as $o) {
                    fputcsv($out, [
                        $o->order_number, $o->customer_name, $o->customer_email,
                        $o->service?->title, $o->total_amount, $o->status,
                        $o->created_at?->format('Y-m-d H:i'), $o->paid_at?->format('Y-m-d H:i'),
                    ]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'view' => Pages\ViewOrder::route('/{record}'),
        ];
    }
}
