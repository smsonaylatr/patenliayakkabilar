<?php

namespace App\Livewire\Frontend;

use App\Models\BlogPost;
use Livewire\Component;

class BlogSearch extends Component
{
    public $search = '';

    public function mount()
    {
        $this->search = request('q', '');
    }

    public function render()
    {
        $results = collect();

        if (strlen(trim($this->search)) >= 2) {
            $terms = explode(' ', trim($this->search));
            $query = BlogPost::where('status', true);
            
            foreach ($terms as $term) {
                if (empty($term)) continue;
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', '%' . $term . '%')
                      ->orWhere('excerpt', 'like', '%' . $term . '%');
                });
            }
            
            $results = $query->take(5)->get();
        }

        return view('livewire.frontend.blog-search', [
            'results' => $results
        ]);
    }
}
