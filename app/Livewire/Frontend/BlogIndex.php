<?php

namespace App\Livewire\Frontend;

use App\Models\BlogPost;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function mount()
    {
        $this->search = request('q', '');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = BlogPost::where('status', true);

        if (strlen(trim($this->search)) > 0) {
            $terms = explode(' ', trim($this->search));
            foreach ($terms as $term) {
                if (empty($term)) continue;
                $query->where(function ($q) use ($term) {
                    $q->where('title', 'like', '%' . $term . '%')
                      ->orWhere('excerpt', 'like', '%' . $term . '%');
                });
            }
        }

        $posts = $query->orderByDesc('created_at')->paginate(12);

        return view('livewire.frontend.blog-index', [
            'posts' => $posts
        ])->layout('components.layouts.app');
    }
}
