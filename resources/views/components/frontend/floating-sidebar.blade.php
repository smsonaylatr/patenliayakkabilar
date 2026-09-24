{{-- Floating Social Sidebar + İndirim Kuponu Widget --}}
{{-- Beyaz zeminde opak siyah, siyah zeminde opak beyaz --}}
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
        width: 2.75rem;
        margin-left: 0.5rem;
        padding-top: 0.375rem;
        padding-bottom: 0.375rem;
        backdrop-filter: saturate(180%) blur(20px);
        -webkit-backdrop-filter: saturate(180%) blur(20px);
        background-color: rgba(22, 22, 23, 0.8);
        border: 0.5px solid rgba(255, 255, 255, 0.18);
        border-radius: 9999px;
        z-index: 30;
        display: none;
    }

    @media screen and (min-width: 768px) {
        .newsletter-bar {
            display: grid;
            gap: 0.25rem;
        }
    }

    @media screen and (min-width: 1024px) {
        .newsletter-bar {
            margin-left: 0.875rem;
            width: 2.875rem;
        }
    }

    .newsletter-bar__social ul {
        padding-top: 0.125rem;
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
    }

    .newsletter-bar__social .social_platform {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: rgba(255, 255, 255, 0.92);
        text-decoration: none;
    }

    .newsletter-bar__social .social_platform:hover {
        color: rgba(255, 255, 255, 0.6);
    }

    .newsletter-bar__social .social_platform svg {
        width: 1rem;
        height: 1rem;
        fill: currentColor;
    }

    .newsletter-bar__button {
        writing-mode: vertical-rl;
        transform: rotate(-180deg);
        font-size: 0.5625rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        font-weight: 600;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.92);
        background-color: rgba(255, 255, 255, 0.08);
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
        background-color: rgba(255, 255, 255, 0.15);
    }

    /* ===== Açık mod: beyaz cam (koyu zemin üzerinde) ===== */
    .newsletter-bar.is-light {
        background-color: rgba(255, 255, 255, 0.72);
        border-color: rgba(0, 0, 0, 0.08);
    }

    .newsletter-bar.is-light .social_platform {
        color: rgba(0, 0, 0, 0.85);
    }

    .newsletter-bar.is-light .social_platform:hover {
        color: rgba(0, 0, 0, 0.5);
    }

    .newsletter-bar.is-light .newsletter-bar__button {
        color: rgba(0, 0, 0, 0.85);
        background-color: rgba(0, 0, 0, 0.05);
    }

    .newsletter-bar.is-light .newsletter-bar__button:hover {
        background-color: rgba(0, 0, 0, 0.1);
    }

    @media (pointer: fine) {
        .newsletter-bar__button:hover span {
            animation: nb-beat 0.6s infinite ease;
        }
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
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="presentation">
                        <path d="M13.397 20.997v-8.196h2.765l.411-3.209h-3.176V7.548c0-.926.258-1.56 1.587-1.56h1.684V3.127A22.336 22.336 0 0 0 14.201 3c-2.444 0-4.122 1.492-4.122 4.231v2.355H7.332v3.209h2.753v8.202h3.312z"/>
                    </svg>
                </a>
            </li>
            @endif
            @if(!empty($sidebarSettings['footer_instagram']))
            <li>
                <a target="_blank" rel="noopener noreferrer" href="{{ $sidebarSettings['footer_instagram'] }}" class="social_platform" aria-label="Instagram">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="presentation">
                        <path d="M12 3c-2.444 0-2.75.01-3.71.054-.959.044-1.613.196-2.185.418A4.412 4.412 0 0 0 4.51 4.511c-.5.5-.809 1.002-1.039 1.594-.222.572-.374 1.226-.418 2.184C3.01 9.25 3 9.556 3 12s.01 2.75.054 3.71c.044.959.196 1.613.418 2.186.23.592.538 1.094 1.039 1.595.5.5 1.002.808 1.594 1.038.572.222 1.226.374 2.184.418C9.25 20.99 9.556 21 12 21s2.75-.01 3.71-.054c.959-.044 1.613-.196 2.186-.419a4.412 4.412 0 0 0 1.595-1.038c.5-.5.808-1.002 1.038-1.594.222-.572.374-1.226.418-2.184.044-.96.054-1.267.054-3.711s-.01-2.75-.054-3.71c-.044-.959-.196-1.613-.418-2.185A4.412 4.412 0 0 0 19.49 4.51c-.5-.5-1.002-.809-1.594-1.039-.572-.222-1.226-.374-2.184-.418C14.75 3.01 14.444 3 12 3zm0 1.622c2.403 0 2.688.009 3.637.052.877.04 1.354.187 1.671.31.42.163.72.358 1.035.673.315.315.51.615.673 1.035.123.317.27.794.31 1.671.043.95.052 1.234.052 3.637s-.009 2.688-.052 3.637c-.04.877-.187 1.354-.31 1.671-.163.42-.358.72-.673 1.035a2.79 2.79 0 0 1-1.035.673c-.317.123-.794.27-1.671.31-.95.043-1.234.052-3.637.052s-2.688-.009-3.637-.052c-.877-.04-1.354-.187-1.671-.31a2.79 2.79 0 0 1-1.035-.673 2.79 2.79 0 0 1-.673-1.035c-.123-.317-.27-.794-.31-1.671-.043-.95-.052-1.234-.052-3.637s.009-2.688.052-3.637c.04-.877.187-1.354.31-1.671.163-.42.358-.72.673-1.035.315-.315.615-.51 1.035-.673.317-.123.794-.27 1.671-.31.95-.043 1.234-.052 3.637-.052zM12 7.378a4.622 4.622 0 1 0 0 9.244 4.622 4.622 0 0 0 0-9.244zM12 15a3 3 0 1 1 0-6 3 3 0 0 1 0 6zm5.884-7.804a1.08 1.08 0 1 1-2.16 0 1.08 1.08 0 0 1 2.16 0z"/>
                    </svg>
                </a>
            </li>
            @endif
            {{-- WhatsApp --}}
            <li>
                <a target="_blank" rel="noopener noreferrer" href="https://wa.me/908503073164" class="social_platform" aria-label="WhatsApp">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="presentation">
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

        function checkBg() {
            var rect = bar.getBoundingClientRect();
            var cx = rect.left + rect.width / 2;
            var cy = rect.top + rect.height / 2;

            bar.style.pointerEvents = 'none';
            var el = document.elementFromPoint(cx, cy);
            bar.style.pointerEvents = '';

            if (!el) return;

            var dark = false;
            var node = el;
            while (node && node !== document.body) {
                var bg = getComputedStyle(node).backgroundColor;
                if (bg && bg !== 'rgba(0, 0, 0, 0)' && bg !== 'transparent') {
                    var m = bg.match(/\d+/g);
                    if (m) {
                        var lum = (parseInt(m[0]) * 299 + parseInt(m[1]) * 587 + parseInt(m[2]) * 114) / 1000;
                        dark = lum < 128;
                    }
                    break;
                }
                node = node.parentElement;
            }

            bar.classList.toggle('is-light', dark);
        }

        var ticking = false;
        window.addEventListener('scroll', function() {
            if (!ticking) {
                requestAnimationFrame(function() { checkBg(); ticking = false; });
                ticking = true;
            }
        }, { passive: true });

        checkBg();
        setTimeout(checkBg, 500);
    })();
</script>
