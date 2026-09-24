{{-- Floating Social Sidebar + İndirim Kuponu Widget --}}
{{-- Halıköy birebir klon: beyaz cam, ince border, magnet hover --}}
@php
    $sidebarSettings = \App\Models\Setting::whereIn('key', [
        'footer_facebook',
        'footer_instagram',
    ])->pluck('value', 'key')->toArray();
@endphp

<style>
    .newsletter-bar {
        position: fixed;
        left: 0;
        top: 50svh;
        transform: translateY(-50%);
        width: 3.25rem;
        margin-left: 0.5rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        background-color: rgba(255, 255, 255, 0.82);
        box-shadow: 0 0 0 0.5px rgba(0, 0, 0, 0.12);
        border-radius: 9999px;
        z-index: 30;
        display: none;
    }

    @media screen and (min-width: 768px) {
        .newsletter-bar {
            display: grid;
            gap: 0.2rem;
        }
    }

    @media screen and (min-width: 1024px) {
        .newsletter-bar {
            margin-left: 0.875rem;
        }
    }

    .newsletter-bar__social ul {
        padding-top: 0.25rem;
        flex-direction: column;
        display: flex;
        align-items: center;
        gap: 0;
        list-style: none;
        margin: 0;
        padding-left: 0;
        padding-right: 0;
    }

    .newsletter-bar__social ul li {
        width: 2.5rem;
        height: 2.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .newsletter-bar__social .social_platform {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
        color: #1d1d1f;
        text-decoration: none;
        position: relative;
    }

    .newsletter-bar__social .social_platform svg {
        width: 0.9375rem;
        height: 0.9375rem;
        fill: currentColor;
        will-change: transform;
        transition: transform 0.35s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .newsletter-bar__social .social_platform:hover svg {
        transform: scale(1.2);
    }

    .newsletter-bar__button {
        writing-mode: vertical-rl;
        transform: rotate(-180deg);
        font-size: 0.5625rem;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 600;
        padding: 0.75rem 0.625rem;
        margin: 0.125rem auto 0;
        color: #1d1d1f;
        background-color: rgba(0, 0, 0, 0.06);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        border: none;
        line-height: 1;
        white-space: nowrap;
    }

    .newsletter-bar__button:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    @media (pointer: fine) {
        .newsletter-bar__button:hover span {
            animation: nb-beat 0.6s infinite ease;
        }
    }

    /* ===== Koyu zemin üzerinde: koyu cam ===== */
    .newsletter-bar.is-dark {
        background-color: rgba(22, 22, 23, 0.72);
        box-shadow: 0 0 0 0.5px rgba(255, 255, 255, 0.15);
    }

    .newsletter-bar.is-dark .social_platform {
        color: rgba(255, 255, 255, 0.92);
    }

    .newsletter-bar.is-dark .newsletter-bar__button {
        color: rgba(255, 255, 255, 0.92);
        background-color: rgba(255, 255, 255, 0.1);
    }

    .newsletter-bar.is-dark .newsletter-bar__button:hover {
        background-color: rgba(255, 255, 255, 0.18);
    }

    @keyframes nb-beat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.06); }
    }
</style>

