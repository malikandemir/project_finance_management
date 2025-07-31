<?php

return [
    'columns' => [
        'text' => [
            'more_list_items' => 'and :count more',
        ],
    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'Select/deselect all items for bulk actions.',
        ],
        'bulk_select_record' => [
            'label' => 'Select/deselect item :key for bulk actions.',
        ],
        'search' => [
            'label' => 'Search',
            'placeholder' => 'Search',
            'indicator' => 'Search',
        ],
    ],

    'summary' => [
        'heading' => 'Summary',
        'subheading' => 'Showing :count of :total results.',
    ],

    'actions' => [
        'disable_reordering' => [
            'label' => 'Finish reordering records',
        ],
        'enable_reordering' => [
            'label' => 'Reorder records',
        ],
        'filter' => [
            'label' => 'Filter',
        ],
        'open_bulk_actions' => [
            'label' => 'Bulk actions',
        ],
        'toggle_columns' => [
            'label' => 'Toggle columns',
        ],
    ],

    'empty' => [
        'heading' => 'No records found',
        'description' => 'Create a new :model',
    ],

    'filters' => [
        'actions' => [
            'remove' => [
                'label' => 'Remove filter',
            ],
            'remove_all' => [
                'label' => 'Remove all filters',
                'tooltip' => 'Remove all filters',
            ],
            'reset' => [
                'label' => 'Reset filters',
            ],
        ],

        'indicator' => 'Active filters',

        'multi_select' => [
            'placeholder' => 'All',
        ],

        'select' => [
            'placeholder' => 'All',
        ],

        'trashed' => [
            'label' => 'Deleted records',
            'only_trashed' => 'Only deleted records',
            'with_trashed' => 'With deleted records',
            'without_trashed' => 'Without deleted records',
        ],
    ],

    'grouping' => [
        'fields' => [
            'group' => [
                'label' => 'Group by',
                'placeholder' => 'Group by',
            ],
            'direction' => [
                'label' => 'Grouping direction',
                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],
            ],
        ],
    ],

    'reorder_indicator' => 'Drag and drop the records into order.',

    'selection_indicator' => [
        'selected_count' => '1 record selected|:count records selected',
        'actions' => [
            'select_all' => [
                'label' => 'Select all :count',
            ],
            'deselect_all' => [
                'label' => 'Deselect all',
            ],
        ],
    ],

    'sorting' => [
        'fields' => [
            'column' => [
                'label' => 'Sort by',
            ],
            'direction' => [
                'label' => 'Sort direction',
                'options' => [
                    'asc' => 'Ascending',
                    'desc' => 'Descending',
                ],
            ],
        ],
    ],
];
