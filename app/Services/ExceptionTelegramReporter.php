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

        // Check if error was reported in the last 15 minutes (Atomic lock with Cache::add)
        $errorHash = md5($e->getMessage() . $e->getFile() . $e->getLine());
        $cacheKey = 'telegram_error_report_' . $errorHash;

        // Cache::add atomically returns false if the key already exists
        if (!Cache::add($cacheKey, true, now()->addMinutes(15))) {
            return;
        }

        $errorMessage = htmlspecialchars(mb_substr($e->getMessage(), 0, 500), ENT_QUOTES, 'UTF-8');
        $exceptionClass = htmlspecialchars(get_class($e), ENT_QUOTES, 'UTF-8');
        $file = htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8');
        $line = $e->getLine();
        
        $url = $request ? htmlspecialchars($request->fullUrl(), ENT_QUOTES, 'UTF-8') : 'N/A';
        $method = $request ? $request->method() : 'N/A';
        $ip = $request ? $request->ip() : 'N/A';
        $userAgent = $request ? htmlspecialchars(mb_substr($request->userAgent() ?? '', 0, 100), ENT_QUOTES, 'UTF-8') : 'N/A';
        
        $timestamp = Carbon::now('Europe/Istanbul')->format('d.m.Y H:i:s');

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
