@php
    $baseUrl = rtrim(config('app.url', 'https://patenliayakkabilar.com'), '/');
    $productUrl = $baseUrl . '/urun/' . $record->slug;
    $storyLink = $productUrl . '?utm_source=instagram&utm_medium=story&utm_campaign=' . $record->slug;
    $reelsLink = $productUrl . '?utm_source=instagram&utm_medium=reels&utm_campaign=' . $record->slug;
    $adsLink = $productUrl . '?utm_source=instagram&utm_medium=cpc&utm_campaign=' . $record->slug;
    $waLink = $productUrl . '?utm_source=whatsapp&utm_medium=chat';
@endphp

<div 
    x-data="{
        copiedIdx: null,
        copy(text, idx) {
            navigator.clipboard.writeText(text);
            this.copiedIdx = idx;
            setTimeout(() => this.copiedIdx = null, 2000);
        }
    }" 
    class="space-y-4 text-left p-1"
>
    <div class="p-3 rounded-lg bg-pink-50 dark:bg-pink-950/20 border border-pink-200 dark:border-pink-900/30 text-xs text-pink-900 dark:text-pink-200">
        <strong>{{ $record->name }}</strong> ürünü için hazırlanmış Instagram ve pazarlama takip linkleri. Paylaştığınızda gelen siparişlerin kaynağı otomatik olarak bu ürünle ilişkilendirilecektir.
    </div>

    <!-- 1. Instagram Story -->
    <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
        <div class="flex items-center justify-between mb-1.5">
            <span class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5">
                <span>📸</span> Instagram Hikaye (Story) Linki
            </span>
            <button 
                type="button" 
                @click="copy('{{ $storyLink }}', 1)"
                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-pink-600 hover:bg-pink-700 text-white transition-all cursor-pointer flex items-center gap-1"
            >
                <span x-text="copiedIdx === 1 ? '✓ Kopyalandı!' : 'Kopyala'"></span>
            </button>
        </div>
        <div class="font-mono text-[11px] text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 select-all break-all">
            {{ $storyLink }}
        </div>
    </div>

    <!-- 2. Instagram Reels -->
    <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
        <div class="flex items-center justify-between mb-1.5">
            <span class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5">
                <span>🎬</span> Instagram Reels Linki
            </span>
            <button 
                type="button" 
                @click="copy('{{ $reelsLink }}', 2)"
                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-pink-600 hover:bg-pink-700 text-white transition-all cursor-pointer flex items-center gap-1"
            >
                <span x-text="copiedIdx === 2 ? '✓ Kopyalandı!' : 'Kopyala'"></span>
            </button>
        </div>
        <div class="font-mono text-[11px] text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 select-all break-all">
            {{ $reelsLink }}
        </div>
    </div>

    <!-- 3. Instagram Reklamı (Meta Ads) -->
    <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
        <div class="flex items-center justify-between mb-1.5">
            <span class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5">
                <span>🎯</span> Meta (Instagram) Reklam Linki
            </span>
            <button 
                type="button" 
                @click="copy('{{ $adsLink }}', 3)"
                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-purple-600 hover:bg-purple-700 text-white transition-all cursor-pointer flex items-center gap-1"
            >
                <span x-text="copiedIdx === 3 ? '✓ Kopyalandı!' : 'Kopyala'"></span>
            </button>
        </div>
        <div class="font-mono text-[11px] text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 select-all break-all">
            {{ $adsLink }}
        </div>
    </div>

    <!-- 4. WhatsApp -->
    <div class="p-3 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-800/40">
        <div class="flex items-center justify-between mb-1.5">
            <span class="font-bold text-xs text-gray-900 dark:text-white flex items-center gap-1.5">
                <span>💬</span> WhatsApp Paylaşım Linki
            </span>
            <button 
                type="button" 
                @click="copy('{{ $waLink }}', 4)"
                class="px-2.5 py-1 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition-all cursor-pointer flex items-center gap-1"
            >
                <span x-text="copiedIdx === 4 ? '✓ Kopyalandı!' : 'Kopyala'"></span>
            </button>
        </div>
        <div class="font-mono text-[11px] text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-950 p-2 rounded border border-gray-200 dark:border-gray-800 select-all break-all">
            {{ $waLink }}
        </div>
    </div>
</div>
