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
                
                @if(!empty($review->images) && is_array($review->images))
                    <div class="mt-4 flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: none;">
                        @foreach($review->images as $media)
                            @if(\Illuminate\Support\Str::endsWith(strtolower($media), ['.mp4', '.mov']))
                                <video src="{{ Storage::url($media) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200" controls></video>
                            @else
                                <a href="{{ Storage::url($media) }}" target="_blank" class="flex-shrink-0 block cursor-zoom-in">
                                    <img src="{{ Storage::url($media) }}" class="h-24 w-24 object-cover rounded-lg border border-gray-200 hover:opacity-90 transition-opacity">
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
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

        <div class="flex min-h-[100dvh] items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal Panel -->
            <div x-show="showReviewModal" 
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
                 class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-md border border-gray-100">
                
                <form wire:submit="submitReview" x-on:review-submitted.window="showReviewModal = false">
                    
                    <!-- Header -->
                    <div class="bg-emerald-50 px-4 py-3 sm:px-5 flex justify-between items-center border-b border-emerald-100/50">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                                <i class="fa-solid fa-star text-xs"></i>
                            </div>
                            <h3 class="text-base font-extrabold text-gray-900" id="modal-title">Ürünü Değerlendir</h3>
                        </div>
                        <button type="button" @click="showReviewModal = false" class="text-emerald-600 hover:text-emerald-800 transition-colors bg-white w-6 h-6 rounded-full flex items-center justify-center shadow-sm">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-4 py-4 sm:px-5 space-y-4">
                        <!-- Puan -->
                        <div class="text-center">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">Ürüne Puanınız</label>
                            <div class="flex justify-center gap-1 text-xl text-yellow-400">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" wire:click="$set('rating', {{ $i }})" class="focus:outline-none transition-transform hover:scale-110 active:scale-95">
                                        <i class="fa-{{ $rating >= $i ? 'solid' : 'regular' }} fa-star drop-shadow-sm"></i>
                                    </button>
                                @endfor
                            </div>
                            @error('rating') <p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- İsim ve E-posta -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">Adınız Soyadınız</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-regular fa-user text-xs"></i>
                                    </div>
                                    <input type="text" wire:model="name" style="padding-left: 2rem;" class="w-full rounded-lg border-gray-200 bg-gray-50/50 shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-xs pr-3 py-2 transition-all" placeholder="Örn: Ayşe Y.">
                                </div>
                                @error('name') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-700 mb-1">E-posta <span class="text-gray-400 font-normal text-[9px]">(Gizli)</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                                        <i class="fa-regular fa-envelope text-xs"></i>
                                    </div>
                                    <input type="email" wire:model="email" style="padding-left: 2rem;" class="w-full rounded-lg border-gray-200 bg-gray-50/50 shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-xs pr-3 py-2 transition-all" placeholder="E-posta">
                                </div>
                                @error('email') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Yorum -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Yorumunuz</label>
                            <textarea wire:model="comment" rows="2" class="w-full rounded-lg border-gray-200 bg-gray-50/50 shadow-inner focus:bg-white focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 text-xs px-3 py-2 transition-all resize-none" placeholder="Ürün hakkında ne düşünüyorsunuz?"></textarea>
                            @error('comment') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror
                        </div>

                        <!-- Fotoğraf / Video Yükleme -->
                        <div>
                            <label class="block text-[11px] font-bold text-gray-700 mb-1">Fotoğraf/Video <span class="text-gray-400 font-normal text-[9px]">(İsteğe Bağlı)</span></label>
                            <div class="mt-1 flex justify-center px-3 pt-3 pb-3 border-2 border-gray-300 border-dashed rounded-lg hover:border-emerald-500 transition-colors bg-gray-50/50">
                                <div class="space-y-1 text-center">
                                    <i class="fa-solid fa-cloud-arrow-up text-xl text-emerald-500 mb-1 block"></i>
                                    <div class="flex text-[11px] text-gray-600 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer rounded-md font-medium text-emerald-600 hover:text-emerald-500">
                                            <span>Dosya Seçin</span>
                                            <input id="file-upload" wire:model="media_files" type="file" multiple accept="image/*,video/mp4,video/quicktime" class="sr-only">
                                        </label>
                                        <p class="pl-1">veya sürükleyin</p>
                                    </div>
                                    <p class="text-[9px] text-gray-500">Maks. 20MB</p>
                                </div>
                            </div>
                            
                            <!-- Yükleniyor Uyarısı -->
                            <div wire:loading wire:target="media_files" class="mt-1 text-[11px] text-emerald-600 font-medium flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-notch fa-spin"></i> Dosyalar yükleniyor...
                            </div>

                            @error('media_files.*') <p class="text-[11px] text-red-500 mt-1 font-medium">{{ $message }}</p> @enderror

                            <!-- Önizleme -->
                            @if($media_files)
                                <div class="mt-2 grid grid-cols-5 sm:grid-cols-6 gap-1.5">
                                    @foreach($media_files as $index => $file)
                                        <div class="relative rounded-md overflow-hidden border border-gray-200 aspect-square shadow-sm">
                                            @if(str_contains($file->getMimeType(), 'image'))
                                                <img src="{{ $file->temporaryUrl() }}" class="object-cover w-full h-full">
                                            @else
                                                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                                    <i class="fa-solid fa-video text-gray-400 text-sm"></i>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-5 flex flex-col sm:flex-row-reverse gap-2 border-t border-gray-100">
                        <button type="submit" style="background-color: #059669; color: white; border: none;" class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 rounded-lg px-5 py-2 text-xs font-bold shadow-md shadow-emerald-200 hover:opacity-90 transition-all active:scale-95">
                            <i class="fa-regular fa-paper-plane"></i>
                            Gönder
                        </button>
                        <button type="button" @click="showReviewModal = false" class="w-full sm:w-auto inline-flex justify-center rounded-lg bg-white px-5 py-2 text-xs font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all active:scale-95">
                            İptal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
