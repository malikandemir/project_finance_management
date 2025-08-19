<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Filament\RelationManagers\CommentsRelationManager;
use App\Filament\RelationManagers\TasksRelationManager;
use App\Filament\RelationManagers\TransactionGroupsRelationManager;
use App\Filament\Resources\Components\CommentsSection;
use App\Filament\Resources\Components\TasksSection;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    protected static ?string $navigationGroup = 'Project Management';
    
    protected static ?int $navigationSort = 1;
    
    public static function getModelLabel(): string
    {
        return __('filament::resources.resources.ProjectResource.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament::resources.resources.ProjectResource.plural');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('filament::resources.sections.project_information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label(__('filament::resources.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('company_id')
                            ->label(__('filament::resources.fields.company'))
                            ->relationship('company', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Textarea::make('description')
                            ->label(__('filament::resources.fields.description'))
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                Forms\Components\Section::make(__('filament::resources.sections.project_timeline'))
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label(__('filament::resources.fields.start_date')),
                        Forms\Components\DatePicker::make('end_date')
                            ->label(__('filament::resources.fields.end_date')),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('filament::resources.fields.is_active'))
                            ->default(true)
                            ->required(),
                        Forms\Components\Select::make('responsible_user_id')
                            ->relationship('responsibleUser', 'name')
                            ->label(__('filament::resources.fields.responsible_person'))
                            ->searchable()
                            ->preload(),
                        Forms\Components\Placeholder::make('created_by')
                            ->label(__('filament::resources.fields.created_by'))
                            ->content(function ($record) {
                                if (!$record) {
                                    return auth()->user()->name;
                                }
                                return $record->exists && $record->creator ? $record->creator->name : (auth()->user()->name ?? 'Unknown');
                            }),
                    ])->columns(2),
                    
                // Placeholder for comments and tasks (only visible on create page)
                Forms\Components\Placeholder::make('comments_tasks_placeholder')
                    ->content('Comments and tasks will be available after saving the project.')
                    ->visible(fn ($operation) => $operation === 'create')
                    ->hiddenOn(['edit', 'view']),
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
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('filament::resources.fields.company'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('filament::resources.fields.start_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('filament::resources.fields.end_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('filament::resources.fields.is_active'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('responsibleUser.name')
                    ->label(__('filament::resources.fields.responsible_person'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('filament::resources.fields.created_by'))
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
                Tables\Filters\SelectFilter::make('company')
                    ->label(__('filament::resources.fields.company'))
                    ->relationship('company', 'name'),
                Tables\Filters\SelectFilter::make('responsible_user_id')
                    ->relationship('responsibleUser', 'name')
                    ->label(__('filament::resources.fields.responsible_person')),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('filament::resources.fields.active_status')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            CommentsRelationManager::class,
            TasksRelationManager::class,
            TransactionGroupsRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'view' => Pages\ViewProject::route('/{record}'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
