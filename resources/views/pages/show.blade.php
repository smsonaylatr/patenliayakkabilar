<x-layouts.app>
    <x-slot:title>{{ $page->meta_title ?? $page->title . ' | Patenli Ayakkabılar' }}</x-slot:title>
    <x-slot:description>{{ $page->meta_description ?? Str::limit(strip_tags($page->content), 155) }}</x-slot:description>
    @if(isset($page->is_indexable) && !$page->is_indexable)
        <x-slot:robots>noindex, follow</x-slot:robots>
    @endif

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
                
                <div class="prose prose-lg prose-blue max-w-none prose-headings:font-bold prose-a:text-blue-600 hover:prose-a:text-blue-500">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
