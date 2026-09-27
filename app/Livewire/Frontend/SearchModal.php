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

    public function performSearch(): mixed
    {
        $search = trim($this->search);
        if (!empty($search)) {
            return $this->redirect('/patenli-ayakkabilar?search=' . urlencode($search), navigate: true);
        }
        return null;
    }

    public function render()
    {
        $results = collect();
        $suggestions = [];
        $matchedCategories = collect();

        $search = trim($this->search);

        if (mb_strlen($search) >= 2) {
            $slugTerm = \Illuminate\Support\Str::slug($search, '-');

            try {
                $aiData = app(AiShoppingService::class)->getSuggestions($search);
                $suggestions = $aiData['suggestions'] ?? [];
            } catch (\Throwable $e) {
                $suggestions = [];
            }

            $results = Product::where('status', true)
                ->where(function ($query) use ($search, $slugTerm) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('short_description', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%')
                          ->orWhere('sku', 'like', '%' . $search . '%');

                    if (!empty($slugTerm)) {
                        $query->orWhere('slug', 'like', '%' . $slugTerm . '%');
                    }

                    $query->orWhereHas('categories', function ($cq) use ($search, $slugTerm) {
                        $cq->where('name', 'like', '%' . $search . '%');
                        if (!empty($slugTerm)) {
                            $cq->orWhere('slug', 'like', '%' . $slugTerm . '%');
                        }
                    });

                    $query->orWhereHas('variants', function ($vq) use ($search) {
                        $vq->where('color', 'like', '%' . $search . '%')
                           ->orWhere('size', 'like', '%' . $search . '%')
                           ->orWhere('wheel_type', 'like', '%' . $search . '%')
                           ->orWhere('sku', 'like', '%' . $search . '%');
                    });
                })
                ->with(['images', 'categories'])
                ->take(6)
                ->get();

            $matchedCategories = Category::where('status', true)
                ->where(function ($cq) use ($search, $slugTerm) {
                    $cq->where('name', 'like', '%' . $search . '%');
                    if (!empty($slugTerm)) {
                        $cq->orWhere('slug', 'like', '%' . $slugTerm . '%');
                    }
                })
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
