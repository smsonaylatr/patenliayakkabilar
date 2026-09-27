<?php

namespace App\Http\Middleware;

use App\Services\TrafficSourceDetector;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureTrafficSource
{
    public function __construct(
        protected TrafficSourceDetector $detector
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Admin, api, webhook veya livewire alt isteklerinde kaynak yakalamayı atla
        if (
            $request->is('admin/*') ||
            $request->is('livewire/*') ||
            $request->is('payment/*') ||
            $request->is('api/*') ||
            !$request->isMethod('GET')
        ) {
            return $next($request);
        }

        try {
            $data = $this->detector->detect($request);

            // GCLID varsa doğrudan geriye uyumluluk için session'a yaz
            if (!empty($data['gclid'])) {
                session(['gclid' => $data['gclid']]);
                cookie()->queue(cookie('gclid', $data['gclid'], 43200)); // 30 gün
            }

            $hasCampaignParams = !empty($data['gclid']) ||
                !empty($data['fbclid']) ||
                !empty($data['ttclid']) ||
                !empty($data['utm_source']) ||
                !empty($data['utm_campaign']) ||
                (!empty($data['referrer']) && $data['traffic_source'] !== 'Doğrudan');

            // Eğer kampanya veya dış referer ile gelindiyse (Last Non-Direct Click), oturum ve çerezi güncelle
            if ($hasCampaignParams) {
                $attribution = [
                    'traffic_source' => $data['traffic_source'],
                    'device_type'    => $data['device_type'],
                    'utm_source'     => $data['utm_source'],
                    'utm_medium'     => $data['utm_medium'],
                    'utm_campaign'   => $data['utm_campaign'],
                    'utm_term'       => $data['utm_term'],
                    'utm_content'    => $data['utm_content'],
                    'gclid'          => $data['gclid'],
                    'fbclid'         => $data['fbclid'],
                    'ttclid'         => $data['ttclid'],
                    'referrer'       => $data['referrer'],
                ];

                session(['traffic_attribution' => $attribution]);
                session(['traffic_source' => $data['traffic_source']]);
                session(['traffic_device' => $data['device_type']]);

                // 30 günlük atıf çerezi kaydet
                cookie()->queue(cookie(
                    'traffic_attribution',
                    json_encode($attribution, JSON_UNESCAPED_UNICODE),
                    43200, // 30 gün
                    '/',
                    null,
                    false,
                    false // JavaScript tarafından da okunabilir
                ));
            } else {
                // Kampanya parametresi yoksa; oturumda henüz yoksa çerezden geri yüklemeyi dene
                if (!session()->has('traffic_source')) {
                    $cookieVal = $request->cookie('traffic_attribution');
                    if ($cookieVal) {
                        $parsed = json_decode($cookieVal, true);
                        if (is_array($parsed) && !empty($parsed['traffic_source'])) {
                            session(['traffic_attribution' => $parsed]);
                            session(['traffic_source' => $parsed['traffic_source']]);
                            session(['traffic_device' => $parsed['device_type'] ?? $data['device_type']]);
                            if (!empty($parsed['gclid'])) {
                                session(['gclid' => $parsed['gclid']]);
                            }
                        }
                    }

                    // Çerezde de yoksa 'Doğrudan' olarak başlat
                    if (!session()->has('traffic_source')) {
                        $attribution = [
                            'traffic_source' => 'Doğrudan',
                            'device_type'    => $data['device_type'],
                            'utm_source'     => null,
                            'utm_medium'     => null,
                            'utm_campaign'   => null,
                            'utm_term'       => null,
                            'utm_content'    => null,
                            'gclid'          => null,
                            'fbclid'         => null,
                            'ttclid'         => null,
                            'referrer'       => null,
                        ];
                        session(['traffic_attribution' => $attribution]);
                        session(['traffic_source' => 'Doğrudan']);
                        session(['traffic_device' => $data['device_type']]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Hata durumunda site akışı asla kesilmemeli
            report($e);
        }

        return $next($request);
    }
}
