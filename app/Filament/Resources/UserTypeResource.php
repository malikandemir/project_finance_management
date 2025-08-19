<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserTypeResource\Pages;
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
    
    protected static ?string $navigationGroup = 'User Management';
    
    protected static ?int $navigationSort = 10;
    
    public static function getModelLabel(): string
    {
        return __('User Types');
    }

    public static function getPluralModelLabel(): string
    {
        return __('User Types');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('User Information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('Name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('Email'))
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('password')
                            ->label(__('Password'))
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('Phone'))
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\Select::make('company_id')
                            ->label(__('Company'))
                            ->relationship('company', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('position')
                            ->label(__('Position'))
                            ->maxLength(100),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Is Active'))
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
                    ->label(__('Name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('Email'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('Company'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('Phone'))
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Is Active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->label(__('User Type'))
                    ->options([
                        'customers' => __('Customers'),
                        'suppliers' => __('Suppliers'),
                        'employers' => __('Employers'),
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
                    ->label(__('Company'))
                    ->relationship('company', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('Active Status')),
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
            'index' => Pages\ListUserTypes::route('/'),
            'create' => Pages\CreateUserType::route('/create'),
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
            '120' => __('Customers'),
            '320' => __('Suppliers'),
            '335' => __('Employers'),
            default => __('Other'),
        };
    }
}
