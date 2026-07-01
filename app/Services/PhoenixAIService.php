<?php

namespace App\Services;

use App\Models\AiRecommendation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\CustomerScore;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PhoenixAIService
{
    /**
     * Tüm kuralları çalıştırıp öneriler üret.
     */
    public function generateRecommendations(): int
    {
        // Eski tamamlanmış/reddedilmiş önerileri temizle (7 günden eski)
        AiRecommendation::whereIn('status', ['dismissed', 'completed'])
            ->where('updated_at', '<', now()->subDays(7))
            ->delete();

        $count = 0;
        $count += $this->checkLowStock();
        $count += $this->checkInactiveCustomers();
        $count += $this->checkHighValueCustomersAtRisk();
        $count += $this->checkRevenueTrend();
        $count += $this->checkAbandonedCarts();
        $count += $this->checkVIPOpportunities();
        $count += $this->runChatGPTAnalysis();

        return $count;
    }

    /**
     * Stoku kritik seviyedeki ürünler
     */
    private function checkLowStock(): int
    {
        $count = 0;
        $products = Product::where('stock', '<=', 3)
            ->where('status', true)
            ->whereNull('deleted_at')
            ->get();

        foreach ($products as $product) {
            $exists = AiRecommendation::where('type', 'stock_alert')
                ->where('status', 'pending')
                ->whereJsonContains('action_data->product_id', $product->id)
                ->exists();

            if (!$exists) {
                AiRecommendation::create([
                    'type' => 'stock_alert',
                    'priority' => $product->stock === 0 ? 'critical' : 'high',
                    'title' => $product->stock === 0
                        ? "⛔ {$product->name} STOKTA YOK"
                        : "⚠️ {$product->name} stoku kritik ({$product->stock} adet)",
                    'description' => "Bu ürün " . ($product->best_seller ? 'çok satan bir ürün ve ' : '') . "stoku tükenmek üzere. Tedarikçiden sipariş verilmeli.",
                    'action_data' => ['product_id' => $product->id, 'current_stock' => $product->stock],
                    'expires_at' => now()->addDays(7),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * 30+ gündür aktif olmayan müşteriler
     */
    private function checkInactiveCustomers(): int
    {
        $inactiveUsers = CustomerScore::where('days_since_last_order', '>=', 30)
            ->where('total_orders', '>=', 1)
            ->where('risk_score', '>=', 60)
            ->limit(10)
            ->get();

        $count = 0;
        foreach ($inactiveUsers as $score) {
            $exists = AiRecommendation::where('type', 'customer_retention')
                ->where('user_id', $score->user_id)
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                $user = $score->user;
                if (!$user) continue;

                AiRecommendation::create([
                    'type' => 'customer_retention',
                    'priority' => $score->risk_score >= 80 ? 'high' : 'medium',
                    'title' => "🔴 {$user->name} kayıp riski yüksek",
                    'description' => "{$score->days_since_last_order} gündür sipariş vermedi. Toplam {$score->total_orders} siparişi ve " . number_format($score->lifetime_value, 2) . " ₺ yaşam boyu değeri var. Geri kazanım kampanyası önerilir.",
                    'action_data' => ['action' => 'retention_campaign', 'suggested_discount' => '15%'],
                    'user_id' => $score->user_id,
                    'expires_at' => now()->addDays(14),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Yüksek değerli müşteriler risk altında mı?
     */
    private function checkHighValueCustomersAtRisk(): int
    {
        $atRisk = CustomerScore::where('lifetime_value', '>=', 500)
            ->where('risk_score', '>=', 50)
            ->where('total_orders', '>=', 2)
            ->limit(5)
            ->get();

        $count = 0;
        foreach ($atRisk as $score) {
            $exists = AiRecommendation::where('type', 'vip_at_risk')
                ->where('user_id', $score->user_id)
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                $user = $score->user;
                if (!$user) continue;

                AiRecommendation::create([
                    'type' => 'vip_at_risk',
                    'priority' => 'critical',
                    'title' => "🚨 VIP müşteri {$user->name} kaybediliyor!",
                    'description' => number_format($score->lifetime_value, 2) . " ₺ değerinde müşteri {$score->days_since_last_order} gündür alışveriş yapmadı. Kişisel indirim veya WhatsApp mesajı önerilir.",
                    'action_data' => ['action' => 'personal_outreach', 'ltv' => $score->lifetime_value],
                    'user_id' => $score->user_id,
                    'expires_at' => now()->addDays(7),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Gelir trendi kontrolü
     */
    private function checkRevenueTrend(): int
    {
        $thisWeek = (float) Order::where('created_at', '>=', now()->subDays(7))->sum('grand_total');
        $lastWeek = (float) Order::whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])->sum('grand_total');

        if ($lastWeek > 0 && $thisWeek < $lastWeek * 0.8) {
            $drop = round((1 - ($thisWeek / $lastWeek)) * 100);

            $exists = AiRecommendation::where('type', 'revenue_drop')
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subDays(3))
                ->exists();

            if (!$exists) {
                AiRecommendation::create([
                    'type' => 'revenue_drop',
                    'priority' => $drop >= 40 ? 'critical' : 'high',
                    'title' => "📉 Gelir bu hafta %{$drop} düştü",
                    'description' => "Geçen hafta: " . number_format($lastWeek, 2) . " ₺, bu hafta: " . number_format($thisWeek, 2) . " ₺. Flash kampanya veya sosyal medya promosyonu önerilir.",
                    'action_data' => ['this_week' => $thisWeek, 'last_week' => $lastWeek, 'drop_percent' => $drop],
                    'expires_at' => now()->addDays(3),
                ]);
                return 1;
            }
        }

        return 0;
    }

    /**
     * Terk edilen sepet kontrolü
     */
    private function checkAbandonedCarts(): int
    {
        $abandonedCount = \App\Models\Cart::whereNotNull('user_id')
            ->where('updated_at', '<=', now()->subHours(2))
            ->where('updated_at', '>=', now()->subDays(1))
            ->whereHas('items')
            ->count();

        if ($abandonedCount >= 3) {
            $exists = AiRecommendation::where('type', 'abandoned_carts')
                ->where('status', 'pending')
                ->where('created_at', '>=', now()->subHours(6))
                ->exists();

            if (!$exists) {
                AiRecommendation::create([
                    'type' => 'abandoned_carts',
                    'priority' => 'high',
                    'title' => "🛒 {$abandonedCount} terk edilmiş sepet tespit edildi",
                    'description' => "Son 24 saatte {$abandonedCount} müşteri sepetini terk etti. Hatırlatma bildirimi veya küçük bir indirim gönderilmesi önerilir.",
                    'action_data' => ['count' => $abandonedCount, 'action' => 'send_reminder'],
                    'expires_at' => now()->addHours(12),
                ]);
                return 1;
            }
        }

        return 0;
    }

    /**
     * VIP'e yükseltilebilecek müşteriler
     */
    private function checkVIPOpportunities(): int
    {
        $potentialVIPs = CustomerScore::where('purchase_score', '>=', 60)
            ->where('loyalty_score', '>=', 40)
            ->where('risk_score', '<=', 30)
            ->where('total_orders', '>=', 3)
            ->limit(5)
            ->get();

        $count = 0;
        foreach ($potentialVIPs as $score) {
            $exists = AiRecommendation::where('type', 'vip_opportunity')
                ->where('user_id', $score->user_id)
                ->where('status', 'pending')
                ->exists();

            if (!$exists) {
                $user = $score->user;
                if (!$user) continue;

                AiRecommendation::create([
                    'type' => 'vip_opportunity',
                    'priority' => 'medium',
                    'title' => "⭐ {$user->name} VIP adayı",
                    'description' => "{$score->total_orders} sipariş, " . number_format($score->lifetime_value, 2) . " ₺ toplam harcama. VIP segmentine eklenip özel avantajlar sunulabilir.",
                    'action_data' => ['action' => 'upgrade_to_vip'],
                    'user_id' => $score->user_id,
                    'expires_at' => now()->addDays(30),
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * ChatGPT kullanarak veri analizi yap ve AI önerileri oluştur.
     */
    private function runChatGPTAnalysis(): int
    {
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey) || $apiKey === 'sk-your-openai-api-key-here') {
            return 0; // Key yoksa OpenAI API'yi atla
        }

        try {
            $totalOrders = \App\Models\Order::count();
            $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('grand_total');
            $activeCustomers = CustomerScore::where('total_orders', '>=', 1)->count();
            $abandonedCarts = \App\Models\Cart::whereHas('items')->where('updated_at', '>=', now()->subDays(7))->count();

            $prompt = "Sen uzman bir E-ticaret AI danışmanısın. Mağazanın mevcut durumu: Toplam Sipariş: {$totalOrders}, Toplam Gelir: {$totalRevenue} ₺, Aktif Müşteri: {$activeCustomers}, Son 7 günde terk edilen sepet: {$abandonedCarts}. Lütfen satışları artırmak ve müşteri deneyimini iyileştirmek için 1 adet yüksek öncelikli fırsat önerisi ver.
JSON formatı:
[
  {
    \"type\": \"insight\",
    \"title\": \"Başlık\",
    \"description\": \"Açıklama\",
    \"action_label\": \"Aksiyon\",
    \"priority\": \"high\"
  }
]";

            $response = Http::withToken($apiKey)->timeout(15)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'Sadece JSON array döndür.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'response_format' => ['type' => 'json_object']
            ]);

            if ($response->successful()) {
                $content = $response->json('choices.0.message.content');
                $data = json_decode($content, true);
                
                $recs = $data['recommendations'] ?? ($data['data'] ?? $data);

                if (is_array($recs) && count($recs) > 0) {
                    $rec = $recs[0];
                    AiRecommendation::create([
                        'type' => $rec['type'] ?? 'insight',
                        'title' => $rec['title'] ?? 'OpenAI Analizi',
                        'description' => $rec['description'] ?? '',
                        'action_label' => $rec['action_label'] ?? 'İncele',
                        'priority' => $rec['priority'] ?? 'high',
                        'is_read' => false,
                        'expires_at' => now()->addDays(7),
                    ]);
                    return 1;
                }
            } else {
                Log::error("ChatGPT API Error: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("ChatGPT API Exception: " . $e->getMessage());
        }

        return 0;
    }
}
