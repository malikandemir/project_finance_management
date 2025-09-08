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
    
    protected function getPaymentStatusDescription($totalPaid, $paymentPercentage, $totalPrice): string
    {
        $paymentStatus = '';
        
        if ($this->record->isFullyPaid()) {
            $paymentStatus = __('payments.project.payment_status.fully_paid');
        } elseif ($totalPrice <= 0) {
            $paymentStatus = __('payments.project.payment_status.no_price');
        } else {
            $paymentStatus = __('payments.project.payment_status.partially_paid', ['percentage' => $paymentPercentage]);
        }
        
        return __('payments.project.payment_status.total_paid') . ': ' . 
               number_format($totalPaid, 2) . ' ' . config('app.currency', 'TRY') . 
               ' | ' . $paymentStatus . ' (' . $paymentPercentage . '%)';
    }
    
    protected function getPaymentStatusColor(): string
    {
        if ($this->record->isFullyPaid()) {
            return 'success';
        } elseif ($this->record->getTotalPrice() <= 0) {
            return 'warning';
        } else {
            return 'primary';
        }
    }

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

        // Calculate financial summaries using the new Project model methods
        $totalPrice = $this->record->getTotalPrice();
        $totalPaid = $this->record->getTotalPaid();
        $paymentPercentage = ($totalPrice > 0) ? round(($totalPaid / $totalPrice) * 100, 2) : 0;
        
        // Also keep the task-based financial data for reference
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
                __('payments.project.payment_status.total_price') . ': ' . number_format($totalPrice, 2) . ' ' . config('app.currency', 'TRY'))
                ->description($this->getPaymentStatusDescription($totalPaid, $paymentPercentage, $totalPrice))
                ->chart([$paymentPercentage, 100 - $paymentPercentage])
                ->color($this->getPaymentStatusColor()),
        ];
    }
}
