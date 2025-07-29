<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\Company;
use App\Models\TheUniformChartOfAccount;
use App\Models\Transaction;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CompanyNegativeBalancesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        // Calculate total negative balance
        $totalNegativeBalance = Company::query()
            ->join('users', 'users.id', '=', 'companies.owner_id')
            ->join('accounts', 'accounts.user_id', '=', 'users.id')
            ->join('the_uniform_chart_of_accounts', 'the_uniform_chart_of_accounts.id', '=', 'accounts.account_uniform_id')
            ->where('the_uniform_chart_of_accounts.number', '=', '120')
            ->where('accounts.balance', '>', 0)
            ->sum('accounts.balance');
            
        return $table
            ->heading('Alacaklar - Toplam: ' . number_format($totalNegativeBalance, 2) . ' TRY')
            ->query(
                // Get companies with negative account balances for uniform 120 (Customers)
                // Using a subquery approach to avoid GROUP BY issues
                Company::query()
                    ->select([
                        'companies.id',
                        'companies.name as company_name',
                        'users.name as owner_name',
                        'accounts.balance',
                        'latest_transactions.last_transaction_date'
                    ])
                    ->join('users', 'users.id', '=', 'companies.owner_id')
                    ->join('accounts', 'accounts.user_id', '=', 'users.id')
                    ->join('the_uniform_chart_of_accounts', 'the_uniform_chart_of_accounts.id', '=', 'accounts.account_uniform_id')
                    ->leftJoinSub(
                        DB::table('transactions')
                            ->select('account_id', DB::raw('MAX(transaction_date) as last_transaction_date'))
                            ->groupBy('account_id'),
                        'latest_transactions',
                        'latest_transactions.account_id', '=', 'accounts.id'
                    )
                    ->where('the_uniform_chart_of_accounts.number', '=', '120')
                    ->where('accounts.balance', '>', 0)
                    ->orderBy('accounts.balance', 'asc')
            )
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('owner_name')
                    ->label('Owner Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('balance')
                    ->label('Balance')
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('last_transaction_date')
                    ->label('Last Transaction Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                // Add filters if needed
            ])
            ->actions([
                // Add actions if needed
            ])
            ->bulkActions([
                // Add bulk actions if needed
            ]);
    }
    
    public static function canView(): bool
    {
        // Only show this widget to super-admin users
        return auth()->user()->hasRole('super-admin');
    }
}
