<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserTypeResource\Pages;
use App\Filament\Resources\UserTypeResource\RelationManagers;
use App\Models\Account;
use App\Models\TheUniformChartOfAccount;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserTypeResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Cariler';
    
    protected static ?int $navigationSort = 1;
    
    // Hide this resource from navigation since we have dedicated resources
    protected static bool $shouldRegisterNavigation = false;
    
    public static function getModelLabel(): string
    {
        return __('entities.current_accounts');
    }

    public static function getPluralModelLabel(): string
    {
        return __('entities.current_accounts');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('entities.user_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('entities.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('entities.email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label(__('entities.password'))
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('entities.phone'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('company_id')
                            ->label(__('entities.company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('position')
                            ->label(__('entities.position'))
                            ->maxLength(100),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('entities.is_active'))
                            ->default(true)
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Hidden::make('account_type')
                    ->default(function (string $context, ?string $state) {
                        // This will be set based on the page we're on
                        return $state ?? request()->query('account_type', null);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('entities.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('entities.email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('entities.company'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('entities.phone'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('entities.is_active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('entities.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->label(__('entities.user_type'))
                    ->options([
                        'customers' => __('entities.customers'),
                        'suppliers' => __('entities.suppliers'),
                        'employers' => __('entities.employers'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }
                        
                        $accountNumber = match ($data['value']) {
                            'customers' => '120',
                            'suppliers' => '320',
                            'employers' => '335',
                            default => null,
                        };
                        
                        if ($accountNumber) {
                            return $query->whereHas('accounts', function (Builder $query) use ($accountNumber) {
                                $query->whereHas('uniformChartOfAccount', function (Builder $query) use ($accountNumber) {
                                    $query->where('number', $accountNumber);
                                });
                            });
                        }
                        
                        return $query;
                    }),
                Tables\Filters\SelectFilter::make('company')
                    ->label(__('entities.company'))
                    ->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('entities.active_status')),
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
            RelationManagers\AccountsRelationManager::class,
            RelationManagers\TransactionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserTypes::route('/'),
            'create' => Pages\CreateUserType::route('/create'),
            'view' => Pages\ViewUserType::route('/{record}'),
            'edit' => Pages\EditUserType::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
    
    public static function getUserTypeLabel(User $user): string
    {
        $accountType = $user->accounts()
            ->whereHas('uniformChartOfAccount', function (Builder $query) {
                $query->whereIn('number', ['120', '320', '335']);
            })
            ->with('uniformChartOfAccount')
            ->first()?->uniformChartOfAccount?->number;
            
        return match ($accountType) {
            '120' => __('entities.customers'),
            '320' => __('entities.suppliers'),
            '335' => __('entities.employers'),
            default => __('entities.other'),
        };
    }
}
