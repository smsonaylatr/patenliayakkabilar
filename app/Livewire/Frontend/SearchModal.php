<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Product;
use App\Services\AiShoppingService;
use Livewire\Component;

class SearchModal extends Component
{
    public string $search = '';

    public function updatedSearch($value): void
    {
        if (!is_string($value)) {
            $this->search = '';
        }
    }

    public function selectSuggestion(string $term): void
    {
        $this->search = $term;
    }

    public function render()
    {
        $results = collect();
        $suggestions = [];
        $matchedCategories = collect();

        $search = trim($this->search);

        if (mb_strlen($search) >= 2) {
            $aiData = app(AiShoppingService::class)->getSuggestions($search);
            $suggestions = $aiData['suggestions'] ?? [];

            $results = Product::where('status', true)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('short_description', 'like', '%' . $search . '%')
                          ->orWhere('sku', 'like', '%' . $search . '%');
                })
                ->with(['images', 'categories'])
                ->take(6)
                ->get();

            $matchedCategories = Category::where('status', true)
                ->where('name', 'like', '%' . $search . '%')
                ->take(3)
                ->get();
        }

        return view('livewire.frontend.search-modal', [
            'results' => $results,
            'suggestions' => $suggestions,
            'matchedCategories' => $matchedCategories,
        ]);
    }
}
