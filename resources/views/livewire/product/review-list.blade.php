<div class="mt-12 bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-8" x-data="{ showReviewModal: false }">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Müşteri Yorumları</h2>
            <div class="flex items-center gap-2 mt-2">
                <div class="flex text-yellow-400 text-sm">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($averageRating))
                            <i class="fa-solid fa-star"></i>
                        @elseif($i == ceil($averageRating) && fmod($averageRating, 1) !== 0.0)
                            <i class="fa-solid fa-star-half-stroke"></i>
                        @else
                            <i class="fa-regular fa-star"></i>
                        @endif
                    @endfor
                </div>
                <span class="text-sm font-medium text-gray-700">{{ $averageRating }} / 5.0</span>
                <span class="text-sm text-gray-400 hidden sm:inline-block">({{ $totalReviews }} Değerlendirme)</span>
            </div>
        </div>
        <button @click="showReviewModal = true" class="hidden sm:flex items-center gap-2 text-sm font-bold text-emerald-600 hover:text-emerald-700 transition-colors">
            Yorum Yap <i class="fa-solid fa-arrow-right"></i>
        </button>
    </div>

    @if (session()->has('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium">
            <i class="fa-solid fa-circle-check mr-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        @forelse($reviews as $review)
            <div class="border-b border-gray-50 pb-6 last:border-0 last:pb-0">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        @php
                            $initials = collect(explode(' ', $review->name ?? 'Müşteri'))
                                ->map(fn($segment) => mb_substr($segment, 0, 1))
                                ->take(2)
                                ->implode('');
                        @endphp
                        <div class="w-11 h-11 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-sm uppercase">
                            {{ $initials }}
                        </div>
                        <div>
                            <div class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                {{ $review->name ?? 'Gizli Müşteri' }}
                                <i class="fa-solid fa-circle-check text-emerald-500 text-xs" title="Doğrulanmış Alıcı"></i>
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $review->created_at->translatedFormat('d F Y') }}</div>
                        </div>
                    </div>
                    <div class="flex text-yellow-400 text-xs tracking-widest">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                        @endfor
                    </div>
                </div>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $review->comment }}</p>
            </div>
        @empty
            <div class="text-center py-8 text-gray-500 text-sm">
                <i class="fa-regular fa-comment-dots text-3xl text-gray-300 mb-3 block"></i>
                Bu ürüne henüz yorum yapılmamış. İlk yorumu siz yapın!
            </div>
        @endforelse
    </div>
    
    @if($totalReviews > 0)
    <a href="{{ route('products.reviews', $product->slug) }}" class="block w-full mt-6 py-3.5 rounded-xl bg-gray-50 border border-gray-100 text-sm font-bold text-gray-900 hover:bg-gray-100 transition-colors text-center">
        Tüm Yorumları Gör ({{ $totalReviews }})
    </a>
    @endif

    <button @click="showReviewModal = true" class="sm:hidden w-full mt-4 py-3.5 rounded-xl bg-emerald-50 border border-emerald-100 text-sm font-bold text-emerald-700 hover:bg-emerald-100 transition-colors">
        Yorum Yap
    </button>

    <!-- Review Modal -->
    <template x-teleport="body">
        <div x-show="showReviewModal" x-on:review-submitted.window="showReviewModal = false" style="display: none;" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showReviewModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" @click="showReviewModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showReviewModal" x-transition.scale class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                    <form wire:submit.prevent="submitReview" class="p-6 sm:p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xl font-bold text-gray-900" id="modal-title">Ürünü Değerlendir</h3>
                            <button type="button" @click="showReviewModal = false" class="text-gray-400 hover:text-gray-500">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Puanınız</label>
                                <div class="flex gap-2 text-2xl text-yellow-400 cursor-pointer">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i wire:click="$set('rating', {{ $i }})" class="fa-{{ $rating >= $i ? 'solid' : 'regular' }} fa-star hover:scale-110 transition-transform"></i>
                                    @endfor
                                </div>
                                @error('rating') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Name & Email -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Adınız Soyadınız</label>
                                    <input type="text" wire:model="name" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm px-4 py-3" placeholder="Örn: Ayşe Yılmaz">
                                    @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">E-posta (Gizli kalacak)</label>
                                    <input type="email" wire:model="email" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm px-4 py-3" placeholder="Örn: ayse@email.com">
                                    @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Comment -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yorumunuz</label>
                                <textarea wire:model="comment" rows="4" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm px-4 py-3" placeholder="Ürün hakkında ne düşünüyorsunuz?"></textarea>
                                @error('comment') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" @click="showReviewModal = false" class="flex-1 px-4 py-3 rounded-xl border border-gray-200 text-gray-700 font-bold hover:bg-gray-50 transition-colors">İptal</button>
                            <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors">Gönder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
