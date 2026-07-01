<x-layouts.app>
    <x-slot:title>{{ $page->title }} | Patenli Ayakkabılar</x-slot>

    <div class="bg-gray-50 py-12 min-h-[60vh]">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
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
