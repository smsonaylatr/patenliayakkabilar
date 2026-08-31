<div>
    <x-slot:title>Rehber Merkezi | Patenli Ayakkabılar</x-slot:title>
    <x-slot:description>Patenli ayakkabı rehberleri, kullanım kılavuzları, güvenlik ipuçları ve satın alma tavsiyeleri. Çocuğunuz için doğru patenli ayakkabıyı seçin.</x-slot:description>
    <x-slot:canonical>{{ url('/blog') }}</x-slot:canonical>
    <x-slot:ogType>website</x-slot:ogType>
    <x-slot:ogImage>{{ asset('images/logo.png') }}</x-slot:ogImage>
    <x-slot:schema>
        <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "Blog",
            "name": "Patenli Ayakkabılar Rehber Merkezi",
            "description": "Patenli ayakkabı rehberleri, kullanım kılavuzları, güvenlik ipuçları ve satın alma tavsiyeleri.",
            "url": "{{ url('/blog') }}"
        }
        </script>
    </x-slot:schema>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="mb-8">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi'],
                ]" />
            </div>

            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Rehber Merkezi</h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">Patenli ayakkabı seçimi, kullanımı ve bakımı hakkında bilmeniz gereken her şey.</p>
                
                {{-- Arama Formu (Livewire Live Search) --}}
                <div class="mt-8 max-w-xl mx-auto px-4 sm:px-0 relative" x-data="{ open: false }" @click.outside="open = false">
                    <div class="relative flex items-center">
                        <input 
                            type="text" 
                            wire:model.live.debounce.300ms="search"
                            @focus="open = true"
                            @input="open = true"
                            @keydown.escape="open = false"
                            placeholder="Blog yazılarında ara..." 
                            class="w-full pl-6 pr-24 py-3 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400"
                            autocomplete="off"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1">
                            @if(strlen($search) > 0)
                                <button type="button" wire:click="clearSearch" aria-label="Aramayı Temizle" class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            @endif
                            <div class="p-2 text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Arama Önerileri Dropdown --}}
                    <div x-show="open && $wire.search.length > 0" x-transition x-cloak class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-lg overflow-hidden text-left" style="display: none;">
                        @if(strlen($search) > 0 && $suggestions->count() > 0)
                            <ul class="py-2">
                                @foreach($suggestions as $post)
                                    <li>
                                        <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="flex items-center px-6 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4 mr-3 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                            <span class="truncate">{{ $post->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @elseif(strlen($search) > 0)
                            <div class="px-6 py-4 text-sm text-gray-500 text-center">
                                Başlıkta eşleşen sonuç bulunamadı.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div wire:loading.delay class="w-full text-center py-12">
                <p class="text-gray-500 font-medium text-lg">Yazılar aranıyor...</p>
            </div>

            <div wire:loading.remove.delay>
                @if($posts->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        @foreach($posts as $post)
                            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow duration-300 group">
                                @if($post->image_path)
                                    <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="block aspect-[16/9] overflow-hidden">
                                        <img src="{{ asset('storage/' . $post->image_path) }}" 
                                             alt="{{ $post->image_alt ?? $post->title }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                             loading="lazy">
                                    </a>
                                @endif
                                <div class="p-6">
                                    <time class="text-xs font-medium text-gray-400 uppercase tracking-wider" datetime="{{ $post->created_at->toW3cString() }}">
                                        {{ $post->created_at->translatedFormat('d F Y') }}
                                    </time>
                                    <h2 class="mt-2 text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                        <a href="{{ route('blog.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a>
                                    </h2>
                                    @if($post->excerpt)
                                        <p class="mt-3 text-sm text-gray-500 line-clamp-3">{{ $post->excerpt }}</p>
                                    @endif
                                    <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                        Devamını Oku
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div class="mt-12">
                        {{ $posts->links() }}
                    </div>
                @else
                    <div class="text-center py-20">
                        <p class="text-xl font-medium text-gray-900">Sonuç bulunamadı.</p>
                        <p class="text-gray-500 mt-2">"{{ $search }}" kelimesini içeren bir yazı henüz eklenmemiş.</p>
                        <button type="button" wire:click="clearSearch" class="mt-6 inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-full font-semibold text-sm text-white tracking-wide hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 ring-blue-300 transition-all shadow-sm">
                            Tüm Yazıları Gör
                        </button>
                    </div>
                @endif
            </div>

            {{-- SEO Zenginleştirme Metni (Sayfa Altı) --}}
            <div class="mt-16 bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Neden Patenli Ayakkabı Rehberini Okumalısınız?</h2>
                <div class="prose prose-blue max-w-none text-gray-600">
                    <p>Çocuklar ve gençler için son dönemin en popüler eğlence aracı olan <strong>patenli ayakkabılar</strong>, doğru seçildiğinde ve güvenli kullanıldığında harika bir fiziksel aktivitedir. Ancak ister <strong>ışıklı tekerlekli ayakkabı</strong> ister <strong>klasik çocuk patenli ayakkabı modelleri</strong> arıyor olun, doğru bedeni seçmek, tekerlek bakımını yapmak ve güvenlik ekipmanlarını doğru kullanmak hayati önem taşır.</p>
                    
                    <p class="mt-4">Rehber Merkezimizdeki içeriklerimiz, uzmanlar ve tecrübeli ebeveynlerin görüşleri doğrultusunda özel olarak hazırlanmaktadır. Blog yazılarımızda şu konuları bulabilirsiniz:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li>Patenli ayakkabı ile ilk sürüş teknikleri ve denge kurma pratikleri</li>
                        <li>Tekerlek bakımı, temizliği ve rulmanların uzun ömürlü kullanımı</li>
                        <li>Güvenli sürüş alanları ve dikkat edilmesi gereken risk faktörleri</li>
                        <li>Çocuğunuzun ayak gelişimini destekleyecek doğru ayakkabı seçimi (Ortopedik detaylar)</li>
                    </ul>
                    <p class="mt-4 text-sm text-gray-500">Düzenli olarak güncellenen blog içeriklerimizle hem sizin hem de çocuğunuzun daha güvenli ve keyifli bir sürüş deneyimi yaşamasını hedefliyoruz.</p>
                </div>
            </div>

        </div>
    </div>
</div>
