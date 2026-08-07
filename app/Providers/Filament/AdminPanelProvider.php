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

                        /* Tablo satır ayırıcı çizgiler: ince beyaz */
                        .fi-ta-record,
                        .fi-ta-row,
                        tr {
                            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                            border-top: none !important;
                        }
                        .fi-ta-record:last-child,
                        .fi-ta-row:last-child,
                        tr:last-child {
                            border-bottom: none !important;
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
                            justify-content: flex-start !important;
                            gap: 3px !important;
                            transform: translateY(-5px) !important;
                            padding-top: 0 !important;
                            padding-bottom: 0 !important;
                            margin-top: 0 !important;
                            width: 96px !important;
                            min-width: 96px !important;
                            max-width: 96px !important;
                            flex: 0 0 96px !important;
                        }

                        /* Sort by ve native checkbox header gizleme */
                        .fi-ta-header-toolbar,
                        .fi-ta-content-header {
                            display: none !important;
                        }

                        /* İşlem ikonları ideal boyutu (20px) */
                        .fi-ta-actions svg,
                        .fi-ta-record-actions svg,
                        td.fi-ta-actions-cell svg,
                        .fi-ta-cell svg {
                            width: 20px !important;
                            height: 20px !important;
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

                        /* Altın Oran Sütun Genişlikleri (φ = 1.618) */

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

                        /* Akordiyon oku: kapalıyken aşağı (v), açıkken yukarı (^) */
                        .fi-ta-collapsible-trigger svg,
                        button[aria-expanded] svg {
                            transform: rotate(180deg) !important;
                            transition: transform 0.2s ease !important;
                        }
                        button[aria-expanded="true"] svg {
                            transform: rotate(0deg) !important;
                        }

                        /* Tablo başlık çubuğu ile tablo kutusunun yekpare birleştirilmesi ve scrollbar engelleme */
                        .fi-ta-ctn,
                        .fi-ta-content-ctn,
                        .fi-ta-content,
                        .fi-ta-table-container {
                            gap: 0 !important;
                            margin-top: 0 !important;
                            overflow-x: hidden !important;
                        }
                        .fi-ta-orders-header {
                            margin: 0 !important;
                            background: transparent !important;
                            border: 1px solid rgba(255, 255, 255, 0.08) !important;
                            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                            border-top-left-radius: 12px !important;
                            border-top-right-radius: 12px !important;
                            border-bottom-left-radius: 0 !important;
                            border-bottom-right-radius: 0 !important;
                        }
                        .fi-ta-content {
                            background: transparent !important;
                            border: 1px solid rgba(255, 255, 255, 0.08) !important;
                            border-top: none !important;
                            border-top-left-radius: 0 !important;
                            border-top-right-radius: 0 !important;
                            border-bottom-left-radius: 12px !important;
                            border-bottom-right-radius: 12px !important;
                            box-shadow: none !important;
                        }

                        /* DevTools: fi-ta-record-content.fi-collapsible genişlik ayarı */
                        div.fi-ta-record-content.fi-collapsible,
                        .fi-ta-record-content.fi-collapsible {
                            width: 100% !important;
                            padding-left: 0 !important;
                            padding-right: 0 !important;
                        }

                        /* ======================================================== */
                        /* MASAÜSTÜ GÖRÜNÜMÜ (min-width: 768px)                     */
                        /* ======================================================== */
                        @media (min-width: 768px) {
                            /* Başlık çubuğu flex, padding ve gap ayarları */
                            .fi-ta-orders-header {
                                display: flex !important;
                                flex-direction: row !important;
                                align-items: center !important;
                                gap: 6px !important;
                                box-sizing: border-box !important;
                                padding-left: 8px !important;
                                padding-right: 8px !important;
                            }

                            /* Veri satırının padding ve gap ayarları */
                            .fi-ta-split, div.fi-ta-split {
                                display: flex !important;
                                flex-direction: row !important;
                                align-items: center !important;
                                gap: 6px !important;
                                box-sizing: border-box !important;
                                padding-left: 0 !important;
                                width: 100% !important;
                            }

                            /* Checkbox alanı */
                            .fi-ta-orders-header > div:nth-child(1) {
                                width: 28px !important;
                                min-width: 28px !important;
                                max-width: 28px !important;
                                flex: 0 0 28px !important;
                            }

                            /* Masaüstü başlık çubuğu sütun kilitleri */
                            .fi-ta-orders-header > div:nth-child(2) { flex: 0 0 155px !important; width: 155px !important; text-align: left !important; }
                            .fi-ta-orders-header > div:nth-child(3) { flex: 0 0 60px !important; width: 60px !important; text-align: left !important; }
                            .fi-ta-orders-header > div:nth-child(4) { flex: 0 0 55px !important; width: 55px !important; text-align: left !important; }
                            .fi-ta-orders-header > div:nth-child(5) { flex: 0 0 75px !important; width: 75px !important; text-align: left !important; }
                            .fi-ta-orders-header > div:nth-child(6) { flex: 0 0 115px !important; width: 115px !important; text-align: left !important; }
                            .fi-ta-orders-header > div:nth-child(7) { flex: 0 0 75px !important; width: 75px !important; text-align: left !important; }
                            .fi-ta-orders-header > div:nth-child(8) { flex: 0 0 75px !important; width: 75px !important; text-align: left !important; }

                            /* Masaüstü veri sütun kilitleri */
                            .order-col-customer { flex: 0 0 155px !important; width: 155px !important; min-width: 155px !important; max-width: 155px !important; text-align: left !important; }
                            .order-col-city { flex: 0 0 60px !important; width: 60px !important; min-width: 60px !important; max-width: 60px !important; text-align: left !important; }
                            .order-col-total { flex: 0 0 55px !important; width: 55px !important; min-width: 55px !important; max-width: 55px !important; text-align: left !important; }
                            .order-col-payment { flex: 0 0 75px !important; width: 75px !important; min-width: 75px !important; max-width: 75px !important; text-align: left !important; }
                            .order-col-date { flex: 0 0 115px !important; width: 115px !important; min-width: 115px !important; max-width: 115px !important; text-align: left !important; }
                            .order-col-status { flex: 0 0 75px !important; width: 75px !important; min-width: 75px !important; max-width: 75px !important; text-align: left !important; }
                            .order-col-payment-status { flex: 0 0 75px !important; width: 75px !important; min-width: 75px !important; max-width: 75px !important; text-align: left !important; }

                            /* Mor taralı fi-growable buton esnemesini sıfırlama */
                            .fi-ta-split button.fi-growable,
                            button.fi-grid-col.fi-growable.fi-ta-col {
                                flex-grow: 0 !important;
                                width: 75px !important;
                                min-width: 75px !important;
                                max-width: 75px !important;
                                flex: 0 0 75px !important;
                            }

                            /* Masaüstü İŞLEMLER HÜCRESİ: 4 ikonun sığması için 85px kilit */
                            .fi-ta-record-actions,
                            .fi-ta-actions,
                            td.fi-ta-actions-cell {
                                width: 85px !important;
                                min-width: 85px !important;
                                max-width: 85px !important;
                                flex: 0 0 85px !important;
                                justify-content: flex-start !important;
                                gap: 2px !important;
                            }
                        }

                        /* ======================================================== */
                        /* MOBİL GÖRÜNÜM (max-width: 767px)                          */
                        /* ======================================================== */
                        @media (max-width: 767px) {
                            .fi-ta-orders-header {
                                display: none !important;
                            }

                            .fi-ta-split, div.fi-ta-split {
                                display: flex !important;
                                flex-direction: column !important;
                                align-items: flex-start !important;
                                gap: 8px !important;
                                width: 100% !important;
                                padding: 12px 10px !important;
                                box-sizing: border-box !important;
                            }

                            .order-col-customer,
                            .order-col-city,
                            .order-col-total,
                            .order-col-payment,
                            .order-col-date,
                            .order-col-status,
                            .order-col-payment-status {
                                width: 100% !important;
                                max-width: 100% !important;
                                flex: 1 1 100% !important;
                                text-align: left !important;
                            }

                            .fi-ta-record-actions,
                            .fi-ta-actions,
                            td.fi-ta-actions-cell {
                                width: 100% !important;
                                max-width: 100% !important;
                                flex: 1 1 100% !important;
                                justify-content: flex-start !important;
                                margin-top: 6px !important;
                                padding-top: 6px !important;
                                border-top: 1px dashed rgba(255, 255, 255, 0.1) !important;
                                gap: 8px !important;
                            }
                        }
                    </style>
                ')
            )
            ->renderHook(
                \Filament\Tables\View\TablesRenderHook::HEADER_BEFORE,
                fn () => (request()->routeIs('filament.admin.resources.orders.*') || request()->is('admin/orders*') || str_contains(request()->header('referer', ''), '/admin/orders')) ? new \Illuminate\Support\HtmlString('
                    <div id="custom-orders-header-bar" wire:key="orders-header-bar-persistent" wire:ignore class="fi-ta-orders-header hidden md:flex w-full text-gray-400 font-extrabold text-[11px] uppercase tracking-wider select-none" style="display: flex !important; flex-direction: row !important; align-items: center !important; width: 100% !important; padding: 12px 0 !important; gap: 6px !important; box-sizing: border-box !important; margin: 0 !important; border: 1px solid rgba(255, 255, 255, 0.08) !important; border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important; border-top-left-radius: 12px !important; border-top-right-radius: 12px !important; border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important; background: transparent !important;">
                        <div style="display: flex; align-items: center; justify-content: flex-start; width: 28px; min-width: 28px; flex: 0 0 28px; flex-shrink: 0; padding-left: 0; box-sizing: border-box;">
                            <input type="checkbox" class="fi-checkbox-input rounded border-gray-700 bg-gray-900 text-primary-600 shadow-sm focus:ring-primary-600 cursor-pointer" onclick="window.toggleSelectAllOrders && window.toggleSelectAllOrders(this.checked)" style="width: 16px; height: 16px;" title="Tümünü Seç">
                        </div>
                        <div style="flex: 0 0 155px; text-align: left;">MÜŞTERİ</div>
                        <div style="flex: 0 0 60px; text-align: left;">ŞEHİR</div>
                        <div style="flex: 0 0 55px; text-align: left;">TUTAR</div>
                        <div style="flex: 0 0 75px; text-align: left;">ÖDEME</div>
                        <div style="flex: 0 0 115px; text-align: left;">TARİH</div>
                        <div style="flex: 0 0 75px; text-align: left;">DURUM</div>
                        <div style="flex: 0 0 75px; text-align: left; white-space: nowrap !important;">ÖDEME DURUMU</div>
                    </div>
                ') : null
            )
            ->renderHook(
                \Filament\View\PanelsRenderHook::BODY_END,
                fn () => new \Illuminate\Support\HtmlString('
                    <script>
                        (function() {
                            window.toggleSelectAllOrders = function(isChecked) {
                                const nativeSelectAll = document.querySelector(".fi-ta-content-header input[type=checkbox], thead input[type=checkbox], [aria-label*=\"Select/deselect\"]");
                                if (nativeSelectAll) {
                                    if (nativeSelectAll.checked !== isChecked) {
                                        nativeSelectAll.click();
                                    }
                                } else {
                                    document.querySelectorAll("tbody input[type=checkbox], .fi-ta-record-checkbox input").forEach(function(cb) {
                                        if (cb.checked !== isChecked) {
                                            cb.click();
                                        }
                                    });
                                }
                            };

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

                            // Bulk action checkbox row - hide from DOM
                            function hideBulkCheckboxRow() {
                                if (!window.location.pathname.includes("/admin/orders")) return;
                                
                                // Method 1: Traverse siblings after custom header
                                var header = document.querySelector(".fi-ta-orders-header");
                                if (header) {
                                    var next = header.nextElementSibling;
                                    while (next) {
                                        if (next.querySelector(".fi-ta-split")) break;
                                        var cb = next.querySelector("input[type=checkbox]");
                                        if (cb && !next.querySelector(".fi-ta-split")) {
                                            next.style.display = "none";
                                            next.style.height = "0";
                                            next.style.overflow = "hidden";
                                            next.style.padding = "0";
                                            next.style.margin = "0";
                                            next.style.border = "none";
                                        }
                                        next = next.nextElementSibling;
                                    }
                                }
                                
                                // Method 2: Find checkbox-only elements
                                document.querySelectorAll("div, tr, td, th, label").forEach(function(el) {
                                    if (el.classList.contains("fi-ta-orders-header")) return;
                                    if (el.querySelector(".fi-ta-split")) return;
                                    if (el.closest(".fi-ta-orders-header")) return;
                                    if (el.closest(".fi-ta-split")) return;
                                    
                                    var inputs = el.querySelectorAll("input[type=checkbox]");
                                    var children = el.children;
                                    
                                    if (inputs.length === 1 && children.length <= 2) {
                                        var text = el.textContent.replace(/\\s/g, "");
                                        if (text.length === 0) {
                                            var parent = el.parentElement;
                                            if (parent && parent.querySelector(".fi-ta-orders-header") && parent.querySelector(".fi-ta-split")) {
                                                el.style.display = "none";
                                            }
                                        }
                                    }
                                });
                            }
                            
                            setTimeout(hideBulkCheckboxRow, 100);
                            setTimeout(hideBulkCheckboxRow, 500);
                            setTimeout(hideBulkCheckboxRow, 1500);
                            document.addEventListener("livewire:navigated", function() { setTimeout(hideBulkCheckboxRow, 200); });
                            new MutationObserver(function() { setTimeout(hideBulkCheckboxRow, 80); })
                                .observe(document.body, { childList: true, subtree: true });
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
