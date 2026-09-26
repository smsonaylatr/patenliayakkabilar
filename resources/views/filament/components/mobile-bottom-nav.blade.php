@php
    $currentPath = request()->path();
    $isLogin = str_starts_with($currentPath, 'admin/login');
@endphp

@if(!$isLogin)
@php
    $isAdminHome = $currentPath === 'admin' || $currentPath === 'admin/';
    $isOrders = str_starts_with($currentPath, 'admin/orders');
    $isProducts = str_starts_with($currentPath, 'admin/products');
    $isStock = str_starts_with($currentPath, 'admin/stock-management');

    $pendingOrdersCount = 0;
    try {
        $pendingOrdersCount = \App\Models\Order::where('status', 'pending')->count();
    } catch (\Throwable $e) {
        $pendingOrdersCount = 0;
    }
@endphp

<div 
    x-data="{
        toggleSidebar() {
            if (window.Alpine && window.Alpine.store && window.Alpine.store('sidebar')) {
                const s = window.Alpine.store('sidebar');
                s.isOpen ? s.close() : s.open();
                return;
            }
            const closeBtn = document.querySelector('.fi-topbar-close-sidebar-btn');
            const openBtn = document.querySelector('.fi-topbar-open-sidebar-btn');
            if (closeBtn && window.getComputedStyle(closeBtn).display !== 'none') {
                closeBtn.click();
            } else if (openBtn) {
                openBtn.click();
            }
        }
    }"
    class="fi-mobile-bottom-bar"
>
    <nav class="fi-mobile-nav-container">
        
        {{-- 1. ANA SAYFA / DASHBOARD --}}
        <a 
            href="{{ url('/admin') }}"
            class="fi-mobile-nav-item {{ $isAdminHome ? 'is-active' : '' }}"
            title="Ana Sayfa"
        >
            <div class="fi-mobile-nav-icon-wrapper">
                <svg class="fi-mobile-nav-icon" fill="none" stroke="currentColor" stroke-width="{{ $isAdminHome ? '2.4' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
            </div>
            <span class="fi-mobile-nav-label">Ana Sayfa</span>
            @if($isAdminHome)
                <span class="fi-mobile-nav-indicator"></span>
            @endif
        </a>

        {{-- 2. SİPARİŞLER --}}
        <a 
            href="{{ url('/admin/orders') }}"
            class="fi-mobile-nav-item {{ $isOrders ? 'is-active' : '' }}"
            title="Siparişler"
        >
            <div class="fi-mobile-nav-icon-wrapper">
                <svg class="fi-mobile-nav-icon" fill="none" stroke="currentColor" stroke-width="{{ $isOrders ? '2.4' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z" />
                </svg>
                @if($pendingOrdersCount > 0)
                    <span class="fi-mobile-nav-badge">
                        {{ $pendingOrdersCount > 99 ? '99+' : $pendingOrdersCount }}
                    </span>
                @endif
            </div>
            <span class="fi-mobile-nav-label">Siparişler</span>
            @if($isOrders)
                <span class="fi-mobile-nav-indicator"></span>
            @endif
        </a>

        {{-- 3. ÜRÜNLER --}}
        <a 
            href="{{ url('/admin/products') }}"
            class="fi-mobile-nav-item {{ $isProducts ? 'is-active' : '' }}"
            title="Ürünler"
        >
            <div class="fi-mobile-nav-icon-wrapper">
                <svg class="fi-mobile-nav-icon" fill="none" stroke="currentColor" stroke-width="{{ $isProducts ? '2.4' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </div>
            <span class="fi-mobile-nav-label">Ürünler</span>
            @if($isProducts)
                <span class="fi-mobile-nav-indicator"></span>
            @endif
        </a>

        {{-- 4. STOK YÖNETİMİ --}}
        <a 
            href="{{ url('/admin/stock-management') }}"
            class="fi-mobile-nav-item {{ $isStock ? 'is-active' : '' }}"
            title="Stok Yönetimi"
        >
            <div class="fi-mobile-nav-icon-wrapper">
                <svg class="fi-mobile-nav-icon" fill="none" stroke="currentColor" stroke-width="{{ $isStock ? '2.4' : '1.8' }}" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
                </svg>
            </div>
            <span class="fi-mobile-nav-label">Stok</span>
            @if($isStock)
                <span class="fi-mobile-nav-indicator"></span>
            @endif
        </a>

        {{-- 5. MENÜ (Filament Çekmecesini Tetikleme) --}}
        <button 
            type="button"
            x-on:click="toggleSidebar()"
            class="fi-mobile-nav-item"
            x-bind:class="{ 'is-active': (window.Alpine && window.Alpine.store && window.Alpine.store('sidebar') && window.Alpine.store('sidebar').isOpen) }"
            title="Menüyü Aç"
        >
            <div class="fi-mobile-nav-icon-wrapper">
                <svg class="fi-mobile-nav-icon" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </div>
            <span class="fi-mobile-nav-label">Menü</span>
        </button>

    </nav>
</div>
@endif
