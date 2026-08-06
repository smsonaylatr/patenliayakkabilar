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
                        /* Siparişler tablosunda genel okunaklı kompakt font boyutu (12px / 0.76rem) */
                        .fi-ta-record,
                        .fi-ta-row,
                        .fi-ta-cell,
                        .fi-ta-split,
                        .fi-ta-split div,
                        .fi-ta-split span,
                        .fi-ta-split p {
                            font-size: 0.76rem !important;
                            line-height: 1.25rem !important;
                        }

                        /* Durum Rozeti kompakt boyutu */
                        .fi-ta-split .fi-badge {
                            font-size: 10px !important;
                            padding: 2px 8px !important;
                        }

                        /* Müşteri Alt E-posta Metni ufaltma */
                        .fi-ta-split .text-xs,
                        .fi-ta-split .text-sm,
                        .fi-ta-split .fi-ta-text-item-description {
                            font-size: 0.7rem !important;
                            opacity: 0.7 !important;
                        }

                        /* İşlem ikonlarını yukarı çekerek satır metinleriyle 1:1 dikey hizalama */
                        .fi-ta-record-actions,
                        .fi-ta-actions,
                        td.fi-ta-actions-cell {
                            display: flex !important;
                            align-items: center !important;
                            align-self: flex-start !important;
                            justify-content: flex-end !important;
                            gap: 3px !important;
                            transform: translateY(-5px) !important;
                            padding-top: 0 !important;
                            padding-bottom: 0 !important;
                            margin-top: 0 !important;
                        }

                        /* Sort by ve Bulk Indicator temizleme */
                        .fi-ta-header-toolbar,
                        .fi-ta-selection-indicator,
                        .fi-ta-selection-indicator-header {
                            display: none !important;
                        }

                        /* İşlem ikonları ideal boyutu (18px) */
                        .fi-ta-actions svg,
                        .fi-ta-record-actions svg,
                        td.fi-ta-actions-cell svg,
                        .fi-ta-cell svg {
                            width: 18px !important;
                            height: 18px !important;
                        }

                        .fi-ta-actions button,
                        .fi-ta-record-actions button,
                        .fi-ta-actions a,
                        .fi-icon-btn {
                            padding: 2px !important;
                            margin: 0 !important;
                            display: inline-flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }

                        /* Split layout ve Başlık çubuğu flex ve gap eşitlemesi */
                        .fi-ta-orders-header,
                        .fi-ta-split,
                        div.fi-ta-split {
                            display: flex !important;
                            flex-direction: row !important;
                            align-items: center !important;
                            justify-content: flex-start !important;
                            gap: 16px !important;
                        }

                        /* Sütun 1: MÜŞTERİ (Başlıkta 2. div çünkü 1. div checkbox) */
                        .fi-ta-orders-header > div:nth-child(2),
                        .fi-ta-split > div:nth-child(1) {
                            width: 240px !important;
                            min-width: 240px !important;
                            max-width: 240px !important;
                            flex: 0 0 240px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Sütun 2: ŞEHİR */
                        .fi-ta-orders-header > div:nth-child(3),
                        .fi-ta-split > div:nth-child(2) {
                            width: 100px !important;
                            min-width: 100px !important;
                            max-width: 100px !important;
                            flex: 0 0 100px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Sütun 3: TUTAR */
                        .fi-ta-orders-header > div:nth-child(4),
                        .fi-ta-split > div:nth-child(3) {
                            width: 90px !important;
                            min-width: 90px !important;
                            max-width: 90px !important;
                            flex: 0 0 90px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Sütun 4: ÖDEME */
                        .fi-ta-orders-header > div:nth-child(5),
                        .fi-ta-split > div:nth-child(4) {
                            width: 110px !important;
                            min-width: 110px !important;
                            max-width: 110px !important;
                            flex: 0 0 110px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Sütun 5: TARİH */
                        .fi-ta-orders-header > div:nth-child(6),
                        .fi-ta-split > div:nth-child(5) {
                            width: 160px !important;
                            min-width: 160px !important;
                            max-width: 160px !important;
                            flex: 0 0 160px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Sütun 6: DURUM */
                        .fi-ta-orders-header > div:nth-child(7),
                        .fi-ta-split > div:nth-child(6) {
                            width: 105px !important;
                            min-width: 105px !important;
                            max-width: 105px !important;
                            flex: 0 0 105px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Sütun 7: ÖDEME DURUMU */
                        .fi-ta-orders-header > div:nth-child(8),
                        .fi-ta-split > div:nth-child(7) {
                            width: 115px !important;
                            min-width: 115px !important;
                            max-width: 115px !important;
                            flex: 0 0 115px !important;
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Tablodaki TÜM başlıklar, metinler ve rozetleri KESİN SOLA YASLA */
                        .fi-ta-orders-header > div,
                        .fi-ta-split > div,
                        .fi-ta-content td,
                        .fi-ta-cell,
                        .fi-ta-text-item,
                        .fi-ta-badge,
                        .fi-badge {
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* Kapsayıcı hücrelerin iç boşluk sıfırlaması */
                        .fi-ta-record-content {
                            width: 100% !important;
                            padding-left: 0 !important;
                            padding-right: 0 !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                \Filament\Tables\View\TablesRenderHook::HEADER_AFTER,
                fn () => request()->routeIs('filament.admin.resources.orders.index') ? new \Illuminate\Support\HtmlString('
                    <div class="fi-ta-orders-header hidden md:flex w-full bg-[#0d111b] text-gray-400 font-extrabold text-[11px] uppercase tracking-wider border-b border-gray-800/80 rounded-t-xl my-1 select-none" style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important; padding-left: 24px !important; padding-right: 140px !important; padding-top: 10px !important; padding-bottom: 10px !important; gap: 16px !important; box-sizing: border-box !important; margin-bottom: 6px !important;">
                        <div style="display: flex; align-items: center; width: 44px; flex-shrink: 0; padding-left: 4px;">
                            <input type="checkbox" class="fi-checkbox-input rounded border-gray-700 bg-gray-900 text-primary-600 shadow-sm focus:ring-primary-600 cursor-pointer" onclick="const isChecked = this.checked; document.querySelectorAll(\'tbody input[type=checkbox], .fi-ta-record-checkbox input\').forEach(cb => { if (cb !== this && cb.checked !== isChecked) { cb.click(); } });" style="width: 16px; height: 16px;" title="Tümünü Seç">
                        </div>
                        <div style="flex: 0 0 250px; text-align: left;">MÜŞTERİ</div>
                        <div style="flex: 0 0 100px; text-align: left;">ŞEHİR</div>
                        <div style="flex: 0 0 90px; text-align: left;">TUTAR</div>
                        <div style="flex: 0 0 110px; text-align: left;">ÖDEME</div>
                        <div style="flex: 0 0 160px; text-align: left;">TARİH</div>
                        <div style="flex: 0 0 105px; text-align: left;">DURUM</div>
                        <div style="flex: 0 0 115px; text-align: left;">ÖDEME DURUMU</div>
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
