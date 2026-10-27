<?php

namespace App\Livewire\Frontend;

use App\Models\BlogPost;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public mixed $search = '';

    public function mount()
    {
        $raw = request('q', '');
        $this->search = $this->sanitizeString($raw);
    }

    public function hydrate(): void
    {
        $this->search = $this->sanitizeString($this->search);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSearch($value): void
    {
        $this->search = $this->sanitizeString($this->search);
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    protected function sanitizeString(mixed $value): string
    {
        if (is_string($value)) {
            return trim($value);
        }

        if (is_array($value)) {
            $first = reset($value);
            return is_string($first) || is_numeric($first) ? trim((string) $first) : '';
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        return '';
    }

    public function render()
    {
        $this->search = $this->sanitizeString($this->search);

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

        $suggestions = collect();
        if (strlen(trim($this->search)) > 0) {
            $sQuery = BlogPost::where('status', true);
            $terms = explode(' ', trim($this->search));
            foreach ($terms as $term) {
                if (empty($term)) continue;
                $sQuery->where('title', 'like', '%' . $term . '%');
            }
            $suggestions = $sQuery->orderByDesc('created_at')->take(5)->get();
        }

        $posts = $query->orderByDesc('created_at')->paginate(12);

        return view('livewire.frontend.blog-index', [
            'posts' => $posts,
            'suggestions' => $suggestions
        ])->layout('components.layouts.app', [
            'title' => 'Paten Rehberi & Blog | Patenli Ayakkabılar',
        ]);
    }
}
