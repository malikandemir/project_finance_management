<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\TaskSummaryWidget;
use App\Models\Comment;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Section;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            TaskSummaryWidget::class,
        ];
    }
    
    protected function getFormSchema(): array
    {
        // Get the parent form schema
        $schema = parent::getFormSchema();
        
        // Append the comments section with direct implementation
        $schema[] = Section::make(__('filament::resources.fields.comments'))
            ->schema([
                // Display existing comments
                Forms\Components\Placeholder::make('comments_list')
                    ->label(__('filament::resources.fields.comments'))
                    ->content(function ($record) {
                        if (!$record || !$record->exists || $record->comments()->count() === 0) {
                            return __('filament::resources.fields.no_comments');
                        }
                        
                        $comments = $record->comments()->with('user')->latest()->get();
                        $html = '<div class="space-y-4">';
                        
                        foreach ($comments as $comment) {
                            $html .= '<div class="p-4 bg-gray-50 rounded-lg">';
                            $html .= '<div class="flex justify-between items-start">';
                            $html .= '<div class="font-medium text-gray-900">' . e($comment->user->name) . '</div>';
                            $html .= '<div class="text-xs text-gray-500">' . $comment->created_at->format('M d, Y H:i') . '</div>';
                            $html .= '</div>';
                            $html .= '<div class="mt-2 text-gray-700">' . e($comment->content) . '</div>';
                            $html .= '</div>';
                        }
                        
                        $html .= '</div>';
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->columnSpanFull(),
                
                // Add comment form
                Forms\Components\Textarea::make('new_comment')
                    ->label(__('filament::resources.fields.add_comment'))
                    ->placeholder(__('filament::resources.fields.comment'))
                    ->maxLength(65535)
                    ->columnSpanFull(),
                
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('submit_comment')
                        ->label(__('filament::resources.fields.submit_comment'))
                        ->action(function (array $data, $record) {
                            // Create a new comment
                            if (!empty($data['new_comment'])) {
                                Comment::create([
                                    'content' => $data['new_comment'],
                                    'commentable_id' => $record->id,
                                    'commentable_type' => get_class($record),
                                    'user_id' => auth()->id(),
                                ]);
                                
                                // Show notification
                                Notification::make()
                                    ->title(__('filament::resources.fields.comment') . ' ' . __('filament::actions.create.messages.created'))
                                    ->success()
                                    ->send();
                                
                                // Refresh the page
                                return redirect(request()->header('Referer'));
                            }
                        })
                ])
            ])
            ->collapsible();
        
        return $schema;
    }
}
