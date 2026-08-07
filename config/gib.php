<?php

return [
    /*
    |--------------------------------------------------------------------------
    | GİB E-Arşiv Portal Yapılandırması
    |--------------------------------------------------------------------------
    |
    | Gelir İdaresi Başkanlığı E-Arşiv Portal entegrasyonu için gerekli
    | kullanıcı bilgileri ve firma tanımları.
    |
    */

    'user_code' => env('GIB_USER_CODE', ''),
    'password' => env('GIB_PASSWORD', ''),
    'is_test' => env('GIB_TEST_MODE', true),

    // Düzenleyen Firma Bilgileri (Fatura Başlığında Çıkacak)
    'company_name' => env('GIB_COMPANY_NAME', 'Patenli Ayakkabılar E-Ticaret A.Ş.'),
    'company_vkn' => env('GIB_COMPANY_VKN', '1111111111'),
    'company_tax_office' => env('GIB_COMPANY_TAX_OFFICE', 'Kadıköy V.D.'),
    'company_address' => env('GIB_COMPANY_ADDRESS', 'Merkez Mah. Atatürk Cad. No:1'),
    'company_city' => env('GIB_COMPANY_CITY', 'İstanbul'),
    'company_district' => env('GIB_COMPANY_DISTRICT', 'Kadıköy'),
];
