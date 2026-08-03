<div class="mt-8 max-w-xl mx-auto px-4 sm:px-0 relative" x-data="{ open: true, searchQuery: @entangle('search').live }" x-init="searchQuery = '{{ request('q', '') }}'" @click.outside="open = false">
    <form action="{{ route('blog.index') }}" method="GET" class="relative">
        <input 
            x-ref="searchInput"
            type="text" 
            name="q" 
            x-model.debounce.300ms="searchQuery"
            @focus="open = true"
            @keydown.escape="open = false"
            placeholder="Blog yazılarında ara..." 
            class="w-full pl-6 pr-24 py-3 border border-gray-300 rounded-full shadow-sm focus:ring-blue-500 focus:border-blue-500 text-gray-900 placeholder-gray-400"
            autocomplete="off"
        >
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center gap-1">
            <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; $refs.searchInput.focus()" class="p-2 text-gray-400 hover:text-red-500 transition-colors" style="display: none;">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <button type="submit" class="p-2 text-gray-500 hover:text-blue-600 transition-colors">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </div>
    </form>

    @if(strlen(trim($search)) >= 2)
        <div x-show="open" class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden text-left">
            @if($results->count() > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($results as $post)
                        <li>
                            <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="flex items-center px-4 py-3 hover:bg-gray-50 transition-colors">
                                @if($post->image_path)
                                    <div class="flex-shrink-0 h-10 w-14 rounded overflow-hidden bg-gray-100">
                                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                                    </div>
                                @endif
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900 line-clamp-1">{{ $post->title }}</p>
                                    @if($post->excerpt)
                                        <p class="text-xs text-gray-500 line-clamp-1 mt-0.5">{{ $post->excerpt }}</p>
                                    @endif
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="bg-gray-50 px-4 py-2 text-center border-t border-gray-100">
                    <button type="button" @click="$root.querySelector('form').submit()" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Tüm sonuçları gör</button>
                </div>
            @else
                <div class="px-4 py-6 text-center text-sm text-gray-500">
                    "{{ $search }}" ile ilgili içerik bulunamadı.
                </div>
            @endif
        </div>
    @endif
</div>
