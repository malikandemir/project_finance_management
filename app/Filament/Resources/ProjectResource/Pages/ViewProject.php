<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\TaskSummaryWidget;
use App\Filament\Resources\Components\CommentsSection;
use App\Filament\Resources\Components\TasksSection;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Section;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            TaskSummaryWidget::class,
        ];
    }
    
    protected function getFooterWidgets(): array
    {
        return [
            // Add any footer widgets here
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);
        
        // Add any data mutations here
        
        return $data;
    }
    
    protected function getFormSchema(): array
    {
        // Get the parent form schema
        $schema = parent::getFormSchema();
        
        // Append the comments section
        $schema[] = CommentsSection::make();
        
        return $schema;
    }
}
