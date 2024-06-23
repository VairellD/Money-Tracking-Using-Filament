<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use BladeUI\Icons\Factory as IconFactory;

class IncomeOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected function getStats(): array
    {

        // $heroicons = app(IconFactory::class)->all();
        // Log::info('Available Heroicons:', $heroicons);

        $Incomecount = Transaction::where('is_income', '1')->where('user_id', auth()->id())->sum('amount');
        $latestIncome = Transaction::where('is_income', '1')->where('user_id', auth()->id())->latest()->first();
        $Expensecount = Transaction::where('is_income', '0')->where('user_id', auth()->id())->sum('amount');

        $formattedIncomeCount = 'IDR ' . number_format($Incomecount, 2, ',', '.');
        $formattedLatestIncome = 'IDR ' . number_format($latestIncome->amount, 2, ',', '.');
        $formattedExpenseCount = 'IDR ' . number_format($Expensecount, 2, ',', '.');

        return [
            
            //
            Stat::make('Income', $formattedIncomeCount)
            ->description('Latest ' . $formattedLatestIncome),
            Stat::make('Expense', $formattedExpenseCount),
            Stat::make('Balance', 'IDR ' . number_format($Incomecount - $Expensecount, 2, ',', '.')),
        ];
    }
}
