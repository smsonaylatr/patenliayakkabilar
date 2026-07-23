<?php

namespace App\Livewire\Product;

use Livewire\Component;
use App\Models\Product;
use App\Models\Review;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ReviewList extends Component
{
    use WithPagination, WithFileUploads;

    public Product $product;

    // Form fields
    public $name = '';
    public $email = '';
    public $rating = 5;
    public $comment = '';
    public $media_files = [];

    public $showForm = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|min:5|max:1000',
        'media_files.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov|max:20480',
    ];

    public function messages()
    {
        return [
            'name.required' => 'Adınız Soyadınız alanı zorunludur.',
            'email.email' => 'Lütfen geçerli bir e-posta adresi girin.',
            'comment.required' => 'Lütfen yorumunuzu yazın.',
            'comment.min' => 'Yorumunuz en az 5 karakter olmalıdır.',
            'media_files.*.mimes' => 'Sadece JPG, PNG, MP4 veya MOV formatında dosyalar yükleyebilirsiniz.',
            'media_files.*.max' => 'Dosya boyutu en fazla 20MB olabilir.',
        ];
    }

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

        $mediaPaths = [];
        if ($this->media_files) {
            foreach ($this->media_files as $file) {
                $mediaPaths[] = $file->store('reviews', 'public');
            }
        }

        $this->product->reviews()->create([
            'user_id' => auth()->id(),
            'name' => $this->name,
            'email' => $this->email,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'images' => empty($mediaPaths) ? null : $mediaPaths,
            'status' => 0, // Pending approval
        ]);

        $this->reset(['comment', 'rating', 'showForm', 'media_files']);
        
        if (auth()->guest()) {
            $this->reset(['name', 'email']);
        }

        $this->dispatch('review-submitted');
        
        session()->flash('success', 'Yorumunuz başarıyla alındı. Onaylandıktan sonra yayınlanacaktır.');
    }

    public function render()
    {
        $reviews = $this->product->reviews()
            ->where('status', 1) // Only approved
            ->latest('id')
            ->take($this->product->has_installments ? 3 : 2)
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
