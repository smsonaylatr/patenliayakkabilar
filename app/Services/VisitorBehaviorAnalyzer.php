<?php

namespace App\Services;

use App\Models\ActiveVisitor;

class VisitorBehaviorAnalyzer
{
    /**
     * Ziyaretçinin adımlarını analiz eder, satın alma niyetini puanlar,
     * davranış yorumu ve kişiselleştirilmiş strateji üretir.
     */
    public function analyze(ActiveVisitor $visitor, array $clientData): void
    {
        $newPath = $clientData['path'] ?? '/';
        $newTitle = $clientData['title'] ?? 'Sayfa';
        $action = $clientData['action'] ?? 'view'; // view, size_click, tab_switch
        $actionDetail = $clientData['action_detail'] ?? null;

        // 1. Gezinme İzini (Journey Trail) Güncelle
        $trail = $visitor->journey_trail ?? [];
        $previousPath = $clientData['previous_path'] ?? null;
        $nowFormatted = now()->format('H:i:s');

        $isFirstStep = empty($trail);
        $isPathChanged = ($previousPath !== null && $previousPath !== $newPath);

        if ($isFirstStep || $isPathChanged || !empty($actionDetail)) {
            // Önceki adım varsa süresini güncelle
            if (!empty($trail)) {
                $lastIndex = count($trail) - 1;
                if (!isset($trail[$lastIndex]['end_time'])) {
                    $trail[$lastIndex]['end_time'] = $nowFormatted;
                }
            }

            // Yeni adımı ekle
            $trail[] = [
                'path' => $newPath,
                'title' => $newTitle,
                'time' => $nowFormatted,
                'action' => $action,
                'detail' => $actionDetail,
            ];

            // Trail maksimum 10 adımda tutulsun (hafiflik için)
            if (count($trail) > 10) {
                $trail = array_slice($trail, -10);
            }

            $visitor->journey_trail = $trail;
            if ($isPathChanged) {
                $visitor->page_views_count = ($visitor->page_views_count ?? 0) + 1;
            }
        }

        // Toplam geçirilen süreyi güncelle
        $totalSeconds = (int) max(0, $visitor->first_seen_at ? now()->diffInSeconds($visitor->first_seen_at) : 0);
        $visitor->time_spent_seconds = $totalSeconds;

        // 2. Davranış Heuristiği ve Satın Alma Niyet Skoru
        $cartItemsCount = (int) ($visitor->cart_items_count ?? 0);
        $cartTotal = (float) ($visitor->cart_total ?? 0.00);
        $pageViews = (int) ($visitor->page_views_count ?? 1);

        // Varsayılan Değerler
        $intentScore = 15;
        $intentLevel = 'browsing';
        $insight = 'Sitede genel keşif yapıyor, henüz karar aşamasında.';
        $strategy = [
            'key' => 'best_sellers',
            'title' => 'En Çok Satanlara Yönlendir',
            'description' => 'Ziyaretçiyi en popüler ve yüksek dönüşümlü modellerimize aktarın.',
            'action_type' => 'redirect',
            'target_url' => '/patenli-ayakkabilar',
            'suggested_message' => 'Bu sezonun en çok satan ışıklı paten modellerini inceleyin!',
            'badge_color' => 'info',
        ];

        // Durum A: Sepette Ürün Var ve Ödeme (Checkout) Sayfasında
        if ($cartItemsCount > 0 && str_contains($newPath, 'checkout')) {
            $intentScore = 88;
            $intentLevel = 'hesitating';
            $insight = "Ödeme adımında bekliyor! Sepetinde {$cartItemsCount} ürün (" . number_format($cartTotal, 2) . " ₺) var. Kargo ücreti veya ödeme bariyeri olabilir.";
            $strategy = [
                'key' => 'checkout_coupon',
                'title' => '%10 İndirim Kuponu Fırlat',
                'description' => 'Ödeme bariyerini aşması için ekranda hemen geçerli %10 kupon bannerı fırlatın.',
                'action_type' => 'offer',
                'suggested_coupon' => 'PATEN10',
                'suggested_message' => 'Alışverişinizi hemen tamamlamanız için size özel %10 indirim kuponu: PATEN10!',
                'action_button' => 'Kuponu Uygula & Öde',
                'action_url' => '/checkout',
                'badge_color' => 'danger',
            ];
        }
        // Durum B: Sepette Ürün Var ama Başka Sayfada Geziniyor (Sepet Terk Riski)
        elseif ($cartItemsCount > 0) {
            $intentScore = 78;
            $intentLevel = 'interested';
            $insight = "Sepetinde " . number_format($cartTotal, 2) . " ₺ değerinde ürün bulunuyor ancak ödemeye gitmedi, ürün bakmaya devam ediyor.";
            $strategy = [
                'key' => 'direct_checkout',
                'title' => 'Ödeme Sayfasına Yönlendir',
                'description' => 'Sepetindeki ürünleri hatırlatıp doğrudan güvenli ödeme ekranına yönlendirin.',
                'action_type' => 'redirect',
                'target_url' => '/checkout',
                'suggested_message' => 'Sepetiniz hazır! Hemen güvenle siparişinizi tamamlayabilirsiniz.',
                'badge_color' => 'warning',
            ];
        }
        // Durum C: Ürün Sayfasında Beden İncelemesi / Uzun Kalış (Beden Tereddütü)
        elseif (str_contains($newPath, 'urun/') || $action === 'size_click') {
            if ($action === 'size_click' || $totalSeconds > 90) {
                $intentScore = 65;
                $intentLevel = 'hesitating';
                $insight = "Ürün detayında yoğunlaştı ve beden/numara seçeneklerini inceliyor. Çocuk ayak numarası veya kalıp tereddütü olabilir.";
                $strategy = [
                    'key' => 'size_help',
                    'title' => 'Canlı Beden & WhatsApp Desteği',
                    'description' => 'Çocuğunuzun ayak ölçüsüne göre doğru numarayı seçmesi için WhatsApp destek teklifi gönderin.',
                    'action_type' => 'offer',
                    'suggested_message' => 'Çocuğunuzun ayak numarasından emin değil misiniz? Uzman ekibimize WhatsApp üzerinden anında danışabilirsiniz!',
                    'action_button' => 'WhatsApp Destek Hattı',
                    'action_url' => 'https://wa.me/905000000000?text=Merhaba,%20patenli%20ayakkabi%20bedeni%20hakkinda%20danismak%20istiyorum',
                    'badge_color' => 'warning',
                ];
            } else {
                $intentScore = 50;
                $intentLevel = 'interested';
                $insight = "Model detaylarını inceliyor. İlk ilgi oluştu.";
                $strategy = [
                    'key' => 'product_urgency',
                    'title' => 'Hızlı Kargo & Güven Bildirimi',
                    'description' => 'Aynı gün kargo ve 14 gün ücretsiz değişim güvencesini hatırlatın.',
                    'action_type' => 'offer',
                    'suggested_message' => 'Bugün saat 14:00\'e kadar verilen tüm siparişler aynı gün kargoda! 14 gün kolay numara değişimi garantisi.',
                    'badge_color' => 'primary',
                ];
            }
        }
        // Durum D: Çok Sayıda Sayfa Gezmiş Sıcak Aday
        elseif ($pageViews >= 4 || $totalSeconds > 180) {
            $intentScore = 60;
            $intentLevel = 'high_intent';
            $insight = "Siteyi detaylı inceledi ({$pageViews} sayfa, " . floor($totalSeconds / 60) . " dakika). Ciddi alışveriş niyetinde sıcak aday.";
            $strategy = [
                'key' => 'special_welcome',
                'title' => 'Özel İndirim Bildirimi Fırlat',
                'description' => 'Satın almayı tetiklemek için ilk alışverişe özel indirim teklif edin.',
                'action_type' => 'offer',
                'suggested_coupon' => 'HOSGELDIN',
                'suggested_message' => 'Bugüne özel tüm patenli ayakkabılarda ücretsiz kargo ve ek indirim fırsatını kaçırmayın!',
                'action_button' => 'Modelleri İncele',
                'action_url' => '/patenli-ayakkabilar',
                'badge_color' => 'success',
            ];
        }

        // Değerleri kaydet
        $visitor->intent_score = $intentScore;
        $visitor->intent_level = $intentLevel;
        $visitor->behavior_insight = $insight;
        $visitor->recommended_strategy = $strategy;
    }
}
