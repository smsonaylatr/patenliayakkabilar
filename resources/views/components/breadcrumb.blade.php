@props(['items' => []])

@if(count($items) > 0)
{{-- Görsel Breadcrumb --}}
<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex items-center gap-2 text-sm text-gray-500 flex-wrap">
        @foreach($items as $i => $item)
            @if(!$loop->last)
                <li>
                    <a href="{{ $item['url'] }}"
                       class="hover:text-gray-900 transition-colors"
                       wire:navigate>
                        {{ $item['name'] }}
                    </a>
                </li>
                <li aria-hidden="true">
                    <svg class="w-4 h-4 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </li>
            @else
                <li class="text-gray-900 font-medium" aria-current="page">{{ $item['name'] }}</li>
            @endif
        @endforeach
    </ol>
</nav>

{{-- JSON-LD BreadcrumbList Schema --}}
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => collect($items)->map(function ($item, $index) use ($items) {
        $element = [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['name'],
        ];

        // Son eleman'da "item" alanı olmasın (Google önerisi)
        if ($index < count($items) - 1 && isset($item['url'])) {
            $element['item'] = url($item['url']);
        }

        return $element;
    })->values()->all(),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
@endif
