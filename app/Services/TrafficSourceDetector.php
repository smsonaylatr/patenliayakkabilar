<?php

namespace App\Services;

use Illuminate\Http\Request;

class TrafficSourceDetector
{
    /**
     * İstekten tüm trafik ve pazarlama kaynak parametrelerini tespit eder.
     *
     * @param Request $request
     * @return array
     */
    public function detect(Request $request): array
    {
        $userAgent = (string) $request->userAgent();
        $deviceType = $this->detectDeviceType($userAgent);

        $gclid = $request->query('gclid');
        $fbclid = $request->query('fbclid');
        $ttclid = $request->query('ttclid');

        $utmSource = $request->query('utm_source');
        $utmMedium = $request->query('utm_medium');
        $utmCampaign = $request->query('utm_campaign');
        $utmTerm = $request->query('utm_term');
        $utmContent = $request->query('utm_content');

        $referrer = $request->header('referer') ?: $request->query('ref');
        $referrerHost = null;

        if ($referrer) {
            $parsedHost = parse_url($referrer, PHP_URL_HOST);
            if ($parsedHost) {
                $referrerHost = strtolower($parsedHost);
            }
        }

        // Kendi sitemizden gelen referer ise referer'ı yoksay
        $currentHost = strtolower($request->getHost());
        if ($referrerHost && ($referrerHost === $currentHost || str_ends_with($referrerHost, '.' . $currentHost))) {
            $referrerHost = null;
            $referrer = null;
        }

        $trafficSource = $this->resolveSourceName(
            gclid: $gclid,
            fbclid: $fbclid,
            ttclid: $ttclid,
            utmSource: $utmSource,
            utmMedium: $utmMedium,
            referrerHost: $referrerHost
        );

        return [
            'traffic_source' => $trafficSource,
            'device_type'    => $deviceType,
            'landing_url'    => substr($request->fullUrl(), 0, 1000),
            'utm_source'     => $utmSource ? substr($utmSource, 0, 100) : null,
            'utm_medium'     => $utmMedium ? substr($utmMedium, 0, 100) : null,
            'utm_campaign'   => $utmCampaign ? substr($utmCampaign, 0, 150) : null,
            'utm_term'       => $utmTerm ? substr($utmTerm, 0, 150) : null,
            'utm_content'    => $utmContent ? substr($utmContent, 0, 150) : null,
            'gclid'          => $gclid ? substr($gclid, 0, 255) : null,
            'fbclid'         => $fbclid ? substr($fbclid, 0, 255) : null,
            'ttclid'         => $ttclid ? substr($ttclid, 0, 255) : null,
            'referrer'       => $referrer ? substr($referrer, 0, 500) : null,
            'referrer_host'  => $referrerHost,
        ];
    }

