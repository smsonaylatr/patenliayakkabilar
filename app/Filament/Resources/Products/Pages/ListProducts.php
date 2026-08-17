<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('syncPoregoStock')
                ->label('Porego Stok Entegrasyonu')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->modalHeading('Porego / Paketfy Stok & Ürün Entegrasyonu')
                ->modalDescription(new HtmlString('
                    <div style="font-size: 14px; line-height: 1.6; color: #cbd5e1;">
                        <p style="margin-bottom: 12px;">✅ <b>Sitenizdeki Porego Canlı API Beslemesi Aktiftir.</b> Sitedeki tüm aktif ürünler, beden varyantları, SKU kodları ve stok miktarları otomatik olarak sunulmaktadır.</p>
                        
                        <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <div style="margin-bottom: 6px;"><b>📌 Ürün Çekim URL (Paketfy / Porego için):</b></div>
                            <code style="color: #38bdf8; word-break: break-all;">https://patenliayakkabilar.com/api/porego/products</code>
                            <div style="margin-top: 8px; margin-bottom: 6px;"><b>📌 Stok Çekim URL:</b></div>
                            <code style="color: #38bdf8; word-break: break-all;">https://patenliayakkabilar.com/api/porego/stock</code>
                        </div>

                        <p style="margin-bottom: 8px;">👉 <b>Porego Panelinde Ürünleri Görmek İçin:</b></p>
                        <ol style="margin-left: 20px; list-style-type: decimal;">
                            <li><a href="https://app.porego.com/dashboard/products" target="_blank" style="color: #38bdf8; text-decoration: underline;">app.porego.com/dashboard/products</a> adresine gidin.</li>
                            <li>Sayfadaki <b>"Mağazanızı Senkronize Edin"</b> butonuna tıklayın.</li>
                        </ol>
                        <p style="margin-top: 10px; font-size: 12px; color: #94a3b8;">Aşağıdaki butonla ayrıca doğrudan Porego API\'sine de push denemesi yapabilirsiniz.</p>
                    </div>
                '))
                ->modalSubmitActionLabel('Porego API Push Denemesi Yap')
                ->modalCancelActionLabel('Kapat')
                ->action(function (): void {
                    $result = app(\App\Services\PoregoApiService::class)->syncProducts();
                    if ($result['success']) {
                        \Filament\Notifications\Notification::make()
                            ->title('Porego Stok Senkronizasyonu Başarılı')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Porego Canlı API Beslemesi Hazır')
                            ->body("Sitedeki API beslemesi aktiftir.\n\nPorego (Paketfy) panelinden ('app.porego.com/dashboard/products') 'Mağazanızı Senkronize Edin' butonuna basarak tüm ürünleri çekebilirsiniz.")
                            ->info()
                            ->persistent()
                            ->send();
                    }
                }),
            CreateAction::make()->label('Yeni Ürün'),
        ];
    }
}
