<x-filament-panels::page>

    {{-- SEO SAĞLIK DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Ürünler --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
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
                        class="mt-3 w-full text-xs font-semibold text-center py-1.5 px-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors">
                    🤖 {{ $this->productsWithoutMeta }} Ürün İçin SEO Üret
                </button>
            @endif
        </div>

        {{-- Kategoriler --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
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
                        class="mt-3 w-full text-xs font-semibold text-center py-1.5 px-3 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-colors">
                    🤖 {{ $this->categoriesWithoutMeta }} Kategori İçin SEO Üret
                </button>
            @endif
        </div>

        {{-- Sayfalar --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
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

        <div class="mt-8 pt-8" style="padding-top: 3rem; margin-top: 2rem; display: block; clear: both;">
            <x-filament::button type="submit" size="lg" class="mt-8">
                💾 Ayarları Kaydet
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
