<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Administration';
    
    protected static ?int $navigationSort = 20;
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.UserResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.UserResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament::resources.sections.user_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament::resources.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('filament::resources.fields.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label(__('filament::resources.fields.password'))
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('filament::resources.fields.phone'))
                            ->tel()
                            ->maxLength(20),
                    ])->columns(2),
                    
                Forms\Components\Section::make(__('filament::resources.sections.company_and_role'))
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label(__('filament::resources.fields.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('position')
                            ->label(__('filament::resources.fields.position'))
                            ->maxLength(100),
                        Forms\Components\Select::make('roles')
                            ->label(__('filament::resources.fields.roles'))
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament::resources.fields.is_active'))
                            ->default(true)
                            ->required(),
                        Forms\Components\TextInput::make('revenue_percentage')
                            ->label(__('filament::resources.fields.revenue_percentage'))
                            ->numeric()
                            ->suffix('%')
                            ->maxValue(100)
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->default(0.00)
                            ->helperText(__('filament::resources.help_texts.revenue_percentage')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::resources.fields.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament::resources.fields.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('filament::resources.fields.company'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('position')
                    ->label(__('filament::resources.fields.position'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label(__('filament::resources.fields.roles'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super-admin' => 'danger',
                        'company-owner' => 'warning',
                        'project-manager' => 'success',
                        default => 'primary',
                    })
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament::resources.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament::resources.fields.phone'))
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('revenue_percentage')
                    ->label(__('filament::resources.fields.revenue_percentage'))
                    ->numeric()
                    ->suffix('%')
                    ->sortable()
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
                Tables\Filters\SelectFilter::make('company')
                    ->label(__('filament::resources.fields.company'))
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('roles')
                    ->label(__('filament::resources.fields.roles'))
                    ->relationship('roles', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament::resources.fields.active_status')),
                Tables\Filters\Filter::make('revenue_percentage')
                    ->form([
                        Forms\Components\TextInput::make('revenue_percentage_from')
                            ->label(__('filament::resources.fields.minimum_percentage'))
                            ->numeric()
                            ->placeholder('0'),
                        Forms\Components\TextInput::make('revenue_percentage_to')
                            ->label(__('filament::resources.fields.maximum_percentage'))
                            ->numeric()
                            ->placeholder('100'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['revenue_percentage_from'],
                                fn (Builder $query, $value): Builder => $query->where('revenue_percentage', '>=', $value),
                            )
                            ->when(
                                $data['revenue_percentage_to'],
                                fn (Builder $query, $value): Builder => $query->where('revenue_percentage', '<=', $value),
                            );
                    })
                    ->label(__('filament::resources.fields.revenue_percentage')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CreatedCompaniesRelationManager::class,
            RelationManagers\CreatedProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    
    public static function canCreate(): bool
    {
        return auth()->user()->can('create_user');
    }
    
    public static function canEdit(Model $record): bool
    {
        return auth()->user()->can('edit_user');
    }
    
    public static function canDelete(Model $record): bool
    {
        return auth()->user()->can('delete_user');
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_users');
    }
}
