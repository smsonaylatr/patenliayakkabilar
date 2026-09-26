@php
    $currentPath = request()->path();
    $isAdminHome = $currentPath === 'admin' || $currentPath === 'admin/';
    $isOrders = str_starts_with($currentPath, 'admin/orders');
    $isProducts = str_starts_with($currentPath, 'admin/products');
    $isStock = str_starts_with($currentPath, 'admin/stock-management');
    
    // Bekleyen sipariş sayısı rozeti
    $pendingOrdersCount = 0;
    try {
        $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
    } catch (\Throwable $e) {
        $pendingOrdersCount = 0;
    }
@endphp

<div 
    x-data="{ sidebarOpen: false }"
    class="fi-mobile-bottom-bar md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-t border-gray-200/80 dark:border-white/10 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] transition-all duration-200"
    style="padding-bottom: max(env(safe-area-inset-bottom, 0px), 8px);"
>
    <nav class="flex items-center justify-around px-2 py-1.5 h-14 max-w-lg mx-auto select-none">
        
        {{-- 1. ANA SAYFA / DASHBOARD --}}
        <a 
            href="{{ url('/admin') }}"
            class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-150 active:scale-90 group relative {{ $isAdminHome ? 'text-[#ff4e00]' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}"
            title="Ana Sayfa"
        >
            <div class="relative">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="{{ $isAdminHome ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </div>
            <span class="text-[10px] font-semibold tracking-tight mt-1 {{ $isAdminHome ? 'font-bold' : '' }}">Ana Sayfa</span>
            @if($isAdminHome)
                <span class="absolute bottom-0 w-5 h-0.5 bg-[#ff4e00] rounded-full"></span>
            @endif
        </a>

        {{-- 2. SİPARİŞLER --}}
        <a 
            href="{{ url('/admin/orders') }}"
            class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-150 active:scale-90 group relative {{ $isOrders ? 'text-[#ff4e00]' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}"
            title="Siparişler"
        >
            <div class="relative">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="{{ $isOrders ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                </svg>
                @if($pendingOrdersCount > 0)
                    <span class="absolute -top-1.5 -right-2.5 min-w-[17px] h-[17px] px-1 bg-[#ff4e00] text-white text-[9px] font-black rounded-full flex items-center justify-center shadow-sm animate-pulse">
                        {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                    </span>
                @endif
            </div>
            <span class="text-[10px] font-semibold tracking-tight mt-1 {{ $isOrders ? 'font-bold' : '' }}">Siparişler</span>
            @if($isOrders)
                <span class="absolute bottom-0 w-5 h-0.5 bg-[#ff4e00] rounded-full"></span>
            @endif
        </a>

        {{-- 3. ÜRÜNLER --}}
        <a 
            href="{{ url('/admin/products') }}"
            class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-150 active:scale-90 group relative {{ $isProducts ? 'text-[#ff4e00]' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}"
            title="Ürünler"
        >
            <div class="relative">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="{{ $isProducts ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </div>
            <span class="text-[10px] font-semibold tracking-tight mt-1 {{ $isProducts ? 'font-bold' : '' }}">Ürünler</span>
            @if($isProducts)
                <span class="absolute bottom-0 w-5 h-0.5 bg-[#ff4e00] rounded-full"></span>
            @endif
        </a>

        {{-- 4. STOK YÖNETİMİ --}}
        <a 
            href="{{ url('/admin/stock-management') }}"
            class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-150 active:scale-90 group relative {{ $isStock ? 'text-[#ff4e00]' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100' }}"
            title="Stok Yönetimi"
        >
            <div class="relative">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="{{ $isStock ? '2.2' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                </svg>
            </div>
            <span class="text-[10px] font-semibold tracking-tight mt-1 {{ $isStock ? 'font-bold' : '' }}">Stok</span>
            @if($isStock)
                <span class="absolute bottom-0 w-5 h-0.5 bg-[#ff4e00] rounded-full"></span>
            @endif
        </a>

        {{-- 5. MENÜ (Filament Sidebar Aç / Kapat) --}}
        <button 
            type="button"
            x-data="{}"
            x-on:click="$store.sidebar.isOpen ? $store.sidebar.close() : $store.sidebar.open()"
            class="flex flex-col items-center justify-center flex-1 py-1 transition-all duration-150 active:scale-90 group relative text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 cursor-pointer focus:outline-none"
            x-bind:class="{ 'text-[#ff4e00]': $store.sidebar.isOpen }"
            title="Navigasyon Menüsü"
        >
            <div class="relative">
                <svg class="w-5 h-5 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>
            <span class="text-[10px] font-semibold tracking-tight mt-1" x-text="$store.sidebar.isOpen ? 'Kapat' : 'Menü'">Menü</span>
        </button>

    </nav>
</div>
