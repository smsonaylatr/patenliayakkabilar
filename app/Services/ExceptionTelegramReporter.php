<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Throwable;
use Carbon\Carbon;

class ExceptionTelegramReporter
{
    protected $telegramAlertService;

    public function __construct(TelegramAlertService $telegramAlertService)
    {
        $this->telegramAlertService = $telegramAlertService;
    }

    public function report(Throwable $e, ?Request $request = null): void
    {
        $ignoredExceptions = [
            ValidationException::class,
            AuthenticationException::class,
            ModelNotFoundException::class,
            NotFoundHttpException::class,
            TokenMismatchException::class,
            ThrottleRequestsException::class,
        ];

        foreach ($ignoredExceptions as $ignoredClass) {
            if ($e instanceof $ignoredClass) {
                return;
            }
        }

        // Check if error was reported in the last 5 minutes
        $errorHash = md5($e->getMessage() . $e->getFile() . $e->getLine());
        $cacheKey = 'telegram_error_report_' . $errorHash;

        if (Cache::has($cacheKey)) {
            return;
        }

        Cache::put($cacheKey, true, now()->addMinutes(5));

        $errorMessage = mb_substr($e->getMessage(), 0, 500);
        $exceptionClass = get_class($e);
        $file = $e->getFile();
        $line = $e->getLine();
        
        $url = $request ? $request->fullUrl() : 'N/A';
        $method = $request ? $request->method() : 'N/A';
        $ip = $request ? $request->ip() : 'N/A';
        $userAgent = $request ? mb_substr($request->userAgent() ?? '', 0, 100) : 'N/A';
        
        $timestamp = Carbon::now('Europe/Istanbul')->format('Y-m-d H:i:s');

        $message = "🚨 <b>HATA BİLDİRİMİ</b>\n\n";
        $message .= "<b>Sınıf:</b> <code>{$exceptionClass}</code>\n";
        $message .= "<b>Mesaj:</b> <code>{$errorMessage}</code>\n";
        $message .= "<b>Dosya:</b> <code>{$file}:{$line}</code>\n\n";
        $message .= "<b>URL:</b> {$url}\n";
        $message .= "<b>Metot:</b> {$method}\n";
        $message .= "<b>IP:</b> {$ip}\n";
        $message .= "<b>User Agent:</b> {$userAgent}\n";
        $message .= "<b>Tarih:</b> {$timestamp}";

        $this->telegramAlertService->sendAlert($message);
    }
}
