<?php

namespace App\Providers\Filament;

use App\Models\User;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->authGuard('admin')
            ->brandName('Patenli Ayakkabılar')
            ->favicon(asset('favicon.ico'))
            ->font('Outfit')
            ->colors([
                'primary' => \Filament\Support\Colors\Color::hex('#ff4e00'),
                'gray' => \Filament\Support\Colors\Color::Slate,
            ])
            ->sidebarCollapsibleOnDesktop()
            ->spa()
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->darkMode()
            ->databaseNotifications()
            ->navigationGroups([
                'Dashboard' => \Filament\Navigation\NavigationGroup::make()->label('Ana Sayfa'),
                'Katalog Yönetimi' => \Filament\Navigation\NavigationGroup::make()->label('Katalog Yönetimi'),
                'Satışlar' => \Filament\Navigation\NavigationGroup::make()->label('Satışlar'),
                'Müşteriler' => \Filament\Navigation\NavigationGroup::make()->label('Müşteri İstihbaratı'),
                'İçerik' => \Filament\Navigation\NavigationGroup::make()->label('İçerik'),
                'İçerik Yönetimi' => \Filament\Navigation\NavigationGroup::make()->label('İçerik Yönetimi'),
                'İletişim' => \Filament\Navigation\NavigationGroup::make()->label('İletişim'),
                'Site Yönetimi' => \Filament\Navigation\NavigationGroup::make()->label('Site Yönetimi'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->databaseNotifications()
            ->renderHook(
                \Filament\Tables\View\TablesRenderHook::TOOLBAR_AFTER,
                fn () => request()->routeIs('filament.admin.resources.orders.index') ? new \Illuminate\Support\HtmlString('
                    <div class="fi-ta-orders-header hidden md:block w-full px-5 py-3 bg-[#0d111b] dark:bg-[#0d111b] text-gray-400 font-extrabold text-[11px] uppercase tracking-wider border-b border-gray-800 rounded-t-xl my-1 select-none">
                        <div class="flex items-center justify-between gap-4 w-full">
                            <div style="width: 15%; flex-shrink: 0;">#SİPARİŞ</div>
                            <div style="width: 25%; flex-shrink: 0;">MÜŞTERİ</div>
                            <div style="width: 10%; flex-shrink: 0; text-align: center;">ÜRÜNLER</div>
                            <div style="width: 12%; flex-shrink: 0; text-align: right;">TUTAR</div>
                            <div style="width: 12%; flex-shrink: 0; text-align: center;">ÖDEME</div>
                            <div style="width: 14%; flex-shrink: 0; text-align: center;">TARİH</div>
                            <div style="width: 12%; flex-shrink: 0; text-align: center;">DURUM</div>
                        </div>
                    </div>
                ') : null
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn () => new \Illuminate\Support\HtmlString('
                    <script>
                        (function() {
                            document.addEventListener("click", function (e) {
                                if (!window.location.pathname.includes("/admin/orders")) return;
                                
                                const target = e.target;
                                if (target.closest("button, select, input, a, form, label, .fi-ta-action, option, [role=\"button\"]")) {
                                    return;
                                }

                                const row = target.closest(".fi-ta-row, tr, .fi-ta-split");
                                if (!row) return;

                                const container = row.closest("tr, .fi-ta-record, div") || row.parentElement;
                                if (!container) return;

                                const trigger = container.querySelector(".fi-ta-collapsible-trigger, [x-on\\\\:click*=\"isCollapsed\"]") || container.querySelector("button");
                                if (trigger) {
                                    trigger.click();
                                }
                            });
                        })();
                    </script>
                ')
            );
    }

    public function boot(): void
    {
        \Filament\Tables\Table::configureUsing(function (\Filament\Tables\Table $table): void {
            $table
                ->defaultPaginationPageOption(50)
                ->paginationPageOptions([10, 25, 50, 100]);
        });
    }
}
