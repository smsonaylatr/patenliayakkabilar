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

    <!-- REVIEW MODAL (YENİ TASARIM) -->
    <div x-show="showReviewModal" 
         style="display: none;" 
         class="fixed inset-0 z-[9999] overflow-y-auto" 
         aria-labelledby="modal-title" 
         role="dialog" 
         aria-modal="true">
        
        <!-- Backdrop -->
        <div x-show="showReviewModal" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" 
             @click="showReviewModal = false"
             aria-hidden="true"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal Panel -->
            <div x-show="showReviewModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-gray-100">
                
                <form wire:submit="submitReview" x-on:review-submitted.window="showReviewModal = false">
                    
                    <!-- Header -->
                    <div class="bg-emerald-50 px-6 py-5 sm:px-8 flex justify-between items-center border-b border-emerald-100/50">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <i class="fa-solid fa-star"></i>
                            </div>
                            <h3 class="text-xl font-extrabold text-gray-900" id="modal-title">Ürünü Değerlendir</h3>
                        </div>
                        <button type="button" @click="showReviewModal = false" class="text-emerald-600 hover:text-emerald-800 transition-colors bg-white w-8 h-8 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-6 sm:px-8 space-y-6">
                        <!-- Puan -->
                        <div class="text-center">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Ürüne Puanınız</label>
                            <div class="flex justify-center gap-2 text-3xl text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110 active:scale-95">
                                        <i class="fa-{{ $rating >= $i ? 'solid' : 'regular' }} fa-star drop-shadow-sm"></i>
                                    </button>
                                @endfor
                            </div>
                            @error('rating') <p class="text-sm text-red-500 mt-2 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- İsim ve E-posta -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Adınız Soyadınız</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-regular fa-user"></i>
                                    </div>
                                    <input type="text" wire:model="name" class="w-full rounded-xl border-gray-200 bg-gray-50/50 shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm pl-10 pr-4 py-3.5 transition-all" placeholder="Örn: Ayşe Yılmaz">
                                </div>
                                @error('name') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">E-posta <span class="text-gray-400 font-normal text-xs">(Gizli tutulur)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-regular fa-envelope"></i>
                                    </div>
                                    <input type="email" wire:model="email" class="w-full rounded-xl border-gray-200 bg-gray-50/50 shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm pl-10 pr-4 py-3.5 transition-all" placeholder="E-posta adresiniz">
                                </div>
                                @error('email') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Yorum -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Yorumunuz</label>
                            <textarea wire:model="comment" rows="4" class="w-full rounded-xl border-gray-200 bg-gray-50/50 shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-sm px-4 py-3.5 transition-all resize-none" placeholder="Ürün hakkında ne düşünüyorsunuz? Deneyiminizi diğer ebeveynlerle paylaşın..."></textarea>
                            @error('comment') <p class="text-sm text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-5 sm:px-8 flex flex-col sm:flex-row-reverse gap-3 border-t border-gray-100">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-emerald-600 px-8 py-3.5 text-sm font-bold text-white shadow-md shadow-emerald-200 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all active:scale-95">
                            <i class="fa-regular fa-paper-plane"></i>
                            Yorumu Gönder
                        </button>
                        <button type="button" @click="showReviewModal = false" class="w-full sm:w-auto inline-flex justify-center rounded-xl bg-white px-8 py-3.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all active:scale-95">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
