<?php

return [
    'navigation_groups' => [
        'Accounting' => 'Muhasebe',
        'Project Management' => 'Proje Yönetimi',
        'Administration' => 'Yönetim',
        'Company Management' => 'Şirket Yönetimi',
    ],
    
    'sections' => [
        'company_information' => 'Şirket Bilgileri',
        'business_details' => 'İş Detayları',
        'account_information' => 'Hesap Bilgileri',
        'account_relationships' => 'Hesap İlişkileri',
        'project_information' => 'Proje Bilgileri',
        'project_timeline' => 'Proje Zaman Çizelgesi',
        'user_information' => 'Kullanıcı Bilgileri',
        'company_and_role' => 'Şirket ve Rol',
        'task_details' => 'Görev Detayları',
        'schedule_assignment' => 'Zamanlama ve Atama',
        'financial_details' => 'Finansal Detaylar',
    ],
    
    'fields' => [
        'name' => 'Ad',
        'email' => 'E-posta',
        'phone' => 'Telefon',
        'address' => 'Adres',
        'tax_number' => 'Vergi Numarası',
        'logo' => 'Logo',
        'description' => 'Açıklama',
        'is_active' => 'Aktif',
        'is_main' => 'Ana Şirket',
        'owner_id' => 'Şirket Sahibi',
        'created_by' => 'Oluşturan',
        'created_at' => 'Oluşturulma Tarihi',
        'updated_at' => 'Güncellenme Tarihi',
        'status' => 'Durum',
        'title' => 'Başlık',
        'project' => 'Proje',
        'due_date' => 'Bitiş Tarihi',
        'is_completed' => 'Tamamlandı',
        'completed' => 'Tamamlandı',
        'price' => 'Fiyat',
        'cost_percentage' => 'Maliyet Yüzdesi',
        'priority' => 'Öncelik',
        'account_name' => 'Hesap Adı',
        'balance' => 'Bakiye',
        'account_group' => 'Hesap Grubu',
        'uniform_account' => 'Tekdüzen Hesap',
        'account_owner' => 'Hesap Sahibi',
        'company' => 'Şirket',
        'start_date' => 'Başlangıç Tarihi',
        'end_date' => 'Bitiş Tarihi',
        'responsible_person' => 'Sorumlu Kişi',
        'active_status' => 'Aktiflik Durumu',
        'account' => 'Hesap',
        'amount' => 'Tutar',
        'transaction_type' => 'İşlem Türü',
        'user' => 'Kullanıcı',
        'transaction_group' => 'İşlem Grubu',
        'group_name' => 'Grup Adı',
        'group_date' => 'Grup Tarihi',
        'balance_after_transaction' => 'İşlem Sonrası Bakiye',
        'transaction_date' => 'İşlem Tarihi',
        'password' => 'Şifre',
        'position' => 'Pozisyon',
        'roles' => 'Roller',
        'revenue_percentage' => 'Gelir Yüzdesi',
        'minimum_percentage' => 'Minimum Yüzde',
        'maximum_percentage' => 'Maksimum Yüzde',
        'id' => 'ID',
        'guard_name' => 'Guard Adı',
        'permissions' => 'İzinler',
        'transactions' => 'İşlemler',
    ],
    
    'help_texts' => [
        'is_main' => 'Muhasebe işlemleri için ana şirket olarak ayarlayın. Sadece bir şirket ana şirket olabilir.',
        'owner_id' => 'Bu şirketin sahibi. Muhasebe işlemleri için gereklidir.',
        'account_owner' => 'Bu hesabın sahibi olan kullanıcı.',
        'revenue_percentage' => 'Bu kullanıcıya tahsis edilen gelir yüzdesi.',
    ],
    
    'actions' => [
        'create_transaction_group' => 'İşlem Grubu Oluştur',
    ],
    
    'options' => [
        'active' => 'Aktif',
        'inactive' => 'Pasif',
        'debit' => 'Borç',
        'credit' => 'Alacak',
        'unknown' => 'Bilinmiyor',
    ],
    
    'widgets' => [
        'task_summary' => [
            'total_tasks' => 'Toplam Görevler',
            'tasks_in_project' => 'Bu projedeki görevler',
            'task_status' => 'Görev Durumu',
            'financial_summary' => 'Finansal Özet',
            'total' => 'Toplam',
            'paid' => 'Ödenen',
            'get_paid' => 'Tahsil Edilen',
        ],
    ],
    
    'resources' => [
        'AccountResource' => [
            'label' => 'Hesap',
            'plural' => 'Hesaplar',
        ],
        'AccountGroupResource' => [
            'label' => 'Hesap Grubu',
            'plural' => 'Hesap Grupları',
        ],
        'CompanyResource' => [
            'label' => 'Şirket',
            'plural' => 'Şirketler',
        ],
        'ProjectResource' => [
            'label' => 'Proje',
            'plural' => 'Projeler',
        ],
        'TransactionResource' => [
            'label' => 'İşlem',
            'plural' => 'İşlemler',
        ],
        'TransactionGroupResource' => [
            'label' => 'İşlem Grubu',
            'plural' => 'İşlem Grupları',
        ],
        'UserResource' => [
            'label' => 'Kullanıcı',
            'plural' => 'Kullanıcılar',
        ],
        'RoleResource' => [
            'label' => 'Rol',
            'plural' => 'Roller',
        ],
        'PermissionResource' => [
            'label' => 'İzin',
            'plural' => 'İzinler',
        ],
        'TaskResource' => [
            'label' => 'Görev',
            'plural' => 'Görevler',
        ],
    ],
];
