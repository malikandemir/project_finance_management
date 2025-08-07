<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Task;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class TaskSummaryWidget extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record) {
            return [];
        }

        // Get tasks by status
        $tasksByStatus = $this->record->tasks()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Calculate financial summaries
        $totalPrice = $this->record->tasks()->sum('price') ?? 0;
        $paidSum = $this->record->tasks()->where('is_paid', true)->sum('price') ?? 0;
        $getPaidSum = $this->record->tasks()->where('is_get_paid', true)->sum('price') ?? 0;

        // Format status counts for display
        $pendingCount = $tasksByStatus['pending'] ?? 0;
        $inProgressCount = $tasksByStatus['in_progress'] ?? 0;
        $completedCount = $tasksByStatus['completed'] ?? 0;
        $onHoldCount = $tasksByStatus['on_hold'] ?? 0;
        $cancelledCount = $tasksByStatus['cancelled'] ?? 0;

        return [
            Stat::make(__('filament::resources.widgets.task_summary.total_tasks'), array_sum($tasksByStatus))
                ->description(__('filament::resources.widgets.task_summary.tasks_in_project'))
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('gray'),

            Stat::make(__('filament::resources.widgets.task_summary.task_status'), 
                "{$pendingCount} Pending, {$inProgressCount} In Progress")
                ->description("{$completedCount} Completed, {$onHoldCount} On Hold, {$cancelledCount} Cancelled")
                ->color('blue'),

            Stat::make(__('filament::resources.widgets.task_summary.financial_summary'), 
                __('filament::resources.widgets.task_summary.total') . ': ₺' . number_format($totalPrice, 2))
                ->description(
                    __('filament::resources.widgets.task_summary.paid') . ': ₺' . number_format($paidSum, 2) . ' | ' .
                    __('filament::resources.widgets.task_summary.get_paid') . ': ₺' . number_format($getPaidSum, 2)
                )
                ->color('success'),
        ];
    }
}
