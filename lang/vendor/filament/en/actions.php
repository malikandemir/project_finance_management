<?php

return [
    'associate' => [
        'modal' => [
            'heading' => 'Associate :label',
            'fields' => [
                'record_id' => [
                    'label' => 'Record',
                ],
            ],
            'actions' => [
                'associate' => [
                    'label' => 'Associate',
                ],
                'associate_another' => [
                    'label' => 'Associate & Associate Another',
                ],
            ],
        ],
        'messages' => [
            'associated' => 'Associated',
        ],
    ],

    'attach' => [
        'modal' => [
            'heading' => 'Attach :label',
            'fields' => [
                'record_id' => [
                    'label' => 'Record',
                ],
                'position' => [
                    'label' => 'Position',
                ],
            ],
            'actions' => [
                'attach' => [
                    'label' => 'Attach',
                ],
                'attach_another' => [
                    'label' => 'Attach & Attach Another',
                ],
            ],
        ],
        'messages' => [
            'attached' => 'Attached',
        ],
    ],

    'create' => [
        'modal' => [
            'heading' => 'Create :label',
            'actions' => [
                'create' => [
                    'label' => 'Create',
                ],
                'create_another' => [
                    'label' => 'Create & Create Another',
                ],
            ],
        ],
        'messages' => [
            'created' => 'Created',
        ],
    ],

    'delete' => [
        'modal' => [
            'heading' => 'Delete :label',
            'actions' => [
                'delete' => [
                    'label' => 'Delete',
                ],
            ],
        ],
        'messages' => [
            'deleted' => 'Deleted',
            'deleted_count' => 'Deleted|:count deleted',
        ],
    ],

    'detach' => [
        'modal' => [
            'heading' => 'Detach :label',
            'actions' => [
                'detach' => [
                    'label' => 'Detach',
                ],
            ],
        ],
        'messages' => [
            'detached' => 'Detached',
            'detached_count' => 'Detached|:count detached',
        ],
    ],

    'dissociate' => [
        'modal' => [
            'heading' => 'Dissociate :label',
            'actions' => [
                'dissociate' => [
                    'label' => 'Dissociate',
                ],
            ],
        ],
        'messages' => [
            'dissociated' => 'Dissociated',
            'dissociated_count' => 'Dissociated|:count dissociated',
        ],
    ],

    'edit' => [
        'modal' => [
            'heading' => 'Edit :label',
            'actions' => [
                'save' => [
                    'label' => 'Save',
                ],
            ],
        ],
        'messages' => [
            'saved' => 'Saved',
        ],
    ],

    'force_delete' => [
        'modal' => [
            'heading' => 'Force Delete :label',
            'actions' => [
                'force_delete' => [
                    'label' => 'Force Delete',
                ],
            ],
        ],
        'messages' => [
            'force_deleted' => 'Force deleted',
            'force_deleted_count' => 'Force deleted|:count force deleted',
        ],
    ],

    'replicate' => [
        'modal' => [
            'heading' => 'Replicate :label',
            'actions' => [
                'replicate' => [
                    'label' => 'Replicate',
                ],
            ],
        ],
        'messages' => [
            'replicated' => 'Replicated',
        ],
    ],

    'restore' => [
        'modal' => [
            'heading' => 'Restore :label',
            'actions' => [
                'restore' => [
                    'label' => 'Restore',
                ],
            ],
        ],
        'messages' => [
            'restored' => 'Restored',
            'restored_count' => 'Restored|:count restored',
        ],
    ],

    'view' => [
        'modal' => [
            'heading' => 'View :label',
            'actions' => [
                'close' => [
                    'label' => 'Close',
                ],
            ],
        ],
    ],

    'bulk_actions' => [
        'modal' => [
            'heading' => 'Bulk Actions for Selected :label',
        ],
    ],
];
