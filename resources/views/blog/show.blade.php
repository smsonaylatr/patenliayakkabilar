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
    {{-- HERO — her zaman gösterilir --}}
    <div class="relative w-full overflow-hidden" style="min-height:280px;background:#111827;">
        @if($post->image_path)
            <img src="{{ Storage::disk('public')->url($post->image_path) }}" 
                 alt="{{ $post->title }}" 
                 class="w-full object-cover"
                 style="max-height:500px;opacity:0.3;position:absolute;inset:0;width:100%;height:100%;"
                 fetchpriority="high">
        @else
            {{-- Görselsiz dekoratif arka plan --}}
            <div style="position:absolute;inset:0;background:linear-gradient(135deg, #0f172a 0%, #1e3a5f 40%, #0d9488 100%);"></div>
            <div style="position:absolute;inset:0;opacity:0.08;background-image:radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px);background-size:30px 30px;"></div>
            <div style="position:absolute;top:50%;right:-60px;width:300px;height:300px;border-radius:50%;border:1px solid rgba(255,255,255,0.08);transform:translateY(-50%);"></div>
            <div style="position:absolute;top:50%;right:40px;width:180px;height:180px;border-radius:50%;border:1px solid rgba(255,255,255,0.06);transform:translateY(-50%);"></div>
        @endif

        <div style="position:absolute;inset:0;background:linear-gradient(to top, rgba(17,24,39,0.95) 0%, rgba(17,24,39,0.3) 60%, rgba(17,24,39,0.1) 100%);"></div>

        <div style="position:relative;z-index:10;max-width:1100px;margin:0 auto;padding:5rem 1.5rem 3rem;display:flex;flex-direction:column;justify-content:flex-end;min-height:280px;">
            {{-- Kategori etiketi --}}
            <div style="margin-bottom:1rem;">
                <span style="display:inline-block;padding:4px 14px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.15);border-radius:999px;color:rgba(255,255,255,0.8);font-size:12px;font-weight:600;letter-spacing:0.5px;text-transform:uppercase;">
                    📝 Rehber
                </span>
            </div>

            <h1 style="font-size:2.4rem;font-weight:800;color:#fff;line-height:1.2;margin:0 0 1rem;max-width:800px;">
                {{ $post->title }}
            </h1>

            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;">
                @if($post->author_name)
                    <div style="display:flex;align-items:center;gap:8px;">
                        <div style="width:32px;height:32px;background:linear-gradient(135deg,#3b82f6,#2563eb);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;">
                            {{ mb_substr($post->author_name, 0, 1) }}
                        </div>
                        <span style="color:rgba(255,255,255,0.85);font-size:14px;font-weight:500;">{{ $post->author_name }}</span>
                    </div>
                    <span style="width:4px;height:4px;background:rgba(255,255,255,0.3);border-radius:50%;display:inline-block;"></span>
                @endif
                <time datetime="{{ ($post->published_at ?? $post->created_at)->toW3cString() }}" 
                      style="color:rgba(255,255,255,0.6);font-size:14px;">
                    {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
                </time>
            </div>
        </div>
    </div>

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

            {{-- İçerik (ilk başlık hero'da gösterildiği için kaldırılır) --}}
            <article class="blog-content">
                @php
                    $content = $post->content;
                    // İçerikteki ilk h1 veya h2'yi kaldır (hero'da zaten gösteriliyor)
                    $content = preg_replace('/^\s*<h[12][^>]*>.*?<\/h[12]>\s*/is', '', $content, 1);
                @endphp
                {!! $content !!}
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
                {{-- Yatay Banner CTA --}}
                <style>
                    .split-cta-container {
                        background: linear-gradient(to right, #ffffff, #eff6ff);
                        border-radius: 16px; border: 1px solid #dbeafe;
                        display: flex; align-items: center; justify-content: space-between;
                        padding: 3rem 4rem; margin: 3rem 0;
                        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
                    }
                    .split-cta-content {
                        flex: 1; padding-right: 3rem;
                    }
                    .split-cta-badge {
                        display: inline-block; padding: 6px 14px; background: #dbeafe; color: #1d4ed8;
                        font-size: 0.85rem; font-weight: 700; border-radius: 999px;
                        margin-bottom: 1rem; text-transform: uppercase; letter-spacing: 0.05em;
                    }
                    .split-cta-title {
                        font-size: 1.8rem; font-weight: 800; color: #1e3a8a; margin: 0 0 1rem; line-height: 1.3;
                    }
                    .split-cta-text {
                        color: #475569; font-size: 1.1rem; margin: 0 0 1.8rem; max-width: 500px; line-height: 1.6;
                    }
                    .split-cta-btn {
                        display: inline-flex; align-items: center; gap: 8px; padding: 14px 32px;
                        background: #2563eb; color: #fff !important; font-weight: 600; font-size: 1.05rem;
                        border-radius: 12px; text-decoration: none !important; transition: all 0.2s ease;
                    }
                    .split-cta-btn:hover {
                        background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
                    }
                    .split-cta-visual {
                        flex-shrink: 0; display: flex; align-items: center; justify-content: center;
                        width: 160px; height: 160px; background: #bfdbfe; border-radius: 50%;
                        border: 10px solid #ffffff; color: #2563eb; box-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
                    }
                    .split-cta-icon {
                        font-size: 64px;
                    }
                    
                    @media (max-width: 768px) {
                        .split-cta-container {
                            flex-direction: column-reverse; text-align: center; padding: 2.5rem 1.5rem;
                        }
                        .split-cta-content {
                            padding-right: 0;
                        }
                        .split-cta-visual {
                            margin-bottom: 2rem; width: 120px; height: 120px; border-width: 6px;
                        }
                        .split-cta-icon {
                            font-size: 48px !important;
                        }
                        .split-cta-text {
                            margin-left: auto; margin-right: auto;
                        }
                    }
                </style>
                <div class="split-cta-container">
                    <div class="split-cta-content">
                        <span class="split-cta-badge">Koleksiyonu Keşfet</span>
                        <h3 class="split-cta-title">Patenli Ayakkabı Modelleri</h3>
                        <p class="split-cta-text">Çocuklarınız için en eğlenceli ve güvenli tekerlekli ayakkabı modellerimizi inceleyin. Hemen sipariş verin, kapınıza gelsin!</p>
                        <a href="{{ route('products.index') }}" wire:navigate class="split-cta-btn">
                            Tüm Ürünleri Gör
                            <i class="fa-solid fa-arrow-right" style="font-size: 14px;"></i>
                        </a>
                    </div>
                    <div class="split-cta-visual">
                        <i class="fa-solid fa-gift split-cta-icon"></i>
                    </div>
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
