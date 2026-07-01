<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6">
        
        <div class="bg-gray-900 dark:bg-black rounded-3xl p-8 text-white shadow-xl flex flex-col md:flex-row items-center justify-between border border-gray-800">
            <div>
                <h2 class="text-3xl font-black tracking-tight mb-2 flex items-center gap-2">
                    @svg('heroicon-s-sparkles', 'w-8 h-8 text-yellow-400')
                    Phoenix AI
                </h2>
                <p class="text-gray-400 max-w-xl text-sm leading-relaxed">
                    Yapay zeka asistanınız mağazanızı 7/24 izler; satışları artırmak, stok sorunlarını önlemek ve müşteri kaybını engellemek için proaktif öneriler sunar.
                </p>
            </div>
            <div class="mt-6 md:mt-0 text-center md:text-right">
                <div class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">
                    {{ $recommendations->count() }}
                </div>
                <div class="text-xs font-bold tracking-widest text-gray-500 uppercase mt-1">Aktif Öneri</div>
            </div>
        </div>

        @if($recommendations->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-2xl p-12 text-center border border-gray-200 dark:border-gray-800 shadow-sm">
                @svg('heroicon-o-check-badge', 'w-16 h-16 mx-auto text-emerald-500 mb-4')
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Harika iş! Yapılacak hiçbir şey yok.</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-2">Mağazanız şu an mükemmel durumda. Tüm önerileri tamamladınız veya şu an için yeni bir analiz sonucu yok.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($recommendations as $rec)
                    @php
                        $color = match($rec->priority) {
                            'critical' => 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-500/10 border-rose-200 dark:border-rose-500/20',
                            'high' => 'text-orange-600 dark:text-orange-400 bg-orange-50 dark:bg-orange-500/10 border-orange-200 dark:border-orange-500/20',
                            'medium' => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-500/10 border-amber-200 dark:border-amber-500/20',
                            default => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 border-blue-200 dark:border-blue-500/20',
                        };
                        
                        $icon = match($rec->type) {
                            'stock_alert' => 'heroicon-o-cube',
                            'customer_retention' => 'heroicon-o-user-minus',
                            'vip_at_risk' => 'heroicon-o-star',
                            'revenue_drop' => 'heroicon-o-arrow-trending-down',
                            'abandoned_carts' => 'heroicon-o-shopping-cart',
                            'vip_opportunity' => 'heroicon-o-sparkles',
                            default => 'heroicon-o-bell',
                        };
                    @endphp

                    <div class="flex flex-col bg-white dark:bg-gray-900 rounded-2xl p-6 border shadow-sm relative overflow-hidden group {{ $color }}">
                        
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-2.5 rounded-xl bg-white dark:bg-gray-800 shadow-sm">
                                @svg($icon, 'w-6 h-6')
                            </div>
                            <span class="text-[10px] font-black tracking-widest uppercase px-2 py-1 rounded-full bg-white/50 dark:bg-black/50">
                                {{ strtoupper($rec->priority) }}
                            </span>
                        </div>

                        <h3 class="font-bold text-gray-900 dark:text-white text-lg mb-2 leading-tight">{{ $rec->title }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm flex-1 leading-relaxed">{{ $rec->description }}</p>

                        <div class="mt-6 flex items-center justify-between gap-3 pt-4 border-t border-gray-100 dark:border-gray-800/50">
                            <button wire:click="completeRecommendation({{ $rec->id }})" class="flex-1 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-gray-200 dark:text-black text-white text-xs font-bold uppercase tracking-wider py-2.5 px-4 rounded-xl transition-colors text-center">
                                Tamamla
                            </button>
                            <button wire:click="dismissRecommendation({{ $rec->id }})" class="p-2.5 text-gray-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-colors" title="Yoksay">
                                @svg('heroicon-o-x-mark', 'w-5 h-5')
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
