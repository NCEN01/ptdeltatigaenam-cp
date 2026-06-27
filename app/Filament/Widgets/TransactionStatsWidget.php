<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\PartnershipRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can('view_orders');
    }

    protected function getStats(): array
    {
        $revenue = Order::where('status', 'paid')->sum('total_amount');
        $paidCount = Order::where('status', 'paid')->count();
        $pending = Order::where('status', 'pending')->count();
        $newLeads = PartnershipRegistration::where('status', 'baru')->count();
        $unpaidInvoices = Invoice::whereNotIn('status', ['lunas', 'dibatalkan'])->count();

        return [
            Stat::make('Total Pendapatan', 'Rp '.number_format((float) $revenue, 0, ',', '.'))
                ->description("{$paidCount} pesanan lunas")
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Pesanan Pending', $pending)
                ->description('Menunggu pembayaran')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Lead Kemitraan Baru', $newLeads)
                ->description("{$unpaidInvoices} invoice belum lunas")
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary'),
        ];
    }
}