<div class="newsletter-bar" id="floatingSidebar">
    {{-- Sosyal İkonlar --}}
    @if(!empty($sidebarSettings['footer_facebook']) || !empty($sidebarSettings['footer_instagram']))
    <div class="newsletter-bar__social">
        <ul role="list">
            @if(!empty($sidebarSettings['footer_facebook']))
            <li>
                <a target="_blank" rel="noopener noreferrer" href="{{ $sidebarSettings['footer_facebook'] }}" class="social_platform" aria-label="Facebook">
                    <svg viewBox="0 0 24 24" stroke="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" role="presentation">
                        <path d="M9.03153 23L9 13H5V9H9V6.5C9 2.7886 11.2983 1 14.6091 1C16.1951 1 17.5581 1.11807 17.9553 1.17085V5.04948L15.6591 5.05052C13.8584 5.05052 13.5098 5.90614 13.5098 7.16171V9H18.75L16.75 13H13.5098V23H9.03153Z"/>
                    </svg>
                </a>
            </li>
            @endif
            @if(!empty($sidebarSettings['footer_instagram']))
            <li>
                <a target="_blank" rel="noopener noreferrer" href="{{ $sidebarSettings['footer_instagram'] }}" class="social_platform" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" stroke="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" role="presentation">
                        <path d="M12 2.98C14.94 2.98 15.28 2.99 16.44 3.04C17.14 3.04 17.83 3.18 18.48 3.42C18.96 3.6 19.39 3.88 19.75 4.24C20.12 4.59 20.4 5.03 20.57 5.51C20.81 6.16 20.94 6.85 20.95 7.55C21 8.71 21.01 9.06 21.01 12C21.01 14.94 21 15.28 20.95 16.44C20.95 17.14 20.81 17.83 20.57 18.48C20.39 18.95 20.11 19.39 19.75 19.75C19.39 20.11 18.96 20.39 18.48 20.57C17.83 20.81 17.14 20.94 16.44 20.95C15.28 21 14.93 21.01 12 21.01C9.07 21.01 8.72 21 7.55 20.95C6.85 20.95 6.16 20.81 5.51 20.57C5.03 20.39 4.6 20.11 4.24 19.75C3.87 19.4 3.59 18.96 3.42 18.48C3.18 17.83 3.05 17.14 3.04 16.44C2.99 15.28 2.98 14.93 2.98 12C2.98 9.07 2.99 8.72 3.04 7.55C3.04 6.85 3.18 6.16 3.42 5.51C3.6 5.03 3.88 4.6 4.24 4.24C4.59 3.87 5.03 3.59 5.51 3.42C6.16 3.18 6.85 3.05 7.55 3.04C8.71 2.99 9.06 2.98 12 2.98ZM12 1C9.01 1 8.64 1.01 7.47 1.07C6.56 1.09 5.65 1.26 4.8 1.58C4.07 1.86 3.4 2.3 2.85 2.85C2.3 3.41 1.86 4.07 1.58 4.8C1.26 5.65 1.09 6.56 1.07 7.47C1.02 8.64 1 9.01 1 12C1 14.99 1.01 15.36 1.07 16.53C1.09 17.44 1.26 18.35 1.58 19.2C1.86 19.93 2.3 20.6 2.85 21.15C3.41 21.7 4.07 22.14 4.8 22.42C5.65 22.74 6.56 22.91 7.47 22.93C8.64 22.98 9.01 23 12 23C14.99 23 15.36 22.99 16.53 22.93C17.44 22.91 18.35 22.74 19.2 22.42C19.93 22.14 20.6 21.7 21.15 21.15C21.7 20.59 22.14 19.93 22.42 19.2C22.74 18.35 22.91 17.44 22.93 16.53C22.98 15.36 23 14.99 23 12C23 9.01 22.99 8.64 22.93 7.47C22.91 6.56 22.74 5.65 22.42 4.8C22.14 4.07 21.7 3.4 21.15 2.85C20.59 2.3 19.93 1.86 19.2 1.58C18.35 1.26 17.44 1.09 16.53 1.07C15.36 1.02 14.99 1 12 1ZM12 6.35C10.88 6.35 9.79 6.68 8.86 7.3C7.93 7.92 7.21 8.8 6.78 9.84C6.35 10.87 6.24 12.01 6.46 13.1C6.68 14.2 7.22 15.2 8.01 15.99C8.8 16.78 9.81 17.32 10.9 17.54C12 17.76 13.13 17.65 14.16 17.22C15.19 16.79 16.07 16.07 16.7 15.14C17.32 14.21 17.65 13.12 17.65 12C17.65 10.5 17.05 9.06 16 8.01C14.94 6.95 13.5 6.36 12.01 6.36L12 6.35ZM12 15.67C11.27 15.67 10.57 15.45 9.96 15.05C9.36 14.65 8.89 14.07 8.61 13.4C8.33 12.73 8.26 11.99 8.4 11.28C8.54 10.57 8.89 9.92 9.4 9.4C9.91 8.88 10.57 8.54 11.28 8.4C11.99 8.26 12.73 8.33 13.4 8.61C14.07 8.89 14.64 9.36 15.05 9.96C15.45 10.56 15.67 11.27 15.67 12C15.67 12.97 15.28 13.91 14.6 14.59C13.91 15.28 12.98 15.66 12.01 15.66L12 15.67ZM17.87 7.45C18.6 7.45 19.19 6.86 19.19 6.13C19.19 5.4 18.6 4.81 17.87 4.81C17.14 4.81 16.55 5.4 16.55 6.13C16.55 6.86 17.14 7.45 17.87 7.45Z"/>
                    </svg>
                </a>
            </li>
            @endif
            {{-- WhatsApp --}}
            <li>
                <a target="_blank" rel="noopener noreferrer" href="https://wa.me/908503073164" class="social_platform" aria-label="WhatsApp">
                    <svg viewBox="0 0 24 24" stroke="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg" role="presentation">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/>
                    </svg>
                </a>
            </li>
        </ul>
    </div>
    @endif

    {{-- %5 İNDİRİM Butonu --}}
    <a href="{{ route('products.index') }}" class="newsletter-bar__button" wire:navigate>
        <span>%5 İNDİRİM</span>
    </a>
</div>

<script>
(function() {
    var bar = document.getElementById('floatingSidebar');
    if (!bar) return;

    // === Magnet hover efekti (Halıköy klonu) ===
    var MAGNET = 10;
    bar.querySelectorAll('.social_platform').forEach(function(link) {
        link.addEventListener('mousemove', function(e) {
            var svg = link.querySelector('svg');
            if (!svg) return;
            var rect = link.getBoundingClientRect();
            var dx = ((e.clientX - rect.left) / rect.width - 0.5) * MAGNET;
            var dy = ((e.clientY - rect.top) / rect.height - 0.5) * MAGNET;
            svg.style.transform = 'scale(1.2) translate(' + dx + 'px,' + dy + 'px)';
        });
        link.addEventListener('mouseleave', function() {
            var svg = link.querySelector('svg');
            if (svg) svg.style.transform = '';
        });
    });

    // === Zemin renk algılama ===
    function checkBg() {
        var rect = bar.getBoundingClientRect();
        var cx = rect.left + rect.width / 2;
        var cy = rect.top + rect.height / 2;
        bar.style.pointerEvents = 'none';
        var el = document.elementFromPoint(cx, cy);
        bar.style.pointerEvents = '';
        if (!el) return;
        var node = el;
        var dark = false;
        while (node && node !== document.body) {
            var bg = getComputedStyle(node).backgroundColor;
            if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
                var m = bg.match(/\d+/g);
                if (m) {
                    dark = (parseInt(m[0]) * 299 + parseInt(m[1]) * 587 + parseInt(m[2]) * 114) / 1000 < 128;
                }
                break;
            }
            node = node.parentElement;
        }
        if (dark) { bar.classList.add('is-dark'); } else { bar.classList.remove('is-dark'); }
    }

    var ticking = false;
    window.addEventListener('scroll', function() {
        if (!ticking) { requestAnimationFrame(function() { checkBg(); ticking = false; }); ticking = true; }
    }, { passive: true });
    checkBg();
    setTimeout(checkBg, 300);
})();
</script>
