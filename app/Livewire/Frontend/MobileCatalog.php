<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Category;

class MobileCatalog extends Component
{
    public function render()
    {
        return view('livewire.frontend.mobile-catalog', [
            'categories' => Category::whereNull('parent_id')->where('status', true)->get(),
        ]);
    }
}
