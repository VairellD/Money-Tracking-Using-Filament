<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Category;

class ByCategories extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Monthly Transactions by Category';

    protected function getData(): array
    {
        $categories = Category::all();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $categoryNames = $categories->pluck('name')->toArray();

        // Initialize transaction count array
        $transactionCount = [];

        // Log current month and year for debugging
        // \Log::info('Current Month: ' . $currentMonth);
        // \Log::info('Current Year: ' . $currentYear);

        // Iterate over each category to get the transaction count
        foreach ($categories as $category) {
            $count = $category->transactions()
                // ->where('is_income', false)
                ->whereYear('transaction_date', $currentYear)
                ->whereMonth('transaction_date', $currentMonth)
                ->sum('amount');

            // Log category name and transaction count for debugging
            // \Log::info('Category: ' . $category->name . ' | Count: ' . $count);

            $transactionCount[] = $count;
        }

        // Define an array of colors
        $colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', 
            '#FF9F40', '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0',
            '#9966FF', '#FF9F40', '#FF6384', '#36A2EB', '#FFCE56',
            // Add more colors if you have more categories
        ];

        // Ensure there are enough colors for all categories
        if (count($categoryNames) > count($colors)) {
            $additionalColors = array_map(function () {
                return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
            }, range(0, count($categoryNames) - count($colors) - 1));

            $colors = array_merge($colors, $additionalColors);
        }

        $backgroundColors = array_slice($colors, 0, count($categoryNames));
        $borderColors = array_slice($colors, 0, count($categoryNames));

        $data = [
            'labels' => $categoryNames,
            'datasets' => [
                [
                    'label' => 'Expense Transactions',
                    'data' => $transactionCount,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 1,
                ],
            ],
        ];

        // Log the final data structure for debugging
        // \Log::info('Chart Data:', $data);

        return $data;
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
