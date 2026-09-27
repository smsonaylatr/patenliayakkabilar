<div>
    {{-- Sütun Başlıkları Çubuğu (Geniş Ekranda) --}}
    <div class="hidden lg:grid grid-cols-12 gap-4 px-5 py-3 text-xs font-extrabold text-slate-400 uppercase tracking-wider bg-slate-900/80 rounded-xl border border-slate-800/90 mb-3 shadow-sm select-none">
        <div class="col-span-3">ZİYARETÇİ & SİNYAL</div>
        <div class="col-span-3">BULUNDUĞU SAYFA & MODEL</div>
        <div class="col-span-4">DAVRANIŞ TEŞHİSİ & NİYET</div>
        <div class="col-span-2">SEPET</div>
    </div>

    {{-- Ziyaretçi Kayıtları Listesi --}}
    <div class="space-y-3.5">
        @forelse ($records as $record)
            @php
                // 1. Ziyaretçi Verileri
                $isOnline = $record->is_currently_online;
                $diff = $record->last_heartbeat_at ? $record->last_heartbeat_at->diffForHumans(null, true) : 'şimdi';
                $deviceIcon = match ($record->device_type) {
                    'mobile' => '📱',
                    'tablet' => '📟',
                    default => '💻',
                };
                $name = $record->user_id && $record->user ? $record->user->name : $record->display_name;
                $isMember = ($record->user_id && $record->user) || $record->is_identified;
                $hasCustomName = !empty($record->guest_name);
                $initial = mb_substr($name, 0, 1);
                $duration = $record->duration_formatted;
                $pageCount = $record->page_views_count ?: 1;
                $phone = $record->user?->phone ?? $record->guest_phone;
                $email = $record->user?->email ?? $record->guest_email;

                // 2. Bulunduğu Sayfa & Model Verileri
                $pageInfo = $record->page_info ?? [];
                $product = $record->current_product;
                $productImage = $record->current_product_image;
                $title = $pageInfo['title'] ?? ($product ? $product->name : ($record->current_title ?: $record->current_path));
                $url = $record->current_url ?: $record->current_path;
                $isCheckout = ($pageInfo['type'] ?? '') === 'checkout' || str_contains($record->current_path, 'checkout');

                $recentDetail = null;
                if (!empty($record->journey_trail)) {
                    foreach (array_reverse($record->journey_trail) as $step) {
                        if (!empty($step['detail'])) {
                            $recentDetail = $step['detail'];
                            break;
                        }
                    }
                }

                // 3. Davranış Teşhisi Verileri
                $score = $record->intent_score ?? 15;
                $insight = $record->behavior_insight ?? 'Sitede genel keşif yapıyor.';
                $color = match (true) {
                    $score >= 80 => '#10b981',
                    $score >= 60 => '#f59e0b',
                    $score >= 40 => '#38bdf8',
                    default => '#94a3b8',
                };
                $gradient = match (true) {
                    $score >= 80 => 'linear-gradient(90deg, #10b981, #059669)',
                    $score >= 60 => 'linear-gradient(90deg, #f59e0b, #d97706)',
                    $score >= 40 => 'linear-gradient(90deg, #38bdf8, #0284c7)',
                    default => 'linear-gradient(90deg, #64748b, #475569)',
                };
                $intentLabel = match (true) {
                    $score >= 80 => '🔥 ÇOK SICAK (%' . $score . ')',
                    $score >= 60 => '⚡ TEREDDÜTTE (%' . $score . ')',
                    $score >= 40 => '👀 İLGİLİ (%' . $score . ')',
                    default => '🔍 KEŞİF (%' . $score . ')',
                };

                // 4. Strateji
                $strategy = $record->recommended_strategy;
                if (is_string($strategy)) {
                    $strategy = json_decode($strategy, true);
                }

                // 5. Kayıt Aksiyonları (Hızlı Butonlar)
                $activeTable = $table ?? (isset($this) && method_exists($this, 'getTable') ? $this->getTable() : null);
                $defaultRecordActions = $activeTable ? $activeTable->getRecordActions() : [];
                $recordActions = array_reduce(
                    $defaultRecordActions,
                    function (array $carry, $action) use ($record): array {
                        $action = $action->getClone();
                        if (! $action instanceof \Filament\Actions\BulkAction) {
                            $action->record($record);
                        }
                        if ($action->isHidden()) {
                            return $carry;
                        }
                        $carry[] = $action;
                        return $carry;
                    },
                    initial: [],
                );
            @endphp

            <div wire:key="visitor-log-{{ $record->id }}" class="visitor-log-card bg-slate-900/80 hover:bg-slate-900 border border-slate-800 hover:border-slate-700/80 rounded-2xl overflow-hidden shadow-md transition-all duration-200">
                {{-- ÜST KISIM: 4 Sütunlu Canlı Bilgiler --}}
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-center p-4 lg:p-5">
                    
                    {{-- 1. Ziyaretçi & Sinyal (3 Sütun) --}}
                    <div class="lg:col-span-3">
                        <div class="flex items-start gap-3">
                            <div class="relative w-10 h-10 rounded-full flex items-center justify-center font-extrabold text-white text-sm shrink-0 shadow-md" style="background: linear-gradient(135deg, #ff4e00, #b45309); box-shadow: 0 0 12px rgba(255, 78, 0, 0.35);">
                                {{ $initial }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                    <span class="font-extrabold text-slate-100 text-sm truncate">{{ $name }}</span>
                                    @if ($isMember)
                                        <span class="bg-emerald-500/25 text-emerald-400 border border-emerald-500/40 px-1.5 py-0.5 rounded text-[9px] font-extrabold">MÜŞTERİ</span>
                                    @elseif ($hasCustomName)
                                        <span class="bg-sky-500/20 text-sky-400 border border-sky-500/40 px-1.5 py-0.5 rounded text-[9px] font-extrabold">MİSAFİR</span>
                                    @endif
                                </div>
                                @if($phone || $email)
                                    <div class="text-[11px] text-sky-400 font-bold flex items-center gap-1.5 mb-1 flex-wrap">
                                        @if($phone) <span>📱 {{ $phone }}</span> @endif
                                        @if($phone && $email) <span>•</span> @endif
                                        @if($email) <span class="text-slate-400">✉️ {{ $email }}</span> @endif
                                    </div>
                                @endif
                                <div class="mb-1.5">
                                    @if ($isOnline)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-500/15 text-emerald-400 border border-emerald-500/40">
                                            <span class="live-radar-dot" style="width:7px; height:7px;"></span> CANLI
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                            AYRILDI ({{ $diff }})
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[11px] text-slate-400 flex items-center gap-1.5 flex-wrap">
                                    <span>{{ $deviceIcon }} {{ $record->browser ?? 'Tarayıcı' }}</span>
                                    <span>•</span>
                                    <span class="font-mono text-slate-500">{{ $record->ip_address }}</span>
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    ⏱️ {{ $duration }} ({{ $pageCount }}. sayfa)
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Bulunduğu Sayfa & Model (3 Sütun) --}}
                    <div class="lg:col-span-3">
                        @if ($product)
                            @php $price = $product->discount_price ?: $product->price; @endphp
                            <div class="flex items-center gap-3">
                                @if ($productImage)
                                    <img src="{{ $productImage }}" alt="{{ $title }}" class="w-12 h-12 rounded-xl object-cover border border-white/15 shrink-0 shadow-sm" />
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-slate-800 flex items-center justify-center text-xl shrink-0 border border-white/10">👟</div>
                                @endif
                                <div class="overflow-hidden min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wide bg-orange-500/15 text-orange-400 border border-orange-500/35">
                                            👟 {{ $pageInfo['badge'] ?? 'Model' }}
                                        </span>
                                    </div>
                                    <a href="{{ $url }}" target="_blank" class="font-extrabold text-slate-100 hover:text-orange-400 text-[12.5px] leading-snug block truncate transition-colors">
                                        {{ $title }} ↗
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="font-extrabold text-emerald-400 text-xs">{{ number_format($price, 2) }} ₺</span>
                                        <span class="text-[10px] text-slate-400 bg-white/5 border border-white/10 px-1.5 py-0.5 rounded">İnceliyor</span>
                                    </div>
                                    @if ($recentDetail)
                                        <div class="text-[11px] text-amber-300 font-bold mt-1 truncate">
                                            🎯 {{ $recentDetail }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @elseif ($isCheckout)
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-xl shrink-0 shadow-sm">
                                    🛒
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wide bg-emerald-500/15 text-emerald-400 border border-emerald-500/35">
                                            🛒 {{ $pageInfo['badge'] ?? 'Ödeme Ekranı' }}
                                        </span>
                                    </div>
                                    <a href="{{ $url }}" target="_blank" class="font-extrabold text-sky-400 hover:text-sky-300 text-[12.5px] block truncate transition-colors">
                                        {{ $title }} ↗
                                    </a>
                                    <div class="text-[11px] text-emerald-400 font-bold mt-1">
                                        Sepet Tutarı: {{ number_format($record->cart_total, 2) }} ₺ {{ $record->cart_items_count > 0 ? "({$record->cart_items_count} ürün)" : '' }}
                                    </div>
                                    @if ($recentDetail)
                                        <div class="text-[11px] text-amber-300 font-bold mt-1 truncate">
                                            🎯 {{ $recentDetail }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            @php
                                $badgeColor = $pageInfo['color'] ?? '#94a3b8';
                                $badgeBg = $pageInfo['bg_color'] ?? 'rgba(255, 255, 255, 0.05)';
                                $badgeBorder = $pageInfo['border_color'] ?? 'rgba(255, 255, 255, 0.1)';
                                $badgeIcon = $pageInfo['icon'] ?? '📄';
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl shrink-0 shadow-sm" style="background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }};">
                                    {{ $badgeIcon }}
                                </div>
                                <div class="overflow-hidden min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase tracking-wide" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; border: 1px solid {{ $badgeBorder }};">
                                            {{ $badgeIcon }} {{ $pageInfo['badge'] ?? 'Sayfa' }}
                                        </span>
                                    </div>
                                    <a href="{{ $url }}" target="_blank" class="font-extrabold text-slate-100 hover:text-white text-[12.5px] block truncate transition-colors">
                                        {{ $title }} ↗
                                    </a>
                                    <div class="text-[10px] text-slate-500 mt-1 font-mono truncate">
                                        {{ $record->current_path }}
                                    </div>
                                    @if ($recentDetail)
                                        <div class="text-[11px] text-amber-300 font-bold mt-1 truncate">
                                            🎯 {{ $recentDetail }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 3. Davranış Teşhisi & Niyet (4 Sütun) --}}
                    <div class="lg:col-span-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black tracking-wider" style="color: {{ $color }};">
                                    {{ $intentLabel }}
                                </span>
                            </div>
                            <div class="bg-white/10 rounded-full h-1.5 w-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500" style="background: {{ $gradient }}; width: {{ min(100, max(5, $score)) }}%;"></div>
                            </div>
                            <div class="text-[11px] text-slate-200 bg-slate-950/70 border-l-[3px] px-2.5 py-1.5 rounded leading-relaxed" style="border-left-color: {{ $color }};">
                                {{ $insight }}
                            </div>
                        </div>
                    </div>

                    {{-- 4. Sepet (2 Sütun) --}}
                    <div class="lg:col-span-2">
                        @if ($record->cart_items_count > 0)
                            <div>
                                <div class="font-extrabold text-emerald-400 text-sm flex items-center gap-1.5">
                                    🛒 {{ number_format($record->cart_total, 2) }} ₺
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ $record->cart_items_count }} ürün sepette
                                </div>
                            </div>
                        @else
                            <div class="text-xs text-slate-500 font-medium">
                                Sepet Boş
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ALT SATIR: Önerilen Strateji ve Hızlı Butonlar (Satır Olarak Altında) --}}
                <div class="visitor-log-footer bg-slate-950/85 border-t border-slate-800/80 px-4 lg:px-5 py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    
                    {{-- SOL: Önerilen Strateji Bilgisi --}}
                    <div class="flex items-center gap-2 flex-wrap min-w-0">
                        <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 flex items-center gap-1 shrink-0">
                            <span class="text-amber-400">⚡</span> Önerilen Strateji:
                        </span>

                        @if ($strategy)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-extrabold bg-orange-500/15 text-orange-400 border border-orange-500/40 shadow-sm shrink-0">
                                ⚡ {{ $strategy['title'] ?? 'Strateji' }}
                            </span>

                            @if (!empty($strategy['suggested_coupon']))
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 shrink-0">
                                    🎟️ {{ $strategy['suggested_coupon'] }}
                                </span>
                            @endif

                            @if (!empty($strategy['suggested_message']))
                                <span class="text-xs text-slate-300 italic hidden xl:inline truncate max-w-md" title="{{ $strategy['suggested_message'] }}">
                                    "{{ $strategy['suggested_message'] }}"
                                </span>
                            @endif
                        @else
                            <span class="text-xs text-slate-500 italic">
                                Sitede gezinmeye devam ediyor, henüz tetikleyici oluşmadı.
                            </span>
                        @endif
                    </div>

                    {{-- SAĞ: Hızlı Butonlar (Aksiyonlar) --}}
                    <div class="flex items-center gap-2 flex-wrap shrink-0 justify-end">
                        @foreach ($recordActions as $action)
                            {{ $action }}
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="p-10 text-center bg-slate-900/60 border border-slate-800/80 rounded-2xl">
                <div class="text-4xl mb-3">📡</div>
                <div class="text-base font-extrabold text-slate-200">Şu An Sitede Canlı Ziyaretçi Yok</div>
                <div class="text-xs text-slate-400 mt-1 max-w-md mx-auto">
                    Kullanıcılar siteye girdiğinde veya sayfaları gezmeye başladığında canlı radar sinyalleri anlık olarak burada listelenecektir.
                </div>
            </div>
        @endforelse
    </div>
</div>
