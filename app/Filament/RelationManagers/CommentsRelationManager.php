<?php

namespace App\Filament\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $recordTitleAttribute = 'content';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('content')
                    ->label(__('Comment'))
                    ->required()
                    ->maxLength(65535),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('content')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('content')
                    ->label(__('Comment'))
                    ->searchable()
                    ->limit(100),
                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('Author'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Posted at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        return $data;
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->user_id === auth()->id()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => $record->user_id === auth()->id() || auth()->user()->hasRole('admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
