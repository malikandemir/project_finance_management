<?php

namespace App\Filament\Resources\TransactionGroupResource\RelationManagers;

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
    protected static string $relationship = 'transactions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('account_id')
                    ->relationship('account', 'account_name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->step(0.01),
                Forms\Components\Select::make('debit_credit')
                    ->label(__('filament::resources.fields.transaction_type'))
                    ->options([
                        Transaction::DEBIT => 'Debit',
                        Transaction::CREDIT => 'Credit',
                    ])
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\TextInput::make('balance_after_transaction')
                    ->numeric()
                    ->step(0.01),
                Forms\Components\DateTimePicker::make('transaction_date')
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('description')
                    ->nullable()
                    ->columnSpan(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('account.account_name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit_credit')
                    ->badge()
                    ->formatStateUsing(function (int $state): string {
                        if ($state === Transaction::DEBIT) {
                            return 'Debit';
                        } elseif ($state === Transaction::CREDIT) {
                            return 'Credit';
                        } else {
                            return 'Unknown';
                        }
                    })
                    ->color(fn (int $state): string => match ($state) {
                        Transaction::DEBIT => 'success',
                        Transaction::CREDIT => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after_transaction')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
