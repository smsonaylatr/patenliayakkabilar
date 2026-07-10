<x-layouts.app>
    <x-slot:title>{{ $post->meta_title ?? $post->title . ' | Patenli Ayakkabılar' }}</x-slot:title>
    <x-slot:description>{{ $post->meta_description ?? Str::limit(strip_tags($post->excerpt ?? $post->content), 155) }}</x-slot:description>
    <x-slot:ogType>article</x-slot:ogType>
    <x-slot:canonical>{{ url('/blog/' . $post->slug) }}</x-slot:canonical>
    @if($post->image_path)
        <x-slot:ogImage>{{ Storage::disk('public')->url($post->image_path) }}</x-slot:ogImage>
    @endif
    @if(isset($post->is_indexable) && !$post->is_indexable)
        <x-slot:robots>noindex, follow</x-slot:robots>
    @endif
    <x-slot:schema>
        @if(app()->bound(\App\Services\SchemaService::class))
            {!! app(\App\Services\SchemaService::class)->blogArticle($post) !!}
        @else
            <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $post->title,
                'description' => Str::limit(strip_tags($post->excerpt ?? $post->content), 200),
                'datePublished' => ($post->published_at ?? $post->created_at)->toW3cString(),
                'dateModified' => $post->updated_at->toW3cString(),
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'Patenli Ayakkabılar',
                    'url' => url('/'),
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
            </script>
        @endif
    </x-slot:schema>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <div class="mb-6">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi', 'url' => route('blog.index')],
                    ['name' => $post->title],
                ]" />
            </div>

            <article class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                @if($post->image_path)
                    <div class="aspect-video w-full overflow-hidden">
                        <img src="{{ Storage::disk('public')->url($post->image_path) }}" 
                             alt="{{ $post->title }}" 
                             class="w-full h-full object-cover"
                             fetchpriority="high">
                    </div>
                @endif

                <div class="p-8 sm:p-12">
                    {{-- Üst bilgiler --}}
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
                        <time datetime="{{ ($post->published_at ?? $post->created_at)->toW3cString() }}">
                            {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                        </time>
                        @if($post->author_name)
                            <span>•</span>
                            <span>{{ $post->author_name }}</span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-8">
                        {{ $post->title }}
                    </h1>

                    <div class="prose prose-lg prose-blue max-w-none prose-headings:font-bold prose-a:text-blue-600 hover:prose-a:text-blue-500 prose-img:rounded-xl">
                        {!! $post->content !!}
                    </div>

                    {{-- İlgili ürünler CTA --}}
                    <div class="mt-12 p-6 bg-gray-50 rounded-2xl border border-gray-100 text-center">
                        <p class="text-lg font-bold text-gray-900 mb-2">Patenli ayakkabı modellerini keşfedin</p>
                        <p class="text-gray-500 text-sm mb-4">En popüler modelleri incelemek için ürünlerimize göz atın.</p>
                        <a href="{{ route('products.index') }}" wire:navigate class="inline-flex items-center gap-2 px-6 py-3 bg-black text-white font-semibold text-sm rounded-full hover:bg-gray-800 transition-colors">
                            Ürünleri İncele
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </article>
        </div>
    </div>
</x-layouts.app>
