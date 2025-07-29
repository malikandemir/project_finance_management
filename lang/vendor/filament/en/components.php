<?php

return [
    'pagination' => [
        'label' => 'Pagination Navigation',
        'overview' => ':first to :last of :total results',
        'fields' => [
            'records_per_page' => [
                'label' => 'Per page',
            ],
        ],
        'actions' => [
            'go_to_page' => [
                'label' => 'Go to page :page',
            ],
            'next' => [
                'label' => 'Next',
            ],
            'previous' => [
                'label' => 'Previous',
            ],
        ],
    ],

    'fields' => [
        'search' => [
            'label' => 'Search',
            'placeholder' => 'Search',
        ],
    ],

    'modal' => [
        'actions' => [
            'close' => [
                'label' => 'Close',
            ],
        ],
    ],
];
