<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

use function Laravel\Prompts\select;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageTable;


class LatestTransaction extends BaseWidget
{
    
    public static ?int $sort = 3;

    public string|array|int $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                // ...
                Transaction::query()
                    ->where('user_id', auth()->id())
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                
                // ...
                Tables\Columns\TextColumn::Make('name'),
                Tables\Columns\TextColumn::Make('amount'),
                Tables\Columns\TextColumn::Make('is_income')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    '1' => 'Income',
                    '0' => 'Expense',
                }),
                Tables\Columns\TextColumn::make('category_id')
                ->formatStateUsing(function ($record) {
                    return $record->category->name ?? '-';
                }),
                Tables\Columns\TextColumn::Make('transaction_date')
                ->columnSpan('full'),
            ])
            ->filters([

                Tables\Filters\SelectFilter::make('is_income')
                ->options([
                    '1' => 'Income',
                    '0' => 'Expense',
                ])
                ->label('Type'),
                //,
            ]);
    }

}
