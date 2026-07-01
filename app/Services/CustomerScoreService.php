<?php

namespace App\Services;

use App\Models\CustomerScore;
use App\Models\Order;
use App\Models\User;
use App\Models\CustomerEvent;
use Carbon\Carbon;

class CustomerScoreService
{
    /**
     * Tek bir müşteri için tüm skorları hesapla ve kaydet.
     */
    public function calculateForUser(User $user): CustomerScore
    {
        $score = CustomerScore::firstOrNew(['user_id' => $user->id]);

        $score->purchase_score = $this->calculatePurchaseScore($user);
        $score->activity_score = $this->calculateActivityScore($user);
        $score->loyalty_score = $this->calculateLoyaltyScore($user);
        $score->engagement_score = $this->calculateEngagementScore($user);
        $score->risk_score = $this->calculateRiskScore($user);
        $score->lifetime_value = $this->calculateLifetimeValue($user);
        $score->avg_order_value = $this->calculateAvgOrderValue($user);
        $score->total_orders = $user->orders()->count();
        $score->days_since_last_order = $this->daysSinceLastOrder($user);
        $score->days_since_last_activity = $this->daysSinceLastActivity($user);
        $score->predicted_churn_probability = $this->predictChurnProbability($score);
        $score->last_calculated_at = now();

        $score->save();

        return $score;
    }

    /**
     * Tüm müşteriler için skorları hesapla.
     */
    public function calculateAll(): int
    {
        $count = 0;
        User::where('role', 'customer')->chunk(100, function ($users) use (&$count) {
            foreach ($users as $user) {
                $this->calculateForUser($user);
                $count++;
            }
        });
        return $count;
    }

    /**
     * Satın Alma Skoru (0-100)
     * Faktörler: Sipariş sayısı, toplam harcama, son sipariş tarihi
     */
    private function calculatePurchaseScore(User $user): int
    {
        $orderCount = $user->orders()->count();
        $totalSpent = (float) $user->orders()->sum('grand_total');
        $lastOrder = $user->orders()->latest()->first();

        if ($orderCount === 0) return 0;

        // Sipariş sayısı puanı (max 30)
        $countScore = min(30, $orderCount * 6);

        // Harcama puanı (max 40) - 5000₺ üstü tam puan
        $spendScore = min(40, ($totalSpent / 5000) * 40);

        // Güncellik puanı (max 30) - son 7 gün = tam, 90+ gün = 0
        $recencyScore = 0;
        if ($lastOrder) {
            $daysSince = $lastOrder->created_at->diffInDays(now());
            $recencyScore = max(0, 30 - ($daysSince * 0.33));
        }

        return (int) min(100, round($countScore + $spendScore + $recencyScore));
    }

    /**
     * Aktivite Skoru (0-100)
     * Son 30 gündeki event sayısına göre
     */
    private function calculateActivityScore(User $user): int
    {
        $eventCount = CustomerEvent::forUser($user->id)->recent(30)->count();

        if ($eventCount === 0) return 0;

        // 50+ event = tam puan
        return (int) min(100, round(($eventCount / 50) * 100));
    }

    /**
     * Sadakat Skoru (0-100)
     * Faktörler: Kayıt süresi, tekrar alışveriş oranı, yorum sayısı
     */
    private function calculateLoyaltyScore(User $user): int
    {
        $daysSinceRegistration = $user->created_at->diffInDays(now());
        $orderCount = $user->orders()->count();
        $reviewCount = $user->reviews()->count();

        // Kayıt süresi puanı (max 30) - 365 gün = tam
        $tenureScore = min(30, ($daysSinceRegistration / 365) * 30);

        // Tekrar alışveriş puanı (max 50) - 5+ sipariş = tam
        $repeatScore = min(50, $orderCount * 10);

        // Yorum katkısı puanı (max 20) - 4+ yorum = tam
        $reviewScore = min(20, $reviewCount * 5);

        return (int) min(100, round($tenureScore + $repeatScore + $reviewScore));
    }

    /**
     * Etkileşim Skoru (0-100)
     * Son 7 gündeki çeşitli event türleri
     */
    private function calculateEngagementScore(User $user): int
    {
        $recentEvents = CustomerEvent::forUser($user->id)->recent(7);

        $productViews = (clone $recentEvents)->ofType('product_view')->count();
        $cartActions = (clone $recentEvents)->whereIn('event_type', ['add_to_cart', 'remove_from_cart'])->count();
        $searches = (clone $recentEvents)->ofType('search')->count();
        $purchases = (clone $recentEvents)->ofType('purchase')->count();

        // Ağırlıklı hesaplama
        $score = ($productViews * 2) + ($cartActions * 5) + ($searches * 3) + ($purchases * 20);

        return (int) min(100, $score);
    }

    /**
     * Risk Skoru (0-100) — Yüksek = kayıp riski yüksek
     */
    private function calculateRiskScore(User $user): int
    {
        $lastOrder = $user->orders()->latest()->first();
        $lastEvent = CustomerEvent::forUser($user->id)->first(); // latest by default

        if (!$lastOrder && !$lastEvent) return 50; // Yeni müşteri, orta risk

        $daysSinceOrder = $lastOrder ? $lastOrder->created_at->diffInDays(now()) : 999;
        $daysSinceActivity = $lastEvent ? $lastEvent->created_at->diffInDays(now()) : 999;

        $minDays = min($daysSinceOrder, $daysSinceActivity);

        return match (true) {
            $minDays <= 7 => 0,
            $minDays <= 14 => 15,
            $minDays <= 30 => 30,
            $minDays <= 60 => 55,
            $minDays <= 90 => 75,
            default => 95,
        };
    }

    private function calculateLifetimeValue(User $user): float
    {
        return (float) $user->orders()->sum('grand_total');
    }

    private function calculateAvgOrderValue(User $user): float
    {
        return (float) ($user->orders()->avg('grand_total') ?? 0);
    }

    private function daysSinceLastOrder(User $user): ?int
    {
        $lastOrder = $user->orders()->latest()->first();
        return $lastOrder ? (int) $lastOrder->created_at->diffInDays(now()) : null;
    }

    private function daysSinceLastActivity(User $user): ?int
    {
        $lastEvent = CustomerEvent::forUser($user->id)->first();
        return $lastEvent ? (int) $lastEvent->created_at->diffInDays(now()) : null;
    }

    /**
     * Basit churn prediction: risk skoru + satın alma sıklığına göre
     */
    private function predictChurnProbability(CustomerScore $score): float
    {
        if ($score->total_orders === 0) return 0.50;

        $riskFactor = $score->risk_score / 100;
        $purchaseFactor = 1 - ($score->purchase_score / 100);

        return round(($riskFactor * 0.6 + $purchaseFactor * 0.4), 2);
    }
}
