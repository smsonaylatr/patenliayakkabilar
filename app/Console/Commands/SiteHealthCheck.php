<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Services\TelegramAlertService;
use Carbon\Carbon;

class SiteHealthCheck extends Command
{
    protected $signature = 'site:health-check';
    protected $description = 'Checks if important URLs are responding correctly and sends an alert if not';

    protected $telegramAlertService;

    public function __construct(TelegramAlertService $telegramAlertService)
    {
        parent::__construct();
        $this->telegramAlertService = $telegramAlertService;
    }

    public function handle()
    {
        $urls = [
            'Ana Sayfa' => 'https://patenliayakkabilar.com',
            'Ürünler' => 'https://patenliayakkabilar.com/patenli-ayakkabilar',
            'İletişim' => 'https://patenliayakkabilar.com/iletisim',
            'Health Check' => 'https://patenliayakkabilar.com/up',
        ];

        $results = [];
        $hasFailures = false;
        $failedDetails = [];

        foreach ($urls as $name => $url) {
            $startTime = microtime(true);
            $status = '🔴';
            $statusCode = null;
            $responseTime = null;
            $errorMsg = null;

            try {
                // Retry up to 2 times with 1000ms delay on transient connection/SSL resets
                $response = Http::withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36 (Patenli-HealthCheck/1.0)',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                        CURLOPT_CONNECTTIMEOUT => 10,
                    ],
                ])
                ->retry(2, 1000, throw: false)
                ->timeout(15)
                ->get($url);

                $responseTime = round(microtime(true) - $startTime, 2);
                $statusCode = $response->status();
                $body = $response->body();

                if ($statusCode === 200 && $responseTime < 15 && !empty($body) && !str_contains(strtolower($body), 'internal server error')) {
                    $status = '🟢';
                } else {
                    $hasFailures = true;
                    $errorType = $statusCode >= 500 ? 'Sunucu Hatası' : ($statusCode === 404 ? 'Sayfa Bulunamadı' : 'Yanıt Hatası');
                    $errorMsg = "Durum Kodu: {$statusCode}, Süre: {$responseTime}s ({$errorType})";
                }
            } catch (\Throwable $e) {
                $hasFailures = true;
                $responseTime = round(microtime(true) - $startTime, 2);
                $errorMsg = $e->getMessage();
            }

            $results[] = [
                'Name' => $name,
                'URL' => $url,
                'Status' => $status,
                'Response Time' => $responseTime !== null ? $responseTime . 's' : 'N/A',
                'Code' => $statusCode ?? 'N/A',
            ];

            if ($status === '🔴') {
                $failedDetails[] = "<b>{$name}</b> ({$url})\nHata: {$errorMsg}";
            }

            // Pacing between requests (250ms) to prevent Cloudflare edge TCP burst throttling
            usleep(250000);
        }

        $this->table(['Name', 'URL', 'Status', 'Response Time', 'Code'], $results);

        $wasFailing = false;
        try {
            $wasFailing = (bool) Cache::get('site_health_is_failing', false);
        } catch (\Throwable $e) {
            Log::warning('Cache check in SiteHealthCheck failed: ' . $e->getMessage());
        }

        if ($hasFailures) {
            $timestamp = Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s');
            
            $shouldAlert = true;
            try {
                $lastAlert = Cache::get('site_health_last_alert');
                if ($wasFailing && $lastAlert && Carbon::parse($lastAlert)->diffInMinutes(now()) < 30) {
                    $shouldAlert = false;
                }
            } catch (\Throwable $e) {
                // If cache check fails, proceed with alert
            }

            if ($shouldAlert) {
                $message = "🔴 <b>SİTE SAĞLIK UYARISI</b>\n\n";
                $message .= "Bazı sayfalar yanıt vermiyor veya hatalı.\n\n";
                $message .= implode("\n\n", $failedDetails) . "\n\n";
                $message .= "<b>Tarih:</b> {$timestamp}";

                $this->telegramAlertService->sendAlert($message);

                try {
                    Cache::put('site_health_last_alert', now()->toDateTimeString(), now()->addDay());
                } catch (\Throwable $e) {}
            }

            try {
                Cache::put('site_health_is_failing', true, now()->addDay());
            } catch (\Throwable $e) {}

            Log::error('Site Health Check failed for some URLs', ['failures' => $failedDetails]);
        } else {
            // If previously failing and now recovered, send recovery alert
            if ($wasFailing) {
                $timestamp = Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s');
                $recoveryMessage = "🟢 <b>SİTE TEKRAR SAĞLIKLI</b>\n\n";
                $recoveryMessage .= "Tüm sayfalar normale döndü ve başarıyla yanıt veriyor.\n\n";
                $recoveryMessage .= "<b>Tarih:</b> {$timestamp}";

                $this->telegramAlertService->sendAlert($recoveryMessage);

                try {
                    Cache::forget('site_health_is_failing');
                    Cache::forget('site_health_last_alert');
                } catch (\Throwable $e) {}

                Log::info('Site Health Check recovered. Recovery alert sent.');
            }

            Log::info('Site Health Check passed for all URLs');
        }
    }
}
