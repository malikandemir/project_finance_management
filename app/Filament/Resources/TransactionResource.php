<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\TransactionGroup;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationGroup = 'Accounting';
    
    protected static ?int $navigationSort = 3;
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.TransactionResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.TransactionResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('account_id')
                    ->label(__('filament::resources.fields.account'))
                    ->relationship('account', 'account_name')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label(__('filament::resources.fields.amount'))
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
                    ->label(__('filament::resources.fields.user'))
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('transaction_group_id')
                    ->label(__('filament::resources.fields.transaction_group'))
                    ->relationship('transactionGroup', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament::resources.fields.group_name'))
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament::resources.fields.description'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('group_date')
                            ->label(__('filament::resources.fields.group_date'))
                            ->required()
                            ->default(now()),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn() => auth()->id()),
                    ])
                    ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                        return $action
                            ->modalHeading(__('filament::resources.actions.create_transaction_group'))
                            ->modalWidth('lg');
                    }),
                Forms\Components\TextInput::make('balance_after_transaction')
                    ->label(__('filament::resources.fields.balance_after_transaction'))
                    ->numeric()
                    ->step(0.01),
                Forms\Components\DateTimePicker::make('transaction_date')
                    ->label(__('filament::resources.fields.transaction_date'))
                    ->required()
                    ->default(now()),
                Forms\Components\Textarea::make('description')
                    ->label(__('filament::resources.fields.description'))
                    ->nullable()
                    ->columnSpan(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('transaction_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('account.account_name')
                    ->label(__('filament::resources.fields.account'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('filament::resources.fields.amount'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('debit_credit')
                    ->label(__('filament::resources.fields.transaction_type'))
                    ->badge()
                    ->color(fn (int $state): string => match ($state) {
                        Transaction::DEBIT => 'success',
                        Transaction::CREDIT => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(function (int $state): string {
                        if ($state === Transaction::DEBIT) {
                            return 'Debit';
                        } elseif ($state === Transaction::CREDIT) {
                            return 'Credit';
                        } else {
                            return 'Unknown';
                        }
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament::resources.fields.user'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('balance_after_transaction')
                    ->label(__('filament::resources.fields.balance_after_transaction'))
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label(__('filament::resources.fields.transaction_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transactionGroup.name')
                    ->label(__('filament::resources.fields.transaction_group'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament::resources.fields.description'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament::resources.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament::resources.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_group_id')
                    ->relationship('transactionGroup', 'name')
                    ->label(__('filament::resources.fields.transaction_group'))
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('account_id')
                    ->relationship('account', 'account_name')
                    ->label(__('filament::resources.fields.account')),
                Tables\Filters\SelectFilter::make('debit_credit')
                    ->options([
                        Transaction::DEBIT => 'Debit',
                        Transaction::CREDIT => 'Credit',
                    ])
                    ->label(__('filament::resources.fields.transaction_type')),
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

    public static function getRelations(): array
    {
        return [
            //
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
