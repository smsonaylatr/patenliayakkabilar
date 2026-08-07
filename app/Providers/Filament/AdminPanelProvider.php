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
                        /* ============================================== */
                        /* SİPARİŞLER TABLOSU — YÜZDE BAZLI FLEX LAYOUT  */
                        /* ============================================== */

                        /* Genel font boyutu */
                        .fi-ta-record,
                        .fi-ta-row,
                        .fi-ta-cell,
                        .order-split-row,
                        .order-split-row div,
                        .order-split-row span,
                        .order-split-row p {
                            font-size: 0.78rem !important;
                            line-height: 1.3rem !important;
                        }

                        /* Satır ayırıcılar */
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

                        /* Badge boyutu */
                        .order-split-row .fi-badge {
                            font-size: 10px !important;
                            padding: 2px 7px !important;
                            white-space: nowrap !important;
                        }

                        /* Müşteri e-posta açıklaması */
                        .order-split-row .fi-ta-text-item-description {
                            font-size: 0.68rem !important;
                            opacity: 0.65 !important;
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                            white-space: nowrap !important;
                        }

                        /* Tüm order-col hücreleri: taşma engelleme */
                        .order-col {
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                            white-space: nowrap !important;
                            min-width: 0 !important;
                        }

                        /* Split row — tam genişlik flex satır */
                        .order-split-row {
                            display: flex !important;
                            flex-direction: row !important;
                            align-items: center !important;
                            width: 100% !important;
                            gap: 0 !important;
                            box-sizing: border-box !important;
                        }

                        /* İşlem butonları */
                        .fi-ta-record-actions,
                        .fi-ta-actions,
                        td.fi-ta-actions-cell {
                            display: flex !important;
                            align-items: center !important;
                            justify-content: flex-end !important;
                            gap: 2px !important;
                            padding: 0 4px !important;
                            flex-shrink: 0 !important;
                        }
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
                            padding: 3px !important;
                            margin: 0 !important;
                            display: inline-flex !important;
                            align-items: center !important;
                            justify-content: center !important;
                        }

                        /* Tablo container birleşik */
                        .fi-ta-ctn,
                        .fi-ta-content-ctn,
                        .fi-ta-content,
                        .fi-ta-table-container {
                            gap: 0 !important;
                            margin-top: 0 !important;
                            overflow-x: hidden !important;
                        }

                        /* Filament native header gizle */
                        .fi-ta-header-toolbar,
                        .fi-ta-content-header {
                            display: none !important;
                        }

                        /* Record content genişlik */
                        .fi-ta-record-content,
                        div.fi-ta-record-content.fi-collapsible,
                        .fi-ta-record-content.fi-collapsible {
                            width: 100% !important;
                            padding-left: 0 !important;
                            padding-right: 0 !important;
                        }

                        /* Akordiyon ok animasyonu */
                        .fi-ta-collapsible-trigger svg,
                        button[aria-expanded] svg {
                            transform: rotate(180deg) !important;
                            transition: transform 0.2s ease !important;
                        }
                        button[aria-expanded="true"] svg {
                            transform: rotate(0deg) !important;
                        }

                        /* Badge ve metin sola hizala */
                        .order-col,
                        .fi-ta-text-item,
                        .fi-ta-badge,
                        .fi-badge {
                            text-align: left !important;
                            justify-content: flex-start !important;
                        }

                        /* ============================================== */
                        /* MASAÜSTÜ (≥768px) — YÜZDE BAZLI SÜTUNLAR      */
                        /* ============================================== */
                        @media (min-width: 768px) {
                            /* Custom header bar */
                            .fi-ta-orders-header {
                                display: flex !important;
                                flex-direction: row !important;
                                align-items: center !important;
                                width: 100% !important;
                                gap: 0 !important;
                                box-sizing: border-box !important;
                                padding: 10px 8px !important;
                                margin: 0 !important;
                                background: transparent !important;
                                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                                border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
                                border-top-left-radius: 12px !important;
                                border-top-right-radius: 12px !important;
                                border-bottom-left-radius: 0 !important;
                                border-bottom-right-radius: 0 !important;
                            }

                            /* İçerik tablosu border */
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

                            /* Split veri satırı */
                            .order-split-row {
                                gap: 0 !important;
                                padding-left: 0 !important;
                                padding-right: 0 !important;
                            }

                            /* Sütun genişlikleri — YÜZDE */
                            .order-col-customer  { flex: 0 0 22% !important; width: 22% !important; max-width: 22% !important; }
                            .order-col-city      { flex: 0 0 9%  !important; width: 9%  !important; max-width: 9%  !important; }
                            .order-col-total     { flex: 0 0 8%  !important; width: 8%  !important; max-width: 8%  !important; }
                            .order-col-payment   { flex: 0 0 12% !important; width: 12% !important; max-width: 12% !important; }
                            .order-col-date      { flex: 0 0 17% !important; width: 17% !important; max-width: 17% !important; }
                            .order-col-status    { flex: 0 0 12% !important; width: 12% !important; max-width: 12% !important; }
                            .order-col-payment-status { flex: 0 0 10% !important; width: 10% !important; max-width: 10% !important; }

                            /* Header sütun genişlikleri — aynı yüzde */
                            .fi-ta-orders-header > .oh-checkbox { flex: 0 0 32px !important; width: 32px !important; }
                            .fi-ta-orders-header > .oh-customer  { flex: 0 0 22% !important; }
                            .fi-ta-orders-header > .oh-city      { flex: 0 0 9%  !important; }
                            .fi-ta-orders-header > .oh-total     { flex: 0 0 8%  !important; }
                            .fi-ta-orders-header > .oh-payment   { flex: 0 0 12% !important; }
                            .fi-ta-orders-header > .oh-date      { flex: 0 0 17% !important; }
                            .fi-ta-orders-header > .oh-status    { flex: 0 0 12% !important; }
                            .fi-ta-orders-header > .oh-pay-status { flex: 0 0 10% !important; }

                            /* İşlem butonları alanı */
                            .fi-ta-record-actions,
                            .fi-ta-actions,
                            td.fi-ta-actions-cell {
                                flex: 0 0 auto !important;
                                width: auto !important;
                                min-width: unset !important;
                                max-width: unset !important;
                                transform: none !important;
                            }

                            /* fi-growable buton sıfırlama */
                            .order-split-row button.fi-growable,
                            button.fi-grid-col.fi-growable.fi-ta-col {
                                flex-grow: 0 !important;
                            }
                        }

                        /* ============================================== */
                        /* MOBİL (<768px) — TEK SÜTUN                     */
                        /* ============================================== */
                        @media (max-width: 767px) {
                            .fi-ta-orders-header {
                                display: none !important;
                            }

                            .order-split-row {
                                display: flex !important;
                                flex-direction: column !important;
                                align-items: flex-start !important;
                                gap: 6px !important;
                                width: 100% !important;
                                padding: 10px !important;
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
                    <div id="custom-orders-header-bar" wire:key="orders-header-bar-persistent" wire:ignore class="fi-ta-orders-header hidden md:flex w-full text-gray-400 font-extrabold text-[11px] uppercase tracking-wider select-none">
                        <div class="oh-checkbox" style="display: flex; align-items: center; justify-content: center;">
                            <input type="checkbox" class="fi-checkbox-input rounded border-gray-700 bg-gray-900 text-primary-600 shadow-sm focus:ring-primary-600 cursor-pointer" onclick="window.toggleSelectAllOrders && window.toggleSelectAllOrders(this.checked)" style="width: 16px; height: 16px;" title="Tümünü Seç">
                        </div>
                        <div class="oh-customer">MÜŞTERİ</div>
                        <div class="oh-city">ŞEHİR</div>
                        <div class="oh-total">TUTAR</div>
                        <div class="oh-payment">ÖDEME</div>
                        <div class="oh-date">TARİH</div>
                        <div class="oh-status">DURUM</div>
                        <div class="oh-pay-status">ÖDEME DURUMU</div>
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

                                const row = target.closest("tr, .fi-ta-row, .fi-ta-record, .order-split-row");
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
                                
                                var header = document.querySelector(".fi-ta-orders-header");
                                if (header) {
                                    var next = header.nextElementSibling;
                                    while (next) {
                                        if (next.querySelector(".order-split-row")) break;
                                        var cb = next.querySelector("input[type=checkbox]");
                                        if (cb && !next.querySelector(".order-split-row")) {
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
                                
                                document.querySelectorAll("div, tr, td, th, label").forEach(function(el) {
                                    if (el.classList.contains("fi-ta-orders-header")) return;
                                    if (el.querySelector(".order-split-row")) return;
                                    if (el.closest(".fi-ta-orders-header")) return;
                                    if (el.closest(".order-split-row")) return;
                                    
                                    var inputs = el.querySelectorAll("input[type=checkbox]");
                                    var children = el.children;
                                    
                                    if (inputs.length === 1 && children.length <= 2) {
                                        var text = el.textContent.replace(/\\s/g, "");
                                        if (text.length === 0) {
                                            var parent = el.parentElement;
                                            if (parent && parent.querySelector(".fi-ta-orders-header") && parent.querySelector(".order-split-row")) {
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
