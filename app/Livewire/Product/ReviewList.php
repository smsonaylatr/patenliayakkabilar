<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Review;
use Livewire\WithPagination;

class ReviewList extends Component
{
    use WithPagination;

    public Product $product;

    // Form fields
    public $name = '';
    public $email = '';
    public $rating = 5;
    public $comment = '';

    public $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:5|max:1000',
    ];

    public function mount(Product $product)
    {
        $this->product = $product;
        
        if (auth()->check()) {
            $this->name = auth()->user()->name;
            $this->email = auth()->user()->email;
        }
    }

    public function submitReview()
    {
        $this->validate();

        $this->product->reviews()->create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'email' => $this->email,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'status' => 0, // Pending approval
        ]);

        $this->reset(['comment', 'rating', 'showForm']);
        
        if (auth()->guest()) {
            $this->reset(['name', 'email']);
        }

        session()->flash('success', 'Yorumunuz başarıyla alındı. Onaylandıktan sonra yayınlanacaktır.');
    }

    public function render()
    {
        $reviews = $this->product->reviews()
            ->where('status', 1) // Only approved
            ->latest()
            ->take(5)
            ->get();

        $averageRating = $this->product->reviews()->where('status', 1)->avg('rating') ?? 5.0;
        $totalReviews = $this->product->reviews()->where('status', 1)->count();

        return view('livewire.product.review-list', [
            'reviews' => $reviews,
            'averageRating' => number_format($averageRating, 1),
            'totalReviews' => $totalReviews,
        ]);
    }
}
