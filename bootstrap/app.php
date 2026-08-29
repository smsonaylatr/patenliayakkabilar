<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(append: [
            \App\Http\Middleware\TrackCustomerActivity::class,
            \App\Http\Middleware\CaptureGoogleClickId::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'payment/paytr/webhook',
            'api/n8n/blog-publish',
            'api/porego/*',
            'api/telegram/webhook',
            'admin/logout',
            'logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e) {
            try {
                $request = request();
                app(\App\Services\ExceptionTelegramReporter::class)->report($e, $request);
            } catch (\Throwable $reportException) {
                // Telegram raporlama hatası ana hatayı engellememeli
                \Illuminate\Support\Facades\Log::error('Telegram error reporter failed: ' . $reportException->getMessage());
            }
        });
    })->create();
