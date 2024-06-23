<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class IncomeOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {
        $Incomecount = Transaction::where('is_income', '1')->where('user_id', auth()->id())->sum('amount');
        $Expensecount = Transaction::where('is_income', '0')->where('user_id', auth()->id())->sum('amount');

        $formattedIncomeCount = 'IDR ' . number_format($Incomecount, 2, ',', '.');
        $formattedExpenseCount = 'IDR ' . number_format($Expensecount, 2, ',', '.');

        return [
            
            //
            Stat::make('Income', $formattedIncomeCount),
            Stat::make('Expense', $formattedExpenseCount),
            Stat::make('Balance', 'IDR ' . number_format($Incomecount - $Expensecount, 2, ',', '.')),
        ];
    }
}
