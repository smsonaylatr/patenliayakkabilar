<x-filament-panels::page>

    {{-- SEO SAĞLIK DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        {{-- Ürünler --}}
        @php
            $productPercent = $this->totalProducts > 0 ? round((($this->totalProducts - $this->productsWithoutMeta) / $this->totalProducts) * 100) : 0;
            $catPercent = $this->totalCategories > 0 ? round((($this->totalCategories - $this->categoriesWithoutMeta) / $this->totalCategories) * 100) : 0;
            $pagePercent = $this->totalPages > 0 ? round((($this->totalPages - $this->pagesWithoutMeta) / $this->totalPages) * 100) : 0;
            $blogPercent = $this->totalBlogPosts > 0 ? round((($this->totalBlogPosts - $this->blogPostsWithoutMeta) / $this->totalBlogPosts) * 100) : 0;
        @endphp

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                            <x-heroicon-o-shopping-bag class="w-5 h-5 text-blue-600 dark:text-blue-400"/>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Ürünler</h3>
                    </div>
                    @if($this->productsWithoutMeta === 0 && $this->totalProducts > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                            <x-heroicon-s-check-circle class="w-4 h-4"/> Kusursuz
                        </span>
                    @elseif($this->productsWithoutMeta > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400">
                            <x-heroicon-s-exclamation-triangle class="w-4 h-4"/> {{ $this->productsWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">%{{ $productPercent }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-800">
                        <div class="{{ $productPercent == 100 ? 'bg-emerald-500' : 'bg-blue-500' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $productPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Toplam <strong>{{ $this->totalProducts }}</strong> üründen <strong>{{ $this->totalProducts - $this->productsWithoutMeta }}</strong> tanesinde SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->productsWithoutMeta > 0)
                <button wire:click="regenerateAllProductSeo" wire:confirm="{{ $this->productsWithoutMeta }} ürünün SEO başlık ve açıklamaları yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        class="mt-2 w-full flex items-center justify-center gap-2 text-sm font-semibold py-2 px-4 rounded-lg bg-gray-900 text-white hover:bg-gray-800 dark:bg-primary-600 dark:hover:bg-primary-500 transition-colors shadow-sm">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div class="mt-2 w-full text-center py-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                    Tüm ürünler optimize edildi 🎉
                </div>
            @endif
        </div>

        {{-- Kategoriler --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                            <x-heroicon-o-rectangle-group class="w-5 h-5 text-indigo-600 dark:text-indigo-400"/>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Kategoriler</h3>
                    </div>
                    @if($this->categoriesWithoutMeta === 0 && $this->totalCategories > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                            <x-heroicon-s-check-circle class="w-4 h-4"/> Kusursuz
                        </span>
                    @elseif($this->categoriesWithoutMeta > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                            <x-heroicon-s-exclamation-triangle class="w-4 h-4"/> {{ $this->categoriesWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">%{{ $catPercent }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-800">
                        <div class="{{ $catPercent == 100 ? 'bg-emerald-500' : 'bg-indigo-500' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $catPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Toplam <strong>{{ $this->totalCategories }}</strong> kategoriden <strong>{{ $this->totalCategories - $this->categoriesWithoutMeta }}</strong> tanesinde SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->categoriesWithoutMeta > 0)
                <button wire:click="regenerateAllCategorySeo" wire:confirm="{{ $this->categoriesWithoutMeta }} kategorinin SEO verileri yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        class="mt-2 w-full flex items-center justify-center gap-2 text-sm font-semibold py-2 px-4 rounded-lg bg-gray-900 text-white hover:bg-gray-800 dark:bg-primary-600 dark:hover:bg-primary-500 transition-colors shadow-sm">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div class="mt-2 w-full text-center py-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                    Tüm kategoriler optimize 🎉
                </div>
            @endif
        </div>

        {{-- Sayfalar --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
                            <x-heroicon-o-document-text class="w-5 h-5 text-purple-600 dark:text-purple-400"/>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Sayfalar</h3>
                    </div>
                    @if($this->pagesWithoutMeta === 0 && $this->totalPages > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                            <x-heroicon-s-check-circle class="w-4 h-4"/> Kusursuz
                        </span>
                    @elseif($this->pagesWithoutMeta > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                            <x-heroicon-s-exclamation-triangle class="w-4 h-4"/> {{ $this->pagesWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">%{{ $pagePercent }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-800">
                        <div class="{{ $pagePercent == 100 ? 'bg-emerald-500' : 'bg-purple-500' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $pagePercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Toplam <strong>{{ $this->totalPages }}</strong> kurumsal sayfadan <strong>{{ $this->totalPages - $this->pagesWithoutMeta }}</strong> tanesinde SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->pagesWithoutMeta > 0)
                <button wire:click="regenerateAllPageSeo" wire:confirm="{{ $this->pagesWithoutMeta }} sayfanın SEO verileri yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        class="mt-2 w-full flex items-center justify-center gap-2 text-sm font-semibold py-2 px-4 rounded-lg bg-gray-900 text-white hover:bg-gray-800 dark:bg-primary-600 dark:hover:bg-primary-500 transition-colors shadow-sm">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div class="mt-2 w-full text-center py-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                    Tüm sayfalar optimize 🎉
                </div>
            @endif
        </div>

        {{-- Blog --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-pink-50 dark:bg-pink-900/30 rounded-lg">
                            <x-heroicon-o-pencil-square class="w-5 h-5 text-pink-600 dark:text-pink-400"/>
                        </div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200">Blog</h3>
                    </div>
                    @if($this->blogPostsWithoutMeta === 0 && $this->totalBlogPosts > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                            <x-heroicon-s-check-circle class="w-4 h-4"/> Kusursuz
                        </span>
                    @elseif($this->blogPostsWithoutMeta > 0)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400">
                            <x-heroicon-s-exclamation-triangle class="w-4 h-4"/> {{ $this->blogPostsWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-1.5">
                        <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                        <span class="font-bold text-gray-700 dark:text-gray-300">%{{ $blogPercent }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 dark:bg-gray-800">
                        <div class="{{ $blogPercent == 100 ? 'bg-emerald-500' : 'bg-pink-500' }} h-2.5 rounded-full transition-all duration-500" style="width: {{ $blogPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 leading-relaxed">
                        Toplam <strong>{{ $this->totalBlogPosts }}</strong> blog yazısından <strong>{{ $this->totalBlogPosts - $this->blogPostsWithoutMeta }}</strong> tanesinde SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->blogPostsWithoutMeta > 0)
                <button wire:click="regenerateAllBlogSeo" wire:confirm="{{ $this->blogPostsWithoutMeta }} blog yazısının SEO verileri yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        class="mt-2 w-full flex items-center justify-center gap-2 text-sm font-semibold py-2 px-4 rounded-lg bg-gray-900 text-white hover:bg-gray-800 dark:bg-primary-600 dark:hover:bg-primary-500 transition-colors shadow-sm">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div class="mt-2 w-full text-center py-2 text-sm text-emerald-600 dark:text-emerald-400 font-medium bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
                    Tüm yazılar optimize 🎉
                </div>
            @endif
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
</x-filament-panels::page>
