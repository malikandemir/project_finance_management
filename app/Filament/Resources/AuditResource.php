<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuditResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use OwenIt\Auditing\Models\Audit;

class AuditResource extends Resource
{
    protected static ?string $model = Audit::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = 'Administration';
    
    protected static ?int $navigationSort = 99;

    public static function getModelLabel(): string
    {
        return __('Audit Log');
    }

    public static function getPluralModelLabel(): string
    {
        return __('Audit Logs');
    }
    
    public static function getNavigationGroup(): ?string
    {
        return __('filament::resources.navigation_groups.' . parent::getNavigationGroup());
    }

    public static function canCreate(): bool
    {
        return false; // Disable creation of audit logs
    }
    
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Disable editing of audit logs
    }
    
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Disable deletion of audit logs
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Empty form as we don't need to create or edit audit logs
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('auditable_type')
                    ->label('Model')
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('auditable_id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable()
                    ->default('System'),
                Tables\Columns\TextColumn::make('changes')
                    ->label('Changes')
                    ->formatStateUsing(function ($record) {
                        $oldValues = (array) $record->old_values;
                        $newValues = (array) $record->new_values;
                        $keys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                        
                        if (empty($keys)) {
                            return 'No changes';
                        }
                        
                        return count($keys) . ' ' . (count($keys) === 1 ? 'field' : 'fields') . ' changed';
                    })
                    ->description(function ($record) {
                        $oldValues = (array) $record->old_values;
                        $newValues = (array) $record->new_values;
                        $keys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                        
                        if (empty($keys)) {
                            return null;
                        }
                        
                        return implode(', ', array_slice($keys, 0, 3)) . 
                            (count($keys) > 3 ? ' and ' . (count($keys) - 3) . ' more' : '');
                    }),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
                Tables\Filters\SelectFilter::make('auditable_type')
                    ->label('Model')
                    ->options(function () {
                        return Audit::distinct('auditable_type')
                            ->pluck('auditable_type')
                            ->mapWithKeys(function ($type) {
                                return [$type => class_basename($type)];
                            })
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // No bulk actions needed
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
            'index' => Pages\ListAudits::route('/'),
            'view' => Pages\ViewAudit::route('/{record}'),
        ];
    }
}
