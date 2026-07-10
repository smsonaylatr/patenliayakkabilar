<x-layouts.app>
    <x-slot:title>{{ $page->meta_title ?? $page->title . ' | Patenli Ayakkabılar' }}</x-slot:title>
    <x-slot:description>{{ $page->meta_description ?? Str::limit(strip_tags($page->content), 155) }}</x-slot:description>
    @if(isset($page->is_indexable) && !$page->is_indexable)
        <x-slot:robots>noindex, follow</x-slot:robots>
    @endif

    <style>
        .page-content { font-size: 17px; line-height: 1.8; color: #374151; text-align: justify; }
        .page-content h1 { font-size: 2rem; font-weight: 800; color: #111827; margin: 2.5rem 0 1rem; line-height: 1.3; }
        .page-content h2 { font-size: 1.6rem; font-weight: 800; color: #111827; margin: 2.5rem 0 0.8rem; padding-bottom: 0.6rem; border-bottom: 2px solid #f3f4f6; line-height: 1.3; }
        .page-content h3 { font-size: 1.3rem; font-weight: 700; color: #1f2937; margin: 2rem 0 0.6rem; line-height: 1.4; }
        .page-content h4 { font-size: 1.1rem; font-weight: 700; color: #1f2937; margin: 1.5rem 0 0.5rem; }
        .page-content p { margin: 0 0 1.2rem; }
        .page-content a { color: #2563eb; font-weight: 500; text-decoration: none; }
        .page-content a:hover { text-decoration: underline; }
        .page-content strong, .page-content b { color: #111827; font-weight: 700; }
        .page-content ul, .page-content ol { margin: 1rem 0 1.5rem 1.5rem; }
        .page-content ul { list-style-type: disc; }
        .page-content ol { list-style-type: decimal; }
        .page-content li { margin-bottom: 0.4rem; line-height: 1.7; }
        .page-content blockquote { margin: 1.5rem 0; padding: 1rem 1.5rem; border-left: 4px solid #3b82f6; background: #eff6ff; border-radius: 0 12px 12px 0; color: #1e40af; font-style: normal; }
        .page-content img { max-width: 100%; height: auto; border-radius: 16px; margin: 1.5rem 0; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .page-content code { background: #f3f4f6; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; font-family: monospace; }
        .page-content pre { background: #1f2937; color: #e5e7eb; padding: 1.2rem; border-radius: 12px; overflow-x: auto; margin: 1.5rem 0; }
        .page-content pre code { background: none; padding: 0; color: inherit; }
        .page-content table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        .page-content th, .page-content td { padding: 0.75rem 1rem; border: 1px solid #e5e7eb; text-align: left; }
        .page-content th { background: #f9fafb; font-weight: 700; color: #111827; }
        .page-content hr { border: none; border-top: 2px solid #f3f4f6; margin: 2rem 0; }
    </style>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-[1100px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <div class="mb-6">
                <x-breadcrumb :items="[
                    ['name' => 'Ana Sayfa', 'url' => route('home')],
                    ['name' => $page->title],
                ]" />
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-12">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-8 border-b border-gray-100 pb-6">
                    {{ $page->title }}
                </h1>
                
                <div class="page-content">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
