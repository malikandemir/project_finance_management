<?php

namespace App\Filament\Resources\UserTypeResource\RelationManagers;

use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountsRelationManager extends RelationManager
{
    protected static string $relationship = 'accounts';

    protected static ?string $recordTitleAttribute = 'account_name';

    public static function getTitle(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): string
    {
        return __('entities.accounts');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('account_name')
                    ->label(__('entities.account_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('balance')
                    ->label(__('entities.balance'))
                    ->numeric()
                    ->disabled()
                    ->default(0),
                Forms\Components\Select::make('account_group_id')
                    ->label(__('entities.account_group'))
                    ->relationship('accountGroup', 'name')
                    ->required(),
                Forms\Components\Select::make('account_uniform_id')
                    ->label(__('entities.uniform_chart_of_account'))
                    ->relationship('uniformChartOfAccount', 'tr_name')
                    ->searchable()
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->label(__('entities.account_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('entities.balance'))
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('accountGroup.name')
                    ->label(__('entities.account_group'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('uniformChartOfAccount.tr_name')
                    ->label(__('entities.uniform_chart_of_account'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('entities.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
