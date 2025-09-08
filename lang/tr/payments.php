<?php

return [
    'get_paid' => [
        'label' => 'Ödeme Al',
        'modal_heading' => 'Ödeme Al',
        'modal_description' => 'Bu görev için ödeme almak üzere ana şirket hesabını (100 veya 102) seçin.',
        'success' => 'Ödeme başarıyla alındı',
        'error' => 'Ödeme işlemi sırasında hata oluştu',
    ],
    'pay' => [
        'label' => 'Öde',
        'modal_heading' => 'Öde',
        'modal_description' => 'Bu görev için ödeme yapmak üzere ana şirket hesabını (100 veya 102) seçin.',
        'success' => 'Ödeme başarıyla işlendi',
        'error' => 'Ödeme işlemi sırasında hata oluştu',
    ],
    'account' => [
        'label' => 'Ana Şirket Hesabı',
    ],
    'project' => [
        'get_paid' => [
            'label' => 'Ödeme Al',
            'modal_heading' => 'Projeden Ödeme Al',
            'modal_description' => 'Bu projeden ödeme almak için ana şirketten bir hesap seçin.',
            'success' => 'Ödeme Başarıyla İşlendi',
            'error' => 'Ödeme İşleminde Hata',
            'amount' => 'Tutar',
            'description' => 'Açıklama',
            'default_description' => 'Projeden alınan ödeme: :name',
            'default_group_name' => 'Proje için ödeme: :name',
            'group_name' => 'Grup Adı',
        ],
        'payment_status' => [
            'title' => 'Ödeme Durumu',
            'total_price' => 'Toplam Proje Fiyatı',
            'total_paid' => 'Toplam Ödenen Tutar',
            'status' => 'Ödeme Durumu',
            'fully_paid' => 'Tamamen Ödendi',
            'partially_paid' => 'Kısmen Ödendi (%:percentage)',
            'no_price' => 'Fiyat Belirlenmedi',
        ],
    ],
];
