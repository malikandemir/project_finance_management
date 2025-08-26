<?php

namespace App\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $recordTitleAttribute = 'title';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label(__('filament::resources.fields.title'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label(__('filament::resources.fields.description'))
                    ->maxLength(65535),
                Forms\Components\Select::make('status')
                    ->label(__('filament::resources.fields.status'))
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'on_hold' => 'On Hold',
                        'cancelled' => 'Cancelled',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Select::make('priority')
                    ->label(__('filament::resources.fields.priority'))
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ])
                    ->default('medium')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label(__('filament::resources.fields.due_date')),
                Forms\Components\Toggle::make('is_completed')
                    ->label(__('filament::resources.fields.is_completed'))
                    ->default(false),
                Forms\Components\Select::make('assigned_to')
                    ->label(__('filament::resources.fields.responsible_person'))
                    ->relationship('assignedUser', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('price')
                    ->label(__('filament::resources.fields.price'))
                    ->numeric()
                    ->prefix('₺')
                    ->inputMode('decimal'),
                Forms\Components\TextInput::make('cost_percentage')
                    ->label(__('filament::resources.fields.cost_percentage'))
                    ->numeric()
                    ->suffix('%')
                    ->inputMode('decimal')
                    ->maxValue(100),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament::resources.fields.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament::resources.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'in_progress' => 'blue',
                        'completed' => 'green',
                        'on_hold' => 'orange',
                        'cancelled' => 'red',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('filament::resources.fields.priority'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'blue',
                        'high' => 'orange',
                        'urgent' => 'red',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('filament::resources.fields.due_date'))
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_completed')
                    ->label(__('filament::resources.fields.is_completed'))
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignedUser.name')
                    ->label(__('filament::resources.fields.responsible_person'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('filament::resources.fields.status'))
                    ->options([
                        'pending' => 'Pending',
                        'in_progress' => 'In Progress',
                        'completed' => 'Completed',
                        'on_hold' => 'On Hold',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('priority')
                    ->label(__('filament::resources.fields.priority'))
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\TernaryFilter::make('is_completed')
                    ->label(__('filament::resources.fields.completed')),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['created_by'] = auth()->id();
                        return $data;
                    }),
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

    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        if ($query === null) {
            $query = $this->getRelationship()->getQuery();
        }
        
        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
