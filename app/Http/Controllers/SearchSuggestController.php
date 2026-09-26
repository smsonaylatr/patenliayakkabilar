<?php

namespace App\Http\Controllers;

use App\Services\AiShoppingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestController extends Controller
{
    public function __construct(
        protected AiShoppingService $aiShoppingService
    ) {}

    /**
     * Akıllı Arama ve AI Öneri Motoru Endpoint'i
     * GET /api/search/suggest?q=...
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->query('q', '');
        $data = $this->aiShoppingService->getSuggestions($query);

        // OpenSearch formatı talep edildiyse standart dizi döndür
        if ($request->header('Accept') === 'application/x-suggestions+json') {
            $suggestions = $data['suggestions'] ?? [];
            return response()->json([
                $query,
                $suggestions,
                array_fill(0, count($suggestions), ''),
                array_map(fn ($s) => url('/patenli-ayakkabilar?search=' . urlencode($s)), $suggestions),
            ], 200, [
                'Content-Type' => 'application/x-suggestions+json',
                'Access-Control-Allow-Origin' => '*',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ], 200, [
            'Cache-Control' => 'public, max-age=300',
            'Access-Control-Allow-Origin' => '*',
        ]);
    }
}
