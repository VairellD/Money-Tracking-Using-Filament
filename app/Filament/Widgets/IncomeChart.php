<?php

namespace App\Filament\Widgets;





use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use App\Models\Transaction;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;


class IncomeChart extends LineChartWidget
{

    protected static ?int $sort = 5;
    
    protected function getData(): array
    {
        
        // Initialize an array to hold the sum of income for each month of the year
        $monthlyIncomeSum = [];

        // Iterate over all 12 months of the year
        for ($month = 1; $month <= 12; $month++) {
            // Get the start and end dates for the current month
            $startDate = \Carbon\Carbon::create(now()->year, $month, 1)->startOfMonth();
            $endDate = $startDate->endOfMonth();

            // Fetching income data for the current month
            $incomeSum = Transaction::where('is_income', '1')
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', now()->year)
                ->where('user_id', auth()->id())
                ->sum('amount');

            $expenseSum = Transaction::where('is_income', '0')
            ->whereMonth('transaction_date', $month)
            ->whereYear('transaction_date', now()->year)
            ->where('user_id', auth()->id())
            ->sum('amount');

            // Add the sum to the array
            $monthlyExpenseSum[] = $expenseSum;
            $monthlyIncomeSum[] = $incomeSum;
        }

        // Format month and year labels for x-axis
        $labels = collect(range(1, 12))->map(function ($month) {
            return \Carbon\Carbon::create(now()->year, $month, 1)->format('M');
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Income Count',
                    'data' => $monthlyIncomeSum, // Income sums for each month
                ],
                [
                    'label' => 'Expense Count',
                    'data' => $monthlyExpenseSum,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#9BD0F5',
                    'colors' =>"#123123", // Expense sums for each month 
                ],
            ],
        ];
    }
}