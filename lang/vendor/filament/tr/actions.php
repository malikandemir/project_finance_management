<?php

return [
    'associate' => [
        'modal' => [
            'heading' => ':label İlişkilendir',
            'fields' => [
                'record_id' => [
                    'label' => 'Kayıt',
                ],
            ],
            'actions' => [
                'associate' => [
                    'label' => 'İlişkilendir',
                ],
                'associate_another' => [
                    'label' => 'İlişkilendir ve başka bir tane ilişkilendir',
                ],
            ],
        ],
        'messages' => [
            'associated' => 'İlişkilendirildi',
        ],
    ],

    'attach' => [
        'modal' => [
            'heading' => ':label Ekle',
            'fields' => [
                'record_id' => [
                    'label' => 'Kayıt',
                ],
                'position' => [
                    'label' => 'Pozisyon',
                ],
            ],
            'actions' => [
                'attach' => [
                    'label' => 'Ekle',
                ],
                'attach_another' => [
                    'label' => 'Ekle ve başka bir tane ekle',
                ],
            ],
        ],
        'messages' => [
            'attached' => 'Eklendi',
        ],
    ],

    'create' => [
        'modal' => [
            'heading' => ':label Oluştur',
            'actions' => [
                'create' => [
                    'label' => 'Oluştur',
                ],
                'create_another' => [
                    'label' => 'Oluştur ve başka bir tane oluştur',
                ],
            ],
        ],
        'messages' => [
            'created' => 'Oluşturuldu',
        ],
    ],

    'delete' => [
        'modal' => [
            'heading' => ':label Sil',
            'actions' => [
                'delete' => [
                    'label' => 'Sil',
                ],
            ],
        ],
        'messages' => [
            'deleted' => 'Silindi',
            'deleted_count' => 'Silindi|:count silindi',
        ],
    ],

    'detach' => [
        'modal' => [
            'heading' => ':label Ayır',
            'actions' => [
                'detach' => [
                    'label' => 'Ayır',
                ],
            ],
        ],
        'messages' => [
            'detached' => 'Ayrıldı',
            'detached_count' => 'Ayrıldı|:count ayrıldı',
        ],
    ],

    'dissociate' => [
        'modal' => [
            'heading' => ':label İlişkiyi Kaldır',
            'actions' => [
                'dissociate' => [
                    'label' => 'İlişkiyi Kaldır',
                ],
            ],
        ],
        'messages' => [
            'dissociated' => 'İlişki kaldırıldı',
            'dissociated_count' => 'İlişki kaldırıldı|:count ilişki kaldırıldı',
        ],
    ],

    'edit' => [
        'modal' => [
            'heading' => ':label Düzenle',
            'actions' => [
                'save' => [
                    'label' => 'Kaydet',
                ],
            ],
        ],
        'messages' => [
            'saved' => 'Kaydedildi',
        ],
    ],

    'force_delete' => [
        'modal' => [
            'heading' => ':label Kalıcı Olarak Sil',
            'actions' => [
                'force_delete' => [
                    'label' => 'Kalıcı Olarak Sil',
                ],
            ],
        ],
        'messages' => [
            'force_deleted' => 'Kalıcı olarak silindi',
            'force_deleted_count' => 'Kalıcı olarak silindi|:count kalıcı olarak silindi',
        ],
    ],

    'replicate' => [
        'modal' => [
            'heading' => ':label Kopyala',
            'actions' => [
                'replicate' => [
                    'label' => 'Kopyala',
                ],
            ],
        ],
        'messages' => [
            'replicated' => 'Kopyalandı',
        ],
    ],

    'restore' => [
        'modal' => [
            'heading' => ':label Geri Yükle',
            'actions' => [
                'restore' => [
                    'label' => 'Geri Yükle',
                ],
            ],
        ],
        'messages' => [
            'restored' => 'Geri yüklendi',
            'restored_count' => 'Geri yüklendi|:count geri yüklendi',
        ],
    ],

    'view' => [
        'modal' => [
            'heading' => ':label Görüntüle',
            'actions' => [
                'close' => [
                    'label' => 'Kapat',
                ],
            ],
        ],
    ],

    'bulk_actions' => [
        'modal' => [
            'heading' => 'Seçili :label için Toplu İşlemler',
        ],
    ],
];
