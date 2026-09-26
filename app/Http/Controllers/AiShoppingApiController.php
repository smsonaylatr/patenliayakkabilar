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
    public function productContext(string $slug): Response
    {
        $product = Product::where('slug', $slug)->where('status', true)->firstOrFail();
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
