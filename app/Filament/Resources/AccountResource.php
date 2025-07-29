<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationGroup = 'Accounting';
    
    protected static ?int $navigationSort = 10;
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.AccountResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.AccountResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament::resources.sections.account_information'))
                    ->schema([
                        Forms\Components\TextInput::make('account_name')
                            ->label(__('filament::resources.fields.account_name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('balance')
                            ->label(__('filament::resources.fields.balance'))
                            ->required()
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
                    
                Forms\Components\Section::make(__('filament::resources.sections.account_relationships'))
                    ->schema([
                        Forms\Components\Select::make('account_group_id')
                            ->label(__('filament::resources.fields.account_group'))
                            ->relationship('accountGroup', 'name')
                            ->required(),
                        Forms\Components\Select::make('account_uniform_id')
                            ->label(__('filament::resources.fields.uniform_account'))
                            ->relationship('uniformChartOfAccount', 'number')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->number} - {$record->en_name}")
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label(__('filament::resources.fields.account_owner'))
                            ->helperText(__('filament::resources.help_texts.account_owner'))
                            ->searchable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('balance', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('account_name')
                    ->label(__('filament::resources.fields.account_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('filament::resources.fields.balance'))
                    ->money('TRY')
                    ->sortable(),
                Tables\Columns\TextColumn::make('accountGroup.name')
                    ->label(__('filament::resources.fields.account_group'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('uniformChartOfAccount.number')
                    ->label(__('filament::resources.fields.uniform_account'))
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->uniformChartOfAccount) {
                            return "{$record->uniformChartOfAccount->number} - {$record->uniformChartOfAccount->en_name}";
                        }
                        return $state;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament::resources.fields.account_owner'))
                    ->sortable(),
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
                //
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
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
