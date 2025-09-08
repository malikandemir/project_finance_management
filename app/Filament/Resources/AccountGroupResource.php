<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountGroupResource\Pages;
use App\Models\AccountGroup;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountGroupResource extends Resource
{
    protected static ?string $model = AccountGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    
    protected static ?string $navigationGroup = 'Accounting';
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.AccountGroupResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.AccountGroupResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('filament::resources.fields.name'))
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::resources.fields.name'))
                    ->searchable(),
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccountGroups::route('/'),
            'create' => Pages\CreateAccountGroup::route('/create'),
            'edit' => Pages\EditAccountGroup::route('/{record}/edit'),
        ];
    }
}
