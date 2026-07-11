<x-filament-panels::page>

    {{-- SEO SAĞLIK DASHBOARD --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        @php
            $productPercent = $this->totalProducts > 0 ? round((($this->totalProducts - $this->productsWithoutMeta) / $this->totalProducts) * 100) : 0;
            $catPercent = $this->totalCategories > 0 ? round((($this->totalCategories - $this->categoriesWithoutMeta) / $this->totalCategories) * 100) : 0;
            $pagePercent = $this->totalPages > 0 ? round((($this->totalPages - $this->pagesWithoutMeta) / $this->totalPages) * 100) : 0;
            $blogPercent = $this->totalBlogPosts > 0 ? round((($this->totalBlogPosts - $this->blogPostsWithoutMeta) / $this->totalBlogPosts) * 100) : 0;
        @endphp

        {{-- Ürünler --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">🛍️</span>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200" style="margin:0;">Ürünler</h3>
                    </div>
                    @if($this->productsWithoutMeta === 0 && $this->totalProducts > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d1fae5; color: #065f46;">
                            ✓ Kusursuz
                        </span>
                    @elseif($this->productsWithoutMeta > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #ffe4e6; color: #9f1239;">
                            ⚠️ {{ $this->productsWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                        <span style="color: #6b7280;">SEO Uyumu</span>
                        <span style="font-weight: bold;">%{{ $productPercent }}</span>
                    </div>
                    <div style="width: 100%; background: #f3f4f6; border-radius: 999px; height: 8px; overflow: hidden;">
                        <div style="height: 100%; border-radius: 999px; transition: width 0.5s; background: {{ $productPercent == 100 ? '#10b981' : '#3b82f6' }}; width: {{ $productPercent }}%;"></div>
                    </div>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 10px; line-height: 1.5;">
                        Toplam <strong>{{ $this->totalProducts }}</strong> üründen <strong>{{ $this->totalProducts - $this->productsWithoutMeta }}</strong> tanesinin SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->productsWithoutMeta > 0)
                <button wire:click="regenerateAllProductSeo" wire:confirm="{{ $this->productsWithoutMeta }} ürünün SEO başlık ve açıklamaları yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        style="margin-top: 10px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; background: #111827; color: white; cursor: pointer; border: none;">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div style="margin-top: 10px; width: 100%; text-align: center; padding: 8px; font-size: 13px; font-weight: 500; color: #059669; background: #ecfdf5; border-radius: 8px;">
                    Tüm ürünler optimize 🎉
                </div>
            @endif
        </div>

        {{-- Kategoriler --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">📁</span>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200" style="margin:0;">Kategoriler</h3>
                    </div>
                    @if($this->categoriesWithoutMeta === 0 && $this->totalCategories > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d1fae5; color: #065f46;">
                            ✓ Kusursuz
                        </span>
                    @elseif($this->categoriesWithoutMeta > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #ffe4e6; color: #9f1239;">
                            ⚠️ {{ $this->categoriesWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                        <span style="color: #6b7280;">SEO Uyumu</span>
                        <span style="font-weight: bold;">%{{ $catPercent }}</span>
                    </div>
                    <div style="width: 100%; background: #f3f4f6; border-radius: 999px; height: 8px; overflow: hidden;">
                        <div style="height: 100%; border-radius: 999px; transition: width 0.5s; background: {{ $catPercent == 100 ? '#10b981' : '#6366f1' }}; width: {{ $catPercent }}%;"></div>
                    </div>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 10px; line-height: 1.5;">
                        Toplam <strong>{{ $this->totalCategories }}</strong> kategoriden <strong>{{ $this->totalCategories - $this->categoriesWithoutMeta }}</strong> tanesinin SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->categoriesWithoutMeta > 0)
                <button wire:click="regenerateAllCategorySeo" wire:confirm="{{ $this->categoriesWithoutMeta }} kategorinin SEO verileri yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        style="margin-top: 10px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; background: #111827; color: white; cursor: pointer; border: none;">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div style="margin-top: 10px; width: 100%; text-align: center; padding: 8px; font-size: 13px; font-weight: 500; color: #059669; background: #ecfdf5; border-radius: 8px;">
                    Tüm kategoriler optimize 🎉
                </div>
            @endif
        </div>

        {{-- Sayfalar --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">📄</span>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200" style="margin:0;">Sayfalar</h3>
                    </div>
                    @if($this->pagesWithoutMeta === 0 && $this->totalPages > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d1fae5; color: #065f46;">
                            ✓ Kusursuz
                        </span>
                    @elseif($this->pagesWithoutMeta > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #ffe4e6; color: #9f1239;">
                            ⚠️ {{ $this->pagesWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                        <span style="color: #6b7280;">SEO Uyumu</span>
                        <span style="font-weight: bold;">%{{ $pagePercent }}</span>
                    </div>
                    <div style="width: 100%; background: #f3f4f6; border-radius: 999px; height: 8px; overflow: hidden;">
                        <div style="height: 100%; border-radius: 999px; transition: width 0.5s; background: {{ $pagePercent == 100 ? '#10b981' : '#a855f7' }}; width: {{ $pagePercent }}%;"></div>
                    </div>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 10px; line-height: 1.5;">
                        Toplam <strong>{{ $this->totalPages }}</strong> sayfadan <strong>{{ $this->totalPages - $this->pagesWithoutMeta }}</strong> tanesinin SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->pagesWithoutMeta > 0)
                <button wire:click="regenerateAllPageSeo" wire:confirm="{{ $this->pagesWithoutMeta }} sayfanın SEO verileri yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        style="margin-top: 10px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; background: #111827; color: white; cursor: pointer; border: none;">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div style="margin-top: 10px; width: 100%; text-align: center; padding: 8px; font-size: 13px; font-weight: 500; color: #059669; background: #ecfdf5; border-radius: 8px;">
                    Tüm sayfalar optimize 🎉
                </div>
            @endif
        </div>

        {{-- Blog --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-5 flex flex-col justify-between">
            <div style="margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 20px;">📝</span>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-200" style="margin:0;">Blog</h3>
                    </div>
                    @if($this->blogPostsWithoutMeta === 0 && $this->totalBlogPosts > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #d1fae5; color: #065f46;">
                            ✓ Kusursuz
                        </span>
                    @elseif($this->blogPostsWithoutMeta > 0)
                        <span style="display:inline-flex; align-items:center; gap:4px; padding: 4px 8px; border-radius: 6px; font-size: 12px; font-weight: bold; background: #ffe4e6; color: #9f1239;">
                            ⚠️ {{ $this->blogPostsWithoutMeta }} Eksik
                        </span>
                    @endif
                </div>
                
                <div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 6px;">
                        <span style="color: #6b7280;">SEO Uyumu</span>
                        <span style="font-weight: bold;">%{{ $blogPercent }}</span>
                    </div>
                    <div style="width: 100%; background: #f3f4f6; border-radius: 999px; height: 8px; overflow: hidden;">
                        <div style="height: 100%; border-radius: 999px; transition: width 0.5s; background: {{ $blogPercent == 100 ? '#10b981' : '#ec4899' }}; width: {{ $blogPercent }}%;"></div>
                    </div>
                    <p style="font-size: 12px; color: #6b7280; margin-top: 10px; line-height: 1.5;">
                        Toplam <strong>{{ $this->totalBlogPosts }}</strong> blog yazısından <strong>{{ $this->totalBlogPosts - $this->blogPostsWithoutMeta }}</strong> tanesinin SEO ayarları tam.
                    </p>
                </div>
            </div>

            @if($this->blogPostsWithoutMeta > 0)
                <button wire:click="regenerateAllBlogSeo" wire:confirm="{{ $this->blogPostsWithoutMeta }} blog yazısının SEO verileri yapay zeka ile üretilecek. Onaylıyor musunuz?"
                        style="margin-top: 10px; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 8px; background: #111827; color: white; cursor: pointer; border: none;">
                    🤖 Yapay Zeka ile Doldur
                </button>
            @else
                <div style="margin-top: 10px; width: 100%; text-align: center; padding: 8px; font-size: 13px; font-weight: 500; color: #059669; background: #ecfdf5; border-radius: 8px;">
                    Tüm blog yazıları optimize 🎉
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
