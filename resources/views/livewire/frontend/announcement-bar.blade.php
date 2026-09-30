@php
    $barEnabled = \Illuminate\Support\Facades\Cache::remember('announcement_bar_enabled', 3600, function () {
        return \App\Models\Setting::get('announcement_bar_enabled', '1');
    });

    $barItems = \Illuminate\Support\Facades\Cache::remember('announcement_bar_items', 3600, function () {
        $raw = \App\Models\Setting::get('announcement_bar_items');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }
        // Varsayılan
        return [
            ['emoji' => '🚚', 'text' => 'KAPIDA ÖDEME FIRSATI'],
            ['emoji' => '🔄', 'text' => '%100 İADE GARANTİSİ'],
            ['emoji' => '💳', 'text' => 'VADESİZ 3 TAKSİT'],
            ['emoji' => '📦', 'text' => 'HIZLI TESLİMAT'],
            ['emoji' => '⚡', 'text' => '24 SAATTE KARGO'],
        ];
    });
@endphp

@if($barEnabled === '1')
<div class="bg-brand-black text-brand-white text-xs font-medium py-2 flex overflow-hidden">
    <div class="flex whitespace-nowrap marquee-content" style="width: max-content;">
        <!-- First Half -->
        <div class="flex items-center justify-around shrink-0">
            @for ($i = 0; $i < 5; $i++)
                @foreach($barItems as $item)
                    <span class="mx-4">{{ $item['emoji'] ?? '' }} {{ $item['text'] }}</span>
                    <span class="mx-4 text-brand-orange">•</span>
                @endforeach
            @endfor
        </div>
        <!-- Second Half (Exact Mirror) -->
        <div class="flex items-center justify-around shrink-0" aria-hidden="true">
            @for ($i = 0; $i < 5; $i++)
                @foreach($barItems as $item)
                    <span class="mx-4">{{ $item['emoji'] ?? '' }} {{ $item['text'] }}</span>
                    <span class="mx-4 text-brand-orange">•</span>
                @endforeach
            @endfor
        </div>
    </div>
</div>
@endif
