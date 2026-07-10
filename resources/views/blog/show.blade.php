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
        @endif
    </x-slot:schema>

    <style>
        .blog-content { font-size: 17px; line-height: 1.8; color: #374151; text-align: justify; }
        .blog-content h1 { font-size: 2rem; font-weight: 800; color: #111827; margin: 2.5rem 0 1rem; line-height: 1.3; }
        .blog-content h2 { font-size: 1.6rem; font-weight: 800; color: #111827; margin: 2.5rem 0 0.8rem; padding-bottom: 0.6rem; border-bottom: 2px solid #f3f4f6; line-height: 1.3; }
        .blog-content h3 { font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 2rem 0 0.6rem; line-height: 1.4; }
        .blog-content h4 { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 1.5rem 0 0.5rem; }
        .blog-content p { margin: 0 0 1.2rem; }
        .blog-content a { color: #2563eb; font-weight: 500; text-decoration: none; }
        .blog-content a:hover { text-decoration: underline; }
        .blog-content strong, .blog-content b { color: #111827; font-weight: 700; }
        .blog-content ul, .blog-content ol { margin: 1rem 0 1.5rem 1.5rem; }
        .blog-content ul { list-style-type: disc; }
        .blog-content ol { list-style-type: decimal; }
        .blog-content li { margin-bottom: 0.4rem; line-height: 1.7; }
        .blog-content blockquote { margin: 1.5rem 0; padding: 1rem 1.5rem; border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 0 12px 12px 0; color: #1e40af; font-style: normal; }
        .blog-content img { max-width: 100%; height: auto; border-radius: 16px; margin: 1.5rem 0; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .blog-content code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; font-family: monospace; }
        .blog-content pre { background: #1f2937; color: #e5e7eb; padding: 1.2rem; border-radius: 12px; overflow-x: auto; margin: 1.5rem 0; }
        .blog-content pre code { background: none; padding: 0; color: inherit; }
        .blog-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .blog-content th, .blog-content td { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; text-align: left; }
        .blog-content th { background: #f9fafb; font-weight: 700; color: #111827; }
        .blog-content hr { border: none; border-top: 2px solid #f3f4f6; margin: 2rem 0; }
    </style>

    {{-- Hero kapak görseli --}}
    @if($post->image_path)
        <div class="relative w-full overflow-hidden" style="max-height: 500px; background: #111827;">
            <img src="{{ Storage::disk('public')->url($post->image_path) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full object-cover"
                 style="max-height: 500px; opacity: 0.35;"
                 fetchpriority="high">
            <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(17,24,39,0.95) 0%, rgba(17,24,39,0.4) 50%, transparent 100%);"></div>
            <div style="position:absolute;bottom:0;left:0;right:0;padding:2rem 1rem 2.5rem;">
                <div style="max-width:1100px;margin:0 auto;">
                    <div style="display:flex;align-items:center;gap:10px;color:rgba(255,255,255,0.7);font-size:14px;margin-bottom:12px;">
                        <time datetime="{{ ($post->published_at ?? $post->created_at)->toW3cString() }}">
                            {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                        </time>
                        @if($post->author_name)
                            <span style="width:4px;height:4px;background:rgba(255,255,255,0.4);border-radius:50%;display:inline-block;"></span>
                            <span>{{ $post->author_name }}</span>
                        @endif
                    </div>
                    <h1 style="font-size:2.2rem;font-weight:800;color:#fff;line-height:1.25;margin:0;">
                        {{ $post->title }}
                    </h1>
                </div>
            </div>
        </div>
    @endif

    <div style="background:#fff;min-height:60vh;">
        <div style="max-width:1100px;margin:0 auto;padding:2.5rem 1.5rem 4rem;">

            {{-- Breadcrumb --}}
            <div style="margin-bottom:2rem;">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => 'Rehber Merkezi', 'url' => route('blog.index')],
                    ['name' => $post->title],
                ]" />
            </div>

            {{-- Kapak görseli yoksa başlık burada --}}
            @if(!$post->image_path)
                <div style="margin-bottom:2rem;">
                    <div style="display:flex;align-items:center;gap:10px;color:#6b7280;font-size:14px;margin-bottom:12px;">
                        <time datetime="{{ ($post->published_at ?? $post->created_at)->toW3cString() }}">
                            {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                        </time>
                        @if($post->author_name)
                            <span style="width:4px;height:4px;background:#9ca3af;border-radius:50%;display:inline-block;"></span>
                            <span>{{ $post->author_name }}</span>
                        @endif
                    </div>
                    <h1 style="font-size:2.2rem;font-weight:800;color:#111827;line-height:1.25;margin:0;">
                        {{ $post->title }}
                    </h1>
                </div>
            @endif

            {{-- İçerik --}}
            <article class="blog-content">
                {!! $post->content !!}
            </article>

            {{-- Alt bilgi --}}
            <div style="margin-top:3.5rem;padding-top:2rem;border-top:2px solid #f3f4f6;">
                
                {{-- Yazar kartı --}}
                @if($post->author_name)
                    <div style="display:flex;align-items:center;gap:14px;margin-bottom:2rem;">
                        <div style="width:48px;height:48px;background:linear-gradient(135deg,#1f2937,#4b5563);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:18px;">
                            {{ mb_substr($post->author_name, 0, 1) }}
                        </div>
                        <div>
                            <p style="font-weight:700;color:#111827;margin:0;">{{ $post->author_name }}</p>
                            <p style="font-size:14px;color:#6b7280;margin:4px 0 0;">
                                {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }} tarihinde yayınlandı
                            </p>
                        </div>
                    </div>
                @endif

                {{-- CTA --}}
                <div style="background:linear-gradient(135deg,#111827,#1f2937);border-radius:16px;padding:2.5rem;text-align:center;">
                    <div style="font-size:2rem;margin-bottom:0.5rem;">👟</div>
                    <h3 style="font-size:1.25rem;font-weight:700;color:#fff;margin:0 0 0.5rem;">Patenli ayakkabı modellerini keşfedin</h3>
                    <p style="color:#9ca3af;font-size:14px;margin:0 auto 1.5rem;max-width:400px;">En popüler patenli ayakkabı modellerini incelemek ve fiyatları görmek için ürünlerimize göz atın.</p>
                    <a href="{{ route('products.index') }}" wire:navigate 
                       style="display:inline-flex;align-items:center;gap:8px;padding:12px 28px;background:#fff;color:#111827;font-weight:700;font-size:14px;border-radius:999px;text-decoration:none;transition:background 0.2s;">
                        Ürünleri İncele
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
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
                <div style="margin-top:3.5rem;">
                    <h2 style="font-size:1.5rem;font-weight:800;color:#111827;margin:0 0 1.5rem;">Diğer Yazılar</h2>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(200px, 1fr));gap:1.2rem;">
                        @foreach($relatedPosts as $related)
                            <a href="{{ url('/blog/' . $related->slug) }}" wire:navigate 
                               style="display:block;background:#f9fafb;border-radius:16px;overflow:hidden;text-decoration:none;transition:box-shadow 0.3s;">
                                @if($related->image_path)
                                    <div style="aspect-ratio:16/9;overflow:hidden;">
                                        <img src="{{ Storage::disk('public')->url($related->image_path) }}" 
                                             alt="{{ $related->title }}"
                                             style="width:100%;height:100%;object-fit:cover;"
                                             loading="lazy">
                                    </div>
                                @else
                                    <div style="aspect-ratio:16/9;background:linear-gradient(135deg,#e5e7eb,#d1d5db);display:flex;align-items:center;justify-content:center;">
                                        <svg width="32" height="32" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                @endif
                                <div style="padding:12px 14px;">
                                    <p style="font-size:12px;color:#6b7280;margin:0 0 4px;">{{ ($related->published_at ?? $related->created_at)->translatedFormat('d M Y') }}</p>
                                    <h3 style="font-weight:700;color:#111827;font-size:14px;line-height:1.4;margin:0;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
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
