<?php

namespace App\Services;

use App\Models\Influencer;
use App\Models\InfluencerCampaign;
use App\Models\InfluencerOutreachLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InfluencerMarketingAIService
{
    /**
     * AI ile YouTube çocuk kanalı araştırması yap.
     * API key yoksa demo veri oluştur.
     */
    public function discoverChannels(): int
    {
        $aiResponse = $this->callOpenAI(
            'Sen uzman bir YouTube influencer araştırmacısısın. Sadece JSON array döndür.',
            'Türkiye\'de 5-12 yaş çocukların vlog, oyun, deneyim videosu çeken YouTube kanallarını öner. ' .
            'Patenli ayakkabı (tekerlekli ayakkabı) ürünü için uygun olanları listele. ' .
            '5 kanal öner. Her kanal için şunları ver: channel_name, subscriber_count (sayı), ' .
            'category (kids_vlog/toy_review/gaming/challenge/unboxing), tier (nano/micro/mid), ' .
            'fit_score (0-100 arası puan). JSON array formatında döndür: ' .
            '[{"channel_name": "...", "subscriber_count": 50000, "category": "kids_vlog", "tier": "micro", "fit_score": 85}]'
        );

        $channels = null;
        if ($aiResponse) {
            $decoded = json_decode($aiResponse, true);
            if (is_array($decoded)) {
                $channels = isset($decoded[0]) ? $decoded : ($decoded['channels'] ?? $decoded['data'] ?? null);
            }
        }

        // Fallback demo verisi
        if (!$channels) {
            $channels = [
                ['channel_name' => 'Minik Patenciler', 'subscriber_count' => 50000, 'category' => 'kids_vlog', 'tier' => 'micro', 'fit_score' => 85, 'avg_views' => 12000, 'engagement_rate' => 5.2],
                ['channel_name' => 'Çocuk Dünyası TV', 'subscriber_count' => 120000, 'category' => 'toy_review', 'tier' => 'mid', 'fit_score' => 78, 'avg_views' => 28000, 'engagement_rate' => 3.8],
                ['channel_name' => 'Eğlenceli Çocuklar', 'subscriber_count' => 25000, 'category' => 'challenge', 'tier' => 'micro', 'fit_score' => 92, 'avg_views' => 8500, 'engagement_rate' => 7.1],
                ['channel_name' => 'Oyun Parkı Vlog', 'subscriber_count' => 8000, 'category' => 'kids_vlog', 'tier' => 'nano', 'fit_score' => 88, 'avg_views' => 3200, 'engagement_rate' => 8.5],
                ['channel_name' => 'Küçük Kaşifler', 'subscriber_count' => 200000, 'category' => 'unboxing', 'tier' => 'mid', 'fit_score' => 75, 'avg_views' => 45000, 'engagement_rate' => 4.2],
            ];
        }

        $count = 0;
        foreach ($channels as $ch) {
            $exists = Influencer::where('channel_name', $ch['channel_name'])->exists();
            if (!$exists) {
                Influencer::create([
                    'channel_name' => $ch['channel_name'],
                    'subscriber_count' => $ch['subscriber_count'] ?? null,
                    'category' => $ch['category'] ?? 'kids_vlog',
                    'tier' => $ch['tier'] ?? 'micro',
                    'fit_score' => $ch['fit_score'] ?? 50,
                    'avg_views' => $ch['avg_views'] ?? null,
                    'engagement_rate' => $ch['engagement_rate'] ?? null,
                    'platform' => 'youtube',
                    'status' => 'discovered',
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Influencer'a özel teklif oluştur.
     */
    public function generateProposal(Influencer $influencer): InfluencerCampaign
    {
        // Tier'a göre paket belirle
        $packageConfig = match ($influencer->tier) {
            'nano', 'micro' => [
                'type' => 'super_hero',
                'name' => 'Süper Kahraman Paketi',
                'amount' => 0,
                'products' => ['2 çift patenli ayakkabı', 'Koruyucu ekipman seti (kask + dizlik + dirseklik)', '"Patenli Takım" rozeti'],
                'expected_videos' => 1,
                'commission' => 15,
            ],
            'mid' => [
                'type' => 'youtube_star',
                'name' => 'YouTube Yıldızı Paketi',
                'amount' => 25000,
                'products' => ['3 çift patenli ayakkabı (çocuk + aile)', 'Premium koruyucu ekipman seti', 'Web sitesinde "YouTuber Favorileri" bölümü'],
                'expected_videos' => 3,
                'commission' => 20,
            ],
            'macro', 'mega' => [
                'type' => 'mega_star',
                'name' => 'Mega Star Paketi',
                'amount' => 75000,
                'products' => ['5 çift patenli ayakkabı', 'Özel tasarım koleksiyon', 'Profesyonel prodüksiyon desteği', 'Sosyal medya reklam bütçesi'],
                'expected_videos' => 6,
                'commission' => 25,
            ],
            default => [
                'type' => 'custom',
                'name' => 'Özel Paket',
                'amount' => 10000,
                'products' => ['1 çift patenli ayakkabı', 'Koruyucu ekipman seti'],
                'expected_videos' => 1,
                'commission' => 10,
            ],
        };

        // AI ile kişiselleştirilmiş teklif metni
        $proposalText = $this->callOpenAI(
            'Sen Patenli Ayakkabılar markasının pazarlama müdürüsün. Türkçe, samimi ve ikna edici teklif yaz.',
            "YouTube kanalı: {$influencer->channel_name}\n" .
            "Abone sayısı: {$influencer->subscriber_count}\n" .
            "Çocuk adı: " . ($influencer->child_name ?: 'belirtilmemiş') . "\n" .
            "Kategori: {$influencer->category}\n" .
            "Paket: {$packageConfig['name']}\n" .
            "Nakit teklif: " . number_format($packageConfig['amount'], 0) . " ₺\n" .
            "Ürünler: " . implode(', ', $packageConfig['products']) . "\n" .
            "Beklenen video: {$packageConfig['expected_videos']}\n" .
            "Komisyon: %{$packageConfig['commission']}\n\n" .
            "Bu bilgilere göre kişiselleştirilmiş, samimi ve ikna edici bir iş birliği teklif metni yaz. " .
            "Çocuğun enerjisini ve kanalın kalitesini öv. Paketteki avantajları vurgula."
        );

        // Fallback şablon
        if (!$proposalText) {
            $childName = $influencer->child_name ?: 'çocuğunuz';
            $proposalText = "Merhaba!\n\n" .
                "{$influencer->channel_name} kanalını büyük bir beğeniyle takip ediyoruz. " .
                "{$childName}'ın enerjisi ve videoları gerçekten harika! 🌟\n\n" .
                "Biz Patenli Ayakkabılar markasıyız — Türkiye'nin en eğlenceli tekerlekli ayakkabılarını üretiyoruz.\n\n" .
                "📦 {$packageConfig['name']} TEKLİFİMİZ:\n";

            foreach ($packageConfig['products'] as $product) {
                $proposalText .= "✅ {$product}\n";
            }

            if ($packageConfig['amount'] > 0) {
                $proposalText .= "💰 " . number_format($packageConfig['amount'], 0) . " ₺ nakit ödeme\n";
            }

            $proposalText .= "🎯 %{$packageConfig['commission']} komisyonlu özel affiliate kodu\n\n" .
                "Beklentimiz: {$packageConfig['expected_videos']} adet eğlenceli deneyim videosu. " .
                "Senaryo tamamen size bırakılmıştır — doğal ve samimi olsun yeter! 😊\n\n" .
                "Sevgilerle,\nPatenli Ayakkabılar Ekibi 🛼";
        }

        // Affiliate kodu oluştur
        if (!$influencer->affiliate_code) {
            $code = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $influencer->channel_name), 0, 6)) . rand(10, 99);
            $influencer->update([
                'affiliate_code' => $code,
                'commission_rate' => $packageConfig['commission'],
            ]);
        }

        return InfluencerCampaign::create([
            'influencer_id' => $influencer->id,
            'campaign_name' => "{$packageConfig['name']} — {$influencer->channel_name}",
            'package_type' => $packageConfig['type'],
            'offered_amount' => $packageConfig['amount'],
            'products_sent' => $packageConfig['products'],
            'expected_videos' => $packageConfig['expected_videos'],
            'status' => 'draft',
            'ai_generated_proposal' => $proposalText,
            'notes' => "Komisyon: %{$packageConfig['commission']} | Affiliate kod: " . ($influencer->affiliate_code ?? 'oluşturulacak'),
        ]);
    }

    /**
     * İletişim mesajı üret.
     */
    public function generateOutreachMessage(Influencer $influencer, string $channel = 'email'): InfluencerOutreachLog
    {
        $childName = $influencer->child_name ?: 'çocuğunuz';

        // AI ile kişiselleştirilmiş mesaj
        $aiMessage = $this->callOpenAI(
            'Sen Patenli Ayakkabılar markasının iletişim uzmanısın. Türkçe, samimi mesajlar yaz.',
            "Kanal: {$influencer->channel_name}\n" .
            "Çocuk adı: {$childName}\n" .
            "Platform: {$influencer->platform}\n" .
            "İletişim kanalı: {$channel}\n\n" .
            "Bu influencer'a ilk temas mesajı yaz. " .
            ($channel === 'dm' ? 'Kısa, samimi, emoji\'li bir DM mesajı yaz (max 200 karakter).' :
            ($channel === 'whatsapp' ? 'Kısa ve samimi bir WhatsApp mesajı yaz.' :
            'Profesyonel ama samimi bir e-posta yaz.'))
        );

        // Fallback şablonlar
        if (!$aiMessage) {
            $aiMessage = match ($channel) {
                'dm' => "Merhaba! 👋 Patenli Ayakkabılar markasından yazıyoruz. " .
                    "{$childName}'ın videolarını çok seviyoruz! 🌟 " .
                    "Size hediye olarak tekerlekli ayakkabı ve koruyucu ekipman seti göndermek istiyoruz 🎁🛼 " .
                    "İlgilenir misiniz? 😊 www.patenliayakkabilar.com",

                'whatsapp' => "Merhaba! 👋\n\n" .
                    "Patenli Ayakkabılar markasından arıyoruz.\n" .
                    "{$influencer->channel_name} kanalını çok beğeniyoruz! 🌟\n\n" .
                    "Size hediye tekerlekli ayakkabı + ekipman göndermek istiyoruz. " .
                    "Karşılığında sadece eğlenceli bir deneyim videosu bekliyoruz 🎬\n\n" .
                    "Detaylar için dönüş yaparsanız seviniriz 😊\n" .
                    "www.patenliayakkabilar.com",

                default => "Konu: 🎉 {$influencer->channel_name} için Özel Sürpriz — Patenli Ayakkabılar\n\n" .
                    "Merhaba" . ($influencer->parent_name ? " {$influencer->parent_name}" : '') . ",\n\n" .
                    "{$influencer->channel_name} kanalını büyük bir beğeniyle takip ediyoruz! " .
                    "{$childName}'ın enerjisi ve videoları gerçekten harika. 🌟\n\n" .
                    "Biz Patenli Ayakkabılar markasıyız — Türkiye'nin en eğlenceli tekerlekli ayakkabılarını üretiyoruz.\n\n" .
                    "🎁 HEDİYE PAKETİ:\n" .
                    "✅ 2 çift patenli ayakkabı ({$childName} + kardeşi için)\n" .
                    "✅ Tam koruyucu ekipman seti (kask + dizlik + dirseklik)\n" .
                    "✅ \"Patenli Takım\" özel rozeti\n" .
                    "✅ Kanalınıza özel %15 komisyonlu indirim kodu\n\n" .
                    "Tek beklentimiz: {$childName}'ın ayakkabıları denerken çektiği " .
                    "eğlenceli bir deneyim videosu paylaşması. Senaryo tamamen size bırakılmıştır!\n\n" .
                    "İlgilenirseniz bu maile dönüş yapmanız yeterli. Paketi hemen hazırlayalım! 😊\n\n" .
                    "Sevgilerle,\nPatenli Ayakkabılar — Pazarlama Ekibi\n" .
                    "🌐 patenliayakkabilar.com",
            };
        }

        $subject = match ($channel) {
            'email' => "🎉 {$influencer->channel_name} için Özel Sürpriz — Patenli Ayakkabılar",
            'dm' => 'Instagram/YouTube DM',
            'whatsapp' => 'WhatsApp Mesajı',
            default => null,
        };

        return InfluencerOutreachLog::create([
            'influencer_id' => $influencer->id,
            'channel' => $channel,
            'direction' => 'outgoing',
            'subject' => $subject,
            'message' => $aiMessage,
            'ai_generated' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * Tüm kampanyaların performans analizi.
     */
    public function analyzePerformance(): array
    {
        $totalCampaigns = InfluencerCampaign::count();
        $completedCampaigns = InfluencerCampaign::where('status', 'completed')->count();
        $totalViews = (int) InfluencerCampaign::sum('total_views');
        $totalClicks = (int) InfluencerCampaign::sum('total_clicks');
        $totalSales = (int) InfluencerCampaign::sum('total_sales');
        $totalRevenue = (float) InfluencerCampaign::sum('revenue_generated');
        $totalSpent = (float) InfluencerCampaign::sum('offered_amount');
        $avgRoi = InfluencerCampaign::where('status', 'completed')->avg('roi');

        $bestCampaign = InfluencerCampaign::with('influencer')
            ->orderByDesc('revenue_generated')
            ->first();

        $worstCampaign = InfluencerCampaign::with('influencer')
            ->where('status', 'completed')
            ->where('revenue_generated', '>', 0)
            ->orderBy('roi')
            ->first();

        $stats = [
            'total_campaigns' => $totalCampaigns,
            'completed_campaigns' => $completedCampaigns,
            'total_views' => $totalViews,
            'total_clicks' => $totalClicks,
            'total_sales' => $totalSales,
            'total_revenue' => number_format($totalRevenue, 2) . ' ₺',
            'total_spent' => number_format($totalSpent, 2) . ' ₺',
            'avg_roi' => $avgRoi ? number_format($avgRoi, 1) . '%' : 'Henüz veri yok',
            'cpv' => $totalViews > 0 ? number_format($totalSpent / $totalViews, 3) . ' ₺' : 'N/A',
        ];

        $insights = [];
        if ($bestCampaign) {
            $insights[] = "🏆 En iyi kampanya: {$bestCampaign->campaign_name} — " . number_format($bestCampaign->revenue_generated, 2) . ' ₺ gelir';
        }
        if ($worstCampaign) {
            $insights[] = "📉 En düşük ROI: {$worstCampaign->campaign_name} — %" . number_format($worstCampaign->roi, 1);
        }
        if ($totalCampaigns === 0) {
            $insights[] = '📋 Henüz kampanya oluşturulmamış. "AI ile Kanal Ara" butonunu kullanarak başlayın.';
        }

        // AI ile ek öneriler
        $recommendations = [];
        $aiInsight = $this->callOpenAI(
            'Sen bir e-ticaret pazarlama danışmanısın. Türkçe yanıtla.',
            "Influencer kampanya verileri:\n" .
            "Toplam kampanya: {$totalCampaigns}\n" .
            "Toplam izlenme: {$totalViews}\n" .
            "Toplam satış: {$totalSales}\n" .
            "Toplam gelir: {$totalRevenue} ₺\n" .
            "Toplam harcama: {$totalSpent} ₺\n\n" .
            "Bu verilere göre 2-3 öneri ver. Kısa ve actionable olsun."
        );

        if ($aiInsight) {
            $recommendations[] = $aiInsight;
        } else {
            $recommendations = [
                '🎯 Mikro influencer\'larla (5K-50K) başlayın — en yüksek ROI bu segmentte.',
                '📹 "İlk kez patenli ayakkabı denedim!" formatındaki videolar en çok viral oluyor.',
                '🔄 3-4 videoluk seri anlaşmaları tek video anlaşmalarından %35 daha verimli.',
            ];
        }

        return [
            'stats' => $stats,
            'insights' => $insights,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * 8 haftalık strateji planının görev durumunu kontrol et.
     */
    public function getTaskStatus(): array
    {
        $totalInfluencers = Influencer::count();
        $contactedCount = Influencer::whereNotIn('status', ['discovered'])->count();
        $activeCampaigns = InfluencerCampaign::whereIn('status', ['accepted', 'in_progress'])->count();
        $completedCampaigns = InfluencerCampaign::where('status', 'completed')->count();
        $sentProposals = InfluencerCampaign::where('status', '!=', 'draft')->count();

        return [
            ['task' => 'Kanal Araştırma', 'target' => 30, 'current' => $totalInfluencers, 'status' => $totalInfluencers >= 30 ? 'completed' : ($totalInfluencers > 0 ? 'in_progress' : 'pending')],
            ['task' => 'İlk Temas (Nano/Mikro)', 'target' => 10, 'current' => $contactedCount, 'status' => $contactedCount >= 10 ? 'completed' : ($contactedCount > 0 ? 'in_progress' : 'pending')],
            ['task' => 'Teklif Gönderimi', 'target' => 15, 'current' => $sentProposals, 'status' => $sentProposals >= 15 ? 'completed' : ($sentProposals > 0 ? 'in_progress' : 'pending')],
            ['task' => 'Ürün Gönderimi', 'target' => 10, 'current' => $activeCampaigns, 'status' => $activeCampaigns > 0 ? 'in_progress' : 'pending'],
            ['task' => 'Video Teslim Takibi', 'target' => 10, 'current' => $completedCampaigns, 'status' => $completedCampaigns > 0 ? 'in_progress' : 'pending'],
            ['task' => 'Performans Analizi', 'target' => 1, 'current' => $completedCampaigns > 0 ? 1 : 0, 'status' => $completedCampaigns > 0 ? 'completed' : 'pending'],
        ];
    }

    /**
     * OpenAI API çağrısı yap. Ortak helper.
     */
    private function callOpenAI(string $systemPrompt, string $userPrompt): ?string
    {
        $apiKey = env('OPENAI_API_KEY');
        if (empty($apiKey) || $apiKey === 'sk-your-openai-api-key-here') {
            return null;
        }

        try {
            $response = Http::withToken($apiKey)->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.7,
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::error('InfluencerAI API Error: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('InfluencerAI Exception: ' . $e->getMessage());
        }

        return null;
    }
}
