<?php

return [
    'nav' => [
        'toggle' => 'Navigasyonu Aç/Kapat',
    ],

    'table' => [
        'actions' => [
            'button' => [
                'label' => 'İşlemler',
            ],
        ],

        'bulk_actions' => [
            'button' => [
                'label' => 'Toplu işlemler',
            ],
        ],

        'columns' => [
            'togglable' => [
                'button' => [
                    'label' => 'Sütunlar',
                ],
            ],
        ],

        'filters' => [
            'button' => [
                'label' => 'Filtreler',
            ],

            'indicator' => 'Aktif Filtreler',

            'multi_select' => [
                'placeholder' => 'Tümü',
            ],

            'select' => [
                'placeholder' => 'Tümü',
            ],

            'trashed' => [
                'label' => 'Silinen kayıtlar',
                'only_trashed' => 'Sadece silinen kayıtlar',
                'with_trashed' => 'Silinen kayıtlarla birlikte',
                'without_trashed' => 'Silinen kayıtlar olmadan',
            ],
        ],

        'grouping' => [
            'button' => [
                'label' => 'Gruplama',
            ],
        ],

        'reorder_indicator' => 'Sıralamak için sürükleyin ve bırakın.',

        'selection_indicator' => [
            'selected_count' => '1 kayıt seçildi|:count kayıt seçildi',
            'actions' => [
                'select_all' => [
                    'label' => 'Tümünü seç (:count)',
                ],
                'deselect_all' => [
                    'label' => 'Tüm seçimleri kaldır',
                ],
            ],
        ],

        'sorting' => [
            'button' => [
                'label' => 'Sıralama',
            ],
        ],
    ],
];
