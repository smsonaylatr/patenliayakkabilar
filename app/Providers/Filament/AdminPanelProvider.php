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
                        /* Split Layout Hizalama & Okunabilirlik Kuralları */
                        .order-split-row {
                            display: flex !important;
                            align-items: center !important;
                            width: 100% !important;
                            gap: 12px !important;
                        }

                        /* Birebir Flex Oranları ve Minimum Genişlikler */
                        .col-split-customer, .oh-customer { flex: 2.2 1 180px !important; min-width: 160px !important; }
                        .col-split-city, .oh-city         { flex: 1 1 80px !important;   min-width: 70px !important; }
                        .col-split-total, .oh-total       { flex: 1 1 90px !important;   min-width: 80px !important; }
                        .col-split-payment, .oh-payment   { flex: 1.2 1 110px !important; min-width: 95px !important; }
                        .col-split-date, .oh-date         { flex: 1.5 1 130px !important; min-width: 120px !important; }
                        .col-split-status, .oh-status     { flex: 1.2 1 110px !important; min-width: 95px !important; }
                        .col-split-pay-status, .oh-pay-status { flex: 1.2 1 110px !important; min-width: 95px !important; }

                        /* Rozetler ve Metinler Asla Sıkışmasın */
                        .col-split-status .fi-badge,
                        .col-split-pay-status .fi-badge,
                        .col-split-payment .fi-badge {
                            white-space: nowrap !important;
                            display: inline-flex !important;
                        }

                        .col-split-date span,
                        .col-split-total span,
                        .col-split-city span {
                            white-space: nowrap !important;
                        }

                        .col-split-customer {
                            overflow: hidden !important;
                            text-overflow: ellipsis !important;
                        }
                    </style>
                ')
            )
            ->renderHook(
                \Filament\Tables\View\TablesRenderHook::HEADER_BEFORE,
                fn () => (request()->routeIs('filament.admin.resources.orders.*') || request()->is('admin/orders*') || str_contains(request()->header('referer', ''), '/admin/orders')) ? new \Illuminate\Support\HtmlString('
                    <div class="orders-split-header-bar hidden md:flex items-center w-full px-4 py-3 mb-2 text-xs font-extrabold text-gray-400 uppercase tracking-wider bg-slate-900/60 border border-slate-800 rounded-xl select-none" style="gap: 12px;">
                        <div style="width: 32px; flex-shrink: 0;"></div>
                        <div class="oh-customer">MÜŞTERİ</div>
                        <div class="oh-city">ŞEHİR</div>
                        <div class="oh-total">TUTAR</div>
                        <div class="oh-payment">ÖDEME</div>
                        <div class="oh-date">TARİH</div>
                        <div class="oh-status">DURUM</div>
                        <div class="oh-pay-status">ÖDEME DURUMU</div>
                        <div style="width: 120px; flex-shrink: 0;"></div>
                    </div>
                ') : null
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
