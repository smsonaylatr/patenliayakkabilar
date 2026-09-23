{{-- Floating Social Sidebar + İndirim Kuponu Widget --}}
{{-- Halıköy temasındaki newsletter-bar yapısının birebir kopyası --}}
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
        top: 50vh;
        top: 50svh;
        transform: translateY(-50%);
        width: 3.25rem;
        margin-left: 1rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        backdrop-filter: blur(6px);
        background-color: rgba(255, 255, 255, 0.8);
        box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.1);
        border-radius: 9999px;
        z-index: 30;
        display: none;
    }

    @media screen and (min-width: 768px) {
        .newsletter-bar {
            display: grid;
            gap: 0.75rem;
        }
    }

    @media screen and (min-width: 640px) {
        .newsletter-bar {
            margin-left: 1.5rem;
        }
    }

    .newsletter-bar__social ul {
        padding-top: 0.25rem;
        flex-direction: column;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.125rem;
        list-style: none;
        margin: 0;
        padding-left: 0;
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
        color: inherit;
        text-decoration: none;
    }

    .newsletter-bar__social .social_platform svg {
        width: 1rem;
        height: 1rem;
        fill: currentColor;
    }

    .newsletter-bar__button {
        writing-mode: vertical-rl;
        transform: rotate(-180deg);
        font-size: 0.625rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        font-weight: 500;
        padding-left: 1rem;
        padding-right: 1rem;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        color: #171717;
        background-color: rgba(23, 23, 23, 0.045);
        border-radius: 9999px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        border: none;
    }

    @media (pointer: fine) {
        .newsletter-bar__button:hover span {
            animation: beat 0.6s infinite ease;
        }
    }

    @keyframes beat {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
</style>

<div class="newsletter-bar">
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
        </ul>
    </div>
    @endif

    {{-- %5 İNDİRİM Butonu --}}
    <a href="{{ route('products.index') }}" class="newsletter-bar__button" wire:navigate>
        <span>%5 İNDİRİM</span>
    </a>
</div>
