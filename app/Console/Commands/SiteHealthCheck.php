<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                $response = Http::timeout(15)->get($url);
                $responseTime = round(microtime(true) - $startTime, 2);
                $statusCode = $response->status();
                $body = $response->body();

                if ($statusCode === 200 && $responseTime < 10 && !empty($body) && !str_contains(strtolower($body), 'internal server error')) {
                    $status = '🟢';
                } else {
                    $hasFailures = true;
                    $errorMsg = "Durum Kodu: {$statusCode}, Süre: {$responseTime}s, Hata Bulundu: " . (str_contains(strtolower($body), 'internal server error') ? 'Evet' : 'Hayır');
                }
            } catch (\Exception $e) {
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
        }

        $this->table(['Name', 'URL', 'Status', 'Response Time', 'Code'], $results);

        if ($hasFailures) {
            $timestamp = Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s');
            
            $message = "🔴 <b>SİTE SAĞLIK UYARISI</b>\n\n";
            $message .= "Bazı sayfalar yanıt vermiyor veya hatalı.\n\n";
            $message .= implode("\n\n", $failedDetails) . "\n\n";
            $message .= "<b>Tarih:</b> {$timestamp}";

            $this->telegramAlertService->sendAlert($message);
            Log::error('Site Health Check failed for some URLs', ['failures' => $failedDetails]);
        } else {
            Log::info('Site Health Check passed for all URLs');
        }
    }
}
