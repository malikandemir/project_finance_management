<?php

namespace App\Filament\Resources\UserTypeResource\RelationManagers;

use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    protected static ?string $recordTitleAttribute = 'description';
    
    protected static ?string $title = 'Transactions';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('entities.transactions');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('description')
                    ->label(__('entities.description'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->label(__('entities.amount'))
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('debit_credit')
                    ->label(__('entities.debit_credit'))
                    ->options([
                        Transaction::DEBIT => 'Debit',
                        Transaction::CREDIT => 'Credit',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('transaction_date')
                    ->label(__('entities.transaction_date'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        // Get the user record
        $user = $this->getOwnerRecord();
        
        return $table
            ->recordTitleAttribute('description')
            // Use a custom query to get transactions from all user accounts
            ->modifyQueryUsing(function (Builder $query) use ($user) {
                // Get all account IDs for this user
                $accountIds = $user->accounts()->pluck('id')->toArray();
                
                // We need to query the transactions table directly instead of through the relationship
                return Transaction::query()->whereIn('account_id', $accountIds);
            })
            ->columns([
                Tables\Columns\TextColumn::make('account.account_name')
                    ->label(__('entities.account'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('entities.description'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('entities.amount'))
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit_credit')
                    ->label(__('entities.debit_credit'))
                    ->formatStateUsing(function (int $state): string {
                        if ($state === Transaction::DEBIT) {
                            return 'Debit';
                        } elseif ($state === Transaction::CREDIT) {
                            return 'Credit';
                        } else {
                            return 'Unknown';
                        }
                    })
                    ->badge()
                    ->color(fn (int $state): string => $state === Transaction::DEBIT ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('balance_after_transaction')
                    ->label(__('entities.balance_after_transaction'))
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label(__('entities.transaction_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transactionGroup.description')
                    ->label(__('entities.transaction_group'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('entities.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('debit_credit')
                    ->label(__('entities.debit_credit'))
                    ->options([
                        Transaction::DEBIT => __('entities.debit'),
                        Transaction::CREDIT => __('entities.credit'),
                    ]),
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label(__('entities.from_date')),
                        Forms\Components\DatePicker::make('until')
                            ->label(__('entities.until_date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                // Transactions are typically created through business logic, not manually
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions for transactions
            ])
            ->defaultSort('transaction_date', 'desc');
    }
}
