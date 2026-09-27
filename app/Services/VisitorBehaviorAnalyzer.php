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
        $previousPath = $clientData['previous_path'] ?? null;
        $normalizePath = function (?string $p): string {
            if (!$p) return '/';
            $p = parse_url($p, PHP_URL_PATH) ?: $p;
            $p = '/' . ltrim($p, '/');
            return rtrim($p, '/') ?: '/';
        };

        $normalizedNewPath = $normalizePath($newPath);
        $normalizedPrevPath = $previousPath !== null ? $normalizePath($previousPath) : null;

        $trail = $visitor->journey_trail ?? [];
        if ($visitor->exists && !empty($visitor->id)) {
            $latestDbTrail = \Illuminate\Support\Facades\DB::table('active_visitors')
                ->where('id', $visitor->id)
                ->value('journey_trail');
            if ($latestDbTrail) {
                $decoded = is_array($latestDbTrail) ? $latestDbTrail : json_decode($latestDbTrail, true);
                if (is_array($decoded) && !empty($decoded)) {
                    $trail = $decoded;
                }
            }
        }

        $nowFormatted = now()->format('H:i:s');
        $isFirstStep = empty($trail);
        $lastIndex = count($trail) - 1;
        $lastStepNormalized = ($lastIndex >= 0 && isset($trail[$lastIndex]['path']))
            ? $normalizePath($trail[$lastIndex]['path'])
            : null;

        $isPathChanged = ($normalizedPrevPath !== null && $normalizedPrevPath !== $normalizedNewPath);
        $isSamePage = (!$isFirstStep && $lastIndex >= 0 && $lastStepNormalized === $normalizedNewPath);
        $actionDetailLower = static::cleanLowercase($actionDetail);

        // İkon ve mikro işlem tipi belirleme (il, ilçe, adres, ödeme vb. için özel sezgisel ikonlar)
        $microIcon = '🖱️';
        if ($action === 'typing' || $action === 'input') {
            $microIcon = '⌨️';
        } elseif ($action === 'focus') {
            $microIcon = '🎯';
        } elseif (str_contains($actionDetailLower ?? '', 'şehir') || str_contains($actionDetailLower ?? '', 'il:') || str_contains($actionDetailLower ?? '', 'il (şehir)') || str_contains($actionDetailLower ?? '', 'ilçe') || str_contains($actionDetailLower ?? '', 'mahalle') || str_contains($actionDetailLower ?? '', 'adres')) {
            $microIcon = '📍';
        } elseif (str_contains($actionDetailLower ?? '', 'ödeme') || str_contains($actionDetailLower ?? '', 'kart')) {
            $microIcon = '💳';
        } elseif ($action === 'coupon' || str_contains($actionDetailLower ?? '', 'kupon')) {
            $microIcon = '🏷️';
        } elseif ($action === 'whatsapp' || str_contains($actionDetailLower ?? '', 'whatsapp')) {
            $microIcon = '💬';
        } elseif ($action === 'cart_add' || str_contains($actionDetailLower ?? '', 'sepet')) {
            $microIcon = '🛒';
        } elseif (str_contains($actionDetailLower ?? '', 'fatura') || str_contains($actionDetailLower ?? '', 'vergi')) {
            $microIcon = '📑';
        } elseif (str_contains($actionDetailLower ?? '', 'not') || str_contains($actionDetailLower ?? '', 'sipariş notu')) {
            $microIcon = '📝';
        } elseif ($action === 'tab' || $action === 'tab_switch' || $action === 'view') {
            $microIcon = '👁️';
        }

        if ($isFirstStep || $isPathChanged || !empty($actionDetail) || $isSamePage) {
            $pageInfo = ActiveVisitor::resolvePageInfo($newPath, $newTitle);
            $cleanTitle = ActiveVisitor::cleanTitle($newTitle);
            $stepTitle = (!empty($cleanTitle) && strcasecmp($cleanTitle, 'Patenli Ayakkabılar') !== 0)
                ? $cleanTitle
                : $pageInfo['title'];

            // Eğer sayfa değişmediyse (aynı sayfada mikro hareket veya tekrarlı heartbeat)
            if ($isSamePage) {
                $trail[$lastIndex]['time'] = $nowFormatted;
                $trail[$lastIndex]['title'] = $stepTitle;
                $trail[$lastIndex]['badge'] = $pageInfo['badge'];
                $trail[$lastIndex]['icon'] = $pageInfo['icon'];
                $trail[$lastIndex]['color'] = $pageInfo['color'];
                if (!empty($pageInfo['image'])) {
                    $trail[$lastIndex]['image'] = $pageInfo['image'];
                }
                if (!empty($pageInfo['price'])) {
                    $trail[$lastIndex]['price'] = $pageInfo['price'];
                }

                // Mikro etkileşim varsa ve son etkileşimlerde aynı değilse interactions dizisine ekle
                if (!empty($actionDetailLower)) {
                    $interactions = $trail[$lastIndex]['interactions'] ?? [];
                    
                    $isDuplicate = false;
                    $checkLimit = min(5, count($interactions));
                    for ($i = count($interactions) - 1; $i >= count($interactions) - $checkLimit; $i--) {
                        if (isset($interactions[$i]['text']) && $interactions[$i]['text'] === $actionDetailLower) {
                            $isDuplicate = true;
                            break;
                        }
                    }

                    if (!$isDuplicate) {
                        $interactions[] = [
                            'time' => $nowFormatted,
                            'type' => $action,
                            'icon' => $microIcon,
                            'text' => $actionDetailLower,
                        ];
                        // Müşterinin hiçbir adımı kaybolmasın: sayfa başına 50 mikro hareket
                        if (count($interactions) > 50) {
                            $interactions = array_slice($interactions, -50);
                        }
                        $trail[$lastIndex]['interactions'] = $interactions;
                    }
                    $trail[$lastIndex]['detail'] = $actionDetailLower;
                    $trail[$lastIndex]['action'] = $action;
                }

                $visitor->journey_trail = $trail;
            } else {
                // Sayfa değişti veya ilk adım
                if (!empty($trail) && $lastIndex >= 0 && !isset($trail[$lastIndex]['end_time'])) {
                    $trail[$lastIndex]['end_time'] = $nowFormatted;
                }

                $newStep = [
                    'path' => $newPath,
                    'title' => $stepTitle,
                    'badge' => $pageInfo['badge'],
                    'icon' => $pageInfo['icon'],
                    'color' => $pageInfo['color'],
                    'image' => $pageInfo['image'] ?? null,
                    'price' => $pageInfo['price'] ?? null,
                    'time' => $nowFormatted,
                    'action' => $action,
                    'detail' => $actionDetailLower,
                    'interactions' => [],
                ];

                if (!empty($actionDetailLower)) {
                    $newStep['interactions'][] = [
                        'time' => $nowFormatted,
                        'type' => $action,
                        'icon' => $microIcon,
                        'text' => $actionDetailLower,
                    ];
                }

                $trail[] = $newStep;

                // Müşterinin gezindiği tüm sayfalar korunsun: 30 sayfa adımı
                if (count($trail) > 30) {
                    $trail = array_slice($trail, -30);
                }

                $visitor->journey_trail = $trail;
                if ($isPathChanged) {
                    $visitor->page_views_count = ($visitor->page_views_count ?? 0) + 1;
                }
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
            'popup_title' => '🔥 Sezonun En Popüler Modelleri',
            'description' => 'Ziyaretçiyi en popüler ve yüksek dönüşümlü modellerimize aktarın.',
            'action_type' => 'redirect',
            'target_url' => '/patenli-ayakkabilar',
            'suggested_message' => 'Bu sezonun en çok satan ışıklı paten modellerini inceleyin!',
            'badge_color' => 'info',
        ];

        // Durum A: Sepette Ürün Var ve Ödeme (Checkout) Sayfasında
        if ($cartItemsCount > 0 && str_contains($newPath, 'checkout')) {
            $hasInfo = !empty($visitor->guest_name) || !empty($visitor->guest_phone) || !empty($visitor->guest_email);
            $intentScore = $hasInfo ? 95 : 88;
            $intentLevel = $hasInfo ? 'hot' : 'hesitating';

            $displayName = $visitor->display_name;
            if ($hasInfo) {
                $contactNote = !empty($visitor->guest_phone) ? " (Tel: {$visitor->guest_phone})" : '';
                $insight = "🔥 {$displayName} ödeme formunda bilgilerini dolduruyor!{$contactNote} Sepetinde {$cartItemsCount} ürün (" . number_format($cartTotal, 2) . " ₺) bekliyor.";
            } else {
                $insight = "Ödeme adımında bekliyor! Sepetinde {$cartItemsCount} ürün (" . number_format($cartTotal, 2) . " ₺) var. Kargo ücreti veya ödeme bariyeri olabilir.";
            }

            $strategy = [
                'key' => 'checkout_coupon',
                'title' => '%10 Sepet İndirim Kuponu',
                'popup_title' => '🎉 Size Özel Sepette %10 İndirim Fırsatı!',
                'description' => 'Ödeme bariyerini aşması için ekranda hemen geçerli %10 kupon bannerı sunun.',
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
                'popup_title' => '🛒 Sepetiniz Sizi Bekliyor!',
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
                    'popup_title' => '👟 Numara Konusunda Kararsız mısınız?',
                    'description' => 'Çocuğunuzun ayak ölçüsüne göre doğru numarayı seçmesi için WhatsApp destek teklifi gönderin.',
                    'action_type' => 'offer',
                    'suggested_message' => 'Çocuğunuzun ayak numarasından emin değil misiniz? Uzman ekibimize WhatsApp üzerinden anında danışabilirsiniz!',
                    'action_button' => 'WhatsApp Destek Hattı',
                    'action_url' => 'https://wa.me/908503073164?text=' . urlencode('Merhaba, patenli ayakkabı bedeni hakkında danışmak istiyorum.'),
                    'badge_color' => 'warning',
                ];
            } else {
                $intentScore = 50;
                $intentLevel = 'interested';
                $insight = "Model detaylarını inceliyor. İlk ilgi oluştu.";
                $strategy = [
                    'key' => 'product_urgency',
                    'title' => 'Hızlı Kargo & Güven Bildirimi',
                    'popup_title' => '⚡ Hızlı Kargo & Kolay Numara Değişimi',
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
                'title' => 'İlk Alışverişe Özel İndirim',
                'popup_title' => '🎁 Hoş Geldiniz! Size Özel %10 İndirim',
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

    /**
     * Türkçe karakter uyumlu temiz küçük harf dönüşümü yapar.
     */
    public static function cleanLowercase(?string $text): ?string
    {
        if ($text === null || $text === '') {
            return null;
        }

        $normalized = str_replace(['İ', 'I', 'i̇'], ['i', 'ı', 'i'], trim($text));

        return mb_strtolower($normalized, 'UTF-8');
    }
}
