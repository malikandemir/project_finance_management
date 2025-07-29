<?php

return [
    'pagination' => [
        'label' => 'Sayfalandırma Navigasyonu',
        'overview' => ':first ile :last arası, toplam :total sonuç',
        'fields' => [
            'records_per_page' => [
                'label' => 'Sayfa başına',
            ],
        ],
        'actions' => [
            'go_to_page' => [
                'label' => 'Sayfaya git :page',
            ],
            'next' => [
                'label' => 'Sonraki',
            ],
            'previous' => [
                'label' => 'Önceki',
            ],
        ],
    ],

    'fields' => [
        'search' => [
            'label' => 'Ara',
            'placeholder' => 'Ara',
        ],
    ],

    'modal' => [
        'actions' => [
            'close' => [
                'label' => 'Kapat',
            ],
        ],
    ],
];
