<x-filament-panels::page>

    {{-- SEO SAĞLIK DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        @php
            $productPercent = $this->totalProducts > 0 ? round((($this->totalProducts - $this->productsWithoutMeta) / $this->totalProducts) * 100) : 0;
            $catPercent = $this->totalCategories > 0 ? round((($this->totalCategories - $this->categoriesWithoutMeta) / $this->totalCategories) * 100) : 0;
            $pagePercent = $this->totalPages > 0 ? round((($this->totalPages - $this->pagesWithoutMeta) / $this->totalPages) * 100) : 0;
            $blogPercent = $this->totalBlogPosts > 0 ? round((($this->totalBlogPosts - $this->blogPostsWithoutMeta) / $this->totalBlogPosts) * 100) : 0;
        @endphp

        {{-- Ürünler --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display:flex; align-items:center; gap:8px;">
                    <x-filament::icon icon="heroicon-o-shopping-bag" style="width: 24px; height: 24px;" class="text-primary-500" />
                    <span>Ürünler</span>
                </div>
            </x-slot>
            <x-slot name="headerEnd">
                @if($this->productsWithoutMeta === 0 && $this->totalProducts > 0)
                    <x-filament::badge color="success">Kusursuz</x-filament::badge>
                @elseif($this->productsWithoutMeta > 0)
                    <x-filament::badge color="warning">{{ $this->productsWithoutMeta }} Eksik</x-filament::badge>
                @endif
            </x-slot>

            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 8px; font-size: 14px;">
                    <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                    <strong class="text-primary-600 dark:text-primary-400">%{{ $productPercent }}</strong>
                </div>
                <div style="width: 100%; height: 8px; border-radius: 9999px; overflow: hidden; background-color: rgba(156, 163, 175, 0.2);">
                    <div style="height: 100%; width: {{ $productPercent }}%; background-color: {{ $productPercent == 100 ? '#10b981' : 'var(--primary-500)' }}; transition: width 0.5s;"></div>
                </div>
                <div style="margin-top: 16px; font-size: 13px; line-height: 1.5;" class="text-gray-600 dark:text-gray-400">
                    Toplam <strong>{{ $this->totalProducts }}</strong> üründen <strong>{{ $this->totalProducts - $this->productsWithoutMeta }}</strong> tanesinde SEO ayarları tamamlandı.
                </div>
            </div>

            @if($this->productsWithoutMeta > 0)
                <div style="margin-top: 24px;">
                    <x-filament::button wire:click="regenerateAllProductSeo" wire:confirm="{{ $this->productsWithoutMeta }} ürünün SEO verileri yapay zeka ile doldurulacak. Onaylıyor musunuz?" icon="heroicon-m-sparkles" style="width: 100%; justify-content: center;">
                        AI ile Eksikleri Doldur
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>

        {{-- Kategoriler --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display:flex; align-items:center; gap:8px;">
                    <x-filament::icon icon="heroicon-o-rectangle-group" style="width: 24px; height: 24px;" class="text-indigo-500" />
                    <span>Kategoriler</span>
                </div>
            </x-slot>
            <x-slot name="headerEnd">
                @if($this->categoriesWithoutMeta === 0 && $this->totalCategories > 0)
                    <x-filament::badge color="success">Kusursuz</x-filament::badge>
                @elseif($this->categoriesWithoutMeta > 0)
                    <x-filament::badge color="warning">{{ $this->categoriesWithoutMeta }} Eksik</x-filament::badge>
                @endif
            </x-slot>

            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 8px; font-size: 14px;">
                    <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                    <strong class="text-indigo-600 dark:text-indigo-400">%{{ $catPercent }}</strong>
                </div>
                <div style="width: 100%; height: 8px; border-radius: 9999px; overflow: hidden; background-color: rgba(156, 163, 175, 0.2);">
                    <div style="height: 100%; width: {{ $catPercent }}%; background-color: {{ $catPercent == 100 ? '#10b981' : '#6366f1' }}; transition: width 0.5s;"></div>
                </div>
                <div style="margin-top: 16px; font-size: 13px; line-height: 1.5;" class="text-gray-600 dark:text-gray-400">
                    Toplam <strong>{{ $this->totalCategories }}</strong> kategoriden <strong>{{ $this->totalCategories - $this->categoriesWithoutMeta }}</strong> tanesinde SEO ayarları tamamlandı.
                </div>
            </div>

            @if($this->categoriesWithoutMeta > 0)
                <div style="margin-top: 24px;">
                    <x-filament::button color="gray" wire:click="regenerateAllCategorySeo" wire:confirm="{{ $this->categoriesWithoutMeta }} kategorinin SEO verileri yapay zeka ile doldurulacak. Onaylıyor musunuz?" icon="heroicon-m-sparkles" style="width: 100%; justify-content: center;">
                        AI ile Eksikleri Doldur
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>

        {{-- Sayfalar --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display:flex; align-items:center; gap:8px;">
                    <x-filament::icon icon="heroicon-o-document-text" style="width: 24px; height: 24px;" class="text-purple-500" />
                    <span>Sayfalar</span>
                </div>
            </x-slot>
            <x-slot name="headerEnd">
                @if($this->pagesWithoutMeta === 0 && $this->totalPages > 0)
                    <x-filament::badge color="success">Kusursuz</x-filament::badge>
                @elseif($this->pagesWithoutMeta > 0)
                    <x-filament::badge color="warning">{{ $this->pagesWithoutMeta }} Eksik</x-filament::badge>
                @endif
            </x-slot>

            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 8px; font-size: 14px;">
                    <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                    <strong class="text-purple-600 dark:text-purple-400">%{{ $pagePercent }}</strong>
                </div>
                <div style="width: 100%; height: 8px; border-radius: 9999px; overflow: hidden; background-color: rgba(156, 163, 175, 0.2);">
                    <div style="height: 100%; width: {{ $pagePercent }}%; background-color: {{ $pagePercent == 100 ? '#10b981' : '#a855f7' }}; transition: width 0.5s;"></div>
                </div>
                <div style="margin-top: 16px; font-size: 13px; line-height: 1.5;" class="text-gray-600 dark:text-gray-400">
                    Toplam <strong>{{ $this->totalPages }}</strong> sayfadan <strong>{{ $this->totalPages - $this->pagesWithoutMeta }}</strong> tanesinde SEO ayarları tamamlandı.
                </div>
            </div>

            @if($this->pagesWithoutMeta > 0)
                <div style="margin-top: 24px;">
                    <x-filament::button color="gray" wire:click="regenerateAllPageSeo" wire:confirm="{{ $this->pagesWithoutMeta }} sayfanın SEO verileri yapay zeka ile doldurulacak. Onaylıyor musunuz?" icon="heroicon-m-sparkles" style="width: 100%; justify-content: center;">
                        AI ile Eksikleri Doldur
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>

        {{-- Blog --}}
        <x-filament::section>
            <x-slot name="heading">
                <div style="display:flex; align-items:center; gap:8px;">
                    <x-filament::icon icon="heroicon-o-pencil-square" style="width: 24px; height: 24px;" class="text-pink-500" />
                    <span>Blog Yazıları</span>
                </div>
            </x-slot>
            <x-slot name="headerEnd">
                @if($this->blogPostsWithoutMeta === 0 && $this->totalBlogPosts > 0)
                    <x-filament::badge color="success">Kusursuz</x-filament::badge>
                @elseif($this->blogPostsWithoutMeta > 0)
                    <x-filament::badge color="warning">{{ $this->blogPostsWithoutMeta }} Eksik</x-filament::badge>
                @endif
            </x-slot>

            <div>
                <div style="display:flex; justify-content:space-between; margin-bottom: 8px; font-size: 14px;">
                    <span class="text-gray-500 dark:text-gray-400">SEO Uyumu</span>
                    <strong class="text-pink-600 dark:text-pink-400">%{{ $blogPercent }}</strong>
                </div>
                <div style="width: 100%; height: 8px; border-radius: 9999px; overflow: hidden; background-color: rgba(156, 163, 175, 0.2);">
                    <div style="height: 100%; width: {{ $blogPercent }}%; background-color: {{ $blogPercent == 100 ? '#10b981' : '#ec4899' }}; transition: width 0.5s;"></div>
                </div>
                <div style="margin-top: 16px; font-size: 13px; line-height: 1.5;" class="text-gray-600 dark:text-gray-400">
                    Toplam <strong>{{ $this->totalBlogPosts }}</strong> blog yazısından <strong>{{ $this->totalBlogPosts - $this->blogPostsWithoutMeta }}</strong> tanesinde SEO ayarları tamamlandı.
                </div>
            </div>

            @if($this->blogPostsWithoutMeta > 0)
                <div style="margin-top: 24px;">
                    <x-filament::button color="gray" wire:click="regenerateAllBlogSeo" wire:confirm="{{ $this->blogPostsWithoutMeta }} blog yazısının SEO verileri yapay zeka ile doldurulacak. Onaylıyor musunuz?" icon="heroicon-m-sparkles" style="width: 100%; justify-content: center;">
                        AI ile Eksikleri Doldur
                    </x-filament::button>
                </div>
            @endif
        </x-filament::section>
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
