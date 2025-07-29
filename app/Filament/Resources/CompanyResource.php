<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    
    protected static ?string $navigationGroup = 'Company Management';
    
    protected static ?int $navigationSort = 1;
    
    // Use the CompanyPolicy for authorization
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.CompanyResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.CompanyResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function canCreate(): bool
    {
        return auth()->user()->can('create_company');
    }
    
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('edit_company', $record);
    }
    
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('delete_company', $record);
    }
    
    public static function canForceDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('force_delete_company', $record);
    }
    
    public static function canRestore(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('restore_company', $record);
    }
    
    public static function canViewAny(): bool
    {
        return auth()->user()->can('view_companies');
    }
    
    public static function canView(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->can('view_company', $record);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament::resources.sections.company_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament::resources.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->label(__('filament::resources.fields.email'))
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label(__('filament::resources.fields.phone'))
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('address')
                            ->label(__('filament::resources.fields.address'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
                
                Forms\Components\Section::make(__('filament::resources.sections.business_details'))
                    ->schema([
                        Forms\Components\TextInput::make('tax_number')
                            ->label(__('filament::resources.fields.tax_number'))
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('logo')
                            ->label(__('filament::resources.fields.logo'))
                            ->image()
                            ->directory('company-logos')
                            ->visibility('public'),
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament::resources.fields.description'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament::resources.fields.is_active'))
                            ->required()
                            ->default(true),
                        Forms\Components\Toggle::make('is_main')
                            ->label(__('filament::resources.fields.is_main'))
                            ->helperText(__('filament::resources.help_texts.is_main'))
                            ->default(false),
                        Forms\Components\Select::make('owner_id')
                            ->label(__('filament::resources.fields.owner_id'))
                            ->relationship('owner', 'name')
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament::resources.fields.name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('email')
                                    ->label(__('filament::resources.fields.email'))
                                    ->email()
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->helperText(__('filament::resources.help_texts.owner_id')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (\Illuminate\Database\Eloquent\Builder $query) {
                $user = auth()->user();
                
                // Super admin can see all companies
                if ($user->hasRole('super-admin')) {
                    return $query;
                }
                
                // Company owners can only see companies they created
                if ($user->hasRole('company-owner')) {
                    return $query->where('created_by', $user->id);
                }
                
                // Project managers can only see companies of projects they manage
                if ($user->hasRole('project-manager')) {
                    return $query->whereHas('projects', function ($q) use ($user) {
                        $q->where('responsible_user_id', $user->id);
                    });
                }
                
                // Team members can only see companies of projects they're part of
                if ($user->hasRole('team-member')) {
                    return $query->whereHas('projects.tasks', function ($q) use ($user) {
                        $q->where('assigned_to', $user->id);
                    });
                }
                
                // Default: don't show any companies
                return $query->where('id', 0);
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('filament::resources.fields.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('filament::resources.fields.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label(__('filament::resources.fields.phone'))
                    ->searchable(),
                Tables\Columns\ImageColumn::make('logo')
                    ->label(__('filament::resources.fields.logo'))
                    ->circular(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament::resources.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_main')
                    ->boolean()
                    ->label(__('filament::resources.fields.is_main'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('filament::resources.fields.created_by'))
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        '1' => __('filament::resources.options.active'),
                        '0' => __('filament::resources.options.inactive'),
                    ])
                    ->label(__('filament::resources.fields.status')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view' => Pages\ViewCompany::route('/{record}'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
