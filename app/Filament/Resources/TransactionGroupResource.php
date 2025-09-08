<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionGroupResource\Pages;
use App\Filament\Resources\TransactionGroupResource\RelationManagers;
use App\Models\TransactionGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionGroupResource extends Resource
{
    protected static ?string $model = TransactionGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';
    
    protected static ?string $navigationGroup = 'Accounting';
    
    protected static ?int $navigationSort = 2;
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.TransactionGroupResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.TransactionGroupResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Forms\Components\Select::make('user_id')
                    ->label(__('filament::resources.fields.user'))
                    ->relationship('user', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::resources.fields.group_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('filament::resources.fields.description'))
                    ->limit(50)
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('group_date')
                    ->label(__('filament::resources.fields.group_date'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('filament::resources.fields.created_by'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label(__('filament::resources.fields.transactions')),
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
            'index' => Pages\ListTransactionGroups::route('/'),
            'create' => Pages\CreateTransactionGroup::route('/create'),
            'edit' => Pages\EditTransactionGroup::route('/{record}/edit'),
        ];
    }
}
