<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use Livewire\Component;

class SearchModal extends Component
{
    public string $search = '';

    public function updatedSearch($value): void
    {
        // Livewire'dan array gelmesini engelle
        if (!is_string($value)) {
            $this->search = '';
        }
    }

    public function render()
    {
        $results = collect();

        $search = trim($this->search);

        if (mb_strlen($search) >= 2) {
            $results = Product::where('status', true)
                ->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('description', 'like', '%' . $search . '%');
                })
                ->take(5)
                ->get();
        }

        return view('livewire.frontend.search-modal', [
            'results' => $results,
        ]);
    }
}

