<x-layouts.app>
    <x-slot:title>{{ $product->name }} Yorumları | Patenli Ayakkabılar</x-slot:title>

    <div class="pt-4 lg:pt-16 pb-20 bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <div class="mb-8">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => $product->name, 'url' => route('products.show', $product->slug)],
                    ['name' => 'Müşteri Yorumları'],
                ]" />
            </div>

            <div class="bg-white rounded-[2rem] border border-gray-100 shadow-sm p-6 sm:p-10">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 mb-10 pb-8 border-b border-gray-100">
                    <div class="flex items-center gap-6">
                        <img src="{{ $product->images->first()?->image_url ?? asset('favicon.png') }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded-2xl border border-gray-100">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                            <div class="flex items-center gap-3 mt-2">
                                <div class="flex text-yellow-400 text-base">
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
                                <span class="text-sm font-medium text-gray-700">{{ number_format($averageRating, 1) }} / 5.0</span>
                                <span class="text-sm text-gray-400">({{ $reviews->total() }} Değerlendirme)</span>
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('products.show', $product->slug) }}#tanitim-bolumu" class="flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-emerald-50 text-emerald-700 font-bold hover:bg-emerald-100 transition-colors whitespace-nowrap flex-shrink-0">
                        Ürüne Dön
                    </a>
                </div>

                <div class="space-y-8">
                    @forelse($reviews as $review)
                        <div class="border-b border-gray-50 pb-8 last:border-0 last:pb-0">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-4">
                                    @php
                                        $initials = collect(explode(' ', $review->name ?? 'Müşteri'))
                                            ->map(fn($segment) => mb_substr($segment, 0, 1))
                                            ->take(2)
                                            ->implode('');
                                    @endphp
                                    <div class="w-12 h-12 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 font-bold text-base uppercase">
                                        {{ $initials }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-base text-gray-900 flex items-center gap-2">
                                            {{ $review->name ?? 'Gizli Müşteri' }}
                                            <i class="fa-solid fa-circle-check text-emerald-500 text-sm" title="Doğrulanmış Alıcı"></i>
                                        </div>
                                        <div class="text-sm text-gray-400 mt-1">{{ $review->created_at->translatedFormat('d F Y') }}</div>
                                    </div>
                                </div>
                                <div class="flex text-yellow-400 text-sm tracking-widest">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-base text-gray-700 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-500">
                            <i class="fa-regular fa-comment-dots text-5xl text-gray-300 mb-4 block"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Henüz yorum yapılmamış</h3>
                            <p>Bu ürün için henüz onaylanmış bir yorum bulunmuyor.</p>
                        </div>
                    @endforelse
                </div>

                @if($reviews->hasPages())
                    <div class="mt-10 pt-8 border-t border-gray-100">
                        {{ $reviews->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
