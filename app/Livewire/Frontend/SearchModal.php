<?php

namespace App\Livewire\Frontend;

use App\Models\Product;
use Livewire\Component;

class SearchModal extends Component
{
    public $search = '';

    public function render()
    {
        $results = collect();

        if (strlen($this->search) >= 2) {
            $results = Product::where('status', true)
                ->where(function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->take(5)
                ->get();
        }

        return view('livewire.frontend.search-modal', [
            'results' => $results
        ]);
    }
}
