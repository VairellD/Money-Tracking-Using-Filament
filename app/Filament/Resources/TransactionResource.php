<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;

class TransactionResource extends Resource
{

    
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('name')
                ->maxLength(255)
                ->required()
                ->label('Name'),

                Forms\Components\Select::make('is_income')
                ->options([
                    '1' => 'Income',
                    '0' => 'Expense',
                ])
                ->label('Type')
                ->required(),

                forms\Components\DatePicker::make('transaction_date')
                ->maxDate(now())
                ->label('Transaction Date'),

                forms\Components\TextInput::make('amount')
                ->required()
                ->label('Amount')
                ->numeric()
                ->default(0),

                forms\components\Select::make('category_id')
                ->relationship('category', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Category'),

                forms\Components\Select::make('account_id')
                ->relationship('account', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->label('Account'),

                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
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
                //
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('is_income')
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    '1' => 'Income',
                    '0' => 'Expense',
                })
                ->label('Type'),
                Tables\Columns\TextColumn::make('transaction_date') ->sortable()
                ->label('Trasaction Date'),
                Tables\Columns\TextColumn::make('category_id')
                ->formatStateUsing(function ($record) {
                    return $record->category->name ?? '-';
                })
                ->label('Category'),
                Tables\Columns\TextColumn::make('account_id')
                ->formatStateUsing(function ($record) {
                    return $record->account->name ?? '-';
                })
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                ->money('IDR'),
            ])
            ->filters([

                Tables\Filters\SelectFilter::make('is_income')
                ->options([
                    '1' => 'Income',
                    '0' => 'Expense',
                ])
                ->label('Type'),
                //
                Tables\Filters\SelectFilter::make('category_id')
                ->relationship('category', 'name')
                ->label('Category'),
                
                QueryBuilder::make('Transaction_date')
                ->constraints([
                    DateConstraint::make('transaction_date')]),
                ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                    ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
            RelationManagers\CategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    
}
