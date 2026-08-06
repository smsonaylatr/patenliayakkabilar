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
            ->renderHook(
                \Filament\View\PanelsRenderHook::HEAD_END,
                fn () => new \Illuminate\Support\HtmlString('
                    <style>
                        /* Siparişler tablosunda akordiyon açılsa bile sağdaki ikonların en üstte sabit kalması */
                        .fi-ta-record-actions,
                        .fi-ta-actions,
                        td.fi-ta-actions-cell {
                            align-self: flex-start !important;
                            vertical-align: top !important;
                            padding-top: 14px !important;
                        }

                        /* Split layout konteyneri esnetme */
                        .fi-ta-split,
                        div.fi-ta-split {
                            flex: 1 1 auto !important;
                            width: 100% !important;
                            justify-content: space-between !important;
                            gap: 12px !important;
                        }

                        /* Akordiyon Panelinin ve İçerik Kapsayıcısının Tam Genişlik Sıfırlaması */
                        .fi-ta-panel,
                        div.fi-ta-panel,
                        .order-detail-panel {
                            width: 100% !important;
                            max-width: 100% !important;
                            margin-left: 0 !important;
                            margin-right: 0 !important;
                            box-sizing: border-box !important;
                        }

                        /* Kapsayıcı hücrelerin iç boşluk sıfırlaması */
                        .fi-ta-content,
                        .fi-ta-panel-cell,
                        tr.fi-ta-panel-row > td,
            ->renderHook(
                \Filament\Tables\View\TablesRenderHook::TOOLBAR_AFTER,
                fn () => request()->routeIs('filament.admin.resources.orders.index') ? new \Illuminate\Support\HtmlString('
                    <div class="fi-ta-orders-header hidden md:grid w-full bg-[#0d111b] text-gray-400 font-extrabold text-[11px] uppercase tracking-wider border-b border-gray-800/80 rounded-t-xl my-2 select-none" style="display: grid !important; grid-template-columns: 120px minmax(180px, 1fr) 90px 110px 130px 120px 110px !important; align-items: center !important; width: 100% !important; padding-left: 68px !important; padding-right: 150px !important; padding-top: 12px !important; padding-bottom: 12px !important; box-sizing: border-box !important; clear: both !important;">
                        <div style="text-align: left;">#SİPARİŞ</div>
                        <div style="text-align: left;">MÜŞTERİ</div>
                        <div style="text-align: center;">ÜRÜNLER</div>
                        <div style="text-align: right; padding-right: 10px;">TUTAR</div>
                        <div style="text-align: center;">ÖDEME</div>
                        <div style="text-align: center;">TARİH</div>
                        <div style="text-align: center;">DURUM</div>
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

                                if (target.closest("button, select, input, a, form, label, .fi-ta-action, [role=\"button\"], .fi-badge, .fi-dropdown-trigger")) {
                                    return;
                                }

                                const row = target.closest("tr, .fi-ta-row, .fi-ta-record, .fi-ta-split");
                                if (!row) return;

                                const container = row.closest("tr, .fi-ta-record, tbody, table") || row.parentElement;
                                if (!container) return;

                                let trigger = container.querySelector("button[x-on\\\\:click*=\"isCollapsed\"], .fi-ta-collapsible-trigger, button[aria-expanded]");
                                
                                if (!trigger && container.parentElement) {
                                    trigger = container.parentElement.querySelector("button[x-on\\\\:click*=\"isCollapsed\"], .fi-ta-collapsible-trigger, button[aria-expanded]");
                                }

                                if (trigger) {
                                    trigger.click();
                                    return;
                                }

                                if (window.Alpine) {
                                    const alpineEl = target.closest("[x-data]") || container.querySelector("[x-data]");
                                    if (alpineEl && window.Alpine.$data(alpineEl)) {
                                        const data = window.Alpine.$data(alpineEl);
                                        if (typeof data.isCollapsed !== "undefined") {
                                            data.isCollapsed = !data.isCollapsed;
                                        }
                                    }
                                }
                            }, true);
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
