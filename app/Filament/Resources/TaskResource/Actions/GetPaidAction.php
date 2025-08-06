<?php

namespace App\Filament\Resources\TaskResource\Actions;

use App\Models\Account;
use App\Models\Task;
use App\Observers\TaskObserver;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class GetPaidAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'get-paid';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('payments.get_paid.label'))
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->modalHeading(__('payments.get_paid.modal_heading'))
            ->modalDescription(__('payments.get_paid.modal_description'))
            ->form([
                Select::make('account_id')
                    ->label(__('payments.account.label'))
                    ->options(function () {
                        return Account::whereHas('uniformChartOfAccount', function (Builder $query) {
                            $query->whereIn('number', ['100', '102']);
                        })->pluck('account_name', 'id');
                    })
                    ->required()
                    ->searchable(),
            ])
            ->action(function (array $data, Task $record): void {
                try {
                    $taskObserver = app(TaskObserver::class);
                    $taskObserver->getPaid($record, $data['account_id']);

                    Notification::make()
                        ->title(__('payments.get_paid.success'))
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('payments.get_paid.error'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(function (Task $record): bool {
                return !$record->is_get_paid && $record->price > 0;
            });
    }
}
