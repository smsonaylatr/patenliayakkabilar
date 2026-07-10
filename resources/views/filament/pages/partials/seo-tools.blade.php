<div class="grid grid-cols-1 md:grid-cols-2 gap-4">

    {{-- Sitemap --}}
    <a href="{{ url('/sitemap.xml') }}" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-map class="w-5 h-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Sitemap.xml</p>
            <p class="text-xs text-gray-500">Sitenizin sitemap'ini görüntüleyin</p>
        </div>
    </a>

    {{-- Google Merchant Feed --}}
    <a href="{{ url('/feeds/google-merchant.xml') }}" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-green-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-shopping-bag class="w-5 h-5 text-green-600 dark:text-green-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Google Merchant Feed</p>
            <p class="text-xs text-gray-500">Ürün feed'ini görüntüleyin</p>
        </div>
    </a>

    {{-- robots.txt --}}
    <a href="{{ url('/robots.txt') }}" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
            <x-heroicon-o-document-text class="w-5 h-5 text-gray-600 dark:text-gray-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">robots.txt</p>
            <p class="text-xs text-gray-500">Tarayıcı kurallarını görüntüleyin</p>
        </div>
    </a>

    {{-- Google Search Console --}}
    <a href="https://search.google.com/search-console" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-globe-alt class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Google Search Console</p>
            <p class="text-xs text-gray-500">Arama performansınızı takip edin</p>
        </div>
    </a>

    {{-- Rich Results Test --}}
    <a href="https://search.google.com/test/rich-results?url={{ urlencode(config('app.url')) }}" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-purple-50 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-star class="w-5 h-5 text-purple-600 dark:text-purple-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Rich Results Test</p>
            <p class="text-xs text-gray-500">Schema doğrulaması yapın</p>
        </div>
    </a>

    {{-- PageSpeed Insights --}}
    <a href="https://pagespeed.web.dev/analysis?url={{ urlencode(config('app.url')) }}" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-bolt class="w-5 h-5 text-red-600 dark:text-red-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">PageSpeed Insights</p>
            <p class="text-xs text-gray-500">Site hızını test edin</p>
        </div>
    </a>

    {{-- Google Merchant Center --}}
    <a href="https://merchants.google.com" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-orange-50 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-building-storefront class="w-5 h-5 text-orange-600 dark:text-orange-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">Google Merchant Center</p>
            <p class="text-xs text-gray-500">Ürün feed'inizi yönetin</p>
        </div>
    </a>

    {{-- llms.txt --}}
    <a href="{{ url('/llms.txt') }}" target="_blank"
       class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 transition-colors group">
        <div class="flex-shrink-0 w-10 h-10 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
            <x-heroicon-o-cpu-chip class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
        </div>
        <div>
            <p class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">llms.txt</p>
            <p class="text-xs text-gray-500">AI tarayıcı bilgilerini görüntüleyin</p>
        </div>
    </a>
</div>
