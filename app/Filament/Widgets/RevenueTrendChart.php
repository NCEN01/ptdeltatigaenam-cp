<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class RevenueTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan (12 Bulan)';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return (bool) auth()->user()?->can('view_orders');
    }

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $labels[] = $month->translatedFormat('M Y');
            $values[] = (float) Order::where('status', 'paid')
                ->whereYear('paid_at', $month->year)
                ->whereMonth('paid_at', $month->month)
                ->sum('total_amount');
        }

        return [
            'datasets' => [[
                'label' => 'Pendapatan (Rp)',
                'data' => $values,
                'borderColor' => '#0A2A5E',
                'backgroundColor' => 'rgba(10, 42, 94, 0.1)',
                'fill' => true,
                'tension' => 0.35,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
