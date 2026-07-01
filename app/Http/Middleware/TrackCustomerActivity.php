<?php

namespace App\Http\Middleware;

use App\Models\CustomerEvent;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackCustomerActivity
{
    /**
     * Sayfa görüntüleme olaylarını otomatik kaydet.
     * Sadece GET istekleri + HTML sayfa yanıtları için çalışır.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Sadece GET istekleri ve başarılı HTML yanıtları
        if (
            !$request->isMethod('GET') ||
            $request->ajax() ||
            $request->is('admin/*') || // Admin paneli hariç
            $request->is('livewire/*') || // Livewire iç istekleri hariç
            $response->getStatusCode() !== 200
        ) {
            return $response;
        }

        try {
            $eventData = [];
            $eventType = 'page_view';

            // Ürün sayfası tespiti
            if ($request->route() && $request->route()->getName() === 'product.show') {
                $eventType = 'product_view';
                $product = $request->route()->parameter('product');
                if ($product) {
                    $eventData['product_id'] = is_object($product) ? $product->id : $product;
                }
            }

            // Kategori sayfası tespiti
            if ($request->route() && $request->route()->getName() === 'category.show') {
                $eventData['category_slug'] = $request->route()->parameter('category');
            }

            // Arama tespiti
            if ($request->has('q') || $request->has('search')) {
                $eventType = 'search';
                $eventData['search_query'] = $request->get('q') ?? $request->get('search');
            }

            CustomerEvent::create([
                'user_id' => auth()->id(),
                'session_id' => $request->session()->getId(),
                'event_type' => $eventType,
                'event_data' => !empty($eventData) ? $eventData : null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'referrer' => $request->header('referer'),
                'utm_source' => $request->get('utm_source'),
                'utm_medium' => $request->get('utm_medium'),
                'utm_campaign' => $request->get('utm_campaign'),
            ]);
        } catch (\Throwable $e) {
            // Event tracking asla sayfayı kırmamalı
            report($e);
        }

        return $response;
    }
}
