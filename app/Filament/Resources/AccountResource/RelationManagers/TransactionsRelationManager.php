<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

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
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('debit_credit')
                    ->label(__('filament::resources.fields.transaction_type'))
                    ->options([
                        Transaction::DEBIT => 'Debit',
                        Transaction::CREDIT => 'Credit',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('balance_after_transaction')
                    ->label('Balance After Transaction')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
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
                    ->color(fn (int $state): string => $state === Transaction::DEBIT ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after_transaction')
                    ->label('Balance After')
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
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
