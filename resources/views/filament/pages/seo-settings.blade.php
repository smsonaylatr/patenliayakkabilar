<x-filament-panels::page>

    {{-- SEO SAĞLIK DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Ürünler --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Ürün SEO</span>
                @if($this->productsWithoutMeta === 0 && $this->totalProducts > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ Tam</span>
                @elseif($this->productsWithoutMeta > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">Eksik</span>
                @endif
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $this->totalProducts - $this->productsWithoutMeta }}</span>
                <span class="text-sm text-gray-400 mb-1">/ {{ $this->totalProducts }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Meta başlıklı ürün</p>
            @if($this->productsWithoutMeta > 0)
                <button wire:click="regenerateAllProductSeo" wire:confirm="{{ $this->productsWithoutMeta }} ürünün meta verileri otomatik üretilecek. Devam?"
                        class="mt-3 w-full text-xs font-semibold text-center py-1.5 px-3 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-900/30 dark:text-primary-400 dark:hover:bg-primary-900/50 transition-colors">
                    🤖 {{ $this->productsWithoutMeta }} Ürün İçin Üret
                </button>
            @endif
        </div>

        {{-- Kategoriler --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Kategori SEO</span>
                @if($this->categoriesWithoutMeta === 0 && $this->totalCategories > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ Tam</span>
                @elseif($this->categoriesWithoutMeta > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">Eksik</span>
                @endif
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $this->totalCategories - $this->categoriesWithoutMeta }}</span>
                <span class="text-sm text-gray-400 mb-1">/ {{ $this->totalCategories }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Meta başlıklı kategori</p>
            @if($this->categoriesWithoutMeta > 0)
                <button wire:click="regenerateAllCategorySeo" wire:confirm="{{ $this->categoriesWithoutMeta }} kategorinin meta verileri otomatik üretilecek. Devam?"
                        class="mt-3 w-full text-xs font-semibold text-center py-1.5 px-3 rounded-lg bg-primary-50 text-primary-700 hover:bg-primary-100 dark:bg-primary-900/30 dark:text-primary-400 dark:hover:bg-primary-900/50 transition-colors">
                    🤖 {{ $this->categoriesWithoutMeta }} Kategori İçin Üret
                </button>
            @endif
        </div>

        {{-- Sayfalar --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Sayfa SEO</span>
                @if($this->pagesWithoutMeta === 0 && $this->totalPages > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ Tam</span>
                @elseif($this->pagesWithoutMeta > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">Eksik</span>
                @endif
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $this->totalPages - $this->pagesWithoutMeta }}</span>
                <span class="text-sm text-gray-400 mb-1">/ {{ $this->totalPages }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Meta başlıklı sayfa</p>
        </div>

        {{-- Blog --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Blog SEO</span>
                @if($this->blogPostsWithoutMeta === 0 && $this->totalBlogPosts > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">✓ Tam</span>
                @elseif($this->blogPostsWithoutMeta > 0)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200">Eksik</span>
                @endif
            </div>
            <div class="flex items-end gap-2">
                <span class="text-3xl font-extrabold text-gray-900 dark:text-white">{{ $this->totalBlogPosts - $this->blogPostsWithoutMeta }}</span>
                <span class="text-sm text-gray-400 mb-1">/ {{ $this->totalBlogPosts }}</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Meta başlıklı blog yazısı</p>
        </div>
    </div>

    {{-- AYARLAR FORMU --}}
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex justify-start">
            <x-filament::button type="submit" size="lg">
                💾 Ayarları Kaydet
            </x-filament::button>
        </div>
    </form>

    {{-- SEO ARAÇLARI --}}
    <div class="mt-8">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">🔧 SEO Araçları</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">SEO yönetimi için sık kullanılan araçlar ve hızlı linkler.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            {{-- Sitemap --}}
            <a href="{{ url('/sitemap.xml') }}" target="_blank"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Sitemap.xml</p>
                    <p class="text-xs text-gray-500 truncate">Site haritanızı görüntüleyin</p>
                </div>
            </a>

            {{-- Merchant Feed --}}
            <a href="{{ url('/feeds/google-merchant.xml') }}" target="_blank"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-green-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Merchant Feed</p>
                    <p class="text-xs text-gray-500 truncate">Ürün XML feed</p>
                </div>
            </a>

            {{-- robots.txt --}}
            <a href="{{ url('/robots.txt') }}" target="_blank"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">robots.txt</p>
                    <p class="text-xs text-gray-500 truncate">Tarayıcı kuralları</p>
                </div>
            </a>

            {{-- llms.txt --}}
            <a href="{{ url('/llms.txt') }}" target="_blank"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">llms.txt</p>
                    <p class="text-xs text-gray-500 truncate">AI tarayıcı bilgisi</p>
                </div>
            </a>

            {{-- Google Search Console --}}
            <a href="https://search.google.com/search-console" target="_blank" rel="noopener"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Search Console</p>
                    <p class="text-xs text-gray-500 truncate">Arama performansı</p>
                </div>
            </a>

            {{-- Rich Results Test --}}
            <a href="https://search.google.com/test/rich-results?url={{ urlencode(config('app.url')) }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-purple-50 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Rich Results Test</p>
                    <p class="text-xs text-gray-500 truncate">Schema doğrulama</p>
                </div>
            </a>

            {{-- PageSpeed Insights --}}
            <a href="https://pagespeed.web.dev/analysis?url={{ urlencode(config('app.url')) }}" target="_blank" rel="noopener"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">PageSpeed</p>
                    <p class="text-xs text-gray-500 truncate">Site hız testi</p>
                </div>
            </a>

            {{-- Google Merchant Center --}}
            <a href="https://merchants.google.com" target="_blank" rel="noopener"
               class="flex items-center gap-3 p-4 rounded-xl bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 hover:ring-primary-500/50 transition-all group">
                <div class="flex-shrink-0 w-9 h-9 bg-orange-50 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Merchant Center</p>
                    <p class="text-xs text-gray-500 truncate">Ürün feed yönetimi</p>
                </div>
            </a>
        </div>
    </div>
</x-filament-panels::page>
