<x-layouts.app>
    <x-slot:title>{{ $post->meta_title ?? $post->title . ' | Patenli Ayakkabılar' }}</x-slot:title>
    <x-slot:description>{{ $post->meta_description ?? Str::limit(strip_tags($post->excerpt ?? $post->content), 155) }}</x-slot:description>
    <x-slot:ogType>article</x-slot:ogType>
    <x-slot:canonical>{{ url('/blog/' . $post->slug) }}</x-slot:canonical>
    @if($post->image_path)
        <x-slot:ogImage>{{ asset('storage/' . $post->image_path) }}</x-slot:ogImage>
    @endif
    @if(isset($post->is_indexable) && !$post->is_indexable)
        <x-slot:robots>noindex, follow</x-slot:robots>
    @endif
    <x-slot:schema>
        @if(app()->bound(\App\Services\SchemaService::class))
            {!! app(\App\Services\SchemaService::class)->blogArticle($post) !!}
        @endif
    </x-slot:schema>

    {{-- Hero kapak görseli --}}
    @if($post->image_path)
        <div class="relative w-full bg-gray-900" style="max-height: 480px; overflow: hidden;">
            <img src="{{ asset('storage/' . $post->image_path) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full h-full object-cover opacity-40"
                 style="max-height: 480px;"
                 fetchpriority="high">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/50 to-transparent"></div>
            <div class="absolute bottom-0 left-0 right-0 p-8 sm:p-12">
                <div class="max-w-3xl mx-auto">
                    <div class="flex items-center gap-3 text-white/70 text-sm mb-4">
                        <time datetime="{{ ($post->published_at ?? $post->created_at)->toW3cString() }}">
                            {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                        </time>
                        @if($post->author_name)
                            <span class="w-1 h-1 bg-white/50 rounded-full"></span>
                            <span>{{ $post->author_name }}</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-tight">
                        {{ $post->title }}
                    </h1>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

            {{-- Breadcrumb --}}
            <div class="mb-8">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi', 'url' => route('blog.index')],
                    ['name' => $post->title],
                ]" />
            </div>

            {{-- Kapak görseli yoksa başlığı burada göster --}}
            @if(!$post->image_path)
                <div class="mb-8">
                    <div class="flex items-center gap-3 text-gray-500 text-sm mb-4">
                        <time datetime="{{ ($post->published_at ?? $post->created_at)->toW3cString() }}">
                            {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                        </time>
                        @if($post->author_name)
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span>{{ $post->author_name }}</span>
                        @endif
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight leading-tight">
                        {{ $post->title }}
                    </h1>
                </div>
            @endif

            {{-- İçerik --}}
            <article class="
                prose prose-lg prose-gray max-w-none
                prose-headings:font-extrabold prose-headings:tracking-tight prose-headings:text-gray-900
                prose-h2:text-2xl prose-h2:mt-12 prose-h2:mb-4 prose-h2:pb-3 prose-h2:border-b prose-h2:border-gray-100
                prose-h3:text-xl prose-h3:mt-8 prose-h3:mb-3
                prose-p:text-gray-600 prose-p:leading-relaxed prose-p:text-[17px]
                prose-li:text-gray-600 prose-li:text-[17px] prose-li:leading-relaxed
                prose-a:text-blue-600 prose-a:font-medium prose-a:no-underline hover:prose-a:underline
                prose-strong:text-gray-900 prose-strong:font-bold
                prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-blue-50 prose-blockquote:py-1 prose-blockquote:px-4 prose-blockquote:rounded-r-lg prose-blockquote:not-italic prose-blockquote:text-gray-700
                prose-img:rounded-2xl prose-img:shadow-md
                prose-code:bg-gray-100 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-code:text-sm prose-code:font-mono
                prose-ul:space-y-1 prose-ol:space-y-1
            ">
                {!! $post->content !!}
            </article>

            {{-- Alt bilgi çizgisi --}}
            <div class="mt-14 pt-8 border-t border-gray-100">
                {{-- Yazar kartı --}}
                @if($post->author_name)
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-12 h-12 bg-gradient-to-br from-gray-800 to-gray-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                            {{ mb_substr($post->author_name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $post->author_name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }} tarihinde yayınlandı
                            </p>
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 sm:p-10 text-center">
                    <div class="text-3xl mb-3">👟</div>
                    <h3 class="text-xl font-bold text-white mb-2">Patenli ayakkabı modellerini keşfedin</h3>
                    <p class="text-gray-400 text-sm mb-6 max-w-md mx-auto">En popüler patenli ayakkabı modellerini incelemek ve fiyatları görmek için ürünlerimize göz atın.</p>
                    <a href="{{ route('products.index') }}" wire:navigate 
                       class="inline-flex items-center gap-2 px-7 py-3 bg-white text-gray-900 font-bold text-sm rounded-full hover:bg-gray-100 transition-colors">
                        Ürünleri İncele
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>

            {{-- Diğer yazılar --}}
            @php
                $relatedPosts = \App\Models\BlogPost::where('status', true)
                    ->where('id', '!=', $post->id)
                    ->latest('published_at')
                    ->take(3)
                    ->get();
            @endphp
            @if($relatedPosts->count())
                <div class="mt-14">
                    <h2 class="text-2xl font-extrabold text-gray-900 mb-6">Diğer Yazılar</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        @foreach($relatedPosts as $related)
                            <a href="{{ url('/blog/' . $related->slug) }}" wire:navigate 
                               class="group block bg-gray-50 rounded-2xl overflow-hidden hover:shadow-md transition-all duration-300">
                                @if($related->image_path)
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ asset('storage/' . $related->image_path) }}" 
                                             alt="{{ $related->title }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                             loading="lazy">
                                    </div>
                                @else
                                    <div class="aspect-video bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <p class="text-xs text-gray-500 mb-1">{{ ($related->published_at ?? $related->created_at)->translatedFormat('d M Y') }}</p>
                                    <h3 class="font-bold text-gray-900 text-sm leading-snug group-hover:text-blue-600 transition-colors line-clamp-2">
                                        {{ $related->title }}
                                    </h3>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
