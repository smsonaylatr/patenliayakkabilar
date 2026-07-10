<x-layouts.app>
    <x-slot:title>Rehber Merkezi | Patenli Ayakkabılar</x-slot:title>
    <x-slot:description>Patenli ayakkabı rehberleri, kullanım kılavuzları, güvenlik ipuçları ve satın alma tavsiyeleri. Çocuğunuz için doğru patenli ayakkabıyı seçin.</x-slot:description>
    <x-slot:canonical>{{ url('/blog') }}</x-slot:canonical>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="mb-8">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi'],
                ]" />
            </div>

            <div class="text-center mb-12">
                <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight sm:text-5xl">Rehber Merkezi</h1>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">Patenli ayakkabı seçimi, kullanımı ve bakımı hakkında bilmeniz gereken her şey.</p>
            </div>

            @if($posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300 group">
                            @if($post->image_path)
                                <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="block aspect-video overflow-hidden">
                                    <img src="{{ Storage::disk('public')->url($post->image_path) }}" 
                                         alt="{{ $post->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                         loading="lazy">
                                </a>
                            @endif
                            <div class="p-6">
                                <time class="text-xs font-medium text-gray-400 uppercase tracking-wider" datetime="{{ $post->created_at->toW3cString() }}">
                                    {{ $post->created_at->translatedFormat('d F Y') }}
                                </time>
                                <h2 class="mt-2 text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" wire:navigate>{{ $post->title }}</a>
                                </h2>
                                @if($post->excerpt)
                                    <p class="mt-3 text-sm text-gray-500 line-clamp-3">{{ $post->excerpt }}</p>
                                @endif
                                <a href="{{ route('blog.show', $post->slug) }}" wire:navigate class="mt-4 inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                                    Devamını Oku
                                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $posts->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <p class="text-lg font-medium text-gray-900">Henüz içerik eklenmemiş.</p>
                    <p class="text-gray-500 mt-2">Çok yakında patenli ayakkabı rehberleri burada olacak.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
