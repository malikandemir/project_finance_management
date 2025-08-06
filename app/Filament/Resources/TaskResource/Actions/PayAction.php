<?php

namespace App\Filament\Resources\TaskResource\Actions;

use App\Models\Account;
use App\Models\Task;
use App\Observers\TaskObserver;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class PayAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'pay';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('payments.pay.label'))
            ->icon('heroicon-o-credit-card')
            ->color('primary')
            ->modalHeading(__('payments.pay.modal_heading'))
            ->modalDescription(__('payments.pay.modal_description'))
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
                    $taskObserver->pay($record, $data['account_id']);

                    Notification::make()
                        ->title(__('payments.pay.success'))
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title(__('payments.pay.error'))
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->visible(function (Task $record): bool {
                return !$record->is_paid && $record->price > 0;
            });
    }
}
