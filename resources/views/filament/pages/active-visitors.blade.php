<x-filament-panels::page>
    <div wire:poll.5s class="space-y-6">
        {{-- Üst KPI Göstergeleri --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
            {{-- 1. Canlı Ziyaretçi --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Şu An Sitede</span>
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                    </span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $onlineCount }}</span>
                    <span class="text-xs text-emerald-600 font-semibold">Canlı</span>
                </div>
                <div class="text-[11px] text-gray-400 mt-1">Son 45 sn aktif</div>
            </div>

            {{-- 2. Sıcak Adaylar (Yüksek Niyet) --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Sıcak Adaylar</span>
                    <span class="text-lg">🔥</span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-orange-600 dark:text-orange-400">{{ $highIntentCount }}</span>
                    <span class="text-xs text-gray-500">Kişi</span>
                </div>
                <div class="text-[11px] text-gray-400 mt-1">Satın alma niyeti %60+</div>
            </div>

            {{-- 3. Sepetinde Ürün Olanlar --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Canlı Sepetler</span>
                    <span class="text-lg">🛒</span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">{{ $cartCount }}</span>
                    <span class="text-xs text-gray-500">Kişi</span>
                </div>
                <div class="text-[11px] text-emerald-600 font-bold mt-1">
                    {{ number_format($cartTotal, 2) }} ₺ potansiyel
                </div>
            </div>

            {{-- 4. Tereddütte Olanlar --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Tereddütte</span>
                    <span class="text-lg">🤔</span>
                </div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-amber-500">{{ $hesitatingCount }}</span>
                    <span class="text-xs text-gray-500">Kişi</span>
                </div>
                <div class="text-[11px] text-gray-400 mt-1">Beden veya ödeme bariyeri</div>
            </div>

            {{-- 5. Üye / Misafir --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-4 shadow-sm col-span-2 sm:col-span-1">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kullanıcı Tipi</span>
                    <span class="text-lg">👤</span>
                </div>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $membersCount }}</span>
                    <span class="text-xs text-gray-400">Üye / </span>
                    <span class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $guestsCount }}</span>
                    <span class="text-xs text-gray-400">Misafir</span>
                </div>
                <div class="text-[11px] text-gray-400 mt-1">Giriş oranı %{{ $onlineCount > 0 ? round(($membersCount / $onlineCount) * 100) : 0 }}</div>
            </div>
        </div>

        {{-- Canlı İstihbarat Açıklama & Durum Çubuğu --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-3 bg-gradient-to-r from-orange-50 to-amber-50 dark:from-orange-950/20 dark:to-amber-950/20 border border-orange-200/60 dark:border-orange-800/40 rounded-xl text-xs">
            <div class="flex items-center gap-2 text-orange-900 dark:text-orange-200 font-medium">
                <span class="text-base">📡</span>
                <span><strong>Canlı Satış İstihbarat Akışı:</strong> Ziyaretçilerin adımları otomatik yorumlanır; tereddüt algılandığında hazır stratejilerle tek tıkla satışa çevirebilirsiniz.</span>
            </div>
            <div class="flex items-center gap-1.5 text-gray-500 font-mono text-[11px]">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Canlı Yenileniyor (5 sn)</span>
            </div>
        </div>

        {{-- Filament Tablosu --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
