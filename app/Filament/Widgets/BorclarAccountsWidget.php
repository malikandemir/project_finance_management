<?php

namespace App\Filament\Widgets;

use App\Models\Account;
use App\Models\TheUniformChartOfAccount;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class BorclarAccountsWidget extends BaseWidget
{
    protected static ?int $sort = 4;
    
    protected int | string | array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        // Calculate total balance
        $totalBalance = User::query()
            ->join('accounts', 'accounts.user_id', '=', 'users.id')
            ->join('the_uniform_chart_of_accounts', 'the_uniform_chart_of_accounts.id', '=', 'accounts.account_uniform_id')
            ->where('the_uniform_chart_of_accounts.number', '=', '320')
            ->sum('accounts.balance');
            
        return $table
            ->heading(__('filament::widgets.borclar_accounts.heading', ['total' => number_format($totalBalance, 2)]))
            ->query(
                // Get users with uniform account 320 (Borçlar)
                User::query()
                    ->select([
                        'users.id',
                        'users.name as user_name',
                        'accounts.balance',
                        'latest_transactions.last_transaction_date'
                    ])
                    ->join('accounts', 'accounts.user_id', '=', 'users.id')
                    ->join('the_uniform_chart_of_accounts', 'the_uniform_chart_of_accounts.id', '=', 'accounts.account_uniform_id')
                    ->leftJoinSub(
                        DB::table('transactions')
                            ->select('account_id', DB::raw('MAX(transaction_date) as last_transaction_date'))
                            ->groupBy('account_id'),
                        'latest_transactions',
                        'latest_transactions.account_id', '=', 'accounts.id'
                    )
                    ->where('the_uniform_chart_of_accounts.number', '=', '320')
                    ->orderBy('accounts.balance', 'desc')
            )
            ->columns([
                TextColumn::make('user_name')
                    ->label(__('filament::widgets.fields.user_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('balance')
                    ->label(__('filament::widgets.fields.total_balance'))
                    ->money('TRY')
                    ->sortable(),
                TextColumn::make('last_transaction_date')
                    ->label(__('filament::widgets.fields.last_transaction_date'))
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
