<?php

namespace Database\Seeders;

use App\Models\CustomerSegment;
use Illuminate\Database\Seeder;

class CustomerSegmentSeeder extends Seeder
{
    public function run(): void
    {
        $segments = [
            [
                'name' => 'VIP Müşteriler',
                'slug' => 'vip',
                'description' => 'Yüksek yaşam boyu değer, düşük kayıp riski olan en değerli müşteriler.',
                'type' => 'dynamic',
                'conditions' => ['min_ltv' => 500, 'max_risk' => 30, 'min_orders' => 3],
                'color' => '#f59e0b',
                'icon' => 'star',
            ],
            [
                'name' => 'Aktif Alıcılar',
                'slug' => 'aktif-alicilar',
                'description' => 'Son 30 günde sipariş vermiş müşteriler.',
                'type' => 'dynamic',
                'conditions' => ['max_days_since_order' => 30],
                'color' => '#10b981',
                'icon' => 'shopping-bag',
            ],
            [
                'name' => 'Kayıp Riski',
                'slug' => 'kayip-riski',
                'description' => '60+ gündür alışveriş yapmamış, daha önce en az 1 sipariş vermiş müşteriler.',
                'type' => 'dynamic',
                'conditions' => ['min_days_since_order' => 60, 'min_orders' => 1],
                'color' => '#ef4444',
                'icon' => 'exclamation-triangle',
            ],
            [
                'name' => 'Yeni Müşteriler',
                'slug' => 'yeni-musteriler',
                'description' => 'Son 7 günde kayıt olmuş müşteriler.',
                'type' => 'dynamic',
                'conditions' => ['max_days_since_registration' => 7],
                'color' => '#3b82f6',
                'icon' => 'user-plus',
            ],
            [
                'name' => 'Sadık Müşteriler',
                'slug' => 'sadik-musteriler',
                'description' => '5+ sipariş vermiş, düzenli alışveriş yapan müşteriler.',
                'type' => 'dynamic',
                'conditions' => ['min_orders' => 5, 'max_risk' => 40],
                'color' => '#8b5cf6',
                'icon' => 'heart',
            ],
            [
                'name' => 'Tek Seferlik Alıcılar',
                'slug' => 'tek-seferlik',
                'description' => 'Sadece 1 sipariş vermiş müşteriler.',
                'type' => 'dynamic',
                'conditions' => ['exact_orders' => 1],
                'color' => '#6b7280',
                'icon' => 'minus-circle',
            ],
        ];

        foreach ($segments as $segment) {
            CustomerSegment::firstOrCreate(
                ['slug' => $segment['slug']],
                $segment
            );
        }
    }
}
