<?php

namespace App\Filament\Resources\Components;

use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;

class CommentsSection
{
    public static function make(): Section
    {
        return Section::make('Comments')
            ->schema([
                // Display existing comments
                Forms\Components\Placeholder::make('comments_list')
                    ->label('Comments')
                    ->content(function ($record) {
                        if (!$record || !$record->exists || $record->comments()->count() === 0) {
                            return 'No comments yet.';
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
                    ->label('Add a comment')
                    ->placeholder('Write your comment here...')
                    ->maxLength(65535)
                    ->columnSpanFull()
                    ->visible(fn ($record) => $record && $record->exists),
                
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('submit_comment')
                        ->label('Add Comment')
                        ->visible(fn ($record) => $record && $record->exists)
                        ->form([
                            Forms\Components\Textarea::make('comment_content')
                                ->label('Comment')
                                ->required()
                                ->maxLength(65535),
                        ])
                        ->action(function (array $data, $record) {
                            // Create a new comment
                            Comment::create([
                                'content' => $data['comment_content'],
                                'commentable_id' => $record->id,
                                'commentable_type' => get_class($record),
                                'user_id' => auth()->id(),
                            ]);
                            
                            // Show notification
                            Notification::make()
                                ->title('Comment added successfully')
                                ->success()
                                ->send();
                            
                            // Refresh the page
                            return redirect(request()->header('Referer'));
                        })
                ])
                ->visible(fn ($record) => $record && $record->exists)
            ])
            ->collapsible();
    }
}
