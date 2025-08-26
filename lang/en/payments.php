<?php

return [
    'get_paid' => [
        'label' => 'Get Paid',
        'modal_heading' => 'Get Paid for Task',
        'modal_description' => 'Select the main company account (100 or 102) to receive payment for this task.',
        'success' => 'Payment received successfully',
        'error' => 'Error processing payment',
    ],
    'pay' => [
        'label' => 'Pay',
        'modal_heading' => 'Pay for Task',
        'modal_description' => 'Select the main company account (100 or 102) to pay for this task.',
        'success' => 'Payment processed successfully',
        'error' => 'Error processing payment',
    ],
    'account' => [
        'label' => 'Main Company Account',
    ],
    'project' => [
        'get_paid' => [
            'label' => 'Get Paid',
            'modal_heading' => 'Get Paid from Project',
            'modal_description' => 'Select an account from the main company to receive payment from this project.',
            'success' => 'Payment Processed Successfully',
            'error' => 'Error Processing Payment',
            'amount' => 'Amount',
            'description' => 'Description',
            'default_description' => 'Payment received from project: :name',
        ],
    ],
];
