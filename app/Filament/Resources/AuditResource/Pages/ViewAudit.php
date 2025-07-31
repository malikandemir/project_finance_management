<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Filament\Resources\AuditResource;
use Filament\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;
    
    protected function getHeaderActions(): array
    {
        return [
            // No edit action as we don't allow editing audit logs
        ];
    }
    
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Audit Information')
                    ->schema([
                        TextEntry::make('event')
                            ->label('Event')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'created' => 'success',
                                'updated' => 'warning',
                                'deleted' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('auditable_type')
                            ->label('Model')
                            ->formatStateUsing(fn (string $state): string => class_basename($state)),
                        TextEntry::make('auditable_id')
                            ->label('Record ID'),
                        TextEntry::make('user.name')
                            ->label('User')
                            ->default('System'),
                        TextEntry::make('created_at')
                            ->label('Date')
                            ->dateTime(),
                        TextEntry::make('ip_address')
                            ->label('IP Address'),
                        TextEntry::make('url')
                            ->label('URL')
                            ->url(fn ($state) => $state)
                            ->openUrlInNewTab(),
                    ])->columns(2),
                
                Section::make('Modified Values')
                    ->schema(function ($record) {
                        $oldValues = (array) $record->old_values;
                        $newValues = (array) $record->new_values;
                        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));
                        sort($allKeys);
                        
                        $schema = [];
                        
                        if (empty($allKeys)) {
                            $schema[] = TextEntry::make('no_changes')
                                ->label('')
                                ->state('No changes recorded')
                                ->color('gray');
                            return $schema;
                        }
                        
                        foreach ($allKeys as $key) {
                            $oldValue = $oldValues[$key] ?? null;
                            $newValue = $newValues[$key] ?? null;
                            
                            $schema[] = Section::make($key)
                                ->schema([
                                    TextEntry::make("old_{$key}")
                                        ->label('Old Value')
                                        ->state(function () use ($oldValue, $key, $oldValues) {
                                            if (!array_key_exists($key, $oldValues)) {
                                                return 'not set';
                                            }
                                            
                                            if (is_array($oldValue) || is_object($oldValue)) {
                                                return json_encode($oldValue, JSON_PRETTY_PRINT);
                                            }
                                            
                                            if (is_bool($oldValue)) {
                                                return $oldValue ? 'true' : 'false';
                                            }
                                            
                                            if (is_null($oldValue)) {
                                                return 'null';
                                            }
                                            
                                            return (string) $oldValue;
                                        })
                                        ->color('danger'),
                                    TextEntry::make("new_{$key}")
                                        ->label('New Value')
                                        ->state(function () use ($newValue, $key, $newValues) {
                                            if (!array_key_exists($key, $newValues)) {
                                                return 'not set';
                                            }
                                            
                                            if (is_array($newValue) || is_object($newValue)) {
                                                return json_encode($newValue, JSON_PRETTY_PRINT);
                                            }
                                            
                                            if (is_bool($newValue)) {
                                                return $newValue ? 'true' : 'false';
                                            }
                                            
                                            if (is_null($newValue)) {
                                                return 'null';
                                            }
                                            
                                            return (string) $newValue;
                                        })
                                        ->color('success'),
                                ])
                                ->columns(2)
                                ->collapsible();
                        }
                        
                        return $schema;
                    }),
            ]);
    }
}
