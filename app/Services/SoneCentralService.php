<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SoneCentralService
{
    protected string $apiUrl;
    protected string $apiKey;
    protected string $assistantId;

    public function __construct()
    {
        $this->apiUrl = Setting::where('key', 'sonecentral_api_url')->value('value')
            ?: config('services.sonecentral.api_url', 'https://pb.sonecentral.com/api/v1');

        $this->apiKey = Setting::where('key', 'sonecentral_api_key')->value('value')
            ?: config('services.sonecentral.api_key', 'sk-sone-v1-veTfj10rXv5zcKWYtLrjXFsG0Y0QfPIvasmk1eB5SA6HcyRO');

        $this->assistantId = Setting::where('key', 'sonecentral_assistant_id')->value('value')
            ?: config('services.sonecentral.assistant_id', '3nco7b3k5th1j2b');
    }

    /**
     * Sone Central Bağlantı Testi
     */
    public function testConnection(): array
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->get("{$this->apiUrl}/key");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Sone Central API bağlantısı başarılı.',
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Sone Central API hatası: ' . $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => 'Bağlantı hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Sone Central Chat Completions Çağrısı
     *
     * @param array $messages Mesaj dizisi [ ['role' => 'user', 'content' => '...'] ]
     * @param string|null $conversationId Varsa devam eden konuşma ID'si
     * @return array
     */
    public function chat(array $messages, ?string $conversationId = null): array
    {
        try {
            $payload = [
                'model' => $this->assistantId,
                'messages' => $messages,
            ];

            if ($conversationId) {
                $payload['sone'] = [
                    'conversation_id' => $conversationId,
                ];
            }

            $response = Http::withToken($this->apiKey)
                ->timeout(20)
                ->post("{$this->apiUrl}/chat/completions", $payload);

            if ($response->successful()) {
                $json = $response->json();
                $reply = $json['choices'][0]['message']['content'] ?? '';
                $newConversationId = $json['sone']['conversation_id'] ?? ($json['choices'][0]['message']['sone']['conversation_id'] ?? null);

                return [
                    'success' => true,
                    'reply' => $reply,
                    'conversation_id' => $newConversationId,
                    'raw' => $json,
                ];
            }

            Log::warning('SoneCentral Chat API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Asistan şu anda yanıt veremiyor. Hata kodu: ' . $response->status(),
            ];
        } catch (\Throwable $e) {
            Log::error('SoneCentral Exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'Sistem hatası: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Müşteri mesajından sipariş veya stok niyetini tespit edip canlı veri bağlamı (context) üretir.
     */
    public function detectContextAndEnrich(string $userMessage): ?string
    {
        $context = [];

        // 1. Sipariş Numarası Tespiti (#1024, sipariş 1024, vb.)
        if (preg_match('/#?([0-9]{3,7})/i', $userMessage, $matches)) {
            $candidateNum = $matches[1];
            $order = Order::with(['items.product', 'items.variant'])
                ->where('order_number', $candidateNum)
                ->orWhere('order_number', '#' . $candidateNum)
                ->latest()
                ->first();

            if ($order) {
                $statusLabels = [
                    'pending' => 'Sipariş Alındı (Hazırlanmayı Bekliyor)',
                    'processing' => 'Hazırlanıyor',
                    'shipped' => 'Kargoya Verildi',
                    'delivered' => 'Teslim Edildi',
                    'cancelled' => 'İptal Edildi',
                ];
                $statusStr = $statusLabels[$order->status] ?? $order->status;
                $kargoStr = $order->shipping_tracking_code ? "Kargo Takip No: {$order->shipping_tracking_code} ({$order->shipping_company})" : "Henüz takip kodu atanmadı";

                $context[] = "[SİSTEM CANLI SİPARİŞ BİLGİSİ: #{$order->order_number} nolu siparişin güncel durumu: {$statusStr}. {$kargoStr}. Toplam Tutar: " . number_format($order->total_amount, 2) . " TL.]";
            }
        }

        // 2. Canlı Stok ve Model Sorgusu Tespiti
        $keywords = ['stok', 'var mı', 'beden', 'numara', 'twingers', 'panter', 'kick speed', 'alessio', 'ndrops'];
        $hasStockIntent = false;
        foreach ($keywords as $kw) {
            if (mb_stripos($userMessage, $kw) !== false) {
                $hasStockIntent = true;
                break;
            }
        }

        if ($hasStockIntent) {
            // Eğer belirli bir model ismi geçiyorsa o modelin anlık varyant stoklarını çek
            $models = ['Panter', 'Twingers', 'Alessio', 'Ndrops', 'Kick Speed'];
            foreach ($models as $m) {
                if (mb_stripos($userMessage, $m) !== false) {
                    $matchedProduct = Product::where('status', true)
                        ->where('name', 'like', "%{$m}%")
                        ->with('variants')
                        ->first();

                    if ($matchedProduct) {
                        $stockBreakdown = [];
                        foreach ($matchedProduct->variants as $v) {
                            $stockBreakdown[] = "{$v->size} no: " . ((int)$v->stock > 0 ? "{$v->stock} adet" : "Tükendi");
                        }
                        $context[] = "[SİSTEM CANLI STOK BİLGİSİ: {$matchedProduct->name} anlık stok durumu -> " . implode(', ', $stockBreakdown) . ". Kalıp tavsiyesi: 1 numara büyük alınmalıdır.]";
                        break;
                    }
                }
            }
        }

        return !empty($context) ? implode("\n", $context) : null;
    }
}
