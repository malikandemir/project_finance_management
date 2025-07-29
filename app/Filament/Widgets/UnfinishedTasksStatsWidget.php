<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UnfinishedTasksStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;
    
    protected function getStats(): array
    {
        return [
            Stat::make(__('filament::widgets.unfinished_tasks.label'), Task::where('is_completed', false)->count())
                ->description(__('filament::widgets.unfinished_tasks.description'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('danger'),
            Stat::make(__('filament::widgets.companies.label'), Company::count())
                ->description(__('filament::widgets.companies.description'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('success'),
            Stat::make(__('filament::widgets.projects.label'), Project::count())
                ->description(__('filament::widgets.projects.description'))
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('warning'),
        ];
    }
}