    /**
     * Kaynak adını standartlaştırılmış ve temiz bir etiket haline getirir.
     */
    protected function resolveSourceName(
        ?string $gclid,
        ?string $fbclid,
        ?string $ttclid,
        ?string $utmSource,
        ?string $utmMedium,
        ?string $referrerHost
    ): string {
        $sourceLower = strtolower(trim((string) $utmSource));
        $mediumLower = strtolower(trim((string) $utmMedium));
        $isPaid = preg_match('/cpc|ppc|paid|ads|reklam/i', $mediumLower) === 1;

        // 1. Google Ads
        if (!empty($gclid) || (str_contains($sourceLower, 'google') && $isPaid)) {
            return 'Google Ads';
        }

        // 2. Meta (Instagram / Facebook) Ads
        if (!empty($fbclid) || ((str_contains($sourceLower, 'instagram') || str_contains($sourceLower, 'facebook') || str_contains($sourceLower, 'meta')) && $isPaid)) {
            if (str_contains($sourceLower, 'instagram')) {
                return 'Instagram Ads';
            }
            if (str_contains($sourceLower, 'facebook')) {
                return 'Facebook Ads';
            }
            return 'Meta Ads';
        }

        // 3. TikTok Ads
        if (!empty($ttclid) || (str_contains($sourceLower, 'tiktok') && $isPaid)) {
            return 'TikTok Ads';
        }

        // 4. Açık UTM Source Tanımlamaları
        if (!empty($sourceLower)) {
            return match (true) {
                str_contains($sourceLower, 'instagram') || $sourceLower === 'ig' => 'Instagram',
                str_contains($sourceLower, 'facebook') || $sourceLower === 'fb' => 'Facebook',
                str_contains($sourceLower, 'tiktok') => 'TikTok',
                str_contains($sourceLower, 'google') => 'Google Organik',
                str_contains($sourceLower, 'youtube') => 'YouTube',
                str_contains($sourceLower, 'whatsapp') => 'WhatsApp',
                str_contains($sourceLower, 'sms') => 'SMS Kampanyası',
                str_contains($sourceLower, 'mail') || str_contains($sourceLower, 'email') || str_contains($sourceLower, 'newsletter') => 'E-Posta',
                str_contains($sourceLower, 'influencer') => 'Influencer',
                default => mb_convert_case(str_replace(['_', '-'], ' ', $utmSource), MB_CASE_TITLE, 'UTF-8'),
            };
        }

        // 5. Organik Referrer Host Kontrolleri
        if (!empty($referrerHost)) {
            if (str_contains($referrerHost, 'google.')) {
                return 'Google Organik';
            }
            if (str_contains($referrerHost, 'instagram.com')) {
                return 'Instagram';
            }
            if (str_contains($referrerHost, 'facebook.com')) {
                return 'Facebook';
            }
            if (str_contains($referrerHost, 'tiktok.com')) {
                return 'TikTok';
            }
            if (str_contains($referrerHost, 'youtube.com')) {
                return 'YouTube';
            }
            if (str_contains($referrerHost, 't.co') || str_contains($referrerHost, 'twitter.com') || str_contains($referrerHost, 'x.com')) {
                return 'X (Twitter)';
            }
            if (str_contains($referrerHost, 'whatsapp.com')) {
                return 'WhatsApp';
            }
            if (str_contains($referrerHost, 'yandex.')) {
                return 'Yandex';
            }
            if (str_contains($referrerHost, 'bing.com')) {
                return 'Bing';
            }
            if (str_contains($referrerHost, 'pinterest.com')) {
                return 'Pinterest';
            }
            if (str_contains($referrerHost, 't.me') || str_contains($referrerHost, 'telegram.org')) {
                return 'Telegram';
            }

            // Tanınmayan harici site
            $cleanHost = preg_replace('/^www\./i', '', $referrerHost);
            return 'Yönlendirme (' . $cleanHost . ')';
        }

        // 6. Hiçbir referans yoksa: Doğrudan
        return 'Doğrudan';
    }

    /**
     * User-Agent üzerinden cihaz türünü tespit eder.
     */
    public function detectDeviceType(string $userAgent): string
    {
        if (preg_match('/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle)/i', $userAgent)) {
            return 'Tablet';
        }

        if (preg_match('/(android|iphone|ipod|mobile|phone|blackberry|opera mini|iemobile)/i', $userAgent)) {
            return 'Mobil';
        }

        return 'Masaüstü';
    }

    /**
     * Kaynak adına göre Filament badge rengi döndürür.
     */
    public static function getBadgeColor(?string $source): string
    {
        if (!$source) {
            return 'gray';
        }

        $sourceLower = strtolower($source);

        if (str_contains($sourceLower, 'google ads')) {
            return 'info';
        }
        if (str_contains($sourceLower, 'google')) {
            return 'primary';
        }
        if (str_contains($sourceLower, 'instagram')) {
            return 'fuchsia';
        }
        if (str_contains($sourceLower, 'facebook') || str_contains($sourceLower, 'meta')) {
            return 'indigo';
        }
        if (str_contains($sourceLower, 'tiktok')) {
            return 'slate';
        }
        if (str_contains($sourceLower, 'whatsapp')) {
            return 'success';
        }
        if (str_contains($sourceLower, 'youtube')) {
            return 'danger';
        }
        if (str_contains($sourceLower, 'sms') || str_contains($sourceLower, 'e-posta')) {
            return 'warning';
        }
        if (str_contains($sourceLower, 'admin')) {
            return 'purple';
        }
        if (str_contains($sourceLower, 'yönlendirme')) {
            return 'warning';
        }

        return 'gray';
    }
}
