<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Widgets\TaskSummaryWidget;
use App\Filament\Resources\Components\CommentsSection;
use App\Filament\Resources\Components\TasksSection;
use App\Helpers\MainCompanyHelper;
use App\Models\Account;
use App\Models\TheUniformChartOfAccount;
use App\Models\Transaction;
use App\Models\TransactionGroup;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\DB;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        $actions = [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
        
        // Only show the Get Paid button if the project is not fully paid
        if (!$this->record->isFullyPaid()) {
            $actions[] = Actions\Action::make('getPaid')
                ->label(__('payments.project.get_paid.label'))
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->modalHeading(__('payments.project.get_paid.modal_heading'))
                ->modalDescription(__('payments.project.get_paid.modal_description'))
                ->form([
                    Select::make('account_id')
                        ->label(__('payments.account.label'))
                        ->options(function () {
                            $mainCompany = MainCompanyHelper::getMainCompany();
                            if (!$mainCompany) {
                                return [];
                            }
                            
                            // Get accounts with uniform account numbers 100 and 102
                            $uniformAccounts = TheUniformChartOfAccount::whereIn('number', ['100', '102'])->pluck('id');
                            
                            return Account::whereIn('account_uniform_id', $uniformAccounts)
                                ->get()
                                ->mapWithKeys(function ($account) {
                                    return [$account->id => $account->account_name . ' (' . $account->uniformChartOfAccount->number . ')'];
                                });
                        })
                        ->required(),
                    TextInput::make('amount')
                        ->label(__('payments.project.get_paid.amount'))
                        ->numeric()
                        ->minValue(0.01)
                        ->required(),
                    TextInput::make('description')
                        ->label(__('payments.project.get_paid.description'))
                        ->default(function () {
                            return __('payments.project.get_paid.default_description', ['name' => $this->record->name]);
                        }),
                ])
                ->action(function (array $data): void {
                    $this->processGetPaidTransaction($data);
                });
        }
        
        return $actions;
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
        
        // Add payment information section
        $schema[] = Section::make(__('payments.project.payment_status.title'))
            ->schema([
                \Filament\Forms\Components\Placeholder::make('total_price')
                    ->label(__('payments.project.payment_status.total_price'))
                    ->content(function () {
                        return number_format($this->record->getTotalPrice(), 2) . ' ' . config('app.currency', 'TRY');
                    }),
                \Filament\Forms\Components\Placeholder::make('total_paid')
                    ->label(__('payments.project.payment_status.total_paid'))
                    ->content(function () {
                        return number_format($this->record->getTotalPaid(), 2) . ' ' . config('app.currency', 'TRY');
                    }),
                \Filament\Forms\Components\Placeholder::make('payment_status')
                    ->label(__('payments.project.payment_status.status'))
                    ->content(function () {
                        $totalPrice = $this->record->getTotalPrice();
                        $totalPaid = $this->record->getTotalPaid();
                        $percentage = ($totalPrice > 0) ? round(($totalPaid / $totalPrice) * 100, 2) : 0;
                        
                        if ($this->record->isFullyPaid()) {
                            $statusText = '<span class="text-success font-medium">' . __('payments.project.payment_status.fully_paid') . '</span>';
                            $barColor = 'bg-success';
                        } else {
                            if ($totalPrice <= 0) {
                                $statusText = '<span class="text-warning font-medium">' . __('payments.project.payment_status.no_price') . '</span>';
                                $barColor = 'bg-warning';
                                $percentage = 0;
                            } else {
                                $statusText = '<span class="text-warning font-medium">' . 
                                    __('payments.project.payment_status.partially_paid', ['percentage' => $percentage]) . 
                                    '</span>';
                                $barColor = 'bg-warning';
                            }
                        }
                        
                        // Create progress bar HTML
                        $progressBar = <<<HTML
                        <div class="mt-2">
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="{$barColor} h-2.5 rounded-full" style="width: {$percentage}%"></div>
                            </div>
                        </div>
                        HTML;
                        
                        return $statusText . $progressBar;
                    })
                    ->html(),
            ])
            ->collapsible();
        
        // Append the comments section
        $schema[] = CommentsSection::make();
        
        return $schema;
    }
    
    /**
     * Process the Get Paid transaction
     *
     * @param array $data
     * @return void
     */
    protected function processGetPaidTransaction(array $data): void
    {
        try {
            DB::beginTransaction();
            
            $mainCompany = MainCompanyHelper::getMainCompany();
            $projectCompany = $this->record->company;
            $amount = $data['amount'];
            $description = $data['description'];
            $selectedAccount = Account::findOrFail($data['account_id']);
            
            // Find or create the 120 account for the project company
            $uniformAccount120 = TheUniformChartOfAccount::where('number', '120')->first();
            
            if (!$uniformAccount120) {
                throw new \Exception('Uniform account 120 not found');
            }
            
            $projectAccount = Account::firstOrCreate(
                [
                    'account_uniform_id' => $uniformAccount120->id,
                    'user_id' => auth()->id(),
                ],
                [
                    'account_name' => 'Accounts Receivable',
                    'balance' => 0,
                    'account_group_id' => 1, // Default account group ID
                ]
            );
            
            // Create transaction group
            $transactionGroup = new TransactionGroup();
            $transactionGroup->description = $description;
            $transactionGroup->group_date = now(); // Add group_date field
            $transactionGroup->user_id = auth()->id(); // Add current logged-in user ID
            $transactionGroup->name = "Get Paid for Project #{$this->record->id}";
            $transactionGroup->transactionable()->associate($this->record); // Associate with the project
            $transactionGroup->save();
            
            // Create debit transaction for the selected account (increase)
            $debitTransaction = new Transaction();
            $debitTransaction->amount = $amount;
            $debitTransaction->debit_credit = Transaction::DEBIT; // Debit
            $debitTransaction->account_id = $selectedAccount->id;
            $debitTransaction->user_id = auth()->id();
            $debitTransaction->transaction_group_id = $transactionGroup->id;
            $debitTransaction->balance_after_transaction = $selectedAccount->balance + $amount;
            $debitTransaction->transaction_date = now();
            $debitTransaction->description = $description;
            $debitTransaction->save();
            
            // Update the selected account balance
            $selectedAccount->balance += $amount;
            $selectedAccount->save();
            
            // Create credit transaction for the project account (decrease)
            $creditTransaction = new Transaction();
            $creditTransaction->amount = $amount;
            $creditTransaction->debit_credit = Transaction::CREDIT; // Credit
            $creditTransaction->account_id = $projectAccount->id;
            $creditTransaction->user_id = auth()->id();
            $creditTransaction->transaction_group_id = $transactionGroup->id;
            $creditTransaction->balance_after_transaction = $projectAccount->balance - $amount;
            $creditTransaction->transaction_date = now();
            $creditTransaction->description = $description;
            $creditTransaction->save();
            
            // Update the project account balance
            $projectAccount->balance -= $amount;
            $projectAccount->save();
            
            DB::commit();
            
            Notification::make()
                ->title(__('payments.project.get_paid.success'))
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->title(__('payments.project.get_paid.error'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
