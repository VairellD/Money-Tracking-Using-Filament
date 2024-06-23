<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Category;
use App\Models\Transaction;

use function PHPSTORM_META\map;

class ByCategories extends ChartWidget
{
    protected static ?int $sort = 4;
    // protected static ?string $heading = 'Chart';

    protected function getData(): array
    {
        $categories = Category::all();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $categoryNames = $categories->pluck('name')->toArray();
        $transactionCount = $categories->map(function ($category) use ($currentMonth, $currentYear) {
            return $category->transactions()
                ->where('is_income', false)
                ->whereYear('transaction_date', $currentYear)
                ->whereMonth('transaction_date', $currentMonth)
                ->count();
        })->toArray();

        return [
            'labels' => $categoryNames,
            'datasets' => [
                [
                    'label' => 'Expense Transactions',
                    'data' => $transactionCount,
                    'backgroundColor' => '#FF6384',
                    'borderColor' => '#FF6384',
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bubble';
    }
}
