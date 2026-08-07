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
                        /* Tablo Genel Yazı ve İkon Boyutlarını Küçültme */
                        .fi-ta-header-cell {
                            font-size: 0.72rem !important;
                            padding-top: 8px !important;
                            padding-bottom: 8px !important;
                        }

                        .fi-ta-cell {
                            font-size: 0.8rem !important;
                            padding-top: 8px !important;
                            padding-bottom: 8px !important;
                        }

                        .fi-ta-cell .fi-badge {
                            font-size: 0.72rem !important;
                            padding: 2px 6px !important;
                        }

                        .fi-ta-actions button,
                        .fi-ta-actions a,
                        .fi-ta-actions .fi-icon-btn {
                            padding: 3px !important;
                        }

                        .fi-ta-actions svg {
                            width: 17px !important;
                            height: 17px !important;
                        }

                        /* Aksiyon Butonlarını Sola Yanaştırma */
                        .fi-ta-actions-cell,
                        .fi-ta-actions,
                        td.fi-ta-actions-cell {
                            display: flex !important;
                            justify-content: flex-start !important;
                            align-items: center !important;
                            gap: 4px !important;
                        }

                        /* Siparişler Sayfası Mobil Responsiveness & %50 Uzaklaştırma */
                        @media (max-width: 768px) {
                            /* Tablo Alanı Dokunmatik Yatay Kaydırma */
                            .fi-ta-ctn,
                            .fi-ta-content,
                            .fi-ta-table-ctn {
                                overflow-x: auto !important;
                                -webkit-overflow-scrolling: touch !important;
                                width: 100% !important;
                            }

                            /* Tablonun Kendisini Mobil Ekranda Kompakt Uzaklaştırma (Zoom) */
                            .fi-ta-table {
                                zoom: 0.75 !important;
                                min-width: 680px !important;
                            }

                            /* Hücre Dolgularını & Fontları Mobil İçin Optimize Etme */
                            .fi-ta-cell {
                                padding-top: 6px !important;
                                padding-bottom: 6px !important;
                                padding-left: 6px !important;
                                padding-right: 6px !important;
                                font-size: 0.78rem !important;
                            }

                            /* Aksiyon Butonları Hizalama */
                            .fi-ta-actions-cell,
                            .fi-ta-actions {
                                display: flex !important;
                                flex-direction: row !important;
                                align-items: center !important;
                                justify-content: flex-end !important;
                                gap: 4px !important;
                                white-space: nowrap !important;
                            }

                            /* Sipariş Detay Modalı Mobilde %95 Genişlik (Centered 95%) */
                            .fi-modal,
                            [role="dialog"],
                            .fi-modal-window-container,
                            .fi-modal-window-ctn {
                                padding-left: 0 !important;
                                padding-right: 0 !important;
                            }

                            .fi-modal-window {
                                width: 95vw !important;
                                max-width: 95vw !important;
                                min-width: 95vw !important;
                                margin: 8px auto !important;
                                border-radius: 12px !important;
                                zoom: 1 !important;
                                box-sizing: border-box !important;
                            }

                            /* Mobilde Takılı Kalan Tooltip Balonlarını Gizleme */
                            .tippy-box,
                            [data-tippy-root],
                            .fi-tooltip {
                                display: none !important;
                            }

                            .fi-modal-window > div,
                            .fi-modal-content,
                            .fi-modal-body,
                            .fi-modal-header {
                                padding: 8px 10px !important;
                                width: 100% !important;
                                max-width: 100% !important;
                                box-sizing: border-box !important;
                            }
                        }
                    </style>
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
