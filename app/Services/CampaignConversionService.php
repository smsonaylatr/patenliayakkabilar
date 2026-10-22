<?php

namespace App\Services;

use App\Models\Campaign;
use App\Models\CustomerSegment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CampaignConversionService
{
    /**
     * Sipariş üzerinden kampanya dönüşümünü kaydeder.
     */
    public static function recordOrderConversion(Order $order): ?Campaign
    {
        $campaign = null;

        // 1. Doğrudan aktif kampanya oturumu kontrolü
        $campaignId = session('active_campaign_id') ?? $order->campaign_id ?? null;
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
        }

        // 2. Traffic Attribution oturumu veya çerezinden utm_campaign kontrolü
        if (!$campaign) {
            $attribution = session('traffic_attribution') ?? [];
            $utmCampaign = $attribution['utm_campaign'] ?? session('utm_campaign') ?? null;

            if ($utmCampaign) {
                $campaign = Campaign::where('name', 'LIKE', '%' . $utmCampaign . '%')
                    ->orWhereJsonContains('tracking_settings->utm_campaign', $utmCampaign)
                    ->latest()
                    ->first();
            }
        }

        // 3. Siparişin landing_url alanından parametre arama
        if (!$campaign && !empty($order->landing_url)) {
            $parsed = parse_url($order->landing_url, PHP_URL_QUERY);
            if ($parsed) {
                parse_str($parsed, $queryParams);
                if (!empty($queryParams['campaign_id'])) {
                    $campaign = Campaign::find($queryParams['campaign_id']);
                } elseif (!empty($queryParams['utm_campaign'])) {
                    $campaign = Campaign::where('name', 'LIKE', '%' . $queryParams['utm_campaign'] . '%')
                        ->orWhereJsonContains('tracking_settings->utm_campaign', $queryParams['utm_campaign'])
                        ->latest()
                        ->first();
                }
            }
        }

        if ($campaign) {
            $revenue = (float) ($order->grand_total ?? $order->total_amount ?? 0);
            $campaign->recordConversion($revenue);
            return $campaign;
        }

        return null;
    }

    /**
     * Kampanyanın kitle sinyallerine göre tahmini müşteri erişimini ve segment profilini hesaplar.
     */
    public static function estimateAudienceReach(Campaign $campaign): array
    {
        $signals = $campaign->audience_signals ?? [];
        $totalReach = 0;
        $breakdown = [];

        // 1. Hedef Segment
        if ($campaign->customer_segment_id) {
            $segment = CustomerSegment::find($campaign->customer_segment_id);
            if ($segment) {
                $segmentUsers = DB::table('customer_segment_user')
                    ->where('customer_segment_id', $segment->id)
                    ->count();
                $totalReach += $segmentUsers;
                $breakdown[] = [
                    'type' => 'Segment',
                    'name' => $segment->name,
                    'count' => $segmentUsers,
                ];
            }
        }

        // 2. Çoklu Segment Sinyalleri
        $additionalSegments = $signals['target_segments'] ?? [];
        if (is_array($additionalSegments) && !empty($additionalSegments)) {
            $addUsers = DB::table('customer_segment_user')
                ->whereIn('customer_segment_id', $additionalSegments)
                ->distinct('user_id')
                ->count('user_id');
            $totalReach = max($totalReach, $addUsers);
        }

        // 3. Davranışsal Sinyaller (Sepet terk, son 30 gün aktif vb.)
        $behavior = $signals['behavior_signals'] ?? [];
        if (in_array('abandoned_cart', (array)$behavior)) {
            $abandonedCount = \App\Models\Cart::where(function($q) {
                $q->whereNotNull('user_id')
                  ->orWhereNotNull('guest_email')
                  ->orWhereNotNull('guest_phone');
            })->has('items')->count();

            $breakdown[] = [
                'type' => 'Davranış',
                'name' => 'Terk Edilmiş Sepet Sahipleri',
                'count' => $abandonedCount,
            ];
            $totalReach += (int) ($abandonedCount * 0.4);
        }

        // Genel kullanıcı havuzundan taban erişim
        if ($totalReach === 0) {
            $totalReach = User::count();
            $breakdown[] = [
                'type' => 'Genel Havuz',
                'name' => 'Kayıtlı Tüm Müşteriler',
                'count' => $totalReach,
            ];
        }

        return [
            'estimated_reach' => $totalReach,
            'breakdown' => $breakdown,
            'intent_keywords_count' => count((array) ($signals['intent_keywords'] ?? [])),
            'demographics_summary' => $signals['demographics'] ?? 'Tüm Yaş & Cinsiyet Grupları',
        ];
    }

    /**
     * Gelişmiş Dönüşüm Takibi (Enhanced Conversions) dataLayer / CAPI payload'u oluşturur.
     */
    public static function buildEnhancedConversionPayload(Order $order, Campaign $campaign): array
    {
        $tracking = $campaign->tracking_settings ?? [];
        $isEnhanced = (bool) ($tracking['enhanced_conversions_enabled'] ?? true);

        $payload = [
            'event' => 'campaign_conversion',
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->name,
            'conversion_goal' => $campaign->conversion_goal ?? 'purchase',
            'value' => (float) ($order->grand_total ?? $order->total_amount ?? 0),
            'currency' => 'TRY',
            'transaction_id' => (string) ($order->order_number ?? $order->id),
        ];

        if ($isEnhanced) {
            $email = $order->customer_email ?? $order->user?->email;
            $phone = $order->customer_phone ?? $order->shipping_phone ?? $order->user?->phone;

            $payload['user_data'] = [
                'sha256_email_address' => $email ? hash('sha256', strtolower(trim($email))) : null,
                'sha256_phone_number' => $phone ? hash('sha256', preg_replace('/[^0-9]/', '', $phone)) : null,
                'address' => [
                    'city' => $order->shipping_city ?? 'İstanbul',
                    'country' => 'TR',
                ],
            ];
        }

        return $payload;
    }
}
