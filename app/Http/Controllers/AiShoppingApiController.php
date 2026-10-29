<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\AiShoppingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AiShoppingApiController extends Controller
{
    public function __construct(
        protected AiShoppingService $aiShoppingService
    ) {}

    /**
     * AI Shopping Products API (ChatGPT, Perplexity, Gemini)
     * GET /api/v1/ai/shopping/products
     */
    public function products(Request $request): JsonResponse
    {
        $filters = [
            'q' => $request->query('q'),
            'gender' => $request->query('gender'),
            'category' => $request->query('category'),
            'in_stock_only' => $request->boolean('in_stock_only', false),
            'limit' => min(50, (int) $request->query('limit', 20)),
        ];

        $data = $this->aiShoppingService->getShoppingProducts($filters);

        return response()->json($data, 200, [
            'Cache-Control' => 'public, max-age=600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * AI Shopping Product Details
     * GET /api/v1/ai/shopping/product/{slug}
     */
    public function product(string $slug): JsonResponse
    {
        $product = Product::where('status', true)
            ->where(function ($q) use ($slug) {
                $q->where('slug', $slug)
                  ->orWhere('sku', $slug)
                  ->orWhere('id', $slug);
            })
            ->first();

        if (!$product) {
            $resolved = Product::resolveByOldSlug($slug);
            if ($resolved && $resolved->status) {
                $product = $resolved;
            }
        }

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Ürün bulunamadı veya satışta değil.',
            ], 404);
        }

        $details = $this->aiShoppingService->getProductAiDetails($product);

        return response()->json([
            'success' => true,
            'data' => $details,
        ], 200, [
            'Cache-Control' => 'public, max-age=600',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * AI Vector Embeddings Dataset
     * GET /api/v1/ai/embeddings
     */
    public function embeddings(): JsonResponse
    {
        $dataset = $this->aiShoppingService->getVectorEmbeddingsDataset();

        return response()->json($dataset, 200, [
            'Cache-Control' => 'public, max-age=1800',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Sone Central Canlı Envanter & Stok API'si
     * GET /api/v1/ai/sonecentral/catalog
     */
    public function soneCentralCatalog(): JsonResponse
    {
        $data = $this->aiShoppingService->getSoneCentralCatalog();

        return response()->json($data, 200, [
            'Cache-Control' => 'no-cache, private',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * AI Asistan & Canlı Destek Sipariş Sorgulama API'si
     * POST|GET /api/v1/ai/order-status
     */
    public function orderStatus(Request $request): JsonResponse
    {
        $query = trim((string) ($request->input('order_number') ?: $request->input('q') ?: $request->input('phone')));

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen sipariş numaranızı (örn: #1024) veya telefon numaranızı girin.',
            ], 400);
        }

        // Temizleme: # işaretini ve boşlukları at
        $cleanQuery = ltrim($query, '#');
        $cleanPhone = preg_replace('/[^0-9]/', '', $query);

        $order = \App\Models\Order::with(['items.product', 'items.variant'])
            ->where(function ($q) use ($query, $cleanQuery, $cleanPhone) {
                $q->where('order_number', $query)
                  ->orWhere('order_number', $cleanQuery)
                  ->orWhere('order_number', '#' . $cleanQuery);

                if (strlen($cleanPhone) >= 7) {
                    $phoneEnd = substr($cleanPhone, -10);
                    $q->orWhere('customer_phone', 'like', "%{$phoneEnd}%");
                }
            })
            ->latest()
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Girilen bilgiye ait aktif bir sipariş kaydı bulunamadı. Lütfen sipariş numaranızı kontrol ediniz veya WhatsApp hattımızdan (+90 850 307 3164) bize ulaşınız.',
            ], 404);
        }

        // Durum Türkçeleştirme
        $statusLabels = [
            'pending' => 'Sipariş Alındı (Onay Bekliyor)',
            'processing' => 'Siparişiniz Hazırlanıyor',
            'shipped' => 'Kargoya Verildi',
            'delivered' => 'Teslim Edildi',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi',
        ];
        $statusText = $statusLabels[$order->status] ?? ucfirst((string) $order->status);

        // Müşteri adını maskele (KVKK)
        $nameParts = explode(' ', trim((string) $order->customer_name));
        $maskedName = '';
        foreach ($nameParts as $part) {
            if (mb_strlen($part) <= 2) {
                $maskedName .= $part . ' ';
            } else {
                $maskedName .= mb_substr($part, 0, 2) . str_repeat('*', max(1, mb_strlen($part) - 2)) . ' ';
            }
        }
        $maskedName = trim($maskedName);

        $itemsSummary = $order->items->map(function ($item) {
            $size = $item->variant?->size ?: ($item->variant_info ?: '');
            return [
                'name' => $item->product_name ?: ($item->product?->name ?? 'Patenli Ayakkabı'),
                'size' => $size,
                'quantity' => (int) $item->quantity,
                'price' => number_format((float) $item->price, 2) . ' ₺',
            ];
        })->toArray();

        return response()->json([
            'success' => true,
            'order' => [
                'order_number' => $order->order_number,
                'status' => $order->status,
                'status_label' => $statusText,
                'customer' => $maskedName,
                'created_at' => $order->created_at ? $order->created_at->format('d.m.Y H:i') : '',
                'total' => number_format((float) $order->total_amount, 2) . ' ₺',
                'shipping_company' => $order->shipping_company ?: 'Anlaşmalı Kargo (Porego / Sürat / Yurtiçi)',
                'tracking_code' => $order->shipping_tracking_code ?: null,
                'tracking_url' => $order->shipping_tracking_url ?: ($order->shipping_tracking_code ? "https://kargotakip.porego.com/{$order->shipping_tracking_code}" : null),
                'items' => $itemsSummary,
                'help_text' => 'Herhangi bir sorunuzda +90 850 307 3164 WhatsApp destek hattımızdan 7/24 bilgi alabilirsiniz.',
            ],
        ], 200, [
            'Access-Control-Allow-Origin' => '*',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * llms-full.txt Endpoint
     * GET /llms-full.txt
     */
    public function llmsFull(): Response
    {
        $markdown = $this->aiShoppingService->generateLlmsFullMarkdown();

        return response($markdown, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'public, max-age=1800',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }

    /**
     * Ürün ham LLM Context çıktısı
     * GET /urun/{slug}/context
     */
    public function productContext(string $slug)
    {
        $product = Product::where('slug', $slug)->where('status', true)->first();
        if (!$product) {
            $resolved = Product::resolveByOldSlug($slug);
            if ($resolved && $resolved->status) {
                return redirect()->route('product.context', ['slug' => $resolved->slug], 301);
            }
            abort(404);
        }
        $details = $this->aiShoppingService->getProductAiDetails($product);

        $md = "# {$product->name}\n\n";
        $md .= "- **Fiyat:** {$details['formatted_price']}\n";
        $md .= "- **Kategori:** {$details['category']}\n";
        $md .= "- **Marka:** {$details['brand']}\n";
        $md .= "- **Stok Durumu:** " . ($details['in_stock'] ? 'Stokta Var' : 'Tükendi') . "\n";
        $md .= "- **Mevcut Numaralar:** " . implode(', ', $details['available_sizes']) . "\n\n";
        $md .= "## Hızlı Özet (AEO / TL;DR)\n{$details['quick_facts_tldr']}\n\n";
        $md .= "## Sıkça Sorulan Sorular\n";
        foreach ($details['faq'] as $f) {
            $md .= "### {$f['question']}\n{$f['answer']}\n\n";
        }

        return response($md, 200, [
            'Content-Type' => 'text/markdown; charset=UTF-8',
            'Cache-Control' => 'public, max-age=600',
        ]);
    }
}
