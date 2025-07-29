<?php

return [
    'columns' => [
        'text' => [
            'more_list_items' => 've :count daha',
        ],
    ],

    'fields' => [
        'bulk_select_page' => [
            'label' => 'Toplu işlemler için tüm öğeleri seç/seçimi kaldır.',
        ],
        'bulk_select_record' => [
            'label' => 'Toplu işlemler için :key öğesini seç/seçimi kaldır.',
        ],
        'search' => [
            'label' => 'Ara',
            'placeholder' => 'Ara',
            'indicator' => 'Ara',
        ],
    ],

    'summary' => [
        'heading' => 'Özet',
        'subheading' => 'Gösterilen :total sonuçtan :count tanesi.',
    ],

    'actions' => [
        'disable_reordering' => [
            'label' => 'Kayıtları yeniden sıralamayı bitir',
        ],
        'enable_reordering' => [
            'label' => 'Kayıtları yeniden sırala',
        ],
        'filter' => [
            'label' => 'Filtrele',
        ],
        'open_bulk_actions' => [
            'label' => 'Toplu işlemler',
        ],
        'toggle_columns' => [
            'label' => 'Sütunları aç/kapat',
        ],
    ],

    'empty' => [
        'heading' => 'Kayıt bulunamadı',
        'description' => 'Yeni bir :model oluşturun',
    ],

    'filters' => [
        'actions' => [
            'remove' => [
                'label' => 'Filtreyi kaldır',
            ],
            'remove_all' => [
                'label' => 'Tüm filtreleri kaldır',
                'tooltip' => 'Tüm filtreleri kaldır',
            ],
            'reset' => [
                'label' => 'Filtreleri sıfırla',
            ],
        ],

        'indicator' => 'Aktif filtreler',

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
        'fields' => [
            'group' => [
                'label' => 'Grupla',
                'placeholder' => 'Grupla',
            ],
            'direction' => [
                'label' => 'Gruplama yönü',
                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],
            ],
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
        'fields' => [
            'column' => [
                'label' => 'Sırala',
            ],
            'direction' => [
                'label' => 'Sıralama yönü',
                'options' => [
                    'asc' => 'Artan',
                    'desc' => 'Azalan',
                ],
            ],
        ],
    ],
];
